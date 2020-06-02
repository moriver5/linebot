
//ブラウザ上でドロップ領域内から出るとき
function onDragLeave(event, drop_id, bgcolor) {
	event.preventDefault();
	var elm = document.getElementById(drop_id);
	elm.style.backgroundColor  = bgcolor;	
}

//ブラウザ上でドロップできる領域内に入ったとき
function onDragOver(event, drop_id, bgcolor) {
	event.preventDefault();
	var elm = document.getElementById(drop_id);
	elm.style.backgroundColor  = bgcolor;
	elm.style.blink  = 'red';
}
 
//Drop領域にドロップしたファイル情報を読み取り
function onDrop(event, form_id, upload_form_name, csrf_token, upload_end_msg, upload_error_msg, form_add_opt, method, timeout, redirect_url, result_prefix) {
	//ブラウザ上でファイルを展開する挙動を抑止
	event.preventDefault();

	//ドロップされたファイルのfilesプロパティを参照
	var files = event.dataTransfer.files;

        //画像の複数選択ドラッグのアップロード対応
//	if (files.length == 1) {
        for (var i=0; i<files.length; i++) {
            FileUpload(form_id, upload_form_name, files[i], csrf_token, upload_end_msg, upload_error_msg, form_add_opt, method, timeout, redirect_url, i, result_prefix);
	}
}
 
//ファイルアップロード
function FileUpload(form_id, upload_form_name, upload_file, csrf_token, upload_end_msg, upload_error_msg, form_add_opt, method, timeout, redirect_url, result_id, result_prefix) {
	//タイムアウトの指定がないときデフォルトのタイムアウトを設定
	if( timeout == undefined ){
		timeout = 10000;
	}

	//メソッド指定がないときはデフォルトでpostを指定
	if( method == undefined ){
		method = 'post';
	}

	//フォームデータの作成
	var formData = new FormData();

	//フォームのキーと値を設定
	formData.append(upload_form_name, upload_file);

	//追加のフォームがあれば追加する
	if( form_add_opt instanceof Array ){
            for(var i= 0;i<form_add_opt.length;i++){
                if( form_add_opt[i] == 'tmp_reserve_date' ){
                    formData.append(form_add_opt[i], $('[name=tmp_reserve_date]').val());	                    
                }else{
                    formData.append(form_add_opt[i], $('#'+form_id+' [name=' + form_add_opt[i] + ']').val());	
                }
            }
	}

	//サーバーへデータ送信(/public/js/admin/ajax.js)
 //   Ajax(form_id, method, csrf_token, formData, upload_end_msg, upload_error_msg, timeout);

	var p_id = 'progress' + result_id;
	var f_id = 'upload_status' + result_id;
        var stat_id = 'status' + result_id;
	$('#result'+result_prefix).append('<div style="font-color:black;background:white;height:25px;font-family:Meiryo;">アップロード状況：<span id="'+ f_id + '"></span><progress id="'+ p_id +'" value="0" max="100">0%</progress><span id="'+ stat_id +'"></span></div>');

	$.ajax($('#' + form_id).prop('action'), {
		xhr:function(){
			XHR = $.ajaxSettings.xhr();
			if( XHR.upload ){
				XHR.upload.addEventListener('progress',
                                    function(e) {
                                        progre = parseInt(e.loaded / e.total * 10000) / 100;
                                        document.getElementById(p_id).value = progre;
                                        document.getElementById(stat_id).innerHTML = progre+'%';
                                        document.getElementById(f_id).innerHTML = '<font color="deeppink>"' + progre + '%</font><br>';
				}, false);   
			}
			return XHR;
		},
		type: method,
		timeout: timeout,
		data: formData,
		contentType: false,
		processData: false,
		headers: {
			'X-CSRF-TOKEN': csrf_token
		},
		//通信しレスポンスが返ってきたとき
		success: function(result_data) {
			//アップロード成功メッセージ表示
			document.getElementById(f_id).innerHTML = '<font color="blue">成功</font> ';

                        if( upload_end_msg != '' && upload_end_msg != undefined ){
                            //完了メッセージ表示
                            swal(upload_end_msg, {}).then(() => {
                                //result_data(insertしたときのidが戻り値なら)
                                if( redirect_url != '' && redirect_url != undefined ){
                                    window.location.href = redirect_url + '/' + result_data;

                                //同ウィンドウをリロード
                                }else{
                                    window.location.reload();
                                }

                                //親ウィンドウがあればリロード
                                if( window.opener ){
                                    window.opener.location.reload();
                                }
                            });
                        }else{
                            if( redirect_url != '' && redirect_url != undefined ){
                                 window.location.href = redirect_url + '/' + result_data;

                             //同ウィンドウをリロード
                             }else{
                                 window.location.reload(true);
                             }

                             //親ウィンドウがあればリロード
                             if( window.opener ){
                                 window.opener.location.reload();
                             }
                        }
		},
		//通信がエラーになったとき
		error: function(error) {
                    //エラーメッセージ表示
                    var list_error= JSON.parse(error.responseText);
                    var err_flg = 'ok';
                    for (var item in list_error){
                        if( item == 'message' ){
                            err_flg = 'ng';
                            var err_title = 'エラー';
                            var duplicate_match = list_error[item].match(/Duplicate\sentry/);
                            if( duplicate_match != null ){
                                err_title = '重複エラー';
                            }
                            swal("'"+list_error[item]+"'",{title: err_title, icon: "error", dangerMode: true}); 
                        }
                    }
                    if( err_flg == 'ok' ){
                        swal("'"+list_error[item]+"'",{title: err_title, icon: "error", dangerMode: true});
                    }
                    //アップロード失敗メッセージ表示
                    document.getElementById(f_id).innerHTML = '<font color="red">失敗</font> ';
                    //エラーメッセージ表示
//                    swal(upload_error_msg);
		}
	});
}

    /*
     *  リターンキーでのpostを無効にする
     */
    $("input").keydown(function(e) {
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
                return false;
        } else {
                return true;
        }
    });

    /*
     * submitボタンを押下したときに表示するダイアログ確認メッセージ
     * 引数：FormのID、Formのmethod、dialogのtitle、dialogのmsg、通信完了後にdialogに表示させるmsg、dialogのcancelmsg、timeout、　完了後にmsgを表示させるかのflg、cancel時にmsgを表示させるかのflg、リダイレクトさせるかのflg、リダイレクトURL
     */
    function submitAlert(form_id, method, dialog_title, dialog_msg, dialog_end_msg, dialog_cancel_msg, timeout, result_msg_flg, cancel_flg, redirect_flg, redirect_url){
        //タイムアウトの指定がないときデフォルトのタイムアウトを設定
        if( timeout == undefined ){
                timeout = 10000;
        }

        if( method == undefined ){
                method = 'post';
        }

        //アカウント編集ボタン押下
        $('#' + form_id).submit(function(event){
            //確認ダイアログ表示
            swal({
              title: dialog_title,
              text: dialog_msg,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((exec_flg) => {
                    //OKボタンを押下
                    if( exec_flg ){
                            event.preventDefault();

                            //ajax通信(アカウント編集処理)
                            $.ajax({
                                    url: $(this).prop('action'),
                                    type: method,
                                    data: $(this).serialize(),
                                    timeout: timeout,
                                    success:function(result_flg){
//alert(result_flg);
                                        //通信完了後のメッセージ
                                        if( result_msg_flg == true ){
                                            swal(dialog_end_msg, {
                                              icon: "success", 
                                            }).then(() => {
                                                //親ウィンドウがあればリロード
                                                if( window.opener ){
                                                    window.opener.location.reload();
                                                }
                                                //redirect_flgがtrueならリダイレクト
                                                if( redirect_flg == true ){
                                                        if( result_flg.match(/^\d+$/) > 0 ){
                                                            window.location.href = redirect_url+'/'+result_flg;
                                                        }else{
                                                            window.location.href = redirect_url;
                                                        }
                                                        return false;
                                                }
                                                window.location.reload();
                                            });
                                        }else{
                                            window.location.reload();
                                        }
                                    },
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
                                    }
                            });

                    //キャンセルボタンを押下
                    }else{
                            //キャンセルしたときに表示させるメッセージ
                            if( cancel_flg === true ){
                                    swal(dialog_cancel_msg); 
                            }
                    }
            });

            return false;
        });
    }

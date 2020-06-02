
/*
 * サーバーとの通信に使用
 */
function Ajax(form_id, method, csrf_token, formData, upload_end_msg, upload_error_msg, timeout) {
	//タイムアウトの指定がないときデフォルトのタイムアウトを設定
	if( timeout == undefined ){
		timeout = 10000;
	}

	//メソッド指定がないときはデフォルトでpostを指定
	if( method == undefined ){
		method = 'post';
	}

	//通信
	$.ajax({
		url: $('#' + form_id).prop('action'),
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
			//完了メッセージ表示
			swal(upload_end_msg);
		},
		//通信がエラーになったとき
		error: function(error) {
			//エラーメッセージ表示
			swal(upload_error_msg);
		}
	});

}
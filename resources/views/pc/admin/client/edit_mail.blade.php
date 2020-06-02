<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>メルマガ運営管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<br />
<br />

<div class="container">
    <div class="col">
        <div class="col-md-2 col-md-offset" style="width:99%;">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>個別メール&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
                    <form id="formEditMail" class="form-horizontal" method="POST" action="/admin/member/client/edit/{{ $id }}/mail/view/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="3"><b>テンプレート</b></td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信者名</b>
											</td>
											<td>
												<input type="text" name="from_name" class="form-control" value="メルマガ運営管理" placeholder="送信者名">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信元メールアドレス</b>
											</td>
											<td>
												<input type="text" name="from_mail" class="form-control" value="{{ config("const.mail_from") }}" placeholder="送信元メールアドレス">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>送信先メールアドレス</b>
											</td>
											<td>
												<input type="text" name="to_mail" class="form-control" value="{{ $user->mail_address }}" placeholder="送信先メールアドレス">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td>
												<b>件名</b>
											</td>
											<td>
												<input type="text" name="subject" class="form-control" value="" placeholder="件名">
											</td>
										</tr>
										<tr style="text-align:center;">
											<td colspan="2">
												<textarea cols="60" rows="10" name="body" class="form-control" placeholder="メールの内容"></textarea>
											</td>
										</tr>
										<tr style="text-align:center;">
											<td colspan="2">
												<textarea cols="60" rows="10" name="html_body" class="form-control" placeholder="HTMLメールの内容"></textarea>
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">メール送信</button>
								<button id="push_history_btn"　type="submit" class="btn btn-primary">送信履歴</button>
							</div>
						</center>
						<input type="hidden" name="id" value="{{ $id }}">
					</form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var mail_history_win = null;
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
	});

	//送信履歴ボタン押下
	$('#push_history_btn').click(function(){
		mail_history_win = window.open('/admin/member/client/edit/{{ $id }}/mail/history', 'mail_history', 'width=600, height=580');
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formEditMail', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_send_mail_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

</body>
</html>

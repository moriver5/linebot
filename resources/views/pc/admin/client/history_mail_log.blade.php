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
					<b>送信履歴&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
						<center>
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="98%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>
												<b>日付</b>
											</td>
											<td>
												<b>件名</b>
											</td>
										</tr>
										@if( !empty($db_data) )
											@foreach($db_data as $lines)
												<tr style="text-align:center;">
													<td>
														{{ $lines->created_at }}
													</td>
													<td>
														<a href="/admin/member/client/edit/{{$id}}/mail/history/detail/{{ $lines->id }}">{{ $lines->subject }}</a>
													</td>
												</tr>
											@endforeach
										@endif
									</table>
								</div>
							</div>
						</center>
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
		mail_history_win = window.open('/admin/member/client/edit/{{ $id }}/mail/history', 'mail_history', 'width=600, height=600');
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formEditMail', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_send_mail_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

</body>
</html>

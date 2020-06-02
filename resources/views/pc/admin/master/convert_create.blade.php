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
    <title>LINE BOT管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<br />
<br />
<br />

<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset" style="width:99%;">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>変換キー追加</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
                    <form id="formKeyAdd" class="form-horizontal" method="POST" action="/admin/member/group/convert/setting/add/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>変換キー</td>
											<td>変更内容</td>
											<td>備考</td>
										</tr>
										<tr style="text-align:center;">
											<td><input type="text" name="key" value="" size="30" maxlength="{{ config('const.convert_key_max_length') }}"></td>
											<td><input type="text" name="value" value="" size="60" maxlength="{{ config('const.convert_value_max_length') }}"></td>
											<td><input type="text" name="remarks" value="" size="25" maxlength="{{ config('const.convert_memo_max_length') }}"></td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">キー追加</button>
							</div>
						</center>
						<input type="hidden" name="group_id" value="{{ $group_id }}">
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
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formKeyAdd', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

</body>
</html>

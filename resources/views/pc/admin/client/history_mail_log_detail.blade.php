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
								<div class="form-group">
									<table border="1" width="98%">
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">
												<b>送信先アドレス</b>
											</td>
											<td>
												{{ $db_data->mail_address }}
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">
												<b>件名</b>
											</td>
											<td>
												{{ $db_data->subject }}
											</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align:center;background:wheat;font-weight:bold;">
												<b>内容</b>
											</td>
										</tr>
										@if( !empty($db_data) )
											<tr style="vertical-align:top;height:340px;">
												<td colspan="2">
													{!! nl2br(e(trans($db_data->body))) !!}
												</td>
											</tr>
										@endif
									</table><br />
									<button id="back_btn" type="submit" class="btn btn-primary">戻る</button>
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

	//戻るボタンクリック
	$('#back_btn').click(function(){
		window.history.back();
		return false;
	});
});
</script>

</body>
</html>

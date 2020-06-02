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

<div class="container" style="width:99%;">
    <div class="col">
        <div class="col-md-2 col-md-offset" style="width:99%;">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>注文追加&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
                    <form id="formOrderAdd" class="form-horizontal" method="POST" action="/admin/member/client/edit/{{ $id }}/order/add/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;font-size:12px;font:normal 12px/120% 'メイリオ',sans-serif;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="3" style="padding:3px;">商品一覧</td>
										</tr>
										@if( !empty($db_data) )
										@foreach($db_data as $lines)
										<tr style="text-align:center;">
											<td style="width:30px;">
												<input type="checkbox" name="product_id[]" class="product_value" value="{{ $lines->id }}_{{ $lines->point }}_{{ $lines->money }}">
											</td>
											<td style="padding:3px;">{{ $lines->title }}pt</td>
											<td style="width:70px;text-align:right;">{{ $lines->money }}円</td>
										</tr>
										@endforeach
										@endif
									</table>
									<br />
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="3" style="padding:3px;">ポイント一覧</td>
										</tr>
										@if( !empty($db_pt_data) )
										@foreach($db_pt_data as $line)
										<tr style="text-align:center;">
											<td style="width:30px;"><input type="radio" name="add_point" class="point_value" value="{{ $line->id }}_{{ $line->point }}_{{ $line->money }}"></td>
											<td style="padding:5px;">{{ $line->point }}pt</td>
											<td style="width:70px;text-align:right;">{{ $line->money }}円</td>
										</tr>
										@endforeach
										@endif
									</table>
								</div>
								<button id="push_add_btn" type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;追加実行&nbsp;&nbsp;&nbsp;</button>
							</div>
						</center>
						<input type='hidden' name='id' value='{{ $id }}'>
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

	//追加実行ボタン押下
	$('#push_add_btn').on('click', function() {

		//入力チェック-未チェックなら停止
		var exist_flg1 = $('.product_value:checked').val();
		var exist_flg2 = $('.point_value:checked').val();

		if( exist_flg1 != undefined || exist_flg2 != undefined ){
			//アカウント編集ボタン押下後のダイアログ確認メッセージ
			//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
			submitAlert('formOrderAdd', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_add_product_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
		}else{
			swal('{{ __('messages.dialog_none_product_msg') }}');
			return false;
		}
	});

});
</script>

</body>
</html>

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
	<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body>
<center>
<div class="panel panel-default" style="font-size:12px;width:90%;margin:20px;">
	<div class="panel-heading">
		<b>注文情報詳細</b>
		<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
	</div>
	<div class="form-group" style="align:center;font-size:12px;font:normal 12px/120% 'メイリオ',sans-serif;">
		<form id="formHistoryUpdate" class="form-horizontal" method="POST" action="/admin/member/client/edit/order/history/update/send">
			{{ csrf_field() }}
		<center>
		<table border="1" width="98%">
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">注文No</td>
				<td style="padding:5px;">{{ $db_data['order_id'] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">注文日時</td>
				<td style="padding:5px;">{{ $db_data['regist_date'] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">広告コード</td>
				<td style="padding:5px;">{{ $db_data['ad_cd'] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">注文者ID</td>
				<td style="padding:5px;">{{ $client_id }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">E-Mail</td>
				<td style="padding:5px;">{{ $db_data['email'] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">支払方法</td>
				<td style="padding:5px;">{{ config('const.list_pay_type')[$db_data['pay_type']] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">電話番号</td>
				<td style="padding:5px;">{{ $db_data['tel'] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">ステータス</td>
				<td style="padding:5px;">
					<select name='status'>
						@foreach(config('const.list_pay_status') as $index => $status)
							@if( $index == $db_data['status'] ){
								<option value='{{ $index }}' selected>{{ $status }}</option>
							@else
								<option value='{{ $index }}'>{{ $status }}</option>
							@endif
						@endforeach
					</select>
				</td>
			</tr>
			<!-- 入金待ち、注文未完了のとき表示 -->
			@if( in_array($db_data['status'],[1,5]) )
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">未入金額</td>
				<td style="padding:5px;">{{ $db_data['money'] }}円</td>
			</tr>
			@endif
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">付加ポイント</td>
				<td style="padding:5px;">{{ $db_data['point'] }}</td>
			</tr>
			<tr>
				<td style="width:100px;padding:5px;text-align:center;background:wheat;font-weight:bold;">メモ</td>
				<td style="padding:5px;">{{ $db_data['description'] }}</td>
			</tr>
		</table>
		<br />
		<table border="1" width="98%">
			<tr>
				<td nowrap style="width:10px;padding:5px;text-align:center;background:wheat;font-weight:bold;">決済</td>
				<td nowrap style="width:110px;padding:5px;text-align:center;background:wheat;font-weight:bold;">内訳</td>
			</tr>
			<tr style="text-align:center;">
				<td nowrap align="center" style="font-size:12px;">
					{{ config('const.settlement_status')[$db_data['status']] }}
				</td>
				<td nowrap style="font-size:12px;">
					<table width="100%">
						@foreach($db_data['order_detail'] as $lines)
						<tr style="border:none;">
							<td nowrap style="padding:10px;width:200px;border:none;border-bottom:1px dotted #252525">
								<a href="{{ $product_url }}{{ $lines['product_id'] }}" target="_blank">
									{{ $lines['title'] }}
								</a>
							</td>
							<td nowrap align="right" valign="bottom" style="padding:10px;width:30px;border:none;border-bottom:1px dotted #252525">
								&yen;{{ $lines['money'] }}
							</td>
						</tr>
						@endforeach
						<tr>
							<td nowrap align="right" style="padding:10px;border:none;"><u>合計</u></td>
							<td nowrap align="right" style="padding:10px;border:none;"><u>&yen;{{ $total_amount['total'] }}</u></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
		<button type="submit" id="push_btn" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;</button>
		</center>
		<input type="hidden" name="old_status" value="{{ $db_data['status'] }}">
		<input type="hidden" name="client_id" value="{{ $client_id }}">
		<input type="hidden" name="order_id" value="{{ $order_id }}">
		<input type="hidden" name="add_point" value="{{ $db_data['point'] }}">
		<input type="hidden" name="pay_amount" value="{{ $total_amount['total'] }}">
		</form>
	</div>
</div>
</center>



<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	//登録ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formHistoryUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});
});
</script>

</body>
</html>


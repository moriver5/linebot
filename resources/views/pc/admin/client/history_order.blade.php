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

	<style>
	.link_btn input[type="submit"]{
		border:none;background:#FFF;text-decoration:underline;color:#00f;
	}
	.link_btn input:hover{
		cursor:pointer;
	}
	.link_btn_disabled input[type="submit"]{
		border:none;background:gray;text-decoration:underline;color:#00f;
	}
	</style>
</head>
<body>
<br />

<div class="container" style="width:800px;">
    <div class="col">
        <div class="col-md-3 ol-md-offset" style="width:770px;">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>注文履歴&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
				<span class="admin_default" style="margin-left:10px;">
					全件数：{{$total }} 件
					({{$currentPage}} / {{$lastPage}}㌻)
				</span>
				<center>{{ $links }}</center>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;font-size:12px;font:normal 12px/120% 'メイリオ',sans-serif;">
								<table border="1" width="100%">
									<tr style="text-align:center;background:wheat;font-weight:bold;">
										<td style="width:80px;padding:3px;">日時</td>
										<td style="width:55px;padding:3px;">注文No</td>
										<td style="width:110px;padding:3px;">注文詳細</td>
										<td style="width:30px;padding:3px;">回数</td>
										<td style="width:30px;padding:3px;">決済</td>
									</tr>
									@foreach($db_data as $product_id => $list_data)
										@foreach($list_data as $index => $lines)
											@if( $index == 0 )
											<tr style="text-align:center;">
												<td style="padding:1px;">{{ preg_Replace("/:\d{2}$/","",$lines->regist_date) }}</td>
												<td style="padding:1px;"><a href="#" onclick="openOrderWin('{{ $id }}', '{{ $lines->order_id }}');">{{ $lines->order_id }}</a></td>
												<td style="padding:1px;">
													<table width="100%">
											@endif
														<tr style="border:none;">
															<td nowrap style="padding:10px;width:200px;border:none;border-bottom:1px dotted #252525">
																<!-- キャンペーン -->
																@if( $lines->type == 0 )
																<a href="{{ $product_url }}{{ $lines->product_id }}" target="_blank">
																	{{ $lines->title }}																	
																</a>
																<!-- ポイント -->
																@else
																ポイント：{{ $lines->point }}pt
																@endif
															</td>
															<td nowrap align="right" valign="bottom" style="padding:10px;width:30px;border:none;border-bottom:1px dotted #252525">
																&yen;{{ $lines->product_money }}
															</td>
														</tr>
										@endforeach
														<tr>
															<td nowrap align="right" style="padding:10px;border:none;"><u>合計</u></td>
															<td nowrap align="right" style="padding:10px;border:none;">
																<form id="formHistoryUpdate{{ $lines->order_id }}" class="form-horizontal link_btn" method="POST" action="/admin/member/client/edit/order/history/update/send">
																{{ csrf_field() }}
																@if( in_array($lines->status, [0,3]) )
																<u>&yen;{{ $total_amount[$product_id]['total'] }}</u>
																@else
																<u><input type="submit" value="&yen;{{ $total_amount[$product_id]['total'] }}" id="{{ $lines->order_id }}" class="pay_btn"></u>
																@endif
																<input type="hidden" name="status" value="0">
																<input type="hidden" name="old_status" value="{{ $lines->status }}">
																<input type="hidden" name="client_id" value="{{ $id }}">
																<input type="hidden" name="order_id" value="{{ $lines->order_id }}">
																<input type="hidden" name="add_point" value="{{ $add_point[$lines->order_id]['add_point'] }}">
																<input type="hidden" name="pay_amount" value="{{ $total_amount[$product_id]['total'] }}">
																</form>						
															</td>
														</tr>
													</table>
												</td>
												<td style="padding:1px;">
													@if( in_array($lines->status, [0, 3]) )
													{{ $lines->pay_count }}
													@else
														--
													@endif
												</td>
												<td style="padding:1px;">{{ config('const.settlement_status')[$lines->status] }}</td>
											</tr>
									@endforeach
									<tr nowrap style="text-align:center;background:wheat;font-weight:bold;">
										<td colspan="2" style="padding:3px;background:#F2F5A9;">
											決済回数：{{ $settlement['num'] }}回
										</td>
										<td nowrap colspan="3" align="right" style="padding:5px;background:#F2F5A9;">
											<b><u>入金済合計金額：&yen;{{ $settlement['total'] }}</u></b>
										</td>
									</tr>
									<tr nowrap style="text-align:center;background:wheat;font-weight:bold;">
										<td colspan="2" style="padding:3px;background:#F2F5A9;">
											予約回数：{{ $reserv['num'] }}回
										</td>
										<td nowrap colspan="3" align="right" style="padding:5px;background:#F2F5A9;">
											<b><u>全予約合計金額：&yen;{{ $reserv['total'] }}</u></b>
										</td>
									</tr>
									<tr nowrap style="text-align:center;background:wheat;font-weight:bold;">
										<td colspan="2" style="padding:3px;background:#F2F5A9;">
											延べ決済回数：{{ $total_settlement_num }}回
										</td>
										<td nowrap colspan="3" align="right" style="padding:5px;background:#F2F5A9;">
											<b><u>入金済合計金額：&yen;{{ $total_settlement }}</u></b>
										</td>
									</tr>
									<tr nowrap style="text-align:center;background:wheat;font-weight:bold;">
										<td colspan="2" style="padding:3px;background:#F2F5A9;">
											延べ予約回数：{{ $total_reserv_num }}回
										</td>
										<td nowrap colspan="3" align="right" style="padding:5px;background:#F2F5A9;">
											<b><u>全予約合計金額：&yen;{{ $total_reserv }}</u></b>
										</td>
									</tr>
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
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
	});

	$('.pay_btn').on('click', function(){
		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formHistoryUpdate'+this.id, 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_paychg_msg') }}', '{{ __('messages.dialog_add_point_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, false);
	});
});
function openOrderWin(client_id, order_id){
	var order_detail_win = window.open('{{ config('const.base_url') }}/admin/member/client/edit/' + client_id + '/order/history/' + order_id, 'order_detail', 'width=600, height=620');
	return false;
}
</script>

</body>
</html>

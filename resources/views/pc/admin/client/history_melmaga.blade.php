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

<div class="container" style="width:950px;">
    <div class="col">
        <div class="col-md-13 ol-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>メルマガ履歴&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
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
										<td style="width:45px;padding:3px;" rowspan="2">メルマガID</td>
										<td style="width:20px;padding:3px;" rowspan="2">配信日時</td>
										<td style="width:240px;padding:3px;" rowspan="2">件名</td>
										<td style="width:60px;padding:3px;" colspan="2">閲覧日時</td>
									</tr>
									<tr style="text-align:center;background:wheat;font-weight:bold;">
										<td style="width:30px;padding:3px;">初回</td>
										<td style="width:30px;padding:3px;">最終</td>
									</tr>
									@foreach($db_data as $lines)
									<tr style="text-align:center;font-weight:bold;">
										<td>
											{{ $lines->melmaga_id }}
										</td>
										<td>
											{{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}):\d{2}/", "$1", $lines->created_at) }}
										</td>
										<td style="padding:5px;">
											<a href="/admin/member/melmaga/mail/history/view/{{ $currentPage }}/{{ $lines->melmaga_id }}/{{ $id }}" target="_blank">{{ $lines->subject }}</a>
										</td>
										<td>
											@if( empty($lines->first_view_datetime) )
												未閲覧
											@else
											{{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}):\d{2}/", "$1", $lines->first_view_datetime) }}
											@endif
										</td>
										<td>
											@if( !empty($lines->first_view_datetime) )
											{{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}):\d{2}/", "$1", $lines->updated_at) }}
											@endif
										</td>
									</tr>
									@endforeach
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
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formOrderAdd', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_add_point_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
function openOrderWin(client_id, order_id){
	var order_detail_win = window.open('{{ config('const.base_url') }}/admin/member/client/edit/' + client_id + '/order/history/' + order_id, 'order_detail', 'width=600, height=620');
	return false;
}
</script>

</body>
</html>

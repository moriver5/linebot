@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-10 col-md-offset-2">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading" style="text-align:left;float:left;margin-top:5px;">
					<b>LINEメッセージの配信状況-[予約配信]</b>
				</div>
				<div style="text-align:right;float:right;margin:5px 20px 0px 0px;">
					<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
				</div>
				<div class="panel-body">
					<center>{{ $db_data['links'] }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:40px;">
								<b>ID</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>タイプ</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>配信状況</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>クリック数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:40px;">
								<b>配信数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:150px;">
								<b>配信予定日時</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
								<b>予約日時</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
								<b>配信日時</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:30px;">
								
							</td>
						</tr>
						@foreach($db_data['db_data'] as $lines)
							<tr>
								<td style="padding:2px;text-align:center;">
									@if( preg_match("/[67]/", $send_type) > 0 )
									{{ $lines['id'] }}
									@else
									<a href="{{ url('/admin/member/line/history/push/message/view') }}/{{ $db_data['currentPage'] }}/{{ $send_type }}/{{ $lines['line_basic_id'] }}/{{ $lines['id'] }}">{{ $lines['id'] }}</a>									
									@endif
								</td>
								<td style="padding:2px;text-align:center;">
									予約配信
								</td>
								<td style="padding:2px;text-align:center;">
									@if( preg_match("/[04]/",$lines['send_status']) )
									<b><font color="red">配信待ち</font></b>
									@elseif( $lines['send_status'] == 1 )
									<b><font color="blue">配信中</font></b>
									@elseif( preg_match("/[25]/", $lines['send_status']) )
										<font color="gray">配信済</font>
									@elseif( $lines['send_status'] == 3 )
										<font color="gray">キャンセル</font>
									@elseif( $lines['send_status'] == 99 )
										<font color="gray">下書き保存</font>
									@endif
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['click'] }}
								</td>
								<td style="padding:2px;text-align:center;">
									<a href="/admin/member/line/history/list/{{ $lines['line_basic_id'] }}/{{ $lines['id'] }}">{{ $lines['send_count'] }}</a>
								</td>
								<td style="padding:2px 5px;text-align:left;">
									友だち登録から{{ $lines['send_after_minute'] }}分後
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['updated_at'] }}
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['send_date'] }}
								</td>
								<td style="padding:2px;text-align:center;">
									@if( preg_match("/[0399]/",$lines['send_status']) )
									<a href="{{ url('/admin/member/line/reserve/status/edit') }}/{{ $db_data['currentPage'] }}/{{$send_type}}/{{$lines['line_basic_id']}}/{{$lines['id']}}">編集</a>
									@else
									<font color="gray">編集</font>
									@endif
								</td>
								<td style="padding:1px;width:39px;text-align:center;">
									@if( preg_match("/[04]/",$lines['send_status']) )
									<form id="formCancel{{ $lines['id'] }}" class="form-horizontal" method="POST" action="/admin/member/line/reserve/status/cancel/{{ $db_data['currentPage'] }}/{{ $send_type }}/{{ $lines['line_basic_id'] }}/{{ $lines['id'] }}">
										{{ csrf_field() }}
										<button id="{{ $lines['id'] }}" class="cancel_btn" type="submit">ｷｬﾝｾﾙ</button>
									</form>
									@else
									--
									@endif
								</td>
								<td style="padding:1px;width:39px;text-align:center;">
									<form id="formDelete{{ $lines['id'] }}" class="form-horizontal" method="POST" action="/admin/member/line/reserve/status/delete/{{ $db_data['currentPage'] }}/{{ $send_type }}/{{ $lines['line_basic_id'] }}/{{ $lines['id'] }}">
										{{ csrf_field() }}
										<button id="{{ $lines['id'] }}" class="delete_btn" type="submit">削除</button>
									</form>
								</td>
							</tr>
						@endforeach
					</table>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//削除ボタンを押下
	$('.delete_btn').click(function(){
		var form_id = 'formDelete' + this.id;
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert(form_id, 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.delete_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $cancel_redirect_url }}?page={{ $db_data['currentPage'] }}');
	});

	//新規作成ボタンを押下
	$('.cancel_btn').click(function(){
		var form_id = 'formCancel' + this.id;
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert(form_id, 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.cancel_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $cancel_redirect_url }}?page={{ $db_data['currentPage'] }}');
	});
});
</script>

@endsection

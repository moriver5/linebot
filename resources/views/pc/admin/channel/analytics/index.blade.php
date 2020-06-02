@extends('layouts.app')

@section('content')

<br />
	<div class="panel panel-default" style="margin-left:200px;width:1800px;">
		<div class="panel-body">
			<form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/line/analytics/friends/{{ $channel_id }}">
			{{ csrf_field() }}
				<b>LINE友だち集計</b>
				<center>
					<table border="1" width="100%">
						<tr>
						<td style="font-weight:bold;text-align:center;background:wheat;width:3%;">集計期間</td>
						<td style="border-right:0px;width:5%;">
							@if( !empty($session['regist_start_date']) )
								&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" value="{{$session['regist_start_date']}}" placeholder="開始日">
							@else
								&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" placeholder="開始日">
							@endif
							@if( !empty($session['regist_end_date']) )
								&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" value="{{$session['regist_end_date']}}" placeholder="終了日">
							@else
								&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" placeholder="終了日">
							@endif
						</td>
						<td style="font-weight:bold;text-align:center;background:wheat;width:3%;">広告コード</td>
						<td style="border-right:0px;width:6%;">
							<input type="text" name="ad_cd" value="{{ $session['line_ad_cd'] }}" size="5"　class="form-control">
						</td>
						<td style="padding:5px;border-left:0px;width:2%;">
							<button type="submit" class="btn btn-primary" id="search_setting" style="width:70%;">検索</button>
							<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
						</td>
					</tr>
					</table>
				</center>
			</form>

			@if( !empty($db_data) )
			<br>
			<span class="admin_default">
				全件数：{{$total }} 件
				({{$currentPage}} / {{$lastPage}}㌻)
			</span>
			<center>{{ $db_data->links() }}</center>
			<table border="1" width="100%">
				<tr>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:3%;">
						LINE ID
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:3%;">
						広告コード
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:23%;">
						友だち追加URL
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:23%;">
						リファラ―
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:3%;">
						状態
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:5%;">
						登録日時
					</td>
				</tr>
				@foreach($db_data as $lines)
				<tr>
					<td style="padding:2px;text-align:center;">
						<a href="/admin/member/client/list/{{ $currentPage }}/{{ $channel_id }}/{{ $lines->user_line_id }}">{{ $lines->user_line_id }}</a>
					</td>
					<td style="padding:2px;text-align:center;">
						<a href="{{ config('const.base_admin_url') }}/member/ad/adcode/edit/1/{{ $lines->id }}">{{ $lines->ad_cd }}</a>
					</td>
					<td style="padding:2px;text-align:center;">
						<a href="{{ config('const.line_friend_add_url') }}?pk_campaign={{ $channel_id }}&ad_cd={{ $lines->ad_cd }}">{{ config('const.line_friend_add_url') }}?pk_campaign={{ $channel_id }}&ad_cd={{ $lines->ad_cd }}</a>
					</td>
					<td style="padding:2px;text-align:center;">
						{{ $lines->access_referrer }}
					</td>
					<td style="padding:2px;text-align:center;">
						@if( $lines->follow_flg == 1 )
							<span style="font-weight:bold;color:blue;">友だち</span>
						@else
							<span style="font-weight:bold;color:red;">ブロック</span>
						@endif
					</td>
					<td style="padding:2px;text-align:center;">
						{{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}):\d{2}/", "$1", $lines->created_at) }}
					</td>
				</tr>
				@endforeach
			</table>
			@endif
		</div>
	</div>
	


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('[name=start_date]').focusin(function(){
		$('[name=start_date]').attr("placeholder","");
	});

	$('[name=end_date]').focusin(function(){
		$('[name=end_date]').attr("placeholder","");
	});

	$.datetimepicker.setLocale('ja');

	/*
	 * 開始日
	 */
	$('#start_date').datetimepicker({
		format:'Y/m/d',
		timepicker:false
	});

	/*
	 * 終了日
	 */
	$('#end_date').datetimepicker({
		format:'Y/m/d',
		timepicker:false
	});

	var dateFormat = {
	  _fmt : {
		"yyyy": function(date) { return date.getFullYear() + ''; },
		"MM": function(date) { return ('0' + (date.getMonth() + 1)).slice(-2); },
		"dd": function(date) { return ('0' + date.getDate()).slice(-2); },
		"hh": function(date) { return ('0' + date.getHours()).slice(-2); },
		"mm": function(date) { return ('0' + date.getMinutes()).slice(-2); },
		"ss": function(date) { return ('0' + date.getSeconds()).slice(-2); }
	  },
	  _priority : ["yyyy", "MM", "dd", "hh", "mm", "ss"],
	  format: function(date, format){
		return this._priority.reduce((res, fmt) => res.replace(fmt, this._fmt[fmt](date)), format)
	  }
	};

	/*
	 * デフォルトの開始日・終了日
	 */
	if( $('#start_date').val() == '' ){
		$('#start_date').val(dateFormat.format(new Date(), 'yyyy/MM/dd'));
	}else{
		$('#start_date').val($('#start_date').val().replace(/(\d{4})(\d{2})(\d{2})/, "$1/$2/$3"));		
	}
	if( $('#end_date').val() == '' ){
		$('#end_date').val(dateFormat.format(new Date(), 'yyyy/MM/dd'));
	}else{
		$('#end_date').val($('#end_date').val().replace(/(\d{4})(\d{2})(\d{2})/, "$1/$2/$3"));		
	}
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>利用統計</b>
					<a href="/admin/member/analytics/statistics/access">ページトップ</a>
				</div>

				<div class="panel-body">
					<div style="text-align:center;">
						<a href="/admin/member/analytics/statistics/access/{{ $channel_id }}/{{ $prev_year }}/{{ $prev_month }}">前の月</a>｜{{ $tomonth }}｜<a href="/admin/member/analytics/statistics/access/{{ $channel_id }}/{{ $next_year }}/{{ $next_month }}">次の月</a>
					</div>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:5px;">
								<b>日付</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>PV</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>友だち</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>ブロック</b>
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $day => $lines)
								<tr>
									<td style="padding:1px 5px;text-align:center;">
										{{ $day }}日
									</td>
									<td style="padding:1px 5px;text-align:right;">
										{{ $lines['pv'] }}
									</td>
									<td style="padding:1px 5px;text-align:right;">
										{{ $lines['reg'] }}
									</td>
									<td style="padding:1px 5px;text-align:right;">
										{{ $lines['unfollow'] }}
									</td>
								</tr>
							@endforeach
							<tr>
								<td class="admin_table" style="background:rgb(255, 255, 153);padding:1px 5px;text-align:center;width:10px;">
									<b>合計</b>
								</td>
								<td class="admin_table" style="background:rgb(255, 255, 153);padding:1px 5px;text-align:right;width:30px;">
									<b>{{ $list_total['pv'] }}</b>
								</td>
								<td class="admin_table" style="background:rgb(255, 255, 153);padding:1px 5px;text-align:right;width:30px;">
									<b>{{ $list_total['reg'] }}</b>
								</td>
								<td class="admin_table" style="background:rgb(255, 255, 153);padding:1px 5px;text-align:right;width:30px;">
									<b>{{ $list_total['unfollow'] }}</b>
								</td>
							</tr>
						@endif
					</table>
				</div>
				<div style="text-align:center;margin-bottom:8px;"><a href="javascript:history.back();" class="btn btn-primary">戻る</a></div>
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

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/ad/media/search/setting', 'convert_table', 'width=680, height=320');
		return false;
	});

	//クライアント検索
	$('.ad_link').on('click', function(){
		var fm = document.formAdSearch;

		//広告コードのリンクテキストを取得
		fm.search_item.value = $(this).text();

		fm.target = '_blank';

		//検索を行う
		fm.submit();
		return false;
	});
});
</script>

@endsection

@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>利用統計</b>
				</div>

				<div class="panel-body">
					<div style="text-align:center;">
						<a href="/admin/member/analytics/statistics/access?date={{ $prev_date }}">前の日</a>｜{{ $today }}｜<a href="/admin/member/analytics/statistics/access?date={{ $next_date }}">次の日</a>
					</div>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:50px;">
								<b>チャンネル名</b>
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
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:1px 5px;text-align:center;">
										<a href="/admin/member/analytics/statistics/access/{{ $lines['basic_id'] }}/{{ $year }}/{{ $month }}">{{ $lines['channel'] }}</a>
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
								<td class="admin_table" style="background:rgb(255, 255, 153);width:50px;">
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

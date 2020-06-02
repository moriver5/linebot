@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-11 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>媒体集計</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:20px;">
								<b>広告コード</b>
							</td>
							<td class="admin_table" style="width:100px;">
								<b>広告コード名称</b>
							</td>
							<td class="admin_table" style="width:40px;">
								集計項目
							</td>
							<td class="admin_table" style="width:50px;">
								<b>合計</b>
							</td>
							@foreach($list_date as $date)
							<td class="admin_table" style="width:40px;">
								<b>{{ $date }}</b>
							</td>
							@endforeach
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $ad_cd => $lines)
							<tr>
								<td style="padding:2px;text-align:center;" rowspan="4">
									<a href="#" class="ad_link">{{ $ad_cd }}</a>
								</td>
								<td style="padding:2px;text-align:center;" rowspan="4">
									<a href="/admin/member/ad/adcode/edit/{{ $currentPage }}/{{ $lines['total']['id'] }}">{{ $list_ad[$ad_cd] }}</a>
								</td>
								<td style="padding:2px;text-align:center;">
									アクセス
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['pv'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['pv'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									登録者数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['reg'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['reg'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									退会者数
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['quit'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['quit'] }}
								</td>
								@endforeach
							</tr>
							<tr>
								<td style="padding:2px;text-align:center;">
									アクティブ
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines['total']['active'] }}
								</td>
								@foreach($lines as $date => $line)
								@if( $date == 'total' )
									@continue;
								@endif
								<td style="padding:2px;text-align:center;">
									{{ $line['active'] }}
								</td>
								@endforeach
							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>	
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/media/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="start_date" value="">
	<input type="hidden" name="end_date" value="">
	<input type="hidden" name="category" value="">
	<input type="hidden" name="disp_type" value="">
	<input type="hidden" name="action_flg" value="">
</form>

<!-- 広告コードのリンクをクリックしたら顧客検索へアクセス -->
<form name="formAdSearch" class="form-horizontal" method="POST" action="/admin/member/client/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_type" value="ad_cd">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="0">
	<input type="hidden" name="search_disp_num" value="0">
	<input type="hidden" name="sort" value="0">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/ad/media/search/setting', 'convert_table', 'width=680, height=380');
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

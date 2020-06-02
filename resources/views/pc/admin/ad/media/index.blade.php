@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-11 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>媒体集計</b>
				</div>

				<div class="panel-heading">
                    <form id="formSearchSetting" class="form-horizontal" method="POST"   action="/admin/member/ad/media/search">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="97%">
										<tr>
											<td class="admin_search" style="width:60px;">LINEチャンネル</td>
											<td style="padding:5px;width:50px;" colspan="3">
												@foreach($list_channel as $index => $lines)
													@if( !empty($session['media_line_channel']) && array_search($lines->line_basic_id, $session['media_line_channel']) !== false )
														&nbsp;&nbsp;<input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}" checked>{{ $lines->name }}</option>
													@else
														&nbsp;&nbsp;<input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}">{{ $lines->name }}</option>													
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:60px;">広告コード</td>
											<td style="padding:5px;width:50px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($ad_search_item as $lines)
													@if( !empty($session['media_search_item']) && $lines[0] == $session['media_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:60px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['media_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['media_search_item_value'] }}" placeholder="" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" placeholder="" autofocus>
												@endif
											</td>
											<td style="width:55px;padding:5px;">
												<!-- LIKE検索-->
												<select name="search_like_type" class="form-control">
												@foreach($search_like_type as $index => $line)
													@if( !empty($session['media_search_like_type']) && $index == $session['media_search_like_type'] )
														<option value="{{ $index }}" selected>{{ $line[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $line[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;width:60px;">期間</td>
											<td colspan="3" style="padding:5px;">
												&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" value="{{ $start_date }}" placeholder="開始日時">
												&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" value="{{ $end_date }}" placeholder="終了日時">
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:60px;">媒体種別</td>
											<td style="padding:5px;width:50px;" colspan="3">
												@foreach($ad_category as $index => $category)
													@if( !empty($session['media_category']) && array_search($index, $session['media_category']) !== false )
														&nbsp;&nbsp;<input type="checkbox" name="category[]" value="{{ $index }}" checked>{{ $category }}</option>
													@else
														&nbsp;&nbsp;<input type="checkbox" name="category[]" value="{{ $index }}">{{ $category }}</option>													
													@endif
												@endforeach
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="search_setting">&nbsp;&nbsp;&nbsp;&nbsp;検索&nbsp;&nbsp;&nbsp;&nbsp;</button>
							</div>
						</center>
					</form>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:50px;">
								<b>LINEチャンネル</b>
							</td>
							<td class="admin_table" style="width:50px;">
								<b>広告コード</b>
							</td>
							<td class="admin_table" style="width:150px;">
								<b>広告コード名称</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>PV</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>友だち</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>24Hブロック</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>ブロック</b>
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										{{ $lines['channel'] }}
									</td>
									<td style="padding:2px;text-align:center;">
										@if( $lines['ad_cd'] == '99k' )
											{{ $lines['ad_cd'] }}
										@else
										<a href="/admin/member/ad/adcode/edit/{{ $currentPage }}/{{ $lines['ad_id'] }}">{{ $lines['ad_cd'] }}</a>
										@endif
									</td>
									<td style="padding:2px;text-align:center;">
										@if( $lines['ad_cd'] == '99k' )
											広告名称なし
										@else
											{{ $lines['name'] }}
										@endif
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines['pv'] }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines['reg'] }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines['unfollow24'] }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines['unfollow'] }}
									</td>
								</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>	
		</div>	
	</div>	

</div>

<!-- 検索 -->
<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/media/search">
	{{ csrf_field() }}
	<input type="hidden" name="line_channel" value="">
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

	$('[name=start_date]').focusin(function(){
		$('[name=start_date]').attr("placeholder","");
	});

	$('[name=start_date]').focusout(function(){
		$('[name=start_date]').attr("placeholder","開始日時");
	});
	
	$('[name=end_date]').focusin(function(){
		$('[name=end_date]').attr("placeholder","");
	});

	$('[name=end_date]').focusout(function(){
		$('[name=end_date]').attr("placeholder","終了日時");
	});

	//登録日時-開始日
	$('#start_date').datetimepicker({format:'Y/m/d',timepicker:false});

	//登録日時-終了日
	$('#end_date').datetimepicker({format:'Y/m/d',timepicker:false});

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

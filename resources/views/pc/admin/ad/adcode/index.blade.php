@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-14 col-md-offset" style="width:1500px">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>検索設定</b>
				</div>
                <div class="panel-body">
                    <form id="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/search">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="97%">
										<tr>
											<td class="admin_search" style="width:60px;">LINEチャンネル</td>
											<td style="padding:5px;width:50px;" colspan="3">
												@foreach($list_channel as $index => $lines)
													@if( !empty($session['ad_line_channel']) && array_search($lines->line_basic_id, explode(",", $session['ad_line_channel'])) !== false )
														&nbsp;&nbsp;<input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}" checked>{{ $lines->name }}</option>
													@else
														&nbsp;&nbsp;<input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}">{{ $lines->name }}</option>													
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:30px;">検索項目</td>
											<td style="padding:5px;width:50px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($ad_search_item as $lines)
													@if( !empty($session['ad_search_item']) && $lines[0] == $session['ad_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['ad_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['ad_search_item_value'] }}" placeholder="" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" placeholder="" autofocus>
												@endif
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:75px;text-align:center;">代理店</td>
											<td style="width:50px;padding:5px;" colspan=3">
												<select name="agency_id" class="form-control">
													@foreach($list_agency as $id => $name)
														@if( !empty($session['ad_agency_id']) && $id == $session['ad_agency_id'] )
														<option value="{{ $id }}" selected>{{ $name }}</option>
														@else
														<option value="{{ $id }}">{{ $name }}</option>
														@endif
													@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:30px;">媒体種別</td>
											<td style="padding:5px;width:50px;" colspan="2">
												@foreach($ad_category as $index => $category)
													@if( !empty($session['ad_category']) && array_search($index, explode(",", $session['ad_category'])) !== false )
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
            </div>

			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>広告コード一覧</b>
					<div style="text-align:right;float:right;margin:-8px 7px 0px 0px;">
					<button id="create" type="submit" class="btn btn-primary" style="float:right;">新規作成</button>
					</div>
				</div>

				<form id="formAdcode" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:30px;">
								<b>ID</b>
							</td>
							<td class="admin_table" style="width:60px;">
								<b>LINEチャンネル</b>
							</td>
							<td class="admin_table" style="width:80px;">
								<b>広告コード名称</b>
							</td>
							<td class="admin_table" style="width:30px;">
								<b>区分</b>
							</td>
							<td class="admin_table" style="width:20px;">
								<b>ASP</b>
							</td>
							<td class="admin_table" style="width:20px;">
								<b>広告コード</b>
							</td>
							<td class="admin_table" style="width:140px;">
								<b>代理店</b>
							</td>
							<td class="admin_table" style="width:160px;">
								<b>友だち追加URL</b>
							</td>
							<td class="admin_table" style="width:25px;">
								削除 <input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/ad/adcode/edit') }}/{{ $currentPage }}/{{$lines->id}}">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:left;">
										{{ $lines->channel_name }}
									</td>
									<td style="padding:2px;text-align:left;">
										{{ $lines->ad_name }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ config('const.ad_category')[$lines->category] }}
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $list_asp[$lines->asp_id] }}
									</td>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/ad/adcode/edit') }}/{{ $currentPage }}/{{$lines->id}}">{{ $lines->ad_cd }}</a>
									</td>
									<td style="padding:2px;text-align:left;">
										@if( $lines->agency_id == 0 )
											自社
										@else
										<a href="/admin/member/ad/agency/edit/{{ $currentPage }}/{{ $lines->agency_id }}">{{ $lines->name }}</a>
										@endif
									</td>
									<td style="padding:2px;text-align:left;">
										<a id="add_firend_url{{ $lines->id }}" href="{{ config('const.line_friend_add_url') }}?pk_campaign={{ $lines->line_basic_id }}&ad_cd={{ $lines->ad_cd }}&asp={{ $lines->asp_id }}" target="_blank"><b>{{ config('const.line_friend_add_url') }}?pk_campaign={{ $lines->line_basic_id }}&ad_cd={{ $lines->ad_cd }}&asp={{ $lines->asp_id }}</b></a>
										<div style="float:right;"><button id="copy{{ $lines->id }}" type="submit" class="copy" style="float:right;background:lavender;">コピー</button></div>
									</td>
									<td style="text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}"></td>
								</tr>
							@endforeach
						@endif
					</table>
					<br />
					<center><button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;</button></center>
				</div>
				</form>
			</div>
		</div>	
	</div>	

</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/search">
	{{ csrf_field() }}
	<input type="hidden" name="line_channel" value="">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="category" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	$(".copy").click(function(){
		event.preventDefault();
		var clipboard = document.getElementById("add_firend_url" + this.id.replace(/copy/, ""));
		var range = document.createRange();
		range.selectNode(clipboard);
		window.getSelection().removeAllRanges();
		window.getSelection().addRange(range);
		document.execCommand('copy');
		swal('友だち追加URLをコピーしました');
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/ad/adcode/search/setting', 'convert_table', 'width=640, height=400');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
//		search_win = window.open('/admin/member/ad/adcode/create', 'create', 'width=740, height=450');
		window.location = '/admin/member/ad/adcode/create';
		return false;
	});

	$('#push_update').on('click', function(){
		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formAdcode', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});
});
</script>

@endsection

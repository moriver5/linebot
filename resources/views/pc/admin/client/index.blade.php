@extends('layouts.app')

@section('content')
<br />
<div class="container">

	<div class="col-md-7 col-md-offset-3 panel panel-default" style="font-size:12px;">
		<div class="panel-heading">
			<b>USER LIST</b>
			<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
			<button id="create" type="submit" style="float:right;">新規作成</button>
		</div>
		<form id="formMailDel" class="form-horizontal" method="POST" action="/admin/member/client/del/send">
			{{ csrf_field() }}
		<div class="panel-body">
			<span class="admin_default" style="margin-left:10px;">
				全件数：{{$total }} 件
				({{$currentPage}} / {{$lastPage}}㌻)
			</span>
			<center>{{ $db_data->links() }}</center>
			<table border="1" align="center" width="98%">
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:70px;">
						<b>顧客ID</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>E-mail</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
						<b>配信</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:40px;">
						<b>無効</b>
<!--
						<input type="checkbox" id="del_all" name="del_all" value="1">
-->
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:40px;">
						<b>削除</b>
						<input type="checkbox" id="del_all" name="del_all" value="1">
					</td>
				</tr>
				@foreach($db_data as $lines)
					{{-- メルマガ無効のときセル色をグレイ --}}
					@if( !empty($lines->disable) || empty($lines->send_flg) )
					<tr style='background:darkgray;'>
					{{-- メルマガ有効のときセル色をホワイト --}}
					@else
					<tr style='background:white;'>
					@endif
						<td style="padding:2px;text-align:center;">
							<a href="{{ url('/admin/member/client/list/') }}/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->id }}</a>
						</td>
						<td style="padding:2px;text-align:center;">
							{{ $lines->mail_address }}
						</td>
						<td style="padding:2px;text-align:center;">
							@if( empty($lines->send_flg) )
								停止中
							@endif
						</td>
						<td style="padding:2px;text-align:center;">
							@if( !empty($lines->disable) )
								●						
							@endif
						</td>
						<td style="padding:2px;text-align:center;">
							<input type="hidden" name="id[]" value="{{ $lines->id }}">
							<input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}">
						</td>
					</tr>
				@endforeach
			</table>
			<br />
			<center><button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;一括削除&nbsp;&nbsp;&nbsp;</button></center>
		</div>
		</form>
	</div>
	
</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/client/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_type" value="">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="group_id" value="">
	<input type="hidden" name="reg_status" value="">
	<input type="hidden" name="sex" value="">
	<input type="hidden" name="age" value="">
	<input type="hidden" name="start_regdate" value="">
	<input type="hidden" name="end_regdate" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/search/setting', 'convert_table', 'width=700, height=435');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/client/create', 'create', 'width=1000, height=555');
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMailDel', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_del_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection

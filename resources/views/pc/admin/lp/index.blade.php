@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>ドメイン一覧</b>
					<button id="search" type="submit" style="float:right;margin-left:10px;">検索設定</button>
					<button id="create" type="submit" style="float:right;">新規作成</button>
				</div>

				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:30px;">
								<b>LP ID</b>
							</td>
							<td class="admin_table" style="width:25px;">
								<b>公開</b>
							</td>
							<td class="admin_table" style="width:100px;">
								<b>ドメイン</b>
							</td>
							<td class="admin_table" style="width:140px;">
								<b>URL</b>
							</td>
							<td class="admin_table" style="width:170px;">
								<b>MEMO</b>
							</td>
							<td class="admin_table" style="width:60px;">
								<b>参照</b>
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/lp/edit') }}/{{ $currentPage }}/{{$lines->id}}" target="_blank">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;">
										@if( !empty($lines->open_flg) )
											〇
										@else
											×
										@endif
									</td>
									<td style="padding:2px;text-align:left;">
										{{ $lines->domain }}
									</td>
									<td style="padding:2px;text-align:left;">
										@if( !empty($lines->open_flg) && !empty($lines->url_open_flg) )
											<a href="https://{{ $lines->domain }}/{{ config('const.landing_url_path') }}/{{ $lines->id}}/{{$lines->name}}" class="lp_preview"><b>https://{{ $lines->domain }}/?afl=****</b></a>
										@else
											<font color="darkgray">https://{{ $lines->domain }}/?afl=****</font>
										@endif
									</td>
									<td style="padding:2px;text-align:left;">
										{{ $lines->memo }}
									</td>
									<td style="padding:2px;text-align:center;">
										<!-- 公開するときのみ表示 -->
										@if( !empty($lines->open_flg) )
										<a href="/admin/member/lp/list/{{ $lines->id }}"><b>編集</b></a> / <a href="/admin/member/lp/create/img/{{ $lines->id }}" target="_blank"><b>画像</b></a> / <a href="/admin/member/lp/list/{{ $lines->id }}/subpage"><b>LP一覧</b></a>
										@endif
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

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/lp/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_item_value" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="disp_type" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	$.datetimepicker.setLocale('ja');

	$('.start_date').each(function(){
		$('#'+this.id).datetimepicker();
	});

	//カーソルがフォーカスされたら日付を消す	
	$('[id^=start_date').focus(function(){
		$("#"+this.id).val('');
	});

	$('.end_date').each(function(){
		$('#'+this.id).datetimepicker();
	});

	//カーソルがフォーカスされたら日付を消す	
	$('[id^=end_date').focus(function(){
		$("#"+this.id).val('');
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/lp/search/setting', 'convert_table', 'width=605, height=285');
		return false;
	});

	//プレビュー表示
	$(".lp_preview").colorbox({iframe:true, width:'80%', height:'80%', closeButton:true, transition:'fade'});

	$('.btn').click(function () {
		parent.$.colorbox.close();
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/lp/create', 'create', 'width=500, height=485');
		return false;
	});
	
	//更新ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formBulkUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
});
</script>

@endsection

@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_search_count) )
						<form id="formStatusPointAdd" class="form-horizontal" method="POST" action="/admin/member/client/status/search/point">
						{{ csrf_field() }}
						<center>
						<table style="width:100%;text-align:left;">
							<tr style="padding:10px;">
								<td style="text-align:left;width:40%;">
									検索結果：{{$db_search_count}} 件&nbsp;
									(<a href="{{ url('/admin/member/client/status/search/list') }}" target="_blank">グループ移行の顧客一覧</a>)
								</td>
								<td style="text-align:left;float:right;width:100px;">								
									<button id="search" type="submit" class="btn btn-primary">
										条件設定
									</button>
								</td>
							</tr>
						</table>
						</center>
						</form>
					@else
						<center>
						<table style="width:100%;text-align:left;">
							<tr style="padding:10px;">
								<td style="text-align:left;float:left;width:100px;">								
									<button id="search" type="submit" class="btn btn-primary">
										条件設定
									</button>
								</td>
								<td style="text-align:left;width:50%;">
									検索結果：0 件
								</td>
							</tr>
						</table>
						</center>
					@endif
                </div>
            </div>
			
            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_group_data) )
						<form id="formGroupMove" class="form-horizontal" method="POST" action="/admin/member/client/group/search/move">
						{{ csrf_field() }}
						<center>
						<button id="move_btn" type="submit" class="btn btn-primary" style="margin-bottom:10px;">
							グループ移行
						</button>
						<table border="1" align="center" style="width:100%;font-size:11px;">
							<tr>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:4%;">
									<b>選択</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>グループ名</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>説明</b>
								</td>
							</tr>
							@if( count($db_group_data) > 0 )
							@foreach($db_group_data as $id => $lines)
								<tr class="slt_group" id="slt_group{{ $id }}">
									<td style="text-align:center;">
										<input type="radio" name="group_id" value="{{ $id }}"　id="group{{ $id }}" class="group">
									</td>
									<td style="text-align:center;">
										{{ $id }}
									</td>
									<td style="text-align:center;">
										{{ $lines['name'] }}
									</td>
									<td style="text-align:center;">
										{{ $lines['memo'] }}
									</td>
								</tr>
							@endforeach
							@endif
						</table>
						</center>
						</form>
					@endif
                </div>
            </div>
        </div>
    </div>
</div>

<form name="formGroupSearch" class="form-horizontal" method="POST" action="/admin/member/client/group/search/count">
	{{ csrf_field() }}
	<input type="hidden" name="search_type" value="">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="group_id" value="">
	<input type="hidden" name="reg_status" value="">
	<input type="hidden" name="dm_status" value="">
	<input type="hidden" name="start_regdate" value="">
	<input type="hidden" name="end_regdate" value="">
	<input type="hidden" name="start_provdate" value="">
	<input type="hidden" name="end_provdate" value="">
	<input type="hidden" name="start_lastdate" value="">
	<input type="hidden" name="end_lastdate" value="">
	<input type="hidden" name="start_paynum" value="">
	<input type="hidden" name="end_paynum" value="">
	<input type="hidden" name="start_payamount" value="">
	<input type="hidden" name="end_payamount" value="">
	<input type="hidden" name="start_actnum" value="">
	<input type="hidden" name="end_actnum" value="">
	<input type="hidden" name="start_pt" value="">
	<input type="hidden" name="end_pt" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//移行先グループ選択でチェックしたセルの色を変更
	$('.group').on('click', function(){
		//最初にチェック済の背景色を消す
		$(".slt_group").css("background-color","");			
		
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/group/,"")).css("background-color","yellow");
		}
	});
	
	//条件検索ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/group/search/setting', 'group_search', 'width=800, height=1000');
		return false;
	});

	//グループ移行ボタン押下後のダイアログ確認メッセージ
	$('#move_btn').on('click', function(){
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formGroupMove', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_move_group_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});

});
</script>

@endsection

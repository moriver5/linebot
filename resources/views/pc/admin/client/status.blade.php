@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_search_count) )
						<form id="formStatusPointAdd" class="form-horizontal" method="POST" action="/admin/member/client/status/search/point">
						{{ csrf_field() }}
						<center>
						<table style="width:100%;text-align:left;">
							<tr style="padding:10px;">
								<td style="text-align:left;float:left;width:100px;">								
									<button id="search" type="submit" class="btn btn-primary">
										条件設定
									</button>
								</td>
								<td style="text-align:left;width:40%;">
									検索結果：{{$db_search_count}} 件&nbsp;
									(<a href="{{ url('/admin/member/client/status/search/list') }}" target="_blank">顧客ステータス変更の一覧</a>)
								</td>
								<td style="width:90px;vertical-align:middle;padding-top:5px;">
									付与POINT：
								</td>
								<td style="width:120px;">
									<input id="point" data-placement="bottom" data-html="true" title="ポイントの加算例⇒ 100<br>ポイントの減算例⇒ -100" type="text" name="point" value="" size="10" placeholder="例：-100" style="height:29px;margin-top:3px;" autofocus> Pt
								</td>
								<td style="text-align:left;">
									<button id="point_push_btn" type="submit" class="btn btn-primary" style="height:32px;padding:3px 5px;">
										&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;
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
        </div>
    </div>
</div>

<form name="formStatusSearch" class="form-horizontal" method="POST" action="/admin/member/client/status/search/count">
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
	$('[name=point]').focusin(function(){
		$('[name=point]').attr("placeholder","");
	});

	$('[name=point]').focusout(function(){
		$('[name=point]').attr("placeholder","例：-100");
	});
	
	//条件検索ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/status/search/setting', 'status_search', 'width=800, height=1000');
		return false;
	});

	//ステータス変更のポイント付与の更新ボタン押下後のダイアログ確認メッセージ
	$('#point_push_btn').on('click', function(){
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formStatusPointAdd', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_add_point_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});
	
	//ポイント付与のヒント表示
	$('#point').tooltip();

});
</script>

@endsection

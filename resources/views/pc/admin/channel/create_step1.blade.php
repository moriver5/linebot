@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル追加&nbsp;-->&nbsp;Step1</b>
					<span style="float:right;"><a href="https://developers.line.biz/console/" target="_blank">LINE Developerへログイン</a></span>
				</div>
                <div class="panel-body">
                    <form id="formGroupAdd" class="form-horizontal" method="POST" action="/admin/member/line/channel/add/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">アプリ名</td>
											<td><input type="text" name="name" value="" size="40" maxlength="{{ config('const.group_name_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">説明</td>
											<td><input type="text" name="memo" value="" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">LINEベーシックID</td>
											<td><input type="text" id="line_basic_id" name="line_basic_id" value="" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">Channel ID</td>
											<td><input type="text" name="line_channel_id" value="" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">Channel Secret</td>
											<td><input type="text" name="line_channel_secret" value="" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">アクセストークン</td>
											<td><input type="text" name="line_token" value="" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">自分のアカウントにこのチャンネルの使用を許可</td>
											<td><input type="checkbox" name="allow_add" value="1" class="form-control" checked></td>
										</tr>
<!--
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">Webhook URL</td>
											<td><div id="webhook"></div></td>
										</tr>
-->
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="push_btn">　　　登録する　　　</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
/*
	$("#line_basic_id").keyup(function(){
		var channel = $('input[name=line_basic_id]').val();
		$("#webhook").html("{{ config('const.base_webhook_url') }}"+channel);
	});
*/
	$("#push_btn").click(function(){
		var channel = $('input[name=line_basic_id]').val();
		var redirect_url = '{{ $redirect_url }}/'+channel;

		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formGroupAdd', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_line_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, redirect_url);
	});
});
</script>

@endsection

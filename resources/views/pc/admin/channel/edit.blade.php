@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル編集</b>
				</div>
                <div class="panel-body">
                    <form id="formChannelEdit" class="form-horizontal" method="POST" action="/admin/member/line/channel/edit/{{ $db_data->line_basic_id }}/send">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">アプリ名</td>
											<td><input type="text" name="name" value="{{ $db_data->name }}" size="40" maxlength="{{ config('const.group_name_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">説明</td>
											<td><input type="text" name="memo" value="{{ $db_data->memo }}" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">LINEベーシックID</td>
											<td style="font-weight:bold;padding:10px 0 10px 10px;">{{ $db_data->line_basic_id }}</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">Channel ID</td>
											<td style="font-weight:bold;padding:10px 0 10px 10px;">{{ $db_data->line_channel_id }}</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">Channel Secret</td>
											<td><input type="text" name="line_channel_secret" value="{{ $db_data->line_channel_secret }}" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">アクセストークン</td>
											<td><input type="text" name="line_token" value="{{ $db_data->line_token }}" size="40" maxlength="{{ config('const.group_memo_max_length') }}" class="form-control"></td>
										</tr>
										<tr>
											<td style="size:10px;padding:10px;text-align:center;background:wheat;font-weight:bold;">Webhook URL</td>
											<td>
												<span id="webhook"　style="padding:10px;"><b>{{ config('const.base_webhook_url') }}{{ $db_data->line_basic_id }}</b></span>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">削除</td>
											<td><input type="checkbox" name="del_flg" value="1" class="form-control"></td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">　　　更新する　　　</button>
								<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
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
var sub_win;
$(document).ready(function(){
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formChannelEdit', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection

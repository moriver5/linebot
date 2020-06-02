@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>ASP編集</b>
				</div>
                <div class="panel-body">

                    <form id="formUpdate" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
							<center>
							<table border="1" width="97%">
								<tr>
									<td class="admin_search" style="width:110px;text-align:center;">ASP名</td>
									<td style="padding:5px;">
										<input id="asp" type="text" class="form-control" name="asp" value="{{ $db_data->asp }}" autofocus placeholder="必須入力">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:110px;text-align:center;">キックバックURL</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="url" type="text" class="form-control" name="url" value="{{ $db_data->kickback_url }}" autofocus placeholder="必須入力">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">削除</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<input type="checkbox" name="del" value="1" class="contents form-control">
									</td>
								</tr>
							</table>
							</center>
						</div>

                        <div>
                            <div style="text-align:center;">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
								<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
                            </div>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	var channel = $('[name=line_channel]').val();
	$("#url").html("<a href=\"{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"\" target=\"_blank\">{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"</a>");

	//友だち追加URL表示
	$("#ad_cd").keyup(function(){
		var channel = $('[name=line_channel]').val();
		$("#url").html("<a href=\"{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"\" target=\"_blank\">{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"</a>");
	});

	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		//別ウィンドウを開く
		window.open($('[name="url"]').val(), 'url_preview', 'width=1000, height=600');
		return false;
	});

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

	//新規作成ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
		
	});
});
</script>
@endsection


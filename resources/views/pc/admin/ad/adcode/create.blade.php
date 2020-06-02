@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>広告コード登録</b>
				</div>
                <div class="panel-body">

                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/create/send">
                        {{ csrf_field() }}

                        <div class="form-group">
							<center>
							<table border="1" width="97%">
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">LINEチャンネル</td>
									<td style="padding:5px;" colspan="3">
										<select name="line_channel" class="form-control">
										@foreach($list_channel as $index => $lines)
											<option value="{{ $lines->line_basic_id }}">{{ $lines->name }}</option>
										@endforeach
										</select>
									</td>
									<td class="admin_search" style="width:100px;text-align:center;">ASP</td>
									<td style="padding:5px;" colspan="3">
										<select id="asp" name="asp" class="form-control">
											<option value="0">なし</option>
										@foreach($list_asp as $index => $lines)
											<option value="{{ $lines->id }}">{{ $lines->asp }}</option>
										@endforeach
										</select>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">代理店</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<select name="agency_id" class="form-control">
											<option value="0">指定なし</option>
											@foreach($db_agency_data as $lines)
												<option value="{{ $lines->id }}">{{ $lines->name }}</option>
											@endforeach
										</select>
									</td>
									<td class="admin_search" style="width:75px;text-align:center;">区分</td>
									<td style="width:100px;padding:5px;" colspan="3">
									<select name="category" class="form-control">
									@foreach(config('const.ad_category') as $key => $category)
										&nbsp;<option value="{{ $key }}">{{ $category }}</option>
									@endforeach
									</select>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">広告コード</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="ad_cd" type="text" class="form-control" name="ad_cd" value="" autofocus placeholder="必須入力">
									</td>
									<td class="admin_search" style="width:100px;text-align:center;">広告コード名称</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="name" type="text" class="form-control" name="ad_name" value="" autofocus placeholder="">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">友だち追加URL</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<div id="url"></div>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">MEMO</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<textarea cols="90" rows="4" name="description" id="description" class="contents form-control"></textarea>
									</td>
								</tr>
							</table>
							</center>
						</div>

                        <div>
                            <div style="text-align:center;">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;作成する&nbsp;&nbsp;&nbsp;&nbsp;
                                </button>
								<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
                            </div>
                        </div>
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
	//友だち追加URL表示
	$("#ad_cd").keyup(function(){
		var channel = $('[name=line_channel]').val();
		$("#url").html("<a href=\"{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"\" target=\"_blank\">{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"&asp="+$("[name=asp] option:selected").val()+"</a>");
	});

	$("#asp").change(function(){
		var channel = $('[name=line_channel]').val();
		$("#url").html("<a href=\"{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"\" target=\"_blank\">{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"&asp="+$("[name=asp] option:selected").val()+"</a>");
	});

	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		//別ウィンドウを開く
		window.open($('[name="url"]').val(), 'url_preview', 'width=1000, height=600');
		return false;
	});

	//新規作成ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
		
	});
});
</script>
@endsection


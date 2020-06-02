@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>広告コード編集</b>
				</div>
                <div class="panel-body">

                    <form id="formUpdate" class="form-horizontal" method="POST" action="/admin/member/ad/adcode/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
							<center>
							<table border="1" width="97%">
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">LINEチャンネル</td>
									<td style="padding:5px;" colspan="3">
										<select name="line_channel" class="form-control">
										@foreach($list_channel as $lines)
											@if( $db_data->line_basic_id == $lines->line_basic_id )
											<option value="{{ $lines->line_basic_id }}" selected>{{ $lines->name }}</option>
											@else
											<option value="{{ $lines->line_basic_id }}">{{ $lines->name }}</option>
											@endif
										@endforeach
										</select>
									</td>
									<td class="admin_search" style="width:100px;text-align:center;">ASP</td>
									<td style="padding:5px;" colspan="3">
										<select id="asp" name="asp" class="form-control">
										@foreach($list_asp as $lines)
											@if( $db_data->asp_id == $lines[0] )
											<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
											@else
											<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>
											@endif
										@endforeach
										</select>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">代理店</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<select name="agency_id" class="form-control">
											@foreach($list_agency_data as $id => $agency)
												@if( $id == $db_data->agency_id )
													&nbsp;<option value="{{ $id }}" selected>{{ $agency }}</option>
												@else
													&nbsp;<option value="{{ $id }}">{{ $agency }}</option>										
												@endif
											@endforeach
										</select>
									</td>
									<td class="admin_search" style="width:100px;text-align:center;">区分</td>
									<td style="width:100px;padding:5px;" colspan="3">
									<select name="category" class="form-control">
									@foreach(config('const.ad_category') as $key => $category)
										@if( $key == $db_data->category )
											&nbsp;<option value="{{ $key }}" selected>{{ $category }}</option>
										@else
											&nbsp;<option value="{{ $key }}">{{ $category }}</option>
										@endif
									@endforeach
									</select>
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:100px;text-align:center;">広告コード</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="ad_cd" type="text" class="form-control" name="ad_cd" value="{{ $db_data->ad_cd }}" autofocus placeholder="必須入力">
									</td>
									<td class="admin_search" style="width:100px;text-align:center;">広告コード名称</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="name" type="text" class="form-control" name="name" value="{{ $db_data->name }}" autofocus placeholder="">
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
										<textarea cols="90" rows="4" name="description" id="description" class="contents form-control">{{ $db_data->memo }}</textarea>
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
								<button id="back_btn" type="submit" class="btn btn-primary">
									戻る
								</button>
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
	//戻るボタン押下時の戻り先をsessionStorageに保存(ブラウザを閉じるまで保存)
	if( document.referrer != location.href ){
		sessionStorage.agency_back_url = document.referrer;
	}

	var channel = $('[name=line_channel]').val();
	$("#url").html("<a href=\"{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"\" target=\"_blank\">{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"&asp="+$("[name=asp] option:selected").val()+"</a>");

	//友だち追加URL表示
	$("#ad_cd").keyup(function(){
		var channel = $('[name=line_channel]').val();
		$("#url").html("<a href=\"{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"\" target=\"_blank\">{{ config('const.line_friend_add_url') }}?pk_campaign="+channel+"&ad_cd=" + $("[name=ad_cd]").val()+"</a>");
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

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});

	//戻るボタンをクリック
	$('#back_btn').on('click', function(){
		window.location = sessionStorage.getItem('agency_back_url');
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


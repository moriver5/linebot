@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>代理店編集</b>
				</div>
                <div class="panel-body">

                    <form id="formUpdate" class="form-horizontal" method="POST" action="/admin/member/ad/agency/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
							<center>
							<table border="1" width="97%">
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">代理店</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<input id="agnecy" type="text" class="form-control" name="agency" value="{{ $db_data->name }}" autofocus placeholder="必須入力">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">ログインID</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="login_id" type="text" class="form-control" name="login_id" value="{{ $db_data->login_id }}" maxlength={{ config('const.agency_login_id_max_length') }} autofocus placeholder="必須入力">
									</td>
									<td class="admin_search" style="width:75px;text-align:center;">パスワード</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="password" type="text" class="form-control" name="password" value="{{ $db_data->password_raw }}" maxlength={{ config('const.password_max_length') }} autofocus placeholder="必須入力">
									</td>	
								</tr>
								<tr>
									<td class="admin_search" style="width:75px;text-align:center;">MEMO</td>
									<td style="width:50px;padding:5px;" colspan=5">
										<textarea cols="90" rows="4" name="description" id="description" class="contents form-control">{!! htmlspecialchars($db_data->memo) !!}</textarea>
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

	//戻るボタンをクリック
	$('#back_btn').on('click', function(){
		window.location = sessionStorage.getItem('agency_back_url');
		return false;
	});

	//新規作成ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formUpdate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});
});
</script>
@endsection


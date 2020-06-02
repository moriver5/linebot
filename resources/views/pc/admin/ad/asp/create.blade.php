@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>ASP登録</b>
				</div>
                <div class="panel-body">

                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/ad/asp/create/send">
                        {{ csrf_field() }}

                        <div class="form-group">
							<center>
							<table border="1" width="97%">
								<tr>
									<td class="admin_search" style="width:110px;text-align:center;">ASP名</td>
									<td style="padding:5px;">
										<input id="asp" type="text" class="form-control" name="asp" value="" autofocus placeholder="必須入力">
									</td>
								</tr>
								<tr>
									<td class="admin_search" style="width:110px;text-align:center;">キックバックURL</td>
									<td style="width:50px;padding:5px;" colspan=3">
										<input id="url" type="text" class="form-control" name="url" value="" autofocus placeholder="必須入力">
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

	//新規作成ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.add_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
		
	});
});
</script>
@endsection


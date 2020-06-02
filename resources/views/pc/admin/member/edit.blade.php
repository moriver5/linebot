@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><b>アカウント編集</b></div>
                <div class="panel-body">
                    <form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/edit/send">
                        {{ csrf_field() }}
<!--
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">ログインID</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $admin_login_id }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
-->
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">ログインID</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="{{ $admin_email }}" maxlength={{ config('const.email_length') }} required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">管理区分</label>

                            <div class="col-md-6">
								@if( $operate_type == config('const.admin_system_auth_id') )
	                                <select name="type" class="form-control">
								@else
	                                <select name="type" class="form-control" disabled>
								@endif
									@foreach($admin_auth_list as $index => $auth_name)
										@if( $index == $admin_auth_type )
											<option value="{{ $index }}" selected>{{ $auth_name }}</option>
										@else
											<option value="{{ $index }}">{{ $auth_name }}</option>
										@endif
									@endforeach
								</select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">閲覧を許可する<br />LINEチャンネル</label>

                            <div class="col-md-6">
								@foreach($list_line_channel as $lines)
									@if( array_search($lines->line_basic_id, $list_allow_line_channel) !== false )
									<input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}" checked> {{ $lines->name }}<br />
									@else
									<input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}"> {{ $lines->name }}<br />
									@endif
								@endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="del" class="col-md-4 control-label">削除</label>

                            <div class="col-md-6">
								@if( $operate_type == config('const.admin_system_auth_id') )
									<input id="del" type="checkbox" class="form-control" name="del" value="1" autofocus>
								@else
									<input id="del" type="checkbox" class="form-control" name="del" value="1" autofocus disabled>
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button id="account_btn" type="submit" class="btn btn-primary">
                                    アカウント更新
                                </button>
                                <button id="back_btn" type="submit" class="btn btn-primary">
                                    戻る
                                </button>
                            </div>
                        </div>
						<input type="hidden" name="id" value="{{ $admin_type }}">
						<input type="hidden" name="page" value="{{ $page }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	//戻るボタンクリック
	$('#back_btn').click(function(){
		window.location.href = '{{ config('const.base_admin_url') }}/{{ config('const.member_home_url_path') }}?page={{ $page }}';
		return false;
	});
	
	//アカウント編集ボタンを押下
	$('#account_btn').click(function(){
		var alert_msg,alert_end_msg;

		//削除チェックボックスの値を取得
		var del_flg = $('[name=del]:checked').val();

		//削除メッセージ設定
		if( del_flg == 1 ){
			alert_msg = '{{ __('messages.dialog_del_alert_msg') }}';
			alert_end_msg = '{{ __('messages.dialog_del_end_msg') }}';

		//編集メッセージ設定
		}else{
			alert_msg = '{{ __('messages.dialog_alert_msg') }}';
			alert_end_msg = '{{ __('messages.account_edit_end') }}';			
		}
		//アカウント編集ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト、msg非表示フラグ、redirectフラグ、redirect先パス
		submitAlert('formEdit', 'post', '{{ __('messages.dialog_alert_title') }}', alert_msg, alert_end_msg, '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	});
});
</script>

@endsection

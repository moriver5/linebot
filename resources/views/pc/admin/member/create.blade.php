@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><b>アカウント登録</b></div>
                <div class="panel-body">
                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/create/send">
                        {{ csrf_field() }}
<!--
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">ログインID</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
-->
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" maxlength={{ config('const.email_length') }} placeholder="登録したメールアドレスがログインIDとなります" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
<!--
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">パスワード</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">確認パスワード</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
-->
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">管理区分</label>

                            <div class="col-md-6">
                                <select name="type" class="form-control">
									@foreach($admin_auth_list as $index => $auth_name)
									<option value="{{ $index }}">{{ $auth_name }}</option>
									@endforeach
								</select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">閲覧を許可する<br />LINEチャンネル</label>

                            <div class="col-md-6">
								@foreach($list_line_channel as $lines)
                                <input type="checkbox" name="line_channel[]" value="{{ $lines->line_basic_id }}"> {{ $lines->name }}<br />
								@endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    アカウント作成
                                </button>
                                <button id="back_btn" type="submit" class="btn btn-primary">
                                    戻る
                                </button>
                            </div>
                        </div>
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
	$('[name=email]').focusin(function(){
		$('[name=email]').attr("placeholder","");
	});

	$('[name=email]').focusout(function(){
		$('[name=email]').attr("placeholder","登録したメールアドレスがログインIDとなります");
	});
	
	//戻るボタンクリック
	$('#back_btn').click(function(){
		window.location.href = '{{ config('const.base_admin_url') }}/{{ config('const.member_home_url_path') }}?page={{ $page }}';
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.account_provision_create') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection

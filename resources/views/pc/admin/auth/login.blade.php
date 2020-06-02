@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><b>LINE BOT管理ログイン</b></div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="/admin/login">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') || session('message') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">ログインID</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" maxlength={{ config('const.email_length') }} placeholder="登録したメールアドレスを入力してください"　required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
								@if (session('message'))
									<span class="help-block">
										<strong>{{ session('message') }}</strong>
									</span>
								@endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">パスワード</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" maxlength={{ config('const.password_max_length') }} required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
<!--
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 自動ログイン
                                    </label>
                                </div>
                            </div>
                        </div>
-->
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    ログイン
                                </button>

                                <a class="btn btn-link" href="/admin/forget">
                                    パスワード再設定
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('[name=email]').focusin(function(){
		$('[name=email]').attr("placeholder","");
	});

	$('[name=email]').focusout(function(){
		$('[name=email]').attr("placeholder","登録したメールアドレスを入力してください");
	});
});
</script>
	
@endsection

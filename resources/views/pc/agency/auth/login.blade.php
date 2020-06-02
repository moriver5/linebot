@extends('layouts.agency_app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><b>ログイン</b></div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="/agency/login">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('login_id') || session('message') ? ' has-error' : '' }}">
                            <label for="login_id" class="col-md-4 control-label">ログインID</label>

                            <div class="col-md-6">
                                <input id="login_id" type="text" class="form-control" name="login_id" value="{{ $login_id }}" maxlength={{ config('const.agency_login_id_max_length') }} required autofocus>

                                @if ($errors->has('login_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('login_id') }}</strong>
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
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    ログイン
                                </button>
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

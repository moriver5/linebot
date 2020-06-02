<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>メルマガ配信 管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body>
<br />
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>LP登録</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">

                    <form id="formCreate" class="form-horizontal" method="POST" action="/admin/member/lp/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="domain" class="col-md-2 control-label admin_default" style="color:black;">ドメイン</label>
                            <div class="col-md-2">
								<select id="domain" class="form-control" name="domain">
									@foreach($list_domain as $lines)
										@if( $db_data->domain == $lines->domain )
										<option value='{{$lines->domain}}' selected>{{$lines->domain}}</option>
										@else
										<option value='{{$lines->domain}}'>{{$lines->domain}}</option>
										@endif
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
                            <label for="open_flg" class="col-md-2 control-label admin_default" style="color:black;">公開</label>
                            <div class="col-md-2">
								<select id="open_flg" class="form-control" name="open_flg">
									@foreach($list_open_flg as $lines)
										@if( $db_data->open_flg == $lines[0] )
										<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>
										@else
										<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
										@endif
									@endforeach
								</select>
                            </div>						
                        </div>

                        <div class="form-group">
							<label for="type" class="col-md-2 control-label admin_default" style="color:black;">MEMO</label>

							<div class="col-md-6">
								<textarea cols="90" rows="6" name="description" id="description" class="contents form-control">{{ $db_data->memo }}</textarea>
                            </div>
						</div>

                        <div class="form-group">
							<label for="type" class="col-md-2 control-label admin_default" style="color:black;">piwikのID</label>

							<div class="col-md-1">
								<input type="text" name="piwik_id" value="{{ $db_data->piwik_id }}" class="contents form-control">
                            </div>
							<div class="col-md-4" style="color:red;font-weight:bold;">
								※設定する場合はpiwikで登録したIDを半角数字で入力します
                            </div>
						</div>

                        <div class="form-group">
                            <label for="del" class="col-md-2 control-label admin_default" style="color:black;">削除</label>

                            <div class="col-md-3">
                                <input id="del" type="checkbox" class="form-control" name="del" value="1">(すべてのランディングページが削除されます)
                            </div>
                        </div>

                        <div>
                            <div style="text-align:center;">
                                <button id="push_btn" type="submit" class="btn btn-primary">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	//登録ボタンを押下
	$('#push_btn').click(function(){
		//新規作成ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCreate', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});
});
</script>

</body>
</html>


@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="font:normal 13px/130% 'メイリオ',sans-serif;">
                <div class="panel-heading"><b>顧客情報編集</b></div>
                <div class="panel-body">
                    <form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/client/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-2 control-label">顧客ID</label>
                            <div class="col-md-3" style="padding-top:7px;">
								{{ $db_data->id }}
                            </div>

                            <label for="status" class="col-md-4 control-label">登録状態</label>
                            <div class="col-md-2">
								<select id="status" class="form-control" name="status">
									@foreach(config('const.regist_status') as $lines)
										@if( $db_data->status == $lines[0] )
											<option value='{{$lines[0]}}' selected>{{$lines[1]}}</option>
										@else
											<option value='{{$lines[0]}}'>{{$lines[1]}}</option>										
										@endif
									@endforeach
								</select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sex" class="col-md-2 control-label">性別</label>

                            <div class="col-md-2">
								<select id="group_id" class="form-control" name="sex">
									@foreach($list_sex as $index => $value)
										@if( $db_data->sex == $index )
											<option value='{{$index}}' selected>{{$value}}</option>
										@else
											<option value='{{$index}}'>{{$value}}</option>										
										@endif
									@endforeach
								</select>
                            </div>

							<label for="age" class="col-md-5 control-label">年代</label>
                            <div class="col-md-3">
								<select id="group_id" class="form-control" name="age">
									@foreach($list_age as $index => $value)
										@if( $db_data->age == $index )
											<option value='{{$index}}' selected>{{$value}}</option>
										@else
											<option value='{{$index}}'>{{$value}}</option>										
										@endif
									@endforeach
								</select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-md-2 control-label">グループ</label>

                            <div class="col-md-5" style="padding-top:7px;">
								{{ $group_name }}
							</div>

                            <label for="group_id" class="col-md-2 control-label">広告コード</label>

                            <div class="col-md-3">
								<input id="ad_cd" type="text" class="form-control" name="ad_cd" value="{{ $db_data->ad_cd }}">
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ad_cd') ? ' has-error' : '' }}">
                            <label for="remember_token" class="col-md-2 control-label">アクセスキー</label>

                            <div class="col-md-4" style="padding-top:7px;">
                                {{ $db_data->remember_token }}
                            </div>

                            <label for="ad_cd" class="col-md-3 control-label">登録日時</label>

                            <div class="col-md-3" style="padding-top:7px;">
                                {{ $db_data->created_at }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="created_at" class="col-md-2 control-label">最終アクセス</label>

                            <div class="col-md-3" style="padding-top:7px;">
                                {{ preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", "$1/$2/$3 $4:$5", $db_data->last_access) }}
                            </div>

                            <label for="updated_at" class="col-md-4 control-label">退会日時</label>

                            <div class="col-md-4" style="padding-top:7px;">
                                {{ $db_data->quit_datetime }}
                            </div>
                        </div>

                        <div class="form-group">
							<label for="name" class="col-md-2 control-label">メルマガ無効</label>
                            <div class="col-md-1">
								@if( !empty($db_data->disable) )
									<input type="checkbox" class="form-control" name="del" value="1" checked>
								@else
									<input type="checkbox" class="form-control" name="del" value="1">
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-11 col-md-offset-4">
                                <button id="push_btn" type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;
                                </button>
                                <button id="push_melmaga_btn" type="submit" class="btn btn-primary">
                                    メルマガ履歴
                                </button>
								@if( $back_btn_flg )
                                <button id="back_btn" type="submit" class="btn btn-primary">
                                   &nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;
                                </button>
								@endif
                            </div>
                        </div>
					<input type='hidden' name='client_id' value='{{ $client_id }}'>
					<input type='hidden' name='group_id' value='{{ $group_id }}'>
					<input type='hidden' name='page' value='{{ $page }}'>
					<input type='hidden' name='regist_date' value='{{ $db_data->regist_date }}'>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var add_point_win;
var add_order_win;
var order_history_win;
var send_mail_win;
var melmaga_win;
$(document).ready(function(){
	//戻るボタンクリック
	$('#back_btn').click(function(){
		window.location.href = '{{ $back_url }}'
		return false;
	});

	//ポイント手動追加ボタン押下
	$('#push_point_btn').click(function(){
		add_point_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/point/add', 'point_add', 'width=600, height=350');
		return false;
	});

	//個別メールボタン押下
	$('#push_mail_btn').click(function(){
		send_mail_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/mail/view', 'edit_mail', 'width=600, height=850');
		return false;
	});

	//注文追加ボタン押下
	$('#push_order_btn').click(function(){
		add_order_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/order/add', 'point_order', 'width=700, height=550');
		return false;
	});

	//注文履歴ボタン押下
	$('#push_order_history_btn').click(function(){
		order_history_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/order/history', 'order_history', 'width=800, height=700');
		return false;
	});

	//メルマガ履歴ボタン押下
	$('#push_melmaga_btn').click(function(){
		melmaga_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/melmaga/history', 'melmaga_history', 'width=1000, height=500');
		return false;
	});

	//アカウント編集ボタンを押下
	$('#push_btn').click(function(){
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

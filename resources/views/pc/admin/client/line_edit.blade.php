@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default" style="font:normal 13px/130% 'メイリオ',sans-serif;">
                <div class="panel-heading"><b>LINE プロフィール</b></div>
                <div class="panel-body">
                    <form id="formEdit" class="form-horizontal" method="POST" action="/admin/member/client/edit/send">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-3 control-label">プロフィール画像</label>
                            <div class="col-md-6" style="padding-top:7px;">
								<img src="{{ $db_data->image }}" width="160" height="160">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-3 control-label">LINE ID</label>
                            <div class="col-md-6" style="padding-top:7px;">
								{{ $db_data->user_line_id }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-md-3 control-label">登録状態</label>
                            <div class="col-md-6" style="padding-top:7px;">
								@if( !empty($db_data->follow_flg) )
									友だち登録済
								@else
									<span style="color:red;font-weight:bold;">ブロック</span>
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-3 control-label">LINEネーム</label>
                            <div class="col-md-6" style="padding-top:7px;">
								{{ $db_data->name }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-md-3 control-label">ステータスメッセージ</label>
                            <div class="col-md-6" style="padding-top:7px;">
								{{ $db_data->message }}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ad_cd') ? ' has-error' : '' }}">
                            <label for="ad_cd" class="col-md-3 control-label">登録日時</label>

                            <div class="col-md-6" style="padding-top:7px;">
                                {{ $db_data->created_at }}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ad_cd') ? ' has-error' : '' }}">
                            <label for="created_at" class="col-md-3 control-label">最終アクセス</label>

                            <div class="col-md-6" style="padding-top:7px;">
                                {{ preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", "$1/$2/$3 $4:$5", $db_data->updated_at) }}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ad_cd') ? ' has-error' : '' }}">
                            <label for="created_at" class="col-md-3 control-label">最終クリック日時</label>

                            <div class="col-md-6" style="padding-top:7px;">
                                {{ preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", "$1/$2/$3 $4:$5", $last_click_date) }}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('ad_cd') ? ' has-error' : '' }}">
                            <label for="created_at" class="col-md-3 control-label">24Hブロック</label>

                            <div class="col-md-6" style="padding-top:7px;">
                                @if( $db_data->block24h == 1 )
									あり
								@else
									なし
								@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="group_id" class="col-md-3 control-label">広告コード</label>

                            <div class="col-md-6" style="padding-top:7px;">
								{{ $db_data->ad_cd }}
                            </div>
                        </div>

                        <div class="form-group">
							<label for="name" class="col-md-3 control-label">アカウント無効</label>
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
                                    LINEメッセージ履歴
                                </button>
								@if( $back_btn_flg )
                                <a href="javascript:history.back();" class="btn btn-primary">戻る</a>
								@endif
                            </div>
                        </div>
					<input type='hidden' name='basic_id' value='{{ $basic_id }}'>
					<input type='hidden' name='line_id' value='{{ $line_id }}'>
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

	//メルマガ履歴ボタン押下
	$('#push_melmaga_btn').click(function(){
		melmaga_win = window.open('/admin/member/client/edit/{{ $db_data->id }}/melmaga/history', 'melmaga_history', 'width=1000, height=500');
		return false;
	});

	//アカウント編集ボタンを押下
	$('#push_btn').click(function(){
		var alert_msg,alert_end_msg;

		//アカウント無効チェックボックスの値を取得
		var del_flg = $('[name=del]:checked').val();

		//無効メッセージ設定
		if( del_flg == 1 ){
			alert_msg = '{{ __('messages.dialog_disable_alert_msg') }}';
			alert_end_msg = '{{ __('messages.dialog_disable_end_msg') }}';

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

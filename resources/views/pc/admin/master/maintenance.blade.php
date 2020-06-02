@extends('layouts.app')

@section('content')
<br />
<br />

<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>メンテナンス設定</b>
				</div>
                <div class="panel-body">
					<b><font color="red" style="font-size:11px;">※ONにしてから競馬サイト・管理画面へ社外からアクセスすると即座にメンテナンスモード表示となり、<br />社外から変更した場合は直せなくなりますので慎重に実行してください。</font></b>
                    <form id="formMaintenance" class="form-horizontal" method="POST" action="/admin/member/maintenance/setting/send">
						{{ csrf_field() }}
						<center>
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="3"><b>テンプレート</b></td>
										</tr>
										<tr>
											<td style="text-align:center;padding:5px;">
												<b>モード</b>
											</td>
											<td style="text-align:left;padding:5px;">
												@if( empty($db_data) )
													ON <input type="radio" name="mode" value="1">　
													OFF <input type="radio" name="mode" value="0" checked>
												@else
													@if( $db_data->mode == 1 )
													<span style="background:yellow;font-weight:bold;padding:5px;border:1px solid black;"><span class="blinking">ON</span> <input type="radio" name="mode" value="1" checked></span>
													@else
														ON <input type="radio" name="mode" value="1">													
													@endif
													@if( $db_data->mode == 0 )
														<span style="background:gainsboro;font-weight:bold;padding:5px;border:1px solid black;">OFF <input type="radio" name="mode" value="0" checked></span>											
													@else
														OFF <input type="radio" name="mode" value="0">													
													@endif
												@endif
											</td>
										</tr>
										<tr style="text-align:center;">
											<td style="text-align:center;padding:5px;">
												<b>表示文言</b>
											</td>
											<td>
												@if( !empty($db_data) )
												<textarea cols="60" rows="20" id="message" name="message" class="form-control contents" placeholder="メンテナンス中">{{ $db_data->body }}</textarea>
												@else
												<textarea cols="60" rows="20" id="message" name="message" class="form-control contents" placeholder="メンテナンス中"></textarea>
												@endif
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary">いますぐメンテナンスモードを変更</button>
								<button type="submit" id="push_preview" class="btn btn-primary">プレビュー</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var prev_win;
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
	});

	//プレビューボタン押下
	$('#push_preview').on('click', function(){
		prev_win = window.open('/admin/member/maintenance/setting/preview', 'maintenance_preview', 'width=1000, height=500');
		prev_win.onload = function(){
			var dom = prev_win.document.getElementById('message');
			$(dom).html($('[name="message"]').val());
		};
		return false;
	});

	//プレビュー機能
	$('.contents').keyup(function(){
		//編集した内容を更新用パラメータに設定
		$('[name="message"]').val($('[name="message"]').val());

		//プレビュー処理
		if( prev_win ){
			var dom = prev_win.document.getElementById('message');
			$(dom).html($('[name="message"]').val());
		}
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMaintenance', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_maintenance_alert_msg') }}', '{{ __('messages.dialog_maintenance_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection

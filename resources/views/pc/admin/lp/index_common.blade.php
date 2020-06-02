@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>ファイル登録</b>
				</div>
                <div class="panel-body">
                    <form id="formFileUpload" class="form-horizontal" method="POST" action="/admin/member/lp/common/create/upload">
                        {{ csrf_field() }}
                        <div class="form-group">
							<table>
								<tr>
									<td>
										<div class="col-md-10"　id="file_upload_section" style="width:100%;">
											<div id="drop" style="text-align:center;width:800px;height:180px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formFileUpload', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　[], 'post', '10000', '')">
												<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br />(複数選択のアップロード可能)</div>
												<center><div id="result" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;width:100%;"></div></center>
											</div>
										</div>
									</td>
								</tr>
							</table>
                        </div>
					</form>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>アップロードファイル一覧</b>
				</div>
                <div class="panel-body">
					<table border="1" align="center" width="100%" style="text-align:center;">
						<tr>
							<td style="padding:5px;background:wheat;">
								拡張子
							</td>
							<td style="padding:5px;background:wheat;">
								ファイル保存先
							</td>
							<td style="padding:5px;background:wheat;">
								URL例
							</td>
						</tr>
						<tr>
							<td>
								下記以外
							</td>
							<td style="padding:5px;text-align:left;">
								/
							</td>
							<td style="padding:5px;text-align:left;">
								{{ $base_url }}/test.html
							</td>
						</tr>
						<tr>
							<td>
								css
							</td>
							<td style="padding:5px;text-align:left;">
								/common/css
							</td>
							<td style="padding:5px;text-align:left;">
								{{ $base_url }}/common/css/style.css
							</td>
						</tr>
						<tr>
							<td>
								js
							</td>
							<td style="padding:5px;text-align:left;">
								/common/js
							</td>
							<td style="padding:5px;text-align:left;">
								{{ $base_url }}/common/js/alert.js
							</td>
						</tr>
						<tr>
							<td>
								jpg<br />png<br />gif<br />ico
							</td>
							<td style="padding:5px;text-align:left;">
								/common/images
							</td>
							<td style="padding:5px;text-align:left;">
								{{ $base_url }}/common/images/logo.png
							</td>
						</tr>
					</table>
					<br />
					<!-- ファイルが登録されていれば表示 -->
					@if( !empty($list_file) )
						<form id="formImgDel" class="form-horizontal" method="POST" action="/admin/member/lp/common/delete">
						{{ csrf_field() }}
						<table style="width:100%;" rules="rows" border="1">
							<tr>
								<td style="text-align:center;padding:5px;border:1px solid #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
									URL
								</td>
								<td style="text-align:center;padding:5px;border:1px solid #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
									削除
								</td>
							</tr>
						@foreach($list_file as $index => $lines)
							<tr>
								<td style="padding:5px;border:none;border:1px solid #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
									<b><a href="{{ $lines['url'] }}" target="_blank">{{ $lines['url'] }}</a></b>
								</td>
								<td style="text-align:center;padding:5px;border:none;border:1px solid #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
									<input type="checkbox" name="file[]" value="{{ $lines['path'] }}">
								</td>
							</tr>
						@endforeach
						</table>
						<br />
						<div>
							<div style="text-align:center;">
								<button id="push_btn" type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ファイル削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</button>
							</div>
						</div>
					@else
						<div style="text-align:center;"><b>ファイル未設定</b></div>
					@endif
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

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//画像削除ボタンを押下
	$('#push_btn').click(function(){
		//画像削除ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formImgDel', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_del_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false);
	});
});
</script>

@endsection

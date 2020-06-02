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
	<link href="{{ asset('css/admin/lightbox.css') }}" rel="stylesheet" />

	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>
	<script src="{{ asset('js/admin/lightbox.js') }}"></script>

</head>
<body>
<br />
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>PDF/画像登録</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">
                    <form id="formImageUpload" class="form-horizontal" method="POST" action="/admin/member/lp/create/img/{{ $id }}/upload">
                        {{ csrf_field() }}
                        <div class="form-group">
							<table>
								<tr>
									<td>
										<div class="col-md-10"　id="file_upload_section" style="width:100%;">
											<div id="drop" style="text-align:center;width:700px;height:180px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formImageUpload', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id'], 'post', '10000', '{{ $redirect_url }}')">
												<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br />(複数選択のPDF/画像アップロード可能)</div>
												<center><div id="result" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;width:100%;"></div></center>
											</div>
										</div>
									</td>
								</tr>
							</table>
                        </div>
						<input type="hidden" name="edit_id" value="{{ $id }}">
					</form>
                </div>
            </div>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default" style="font-size:12px;">
                <div class="panel-heading">
					<b>アップロードPDF/画像</b>
				</div>
                <div class="panel-body">
					<!-- 画像が登録されていれば表示 -->
					@if( !empty($list_img) )
						<form id="formImgDel" class="form-horizontal" method="POST" action="{{ $post_url }}">
						{{ csrf_field() }}
						<table style="width:100%;" rules="rows">
						@foreach($list_img as $index => $img)
							@if( $index % 4 == 0 )
								<tr>
							@endif
								<td style="padding:5px;border:none;border-bottom:1px dotted #aaa;color:black;font:bold 12px/120% 'メイリオ',sans-serif;">
									<input type="checkbox" name="img[]" value="{{ $img }}">
									@if( preg_match("/pdf$/", $img) > 0 )
									<b><a href="{{ $img_url }}/{{ $img }}" target="_blank">{{ $img }}</a></b>
									@else
									<b><a href="{{ $img_url }}/{{ $img }}" data-lightbox="roadtrip">{{ $img }}</a></b>
									@endif
								</td>
							@if( $index % 4 == 3 )
								</tr>
							@endif
						@endforeach
						</table>
						<br />
						<div>
							<div style="text-align:center;">
								<button id="push_btn" type="submit" class="btn btn-primary">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PDF/画像削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</button>
							</div>
						</div>
					@else
						<div style="text-align:center;"><b>PDF/画像未設定</b></div>
					@endif
					<div style="color:black;font:bold 13px/130% 'メイリオ',sans-serif;">
						<hr>
						<b>
							※ PDF/画像を使用する際には<br />
							&lt;img src="/img/[image名]" alt="***" width="***" height="***" /&gt;<br />
							例）&lt;img src="/img/test.jpg" alt="test" width="100" height="50" /&gt;<br />
							<br />
							&lt;a href="/img/[PDF名]"&gt;PDF&lt;/a&gt;<br />
							例）&lt;a href="/img/test.pdf"&gt;PDF&lt;/a&gt;
						</b>
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
    lightbox.option({
		'alwaysShowNavOnTouchDevices':true,
		'wrapAround': true
    });

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

</body>
</html>

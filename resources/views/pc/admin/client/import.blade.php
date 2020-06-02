@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">

            <div class="panel panel-default">
                <div class="panel-body">
						<form id="formImport" enctype="multipart/form-data" class="form-horizontal" method="POST" action="/admin/member/client/import/upload">
						{{ csrf_field() }}
						<center>
						<b>顧客データインポート</b><br />
						<br />
						<table border="1" align="center" width="100%" style="text-align:center;">
							<tr>
								<td style="padding:5px;background:wheat;">
									フォーマット
								</td>
								<td colspan="7">
									メールアドレス,グループID,登録状況,広告コード,性別,年代,備考
								</td>
							</tr>
							<tr>
								<td style="padding:5px;background:wheat;">
									項目名
								</td>
								<td>
									メールアドレス
								</td>
								<td>
									グループID
								</td>
								<td>
									登録状況
								</td>
								<td>
									広告コード
								</td>
								<td>
									性別
								</td>
								<td>
									年代
								</td>
								<td>
									備考
								</td>
							</tr>
							<tr>
								<td style="padding:5px;background:wheat;">
									必須
								</td>
								<td>
									●
								</td>
								<td>
									●
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
							</tr>
							<tr>
								<td style="padding:5px;background:wheat;">
									デフォルト
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									1(購読済)
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
								<td>
									
								</td>
							</tr>
						</table>
						<br />
						<table border=1 style="border:1px solid gray;width:100%;text-align:left;">
							<tr style="text-align:center;padding:10px;">
<!--
								<td style="width:150px;vertical-align:middle;font-weight:bold;">
									広告コード
								</td>
-->
								<td style="width:500px;vertical-align:middle;padding:5px;">
									<input id="ad_cd" data-placement="bottom" data-html="true" type="text" name="ad_cd" value="" placeholder="こちらに広告コードを入力すると優先されて登録されます" size="20" class="form-control" style="height:29px;margin-top:3px;" autofocus>
								</td>
							</tr>
							<tr style="text-align:center;padding:10px;">
<!--
								<td style="width:150px;font-weight:bold;">
									アップロードファイル
								</td>
-->
								<td style="width:500px;padding:5px;">
									<div id="file_upload_section">
										<div id="drop" style="width:600px; height:300px; vertical-align:middle; display:table-cell; border:3px solid burlywood;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formImport', 'import_file', '{{csrf_token()}}', '{{ __('messages.dialog_upload_end_msg') }}', '{{ __('messages.dialog_upload_error_msg') }}',　['ad_cd'], 'post', '10000')">
											<div style="font:italic normal bold 18px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください</div>
											<center><div id="result" style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;margin:20px;width:300px;"></div></center>
										</div>
									</div>
								</td>
							</tr>
<!--
							<tr style="text-align:center;padding:10px;">
								<td style="width:120px;padding:5px;">
									<input data-placement="bottom" data-html="true" type="file" name="import_file" value="" style="height:29px;margin-top:3px;" autofocus>
								</td>
								<td style="width:120px;padding:5px;">
									<button id="push_btn" type="submit" class="btn btn-primary">
										アップロード
									</button>
								</td>
							</tr>
-->
						</table>
						</center>
						</form>
						<br />
						<table border=1 style="border:1px solid gray;width:100%;text-align:left;">
							<tr style="text-align:center;padding:10px;">
								<td style="width:240px;vertical-align:middle;padding:5px;background:wheat;">
									不正メールアドレス
								</td>
								<td style="width:500px;vertical-align:middle;padding:5px;">
									@if( !empty($bad_email_flg) )
									<a href="/admin/member/client/import/dl/bad_email">ファイルダウンロード</a>
									@else
									<font color="gray">ありません</font>
									@endif
								</td>
								<td style="width:50px;vertical-align:middle;padding:5px;">
									@if( !empty($bad_email_flg) )
									<a id="bad_email">削除</a>
									@else
									<font color="gray">削除</font>
									@endif
								</td>
							</tr>
							<tr style="text-align:center;padding:10px;">
								<td style="width:240px;vertical-align:middle;padding:5px;background:wheat;">
									宛先不明ドメイン
								</td>
								<td style="width:500px;vertical-align:middle;padding:5px;">
									@if( !empty($mx_domain_flg) )
									<a href="/admin/member/client/import/dl/unknown_mx_domain">ファイルダウンロード</a>
									@else
									<font color="gray">ありません</font>
									@endif
								</td>
								<td style="width:50px;vertical-align:middle;padding:5px;">
									@if( !empty($mx_domain_flg) )
									<a id="mx_domain">削除</a>
									@else
									<font color="gray">削除</font>
									@endif
								</td>
							</tr>
							<tr style="text-align:center;padding:10px;">
								<td style="width:240px;vertical-align:middle;padding:5px;background:wheat;">
									重複メールアドレス
								</td>
								<td style="width:500px;vertical-align:middle;padding:5px;">
									@if( !empty($duplicate_flg) )
									<a href="/admin/member/client/import/dl/duplicate_email">ファイルダウンロード</a>
									@else
									<font color="gray">ありません</font>
									@endif
								</td>
								<td style="width:50px;vertical-align:middle;padding:5px;">
									@if( !empty($duplicate_flg) )
									<a id="duplicate">削除</a>
									@else
									<font color="gray">削除</font>
									@endif
								</td>
							</tr>
						</table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/ajax.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">

$(document).ready(function(){
	$('[name=ad_cd]').focusin(function(){
		$('[name=ad_cd]').attr("placeholder","");
	});

	$('[name=ad_cd]').focusout(function(){
		$('[name=ad_cd]').attr("placeholder","広告コードがある場合はここに入力してください");
	});

    //リターンキーでのpostを無効にする
    $("input").keydown(function(e) {
        if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
                return false;
        } else {
                return true;
        }
    });

	//不正メールアドレスの削除リンク押下
	$('#bad_email').on('click', function(){
		//確認ダイアログ表示
		swal({
		  title: '{{ __('messages.dialog_alert_title') }}',
		  text: '{{ __('messages.dialog_del_alert_msg') }}',
		  icon: "warning",
		  buttons: true,
		  dangerMode: true,
		})
		.then((exec_flg) => {
			if( exec_flg ){
				location.href = '/admin/member/client/import/del/bad_email';
			}else{
				return false;
			}
		});
	});

	//宛先不明ドメインの削除リンク押下
	$('#mx_domain').on('click', function(){
		//確認ダイアログ表示
		swal({
		  title: '{{ __('messages.dialog_alert_title') }}',
		  text: '{{ __('messages.dialog_del_alert_msg') }}',
		  icon: "warning",
		  buttons: true,
		  dangerMode: true,
		})
		.then((exec_flg) => {
			if( exec_flg ){
				location.href = '/admin/member/client/import/del/unknown_mx_domain';
			}else{
				return false;
			}
		});
	});

	//重複メールアドレスの削除リンク押下
	$('#duplicate').on('click', function(){
		//確認ダイアログ表示
		swal({
		  title: '{{ __('messages.dialog_alert_title') }}',
		  text: '{{ __('messages.dialog_del_alert_msg') }}',
		  icon: "warning",
		  buttons: true,
		  dangerMode: true,
		})
		.then((exec_flg) => {
			if( exec_flg ){
				location.href = '/admin/member/client/import/del/duplicate_email';
			}else{
				return false;
			}
		});
	});
});
</script>

@endsection

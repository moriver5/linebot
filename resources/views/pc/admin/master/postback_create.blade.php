@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>ポストバック設定&nbsp;&nbsp;&nbsp;&nbsp;</b>
				</div>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;">
								<table width="95%" id="message_form">
									<tr style="text-align:center;border:1px solid darkgray;background:#9FF781;">
										<td>
											<div style="text-align:left;padding:5px;"><b>共通</b></div>
										</td>
									</tr>
									<tr style="text-align:center;border:1px solid darkgray;">
										<td style="padding:15px;">
											<table width="100%">
												<tr style="text-align:center;">
													<td>
														<div style="text-align:left;font-size:12px;font-weight:bold;"><b>管理名</b></div>
														<input type="text" id="postback_name" name="tmp_postback_name" class="form-control" value="{{ $title }}" placeholder="">
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr class="disp_cloumn" style="display:none;text-align:center;">
										<td style="padding:10px;"></td>
									</tr>
									<tr class="disp_cloumn" style="display:none;text-align:center;border-top:1px solid darkgray;border-left:1px solid darkgray;border-right:1px solid darkgray;background:#9FF781;">
										<td>
											<div style="float:left;text-align:left;padding:5px;"><b id="click_cloumn">アクションの登録</b></div>
										</td>
									</tr>
									@if( !empty($list_msg) )
										<tr style="border:1px solid black;text-align:center;">
											<td>
												<span style="font-size:13px;float:left;padding:5px;">ポストバック値：{{ $db_data->postback }}</span>
											</td>
										</tr>
										@foreach($list_msg as $name => $msg)
											@if( preg_match("/\.(png|jpg|jpeg)$/", $msg) > 0 )
											<tr style="text-align:center;font-weight:bold;">
												<td>
												<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/setting/postback/create/img/{{ $channel_id }}/upload">
												<table border="1" width="100%">
													<tr style="text-align:center;">
														<td style="width:70%;">
														<div id="file_upload_section{{ $name }}" style="width:100%;">
															<div id="drop{{ $name }}" style="text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop{{ $name }}', 'white')" ondragover="onDragOver(event, 'drop{{ $name }}', 'wheat')" ondrop="onDrop(event, 'formLine{{ $name }}', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg'], 'post', '10000', '{{ $redirect_url }}', '{{ $name }}')">
																<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br><br>反映されないときはctrl+F5を押してください</div>
																<center><div id="result{{ $name }}" style="font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;"></div></center>
															</div>
														</div>
														<input type="hidden" name="page" value="1">
														<input type="hidden" name="msg" value="{{ $name }}">
														<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
														<input type="hidden" name="edit_id" value="{{ $edit_id }}">
														<input type="hidden" name="push_title" value="">
														</td>
														<td style="width:30%;text-align:center;">
															@if( !empty($db_data) )
															<img src="/images/preview/{{ $msg }}" width="100" height="100">
															@endif
															<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin-left:5px;">削除</button>
														</td>
													</tr>
												</table>
												</td>
												</form>
											</tr>
											@else
											<tr style="text-align:center;">
												<td>
												<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/setting/postback/create/{{ $channel_id }}/save/send">
													{{ csrf_field() }}
													<table border="1" width="100%">
														<tr style="text-align:center;">
															<td>
																<textarea cols="50" rows="5" name="{{ $name }}" class="form-control" placeholder="1通目：LINEメッセージの内容
	※パッケージID<>ステッカーIDの入力でラインスタンプを送ることができます。
	例：11537<>52002735">{{ $msg }}</textarea>
																<input type="hidden" name="page" value="1">
																<input type="hidden" name="msg" value="{{ $name }}">
																<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
																<input type="hidden" name="edit_id" value="{{ $edit_id }}">
																<input type="hidden" name="push_title" value="">
															</td>
															<td>
																<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button><br />
																<button type="submit" id="save{{ $name }}" class="btn btn-primary save_msg" style="margin:5px;">下書き保存</button>
															</td>
														</tr>
													</table>
												</form>
												</td>
											</tr>
											@endif
										@endforeach
									@endif
								</table>
								<br />
							<form id="formPostback" class="form-horizontal" method="POST" action="/admin/member/line/setting/postback/create/{{ $channel_id }}/save/send">
								{{ csrf_field() }}
								<button type="submit" id="push_btn" class="btn btn-primary">ポストバック設定保存</button>
								<button type="submit" id="add_text_form" class="btn btn-primary">テキスト追加</button>
								<button type="submit" id="add_img_form" class="btn btn-primary">画像追加</button>
								<button type="submit" id="emoji" class="btn btn-primary" style="margin:5px;">絵文字</button>
								<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
								<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
								<input type='hidden' name='edit_id' value="{{ $edit_id }}">
								<input type="hidden" name="push_title" value="">
								<input type="hidden" name="postback_name" value="">
								<input type="hidden" name="msg1" value="">
								<input type="hidden" name="msg2" value="">
								<input type="hidden" name="msg3" value="">
								<input type="hidden" name="msg4" value="">
								<input type="hidden" name="msg5" value="">
							</form>
							</div>
						</div>
					</center>

                </div>
            </div>

			<b>Preview</b>
			<div class="panel panel-default" style="background:papayawhip;">
				<span style="font-size:10px;color:red;">※保存するとPreviewに反映されます</span>
				<div class="line__container">
					<!-- タイトル -->
					<div class="line__title">
						@if( !empty($db_data->name) )
						{{ $db_data->name }}
						@endif
					</div>

					<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
					<div class="line__contents scroll">
						@foreach($list_msg as $index => $msg)
							@if( preg_match("/\.png$/", $msg) > 0 )
								<!-- 相手のスタンプ -->
								<div class="line__left">
								  <figure>
									<img src="/images/admin/line_none.jpg" />
								  </figure>
								  <div class="line__left-text">
									<div class="stamp"><img src="/images/preview/{{ $msg }}" /></div>
									<date_area>
									</date_area>
								  </div>
								</div>
							@else
								<!-- 相手の吹き出し -->
								<div class="line__left">
								  <figure>
									<img src="/images/admin/line_none.jpg" />
								  </figure>
								  <div class="line__left-text">
									<div class="name">&nbsp;<br />&nbsp;</div>
									<div class="text">{!! preg_replace("/\n/", "<br />", $msg) !!}</div>
								  </div>
								  <date_area>
								  </date_area>
								</div>
							@endif
						@endforeach
					</div>
					<!--　▲会話エリア ここまで -->
				</div>
				<!--　▲LINE風ここまで -->
			</div>
        </div>
	</div>	

</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
var sub_win;
function Ajax(msgId, action_url, redirect_url){
	var postback_name = $('input[name=tmp_postback_name]').val();
	if( postback_name != undefined ){
		$('#formLinemsg'+msgId+' [name=push_title]').val(postback_name);
	}
//alert(msgId);
	$.ajax({
		url: action_url,
		type: "post",
		timeout: 10000,
		data: $('#formLinemsg' + msgId).serialize(),

		headers: {
			'X-CSRF-TOKEN': $('input[name=_token]').val()
		},
		//通信しレスポンスが返ってきたとき
		success: function(postback_id) {
			if( window.opener ){
				window.opener.location.reload();
			}

			if( redirect_url != undefined && redirect_url != '' ){
				window.location.href = redirect_url+'/'+postback_id;
			}
		},
		//通信がエラーになったとき
		error: function(error) {

		}
	});
}

$(document).ready(function(){
	@if( !empty($list_msg) )
	$('.disp_cloumn').css('display','block');
	@endif
	var idNo = {{ count($list_msg) }};
	$('#add_text_form').click(function(event){
		event.preventDefault();
		$('.disp_cloumn').css('display','block');
		var stop_flg = true;
		$('textarea[name^=msg]').each(function(){
			if( $(this).val() == "" ){
				swal("最初に入力してから次のフォームを追加してください");
				stop_flg = false;
				return false;
			}
		});
		$('[id^=formLinemsg]').each(function(){
			var id = $(this).attr('id').replace(/formLinemsg/,"");
			if( $("#dropmsg"+id).length > 0 ){
				if( $("#"+this.id+" img").attr("src") == undefined ){
					swal("最初に入力してから次のフォームを追加してください");
					stop_flg = false;
					return false;
				}
			}
		});
		if( !stop_flg ){
			return false;
		}
		idNo++;
		if( idNo == 6 ){
			swal("配信可能な件数は画像と合わせて５件までです");
			return false;
		}
		$('#message_form').append('<tr style="text-align:center;border:1px solid darkgray;"><td><form id="formLinemsg'+idNo+'" class="form-horizontal" method="POST" action="/admin/member/line/setting/postback/create/{{ $channel_id }}/save/send"><table width="100%"><tr><td><textarea cols="50" rows="5" name="msg'+idNo+'" class="form-control" placeholder="'+idNo+'通目：LINEメッセージの内容"></textarea><input type="hidden" name="page" value="1"><input type="hidden" name="send_type" value=""><input type="hidden" name="edit_id" value="{{ $edit_id }}"><input type="hidden" name="channel_id" value="{{ $channel_id }}"><input type="hidden" name="msg" value="msg'+idNo+'"><input type="hidden" name="push_title" value=""></td><td><button type="submit" id="delmsg'+idNo+'" class="btn btn-primary del_msg" style="margin:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button><br><button type="submit" id="savemsg'+idNo+'" class="btn btn-primary save_msg" style="margin:5px;;">下書き保存</button></td></tr></table></form></td></tr>');
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax(id, $('#formLinemsg' + id).prop('action'), '{{ $redirect_url }}');
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax(id, "/admin/member/line/setting/postback/{{ $channel_id }}/delete");
			return false;
		});
		return false;
	});

	$('#add_img_form').click(function(event){
		event.preventDefault();
		$('.disp_cloumn').css('display','block');
		var stop_flg = true;
		$('textarea[name^=msg]').each(function(){
			if( $(this).val() == "" ){
				swal("最初に入力してから次のフォームを追加してください");
				stop_flg = false;
				return false;
			}
		});
		$('[id^=formLinemsg]').each(function(){
			var id = $(this).attr('id').replace(/formLinemsg/,"");
			if( $("#dropmsg"+id).length > 0 ){
				if( $("#"+this.id+" img").attr("src") == undefined ){
					swal("最初に入力してから次のフォームを追加してください");
					stop_flg = false;
					return false;
				}
			}
		});
		if( !stop_flg ){
			return false;
		}
		idNo++;
		if( idNo == 6 ){
			swal("配信可能な件数は画像と合わせて５件までです");
			return false;
		}
		$('#message_form').append("<tr style='text-align:center;border:1px solid darkgray;'><td><form id='formLinemsg"+idNo+"' class='form-horizontal' method='POST' action='/admin/member/line/setting/postback/create/img/{{ $channel_id }}/upload'><table width='100%'><tr><td style='text-align:center;border-right:1px solid darkgray;'><div id='file_upload_sectionmsd"+idNo+"' style='width:100%;'><div id='dropmsg"+idNo+"' style='text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;' ondragleave=\"onDragLeave(event, 'dropmsg"+idNo+"', 'white');\" ondragover=\"onDragOver(event, 'dropmsg"+idNo+"', 'wheat');\" ondrop=\"onDrop(event, 'formLinemsg"+idNo+"', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', 'msg"+idNo+"');\"><div style='font:italic normal bold 16px/150% メイリオ,sans-serif;color:silver;'>アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div><input type='hidden' name='page' value='1'><input type='hidden' name='msg' value='msg"+idNo+"'><input type='hidden' name='edit_id' value='{{ $edit_id }}'><center><div id='resultmsg"+idNo+"' style='font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;'></div></center></div></div></td><td><button type='submit' id='delmsg"+idNo+"' class='btn btn-primary del_msg' style='margin:5px;'>削除</button></td></tr></table></form></td></tr>");
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax(id, $('#formLinemsg' + id).prop('action'), '{{ $redirect_url }}');
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax(id, "/admin/member/line/setting/postback/{{ $channel_id }}/delete");
			return false;
		});
		return false;
	});

	$('.save_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/savemsg/, "");
		Ajax(id, $('#formLinemsg' + id).prop('action'), '{{ $redirect_url }}');
		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		Ajax(id, "/admin/member/line/setting/postback/{{ $channel_id }}/delete");
		return false;
	});

	//絵文字表(HTML)ボタン押下
	$('[id^=emoji]').on('click', function(){
		sub_win = window.open('/admin/member/line/emoji/convert/list', 'convert_table', 'width=600, height=300');
		return false;
	});

	$('#push_btn').click(function(){
//		event.preventDefault();
		$('textarea[name^=msg]').each(function(){
			if( $(this).val() != '' ){
				$('[name='+$(this).attr('name')+']').val($(this).val());
				$('[name=msg]').val($(this).attr('name'));				
			}
		});

		$('[id^=formLinemsg]').each(function(){
			var id = $(this).attr('id').replace(/formLinemsg/,"");
			var msg1 = $('#formLinemsg'+id+' textarea[name^=msg'+id+']').val();
			var msg2 = $("#"+this.id+" img").attr("src");
			var msg_id = $(this).attr('id').replace(/formLinemsg/,"").replace(/(\d+)_\d+_\d+/, "$1");
//alert(msg1+"<>"+msg2+"<>"+msg_id);

			if( msg1 != undefined ){
				$('#formPostback [name=msg'+msg_id+']').val(msg1);
			}else if( msg2 != undefined ){
				$('#formPostback [name=msg'+msg_id+']').val(msg2.replace(/\/images\/preview\/(.+)/, "$1"));
			}
		});

		var postback_name = $('input[name=tmp_postback_name]').val();
		if( postback_name != undefined ){
			$('#formPostback input[name=push_title]').val(postback_name);
		}

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formPostback', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_melmaga_wait_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $redirect_url }}');
	});
});


</script>

@endsection

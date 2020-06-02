@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>{{ $channel_name }} -> 自動応答設定</b>
				</div>
				<center>
                <div class="panel-body">
					<!-- タブ -->
					<ul id="tab-menu">
					@foreach($db_data as $index => $lines)
						@if( $index == 0 )
							<li class="active type{{ $lines->type }}" id="{{ $lines->id }}_{{ $lines->type }}">{{ $lines->name }}</li>
						@else
							<li class="type{{ $lines->type }}" id="{{ $lines->id }}_{{ $lines->type }}">{{ $lines->name }}</li>							
						@endif
					@endforeach
					</ul>
					<div id="tab-box" class="form-group" style="align:center;">
						@if( !empty($list_msg) )
							@foreach($list_msg as $index => $lines)
							<div>
							<table border="1" width="95%" id="cloumn{{ $index }}" class="message_form">
								@foreach($lines as $name => $msg)
									@if( preg_match("/^msg\d_\d+_\d+\.(png|jpg|jpeg)/", $msg) > 0 )
										<tr style="text-align:center;">
											<td>
												<form id="formLine{{ $name }}_{{ $index }}" class="form-horizontal" method="POST" action="/admin/member/line/setting/img/upload/{{ $channel_id }}">
												<table border="1" width="100%">
													<tr style="text-align:center;">
														<td>
															<table id="file_upload_section{{ $name }}" style="width:100%;">
																<tr>
																	<td id="drop{{ $name }}_{{ $index }}" style="text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop{{ $name }}_{{ $index }}', 'white')" ondragover="onDragOver(event, 'drop{{ $name }}_{{ $index }}', 'wheat')" ondrop="onDrop(event, 'formLine{{ $name }}_{{ $index }}', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg','send_type','channel_id'], 'post', '10000', '{{ $redirect_url }}', '{{ $name }}')">
																		<span style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br><br>反映されないときはctrl+F5を押してください</span>
																		<center><span id="result{{ $name }}" style="font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;"></span></center>
																		<br /><input type="file" id="imgfile{{ $name }}_{{ $index }}" name="import_file"><input id="btn{{ $name }}_{{ $index }}" type="button" value="画像選択" style="margin-bottom:25px;">
																	</td>
																</tr>
															</table>
															<input type="hidden" name="send_type" value="{{ $index }}">
															<input type="hidden" name="msg" value="{{ $name }}">
															<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
															<input type="hidden" name="edit_id" value="{{ $index }}">
														</td>
														<td style="padding:15px;text-align:center;">
															@if( !empty($db_data) )
															<img src="/images/preview/{{ $msg }}" width="150" height="150">
															@endif
														</td>
													</tr>
												</table>
											</td>
											<td>
												<button type="submit" id="del{{ $name }}_{{ $index }}" class="btn btn-primary del_img" style="margin:5px;">削除</button>
												</form>
											</td>
										</tr>
									@elseif( preg_match("/^imglinkmsg\d+_\d+_\d+\.(png|jpg|jpeg)/", $msg) > 0 )
										<tr style="text-align:center;">
											<td>
											<form id="formLine{{ $name }}_{{ $index }}" class="form-horizontal" method="POST" action="/admin/member/line/setting/imgmap/upload/{{ $channel_id }}">
											<table border="1" width="100%">
												<tr style="text-align:center;">
													<td colspan="2">
														<div style="width:100%;">
															<div style="width:50%;float:left;">
														@if( preg_match("/^.+\|.+$/", $msg) > 0 )
														<input type="text" name="tmp_title{{ $name }}" value="{{ preg_replace("/^.+\|.*\|(.*)$/","$1", $msg) }}" class="form-control" placeholder="画像の代替テキストを入力(400文字以内)→入力例：">
														@else
														<input type="text" name="tmp_title{{ $name }}" value="" class="form-control" placeholder="画像の代替テキストを入力(400文字以内)→入力例：">
														@endif
															</div>
															<div style="width:50%;float:left;">
														@if( preg_match("/^.+\|.+$/", $msg) > 0 )
														<input type="text" name="tmp_url{{ $name }}" value="{{ preg_replace("/^.+\|(.*)\|.*$/","$1", $msg) }}" class="form-control" placeholder="クリック先URLを入力→入力例：https://yahoo.co.jp">
														@else
														<input type="text" name="tmp_url{{ $name }}" value="" class="form-control" placeholder="クリック先URLを入力→入力例：https://yahoo.co.jp">
														@endif
															</div>
														</div>
													</td>
												</tr>
												<tr style="text-align:center;">
													<td>
													<div id="file_upload_section{{ $name }}" style="width:100%;">
														<div id="drop{{ $name }}_{{ $index }}" style="text-align:center;width:1000px;height:90px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop{{ $name }}_{{ $index }}', 'white')" ondragover="onDragOver(event, 'drop{{ $name }}_{{ $index }}', 'wheat')" ondrop="onDrop(event, 'formLine{{ $name }}_{{ $index }}', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type', 'tmp_url{{$name}}', 'tmp_title{{$name}}'], 'post', '10000', '{{ $redirect_url }}', '{{ $name}}')">
															<div style="font:italic normal bold 14px/140% 'メイリオ',sans-serif;color:silver;padding-top:20px;">アップロードするファイルをここにドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div>
															<center><div id="result{{ $name }}" style="font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;"></div></center>
															<br /><input type="file" id="imgfile{{ $name }}_{{ $index }}" name="import_file"><input id="btn{{ $name }}_{{ $index }}" type="button" value="画像選択" style="margin-bottom:25px;">
														</div>
													</div>
													<input type="hidden" name="page" value="1">
													<input type="hidden" name="send_type" value="{{ $index }}">
													<input type="hidden" name="msg" value="{{ $name }}">
													<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
													<input type="hidden" name="edit_id" value="{{ $edit_id }}">
													</td>
													<td style="padding:15px;text-align:center;">
														@if( !empty($db_data) )
														<img id="img{{ $name }}" src="/images/preview/{{ preg_replace("/^(.+)\|.*\|.*$/","$1", $msg) }}" width="150" height="150">
														@endif
													</td>
												</tr>
											</table>
											</td>
											<td>
											<button type="submit" id="del{{ $name }}_{{ $index }}" class="btn btn-primary del_img" style="margin:5px;">削除</button>
											</form>
											</td>
										</tr>
									@else
										@if( !empty($msg) )
										<tr style="text-align:center;">
											<td colspan="2">
												<form id="formLine{{ $name }}_{{ $index }}" class="form-horizontal" method="POST" action="/admin/member/line/setting/msg/save/{{ $channel_id }}">
												{{ csrf_field() }}
												<table width="100%">
													<tr>
														<td width="92%">
															<textarea cols="90" rows="5" name="{{ $name }}_{{ $index }}" class="form-control" placeholder="LINEメッセージの内容">{{ $msg }}</textarea>
															<input type="hidden" name="send_type" value="{{ $index }}">
															<input type="hidden" name="msg" value="{{ $name }}">
															<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
															<input type="hidden" name="edit_id" value="{{ $index }}">
														</td>
														<td>
															<button type="submit" id="del{{ $name }}_{{ $index }}" class="btn btn-primary del_msg" style="margin:5px;">削除</button>
															<button type="submit" id="save{{ $name }}_{{ $index }}" class="btn btn-primary save_msg" style="margin:5px;">保存</button>
														</td>
													</tr>
												</table>
												</form>
											</td>
										</tr>
										@endif
									@endif
								@endforeach
							</table>
							</div>
							@endforeach
						@else
							<div>
								<table border="1" width="95%" class="message_form">
								</table>
							</div>
						@endif
						<br />
						<form id="formImmediatePushMessage" class="form-horizontal" method="POST" action="/admin/member/line/setting/msg/save/{{ $channel_id }}/send">
							{{ csrf_field() }}
							<button type="submit" id="push_btn" class="btn btn-primary">設定更新</button>
							<button type="submit" id="add_text_form" class="btn btn-primary">テキスト追加</button>
							<button type="submit" id="add_img_form" class="btn btn-primary">画像追加</button>
							<button type="submit" id="add_img_link_form" class="btn btn-primary">画像リンク追加</button>
							<button type=submit id=add_setting class="btn btn-primary">メッセージ文追加</button>
							<button type="submit" id="emoji" class="btn btn-primary">&nbsp;&nbsp;絵文字表&nbsp;&nbsp;</button>
							<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
							<input type="hidden" name="tab" value="{{ $edit_id }}">
							<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
							<input type='hidden' name='edit_id' value="{{ $edit_id }}">
							<input type="hidden" name="send_type" value="0">
							<input type="hidden" name="msg1" value="">
							<input type="hidden" name="msg2" value="">
							<input type="hidden" name="msg3" value="">
							<input type="hidden" name="msg4" value="">
							<input type="hidden" name="msg5" value="">
							<input type="hidden" name="urlmsg1" value="">
							<input type="hidden" name="urlmsg2" value="">
							<input type="hidden" name="urlmsg3" value="">
							<input type="hidden" name="urlmsg4" value="">
							<input type="hidden" name="urlmsg5" value="">
							<input type="hidden" name="titlemsg1" value="">
							<input type="hidden" name="titlemsg2" value="">
							<input type="hidden" name="titlemsg3" value="">
							<input type="hidden" name="titlemsg4" value="">
							<input type="hidden" name="titlemsg5" value="">
						</form>
					</div>
                </div>
				</center>

				<b>Preview</b>
				<div class="panel panel-default" style="background:papayawhip;">
					<span style="font-size:10px;color:red;">※保存するとPreviewに反映されます</span>
					<div id="tab-box2" class="line__container">
						<!-- タイトル -->
						<div class="line__title">
							@if( !empty($channel_name) )
							{{ $channel_name }}
							@endif
						</div>

						@foreach($list_msg as $index => $lines)
							<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
							<div id="preview{{ $index }}" class="line__contents scroll">
								@foreach($lines as $name => $msg)
									@if( preg_match("/^(msg|imglinkmsg)\d+_\d+_\d+\.(png|jpg|jpeg)/", $msg) > 0 )
										<!-- 相手のスタンプ -->
										<div class="line__left">
										  <figure>
											<img src="/images/admin/line_none.jpg" />
										  </figure>
										  <div class="line__left-text">
											<div class="stamp"><img src="/images/preview/{{ preg_replace("/^(.+)\|.*\|.*$/","$1", $msg) }}" /></div>
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
										</div>
									@endif
								@endforeach
							</div>
							<!--　▲会話エリア ここまで -->
						@endforeach
					</div>
					<!--　▲LINE風ここまで -->
				</div>
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
function Ajax(msgId, action_url){
	$.ajax({
		url: action_url,
		type: "post",
		timeout: 10000,
		data: $('#formLinemsg' + msgId).serialize(),

		headers: {
			'X-CSRF-TOKEN': $('input[name=_token]').val()
		},
		//通信しレスポンスが返ってきたとき
		success: function(redirect_url) {
			window.location.href = redirect_url;
		},
		//通信がエラーになったとき
		error: function(error) {
alert(error);
		}
	});
}

$(document).ready(function(){
	var active_click_id;

	$('#add_setting').on('click',function(){
		window.location.href='/admin/member/line/setting/add/{{ $channel_id }}';
		return false;
	});

	$('#add_text_form').click(function(event){
		var idNo = 0;
		event.preventDefault();
		var stop_flg = true;
		$('#cloumn'+active_click_id+' textarea[name^=msg]').each(function(){
			if( this.id != '' && $(this).val() == "" ){
				swal("最初に入力してから次のフォームを追加してください");
				stop_flg = false;
				return false;
			}
		});
		$('#cloumn'+active_click_id+' [id^=formLinemsg]').each(function(){
			idNo++;
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

		var active_id = $('.active').attr("id");

		var send_type = $('.active').attr('class').replace(/type/, '').replace(/\s*active/, '');

		$('#cloumn'+active_click_id+'.message_form').append('<tr style="text-align:center;"><td colspan="2"><form id="formLinemsg'+idNo+'_'+active_id+'" class="form-horizontal" method="POST" action="/admin/member/line/setting/msg/save/{{ $channel_id }}"><table width="100%"><tr><td width="92%"><textarea cols="90" rows="5" name="msg'+idNo+'_'+active_id+'" class="form-control" placeholder="'+idNo+'通目：LINEメッセージの内容"></textarea><input type="hidden" name="send_type" value="'+send_type+'"><input type="hidden" name="edit_id" value="'+active_id+'"><input type="hidden" name="channel_id" value="{{ $channel_id }}"><input type="hidden" name="msg" value="msg'+idNo+'"></td><td><button type="submit" id="delmsg'+idNo+'_'+active_id+'" class="btn btn-primary del_msg" style="margin:5px;">削除</button><br><button type="submit" id="savemsg'+idNo+'_'+active_id+'" class="btn btn-primary save_msg" style="margin:5px;;">保存</button></td></tr></table></form></td></tr>');
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			if( $('.active textarea[name=msg' + id+']').val() == '' ){
				swal("メッセージを入力してください");
				return false;
			}
			Ajax(id, "/admin/member/line/setting/msg/save/{{ $channel_id }}");
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
			var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
			Ajax(id, "/admin/member/line/setting/msg/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
			return false;
		});

		$('.del_img').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
			var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
			Ajax(id, "/admin/member/line/setting/img/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
			return false;
		});
		return false;
	});

	$('#add_img_form').click(function(event){
		var idNo = 0;
		event.preventDefault();
		var stop_flg = true;
		$('#cloumn'+active_click_id+' textarea[name^=msg]').each(function(){
			if( this.id != '' && $(this).val() == "" ){
				swal("最初に入力してから次のフォームを追加してください");
				stop_flg = false;
				return false;
			}
		});

		$('#cloumn'+active_click_id+' [id^=formLinemsg]').each(function(){
			idNo++;
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

		var active_id = $('.active').attr("id");

		var send_type = $('.active').attr('class').replace(/type/, '').replace(/\s*active/, '');

		$('.message_form').append("<tr style='text-align:center;'><td><form id='formLinemsg"+idNo+"_"+active_id+"' class='form-horizontal' method='POST' action='/admin/member/line/setting/img/upload/{{ $channel_id }}'><table id='file_upload_sectionmsd"+idNo+"' style='width:100%;'><tr><td id='dropmsg"+idNo+"_"+active_id+"' style='text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;' ondragleave=\"onDragLeave(event, 'dropmsg"+idNo+"_"+active_id+"', 'white');\" ondragover=\"onDragOver(event, 'dropmsg"+idNo+"_"+active_id+"', 'wheat');\" ondrop=\"onDrop(event, 'formLinemsg"+idNo+"_"+active_id+"', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg','send_type','channel_id'], 'post', '10000', '{{ $redirect_url }}', 'msg"+idNo+"');\"><span style='font:italic normal bold 16px/150% メイリオ,sans-serif;color:silver;'>アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br><br>反映されないときはctrl+F5を押してください</span><center><span id='resultmsg"+idNo+"' style='font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;'></span></center><br /><input type='file' id='imgfilemsg"+idNo+"_"+active_id+"' name='import_file'><input id='btnmsg"+idNo+"_"+active_id+"' type='button' value='画像選択' style='margin-bottom:25px;'></td></tr></table><input type='hidden' name='send_type' value='"+send_type+"'><input type='hidden' name='msg' value='msg"+idNo+"'><input type='hidden' name='channel_id' value='{{ $channel_id }}'><input type='hidden' name='edit_id' value='"+active_id+"'></td><td><button type='submit' id='delmsg"+idNo+"' class='btn btn-primary del_img' style='margin:5px;'>削除</button></form></td></tr>");
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			if( $('[name=msg' + id+']').val() == '' ){
				swal("メッセージを入力してください");
				return false;
			}
			Ajax(id, "/admin/member/line/setting/msg/save/{{ $channel_id }}");
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
			var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
			Ajax(id, "/admin/member/line/setting/msg/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
			return false;

		});

		$('.del_img').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
			var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
			Ajax(id, "/admin/member/line/setting/img/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
			return false;

		});

		$('input[type=button]').click(function() {
			var click_btn_id = this.id.replace(/btnmsg(\d+_\d+_\d+)/, "$1");

			$('#formLinemsg'+click_btn_id+' input[type=file]').trigger('click');

			//ファイル選択から画像を選択してアップロード
			$("#cloumn"+active_click_id+" [id=imgfilemsg"+click_btn_id+"]").change(function(event){
				event.preventDefault();

				//ドロップされたファイルのfilesプロパティを参照
				var files = this.files;

				var form_id = 'formLinemsg' + click_btn_id;

				var msg_id = click_btn_id.replace(/(\d+)_\d+_\d+/, "$1");

				//画像の複数選択ドラッグのアップロード対応
				for (var i=0; i<files.length; i++) {
					var prefix = 'msg' + msg_id;
					FileUpload(form_id, 'import_file', files[i], '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id',　'msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', (i+1),prefix);
				}
			});
		});

		return false;
	});

	$('#add_img_link_form').click(function(event){
		var idNo = 0;
		event.preventDefault();
		var stop_flg = true;
		$('#cloumn'+active_click_id+' textarea[name^=msg]').each(function(){
			if( this.id != '' && $(this).val() == "" ){
				swal("最初に入力してから次のフォームを追加してください");
				stop_flg = false;
				return false;
			}
		});
		$('#cloumn'+active_click_id+' [id^=formLinemsg]').each(function(){
			idNo++;
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

		var active_id = $('.active').attr("id");

		var send_type = $('.active').attr('class').replace(/type/, '').replace(/\s*active/, '');

		$('.message_form').append("<tr style='text-align:center;'><td colspan='2'><form id='formLinemsg"+idNo+"_"+active_id+"' class='form-horizontal' method='POST' action='/admin/member/line/setting/imgmap/upload/{{ $channel_id }}'><table border='1' width='100%'><tr style='text-align:center;'><td colspan='3'><div style='width:100%;'><div style='width:50%;float:left;'><input type='text' name='tmp_titlemsg"+idNo+"' value='' class='form-control' placeholder='代替テキストを入力(400文字以内)→入力例：'></div><div style='width:50%;float:left;'><input type='text' name='tmp_urlmsg"+idNo+"' value='' class='form-control' placeholder='クリック先URLを入力→入力例：https://yahoo.co.jp'></div></td></tr><tr style='text-align:center;'><td><div id='file_upload_sectionmsd"+idNo+"' style='width:100%;'><div id='dropmsg"+idNo+"_"+active_id+"' style='text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;' ondragleave=\"onDragLeave(event, 'dropmsg"+idNo+"_"+active_id+"', 'white');\" ondragover=\"onDragOver(event, 'dropmsg"+idNo+"_"+active_id+"', 'wheat');\" ondrop=\"onDrop(event, 'formLinemsg"+idNo+"_"+active_id+"', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type', 'tmp_urlmsg"+idNo+"', 'tmp_titlemsg"+idNo+"'], 'post', '10000', '{{ $redirect_url }}', 'msg"+idNo+"');\"><div style='font:italic normal bold 16px/150% メイリオ,sans-serif;color:silver;'>アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div><input type='hidden' name='page' value='1'><input type='hidden' name='send_type' value='"+send_type+"'><input type='hidden' name='msg' value='msg"+idNo+"'><input type='hidden' name='edit_id' value='"+active_id+"'><input type='hidden' name='urlmsg"+idNo+"' value=''><input type='hidden' name='titlemsg"+idNo+"' value=''><center><div id='resultmsg"+idNo+"' style='font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;'></div></center><br /><input type='file' id='imgfilemsg"+idNo+"_"+active_id+" name='import_file'><input id='btnmsg"+idNo+"_"+active_id+"' type='button' value='画像選択' style='margin-bottom:25px;'></div></div></td><td><button type='submit' id='delmsg"+idNo+"_"+active_id+"' class='btn btn-primary del_img' style='margin:5px;'>削除</button></form></td></tr></table></td></tr>");
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax2(id, "/admin/member/line/setting/msg/save/{{ $channel_id }}");
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
			var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
			Ajax(id, "/admin/member/line/setting/msg/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
			return false;
		});

		$('input[type=button]').click(function() {
			var click_btn_id = this.id.replace(/btnmsg(\d+_\d+_\d+)/, "$1");

			$('#formLinemsg'+click_btn_id+' input[type=file]').trigger('click');

			//ファイル選択から画像を選択してアップロード
			$("#cloumn"+active_click_id+" [id=imgfilemsg"+click_btn_id+"]").change(function(event){
				event.preventDefault();

				//ドロップされたファイルのfilesプロパティを参照
				var files = this.files;

				var form_id = 'formLinemsg' + click_btn_id;

				var msg_id = click_btn_id.replace(/(\d+)_\d+_\d+/, "$1");

				//画像の複数選択ドラッグのアップロード対応
				for (var i=0; i<files.length; i++) {
					var prefix = 'msg' + msg_id;
					FileUpload(form_id, 'import_file', files[i], '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id',　'msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', (i+1),prefix);
				}
			});
		});

		return false;
	});

	$('.save_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/savemsg/, "");
		if( $('[name=msg' + id+']').val() == '' ){
			swal("メッセージを入力してください");
			return false;
		}
		Ajax(id, "/admin/member/line/setting/msg/save/{{ $channel_id }}");
		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
		var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
		Ajax(id, "/admin/member/line/setting/msg/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
		return false;
	});

	$('.del_img').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		var dbid = this.id.replace(/delmsg/, "").replace(/\d+_(\d+)_\d+/, "$1");
		var send_type = this.id.replace(/delmsg/, "").replace(/\d+_\d+_(\d+)/, "$1");
		Ajax(id, "/admin/member/line/setting/img/delete/{{ $channel_id }}/"+dbid+"/"+send_type);
		return false;
	});

	//絵文字表(HTML)ボタン押下
	$('[id^=emoji]').on('click', function(){
		sub_win = window.open('/admin/member/line/emoji/convert/list', 'convert_table', 'width=600, height=300');
		return false;
	});

	$('#push_btn').click(function(){
		var stop_flg = true;
		$('#cloumn'+active_click_id+' [id^=formLinemsg]').each(function(){
			var id = $(this).attr('id').replace(/formLinemsg/,"");
			var msg1 = $('#cloumn'+active_click_id+' #formLinemsg'+id+' textarea[name^=msg'+id+']').val();
			var msg2 = $("#"+this.id+" img").attr("src");
			var msg_id = $(this).attr('id').replace(/formLinemsg/,"").replace(/(\d+)_\d+_\d+/, "$1");

			var title = $('#cloumn'+active_click_id+' #formLinemsg'+id+' input[name=tmp_titlemsg'+msg_id+']').val();
			var url = $('#cloumn'+active_click_id+' #formLinemsg'+id+' input[name=tmp_urlmsg'+msg_id+']').val();

			if( title == '' ){
				swal("代替テキストを入力してください");
				stop_flg = false;
				return false;	
			}

			if( url == '' ){
				swal("クリック先URLを入力してください");
				stop_flg = false;
				return false;	
			}

			if( msg1 == '' ){
				swal("メッセージを入力してください");
				stop_flg = false;
				return false;	
			}

			$('#formImmediatePushMessage [name=msg'+msg_id+']').val(msg1);

			if( msg1 != undefined ){
				$('#formImmediatePushMessage [name=msg'+msg_id+']').val(msg1);
			}else if( msg2 != undefined ){
				var imglink = msg2.match(/\/images\/preview\/(imglink.+)/);
				if( imglink != null ){
					$('#formImmediatePushMessage [name=msg'+msg_id+']').val(imglink[1]+"|"+url+"|"+title);					
//alert(imglink+"|"+url+"|"+title);
				}else{
					$('#formImmediatePushMessage [name=msg'+msg_id+']').val(msg2.replace(/\/images\/preview\/(.+)/, "$1"));
				}
			}
		});

		if( !stop_flg ){
			return false;
		}

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formImmediatePushMessage', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_save_end_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $redirect_url }}');
	});

	var id = $('[name="tab"]').val();

	//デフォルトのカラムを表示
	$("[id^=cloumn]").each(function(index,elem){
		var id = this.id;

		if( index == 0 ){
			$('#'+id).css('display', 'block');
		}else{
			$('#'+id).css('display', 'none');
		}
	});

	//タブをクリックしなかったときのデフォルトのIDを設定
	$("#tab-menu li:first").each(function(index,elem){
		var id = this.id;
		active_click_id = id;
		if( $('[name="tab"]').val() == '' ){
			$('[name="tab"]').val(id);
		}

		//クリックされたタブIDを取得
		var tabid = $('[name="tab"]').val();

		var channel_id = $('[name="channel_id"]').val();
		$('[name="edit_id"]').val(tabid);

		// タブメニュー
		$('#' + tabid).addClass('active').siblings('li').removeClass('active');

		$("#img"+tabid).css("background", "#58FA58");	

		// タブの中身
		var index = $('#tab-menu li#'+tabid).index();

		$('#tab-box div').eq(index).addClass('active').siblings('div').removeClass('active');

		$('[name="send_type"]').val($('.active').attr('class').replace(/type/, '').replace(/\s*active/, ''));

		$("[id^=cloumn]").each(function(index,elem){
			var click_id = id;
			var cloumn_id = this.id.replace(/cloumn(\d+_\d+)/, "$1");
			if( cloumn_id == click_id ){
				$('#cloumn'+cloumn_id).css('display', 'block');
			}else{
				$('#cloumn'+cloumn_id).css('display', 'none');
			}
		});

		//デフォルトのプレビューのみを表示
		$.when(
			$('[id^=preview]').each(function(){
				$('#'+this.id).css('display', 'none');
			})
		).done(function(){
			$('#preview'+id).css('display', 'block');			
		});

		return false;
	});

	//出力文言のタブ切り替え
	$('#tab-menu li').on('click', function(){
		var id = $(this).attr("id");

		$('[name="send_type"]').val($(this).attr('class').replace(/type/, '').replace(/\s*active/, ''));

		//編集したIDがわかるようにIDをパラメータに設定
		$('[name="tab"]').val($(this).attr("id"));

		var resetColorImage = $.when(
			$(".img").each(function(){
				$("#"+this.id).css("background", "white");
			})
		);
		resetColorImage.done(function(){
			$("#img"+id).css("background", "#58FA58");			
		});

		var type = $(this).attr("class").replace(/(^.*type)/, "");
		$('[name="type"]').val(type);

		if($(this).not('active')){
			// タブメニュー
			$(this).addClass('active').siblings('li').removeClass('active');

			// タブの中身
			var index = $('#tab-menu li').index(this);

			$('#tab-box div').eq(index).addClass('active').siblings('div').removeClass('active');

			//クリックされたタブIDを取得
			var id = $('[name="tab"]').val();
			var channel_id = $('[name="channel_id"]').val();
			$('[name="edit_id"]').val(id);
		}

		$("[id^=cloumn]").each(function(index,elem){
			var click_id = id;
			var cloumn_id = this.id.replace(/cloumn(\d+_\d+)/, "$1");
			if( cloumn_id == click_id ){
				active_click_id = cloumn_id;
				$('#cloumn'+cloumn_id).css('display', 'block');
			}else{
				$('#cloumn'+cloumn_id).css('display', 'none');
			}
		});

		//クリックされたタブのプレビューのみを表示
		$.when(
			$('[id^=preview]').each(function(){
				$('#'+this.id).css('display', 'none');
			})
		).done(function(){
			$('#preview'+id).css('display', 'block');			
		});
	});

	$('input[type=button]').click(function() {
		var click_btn_id = this.id.replace(/btnmsg(\d+_\d+_\d+)/, "$1");

		$('#formLinemsg'+click_btn_id+' input[type=file]').trigger('click');
//alert(active_click_id);
		//ファイル選択から画像を選択してアップロード
		$("#cloumn"+active_click_id+" [id=imgfilemsg"+click_btn_id+"]").change(function(event){
			event.preventDefault();

			//ドロップされたファイルのfilesプロパティを参照
			var files = this.files;

			var form_id = 'formLinemsg' + click_btn_id;

			var msg_id = click_btn_id.replace(/(\d+)_\d+_\d+/, "$1");

			//画像の複数選択ドラッグのアップロード対応
			for (var i=0; i<files.length; i++) {
				var prefix = 'msg' + msg_id;
				FileUpload(form_id, 'import_file', files[i], '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id',　'msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', (i+1),prefix);
			}
		});
	});
});


</script>

@endsection

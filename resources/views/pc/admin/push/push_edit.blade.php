@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>即時配信のLINEメッセージの内容&nbsp;&nbsp;&nbsp;&nbsp;</b>
				</div>
                <div class="panel-body">
					<span style="margin-left:20px;">
						<b>配信状況</b>：
						@if( $send_status == 0 )
						配信待ち
						@elseif( $send_status == 99 )
						配信停止(下書き保存)
						@endif
					</span>
						<center>
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%" id="message_form">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											@if( !empty($list_msg) )
												@foreach($list_msg as $name => $lines)
													@if( preg_match("/^msg\d_\d+\.png$/", $lines[0]) > 0 )
													<tr style="text-align:center;">
														<td>
														<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/push/message/img/{{ $channel_id }}/upload">
														<table border="1" width="100%">
															<tr style="text-align:center;">
																<td>
																<div id="file_upload_section{{ $name }}" style="width:100%;">
																	<div id="drop{{ $name }}" style="text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop{{ $name }}', 'white')" ondragover="onDragOver(event, 'drop{{ $name }}', 'wheat')" ondrop="onDrop(event, 'formLine{{ $name }}', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg'], 'post', '10000', '{{ $redirect_url }}', '{{ $name }}')">
																		<div style="font:italic normal bold 16px/150% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br><br>反映されないときはctrl+F5を押してください</div>
																		<center><div id="result{{ $name }}" style="font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;"></div></center>
																	</div>
																</div>
																<input type="hidden" name="page" value="1">
																<input type="hidden" name="send_type" value="0">
																<input type="hidden" name="msg" value="{{ $name }}">
																<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
																<input type="hidden" name="edit_id" value="{{ $edit_id }}">
																</td>
																<td style="padding:15px;text-align:center;">
																	@if( !empty($db_data) )
																	<img src="/images/preview/{{ $lines[0] }}" width="150" height="150">
																	@endif
																</td>
															</tr>
														</table>
														</td>
														<td>
														<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin:5px;">削除</button>
														</form>
														</td>
													</tr>
													@else
													<tr style="text-align:center;">
														<td>
														<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/push/message/save/send">
															{{ csrf_field() }}
															<textarea cols="50" rows="5" name="{{ $name }}" class="form-control" placeholder="1通目：LINEメッセージの内容
			※パッケージID<>ステッカーIDの入力でラインスタンプを送ることができます。
			例：11537<>52002735">{{ $lines[0] }}</textarea>
														<input type="hidden" name="page" value="1">
														<input type="hidden" name="send_type" value="0">
														<input type="hidden" name="msg" value="{{ $name }}">
														<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
														<input type="hidden" name="edit_id" value="{{ $edit_id }}">
														</td>
														<td>
															<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button><br />
															<button type="submit" id="save{{ $name }}" class="btn btn-primary save_msg" style="margin:5px;">下書き保存</button>
														</form>
														</td>
													</tr>
													@endif
												@endforeach
											@endif
										</tr>
									</table>
									<br />
								<form id="formImmediatePushMessage" class="form-horizontal" method="POST" action="/admin/member/line/push/message/send">
									{{ csrf_field() }}
									<button type="submit" id="push_btn" class="btn btn-primary">メッセージ配信</button>
									<button type="submit" id="add_text_form" class="btn btn-primary">テキスト追加</button>
									<button type="submit" id="add_img_form" class="btn btn-primary">画像追加</button>
									<button type="submit" id="emoji" class="btn btn-primary" style="margin:5px;">絵文字</button>
									<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
									<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
									<input type='hidden' name='edit_id' value="{{ $edit_id }}">
									<input type="hidden" name="send_type" value="0">
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
						@foreach($list_msg as $index => $lines)
							@if( preg_match("/\.png$/", $lines[0]) > 0 )
								<!-- 相手のスタンプ -->
								<div class="line__left">
								  <figure>
									<img src="/images/admin/line_none.jpg" />
								  </figure>
								  <div class="line__left-text">
									<div class="stamp"><img src="/images/preview/{{ $lines[0] }}" /></div>
									<date_area>
									  {{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s)(\d{2}:\d{2}):\d{2}/", "$2", $lines[1]) }}
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
									<div class="text">{!! preg_replace("/\n/", "<br />", $lines[0]) !!}</div>
								  </div>
								  <date_area>
									{{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s)(\d{2}:\d{2}):\d{2}/", "$2", $lines[1]) }}
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
		success: function() {
			window.location.reload();
		},
		//通信がエラーになったとき
		error: function(error) {

		}
	});
}

$(document).ready(function(){
	var idNo = {{ count($list_msg) }};
	$('#add_text_form').click(function(event){
		event.preventDefault();
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
		$('#message_form').append('<tr style="text-align:center;"><td><form id="formLinemsg'+idNo+'" class="form-horizontal" method="POST" action="/admin/member/line/push/message/save/send"><textarea cols="50" rows="5" name="msg'+idNo+'" class="form-control" placeholder="'+idNo+'通目：LINEメッセージの内容"></textarea><input type="hidden" name="page" value="1"><input type="hidden" name="send_type" value="0"><input type="hidden" name="edit_id" value="{{ $edit_id }}"><input type="hidden" name="channel_id" value="{{ $channel_id }}"><input type="hidden" name="msg" value="msg'+idNo+'"></td><td><button type="submit" id="delmsg'+idNo+'" class="btn btn-primary del_msg" style="margin:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button><br><button type="submit" id="savemsg'+idNo+'" class="btn btn-primary save_msg" style="margin:5px;;">下書き保存</button></form></td></tr>');
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax(id, $('#formLinemsg' + id).prop('action'));
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
			return false;
		});
		return false;
	});

	$('#add_img_form').click(function(event){
		event.preventDefault();
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
		$('#message_form').append("<tr style='text-align:center;'><td><form id='formLinemsg"+idNo+"' class='form-horizontal' method='POST' action='/admin/member/line/push/message/img/{{ $channel_id }}/upload'><div id='file_upload_sectionmsd"+idNo+"' style='width:100%;'><div id='dropmsg"+idNo+"' style='text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;' ondragleave=\"onDragLeave(event, 'dropmsg"+idNo+"', 'white');\" ondragover=\"onDragOver(event, 'dropmsg"+idNo+"', 'wheat');\" ondrop=\"onDrop(event, 'formLinemsg"+idNo+"', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', 'msg"+idNo+"');\"><div style='font:italic normal bold 16px/150% メイリオ,sans-serif;color:silver;'>アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div><input type='hidden' name='page' value='1'><input type='hidden' name='send_type' value='0'><input type='hidden' name='msg' value='msg"+idNo+"'><input type='hidden' name='edit_id' value='{{ $edit_id }}'><center><div id='resultmsg"+idNo+"' style='font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;'></div></center></div></div></td><td><button type='submit' id='delmsg"+idNo+"' class='btn btn-primary del_msg' style='margin:5px;'>削除</button></form></td></tr></table>");
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax(id, $('#formLinemsg' + id).prop('action'));
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
			return false;
		});
		return false;
	});

	$('.save_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/savemsg/, "");
		Ajax(id, $('#formLinemsg' + id).prop('action'));
		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		Ajax(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
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
			}
		});
		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formImmediatePushMessage', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_melmaga_wait_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $redirect_url }}');
	});
});


</script>

@endsection

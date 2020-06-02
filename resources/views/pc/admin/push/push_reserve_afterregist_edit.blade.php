@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>登録後配信のLINEメッセージの内容&nbsp;&nbsp;&nbsp;&nbsp;</b>
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
									<form id="formSegment" class="form-horizontal" method="POST" action="/admin/member/line/{{ $channel_id }}/segment/count">
									{{ csrf_field() }}
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td colspan="2">
												<div style="float:left;"><b>セグメント条件設定</b></div>
												<div style="color:blue;float:right;margin-left:200px;"><b>現在の配信予定数：<span id="segment_result"></span>人</b></div>
											</td>
										</tr>
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>
												<b>LINE ID</b>
											</td>
											<td>
												<input type="text" id="line_id" name="line_id" class="form-control segment" value="{{ $segment['line_id'] }}" placeholder="LINE IDが複数ある時は半角カンマ(,)で入力してください">
											</td>
										</tr>
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>
												<b>登録期間</b>
											</td>
											<td>
												<div style="width:45%;float:left;"><input type="text" id="start_reg_date" name="start_reg_date" class="form-control segment" value="{{ $segment['start_reg_date'] }}" placeholder=""></div>
												<div style="width:10%;float:left;padding-top:7px;">～</div>
												<div style="width:45%;float:left;"><input type="text" id="end_reg_date" name="end_reg_date" class="form-control segment" value="{{ $segment['end_reg_date'] }}" placeholder=""></div>
											</td>
										</tr>
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>
												<b>クリック期間</b>
											</td>
											<td>
												<div style="width:45%;float:left;"><input type="text" id="start_click_date" name="start_click_date" class="form-control segment" value="{{ $segment['start_click_date'] }}" placeholder=""></div>
												<div style="width:10%;float:left;padding-top:7px;">～</div>
												<div style="width:45%;float:left;"><input type="text" id="end_click_date" name="end_click_date" class="form-control segment" value="{{ $segment['end_click_date'] }}" placeholder=""></div>
											</td>
										</tr>
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>
												<b>広告コード</b>
											</td>
											<td>
												<div style="width:50%;float:left;"><input type="text" id="ad_code" name="ad_code" class="form-control segment" value="{{ $segment['ad_code'] }}" placeholder=""></div>
												<div style="width:50%;float:left;">
													<select id="opt" name="opt" class="form-control segment">
														@foreach($search_like_type as $index => $lines)
															@if( $index == $segment['opt'] )
															<option value="{{ $index }}" selected>{{ $lines[2] }}</option>
															@else
															<option value="{{ $index }}">{{ $lines[2] }}</option>
															@endif
														@endforeach
													</select>
												</div>
											</td>
										</tr>
									</table>
									<input type="hidden" name="edit_id" value="{{ $edit_id }}">
									</form>
									<br>
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td>
												<b>友だち登録から</b> 
												<input type="text" id="send_after_minute" name="send_after_minute" value="{{ $after_minute }}" size="10"> 分後に配信
											</td>
										</tr>
									</table>
									<table border="1" width="95%" id="message_form">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											@if( !empty($list_msg) )
												@foreach($list_msg as $name => $lines)
													@if( preg_match("/^imglinkmsg\d_\d+\.(png|jpg|jpeg)/", $lines[0]) > 0 )
													<tr style="text-align:center;">
														<td colspan="2">
														<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/push/message/imgmap/{{ $channel_id }}/upload">
														<table border="1" width="100%">
															<tr style="text-align:center;">
																<td colspan="3">
																	<div style="width:100%;">
																		<div style="width:50%;float:left;">
																	@if( preg_match("/^.+\|.+$/", $lines[0]) > 0 )
																	<input type="text" name="tmp_title{{ $name }}" value="{{ preg_replace("/^.+\|.+\|(.+)$/","$1", $lines[0]) }}" class="form-control" placeholder="画像の代替テキストを入力(400文字以内)→入力例：">
																	@else
																	<input type="text" name="tmp_title{{ $name }}" value="" class="form-control" placeholder="画像の代替テキストを入力(400文字以内)→入力例：">
																	@endif
																		</div>
																		<div style="width:50%;float:left;">
																	@if( preg_match("/^.+\|.+$/", $lines[0]) > 0 )
																	<input type="text" name="tmp_url{{ $name }}" value="{{ preg_replace("/^.+\|(.+)\|.+$/","$1", $lines[0]) }}" class="form-control" placeholder="クリック先URLを入力→入力例：https://yahoo.co.jp">
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
																	<div id="drop{{ $name }}" style="text-align:center;width:800px;height:90px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop{{ $name }}', 'white')" ondragover="onDragOver(event, 'drop{{ $name }}', 'wheat')" ondrop="onDrop(event, 'formLine{{ $name }}', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'url{{$name}}', 'title{{$name}}'], 'post', '10000', '{{ $redirect_url }}', '{{ $name}}')">
																		<div style="font:italic normal bold 14px/140% 'メイリオ',sans-serif;color:silver;padding-top:20px;">アップロードするファイルをここにドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div>
																		<center><div id="result{{ $name }}" style="font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;"></div></center>
																		<br /><input type="file" id="imgfile{{ $name }}" name="import_file"><input type="button" value="画像選択" style="margin-bottom:25px;">
																	</div>
																</div>
																<input type="hidden" name="page" value="1">
																<input type="hidden" name="send_type" value="1">
																<input type="hidden" name="msg" value="{{ $name }}">
																<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
																<input type="hidden" name="edit_id" value="{{ $edit_id }}">
																<input type="hidden" name="tmp_send_after_minute" value="{{ $after_minute }}">
																<input type="hidden" name="url{{ $name }}" value="">
																<input type="hidden" name="title{{ $name }}" value="">
																</td>
																<td style="padding:15px;text-align:center;">
																	@if( !empty($db_data) )
																	<img id="img{{ $name }}" src="/images/preview/{{ preg_replace("/^(.+)\|.+\|.+$/","$1", $lines[0]) }}" width="150" height="150">
																	@endif
																</td>
																<td>
																<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin:5px;">削除</button>
																</form>
																</td>
															</tr>
														</table>
														</td>
													</tr>
													@else
													<tr style="text-align:center;">
														<td>
														<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/push/message/save/send">
															{{ csrf_field() }}
															<textarea cols="50" rows="7" id="textarea{{ $name }}" name="{{ $name }}" class="form-control" placeholder="1通目：LINEメッセージの内容
			※パッケージID<>ステッカーIDの入力でラインスタンプを送ることができます。
			例：11537<>52002735">{{ $lines[0] }}</textarea>
														<input type="hidden" name="page" value="{{ $page }}">
														<input type="hidden" name="send_type" value="4">
														<input type="hidden" name="msg" value="{{ $name }}">
														<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
														<input type="hidden" name="edit_id" value="{{ $edit_id }}">
														<input type="hidden" name="tmp_send_after_minute" value="{{ $after_minute }}">
														</td>
														<td>
															<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button><br />
															<button type="submit" id="save{{ $name }}" class="btn btn-primary save_msg" style="margin:5px;">下書き保存</button><br />
														</form>
														</td>
													</tr>
													@endif
												@endforeach
											@endif
										</tr>
									</table>
									<br />
								<form id="formReservePushMessage" class="form-horizontal" method="POST" action="/admin/member/line/push/message/reserve/send">
									{{ csrf_field() }}
									<button type="submit" id="save_btn" class="btn btn-primary push_save">下書き保存</button>
									<button type="submit" id="push_btn" class="btn btn-primary push_save">予約設定更新</button>
									<button type="submit" id="add_text_form" class="btn btn-primary">テキスト追加</button>
									<button type="submit" id="add_img_link_form" class="btn btn-primary">画像リンク追加</button>
									<!--<button type="submit" id="add_video_form" class="btn btn-primary">動画追加</button>-->
									<button type="submit" id="emoji" class="btn btn-primary" style="margin:5px;">絵文字</button>
									<button type="submit" id="lineemoji" class="btn btn-primary" style="margin:5px;">LINE絵文字</button>
									<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
									<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
									<input type='hidden' name='edit_id' value="{{ $edit_id }}">
									<input type="hidden" name="send_status" value="0">
									<input type="hidden" name="send_type" value="4">
									<input type="hidden" name="send_after_minute" value="{{ $after_minute }}">
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
									<input type="hidden" name="line_id" value="">
									<input type="hidden" name="start_reg_date" value="">
									<input type="hidden" name="end_reg_date" value="">
									<input type="hidden" name="start_click_date" value="">
									<input type="hidden" name="end_click_date" value="">
									<input type="hidden" name="ad_code" value="">
									<input type="hidden" name="opt" value="">
								</form>
								</div>
							</div>
						</center>
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
							@if( !empty($send_date) )
							<title_date>{{ $send_date }}</title_date>
							<div class="name">&nbsp;</div>
							@endif
							@foreach($list_msg as $index => $lines)
								@if( preg_match("/^imglinkmsg\d_\d+\.(png|jpg|jpeg)/", $lines[0]) > 0 )
									<!-- 相手のスタンプ -->
									<div class="line__left">
									  <figure>
										<img src="/images/admin/line_none.jpg" />
									  </figure>
									  <div class="line__left-text">
										<div class="stamp"><img src="/images/preview/{{ preg_replace("/^(.+)\|.+\|.+$/","$1", $lines[0]) }}" /></div>
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

</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script src="{{ asset('js/admin/file_upload.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
var sub_win;


function Ajax(form_id){
//alert($('#'+form_id+' input[name=_token]').val());
//alert($('#'+form_id).serialize());
	$.ajax({
		url: $('#' + form_id).prop('action'),
		type: "post",
		timeout: 10000,
		data: $('#'+form_id).serialize(),

		headers: {
			'X-CSRF-TOKEN': $('#'+form_id+' input[name=_token]').val()
		},
		//通信しレスポンスが返ってきたとき
		success: function(result) {
			//配信予定数を表示
			$('#segment_result').text(result);
		},
		//通信がエラーになったとき
		error: function(error) {

		}
	});
}

function Ajax2(blurId, action_url){
	//配信予定時刻の設定
	$('#formLinemsg' + blurId+' [name=tmp_send_after_minute]').val($('[name=send_after_minute]').val());

	$.ajax({
		url: action_url,
		type: "post",
		timeout: 10000,
		data: $('#formLinemsg' + blurId).serialize(),

		headers: {
			'X-CSRF-TOKEN': $('input[name=_token]').val()
		},
		//通信しレスポンスが返ってきたとき
		success: function(redirect_url) {
			window.location.href = redirect_url;
		},
		//通信がエラーになったとき
		error: function(error) {

		}
	});
}

$(document).ready(function(){
	$.datetimepicker.setLocale('ja');


	//送信予定時刻
	$('#reserve_date').datetimepicker({
		format:'H:i',
		step:1,
		datepicker:false
	});

	$.datetimepicker.setLocale('ja');

	$('#start_reg_date').datetimepicker({
		step:1,
	});

	$('#end_reg_date').datetimepicker({
		step:1,
	});

	$('#start_click_date').datetimepicker({
		step:1,
	});

	$('#end_click_date').datetimepicker({
		step:1,
	});

	$('.segment').blur(function(){
		event.preventDefault();
		Ajax('formSegment');
	});

	window.onload = function(){
		Ajax('formSegment');
	}

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
				if( $("#imgmsg"+id).attr("src") == undefined ){
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
		$('#message_form').append('<tr style="text-align:center;"><td><form id="formLinemsg'+idNo+'" class="form-horizontal" method="POST" action="/admin/member/line/push/message/save/send"><textarea cols="50" rows="7" name="msg'+idNo+'" class="form-control" placeholder="'+idNo+'通目：LINEメッセージの内容"></textarea><input type="hidden" name="page" value="{{ $page }}"><input type="hidden" name="edit_id" value="{{ $edit_id }}"><input type="hidden" name="channel_id" value="{{ $channel_id }}"><input type="hidden" name="send_type" value="4"><input type="hidden" name="msg" value="msg'+idNo+'"><input type="hidden" name="tmp_send_after_minute" value="{{ $after_minute }}"><input type="hidden" name="tmp_regular_time" value="{{ $after_minute }}"></td><td><button type="submit" id="delmsg'+idNo+'" class="btn btn-primary del_msg" style="margin:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button><br><button type="submit" id="savemsg'+idNo+'" class="btn btn-primary save_msg" style="margin:5px;;">下書き保存</button></form></td></tr>');
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax2(id, $('#formLinemsg' + id).prop('action'));
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax2(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
			return false;
		});
		return false;
	});

	$('#add_img_link_form').click(function(event){
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
				if( $("#imgmsg"+id).attr("src") == undefined ){
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
		$('#message_form').append("<tr style='text-align:center;'><td colspan='2'><form id='formLinemsg"+idNo+"' class='form-horizontal' method='POST' action='/admin/member/line/push/message/imgmap/{{ $channel_id }}/upload'><table border='1' width='100%'><tr style='text-align:center;'><td colspan='3'><div style='width:100%;'><div style='width:50%;float:left;'><input type='text' name='titlemsg"+idNo+"' value='' class='form-control' placeholder='代替テキストを入力(400文字以内)→入力例：'></div><div style='width:50%;float:left;'><input type='text' name='urlmsg"+idNo+"' value='' class='form-control' placeholder='クリック先URLを入力→入力例：https://yahoo.co.jp'></div></td></tr><tr style='text-align:center;'><td><div id='file_upload_sectionmsd"+idNo+"' style='width:100%;'><div id='dropmsg"+idNo+"' style='text-align:center;width:1000px;height:130px; vertical-align:middle; display:table-cell;' ondragleave=\"onDragLeave(event, 'dropmsg"+idNo+"', 'white');\" ondragover=\"onDragOver(event, 'dropmsg"+idNo+"', 'wheat');\" ondrop=\"onDrop(event, 'formLinemsg"+idNo+"', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type', 'tmp_send_after_minute', 'urlmsg"+idNo+"', 'titlemsg"+idNo+"'], 'post', '10000', '{{ $redirect_url }}', 'msg"+idNo+"');\"><div style='font:italic normal bold 16px/150% メイリオ,sans-serif;color:silver;'>アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div><input type='hidden' name='page' value='1'><input type='hidden' name='send_type' value='4'><input type='hidden' name='msg' value='msg"+idNo+"'><input type='hidden' name='edit_id' value='{{ $edit_id }}'><input type='hidden' name='tmp_send_after_minute' value='{{ $reserve_date }}'><input type='hidden' name='urlmsg"+idNo+"' value=''><input type='hidden' name='titlemsg"+idNo+"' value=''><center><div id='resultmsg"+idNo+"' style='font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;'></div></center></div></div></td><td><button type='submit' id='delmsg"+idNo+"' class='btn btn-primary del_msg' style='margin:5px;'>削除</button></form></td></tr></table></td></tr>");
		$('.save_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/savemsg/, "");
			Ajax2(id, $('#formLinemsg' + id).prop('action'));
			return false;
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax2(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
			return false;
		});

		$('input[type=button]').click(function() {
			$('input[type=file]').trigger('click');
		});

		//ファイル選択から画像を選択してアップロード
		$("[id^=imgfilemsg]").change(function(event){
			event.preventDefault();

			//ドロップされたファイルのfilesプロパティを参照
			var files = this.files;

			var form_id = 'formLinemsg' + this.id.replace(/imgfilemsg(\d+)/, "$1");

			//画像の複数選択ドラッグのアップロード対応
			for (var i=0; i<files.length; i++) {
				var prefix = 'msg' + (i+1);
				FileUpload(form_id, 'import_file', files[i], '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id',　'msg', 'send_type', 'tmp_send_after_minute'], 'post', '10000', '{{ $redirect_url }}', (i+1), prefix);
			}
		});
		return false;
	});

	$('.save_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/savemsg/, "");
		Ajax2(id, $('#formLinemsg' + id).prop('action'));
		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		Ajax2(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
		return false;
	});

	$('[id^=emoji]').on('click', function(){
		sub_win = window.open('/admin/member/line/emoji/convert/list', 'convert_table', 'width=600, height=300');
		return false;
	});

	$('[id^=lineemoji]').on('click', function(){
		sub_win = window.open('https://developers.line.biz/media/messaging-api/emoji-list.pdf', 'line_emoji', 'width=800, height=900');
		return false;
	});

	$('.push_save').click(function(){
		var send_after_minute = $('[name=send_after_minute]').val();
		$('[name=send_after_minute]').val(send_after_minute);

		$('textarea[name^=msg]').each(function(){
			if( $(this).val() != '' ){
				$('[name='+$(this).attr('name')+']').val($(this).val());
			}
		});

		var stop_flg = true;
		$('input[name^=tmp_titlemsg]').each(function(){
			if( $(this).val() == '' ){
				swal("代替テキストを入力してください");
				stop_flg = false;
				return false;	
			}
		});

		if( !stop_flg ){
			return false;
		}

		$('input[name^=tmp_urlmsg]').each(function(){
			if( $(this).val() == '' ){
				swal("クリック先URLを入力してください");
				stop_flg = false;
				return false;	
			}
			var id = $(this).attr('name').replace(/tmp_urlmsg(\d+)/, "$1");
			var img = 'imgmsg' + id;
			$('input[name='+$(this).attr('name').replace(/^tmp_(.+)/, "$1")+']').val($("#"+img).attr('src').replace(/\/images\/preview\/(.+)/, "$1")+"|"+$(this).val()+"|"+$('input[name=tmp_titlemsg'+id+']').val());
//alert($(this).attr('name')+"<>"+$("#"+img).attr('src').replace(/\/images\/preview\/(.+)/, "$1")+"<>"+$('input[name=tmp_titlemsg'+id+']').val());
		});

		if( this.id == 'save_btn' ){
			$('#formReservePushMessage [name=send_status]').val('99');
		}

		var end_msg = '';
		if( this.id == 'save_btn' ){
			end_msg = '{{ __('messages.dialog_overwrite_msg') }}';
		}else{
			end_msg = '{{ __('messages.dialog_melmaga_wait_msg') }}';
		}

		$('#formReservePushMessage [name=line_id]').val($('#formSegment input[name=line_id]').val());
		$('#formReservePushMessage [name=start_reg_date]').val($('#formSegment input[name=start_reg_date]').val());
		$('#formReservePushMessage [name=end_reg_date]').val($('#formSegment input[name=end_reg_date]').val());
		$('#formReservePushMessage [name=start_click_date]').val($('#formSegment input[name=start_click_date]').val());
		$('#formReservePushMessage [name=end_click_date]').val($('#formSegment input[name=end_click_date]').val());
		$('#formReservePushMessage [name=ad_code]').val($('#formSegment input[name=ad_code]').val());
		$('#formReservePushMessage [name=opt]').val($('#formSegment [name=opt] option:selected').val());

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formReservePushMessage', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', end_msg, '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $redirect_url }}');
	});

	$('input[type=button]').click(function() {
		$('input[type=file]').trigger('click');
	});

	//ファイル選択から画像を選択してアップロード
	$("[id^=imgfilemsg]").change(function(event){
		event.preventDefault();

		//ドロップされたファイルのfilesプロパティを参照
		var files = this.files;

		var form_id = 'formLinemsg' + this.id.replace(/imgfilemsg(\d+)/, "$1");

		//画像の複数選択ドラッグのアップロード対応
        for (var i=0; i<files.length; i++) {
			var prefix = 'msg' + (i+1);
            FileUpload(form_id, 'import_file', files[i], '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id',　'msg', 'send_type', 'tmp_send_after_minute'], 'post', '10000', '{{ $redirect_url }}', i+1, prefix);
		}
	});
});


</script>

@endsection

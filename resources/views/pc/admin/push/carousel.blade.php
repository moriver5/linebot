@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div style="width:1250px;float:left;">
            <div class="panel panel-default" style="width:750px;float:left;">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>カルーセルメッセージ作成</b>
				</div>
                <div class="panel-body">
						<center>
							<ul id="tab-menu">
							@if( !empty($list_msg) )
							@foreach($list_msg as $name => $lines)
								@if( $loop->first )
									<li class="active type{{ $name }}" id="{{ $name }}">カラム{{ ($loop->index+1) }}</li>
								@else
									<li class="type{{ $name }}" id="{{ $name }}">カラム{{ ($loop->index+1) }}</li>							
								@endif
							@endforeach
							@endif
							</ul>
							<div>
								<div class="form-group" style="align:center;">
									@if( !empty($db_data) )
									<div style="margin-left:20px;text-align:left;font-weight:bold;float:left">配信状況：<font color="#0080FF">{{ config('const.list_send_status')[$db_data->send_status] }}</font></div>
									@endif
									<div style="margin-right:20px;text-align:right;color:red;font-weight:bold;">※全項目必須です</div>
									<table width="95%" id="message_form">
										<tr style="text-align:center;border:1px solid darkgray;background:#9FF781;">
											<td>
												<div style="text-align:left;padding:5px;"><b>共通</b></div>
											</td>
										</tr>
										<tr style="text-align:center;border:1px solid darkgray;">
											<td style="padding:15px;">
												<table width="100%">
													<tr style="text-align:center;font-weight:bold;">
														<td style="width:25%;">
															<div style="text-align:left;font-size:12px;font-weight:bold;"><b>配信日時</b></div>
															<input type="text" id="reserve_date" name="tmp_reserve_date" class="form-control" value="{{ $reserve_date }}" placeholder="2019/08/27 16:00">
														</td>
														<td style="width:75%;">
															<div style="text-align:left;font-size:12px;font-weight:bold;"><b>メッセージのタイトル</b></div>
															<input type="text" name="tmp_push_title" value="{{ $push_title }}" class="form-control" placeholder="通知バナーに表示されるメッセージのタイトル">
														</td>
													</tr>
													<tr style="text-align:center;font-weight:bold;">
														<td style="width:50%;padding-top:10px;">
															<div style="text-align:left;font-size:12px;"><b>画像の比率</b></div>
															<select name="tmp_img_ratio" class="form-control">
																@foreach(config('const.list_carousel_img_ratio') as $index => $ratio)
																	@if( isset($db_data->img_ratio) && $index == $db_data->img_ratio )
																		<option value="{{ $index }}" selected>{{ $ratio }}</option>
																	@else
																		<option value="{{ $index }}">{{ $ratio }}</option>
																	@endif
																@endforeach
															</select>
														</td>
														<td style="width:50%;padding-top:10px;">
															<div style="text-align:left;font-size:12px;"><b>画像サイズ</b></div>
															<select name="tmp_img_size" class="form-control">
																@foreach(config('const.list_carousel_img_size') as $index => $size)
																	@if( isset($db_data->img_size) && $index == $db_data->img_size )
																		<option value="{{ $index }}" selected>{{ $size }}</option>
																	@else
																		<option value="{{ $index }}">{{ $size }}</option>
																	@endif
																@endforeach
															</select>
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
												<div style="text-align:left;padding:5px;"><b id="click_cloumn"></b></div>
											</td>
										</tr>
										@if( !empty($list_msg) )
											@foreach($list_msg as $name => $lines)
												<tr style="text-align:center;border:1px solid darkgray;" class="cloumn{{ $name }}">
													<td style="padding:0px 15px 15px 15px;">
													<form id="formLine{{ $name }}" class="form-horizontal" method="POST" action="/admin/member/line/setting/carousel/img/{{ $channel_id }}/upload">
													<table width="100%">
														<tr style="text-align:center;">
															<td>
															<div style="text-align:left;font-size:12px;font-weight:bold;"><b>挿入画像</b></div>
															<div id="file_upload_section{{ $name }}" style="width:100%;border:1px solid darkgray;">
																<div id="drop{{ $name }}" style="text-align:center;width:1000px;height:90px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop{{ $name }}', 'white')" ondragover="onDragOver(event, 'drop{{ $name }}', 'wheat')" ondrop="onDrop(event, 'formLine{{ $name }}', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', '{{ $name }}')">
																	<div style="font:italic normal bold 14px/140% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br><br>反映されないときはctrl+F5を押してください</div>
																	<center><div id="result{{ $name }}" style="font:italic normal bold 14px/140% メイリオ,sans-serif;width:100%;"></div></center>
																</div>
															</div>
															<input type="hidden" name="page" value="1">
															<input type='hidden' name='send_type' value="1">
															<input type="hidden" name="msg" value="{{ $name }}">
															<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
															<input type="hidden" name="edit_id" value="{{ $edit_id }}">
															</td>
															<td style="padding:5px 10px 0 10px;text-align:center;">
																@if( !empty($db_data) )
																<img src="/images/preview/{{ $lines[0] }}" width="150" height="150">
																@endif
															</td>
															<td>
															<button type="submit" id="del{{ $name }}" class="btn btn-primary del_msg" style="margin:5px;">削除</button>
															</td>
														</tr>
														<tr style="text-align:center;">
															<td colspan="3">
																<div style="float:left;text-align:left;width:40%;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;"><b>カラムタイトル</b></div>
																	<input type="text" id="column_title{{ $name }}" name="column_title{{ $name }}" value="{{ $lines[2] }}" maxlength="40" class="form-control" placeholder="カラムのタイトル">
																	<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="column_title_len{{ $name }}"></span>/40文字</div>
																</div>
																<div style="float:left;text-align:left;width:60%;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;"><b>テキスト</b></div>
																	<input type="text" id="msg{{ $name }}"　name="msg{{ $name }}" class="form-control" value="{{ $lines[3] }}" maxlength="60" placeholder="メッセージテキスト">
																	<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="msg_len{{ $name }}"></span>/60文字</div>
																</div>
															</td>
														</tr>
														<tr style="text-align:center;">
															<td colspan="3">
																<div style="padding-top:10px;width:27%;float:left;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;">アクションタイプ</div>
																	<select name="action{{ $name }}" class="action{{ $name }} form-control">
																		@foreach(config('const.list_carousel_action_type') as $index => $type_name)
																			@if( $index == $lines[4] )
																				<option value="{{ $index }}" selected>{{ $type_name }}</option>
																			@else
																				<option value="{{ $index }}">{{ $type_name }}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div id="slt_action{{ $name }}" style="padding-top:10px;width:73%;float:left;">
																	<div style="width:30%;float:left;">
																		<div style="text-align:left;font-size:12px;font-weight:bold;">アクションラベル</div>
																		<input type="text" id="label{{ $name }}" name="label{{ $name }}" value="{{ $lines[5] }}" maxlength="10" class="form-control" placeholder="例）もっと見る">
																		<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="label_len{{ $name }}"></span>/10文字</div>
																	</div>
																	<div style="width:70%;float:left;">
																		<div style="text-align:left;font-size:12px;" class="act_msg">
																			@if( $lines[4] == 1 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" name="label_action{{ $name }}" value="{{ $lines[6] }}" class="form-control" placeholder="">
																			@elseif( $lines[4] == 3 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>ポストバック値</div>
																			<select id="label_action{{ $name }}" name="label_action{{ $name }}" class='form-control'>
																				<option value=''>使用する管理名を選択してください</option>
																				@foreach($list_postback as $postbacks)
																					@if( $lines[6] == $postbacks[0] )
																					<option value="{{ $postbacks[0] }}" selected>ID：{{ $postbacks[1] }}</option>
																					@else
																					<option value="{{ $postbacks[0] }}">ID：{{ $postbacks[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@endif
																		</div>
																	</div>
																</div>
															</td>
														</tr>
													</table>
													</form>
													</td>
												</tr>
											@endforeach
										@endif
									</table>
									<br />
								<form id="formCarouselSetting" class="form-horizontal" method="POST" action="/admin/member/line/setting/carousel/save/{{ $channel_id }}/send">
									{{ csrf_field() }}
									<button type="submit" id="save_btn" class="btn btn-primary">下書き保存</button>
									<button type="submit" id="push_btn" class="btn btn-primary">予約設定更新</button>
									<button type="submit" id="add_img_form" class="btn btn-primary">カラム追加</button>
									<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
									<input type='hidden' name='send_status' value="0">
									<input type='hidden' name='send_type' value="1">
									<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
									<input type='hidden' name='edit_id' value="{{ $edit_id }}">
									<input type="hidden" name="reserve_date" value="">
									<input type="hidden" name="push_title" value="">
									<input type="hidden" name="img_ratio" value="">
									<input type="hidden" name="img_size" value="">
									<input type="hidden" name="title1" value="">
									<input type="hidden" name="title2" value="">
									<input type="hidden" name="title3" value="">
									<input type="hidden" name="title4" value="">
									<input type="hidden" name="title5" value="">
									<input type="hidden" name="title6" value="">
									<input type="hidden" name="title7" value="">
									<input type="hidden" name="title8" value="">
									<input type="hidden" name="title9" value="">
									<input type="hidden" name="title10" value="">
									<input type="hidden" name="img1" value="">
									<input type="hidden" name="img2" value="">
									<input type="hidden" name="img3" value="">
									<input type="hidden" name="img4" value="">
									<input type="hidden" name="img5" value="">
									<input type="hidden" name="img6" value="">
									<input type="hidden" name="img7" value="">
									<input type="hidden" name="img8" value="">
									<input type="hidden" name="img9" value="">
									<input type="hidden" name="img10" value="">
									<input type="hidden" name="text1" value="">
									<input type="hidden" name="text2" value="">
									<input type="hidden" name="text3" value="">
									<input type="hidden" name="text4" value="">
									<input type="hidden" name="text5" value="">
									<input type="hidden" name="text6" value="">
									<input type="hidden" name="text7" value="">
									<input type="hidden" name="text8" value="">
									<input type="hidden" name="text9" value="">
									<input type="hidden" name="text10" value="">
									<input type="hidden" name="act1" value="">
									<input type="hidden" name="act2" value="">
									<input type="hidden" name="act3" value="">
									<input type="hidden" name="act4" value="">
									<input type="hidden" name="act5" value="">
									<input type="hidden" name="act6" value="">
									<input type="hidden" name="act7" value="">
									<input type="hidden" name="act8" value="">
									<input type="hidden" name="act9" value="">
									<input type="hidden" name="act10" value="">
									<input type="hidden" name="label1" value="">
									<input type="hidden" name="label2" value="">
									<input type="hidden" name="label3" value="">
									<input type="hidden" name="label4" value="">
									<input type="hidden" name="label5" value="">
									<input type="hidden" name="label6" value="">
									<input type="hidden" name="label7" value="">
									<input type="hidden" name="label8" value="">
									<input type="hidden" name="label9" value="">
									<input type="hidden" name="label10" value="">
									<input type="hidden" name="value1" value="">
									<input type="hidden" name="value2" value="">
									<input type="hidden" name="value3" value="">
									<input type="hidden" name="value4" value="">
									<input type="hidden" name="value5" value="">
									<input type="hidden" name="value6" value="">
									<input type="hidden" name="value7" value="">
									<input type="hidden" name="value8" value="">
									<input type="hidden" name="value9" value="">
									<input type="hidden" name="value10" value="">
								</form>
								</div>
							</div>
						</center>
                </div>
            </div>

			<div class="panel panel-default" style="margin-left:20px;background:papayawhip;float:left;">
				<div style="text-align:center;font-size:10px;color:red;height:25px;padding:10px;">※保存するとPreviewに反映されます</div>
				<div class="line__container">
					<!-- タイトル -->
					<div class="line__title">
						@if( !empty($db_data->name) )
						{{ $db_data->name }}
						@endif
					</div>

					<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
					<div class="line_carousel_contents scroll">
						@if( !empty($list_msg) )
						<div class="line__left">
							<carousel_figure>
							  <img src="/images/admin/line_none.jpg" />
							</carousel_figure>
						</div>
						<div class="slide-wrap" style="height:350px;">
							@foreach($list_msg as $index => $lines)
								@if( preg_match("/\.(png|jpg|jpeg)$/", $lines[0]) > 0 )
									<!-- 相手のスタンプ -->
									<div class="slide-box">
										<div style="width:100%;height:200px;background-image:url('/images/preview/{{ $lines[0] }}');background-size:100% auto;"></div>
										<div style="padding:5px;text-align:left;font-size:13px;font-weight:bold;">{{ $lines[2] }}</div>
										<div style="height:100%;padding:0 5px 10px 5px;">{{ $lines[3] }}</div>
										<div style="width:50%;text-align:center;height:50px;padding:15px;position:absolute;bottom:0;"><a href="{{ $lines[6] }}" target="_blank">{{ $lines[5] }}</a></div>
									</div>
								@endif
							@endforeach
						</div>
						@endif
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
		success: function(redirect_url) {
			window.location.href = redirect_url;
		},
		//通信がエラーになったとき
		error: function(error) {

		}
	});
}

$(document).ready(function(){
	var idNo = {{ count($list_msg) }};

	$.datetimepicker.setLocale('ja');

	//送信予定時刻
	$('#reserve_date').datetimepicker({
		step:1,
	});

	$('#add_img_form').click(function(event){
		event.preventDefault();
		$('.disp_cloumn').css('display','block');
		var stop_flg = true;
		$('[id^=formLine]').each(function(){
			var id = $(this).attr('id').replace(/formLineimg/,"");
			if( $("#dropimg"+id).length > 0 ){
				if( $("#"+this.id+" img").attr("src") == undefined ){
					swal("最初に保存してから次のフォームを追加してください");
					stop_flg = false;
					return false;
				}
			}
		});
		if( !stop_flg ){
			return false;
		}
		idNo++;
		if( idNo == 11 ){
			swal("LINEの仕様でカラムは最大１０件までしか作成できません");
			return false;
		}

		//表示されているカラムを非表示
		$("[class^=cloumn]").each(function(index,elem){
			var tr_class = $(this).attr('class');
			var tab_id = $(this).attr('class').replace(/cloumn/, "");
			$('.'+tr_class).css('display', 'none');
			$("#"+tab_id).removeClass('active');
		});
		$('#tab-menu').append("<li class='active typeimg"+idNo+"' id='img"+idNo+"'>カラム"+idNo+"</li>");
		$('#message_form').append("<tr style='text-align:center;border:1px solid darkgray;' class='cloumnimg"+idNo+"'><td style='padding:15px;'><form id='formLineimg"+idNo+"' class='form-horizontal' method='POST' action='/admin/member/line/setting/carousel/img/{{ $channel_id }}/upload'><table width='100%'><tr style='text-align:left;font-size:12px;font-weight:bold;'><td><b>挿入画像</b></td></tr><tr style='text-align:center;'><td><div id='file_upload_sectionmsd"+idNo+"' style='width:100%;'><div id='dropimg"+idNo+"' style='border:1px solid darkgray;text-align:center;width:1000px;height:90px; vertical-align:middle; display:table-cell;' ondragleave=\"onDragLeave(event, 'dropimg"+idNo+"', 'white');\" ondragover=\"onDragOver(event, 'dropimg"+idNo+"', 'wheat');\" ondrop=\"onDrop(event, 'formLineimg"+idNo+"', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id', 'msg', 'send_type'], 'post', '10000', '{{ $redirect_url }}', 'msg"+idNo+"');\"><div style='font:italic normal bold 16px/150% メイリオ,sans-serif;color:silver;'>アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div><input type='hidden' name='page' value='1'><input type='hidden' name='send_type' value='1'><input type='hidden' name='msg' value='img"+idNo+"'><input type='hidden' name='edit_id' value='{{ $edit_id }}'><center><div id='resultmsg"+idNo+"' style='font:italic normal bold 16px/150% メイリオ,sans-serif;width:100%;'></div></center></div></div></td><td><button type='submit' id='delmsg"+idNo+"' class='btn btn-primary del_msg' style='margin:5px;'>削除</button></td></tr><tr><td colspan='3'>&nbsp;</td></tr><tr style='text-align:left;'><td colspan='3'><div style='float:left;text-align:left;width:40%;'><div style='text-align:left;font-size:12px;font-weight:bold;'><b>カラムタイトル</b></div><input type='text' id='column_titleimg"+idNo+"' name='column_titleimg"+idNo+"' value='' maxlength='40' class='form-control' placeholder='カラムのタイトル'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='column_title_lenimg"+idNo+"'>0</span>/40文字</div></div><div style='float:left;text-align:left;width:60%;'><div style='text-align:left;font-size:12px;font-weight:bold;'><b>テキスト</b></div><input type='text' id='msgimg"+idNo+"' name='msgimg"+idNo+"' maxlength='60' class='form-control' placeholder='メッセージテキスト'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='msg_lenimg"+idNo+"'>0</span>/60文字</div></div></td></tr><tr style='text-align:center;'><td colspan='2'><div style='padding-top:10px;width:27%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションタイプ</div><select name='actionimg"+idNo+"' class='form-control'><option value='1'>webページへのリンク</option><option value='3'>ポストバック</option></select></div><div id='slt_action"+idNo+"' style='padding-top:10px;width:73%;float:left;'><div style='width:30%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションラベル</div><input type='text' id='labelimg"+idNo+"' name='labelimg"+idNo+"' maxlength='10' value='' class='form-control' placeholder='例）もっと見る'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_lenimg"+idNo+"'>0</span>/10文字</div></div><div style='width:70%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div><input type='text' name='label_actionimg"+idNo+"' value='' class='form-control' placeholder='例）https://yahoo.co.jp'></div></div></td></tr></table></form></td></tr>");

		//カラムタイトルの残り入力文字数の表示
		$('[id^=column_title]').keyup(function(e){
			var column_title = $('#'+this.id).val();
			var str_len = column_title.length;
			$('#column_title_len'+this.id.replace(/column_title/, '')).html(str_len);
		});

		//カラムタイトルの残り入力文字数の表示
		$('[id^=msg]').keyup(function(e){
			var column_text = $('#'+this.id).val();
			var str_len = column_text.length;
			$('#msg_len'+this.id.replace(/msg/, '')).html(str_len);
		});

		//カラムタイトルの残り入力文字数の表示
		$('[id^=label]').keyup(function(e){
			var label = $('#'+this.id).val();
			var str_len = label.length;
			$('#label_len'+this.id.replace(/label/, '')).html(str_len);
		});

		//カラム名を変更して表示
		$('#click_cloumn').text($('#img'+idNo).text());

		//タブ切り替え
		$('#tab-menu li').on('click', function(){
			var tab_id = $(this).attr("id");
//			$('[name="send_type"]').val($(this).attr('class').replace(/type/, '').replace(/\s*active/, ''));

			//カラム名を変更して表示
			$('#click_cloumn').text($('#'+this.id).text());

			//編集したIDがわかるようにIDをパラメータに設定
			$('[name="tab"]').val($(this).attr("id"));

			var resetColorImage = $.when(
				$(".img").each(function(){
					$("#"+this.id).css("background", "white");
				})
			);
			resetColorImage.done(function(){
				$("#img"+tab_id).css("background", "#58FA58");			
			});

			var type = $(this).attr("class").replace(/(^.*type)/, "");
			$('[name="type"]').val(type);

			if($(this).not('active')){
				// タブメニュー
				$(this).addClass('active').siblings('li').removeClass('active');
			}

			$("[class^=cloumn]").each(function(index,elem){
				var tr_class = $(this).attr('class');
				var match_tab_id = $(this).attr('class').replace(/cloumn/, "");
				if( tab_id == match_tab_id ){
					$('.'+tr_class).css('display', 'block');
				}else{
					$('.'+tr_class).css('display', 'none');
				}
			});
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
			return false;
		});

		$('[name^=action]').change(function(){
			var value = $(this).val();
			var id = $(this).attr('name').replace(/action/, "");
			$('#slt_action'+idNo).empty();

			var msg = "";
			var example = "";
			if( value == 1 ){
				msg = "リンクURL";
				example = "例）https://yahoo.co.jp";
				$('#slt_action'+idNo).append("<div style='width:30%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションラベル</div><input type='text' id='labelimg"+idNo+"' name='labelimg"+idNo+"' value='' maxlength='20' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_lenimg"+idNo+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><input type='text' id='label_actionimg"+idNo+"' name='label_actionimg"+idNo+"' value='' class='form-control' placeholder=''></div></div>");
			}else if( value == 2 ){
				msg = "メッセージテキスト";
				example = "任意のテキストを入力してください";
			}else if( value == 3 ){
				msg = "ポストバック値";
				example = "受け取りたい値を入力してください";
				$('#slt_action'+idNo).append("<div style='width:30%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションラベル</div><input type='text' id='labelimg"+idNo+"' name='labelimg"+idNo+"' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_lenimg"+idNo+"'>0</span>/20文字</div></div><div style='float:left;width:70%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><select id='label_actionimg"+idNo+"' name='label_actionimg"+idNo+"' class='form-control'><option value=''>使用する管理名を選択してください</option>{!! $postback_options !!}</select></div></div>");
			}
		});

		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		Ajax(id, "/admin/member/line/push/message/{{ $channel_id }}/delete");
		return false;
	});

	$('#push_btn').click(function(){
		var stop_flg = false;
		//それぞれのカラムからデータ取得
		$('[id^=formLineimg]').each(function(){
			var id = $(this).attr('id').replace(/formLineimg/,"");
			var title = $('#formLineimg'+id+' input[name^=column_titleimg'+id+']').val();
			var img = $("#"+this.id+" img").attr("src");
			var msg_id = $(this).attr('id').replace(/formLineimg/,"").replace(/(\d+)_\d+/, "$1");
			var msg = $('#formLineimg'+id+' input[name^=msgimg'+id+']').val();
			var action = $('#formLineimg'+id+' [name^=actionimg'+id+'] option:selected').val();
			var label = $('#formLineimg'+id+' input[name^=labelimg'+id+']').val();
			var label_action = $('#formLineimg'+id+' input[name^=label_actionimg'+id+']').val();
			if( label_action == undefined ){
				label_action = $('#formLineimg'+id+' [name^=label_actionimg'+id+'] option:selected').val();			
			}

			if( img != undefined ){
				$('#formCarouselSetting [name=img'+msg_id+']').val(img.replace(/\/images\/preview\/(.*)/, "$1"));
			}else{
				stop_flg = true;
				return false;
			}
			if( title != undefined ){
				$('#formCarouselSetting [name=title'+msg_id+']').val(title);
			}
			if( msg != undefined ){
				$('#formCarouselSetting [name=text'+msg_id+']').val(msg);
			}
			if( action != undefined ){
				$('#formCarouselSetting [name=act'+msg_id+']').val(action);
			}
			if( label != undefined ){
				$('#formCarouselSetting [name=label'+msg_id+']').val(label);
			}
			if( label_action != undefined ){
				$('#formCarouselSetting [name=value'+msg_id+']').val(label_action);
			}
		});

		if( stop_flg ){
			swal("画像を設定してください");
			return false;
		}

		var reserve_date = $('input[name=tmp_reserve_date]').val();
		if( reserve_date != undefined ){
			$('#formCarouselSetting [name=reserve_date]').val(reserve_date);
		}

		var push_title = $('input[name=tmp_push_title]').val();
		if( push_title != undefined ){
			$('#formCarouselSetting [name=push_title]').val(push_title);
		}

		var img_ratio = $('[name=tmp_img_ratio] option:selected').val();
		if( img_ratio != undefined ){
			$('#formCarouselSetting [name=img_ratio]').val(img_ratio);
		}

		var img_size = $('[name=tmp_img_size] option:selected').val();
		if( img_size != undefined ){
			$('#formCarouselSetting [name=img_size]').val(img_size);
		}

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCarouselSetting', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_save_end_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $save_redirect_url }}');
	});

	$('#save_btn').click(function(){
		var stop_flg = false;
		//それぞれのカラムからデータ取得
		$('[id^=formLineimg]').each(function(){
			var id = $(this).attr('id').replace(/formLineimg/,"");
			var title = $('#formLineimg'+id+' input[name^=column_titleimg'+id+']').val();
			var img = $("#"+this.id+" img").attr("src");
			var msg_id = $(this).attr('id').replace(/formLineimg/,"").replace(/(\d+)_\d+/, "$1");
			var msg = $('#formLineimg'+id+' input[name^=msgimg'+id+']').val();
			var action = $('#formLineimg'+id+' [name^=actionimg'+id+'] option:selected').val();
			var label = $('#formLineimg'+id+' input[name^=labelimg'+id+']').val();
			var label_action = $('#formLineimg'+id+' input[name^=label_actionimg'+id+']').val();
			if( label_action == undefined ){
				label_action = $('#formLineimg'+id+' [name^=label_actionimg'+id+'] option:selected').val();			
			}
//alert(id+"<>"+label+"<>"+label_action);
			if( img != undefined ){
				$('#formCarouselSetting [name=img'+msg_id+']').val(img.replace(/\/images\/preview\/(.*)/, "$1"));
			}else{
				stop_flg = true;
				return false;
			}
			if( title != undefined ){
				$('#formCarouselSetting [name=title'+msg_id+']').val(title);
			}
			if( msg != undefined ){
				$('#formCarouselSetting [name=text'+msg_id+']').val(msg);
			}
			if( action != undefined ){
				$('#formCarouselSetting [name=act'+msg_id+']').val(action);
			}
			if( label != undefined ){
				$('#formCarouselSetting [name=label'+msg_id+']').val(label);
			}
			if( label_action != undefined ){
				$('#formCarouselSetting [name=value'+msg_id+']').val(label_action);
			}
		});

		if( stop_flg ){
			swal("画像を設定してください");
			return false;
		}

		var reserve_date = $('input[name=tmp_reserve_date]').val();
		if( reserve_date != undefined ){
			$('#formCarouselSetting [name=reserve_date]').val(reserve_date);
		}

		var push_title = $('input[name=tmp_push_title]').val();
		if( push_title != undefined ){
			$('#formCarouselSetting [name=push_title]').val(push_title);
		}

		var img_ratio = $('[name=tmp_img_ratio] option:selected').val();
		if( img_ratio != undefined ){
			$('#formCarouselSetting [name=img_ratio]').val(img_ratio);
		}

		var img_size = $('[name=tmp_img_size] option:selected').val();
		if( img_size != undefined ){
			$('#formCarouselSetting [name=img_size]').val(img_size);
		}

		$('#formCarouselSetting [name=send_status]').val('99');

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCarouselSetting', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_save_end_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $save_redirect_url }}');
	});

	//デフォルトのカラムを表示
	$("[class^=cloumn]").each(function(index,elem){
		var tr_class = $(this).attr('class');
		if( index == 0 ){
			$('.disp_cloumn').css('display','block');
			$('.'+tr_class).css('display', 'block');

			//デフォルトのカラム名を表示
			$('#click_cloumn').text($('#img1').text());

			//カラムタイトルの残り入力文字数の表示
			var column_title = $('#column_titleimg1').val();
			var str_len = column_title.length;
			$('#column_title_lenimg1').html(str_len);

			//カラムタイトルの残り入力文字数の表示
			var column_text = $('#msgimg1').val();
			var str_len = column_text.length;
			$('#msg_lenimg1').html(str_len);

			//カラムタイトルの残り入力文字数の表示
			var label = $('#labelimg1').val();
			var str_len = label.length;
			$('#label_lenimg1').html(str_len);

			var name = $('[name=actionimg1] option:selected').val();
			var example = "";
			if( name == 1 ){
				example = "例）https://yahoo.co.jp";
			}else if( name == 2 ){
				example = "任意のテキストを入力してください";
			}else if( name == 3 ){
				example = "受け取りたい値を入力してください";
			}
			$('#formLineimg1 input[name=label_actionimg1]').attr('placeholder', example);

		}else{
			$('.'+tr_class).css('display', 'none');
		}
	});

	//タブ切り替え
	$('#tab-menu li').on('click', function(){
		var tab_id = $(this).attr("id");
//		$('[name="send_type"]').val($(this).attr('class').replace(/type/, '').replace(/\s*active/, ''));

		//カラム名を変更して表示
		$('#click_cloumn').text($('#'+this.id).text());

		//編集したIDがわかるようにIDをパラメータに設定
		$('[name="tab"]').val($(this).attr("id"));

		var resetColorImage = $.when(
			$(".img").each(function(){
				$("#"+this.id).css("background", "white");
			})
		);
		resetColorImage.done(function(){
			$("#img"+tab_id).css("background", "#58FA58");			
		});

		var type = $(this).attr("class").replace(/(^.*type)/, "");
		$('[name="type"]').val(type);

		if($(this).not('active')){
			// タブメニュー
			$(this).addClass('active').siblings('li').removeClass('active');
		}

		$("[class^=cloumn]").each(function(index,elem){
			var tr_class = $(this).attr('class');
			var match_tab_id = $(this).attr('class').replace(/cloumn/, "");
			if( tab_id == match_tab_id ){
				$('.'+tr_class).css('display', 'block');

				//カラムタイトルの残り入力文字数の表示
				var column_title = $('#column_title'+tab_id).val();
				var str_len = column_title.length;
				$('#column_title_len'+tab_id).html(str_len);

				//カラムタイトルの残り入力文字数の表示
				var column_text = $('#msg'+tab_id).val();
				var str_len = column_text.length;
				$('#msg_len'+tab_id).html(str_len);

				//カラムタイトルの残り入力文字数の表示
				var label = $('#label'+tab_id).val();
				var str_len = label.length;
				$('#label_len'+tab_id).html(str_len);

			}else{
				$('.'+tr_class).css('display', 'none');
			}
		});
	});

	//カラムタイトルの残り入力文字数の表示
	$('[id^=column_title]').keyup(function(e){
		var column_title = $('#'+this.id).val();
		var str_len = column_title.length;
		$('#column_title_len'+this.id.replace(/column_title/, '')).html(str_len);
	});

	//カラムタイトルの残り入力文字数の表示
	$('[id^=msg]').keyup(function(e){
		var column_text = $('#'+this.id).val();
		var str_len = column_text.length;
		$('#msg_len'+this.id.replace(/msg/, '')).html(str_len);
	});

	//カラムタイトルの残り入力文字数の表示
	$('[id^=label]').keyup(function(e){
		var label = $('#'+this.id).val();
		var str_len = label.length;
		$('#label_len'+this.id.replace(/label/, '')).html(str_len);
	});

	$('[name^=action]').change(function(){
		var value = $(this).val();
		var id = $(this).attr('name').replace(/action/, "");
		$('#slt_action'+id).empty();

		var msg = "";
		var example = "";
		if( value == 1 ){
			msg = "リンクURL";
			example = "例）https://yahoo.co.jp";
			$('#slt_action'+id).append("<div style='width:30%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションラベル</div><input type='text' id='label"+id+"' name='label"+id+"' value='' maxlength='20' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><input type='text' id='label_action"+id+"' name='label_action"+id+"' value='' class='form-control' placeholder=''></div></div>");
		}else if( value == 2 ){
			msg = "メッセージテキスト";
			example = "任意のテキストを入力してください";
		}else if( value == 3 ){
			msg = "ポストバック値";
			example = "受け取りたい値を入力してください";
			$('#slt_action'+id).append("<div style='width:30%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションラベル</div><input type='text' id='label"+id+"' name='label"+id+"' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:70%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><select id='label_action"+id+"' name='label_action"+id+"' class='form-control'><option value=''>使用する管理名を選択してください</option>{!! $postback_options !!}</select></div></div>");
		}
		$('#formLine'+id+' input[name=label_actionimg1]').attr('placeholder', example);
	});
});


</script>

@endsection

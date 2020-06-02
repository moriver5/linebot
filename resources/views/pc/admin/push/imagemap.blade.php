@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div style="width:1600px;float:left;">
            <div class="panel panel-default" style="width:1150px;float:left;">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>イマージマップ作成</b>
				</div>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;">
								@if( !empty($db_data) )
								<div style="margin-left:20px;text-align:left;font-weight:bold;float:left">配信状況：<font color="#0080FF">{{ config('const.list_send_status')[$send_status] }}</font></div>
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
											<form id="formCommonLine" class="form-horizontal" method="POST" action="/admin/member/line/setting/imagemap/img/{{ $channel_id }}/upload">
											<table width="100%">
												<tr style="text-align:center;font-weight:bold;">
													<td style="width:22%;">
														<div style="text-align:left;font-size:12px;font-weight:bold;"><b>配信日時</b></div>
														<input type="text" id="reserve_date" name="tmp_reserve_date" class="form-control" value="{{ $reserve_date }}" placeholder="2019/08/27 16:00">
														<div style='font:bold 7px/70% メイリオ,sans-serif;margin-top:3px;'>&nbsp;</div>
													</td>
													<td style="width:68%;">
														<div style="text-align:left;font-size:12px;font-weight:bold;"><b>画像の代替テキスト</b></div>
														<input type="text" id="alttext" name="tmp_alttext" class="form-control" value="{{ $alttext }}" placeholder="">
														<div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'>
															<span id='alttext_len'>0</span>/400文字
														</div>
													</td>
												</tr>
												<tr style="text-align:center;font-weight:bold;">
													<td colspan="2">
														<div style="margin-top:10px;text-align:left;font-size:12px;font-weight:bold;"><b>挿入画像</b></div>
														<div id="file_upload_section" style="width:100%;border:1px solid darkgray;">
															<div id="drop" style="text-align:center;width:1000px;height:90px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formCommonLine', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type', 'tmp_reserve_date', 'tmp_push_title', 'tmp_img_ratio', 'tmp_img_size'], 'post', '10000', '{{ $redirect_url }}', '')">
																<div style="font:italic normal bold 14px/140% 'メイリオ',sans-serif;color:silver;padding-top:20px;">アップロードするファイルをここにドラッグアンドドロップしてください<br>反映されないときはctrl+F5を押してください</div>
																<center><div id="result" style="font:italic normal bold 14px/140% メイリオ,sans-serif;width:100%;"></div></center>
																<br /><input type="file" id="imgfile" name="import_file"><input type="button" value="画像選択" style="margin-bottom:25px;">
															</div>
														</div>
														<input type="hidden" name="page" value="1">
														<input type='hidden' name='send_type' value="1">
														<input type="hidden" name="msg" value="">
														<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
														<input type="hidden" name="edit_id" value="{{ $edit_id }}">
													</td>
												</tr>
												<tr style="text-align:center;font-weight:bold;">
													<td colspan="2" style="padding:10px 10px 0 10px;text-align:center;">
														@if( !empty($img) )
														<canvas id="drowing" width="0" height="0"></canvas>
														<br>
														<button type="submit" id="del" class="btn btn-primary del_msg" style="margin:5px;">削除</button>
														@endif
													</td>
												</tr>
											</table>
											</form>
										</td>
									</tr>
									<tr class="disp_cloumn" style="display:none;text-align:center;">
										<td style="padding:10px;"></td>
									</tr>
									<tr class="disp_cloumn" style="text-align:center;border-top:1px solid darkgray;border-left:1px solid darkgray;border-right:1px solid darkgray;background:#9FF781;">
										<td>
											<div style="text-align:left;padding:5px;"><b id="click_cloumn">イメージマップ設定</b></div>
										</td>
									</tr>
									<tr style="text-align:center;border-top:1px solid darkgray;border-left:1px solid darkgray;border-right:1px solid darkgray;">
										<td>
											<div style="text-align:left;padding:5px;font-size:10px;color:red;">
												※領域選択したいフォームのマップをチェックしてから画像のクリックしたい領域をマウスで選択してください<br>
												※LINEの仕様上、横幅：1040pxの画像を基準にイメージマップが設定されるため、横幅：1040pxの画像をアップロードして設定してください。別サイズの画像で設定するとクリック位置がズレます。<br>
												※画像フォーマット：JPEGまたはPNGのみ使用可能<br>
												※最大ファイルサイズ：1MB
											</div>
										</td>
									</tr>
									<form id='formLine' class='form-horizontal' method='POST' action='/admin/member/line/setting/carousel/img/{{ $channel_id }}/upload'>
									@if( !empty($list_area) )
										@foreach($list_area as $index => $lines)
											<tr style='text-align:center;border:1px solid darkgray;' class='cloumn{{ ($loop->index+1) }}'>
												<td style='padding:5px;'>
													<table width='100%'>
													<tr style='text-align:left;'>
														<td>
															<div style='float:left;text-align:left;width:6%;'>
																<div style='text-align:left;font-size:10px;font-weight:bold;'>
																	<b>マップ{{ ($loop->index+1) }}</b>
																</div>
																<input type='radio' id='active{{ ($loop->index+1) }}' name='active' value="{{ ($loop->index+1) }}" placeholder='' style="margin:13px 10px;">
															</div>
															<div style='float:left;text-align:left;width:35%;'>
																<div style='text-align:left;font-size:10px;font-weight:bold;'>
																	<b>遷移先URL</b>
																</div>
																<input type='text' id='url{{ ($loop->index+1) }}' name='url{{ ($loop->index+1) }}' value='{{ $lines[4] }}' maxlength='255' class='form-control' placeholder=''>
															</div>
															<div style='float:left;text-align:left;width:10%;margin-left:40px;'>
																<div style='text-align:left;font-size:10px;font-weight:bold;'>
																	&nbsp;
																</div>
																<button type="submit" id="delmap{{ ($loop->index+1) }}" class="btn btn-primary">削除</button>
															</div>
														</td>
													</tr>
													</table>
													<input type='hidden' name='logs_edit_id"+idNo+"' value=''>
												</td>
											</tr>
										@endforeach
									@endif
									</form>
								</table>
								<br />
							<form id="formCarouselSetting" class="form-horizontal" method="POST" action="/admin/member/line/setting/imagemap/save/{{ $channel_id }}/send">
								{{ csrf_field() }}
								<button type="submit" id="save_btn" class="btn btn-primary push_save">下書き保存</button>
								<button type="submit" id="push_btn" class="btn btn-primary push_save">予約設定更新</button>
								<button type="submit" id="add_img_form" class="btn btn-primary">イメージマップ追加</button>
								<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
								<input type='hidden' name='tab' value="">
								<input type='hidden' name='send_status' value="0">
								<input type='hidden' name='send_type' value="1">
								<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
								<input type='hidden' name='edit_id' value="{{ $edit_id }}">
								<input type="hidden" name="reserve_date" value="">
								<input type="hidden" name="alttext" value="">
								<input type="hidden" name="imagemap" value="">
							</form>
							</div>
						</div>
					</center>
                </div>
            </div>

			<div class="panel panel-default" style="margin-left:20px;background:papayawhip;float:left;">
				<div style="text-align:center;font-size:10px;color:red;height:25px;padding:10px;">
					※保存するとPreviewに反映されます。<br>
				</div>
				<div class="line__container" style="width:323px;">
					<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
					<div class="line_imagemap_contents scroll">
						<br>
						<div>
						  <div>
							@if( !empty($img) )
								<div style="width:100%;"><img src="/php/line/imagemap/{{ $channel_id }}/{{ $img }}/300" style="border-radius:2%;" /></div>
								<date_area style="float:right;margin-right:2px;">
								  {{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s)(\d{2}:\d{2}):\d{2}/", "$2", $reserve_date) }}
								</date_area>
							@endif
						  </div>
						</div>
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

function Ajax(action_url){
	$.ajax({
		url: action_url,
		type: "post",
		timeout: 10000,
		data: $('#formCommonLine').serialize(),

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
	var idNo = {{ count($list_area) }};
	var reacts = {};

	$.datetimepicker.setLocale('ja');

	//送信予定時刻
	$('#reserve_date').datetimepicker({
		step:1,
	});

	$('#add_img_form').click(function(event){
		event.preventDefault();
		idNo++;

		$('#message_form').append("<tr style='text-align:center;border:1px solid darkgray;' class='cloumn"+idNo+"'><td style='padding:5px;'><table width='100%'><tr style='text-align:left;'><td><div style='float:left;text-align:left;width:6%;'><div style='text-align:left;font-size:10px;font-weight:bold;'><b>マップ"+idNo+"</b></div><input type='radio' id='active"+idNo+"' name='active' value='"+idNo+"' placeholder='' style='margin:13px 10px;'></div><div style='float:left;text-align:left;width:35%;'><div style='text-align:left;font-size:10px;font-weight:bold;'><b>遷移先URL</b></div><input type='text' id='url"+idNo+"' name='url"+idNo+"' maxlength='255' class='form-control' placeholder=''></div><div style='float:left;text-align:left;width:10%;margin-left:40px;'><div style='text-align:left;font-size:10px;font-weight:bold;'>&nbsp;</div><button type='submit' id='delmap"+idNo+"' class='btn btn-primary'>削除</button></div></td></tr></table><input type='hidden' name='logs_edit_id"+idNo+"' value=''></td></tr>");

		//シナリオメッセージの残り入力文字数の表示
		$('[id^=alttext]').keyup(function(e){
			var column_text = $('#'+this.id).val();
			var str_len = column_text.length;
			$('#alttext_len').html(str_len);
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax("/admin/member/line/setting/imagemap/img/{{ $channel_id }}/delete");
			return false;
		});

		//削除ボタン押下
		$('[id^=delmap]').click(function(){
			event.preventDefault();
			var id = this.id.replace(/^delmap/, '');
			delete reacts[id];
			$(this).parents(".cloumn"+id).remove();
			draw(reacts);
		});

		$('[id^=active]').click(function(){
			//選択した背景色をリセット
			$('[class^=cloumn]').css('background', '');

			id = $(this).attr('id').replace(/active/,"");
			var active = $('input[name=active]:checked').val();
			if( active == id ){
				active_id = active;
				if( reacts[id] == undefined ){
					reacts[active_id] = {};
				}
				draw(reacts, active_id);

				//選択した背景色を設定
				$('.cloumn'+active_id).css('background', 'yellow');

				return;
			}
		});

		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		Ajax("/admin/member/line/setting/imagemap/img/{{ $channel_id }}/delete");
		return false;
	});

	$('.push_save, .push_send').click(function(){
		//それぞれのカラムからデータ取得
		var err_flg = false;
		var imagemap = [];
		$('[id^=active]').each(function(){
			var id = this.id.replace(/active/, '');
			var xpos = $('input[name="xpos'+id+'"]').val();
			var width = $('input[name="width'+id+'"]').val();
			var ypos = $('input[name="ypos'+id+'"]').val();
			var height = $('input[name="height'+id+'"]').val();
			var url = $('input[name="url'+id+'"]').val();
			if( url == '' ){
				err_flg = true;
				return false;
			}
			for(key in reacts){
				if( key == id ){
//alert(key+"<>"+Math.round(reacts[key].startX)+"<>"+Math.round(reacts[key].startY)+"<>"+Math.round(Math.abs(reacts[key].endX))+"<>"+Math.round(Math.abs(reacts[key].endY))+"<>"+url);
					imagemap[imagemap.length] = [Math.round(reacts[key].startX), Math.round(Math.abs(reacts[key].endX)), Math.round(reacts[key].startY), Math.round(Math.abs(reacts[key].endY)), url];
				}
			}
		});

		if( err_flg ){
			swal("遷移先URLを入力してください");
			return false;
		}

		if( imagemap.length > 0 ){
			$('#formCarouselSetting [name=imagemap]').val(imagemap.join('|'));
		}

		var reserve_date = $('input[name=tmp_reserve_date]').val();
		if( reserve_date != undefined ){
			$('#formCarouselSetting [name=reserve_date]').val(reserve_date);
		}

		var alttext = $('input[name=tmp_alttext]').val();
		if( alttext != undefined ){
			$('#formCarouselSetting [name=alttext]').val(alttext);
		}

		if( this.id == 'save_btn' ){
			$('#formCarouselSetting [name=send_status]').val('99');
		}else{
			
		}

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCarouselSetting', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_save_end_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $save_redirect_url }}');
	});

	//画面読み込み時の代替テキストの入力文字数表示
	var column_text = $('#alttext').val();
	var str_len = column_text.length;
	$('#alttext_len').html(str_len);

	//代替テキストフォームでキーボードのキーを上げたときに代替テキストの入力文字数表示
	$('[id^=alttext]').keyup(function(e){
		var column_text = $('#alttext').val();
		var str_len = column_text.length;
		$('#alttext_len').html(str_len);
	});

	//画像のマウス領域選択
	var canvas = document.getElementById('drowing');
	if( canvas != null ){
		var context = canvas.getContext('2d');
		var active_id = null;

		//DB保存の座標データで初期化
		@foreach($list_area as $index => $lines)
			reacts[{{$index+1}}] = {startY:{{ $lines[2] }}, startX:{{ $lines[0] }}, endY:{{ $lines[3] }}, endX:{{ $lines[1] }}};
		@endforeach

		//アップロード画像表示
		var imageObj = new Image();
		imageObj.onload = function() {
			canvas.width = imageObj.width;
			canvas.height = imageObj.height;
			draw(reacts);
		};
		imageObj.src = '/images/preview/{{ $img }}';

		canvas.addEventListener("mousedown", onMouseDown, false);
		canvas.addEventListener("mouseup" , onMouseUp , false);
		window.addEventListener("keyup" , onKeyUp , false);
	}

	//チェックを入れたラジオボタンの項目の背景色を変える
	$('[id^=active]').click(function(){
		//選択した背景色をリセット
		$('[class^=cloumn]').css('background', '');

		id = $(this).attr('id').replace(/active/,"");
		var active = $('input[name=active]:checked').val();
		if( active == id ){
			active_id = active;
			if( reacts[id] == undefined ){
				reacts[active_id] = {};
			}
			draw(reacts, active_id);

			//選択した背景色を設定
			$('.cloumn'+active_id).css('background', 'yellow');

			return;
		}
	});

	//削除ボタン押下
	$('[id^=delmap]').click(function(){
		event.preventDefault();
		var id = this.id.replace(/^delmap/, '');
		delete reacts[id];
		$(this).parents(".cloumn"+id).remove();
		draw(reacts);
	});

	// 矩形オブジェクト
	var _rectangle = createRect();

	//初期値座標
	function createRect() {
		return { startY:0, startX:0, endY:0, endX:0 };
	};

	//画像内でマウスをクリックしたら
	function onMouseDown (e) {
		var node = document.getElementById('drowing');
		var clientRect = node.getBoundingClientRect();
		var x = clientRect.left;
		var y = clientRect.top;

		_rectangle.startY = e.clientY - y;
		_rectangle.startX = e.clientX - x;

		$('[id^=active]').each(function(){
			id = $(this).attr('id').replace(/active/,"");
			var active = $('input[name=active]:checked').val();
			if( active == id ){
				active_id = active;
				if( reacts[id] == undefined ){
					reacts[active_id] = {};
				}
				$('#xpos'+id).val(Math.round(e.clientX - x));
				$('#ypos'+id).val(Math.round(e.clientY - y));
				return;
			}
		});

		if( active_id == null ){
			swal("画像領域を変更したいフォームのマップにチェックを入れてください");
			return false;
		}

		canvas.addEventListener ("mousemove", onMouseMove, false);
	};

	//画像内でクリックしたままマウスを移動したら
	function onMouseMove (e) {
		draw(reacts);
		_rectangle.endY = e.layerY - _rectangle.startY;
		_rectangle.endX = e.layerX - _rectangle.startX;
//		context.lineWidth = 2;
//		context.strokeStyle = "rgb(255, 255, 0)";
		context.strokeRect (_rectangle.startX, _rectangle.startY, _rectangle.endX, _rectangle.endY);
	};

	//画像内でクリックしたままのマウスを上げたとき
	function onMouseUp (e) {
		var active;
		$('[id^=active]').each(function(){
			var id = $(this).attr('id').replace(/active/,"");
			active = $('input[name=active]:checked').val();

			if( active == id ){
				$('#width'+id).val(Math.round(Math.abs(_rectangle.endX)));
				$('#height'+id).val(Math.round(Math.abs(_rectangle.endY)));
				return;
			}
		});
		reacts[active_id] = _rectangle;
		draw(reacts, active_id);
		_rectangle = createRect();
		canvas.removeEventListener ("mousemove", onMouseMove, false);
	};

	//マウスで選択した領域を描く
	function draw(list_react, active_id = null) {
		context.drawImage(imageObj, 0, 0);
		context.lineWidth = 2;
		context.strokeStyle = "rgb(255, 255, 0)";
		context.fillStyle = "black";
		context.font = "10px 'メイリオ', 'Meiryo', 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', 'ＭＳ Ｐゴシック'";
		context.textAlign = "left";
		context.textBaseline = "top";

		if( list_react != undefined ){
			for(key in list_react){
//alert(list_react[key].startX+"|"+list_react[key].startY+"|"+list_react[key].endX+"|"+list_react[key].endY);
				if( key == active_id ){
					context.globalAlpha = 0.7;
					context.fillStyle = "rgb(255, 255, 0)";
					context.fillRect(list_react[key].startX, list_react[key].startY, list_react[key].endX, list_react[key].endY);
					context.fillStyle = "black";
					context.font = "10px 'メイリオ', 'Meiryo', 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', 'ＭＳ Ｐゴシック'";
				}else{
					context.globalAlpha = 1;
					context.fillStyle = "";
					context.strokeRect(list_react[key].startX, list_react[key].startY, list_react[key].endX, list_react[key].endY);
				}
				context.globalAlpha = 1;
				context.fillText("マップ"+key, (list_react[key].startX+3), (list_react[key].startY+3));
			}
		}
	};

	function onKeyUp (e) {
		switch(e.key) {
			case 'z':
				reacts[active_id].pop();
				break;
			default:
				return;
		};
		draw(reacts);
	};

	$('input[type=button]').click(function() {
		$('input[type=file]').trigger('click');
	});

	//ファイル選択から画像を選択してアップロード
	$("#imgfile").change(function(event){
		event.preventDefault();

		//ドロップされたファイルのfilesプロパティを参照
		var files = this.files;

		//画像の複数選択ドラッグのアップロード対応
        for (var i=0; i<files.length; i++) {
            FileUpload('formCommonLine', 'import_file', files[i], '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id',　'msg', 'send_type', 'tmp_reserve_date'], 'post', '10000', '{{ $redirect_url }}', i, '');
		}
	});

});

</script>

@endsection

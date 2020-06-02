@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div style="width:1350px;float:left;">
            <div class="panel panel-default" style="width:900px;float:left;">
                <div class="panel-heading" style="font:normal 13px/130% 'メイリオ',sans-serif;">
					<b>４択メッセージ作成</b>
				</div>
                <div class="panel-body">
						<center>
							<ul id="tab-menu">
							@if( !empty($list_scenario) )
							@foreach($list_scenario as $index => $scenario)
								@if( $loop->first )
									<li class="active type{{ ($loop->index+1) }}" id="{{ ($loop->index+1) }}">シナリオ{{ ($loop->index+1) }}（ID:{{ $scenario[1] }}）</li>
								@else
									<li class="type{{ ($loop->index+1) }}" id="{{ ($loop->index+1) }}">シナリオ{{ ($loop->index+1) }}（ID:{{ $scenario[1] }}）</li>
								@endif
							@endforeach
							@endif
							</ul>
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
												<form id="formCommonLine" class="form-horizontal" method="POST" action="/admin/member/line/setting/4choices/img/{{ $channel_id }}/upload">
												<table width="100%">
													<tr style="text-align:center;font-weight:bold;">
														<td style="width:50%;">
															<div style="text-align:left;font-size:12px;font-weight:bold;"><b>配信日時</b></div>
															<input type="text" id="reserve_date" name="tmp_reserve_date" class="form-control" value="{{ $reserve_date }}" placeholder="2019/08/27 16:00">
														</td>
														<td style="width:50%;">
															<div style="text-align:left;font-size:12px;font-weight:bold;"><b>メッセージのタイトル</b></div>
															<input type="text" name="tmp_push_title" value="{{ $push_title }}" class="form-control" placeholder="通知バナーに表示されるメッセージのタイトル">
														</td>
													</tr>
													<tr style="text-align:center;font-weight:bold;">
														<td style="width:50%;">
															<div style="text-align:left;font-size:12px;"><b>画像の比率</b></div>
															<select name="tmp_img_ratio" class="form-control">
																@foreach(config('const.list_carousel_img_ratio') as $index => $ratio)
																	@if( $index == $img_ratio )
																		<option value="{{ $index }}" selected>{{ $ratio }}</option>
																	@else
																		<option value="{{ $index }}">{{ $ratio }}</option>
																	@endif
																@endforeach
															</select>
														</td>
														<td style="width:50%;">
															<div style="text-align:left;font-size:12px;"><b>画像サイズ</b></div>
															<select name="tmp_img_size" class="form-control">
																@foreach(config('const.list_carousel_img_size') as $index => $size)
																	@if( $index == $img_size )
																		<option value="{{ $index }}" selected>{{ $size }}</option>
																	@else
																		<option value="{{ $index }}">{{ $size }}</option>
																	@endif
																@endforeach
															</select>
														</td>
													</tr>
													<tr style="text-align:center;font-weight:bold;">
														<td>
															<div style="text-align:left;font-size:12px;font-weight:bold;"><b>挿入画像</b></div>
															<div id="file_upload_section" style="width:100%;border:1px solid darkgray;">
																<div id="drop" style="text-align:center;width:1000px;height:90px; vertical-align:middle; display:table-cell;" ondragleave="onDragLeave(event, 'drop', 'white')" ondragover="onDragOver(event, 'drop', 'wheat')" ondrop="onDrop(event, 'formCommonLine', 'import_file', '{{csrf_token()}}', '', '{{ __('messages.dialog_upload_error_msg') }}',　['edit_id','msg', 'send_type', 'tmp_reserve_date', 'tmp_push_title', 'tmp_img_ratio', 'tmp_img_size'], 'post', '10000', '{{ $redirect_url }}', '')">
																	<div style="font:italic normal bold 14px/140% 'メイリオ',sans-serif;color:silver;">アップロードするファイルをここに<br />ドラッグアンドドロップしてください<br><br>反映されないときはctrl+F5を押してください</div>
																	<center><div id="result" style="font:italic normal bold 14px/140% メイリオ,sans-serif;width:100%;"></div></center>
																</div>
															</div>
															<input type="hidden" name="page" value="1">
															<input type='hidden' name='send_type' value="1">
															<input type="hidden" name="msg" value="">
															<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
															<input type="hidden" name="edit_id" value="{{ $edit_id }}">
														</td>
														<td style="padding:5px 10px 0 10px;text-align:center;">
															@if( !empty($img) )
															<img src="/images/preview/{{ $img }}" width="150" height="150">
															<button type="submit" id="del" class="btn btn-primary del_msg" style="margin:5px;">削除</button>
															@else
															<span style="color:darkgray;opacity:0.5;">画像表示エリア</span>
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
										<tr class="disp_cloumn" style="display:none;text-align:center;border-top:1px solid darkgray;border-left:1px solid darkgray;border-right:1px solid darkgray;background:#9FF781;">
											<td>
												<div style="text-align:left;padding:5px;"><b id="click_cloumn"></b></div>
											</td>
										</tr>
										@if( !empty($db_data) )
											@foreach($db_data as $index => $lines)
												<tr style="text-align:center;border:1px solid darkgray;" id="cloumn{{ ($loop->index+1) }}" class="cloumn{{ ($loop->index+1) }}">
													<td style="padding:15px;">
													<form id="formLine{{ ($loop->index+1) }}" class="form-horizontal" method="POST" action="">
													<table width="100%">
														<tr style="text-align:center;">
															<td colspan="2">
																<div style="float:left;text-align:left;width:100%;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;"><b>Q．シナリオメッセージ</b></div>
																	<input type="text" id="msg{{ ($loop->index+1) }}"　name="msg{{ ($loop->index+1) }}" class="form-control" value="{{ $lines->msg }}" maxlength="60" placeholder="あなたの好きなものは何ですか？！">
																	<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="msg_len{{ ($loop->index+1) }}"></span>/240文字</div>
																</div>
															</td>
														</tr>
														<tr style="text-align:center;">
															<td style='padding:15px 5px 5px 5px;'>
																A.1
															</td>
															<td>
																<div style="padding-top:10px;width:27%;float:left;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;">アクションタイプ</div>
																	<select name="action1_{{ ($loop->index+1) }}" class="action{{ ($loop->index+1) }} form-control">
																		@foreach(config('const.list_choices_action_type') as $act_index => $type_name)
																			@if( $lines->act1 == $act_index )
																				<option value="{{ $act_index }}" selected>{{ $type_name }}</option>
																			@else
																				<option value="{{ $act_index }}">{{ $type_name }}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div id="slt_action1_{{ ($loop->index+1) }}" style="padding-top:10px;width:73%;float:left;">
																	<div style="width:40%;float:left;">
																		<div style="text-align:left;font-size:12px;font-weight:bold;">選択肢</div>
																		<input type="text" id="label1_{{ ($loop->index+1) }}" name="label1_{{ ($loop->index+1) }}" value="{{ $lines->label1 }}" maxlength="20" class="form-control" placeholder="イチゴ">
																		<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="label_len1_{{ ($loop->index+1) }}"></span>/20文字</div>
																	</div>
																	<div style="float:left;width:60%;">
																		<div style="text-align:left;font-size:12px;" class="act_msg">
																			@if( $lines->act1 == 1 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action1_{{ ($loop->index+1) }}" name="label_action1_{{ ($loop->index+1) }}" value="{{ $lines->value1 }}" class="form-control" placeholder="">
																			@elseif( $lines->act1 == 2 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>メッセージテキスト</div>
																			@elseif( $lines->act1 == 3 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>ポストバック値</div>
																			<select id="label_action1_{{ ($loop->index+1) }}" name="label_action1_{{ ($loop->index+1) }}" class='form-control'>
																				<option value=''>選択してください</option>
																				@foreach($list_postback as $postbacks)
																					@if( $lines->value1 == $postbacks[0] )
																					<option value="{{ $postbacks[0] }}" selected>ID：{{ $postbacks[1] }}</option>
																					@else
																					<option value="{{ $postbacks[0] }}">ID：{{ $postbacks[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@elseif( $lines->act1 == 4 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>次のシナリオID</div>
																			<select id="label_action1_{{ ($loop->index+1) }}" name="label_action1_{{ ($loop->index+1) }}" class='form-control'>
																				<option value="">選択してください</option>
																				@foreach($list_scenario as $scenario)
																					@if( $lines->value1 == $scenario[0] )
																					<option value="{{ $scenario[0] }}" selected>シナリオID：{{ $scenario[1] }}</option>
																					@else
																					<option value="{{ $scenario[0] }}">シナリオID：{{ $scenario[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@else
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action1_{{ ($loop->index+1) }}" name="label_action1_{{ ($loop->index+1) }}" value="{{ $lines->value1 }}" class="form-control" placeholder="">
																			@endif
																		</div>
																	</div>
																</div>
															</td>
														</tr>

														<tr style="text-align:center;">
															<td style='padding:15px 5px 5px 5px;'>
																A.2
															</td>
															<td>
																<div style="padding-top:10px;width:27%;float:left;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;">アクションタイプ</div>
																	<select name="action2_{{ ($loop->index+1) }}" class="action{{ ($loop->index+1) }} form-control">
																		@foreach(config('const.list_choices_action_type') as $act_index => $type_name)
																			@if( $lines->act2 == $act_index )
																				<option value="{{ $act_index }}" selected>{{ $type_name }}</option>
																			@else
																				<option value="{{ $act_index }}">{{ $type_name }}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div id="slt_action2_{{ ($loop->index+1) }}" style="padding-top:10px;width:73%;float:left;">
																	<div style="width:40%;float:left;">
																		<div style="text-align:left;font-size:12px;font-weight:bold;">選択肢</div>
																		<input type="text" id="label2_{{ ($loop->index+1) }}" name="label2_{{ ($loop->index+1) }}" value="{{ $lines->label2 }}" maxlength="20" class="form-control" placeholder="スイカ">
																		<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="label_len2_{{ ($loop->index+1) }}"></span>/20文字</div>
																	</div>
																	<div style="float:left;width:60%;">
																		<div style="text-align:left;font-size:12px;" class="act_msg">
																			@if( $lines->act2 == 1 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action2_{{ ($loop->index+1) }}" name="label_action2_{{ ($loop->index+1) }}" value="{{ $lines->value2 }}" class="form-control" placeholder="">
																			@elseif( $lines->act2 == 2 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>メッセージテキスト</div>
																			@elseif( $lines->act2 == 3 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>ポストバック値</div>
																			<select id="label_action2_{{ ($loop->index+1) }}" name="label_action2_{{ ($loop->index+1) }}" class='form-control'>
																				<option value=''>選択してください</option>
																				@foreach($list_postback as $postbacks)
																					@if( $lines->value2 == $postbacks[0] )
																					<option value="{{ $postbacks[0] }}" selected>ID：{{ $postbacks[1] }}</option>
																					@else
																					<option value="{{ $postbacks[0] }}">ID：{{ $postbacks[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@elseif( $lines->act2 == 4 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>次のシナリオID</div>
																			<select id="label_action2_{{ ($loop->index+1) }}" name="label_action2_{{ ($loop->index+1) }}" class='form-control'>
																				<option value="">選択してください</option>
																				@foreach($list_scenario as $scenario)
																					@if( $lines->value2 == $scenario[0] )
																					<option value="{{ $scenario[0] }}" selected>シナリオID：{{ $scenario[1] }}</option>
																					@else
																					<option value="{{ $scenario[0] }}">シナリオID：{{ $scenario[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@else
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action2_{{ ($loop->index+1) }}" name="label_action2_{{ ($loop->index+1) }}" value="{{ $lines->value2 }}" class="form-control" placeholder="">
																			@endif
																		</div>
																	</div>
																</div>
															</td>
														</tr>

														<tr style="text-align:center;">
															<td style='padding:15px 5px 5px 5px;'>
																A.3
															</td>
															<td>
																<div style="padding-top:10px;width:27%;float:left;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;">アクションタイプ</div>
																	<select name="action3_{{ ($loop->index+1) }}" class="action{{ ($loop->index+1) }} form-control">
																		@foreach(config('const.list_choices_action_type') as $act_index => $type_name)
																			@if( $lines->act3 == $act_index )
																				<option value="{{ $act_index }}" selected>{{ $type_name }}</option>
																			@else
																				<option value="{{ $act_index }}">{{ $type_name }}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div id="slt_action3_{{ ($loop->index+1) }}" style="padding-top:10px;width:73%;float:left;">
																	<div style="width:40%;float:left;">
																		<div style="text-align:left;font-size:12px;font-weight:bold;">選択肢</div>
																		<input type="text" id="label3_{{ ($loop->index+1) }}" name="label3_{{ ($loop->index+1) }}" value="{{ $lines->label3 }}" maxlength="20" class="form-control" placeholder="スイカ">
																		<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="label_len3_{{ ($loop->index+1) }}"></span>/20文字</div>
																	</div>
																	<div style="float:left;width:60%;">
																		<div style="text-align:left;font-size:12px;" class="act_msg">
																			@if( $lines->act3 == 1 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action3_{{ ($loop->index+1) }}" name="label_action3_{{ ($loop->index+1) }}" value="{{ $lines->value3 }}" class="form-control" placeholder="">
																			@elseif( $lines->act3 == 2 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>メッセージテキスト</div>
																			@elseif( $lines->act3 == 3 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>ポストバック値</div>
																			<select id="label_action3_{{ ($loop->index+1) }}" name="label_action3_{{ ($loop->index+1) }}" class='form-control'>
																				<option value=''>選択してください</option>
																				@foreach($list_postback as $postbacks)
																					@if( $lines->value3 == $postbacks[0] )
																					<option value="{{ $postbacks[0] }}" selected>ID：{{ $postbacks[1] }}</option>
																					@else
																					<option value="{{ $postbacks[0] }}">ID：{{ $postbacks[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@elseif( $lines->act3 == 4 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>次のシナリオID</div>
																			<select id="label_action2_{{ ($loop->index+1) }}" name="label_action3_{{ ($loop->index+1) }}" class='form-control'>
																				<option value="">選択してください</option>
																				@foreach($list_scenario as $scenario)
																					@if( $lines->value3 == $scenario[0] )
																					<option value="{{ $scenario[0] }}" selected>シナリオID：{{ $scenario[1] }}</option>
																					@else
																					<option value="{{ $scenario[0] }}">シナリオID：{{ $scenario[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@else
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action3_{{ ($loop->index+1) }}" name="label_action3_{{ ($loop->index+1) }}" value="{{ $lines->value3 }}" class="form-control" placeholder="">
																			@endif
																		</div>
																	</div>
																</div>
															</td>
														</tr>

														<tr style="text-align:center;">
															<td style='padding:15px 5px 5px 5px;'>
																A.4
															</td>
															<td>
																<div style="padding-top:10px;width:27%;float:left;">
																	<div style="text-align:left;font-size:12px;font-weight:bold;">アクションタイプ</div>
																	<select name="action4_{{ ($loop->index+1) }}" class="action{{ ($loop->index+1) }} form-control">
																		@foreach(config('const.list_choices_action_type') as $act_index => $type_name)
																			@if( $lines->act4 == $act_index )
																				<option value="{{ $act_index }}" selected>{{ $type_name }}</option>
																			@else
																				<option value="{{ $act_index }}">{{ $type_name }}</option>
																			@endif
																		@endforeach
																	</select>
																</div>
																<div id="slt_action4_{{ ($loop->index+1) }}" style="padding-top:10px;width:73%;float:left;">
																	<div style="width:40%;float:left;">
																		<div style="text-align:left;font-size:12px;font-weight:bold;">選択肢</div>
																		<input type="text" id="label4_{{ ($loop->index+1) }}" name="label4_{{ ($loop->index+1) }}" value="{{ $lines->label4 }}" maxlength="20" class="form-control" placeholder="スイカ">
																		<div style="text-align:right;font:bold 7px/70% 'メイリオ',sans-serif;color:darkgray;margin-top:3px;"><span id="label_len4_{{ ($loop->index+1) }}"></span>/20文字</div>
																	</div>
																	<div style="float:left;width:60%;">
																		<div style="text-align:left;font-size:12px;" class="act_msg">
																			@if( $lines->act4 == 1 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action4_{{ ($loop->index+1) }}" name="label_action4_{{ ($loop->index+1) }}" value="{{ $lines->value4 }}" class="form-control" placeholder="">
																			@elseif( $lines->act4 == 2 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>メッセージテキスト</div>
																			@elseif( $lines->act4 == 3 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>ポストバック値</div>
																			<select id="label_action4_{{ ($loop->index+1) }}" name="label_action4_{{ ($loop->index+1) }}" class='form-control'>
																				<option value=''>選択してください</option>
																				@foreach($list_postback as $postbacks)
																					@if( $lines->value4 == $postbacks[0] )
																					<option value="{{ $postbacks[0] }}" selected>ID：{{ $postbacks[1] }}</option>
																					@else
																					<option value="{{ $postbacks[0] }}">ID：{{ $postbacks[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@elseif( $lines->act4 == 4 )
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>次のシナリオID</div>
																			<select id="label_action4_{{ ($loop->index+1) }}" name="label_action4_{{ ($loop->index+1) }}" class='form-control'>
																				<option value="">選択してください</option>
																				@foreach($list_scenario as $scenario)
																					@if( $lines->value4 == $scenario[0] )
																					<option value="{{ $scenario[0] }}" selected>シナリオID：{{ $scenario[1] }}</option>
																					@else
																					<option value="{{ $scenario[0] }}">シナリオID：{{ $scenario[1] }}</option>
																					@endif
																				@endforeach
																			</select>
																			@else
																			<div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div>
																			<input type="text" id="label_action4_{{ ($loop->index+1) }}" name="label_action4_{{ ($loop->index+1) }}" value="{{ $lines->value4 }}" class="form-control" placeholder="">
																			@endif
																		</div>
																	</div>
																</div>
															</td>
														</tr>

													</table>
													<input type="hidden" name="logs_edit_id{{ ($loop->index+1) }}" value="{{ $lines->id }}">
													</form>
													</td>
												</tr>
											@endforeach
										@endif
									</table>
									<br />
								<form id="formCarouselSetting" class="form-horizontal" method="POST" action="/admin/member/line/setting/4choices/save/{{ $channel_id }}/send">
									{{ csrf_field() }}
									<button type="submit" id="save_btn" class="btn btn-primary push_save">下書き保存</button>
									<button type="submit" id="push_btn" class="btn btn-primary push_save">予約設定更新</button>
									<button type="submit" id="add_img_form" class="btn btn-primary">追加</button>
									<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
									<input type='hidden' name='tab' value="">
									<input type='hidden' name='send_status' value="0">
									<input type='hidden' name='send_type' value="1">
									<input type='hidden' name='channel_id' value='{{ $channel_id }}'>
									<input type='hidden' name='edit_id' value="{{ $edit_id }}">
									<input type="hidden" name="reserve_date" value="">
									<input type="hidden" name="push_title" value="">
									<input type="hidden" name="img_ratio" value="">
									<input type="hidden" name="img_size" value="">
									<input type="hidden" name="text" value="">
									<input type="hidden" name="act1" value="">
									<input type="hidden" name="act2" value="">
									<input type="hidden" name="act3" value="">
									<input type="hidden" name="act4" value="">
									<input type="hidden" name="label1" value="">
									<input type="hidden" name="label2" value="">
									<input type="hidden" name="label3" value="">
									<input type="hidden" name="label4" value="">
									<input type="hidden" name="value1" value="">
									<input type="hidden" name="value2" value="">
									<input type="hidden" name="value3" value="">
									<input type="hidden" name="value4" value="">
									<input type="hidden" name="logs_ids" value="">
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
								<div class="slide-box">
									<div style="width:100%;height:200px;background:white;"></div>
									<div style="padding:5px;text-align:left;font-size:13px;font-weight:bold;">{{ $lines[1] }}</div>
									<div style="height:100%;padding:0 5px 10px 5px;">{{ $lines[4] }}</div>
									<div style="height:100%;padding:0 5px 10px 5px;">{{ $lines[7] }}</div>
								</div>
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
	var idNo = {{ count($db_data) }};

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
			var id = $(this).attr('id').replace(/formLine/,"");
			if( $("#drop"+id).length > 0 ){
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

		//表示されているカラムを非表示
		$("[class^=cloumn]").each(function(index,elem){
			var tr_class = $(this).attr('class');
			var tab_id = $(this).attr('class').replace(/cloumn/, "");
			$('.'+tr_class).css('display', 'none');
			$("#"+tab_id).removeClass('active');
		});
		$('#tab-menu').append("<li class='active type"+idNo+"' id='"+idNo+"'>シナリオ"+idNo+"</li>");
		$('#message_form').append("<tr style='text-align:center;border:1px solid darkgray;' class='cloumn"+idNo+"'><td style='padding:15px;'><form id='formLine"+idNo+"' class='form-horizontal' method='POST' action='/admin/member/line/setting/carousel/img/{{ $channel_id }}/upload'><table width='100%'><tr style='text-align:left;'><td colspan='2'><div style='float:left;text-align:left;width:100%;'><div style='text-align:left;font-size:12px;font-weight:bold;'><b>Q．シナリオメッセージ</b></div><input type='text' id='msg"+idNo+"' name='msg"+idNo+"' maxlength='60' class='form-control' placeholder='イチゴとスイカどちらが好きですか？'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='msg_len"+idNo+"'>0</span>/240文字</div></div></td></tr><tr style='text-align:center;'><td style='padding:15px 5px 5px 5px;'>A.1</td><td><div style='padding-top:10px;width:27%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションタイプ</div><select name='action1_"+idNo+"' class='form-control'><option value='1'>webページへのリンク</option><option value='3'>ポストバック</option><option value='4'>次のシナリオ</option></select></div><div id='slt_action1_"+idNo+"' style='padding-top:10px;width:73%;float:left;'><div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+idNo+"' name='label1_"+idNo+"' maxlength='20' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+idNo+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div><input type='text' id='label_action1_"+idNo+"' name='label_action1_"+idNo+"' value='' class='form-control' placeholder=''></div></div></div></td></tr><tr style='text-align:center;'><td style='padding:15px 5px 5px 5px;'>A.2</td><td><div style='padding-top:10px;width:27%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションタイプ</div><select name='action2_"+idNo+"' class='form-control'><option value='1'>webページへのリンク</option><option value='3'>ポストバック</option><option value='4'>次のシナリオ</option></select></div><div id='slt_action2_"+idNo+"' style='padding-top:10px;width:73%;float:left;'><div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+idNo+"' name='label2_"+idNo+"' maxlength='20' value='' class='form-control' placeholder='スイカ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+idNo+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div><input type='text' id='label_action2_"+idNo+"' name='label_action2_"+idNo+"' value='' class='form-control' placeholder=''></div></div></div></td></tr><tr style='text-align:center;'><td style='padding:15px 5px 5px 5px;'>A.3</td><td><div style='padding-top:10px;width:27%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションタイプ</div><select name='action3_"+idNo+"' class='form-control'><option value='1'>webページへのリンク</option><option value='3'>ポストバック</option><option value='4'>次のシナリオ</option></select></div><div id='slt_action3_"+idNo+"' style='padding-top:10px;width:73%;float:left;'><div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+idNo+"' name='label3_"+idNo+"' maxlength='20' value='' class='form-control' placeholder='スイカ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+idNo+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div><input type='text' id='label_action3_"+idNo+"' name='label_action3_"+idNo+"' value='' class='form-control' placeholder=''></div></div></div></td></tr><tr style='text-align:center;'><td style='padding:15px 5px 5px 5px;'>A.4</td><td><div style='padding-top:10px;width:27%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>アクションタイプ</div><select name='action4_"+idNo+"' class='form-control'><option value='1'>webページへのリンク</option><option value='3'>ポストバック</option><option value='4'>次のシナリオ</option></select></div><div id='slt_action4_"+idNo+"' style='padding-top:10px;width:73%;float:left;'><div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+idNo+"' name='label4_"+idNo+"' maxlength='20' value='' class='form-control' placeholder='スイカ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+idNo+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>リンクURL</div><input type='text' id='label_action4_"+idNo+"' name='label_action4_"+idNo+"' value='' class='form-control' placeholder=''></div></div></div></td></tr></table><input type='hidden' name='logs_edit_id"+idNo+"' value=''></form></td></tr>");

		//シナリオメッセージの残り入力文字数の表示
		$('[id^=msg]').keyup(function(e){
			var column_text = $('#'+this.id).val();
			var str_len = column_text.length;
			$('#msg_len'+this.id.replace(/msg/, '')).html(str_len);
		});

		//選択肢の残り入力文字数の表示
		$('[id^=label]').keyup(function(e){
			var label = $('#'+this.id).val();
			var str_len = label.length;
			$('#label_len'+this.id.replace(/label/, '')).html(str_len);
		});

		//カラム名を変更して表示
		$('#click_cloumn').text($('#'+idNo).text());
		$('#click_cloumn').removeClass();
		$('#click_cloumn').addClass('click_cloumn'+idNo);

		//タブ切り替え
		$('#tab-menu li').on('click', function(){
			var tab_id = $(this).attr("id");
//			$('[name="send_type"]').val($(this).attr('class').replace(/type/, '').replace(/\s*active/, ''));

			//カラム名を変更して表示
			$('#click_cloumn').text($('#'+this.id).text());

			//編集したIDがわかるようにIDをパラメータに設定
			$('[name="tab"]').val($(this).attr("id"));

			var type = $(this).attr("class").replace(/(^.*type)/, "");
			$('[name="type"]').val(type);

			if($(this).not('active')){
				// タブメニュー
				$(this).addClass('active').siblings('li').removeClass('active');
			}

			$('#click_cloumn').removeClass();
			$("[class^=cloumn]").each(function(index,elem){
				var tr_class = $(this).attr('class');
				var match_tab_id = $(this).attr('class').replace(/cloumn/, "");
				if( tab_id == match_tab_id ){
					$('table tr[class='+tr_class+']').css('display', 'block');
					$('#click_cloumn').addClass('click_cloumn'+tab_id);
				}else{
					$('table tr[class='+tr_class+']').css('display', 'none');
				}
			});
		});

		$('.del_msg').click(function(event){
			event.preventDefault();
			var id = this.id.replace(/delmsg/, "");
			Ajax("/admin/member/line/setting/4choices/img/{{ $channel_id }}/delete");
			return false;
		});

		$('[name^=action]').change(function(){
			var value = $(this).val();
			var id = $(this).attr('name').replace(/action/, "");
			$('#slt_action'+id).empty();

			var msg = "";
			var example = "";
			if( value == 1 ){
				msg = "リンクURL";
				example = "イチゴ";
				$('#slt_action'+id).append("<div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+id+"' name='label"+id+"' value='' maxlength='20' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><input type='text' id='label_action"+id+"' name='label_action"+id+"' value='' class='form-control' placeholder=''></div></div>");
			}else if( value == 2 ){
				msg = "メッセージテキスト";
				example = "任意のテキストを入力してください";
			}else if( value == 3 ){
				msg = "ポストバック選択";
				example = "受け取りたい値を入力してください";
				$('#slt_action'+id).append("<div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+id+"' name='label"+id+"' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><select id='label_action"+id+"' name='label_action"+id+"' class='form-control'><option value=''>使用する管理名を選択してください</option>{!! $postback_options !!}</select></div></div>");
			}else if( value == 4 ){
				msg = "次のシナリオID";
				example = "次のシナリオID(半角数字)を入力してください";
				$('#slt_action'+id).append("<div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+id+"' name='label"+id+"' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><select id='label_action"+id+"' name='label_action"+id+"' class='form-control'><option value=''>選択してください</option>{!! $scenario_options !!}</select></div></div>");
			}
			$('#label_action'+id+' input[name=label_action'+id+']').attr('placeholder', example);
		});

		return false;
	});

	$('.del_msg').click(function(event){
		event.preventDefault();
		var id = this.id.replace(/delmsg/, "");
		Ajax("/admin/member/line/setting/4choices/img/{{ $channel_id }}/delete");
		return false;
	});

	$('.push_save, .push_send').click(function(){
		var active_id = $('#click_cloumn').attr('class').replace(/click_cloumn(\d+)/, "$1");
//alert(this.id+"<>"+active_id);
		var stop_flg = true;
		var logs_ids = '';
		var click_id = this.id
		var act_count = 0;

		//それぞれのカラムからデータ取得
		$('[id=formLine'+active_id+']').each(function(){
			act_count++;

			var id = $(this).attr('id').replace(/formLine/,"");
			var img = $("#"+this.id+" img").attr("src");
			var msg_id = $(this).attr('id').replace(/formLine/,"").replace(/(\d+)_\d+/, "$1");
			var msg = $('#formLine'+id+' input[name^=msg'+id+']').val();
			if( msg == '' ){
				swal("シナリオメッセージは必須です");
				stop_flg = false;
				return false;
			}
			var action1 = $('#formLine'+id+' [name^=action1_'+id+'] option:selected').val();
			var label1 = $('#formLine'+id+' input[name^=label1_'+id+']').val();
			var label_action1 = $('#formLine'+id+' input[name^=label_action1_'+id+']').val();
			if( label_action1 == undefined ){
				label_action1 = $('#formLine'+id+' [name^=label_action1_'+id+'] option:selected').val();
				if( click_id == 'push_btn' && label_action1 == '' ){
					act_count++;
				}
			}

			var action2 = $('#formLine'+id+' [name^=action2_'+id+'] option:selected').val();
			var label2 = $('#formLine'+id+' input[name^=label2_'+id+']').val();
			var label_action2 = $('#formLine'+id+' input[name^=label_action2_'+id+']').val();
			if( label_action2 == undefined ){
				label_action2 = $('#formLine'+id+' [name^=label_action2_'+id+'] option:selected').val();
				if( click_id == 'push_btn' && label_action2 == '' ){
					act_count++;
				}
			}

			var action3 = $('#formLine'+id+' [name^=action3_'+id+'] option:selected').val();
			var label3 = $('#formLine'+id+' input[name^=label3_'+id+']').val();
			var label_action3 = $('#formLine'+id+' input[name^=label_action3_'+id+']').val();
			if( label_action3 == undefined ){
				label_action3 = $('#formLine'+id+' [name^=label_action3_'+id+'] option:selected').val();
				if( click_id == 'push_btn' && label_action3 == '' ){
					act_count++;
				}
			}

			var action4 = $('#formLine'+id+' [name^=action4_'+id+'] option:selected').val();
			var label4 = $('#formLine'+id+' input[name^=label4_'+id+']').val();
			var label_action4 = $('#formLine'+id+' input[name^=label_action4_'+id+']').val();
			if( label_action4 == undefined ){
				label_action4 = $('#formLine'+id+' [name^=label_action4_'+id+'] option:selected').val();
				if( click_id == 'push_btn' && label_action4 == '' ){
					act_count++;
				}
			}

			var tmp_logs_ids = $('#formLine'+id+' input[name^=logs_edit_id'+id+']').val();
//alert(id+"<>"+msg_id+"<>"+msg+"<>"+action1+"<>"+label1+"<>"+label_action1+"<>"+action2+"<>"+label2+"<>"+label_action2+"<>"+tmp_logs_ids);
			if( msg != undefined ){
				$('#formCarouselSetting [name=text]').val(msg);
			}
			if( action1 != undefined ){
				$('#formCarouselSetting [name=act1]').val(action1);
			}
			if( label1 != undefined ){
				$('#formCarouselSetting [name=label1]').val(label1);
			}
			if( label_action1 != undefined ){
				$('#formCarouselSetting [name=value1]').val(label_action1);
			}
			if( action2 != undefined ){
				$('#formCarouselSetting [name=act2]').val(action2);
			}
			if( label2 != undefined ){
				$('#formCarouselSetting [name=label2]').val(label2);
			}
			if( label_action2 != undefined ){
				$('#formCarouselSetting [name=value2]').val(label_action2);
			}
			if( action3 != undefined ){
				$('#formCarouselSetting [name=act3]').val(action3);
			}
			if( label3 != undefined ){
				$('#formCarouselSetting [name=label3]').val(label3);
			}
			if( label_action3 != undefined ){
				$('#formCarouselSetting [name=value3]').val(label_action3);
			}
			if( action4 != undefined ){
				$('#formCarouselSetting [name=act4]').val(action4);
			}
			if( label4 != undefined ){
				$('#formCarouselSetting [name=label4]').val(label4);
			}
			if( label_action4 != undefined ){
				$('#formCarouselSetting [name=value4]').val(label_action4);
			}
			if( tmp_logs_ids != '' ){
				$('#formCarouselSetting [name=logs_ids]').val(tmp_logs_ids);
			}
			if( act_count == 0 ){
				stop_flg = false;
				return false;
			}
			act_count = 0;
		});

		if( !stop_flg ){
			swal("ポストバックまたはシナリオを最低１つ選択しないと配信できません");
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

		var img_size = $('[name=tmp_img_size] option:selected').val();
		if( img_size != undefined ){
			$('#formCarouselSetting [name=img_size]').val(img_size);
		}

		var img_ratio = $('[name=tmp_img_ratio] option:selected').val();
		if( img_ratio != undefined ){
			$('#formCarouselSetting [name=img_ratio]').val(img_ratio);
		}

		if( this.id == 'save_btn' ){
			$('#formCarouselSetting [name=send_status]').val('99');
		}else{
			
		}

		//更新ボタン押下後のダイアログ確認メッセージ
		//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
		submitAlert('formCarouselSetting', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.dialog_save_end_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true, false, true, '{{ $save_redirect_url }}');
	});

	//デフォルトのカラムを表示
	$("[class^=cloumn]").each(function(index,elem){
		var tr_class = $(this).attr('class');

		if( index == 0 ){
			$('.disp_cloumn').css('display','block');
			$('tr .'+tr_class).css('display', 'block');

			//デフォルトのカラム名を表示
			$('#click_cloumn').text($('#1').text());
			$('#click_cloumn').addClass('click_cloumn1');

			//カラムタイトルの残り入力文字数の表示
			var column_text = $('#msg1').val();

			var str_len = column_text.length;
			$('#msg_len1').html(str_len);

			//カラムタイトルの残り入力文字数の表示
			var label = $('#label1_1').val();
			var str_len = label.length;
			$('#label_len1_1').html(str_len);

			var label = $('#label2_1').val();
			var str_len = label.length;
			$('#label_len2_1').html(str_len);

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

		var type = $(this).attr("class").replace(/(^.*type)/, "");
		$('[name="type"]').val(type);

		if($(this).not('active')){
			// タブメニュー
			$(this).addClass('active').siblings('li').removeClass('active');
		}

		$('#click_cloumn').removeClass();

		$("[class^=cloumn]").each(function(index,elem){
			var tr_class = $(this).attr('class');
			var match_tab_id = $(this).attr('class').replace(/cloumn/, "");
//alert(tr_class+"<>"+match_tab_id);
			if( tab_id == match_tab_id ){
				$('.'+tr_class).css('display', 'block');

				//カラムタイトルの残り入力文字数の表示
				var column_text = $('#msg'+tab_id).val();
				var str_len = column_text.length;
				$('#msg_len'+tab_id).html(str_len);

				//カラムタイトルの残り入力文字数の表示
				var label = $('#label1_'+tab_id).val();
				var str_len = label.length;
				$('#label_len1_'+tab_id).html(str_len);

				var label = $('#label2_'+tab_id).val();
				var str_len = label.length;
				$('#label_len2_'+tab_id).html(str_len);

				$('#click_cloumn').addClass('click_cloumn'+tab_id);
			}else{
				$('.'+tr_class).css('display', 'none');
			}
		});
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
		var name = $(this).val();
		var id = $(this).attr('name').replace(/action/, "");
		$('#slt_action'+id).empty();
//alert(name+"<>"+id+"<>");
		var msg = "";
		var example = "";
		if( name == 1 ){
			msg = "リンクURL";
			example = "例）https://yahoo.co.jp";
			$('#slt_action'+id).append("<div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+id+"' name='label"+id+"' value='' maxlength='20' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><input type='text' id='label_action"+id+"' name='label_action"+id+"' value='' class='form-control' placeholder=''></div></div>");
		}else if( name == 2 ){
			msg = "メッセージテキスト";
			example = "任意のテキストを入力してください";
		}else if( name == 3 ){
			msg = "ポストバック選択";
			example = "次のシナリオのパラメータを入力してください(例：)";
			$('#slt_action'+id).append("<div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+id+"' name='label"+id+"' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><select id='label_action"+id+"' name='label_action"+id+"' class='form-control'><option value=''>使用する管理名を選択してください</option>{!! $postback_options !!}</select></div></div>");
		}else if( name == 4 ){
			msg = "次のシナリオID";
			example = "次のシナリオID(半角数字)を入力してください";
			$('#slt_action'+id).append("<div style='width:40%;float:left;'><div style='text-align:left;font-size:12px;font-weight:bold;'>選択肢</div><input type='text' id='label"+id+"' name='label"+id+"' value='' class='form-control' placeholder='イチゴ'><div style='text-align:right;font:bold 7px/70% メイリオ,sans-serif;color:darkgray;margin-top:3px;'><span id='label_len"+id+"'>0</span>/20文字</div></div><div style='float:left;width:60%;'><div style='text-align:left;font-size:12px;' class='act_msg'><div style='text-align:left;font-size:12px;font-weight:bold;'>"+msg+"</div><select id='label_action"+id+"' name='label_action"+id+"' class='form-control'><option value=''>選択してください</option>{!! $scenario_options!!}</select></div></div>");
		}
//		$('#label_action'+id+' input[name=label_action'+id+']').attr('placeholder', example);
	});
});


</script>

@endsection

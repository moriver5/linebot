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
    <title>メルマガ運営管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	<link href="{{ asset('css/admin/jquery.datetimepicker.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<!-- jQuery Liblary -->
	<script src="{{ asset('js/admin/jquery.datetimepicker.full.min.js') }}"></script>

</head>
<body>
<br />
<center>
<div class="container" style="width:100%;">
    <div class="col">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>検索設定</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;"><b>close</b></span>
				</div>
                <div class="panel-body">
                    <form id="formSearchSetting" class="form-horizontal" method="POST">
						{{ csrf_field() }}
						<center>

							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="97%">
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;width:110px;">検索項目</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索タイプ -->
												<select name="search_type" class="form-control">
												@foreach($client_search_item as $item => $disp_name)
													@if( !empty($session['search_type']) && $item == $session['search_type'] )
														<option value="{{ $item }}" selected>{{ $disp_name }}</option>
													@else
														<option value="{{ $item }}">{{ $disp_name }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="padding:5px;">											
												<!-- 検索タイプの値 -->
												@if( !empty($session['search_item']) )
													<input type="text" name="search_item" value="{{$session['search_item']}}" size="20" placeholder="コンマ(,)で複数設定可能" class="form-control">
												@else
													<input type="text" name="search_item" value="" size="20" placeholder="コンマ(,)で複数設定可能" class="form-control">
												@endif
											</td>
											<td style="width:115px;padding:5px;">
												<!-- LIKE検索-->
												<select name="search_like_type" class="form-control">
												@foreach($search_like_type as $index => $line)
													@if( !empty($session['search_like_type']) && $index == $session['search_like_type'] )
														<option value="{{ $index }}" selected>{{ $line[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $line[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">グループ</td>
											<td colspan="3" style="padding:5px;">
									@foreach($db_group_data as $index => $lines)
										@if( $index != 0 && $index % 2 == 0 )
											<br />
										@endif
										@if( !empty($session['group_id']) && preg_match("/^(".preg_replace("/,/", "|",$session['group_id']).")$/",$lines->id) > 0 )
											&nbsp;<input type="checkbox" name="group_id" value="{{ $lines->id }}" checked>{{ $lines->name }}
										@else
											&nbsp;<input type="checkbox" name="group_id" value="{{ $lines->id }}">{{ $lines->name }}									
										@endif
									@endforeach
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">登録状態</td>
											<td colspan="3" style="padding:5px;">
												@foreach($regist_status as $index => $line)
													@if( !empty($session['reg_status']) && preg_match("/^(".preg_replace("/,/","|",$session['reg_status']).")$/",$index) > 0 )
														&nbsp;&nbsp;<input type="checkbox" name="reg_status" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;&nbsp;<input type="checkbox" name="reg_status" value="{{ $index }}">{{ $line[1] }}													
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">DM購読</td>
											<td colspan="3" style="padding:5px;">
												@foreach($dm_status as $index => $line)
													@if( isset($session['dm_status']) && $index == $session['dm_status'] )
														&nbsp;&nbsp;<input type="radio" name="dm_status" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="dm_status" value="{{ $index }}" checked>{{ $line[1] }}
													@else
														&nbsp;<input type="radio" name="dm_status" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">登録日時</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_regdate']) )
													&nbsp;&nbsp;<input id="start_regdate" type="text" name="start_regdate" value="{{$session['start_regdate']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_regdate" type="text" name="start_regdate" placeholder="開始日時">
												@endif
												@if( !empty($session['end_regdate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_regdate" type="text" name="end_regdate" value="{{$session['end_regdate']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_regdate" type="text" name="end_regdate" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">仮登録日時</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_provdate']) )
													&nbsp;&nbsp;<input id="start_provdate" type="text" name="start_provdate" value="{{$session['start_provdate']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_provdate" type="text" name="start_provdate" placeholder="開始日時">
												@endif
												@if( !empty($session['end_provdate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_provdate" type="text" name="end_provdate" value="{{$session['end_provdate']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_provdate" type="text" name="end_provdate" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">最終アクセス</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_lastdate']) )
													&nbsp;&nbsp;<input id="start_lastdate" type="text" name="start_lastdate" value="{{$session['start_lastdate']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_lastdate" type="text" name="start_lastdate" placeholder="開始日時">
												@endif
												@if( !empty($session['end_lastdate']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_lastdate" type="text" name="end_lastdate" value="{{$session['end_lastdate']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_lastdate" type="text" name="end_lastdate" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">入金回数</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_paynum']) )
													&nbsp;&nbsp;<input type="text" name="start_paynum" value="{{ $session['start_paynum'] }}">
												@else
													&nbsp;&nbsp;<input type="text" name="start_paynum">
												@endif
												@if( !empty($session['end_paynum']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_paynum" value="{{ $session['end_paynum'] }}">&nbsp;回
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_paynum">&nbsp;回
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">入金金額</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_payamount']) )
													&nbsp;&nbsp;<input type="text" name="start_payamount" value="{{ $session['start_payamount'] }}">
												@else
													&nbsp;&nbsp;<input type="text" name="start_payamount">
												@endif
												@if( !empty($session['end_payamount']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_payamount" value="{{ $session['end_payamount'] }}">&nbsp;円
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_payamount">&nbsp;円
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">アクション回数</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_actnum']) )
													&nbsp;&nbsp;<input type="text" name="start_actnum" value="{{ $session['start_actnum'] }}">
												@else
													&nbsp;&nbsp;<input type="text" name="start_actnum">
												@endif
												@if( !empty($session['end_actnum']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_actnum" value="{{ $session['end_actnum'] }}">&nbsp;回
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_actnum">&nbsp;回
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">POINT</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['start_pt']) )
													&nbsp;&nbsp;<input type="text" name="start_pt" value="{{ $session['start_pt'] }}">
												@else
													&nbsp;&nbsp;<input type="text" name="start_pt">
												@endif
												@if( !empty($session['end_pt']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_pt" value="{{ $session['end_pt'] }}">&nbsp;pt
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input type="text" name="end_pt">&nbsp;pt
												@endif
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">表示件数</td>
											<td colspan="3" style="padding:5px;">
												&nbsp;&nbsp;<select name="search_disp_num" style="padding:3px;">
												@foreach($search_disp_num as $index => $num)
													@if( isset($session['search_disp_num']) && $session['search_disp_num'] == $index )
														<option value="{{ $index }}" selected>{{ $num }}</option>
													@elseif( $index == 0 )
														<option value="{{ $index }}" selected>{{ $num }}</option>													
													@else
														<option value="{{ $index }}">{{ $num }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">ソート</td>
											<td colspan="3" style="padding:5px;">
												@foreach($sort_list as $index => $line)
													@if( isset($session['sort']) && $index == $session['sort'] )
														&nbsp;&nbsp;<input type="radio" name="sort" value="{{ $index }}" checked>{{ $line[1] }}
													@elseif( $index == 0 )
														&nbsp;&nbsp;<input type="radio" name="sort" value="{{ $index }}" checked>{{ $line[1] }}													
													@else
														&nbsp;<input type="radio" name="sort" value="{{ $index }}">{{ $line[1] }}
													@endif
												@endforeach
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="search_setting">検索</button>
							</div>
						</center>
					</form>
                </div>
            </div>
        </div>
    </div>
</div>
</center>

<script type="text/javascript">
$(document).ready(function(){
	$('[name=search_item]').focusin(function(){
		$('[name=search_item]').attr("placeholder","");
	});

	$('[name=search_item]').focusout(function(){
		$('[name=search_item]').attr("placeholder","コンマ(,)で複数設定可能");
	});
	
	$('[name=start_regdate]').focusin(function(){
		$('[name=start_regdate]').attr("placeholder","");
	});

	$('[name=start_regdate]').focusout(function(){
		$('[name=start_regdate]').attr("placeholder","開始日時");
	});
	
	$('[name=end_regdate]').focusin(function(){
		$('[name=end_regdate]').attr("placeholder","");
	});

	$('[name=end_regdate]').focusout(function(){
		$('[name=end_regdate]').attr("placeholder","終了日時");
	});
	
	$('[name=start_provdate]').focusin(function(){
		$('[name=start_provdate]').attr("placeholder","");
	});

	$('[name=start_provdate]').focusout(function(){
		$('[name=start_provdate]').attr("placeholder","開始日時");
	});
	
	$('[name=end_provdate]').focusin(function(){
		$('[name=end_provdate]').attr("placeholder","");
	});

	$('[name=end_provdate]').focusout(function(){
		$('[name=end_provdate]').attr("placeholder","終了日時");
	});
	
	$('[name=start_lastdate]').focusin(function(){
		$('[name=start_lastdate]').attr("placeholder","");
	});

	$('[name=start_lastdate]').focusout(function(){
		$('[name=start_lastdate]').attr("placeholder","開始日時");
	});
	
	$('[name=end_lastdate]').focusin(function(){
		$('[name=end_lastdate]').attr("placeholder","");
	});

	$('[name=end_lastdate]').focusout(function(){
		$('[name=end_lastdate]').attr("placeholder","終了日時");
	});
	
	$.datetimepicker.setLocale('ja');

	//登録日時-開始日
	$('#start_regdate').datetimepicker();

	//登録日時-終了日
	$('#end_regdate').datetimepicker();
	
	//仮登録日時-開始日
	$('#start_provdate').datetimepicker();

	//仮登録日時-終了日
	$('#end_provdate').datetimepicker();

	//最終アクセス日時-開始日
	$('#start_lastdate').datetimepicker();

	//最終アクセス日時-終了日
	$('#end_lastdate').datetimepicker();

	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
		return false;
	});
	
	/*
	 * 親ウィンドウ側のフォーム値を設定し検索を行う
	 */
	//検索ボタン押下
	$('#search_setting').on('click', function(){	
		//親ウィンドウのフォームオブジェクトを取得
		var fm = window.opener.document.formStatusSearch;

		//検索項目
		fm.search_type.value = $('[name="search_type"]').val();

		//検索項目の値
		fm.search_item.value = $('[name="search_item"]').val();

		//LIKE検索項目
		fm.search_like_type.value = $('[name="search_like_type"]').val();

		//グループ
        fm.group_id.value = $('[name="group_id"]:checked').map(function(){
            return $(this).val();
        }).get();

		//登録状態
        fm.reg_status.value = $('[name="reg_status"]:checked').map(function(){
            return $(this).val();
        }).get();

		//DM購読
		fm.dm_status.value = $('[name="dm_status"]:checked').val();

		//登録日時-開始
		fm.start_regdate.value = $('[name="start_regdate"]').val();

		//登録日時-終了
		fm.end_regdate.value = $('[name="end_regdate"]').val();

		//仮登録日時-開始
		fm.start_provdate.value = $('[name="start_provdate"]').val();

		//仮登録日時-終了
		fm.end_provdate.value = $('[name="end_provdate"]').val();

		//最終アクセス-開始
		fm.start_lastdate.value = $('[name="start_lastdate"]').val();

		//最終アクセス-終了
		fm.end_lastdate.value = $('[name="end_lastdate"]').val();

		//入金回数-開始
		fm.start_paynum.value = $('[name="start_paynum"]').val();

		//入金回数-終了
		fm.end_paynum.value = $('[name="end_paynum"]').val();

		//入金金額-開始
		fm.start_payamount.value = $('[name="start_payamount"]').val();

		//入金金額-終了
		fm.end_payamount.value = $('[name="end_payamount"]').val();

		//アクション回数-開始
		fm.start_actnum.value = $('[name="start_actnum"]').val();

		//アクション回数-終了
		fm.end_actnum.value = $('[name="end_actnum"]').val();

		//ポイント-開始
		fm.start_pt.value = $('[name="start_pt"]').val();

		//ポイント-終了
		fm.end_pt.value = $('[name="end_pt"]').val();

		//表示件数
		fm.search_disp_num.value = $('[name="search_disp_num"]').val();

		//ソート
		fm.sort.value = $('[name="sort"]:checked').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});
});
</script>

</body>
</html>

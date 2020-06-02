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
											<td style="width:130px;padding:5px;">
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
											<td style="text-align:center;background:wheat;font-weight:bold;">性別</td>
											<td colspan="3" style="padding:5px;">
												<select id="sex" class="form-control" name="sex">
													@foreach($list_sex as $index => $value)
														@if( !empty($session['reg_sex']) && $session['reg_sex'] == $index )
															<option value='{{$index}}' selected>{{$value}}</option>
														@else
															<option value='{{$index}}'>{{$value}}</option>
														@endif
													@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;">年代</td>
											<td colspan="3" style="padding:5px;">
												<select id="age" class="form-control" name="age">
													@foreach($list_age as $index => $value)
														@if( !empty($session['reg_age']) && $session['reg_age'] == $index )
															<option value='{{$index}}' selected>{{$value}}</option>
														@else
															<option value='{{$index}}'>{{$value}}</option>
														@endif
													@endforeach
												</select>
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

	$.datetimepicker.setLocale('ja');

	//登録日時-開始日
	$('#start_regdate').datetimepicker();

	//登録日時-終了日
	$('#end_regdate').datetimepicker();

	//ダブルクリックで現在時刻入力
	$("#start_regdate").dblclick(function () {
	  $('[name=start_regdate]').val(dateFormat.format(new Date(), 'yyyy/MM/dd hh:mm'));
	});

	$("#end_regdate").dblclick(function () {
	  $('[name=end_regdate]').val(dateFormat.format(new Date(), 'yyyy/MM/dd hh:mm'));
	});

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
		var fm = window.opener.document.formGroupSearch;

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

		fm.sex.value = $('[name="sex"]').val();

		fm.age.value = $('[name="age"]').val();

		//登録日時-開始
		fm.start_regdate.value = $('[name="start_regdate"]').val();

		//登録日時-終了
		fm.end_regdate.value = $('[name="end_regdate"]').val();

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

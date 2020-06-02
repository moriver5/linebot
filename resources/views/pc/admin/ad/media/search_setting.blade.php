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
    <title>LINE BOT管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin/admin.css') }}" rel="stylesheet" />
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
        <div class="col-md-8">
            <div class="panel panel-default" style="font-size:12px;">
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
											<td class="admin_search" style="width:60px;">LINEチャンネル</td>
											<td style="padding:5px;width:50px;" colspan="3">
												@foreach($list_channel as $index => $lines)
													@if( !empty($session['media_line_channel']) && array_search($lines->line_basic_id, explode(",", $session['media_line_channel'])) !== false )
														&nbsp;&nbsp;<input type="checkbox" name="line_channel" value="{{ $lines->line_basic_id }}" checked>{{ $lines->name }}</option>
													@else
														&nbsp;&nbsp;<input type="checkbox" name="line_channel" value="{{ $lines->line_basic_id }}">{{ $lines->name }}</option>													
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:60px;">広告コード</td>
											<td style="padding:5px;width:50px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($ad_search_item as $lines)
													@if( !empty($session['media_search_item']) && $lines[0] == $session['media_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:60px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['media_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['media_search_item_value'] }}" placeholder="" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" placeholder="" autofocus>
												@endif
											</td>
											<td style="width:55px;padding:5px;">
												<!-- LIKE検索-->
												<select name="search_like_type" class="form-control">
												@foreach($search_like_type as $index => $line)
													@if( !empty($session['media_search_like_type']) && $index == $session['media_search_like_type'] )
														<option value="{{ $index }}" selected>{{ $line[2] }}</option>
													@else
														<option value="{{ $index }}">{{ $line[2] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;background:wheat;font-weight:bold;width:60px;">期間</td>
											<td colspan="3" style="padding:5px;">
												@if( !empty($session['media_start_date']) )
													&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" value="{{$session['media_start_date']}}" placeholder="開始日時">
												@else
													&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" placeholder="開始日時">
												@endif
												@if( !empty($session['media_end_date']) )
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" value="{{$session['media_end_date']}}" placeholder="終了日時">
												@else
													&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_date" type="text" name="end_date" placeholder="終了日時">
												@endif
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:60px;">媒体種別</td>
											<td style="padding:5px;width:50px;" colspan="3">
												@foreach($ad_category as $index => $category)
													@if( !empty($session['media_category']) && array_search($index, explode(",", $session['media_category'])) !== false )
														&nbsp;&nbsp;<input type="checkbox" name="category" value="{{ $index }}" checked>{{ $category }}</option>
													@else
														&nbsp;&nbsp;<input type="checkbox" name="category" value="{{ $index }}">{{ $category }}</option>													
													@endif
												@endforeach
											</td>
										</tr>
									</table>
								</div>
								<button type="submit" class="btn btn-primary" id="search_setting">&nbsp;&nbsp;&nbsp;&nbsp;検索&nbsp;&nbsp;&nbsp;&nbsp;</button>
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
	$('[name=start_date]').focusin(function(){
		$('[name=start_date]').attr("placeholder","");
	});

	$('[name=start_date]').focusout(function(){
		$('[name=start_date]').attr("placeholder","開始日時");
	});
	
	$('[name=end_date]').focusin(function(){
		$('[name=end_date]').attr("placeholder","");
	});

	$('[name=end_date]').focusout(function(){
		$('[name=end_date]').attr("placeholder","終了日時");
	});

	//登録日時-開始日
	$('#start_date').datetimepicker({format:'Y/m/d',timepicker:false});

	//登録日時-終了日
	$('#end_date').datetimepicker({format:'Y/m/d',timepicker:false});

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
		var fm = window.opener.document.formSearch;

		//LINEチャンネル
		fm.line_channel.value = $('[name="line_channel"]:checked').map(function(){
			return $(this).val();
		}).get();

		//検索項目
		fm.search_item.value = $('[name="search_item"]').val();

		//検索値
		fm.search_item_value.value = $('[name="search_item_value"]').val();

		//検索LIKE
		fm.search_like_type.value = $('[name="search_like_type"]').val();

		//開始期間
		fm.start_date.value = $('[name="start_date"]').val();

		//終了機関
		fm.end_date.value = $('[name="end_date"]').val();

		//媒体種別
		fm.category.value = $('[name="category"]:checked').map(function(){
			return $(this).val();
		}).get();

		//表示
		fm.disp_type.value = $('[name="disp_type"]:checked').val();

		//アクション回数表示
		fm.action_flg.value = $('[name="action_flg"]:checked').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});

});
</script>

</body>
</html>

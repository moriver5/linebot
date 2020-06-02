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
													@if( !empty($session['ad_line_channel']) && array_search($lines->line_basic_id, explode(",", $session['ad_line_channel'])) !== false )
														&nbsp;&nbsp;<input type="checkbox" name="line_channel" value="{{ $lines->line_basic_id }}" checked>{{ $lines->name }}</option>
													@else
														&nbsp;&nbsp;<input type="checkbox" name="line_channel" value="{{ $lines->line_basic_id }}">{{ $lines->name }}</option>													
													@endif
												@endforeach
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:30px;">検索項目</td>
											<td style="padding:5px;width:50px;">
												<!-- 検索タイプ -->
												<select name="search_item" class="form-control">
												@foreach($ad_search_item as $lines)
													@if( !empty($session['ad_search_item']) && $lines[0] == $session['ad_search_item'] )
														<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
													@else
														<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>													
													@endif
												@endforeach
												</select>
											</td>
											<td style="width:100px;padding:5px;">
												<!-- 検索項目の値 -->
												@if( !empty($session['ad_search_item_value']) )
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="{{ $session['ad_search_item_value'] }}" placeholder="" autofocus>
												@else
													<input id="search_item_value" type="text" class="form-control" name="search_item_value" value="" placeholder="" autofocus>
												@endif
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:30px;">表示件数</td>
											<td style="padding:5px;width:50px;" colspan="2">
												<!-- 検索タイプ -->
												<select name="search_disp_num" class="form-control">
												@foreach($ad_search_disp_num as $num)
													@if( !empty($session['ad_search_disp_num']) && $num == $session['ad_search_disp_num'] )
														<option value="{{ $num }}" selected>{{ $num }}</option>
													@else
														<option value="{{ $num }}">{{ $num }}</option>													
													@endif
												@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<td class="admin_search" style="width:30px;">媒体種別</td>
											<td style="padding:5px;width:50px;" colspan="2">
												@foreach($ad_category as $index => $category)
													@if( !empty($session['ad_category']) && array_search($index, explode(",", $session['ad_category'])) !== false )
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

		fm.line_channel.value = $('[name="line_channel"]:checked').map(function(){
			return $(this).val();
		}).get();

		//検索項目
		fm.search_item.value = $('[name="search_item"]').val();

		//検索項目
		fm.search_item_value.value = $('[name="search_item_value"]').val();

		//
		fm.category.value = $('[name="category"]:checked').map(function(){
			return $(this).val();
		}).get();

		//有効/無効
//		fm.aggregate_flg.value = $('[name="aggregate_flg"]:checked').val();

		//ソート
		fm.search_disp_num.value = $('[name="search_disp_num"]').val();

		//親ウィンドウの検索を行う
		fm.submit();

		return false;
	});

});
</script>

</body>
</html>

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
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<br />

<div class="container" style="width:1400px;">
    <div class="col">
        <div class="col-md-14 ol-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>アクセス履歴&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
				<span class="admin_default" style="margin-left:10px;">
					全件数：{{$total }} 件
					({{$currentPage}} / {{$lastPage}}㌻)
				</span>
				<center>{{ $links }}</center>
                <div class="panel-body">
					<span style="color:red;font-size:14px;font-weight:bold;">※メルマガからのアクセスはメルマガIDが表示されています</span>
					<center>
						<div>
							<div class="form-group" style="align:center;font-size:12px;font:normal 12px/120% 'メイリオ',sans-serif;">
								<table border="0" width="100%">
									<tr style="text-align:center;background:wheat;font-weight:bold;">
										<td style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;width:110px;padding:3px;">日時</td>
										<td colspan="2" style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;width:290px;padding:3px;">ページ名</td>
										<td style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;width:55px;padding:3px;">メルマガID</td>
									</tr>
									@foreach($db_data as $lines)
									<tr style="text-align:center;font-weight:bold;">
										<td style="width:115px;border-top:1px solid black;border:1px solid black; solid black;padding:5px;">
											{{ $lines->created_at }}
										</td>
										<td style="text-align:left;width:190px;border-top:1px solid black;border:1px solid black;padding:5px;">
											@if( preg_match("/<>/", $lines->page) > 0 )
											{{ preg_replace("/^(.*?)<>(.*)$/", "$1", $lines->page) }}
											@endif
										</td>
										<td style="text-align:left;width:250px;border-top:1px solid black;border:1px solid black;padding:5px;">
											{{ preg_replace("/^(.*?)<>(.*)$/", "$2", $lines->page) }}
										</td>
										<td style="width:20px;text-align:center;border:1px solid black;">
											{{ $lines->melmaga_id }}
										</td>
									</tr>
									@endforeach
								</table>
							</div>
						</div>
					</center>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.close();
	});
	
});
</script>

</body>
</html>

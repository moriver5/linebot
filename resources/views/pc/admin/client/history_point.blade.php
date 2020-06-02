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

<div class="container" style="width:730px;">
    <div class="col">
        <div class="col-md-10 ol-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>ポイント履歴&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue">顧客ID【 {{ $id }} 】</font></b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
				<span class="admin_default" style="margin-left:10px;">
					全件数：{{$total }} 件
					({{$currentPage}} / {{$lastPage}}㌻)
				</span>
				<center>{{ $links }}</center>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;font-size:12px;font:normal 12px/120% 'メイリオ',sans-serif;">
								<table border="0" width="100%">
									<tr style="text-align:center;background:wheat;font-weight:bold;">
										<td style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;width:20px;padding:3px;">日時</td>
										<td colspan="3" style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;padding:3px;">ポイント履歴</td>
										<td style="border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;padding:3px;"></td>
									</tr>
									@foreach($db_data as $lines)
									<tr style="text-align:center;font-weight:bold;">
										<td style="width:160px;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-left:1px solid black;padding:5px;">
											{{ $lines->created_at }}
										</td>
										<td style="width:30px;padding-left:50px;text-align:left;border-bottom:1px solid black;">
											{{ $lines->prev_point }}&nbsp;&nbsp;&nbsp;
										</td>
										<td style="width:50px;padding:3px;text-align:left;border-bottom:1px solid black;">
											➡&nbsp;&nbsp;&nbsp;{{ $lines->current_point }}
										</td>
										<td style="width:70pxpadding:3px;text-align:left;border-bottom:1px solid black;border-right:1px solid black;">
											@if( $lines->current_point - $lines->prev_point > 0 )
											(<font color="blue"><b>＋</b></font>{{ $lines->add_point }})
											@elseif( $lines->current_point - $lines->prev_point < 0 )
											(<font color="red"><b>－</b></font>{{ $lines->add_point }})
											@endif
										</td>
										<td style="width:110px;padding:3px;text-align:center;border-bottom:1px solid black;border-right:1px solid black;">
											@if( $lines->operator == 'user' )
												情報閲覧
											@elseif( $lines->operator == 'credit' )
												クレジット決済
											@elseif( $lines->operator == 'netbank' )
												ネットバンク決済
											@else
												<font color="deepskyblue">管理手動</font>
											@endif
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

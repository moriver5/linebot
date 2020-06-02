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
    <title>LINE BOT 代理店管理</title>

    <!-- Styles -->
    <link href="{{ asset('/css/admin/app.css') }}" rel="stylesheet" />
	<link href="{{ asset('/css/admin/allow.css') }}" rel="stylesheet" />
	<link href="{{ asset('/css/admin/admin.css') }}" rel="stylesheet" />
	<link href="{{ asset('/css/admin/colorbox.css') }}" rel="stylesheet" />

	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


	<!-- JavaScript Library -->
	<script src="{{ asset('/js/admin/utility.js') }}"></script>

	<!-- 日付フォーマット -->
	<script type="text/javascript">
		var dateFormat = {
		  _fmt : {
			"yyyy": function(date) { return date.getFullYear() + ''; },
			"MM": function(date) { return ('0' + (date.getMonth() + 1)).slice(-2); },
			"dd": function(date) { return ('0' + date.getDate()).slice(-2); },
			"hh": function(date) { return ('0' + date.getHours()).slice(-2); },
			"mm": function(date) { return ('0' + date.getMinutes()).slice(-2); },
			"ss": function(date) { return ('0' + date.getSeconds()).slice(-2); }
		  },
		  _priority : ["yyyy", "MM", "dd", "hh", "mm", "ss"],
		  format: function(date, format){
			return this._priority.reduce((res, fmt) => res.replace(fmt, this._fmt[fmt](date)), format)
		  }
		};
	</script>
</head>
<body class="drawer drawer--left">

<br />
<div class="container">
    <div class="row">
<!--
        <div class="col-md-8 col-md-offset-2">
			<form id="formCreate" class="form-horizontal" method="POST" action="/agency/member/aggregate/month/{{ $basic_id }}/{{ $ad_cd }}">
			{{ csrf_field() }}
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>【{{ $agency }}】 ＞＞ {{ $ad_cd }} ＞＞ 広告コード集計</b>
					<div style="padding:3px;float:right;margin-left:10px;background:lavender;font-weight:bold;"><a href="/agency/logout" style="color:black;">ログアウト</a></div>
				</div>
                <div class="panel-body">
					<center>
						<table border="1" width="100%">
							<tr>
							<td style="font-weight:bold;padding:5px;text-align:center;background:wheat;">集計期間</td>
							<td colspan="3" style="padding:5px;border-right:0px;">
								@if( !empty($session['ad_start_date']) )
									&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" value="{{$session['ad_start_date']}}" placeholder="開始日">
								@else
									&nbsp;&nbsp;<input id="start_date" type="text" name="start_date" placeholder="開始日">
								@endif
							</td>
							<td style="padding:5px;border-left:0px;">
								<button type="submit" class="btn btn-primary" id="search_setting" style="width:100%;">集計結果</button>
							</td>
						</tr>
						</table>
					</center>
                </div>
            </div>
			</form>
        </div>
    </div>
-->
	@if( !empty($db_data) )
	<div class="panel panel-default col-md-6 col-md-offset-3">
		<div class="panel-heading">
			<b>【{{ $agency }}】 ＞＞ {{ $name }} ＞＞ {{ $ad_cd }} ＞＞ 日別集計</b>
			<div style="padding:3px;float:right;margin-left:10px;background:lavender;font-weight:bold;"><a href="/agency/logout" style="color:black;">ログアウト</a></div>
		</div>
		<div class="panel-body">
			<div style="float:right;margin-bottom:5px;">
				<a href="/agency/member" class="btn btn-primary">戻る</a>
			</div>
			<table border="1" width="100%">
				<tr>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;width:160px;">
						日付
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						アクセス
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						登録者数
					</td>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						24Hブロック者数
					</td>
				</tr>
				@foreach($db_data as $date => $lines)
				<tr>
					<td style="padding:2px;text-align:left;">
						{{ $lines['date'] }}
					</td>
					<td style="padding:2px;text-align:center;">
						@if( $lines['pv'] > 0 )
						<b>{{ $lines['pv'] }}</b>
						@else
						{{ $lines['pv'] }}
						@endif
					</td>
					<td style="padding:2px;text-align:center;">
						@if( $lines['reg'] > 0 )
						<b>{{ $lines['reg'] }}</b>
						@else
						{{ $lines['reg'] }}
						@endif
					</td>
					<td style="padding:2px;text-align:center;">
						@if( $lines['unfollow'] > 0 )
						<b>{{ $lines['unfollow'] }}</b>
						@else
						{{ $lines['unfollow'] }}
						@endif
					</td>
				</tr>
				@endforeach
				<tr>
					<td style="font-size:11px;font-weight:bold;padding:5px;text-align:center;background:wheat;">
						合計
					</td>
					<td style="padding:2px;text-align:center;background:yellow;">
						<b>{{ $list_total['pv'] }}</b>
					</td>
					<td style="padding:2px;text-align:center;background:yellow;">
						<b>{{ $list_total['reg'] }}</b>						
					</td>
					<td style="padding:2px;text-align:center;background:yellow;">
						<b>{{ $list_total['unfollow'] }}</b>
					</td>
				</tr>
			</table>
			<div style="float:right;margin-top:5px;">
				<a href="/agency/member" class="btn btn-primary">戻る</a>
			</div>
		</div>
	</div>
	@endif
	
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.ja.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('[name=start_date]').focusin(function(){
		$('[name=start_date]').attr("placeholder","");
	});

	//開始日
	$('#start_date').datepicker({
		format: 'yyyy/mm',
		language: 'ja',
		autoclose: true,
		minViewMode: 'months'
	});

	//デフォルトの開始日・終了日
	if( $('#start_date').val == '' ){
		$('#start_date').val(dateFormat.format(new Date(), 'yyyy/MM'));
	}
});
</script>
</body>
</html>
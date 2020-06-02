@extends('layouts.app')

@section('content')
<br />
<form id="formExportLog" class="form-horizontal" method="POST" action="/admin/member/client/export/opeartion/log">
	{{ csrf_field() }}
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" style="text-align:center;">
					<b>顧客データエクスポートログ</b>
				</div>
				<div class="panel-heading">
					<table border="1" width="100%">
						<tr>
							<td style="text-align:center;background:wheat;font-weight:bold;">エクスポート日時</td>
							<td style="padding:5px;">
								@if( isset($session['start_export_date']) )
									&nbsp;&nbsp;<input id="start_export_date" type="text" name="start_export_date" value="{{$session['start_export_date']}}" placeholder="開始日時">
								@else
									&nbsp;&nbsp;<input id="start_export_date" type="text" name="start_export_date" placeholder="開始日時">
								@endif
								@if( isset($session['end_export_date']) )
									&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_export_date" type="text" name="end_export_date" value="{{$session['end_export_date']}}" placeholder="終了日時">
								@else
									&nbsp;&nbsp;～&nbsp;&nbsp;<input id="end_export_date" type="text" name="end_export_date" placeholder="終了日時">
								@endif
							</td>
							<td style="text-align:center;background:wheat;font-weight:bold;">ソート</td>
							<td style="padding:5px;">
								@if( isset($session['sort']) )
									@if( $session['sort'] == 0 )
										&nbsp;&nbsp;<input id="sort" type="radio" name="sort" value="0" checked>新しい順
										&nbsp;&nbsp;<input id="sort" type="radio" name="sort" value="1">古い順
									@elseif( $session['sort'] == 1 )
										&nbsp;&nbsp;<input id="sort" type="radio" name="sort" value="0">新しい順
										&nbsp;&nbsp;<input id="sort" type="radio" name="sort" value="1" checked>古い順
									@endif
								@else
									&nbsp;&nbsp;<input id="sort" type="radio" name="sort" value="0" checked>新しい順
									&nbsp;&nbsp;<input id="sort" type="radio" name="sort" value="1">古い順
								@endif
							</td>
							<td style="padding:5px;">
								<button type="submit" class="btn btn-primary" name="submit" value="1" id="push_btn">検索</button>
							</td>
						</tr>
					</table>
				</div>
			</div>
				
            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_data) )
						{{ csrf_field() }}
						<center>
						{{ $db_data->links() }}
						<table border="1" align="center" style="width:100%;">
							<tr>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>操作者</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>エクスポート日時</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ファイル名</b>
								</td>
							</tr>
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:5px;text-align:center;">
										{{ $lines->login_id }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->created_at }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->file }}
									</td>
								</tr>
							@endforeach
						</table>
						</center>
					@endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('[name=start_export_date]').focusin(function(){
		$('[name=start_export_date]').attr("placeholder","");
	});

	$('[name=start_export_date]').focusout(function(){
		$('[name=start_export_date]').attr("placeholder","開始日時");
	});

	$('[name=end_export_date]').focusin(function(){
		$('[name=end_export_date]').attr("placeholder","");
	});

	$('[name=end_export_date]').focusout(function(){
		$('[name=end_export_date]').attr("placeholder","終了日時");
	});
	
	$.datetimepicker.setLocale('ja');

	//登録日時-開始日
	$('#start_export_date').datetimepicker();

	//登録日時-終了日
	$('#end_export_date').datetimepicker();

	//更新ボタン押下時に更新用パラメータにデータ設定
	$('#push_btn').on('click', function(){

		//条件検索ボタン押下
		$('#formExportLog').submit(function(event){
			//ajax通信(アカウント編集処理)
			$.ajax({
				url: $(this).prop('action'),
				type: method,
				data: $(this).serialize(),
				timeout: timeout,
				success:function(result_flg){
					window.location.reload();
				},
				error: function(error) {

				}
			});
			
			return false;
		});

	});

});
</script>

@endsection

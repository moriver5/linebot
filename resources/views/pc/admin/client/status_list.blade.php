@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>顧客ステータス変更の一覧</b>
				</div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
					@if( !empty($db_data) )
						{{ csrf_field() }}
						<center>
						{{ $db_data->links() }}
						<table border="1" align="center" width="100%">
							<tr>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>顧客ID</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>広告ｺｰﾄﾞ</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>PC-mail</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>ｸﾞﾙｰﾌﾟ</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>登録状態</b>
								</td>
								<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
									<b>登録日時</b>
								</td>
							</tr>
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:5px;text-align:center;">
			<!--							<a href="{{ url('/admin/member/client/edit') }}/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->id }}</a>-->
										{{ $lines->id }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->ad_cd }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->mail_address }}
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $list_group[$lines->group_id] }}
									</td>
									<td style="padding:5px;text-align:center;">
										@if( config('const.disp_regist_status')[2] == config('const.disp_regist_status')[$lines->status] )
										<b><font color="darkgray">{{ config('const.disp_regist_status')[$lines->status] }}</font></b>
										@elseif( config('const.disp_regist_status')[3] == config('const.disp_regist_status')[$lines->status] )
										<b><font color="red">{{ config('const.disp_regist_status')[$lines->status] }}</font></b>
										@else
										{{ config('const.disp_regist_status')[$lines->status] }}
										@endif
									</td>
									<td style="padding:5px;text-align:center;">
										{{ $lines->created_at }}
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

	//更新ボタン押下時に更新用パラメータにデータ設定
	$('#push_btn').on('click', function(){

		//条件検索ボタン押下
		$('#formStatusSearch').submit(function(event){
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

@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル別ユーザー一覧</b>
				</div>
				<div class="panel-body">
                    <form id="formGroupUser" class="form-horizontal" method="POST" action="/admin/member/group/search/{{ $channel_id }}/category/bulk/move/send">
						{{ csrf_field() }}
						<span class="admin_default" style="margin-left:20px;">
							全件数：{{$total }} 件
							({{$currentPage}} / {{$lastPage}}㌻)
						</span>
						<center>{{ $links }}</center>
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">										
											<td>ID</td>
											<td >ベーシックID</td>
											<td>LINEネーム</td>
											<td>フォロー</td>
											<td>無効</td>
											<td>削除</td>
										</tr>
										@foreach($db_data as $index => $lines)
										{{-- 登録済のときセル色をグレイ --}}
										@if( !empty($lines->follow_flg) )
										<tr class="slt_group" id="slt_group{{ $lines->client_id }}" style='text-align:center;background:white;'>
										{{-- ブロックのときセル色をホワイト --}}
										@else
										<tr class="slt_group" id="slt_group{{ $lines->client_id }}" style='text-align:center;background:darkgray;'>
										@endif
											<td style="width:60px;"><a href="/admin/member/client/list/{{ $currentPage }}/{{ $channel_id }}/{{ $lines->user_line_id }}">{{ $lines->id }}</a></td>
											<td style="width:170px;">{{ $lines->line_basic_id }}</td>
											<td style="width:170px;">{{ $lines->name }}</td>
											<td style="width:60px;">
												@if( !empty($lines->follow_flg) )
													友だち
												@else
													@if( $lines->block24h )
													<font color="red">24Hブロック</font>
													@else
													<font color="red">ブロック</font>
													@endif
												@endif
											</td>
											<td style="width:100px;">{{ $lines->disable }}</td>
											<td style="width:100px;"></td>
										</tr>
										@endforeach
									</table>
								</div>
							</div>
						</center>
					</form>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="formGroupUserPersonal" class="form-horizontal link_btn" method="POST" action="/admin/member/group/search/{{ $channel_id }}/category/move/send">
{{ csrf_field() }}
<input type="hidden" name="personal_client_id" value="">
<input type="hidden" name="personal_category_id" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var sub_win;
$(document).ready(function(){
/*
	//グループ管理で削除選択にチェックしたセルの色を変更
	$('.del_group').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","white");
		}
	});
<<<<<<< .mine
*/
||||||| .r2156
	
=======
*/	
>>>>>>> .r2157
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formGroupUser', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});

</script>

@endsection

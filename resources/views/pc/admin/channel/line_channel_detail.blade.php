@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル詳細－[{{ $db_data->name }}]</b>
				</div>
				<div class="panel-body">
					<div style="width:300px;padding-left:15px;">
						<table width="100%">
							<tr>
								<td>
									友だち登録者数
								</td>
								<td>
									：
								</td>
								<td>
									<a href="/admin/member/line/channel/friends/{{ $channel_id }}">{{ $db_data->count }}人</a>
								</td>
							</tr>
							<tr>
								<td>
									友だち追加回数
								</td>
								<td>
									：
								</td>
								<td>
									@if( isset($followers_data['followers']) ){{ $followers_data['followers'] }}@endif人
								</td>
							</tr>
							<tr>
								<td>
									 友だちブロック数
								</td>
								<td>
									：
								</td>
								<td>
									@if( isset($followers_data['blocks']) ){{ $followers_data['blocks'] }}@endif人
								</td>
							</tr>
							<tr>
								<td>
									メッセージ配信数
								</td>
								<td>
									：
								</td>
								<td>
									@if( isset($push_data['apiMulticast']) ){{ $push_data['apiMulticast'] }}@endif通
								</td>
							</tr>
							<tr>
								<td>
									応答メッセージ配信数
								</td>
								<td>
									：
								</td>
								<td>
									@if( isset($push_data['apiReply']) ){{ $push_data['apiReply'] }}通@endif
								</td>
							</tr>
							<tr>
								<td>
									 挨拶メッセージのLINKクリック数
								</td>
								<td>
									：
								</td>
								<td>
									{{ $click_count }}回
								</td>
							</tr>
						</table>
					</div>
					<center>
						<div>
							<div class="form-group" style="align:center;">
								@if( $type == 'setting' )
								<table border="1" width="95%">
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td colspan="2" style="padding:10px;"><a href="/admin/member/line/channel/edit/{{ $channel_id }}">チャンネル基本設定</a></td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td colspan="2" style="padding:10px;"><a href="/admin//admin/member/group/convert/setting/{{ $channel_id }}">％変換設定</a></td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td colspan="2" style="padding:10px;"><a href="/admin/member/line/setting/postback/{{ $channel_id }}">ポストバック設定</a></td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td colspan="2" style="padding:10px;"><a href="/admin/member/line/analytics/friends/{{ $channel_id }}">友だち追加集計</a></td>
									</tr>
								</table>
								@else
								<table border="1" width="95%">
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;"><a href="/admin/member/line/setting/replay/{{ $channel_id }}">自動応答設定</a></td>
										<td style="padding:10px;">
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/push/message/1/{{ $channel_id }}">予約配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/1/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/push/message/4/{{ $channel_id }}">登録後配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/4/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
								</table>
								<br>
								▼開発中▼
								<table border="1" width="95%">
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/push/message/{{ $channel_id }}">即時配信</a>&nbsp&nbsp;
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/0/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/setting/2choices/{{ $channel_id }}">２択予約配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/6/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/setting/4choices/{{ $channel_id }}">４択予約配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/7/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/setting/carousel/{{ $channel_id }}">カルーセル予約配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/5/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/setting/imagemap/{{ $channel_id }}">イメージマップ配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/8/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>

									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/push/message/2/{{ $channel_id }}">毎日配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/2/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/push/message/3/{{ $channel_id }}">毎週配信</a>
										</td>
										<td style="padding:10px;">
											<a href="/admin/member/line/reserve/status/3/{{ $channel_id }}">配信状況</a>
										</td>
									</tr>
								</table>
								@endif
							</div>
							<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
						</div>
					</center>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var sub_win;
$(document).ready(function(){
	//グループ管理で削除選択にチェックしたセルの色を変更
	$('.del_group').on('click', function(){
		//セルの色を変更
		if( $(this).is(':checked') ){
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#group" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#memo" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#group" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#memo" + this.id.replace(/del_group/,"")).css("background-color","white");
		}
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formGroup', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

	//グループ追加ボタンを押下
	$('.add_category').on('click', function(){
		sub_win = window.open('/admin/member/group/search/category/add/' + this.id, 'category_add', 'width=700, height=350');
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});
	
	//更新ボタン押下
	$('#push_update').on('click', function() {
		//グループ名に未入力があるか確認
		$('.group_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});
	});
	
	//グループ名のテキストBOXにカーソルが当たったら
	$('.group_data').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});

});
</script>

@endsection

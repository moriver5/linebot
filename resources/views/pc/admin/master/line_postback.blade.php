@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading" style="height:50px;">
					<div style="text-align:left;float:left;margin-top:5px;">
						<b>ポストバックアクション管理－[{{ $db_data->name }}]</b>
					</div>
					<div style="text-align:right;float:right;margin-top:-3px;">
						<button type="submit" id="add_btn" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;＋新規追加&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
						<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
					</div>
				</div>
				<div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;">
								<table border="1" width="100%">
									<tr class="slt_group" id="slt_group" style="text-align:center;background:wheat;font-weight:bold;">
										<td style="width:10%;padding:10px;">
											ID
										</td>
										<td style="width:50%;padding:10px;">
											管理名
										</td>
										<td style="width:22%;padding:10px;">
											ﾎﾟｽﾄﾊﾞｯｸ値
										</td>
										<td style="width:18%;padding:10px;">
											最終更新日時
										</td>
									</tr>
									@if( !empty($postback_data) )
									@foreach($postback_data as $lines)
									<tr class="slt_group" id="slt_group" style="text-align:center;">
										<td style="padding:3px;">
											<a href="/admin/member/line/setting/postback/create/{{ $channel_id }}/{{ $lines->id }}">{{ $lines->id }}</a>
										</td>
										<td style="padding:3px;">
											{{ $lines->name }}
										</td>
										<td style="padding:3px;">
											{{ $lines->postback }}
										</td>
										<td style="padding:3px;">
											{{ preg_replace("/(\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}):\d{2}/", "$1", $lines->updated_at) }}
										</td>
									</tr>
									@endforeach
									@endif
								</table>
							</div>
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

	$('#add_btn').on('click', function(){
//		window.open('/admin/member/line/setting/postback/create/{{ $channel_id }}');
		window.location = '/admin/member/line/setting/postback/create/{{ $channel_id }}';
		return false;
	});

});
</script>

@endsection

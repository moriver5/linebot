@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル一覧</b>
				</div>
				<div class="panel-body">
					@if( $total > 0 )
                    <form id="formGroup" class="form-horizontal" method="POST" action="/admin/member/group/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;width:20px;">ベーシックID</td>
											<td style="padding:0px 0px;width:60px;">チャンネル名</td>
											<td style="padding:0px 0px;width:130px;">メモ</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="slt_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td><a href="/admin/member/line/channel/detail/{{ $lines->line_basic_id }}/{{ $type }}">{{ $lines->line_basic_id }}</a></td>
											<td>{{ $lines->name }}</td>
											<td>{{ $lines->memo }}</td>
										</tr>
										@endforeach
									</table>
								</div>
							</div>
						</center>
					</form>
					@else
						<center><b>閲覧できるLINEチャンネルがありません。</b></center>
					@endif
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

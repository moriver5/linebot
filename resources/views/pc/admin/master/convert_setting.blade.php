@extends('layouts.app')

@section('content')
<br />
<div class="container" style="width:1500px;">
    <div class="col">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>{{ $group_name }}->%変換設定</b>
				</div>
				<div class="panel-body">
                    <form id="formConvert" class="form-horizontal" method="POST" action="/admin/member/group/convert/setting/send">
						{{ csrf_field() }}
						<center>
							<!-- タブの中身 -->
							<div>
								<div class="form-group" style="align:center;">
									{{ $db_data->links() }}
									<table border="1" width="95%">
										<tr style="text-align:center;background:wheat;font-weight:bold;">
											<td style="padding:1px 3px;">ID</td>
											<td style="padding:1px 0px;">変換キー</td>
											<td style="padding:1px 0px;">変更内容</td>
											<td style="padding:1px 0px;">備考</td>
											<td style="padding:1px 2px;">
												削除<br /><input type="checkbox" id="del_all" name="del_all" value="1">
											</td>
										</tr>
										@foreach($db_data as $index => $lines)
										<tr class="del slt_group" id="slt_group{{ $lines->id }}" style="text-align:center;">
											<td>{{ $lines->id }}<input type="hidden" class="del" name="id[]" value="{{ $lines->id }}"></td>
											<td><input type="text" class="del key_data" id="key{{ $lines->id }}" name="key[]" value="{{ $lines->key }}" size="30" maxlength="{{ config('const.convert_key_max_length') }}"></td>
											<td><input type="text" class="del value_data" id="value{{ $lines->id }}" name="value[]" value="{{ $lines->value }}" size="55" maxlength="{{ config('const.convert_value_max_length') }}"></td>
											<td><input type="text" class="del" id="remarks{{ $lines->id }}" name="remarks[]" value="{{ $lines->memo }}" size="27" maxlength="{{ config('const.convert_memo_max_length') }}"></td>
											<td style="text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}"></td>
										</tr>
										@endforeach
									</table>
								</div>
								<button type="submit" id="push_update" class="btn btn-primary">更新</button>
								<button type="submit" id="add_key" class="btn btn-primary">変換キー追加</button>
							</div>
						</center>
					</form>
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
			$("#key" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#value" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
			$("#remarks" + this.id.replace(/del_group/,"")).css("background-color","#F4FA58");
		//セルの色を元に戻す
		}else{
			$("#slt_group" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#key" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#value" + this.id.replace(/del_group/,"")).css("background-color","white");
			$("#remarks" + this.id.replace(/del_group/,"")).css("background-color","white");
		}
	});
	
	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formConvert', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
	
	//キー追加ボタンを押下
	$('#add_key').on('click', function(){
		sub_win = window.open('/admin/member/group/convert/setting/add/{{ $group_id }}', 'convert_table', 'width=1000, height=350');
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
		//チェックされたらセルの色を変更
		if( $(this).is(':checked') ){
			$('.del').css("background-color","#F4FA58");
		//チェックが外されたらセルの色を元に戻す
		}else{
			$('.del').css("background-color","white");			
		}
	});
	
	//更新ボタン押下
	$('#push_update').on('click', function() {
		//変換キーに未入力があるか確認
		$('.key_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});

		//変換内容に未入力があるか確認
		$('.value_data').each(function(){
			//未入力があればテキストBOXの背景色を変更
			if( $(this).val() == '' ){
				$(this).css("background-color","yellow");
			}
		});

	});
	
	//変換キーのテキストBOXにカーソルが当たったら
	$('.key_data').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});
	
	//変換内容のテキストBOXにカーソルが当たったら
	$('.value_data').on('click', function() {
		//カーソルが当たった背景色を白に変更(イエローの背景色を白に変更するのが狙い)
		$(this).css("background-color","white");
		return false;
	});

});
</script>

@endsection

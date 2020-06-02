@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-2">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>代理店一覧</b>
					<div style="text-align:right;float:right;margin:-8px 7px 0px 0px;">
					<button id="create" type="submit" class="btn btn-primary" style="float:right;">新規作成</button>
					</div>
				</div>

				<form id="formAgency" class="form-horizontal" method="POST" action="/admin/member/ad/agency/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:30px;">
								<b>代理店ID</b>
							</td>
							<td class="admin_table" style="width:220px;">
								<b>代理店名</b>
							</td>
							<td class="admin_table" style="width:10px;">
								<b>管理</b>
							</td>
							<td class="admin_table" style="width:20px;">
								削除 <input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										{{ $lines->id }}
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/ad/agency/edit') }}/{{ $currentPage }}/{{$lines->id}}">{{ $lines->name }}</a>
									</td>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/agency') }}/login/{{ $lines->login_id }}" target="_blank">管理画面</a>
									</td>
									<td style="text-align:center;"><input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}"></td>
								</tr>
							@endforeach
						@endif
					</table>
					<br />
					<center><button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;削除&nbsp;&nbsp;&nbsp;</button></center>
				</div>
				</form>
			</div>	
		</div>	
	</div>	

</div>

<!-- 画面アラートJavascript読み込み -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){

	//新規作成ボタン押下
	$('#create').on('click', function(){
//		search_win = window.open('/admin/member/ad/agency/create', 'create', 'width=540, height=375');
		window.location = '/admin/member/ad/agency/create';
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formAgency', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection

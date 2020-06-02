@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>ASP一覧</b>
					<div style="text-align:right;float:right;margin:-8px 7px 0px 0px;">
					<button id="create" type="submit" class="btn btn-primary" style="float:right;">新規作成</button>
					</div>
				</div>

				<form id="formAsp" class="form-horizontal" method="POST" action="/admin/member/ad/asp/send">
				{{ csrf_field() }}
				<div class="panel-body">
					<span class="admin_default" style="margin-left:10px;">
						全件数：{{$total }} 件
						({{$currentPage}} / {{$lastPage}}㌻)
					</span>
					<center>{{ $links }}</center>
					<table border="1" align="center" width="99%">
						<tr>
							<td class="admin_table" style="width:20px;">
								<b>ID</b>
							</td>
							<td class="admin_table" style="width:40px;">
								<b>ASP名</b>
							</td>
							<td class="admin_table" style="width:130px;">
								<b>キックバックURL</b>
							</td>
							<td class="admin_table" style="width:10px;">
								削除 <input type="checkbox" id="del_all" name="del_all" value="1">
							</td>
						</tr>
						@if( !empty($db_data) )
							@foreach($db_data as $lines)
								<tr>
									<td style="padding:2px;text-align:center;">
										<a href="{{ url('/admin/member/ad/asp/edit') }}/{{ $currentPage }}/{{$lines->id}}">{{ $lines->id }}</a>
										<input type="hidden" name="id[]" value="{{ $lines->id }}">
									</td>
									<td style="padding:2px;text-align:center;">
										{{ $lines->asp }}
									</td>
									<td style="padding:2px;text-align:left;">
										{{ $lines->kickback_url }}
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
	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		window.location = '/admin/member/ad/asp/create';
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formAsp', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.delete_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);

});
</script>

@endsection

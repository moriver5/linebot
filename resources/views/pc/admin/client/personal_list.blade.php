@extends('layouts.app')

@section('content')
<br />
<div class="container">

	<div class="col-md-9 col-md-offset-2 panel panel-default" style="font-size:12px;">
		<div class="panel-heading">
			<b>メルマガ購読リスト</b>
		</div>
		<form id="formMailDel" class="form-horizontal" method="POST" action="/admin/member/client/list/{{$page}}/{{$client_id}}/send">
			{{ csrf_field() }}
		<div class="panel-body">
			<table border="1" align="center" width="98%">
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>登録日時</b>
					</td>
					<td style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						{{ preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", "$1/$2/$3 $4:$5", $user_data->regist_date) }}
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>最終アクセス</b>
					</td>
					<td style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						{{  preg_replace("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", "$1/$2/$3 $4:$5", $user_data->last_access) }}
					</td>
				</tr>
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>顧客ID</b>
					</td>
					<td style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						<b>{{ $client_id }}</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>メルマガ配信</b>
					</td>
					<td style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						<select name="send_flg" class="form-control">
							@foreach($list_send_status as $lines)
								@if( $lines[0] == $user_data->send_flg)
									<option value="{{ $lines[0] }}" selected>{{ $lines[1] }}</option>
								@else
									<option value="{{ $lines[0] }}">{{ $lines[1] }}</option>
								@endif
							@endforeach
						</select>
					</td>
				</tr>
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>メールアドレス</b>
					</td>
					<td colspan="3" style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						<input id="email" type="text" class="form-control" name="email" value="{{ $email }}" maxlength={{ config('const.email_length') }} required autofocus>
					</td>
				</tr>
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>MEMO</b>
					</td>
					<td colspan="3" style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						<textarea rows="7" name="description" class="form-control"></textarea>
					</td>
				</tr>
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:90px;">
						<b>このアカウントを無効</b>
					</td>
					<td colspan="3" style="padding:5px;text-align:center;font-weight:bold;width:200px;">
						@if( !empty($user_data->disable) )
						<input type="checkbox" class="form-control" name="disable" value="1" checked>
						@else
						<input type="checkbox" class="form-control" name="disable" value="1">
						@endif
					</td>
				</tr>
			</table>
			<br />
			<span class="admin_default" style="margin-left:10px;">
				全件数：{{$total }} 件
				({{$currentPage}} / {{$lastPage}}㌻)
			</span>
			<center>{{ $db_data->links() }}</center>
			<table border="1" align="center" width="98%">
				<tr>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:100px;">
						<b>広告コード</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
						<b>登録状況</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;">
						<b>グループ</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
						<b>性別</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:80px;">
						<b>年代</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:130px;">
						<b>登録日時</b>
					</td>
					<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
						<b>無効</b><br />
<!--
						<input type="checkbox" id="del_all" name="del_all" value="1">
-->
					</td>
				</tr>
				@foreach($db_data as $lines)
					{{-- メルマガ無効のときセル色をグレイ --}}
					@if( !empty($lines->disable) )
					<tr style='background:darkgray;'>
					{{-- メルマガ有効のときセル色をホワイト --}}
					@else
					<tr style='background:white;'>
					@endif
						<td style="padding:5px;text-align:center;">
							{{ $lines->ad_cd }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ config('const.disp_regist_status')[$lines->status] }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ $lines->name }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ config('const.list_sex')[$lines->sex] }}
						</td>
						<td style="padding:5px;text-align:center;">
							{{ config('const.list_age')[$lines->age] }}
						</td>
						<td style="padding:5px;text-align:center;">
							<a href="{{ url('/admin/member/client/edit') }}/{{ $db_data->currentPage() }}/{{ $lines->client_id }}/{{$lines->group_id}}">{{ $lines->created_at }}</a>
						</td>
						<td style="padding:5px;text-align:center;">
							@if( !empty($lines->disable) )
								●						
							@endif
<!--
							<input type="hidden" name="id[]" value="{{ $lines->id }}">
							<input type="checkbox" class="del del_group" name="del[]" value="{{ $lines->id }}" id="del_group{{ $lines->id }}">
-->
						</td>
					</tr>
				@endforeach
			</table>
			<br />
			<center>
				<button type="submit" id="push_update" class="btn btn-primary">&nbsp;&nbsp;&nbsp;更新&nbsp;&nbsp;&nbsp;</button>
				<button id="back_btn" type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;</button>
			</center>
		</div>
		</form>
	</div>
	
</div>

<form name="formSearch" class="form-horizontal" method="POST" action="/admin/member/client/search">
	{{ csrf_field() }}
	<input type="hidden" name="search_type" value="">
	<input type="hidden" name="search_item" value="">
	<input type="hidden" name="search_like_type" value="">
	<input type="hidden" name="group_id" value="">
	<input type="hidden" name="reg_status" value="">
	<input type="hidden" name="dm_status" value="">
	<input type="hidden" name="start_regdate" value="">
	<input type="hidden" name="end_regdate" value="">
	<input type="hidden" name="start_provdate" value="">
	<input type="hidden" name="end_provdate" value="">
	<input type="hidden" name="start_lastdate" value="">
	<input type="hidden" name="end_lastdate" value="">
	<input type="hidden" name="start_paydate" value="">
	<input type="hidden" name="end_paydate" value="">
	<input type="hidden" name="start_paynum" value="">
	<input type="hidden" name="end_paynum" value="">
	<input type="hidden" name="start_payamount" value="">
	<input type="hidden" name="end_payamount" value="">
	<input type="hidden" name="start_actnum" value="">
	<input type="hidden" name="end_actnum" value="">
	<input type="hidden" name="start_pt" value="">
	<input type="hidden" name="end_pt" value="">
	<input type="hidden" name="search_disp_num" value="">
	<input type="hidden" name="sort" value="">
</form>

<!-- 画面アラートJavascript読み込み -->
<script src="{{ asset('js/admin/alert.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//戻るボタンクリック
	$('#back_btn').click(function(){
		window.location.href = '{{ $back_url }}'
		return false;
	});

	//削除のすべて選択のチェックをOn/Off
	$('#del_all').on('change', function() {
		$('.del').prop('checked', this.checked);
	});

	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/search/setting', 'convert_table', 'width=700, height=435');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/client/create', 'create', 'width=1000, height=655');
		return false;
	});

	//アカウント編集ボタン押下後のダイアログ確認メッセージ
	//引数：フォームID、フォームのmethod、ダイアログのタイトル、ダイアログのメッセージ、通信完了後にダイアログに表示させるメッセージ、ダイアログのキャンセルメッセージ、タイムアウト
	submitAlert('formMailDel', 'post', '{{ __('messages.dialog_alert_title') }}', '{{ __('messages.dialog_alert_msg') }}', '{{ __('messages.update_msg') }}', '{{ __('messages.cancel_msg') }}', {{ config('const.admin_default_ajax_timeout') }}, true);
});
</script>

@endsection

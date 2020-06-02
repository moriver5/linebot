@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-7 col-md-offset-2">
			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading">
					<b>LINEメッセージ配信履歴</b>

				</div>
				<div class="panel-body">
					<center>{{ $db_data->links() }}</center>
					<table border="1" align="center" width="98%">
						<tr>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>ID</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:60px;">
								<b>配信状況</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:50px;">
								<b>配信数</b>
							</td>
							<td style="padding:5px;text-align:center;background:wheat;font-weight:bold;width:120px;">
								<b>配信日時</b>
							</td>
						</tr>
						@foreach($db_data as $lines)
							<tr>
								<td style="padding:2px;text-align:center;">
									<a href="{{ url('/admin/member/melmaga/mail/history/view/') }}/{{ $db_data->currentPage() }}/{{$lines->id}}">{{ $lines->id }}</a>
								</td>
								<td style="padding:2px;text-align:center;">
									@if( $lines->send_status == 0 )
									<b><font color="red">配信待ち</font></b>
									@elseif( $lines->send_status == 1 )
									<b><font color="blue">送信中</font></b>
									@elseif( $lines->send_status == 2 )
										<font color="gray">配信済</font>
									@elseif( $lines->send_status == 3 )
										<font color="gray">ｷｬﾝｾﾙ</font>
									@endif
								</td>
								<td style="padding:2px;text-align:center;">
									<a href="/admin/member/melmaga/mail/history/list/{{ $lines->id }}">{{ $lines->send_count }}</a>
								</td>
								<td style="padding:2px;text-align:center;">
									{{ $lines->send_date }}
								</td>
							</tr>
						@endforeach
					</table>

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var search_win;
$(document).ready(function(){
	//検索設定ボタン押下
	$('#search').on('click', function(){
		search_win = window.open('/admin/member/client/search/setting', 'convert_table', 'width=700, height=655');
		return false;
	});

	//新規作成ボタン押下
	$('#create').on('click', function(){
		search_win = window.open('/admin/member/client/create', 'create', 'width=1000, height=655');
		return false;
	});
});
</script>

@endsection

@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">

			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading" style="text-align:center;">
					<b>LINEメッセージ内容詳細</b>
				</div>
				<div class="panel-body">
					<div class="line__container">
						<!-- タイトル -->
						<div class="line__title">
						  {{ $db_data->name }}
						</div>

						<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
						<div class="line__contents scroll">
							@if( !empty($send_date) )
							<title_date>{{ $send_date }}</title_date>
							<div class="name">&nbsp;</div>
							@endif
							@foreach($list_msg as $index => $lines)
								@if( preg_match("/\.png$/", $lines[0]) > 0 )
									<!-- 相手のスタンプ -->
									<div class="line__left">
									  <figure>
										<img src="/images/admin/line_none.jpg" />
									  </figure>
									  <div class="line__left-text">
										<div class="stamp"><img src="/images/preview/{{ $lines[0] }}" /></div>
										<date_area>
										  {{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s)(\d{2}:\d{2}):\d{2}/", "$2", $lines[1]) }}
										</date_area>
									  </div>
									</div>
								@else
									<!-- 相手の吹き出し -->
									<div class="line__left">
									  <figure>
										<img src="/images/admin/line_none.jpg" />
									  </figure>
									  <div class="line__left-text">
										<div class="name">&nbsp;<br />&nbsp;</div>
										<div class="text">{{ $lines[0] }}</div>
									  </div>
									  <date_area>
										{{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s)(\d{2}:\d{2}):\d{2}/", "$2", $lines[1]) }}
									  </date_area>
									</div>
								@endif
							@endforeach
						</div>
						<!--　▲会話エリア ここまで -->
					</div>
					<!--　▲LINE風ここまで -->
				</div>
				<div style="text-align:center;margin-bottom:10px;">
					<a href="javascript:history.back();" class="btn btn-primary">戻る</a>
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

@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-5 col-md-offset-3">

			<div class="panel panel-default" style="margin-left:20px;background:papayawhip;float:left;">
				<div style="text-align:center;font-size:10px;color:red;height:25px;padding:10px;">※保存するとPreviewに反映されます</div>
				<div class="line__container">
					<!-- タイトル -->
					<div class="line__title">
						@if( !empty($db_data->name) )
						{{ $db_data->name }}
						@endif
					</div>

					<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
					<div class="line_carousel_contents scroll">
						@if( !empty($list_msg) )
						<div class="line__left">
							<carousel_figure>
							  <img src="/images/admin/line_none.jpg" />
							</carousel_figure>
						</div>
						<div class="slide-wrap" style="height:350px;">
							@foreach($list_msg as $index => $lines)
								@if( preg_match("/\.(png|jpg|jpeg)$/", $lines[0]) > 0 )
									<!-- 相手のスタンプ -->
									<div class="slide-box">
										<div style="width:100%;height:200px;background-image:url('/images/preview/{{ $lines[0] }}');background-size:100% auto;"></div>
										<div style="padding:5px;text-align:left;font-size:13px;font-weight:bold;">{{ $lines[2] }}</div>
										<div style="height:100%;padding:0 5px 10px 5px;">{{ $lines[3] }}</div>
										<div style="width:50%;text-align:center;height:50px;padding:15px;position:absolute;bottom:0;"><a href="">{{ $lines[5] }}</a></div>
									</div>
								@endif
							@endforeach
						</div>
						@endif
					</div>
					<!--　▲会話エリア ここまで -->
				</div>
				<!--　▲LINE風ここまで -->
				<div style="text-align:center;margin-bottom:8px;">
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

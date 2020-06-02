@extends('layouts.app')

@section('content')
<br />
<br />
<div class="container">
    <div class="col">
        <div class="col-md-8 col-md-offset">

			<div class="panel panel-default" style="font-size:12px;">
				<div class="panel-heading" style="text-align:center;">
					<b>LINEメッセージ内容詳細</b>
				</div>
				<div class="panel-body">
					<div class="panel panel-default" style="margin:0px 30px;background:papayawhip;float:left;">
						<div style="text-align:center;font-size:10px;color:red;height:25px;padding:10px;">
							※保存するとPreviewに反映されます。<br>
						</div>
						<div class="line__container" style="width:721px;">
							<!-- ▼会話エリア scrollを外すと高さ固定解除 -->
							<div class="line_imagemap_contents scroll">
								<br>
								<div>
								  <div>
									<div style="width:100%;"><img src="/php/line/imagemap/{{ $channel_id }}/{{ $img }}/700" style="border-radius:2%;" /></div>
									<date_area style="float:right;margin-right:2px;">
									  {{ preg_replace("/(\d{4}\-\d{2}\-\d{2}\s)(\d{2}:\d{2}):\d{2}/", "$2", $reserve_date) }}
									</date_area>
								  </div>
								</div>
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

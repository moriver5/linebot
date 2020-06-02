@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル追加&nbsp;-->&nbsp;Step2</b>
					<span style="float:right;"><a href="https://developers.line.biz/console/" target="_blank">LINE Developerへログイン</a></span>
				</div>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;">
								<table border="1" width="95%">
									<tr>
										<td style="size:10px;padding:10px;text-align:center;background:wheat;font-weight:bold;">Webhook URL</td>
										<td>
											<span id="webhook"　style="padding:10px;"><b>{{ config('const.base_webhook_url') }}{{ $line_basic_id }}</b></span>
											<button type="submit" class="btn btn-primary" id="push_copy">Webhook URLをコピー</button>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="padding:10px;font-weight:bold;">
											手順１．Webhook URLをコピーします。<br>
											手順２．リンクをクリックしアクセス→<a href="https://developers.line.biz/console/channel/{{ $channel_id }}/messaging-api" target="_blank">Webhook URLの設定をする</a><br>
											手順３．下記画像の赤枠内の入力フォームにWebhook URLを入力し更新ボタンを押下する<br>
											手順４．検証ボタンを押下し「成功」が表示されたらOKです。<br>
											手順５．下記画像と同じように青枠内の「Webhookの利用」にチェックを入れます。<br>
											<br>
											<center><img src="/images/admin/webhook.png"></center>

										</td>
									</tr>
								</table>
							</div>
							<button type="submit" class="btn btn-primary" id="push_btn">　　　次へ　　　</button>
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
$(document).ready(function(){
	$("#push_copy").click(function(){
		var clipboard = document.getElementById('webhook');
		var range = document.createRange();
		range.selectNode(clipboard);
		window.getSelection().addRange(range);
		document.execCommand('copy');
		swal('Webhook URLをコピーしました');
	});

	$("#push_btn").click(function(){
		window.location = "{{ $redirect_url }}";
	});
});
</script>

@endsection

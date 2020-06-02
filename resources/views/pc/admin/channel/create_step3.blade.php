@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル追加&nbsp;-->&nbsp;Step3</b>
					<span style="float:right;"><a href="https://developers.line.biz/console/" target="_blank">LINE Developerへログイン</a></span>
				</div>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;">
								<table border="1" width="95%">
									<tr>
										<td colspan="2" style="padding:10px;font-weight:bold;">
											手順１．リンクをクリックしアクセス→<a href="https://manager.line.biz/account/{{ $line_basic_id }}/setting/response" target="_blank">自動応答の設定をする</a><br>
											手順２．下記画像の青枠内と同じ設定をしてください<br>
											　　　　１．応答モード→「<b><font color="blue">Bot</font></b>」にチェック<br>
											　　　　２．あいさつメッセージ→「<b><font color="red">オフ</font></b>」にチェック<br>
											　　　　３．応答メッセージ→「<b><font color="red">オフ</font></b>」にチェック<br>
											　　　　４．Webhook→「<b><font color="blue">オン</font></b>」にチェック<br>
											<br>
											<center><img src="/images/admin/response.png"></center>

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
	$("#push_btn").click(function(){
		window.location = "{{ $redirect_url }}";
	});
});
</script>

@endsection

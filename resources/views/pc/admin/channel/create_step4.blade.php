@extends('layouts.app')

@section('content')
<br />
<div class="container">
    <div class="col">
        <div class="col-md-9 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>LINEチャンネル追加&nbsp;-->&nbsp;Step4</b>
					<span style="float:right;"><a href="https://developers.line.biz/console/" target="_blank">LINE Developerへログイン</a></span>
				</div>
                <div class="panel-body">
					<center>
						<div>
							<div class="form-group" style="align:center;">
								<table border="1" width="95%">
									<tr>
										<td colspan="2" style="padding:10px;font-weight:bold;">
											手順１．リンクをクリックしアクセス→<a href="{{ config('const.base_url') }}/admin/member/line/setting/replay/{{ $line_basic_id }}">友だち追加の自動応答メッセージの設定をする</a><br>
											手順２．プルダウンの「友だち追加時あいさつ」を選択<br>
											手順３．友だち追加時あいさつのメッセージを入力後、追加ボタンを押下し保存する。設定は以上で終了です。
										</td>
									</tr>
								</table>
							</div>
						</div>
					</center>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

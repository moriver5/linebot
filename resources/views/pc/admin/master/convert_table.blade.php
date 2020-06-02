<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta name="robots" content="noindex,nofollow">
    <meta charset="utf-8">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LINE BOT管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<br />
<div>
    <div class="col">
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>変換表</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
					<center>
						<table>
						@foreach($db_data as $index => $lines)
							@if( $index % 3 == 0)
								<tr>
							@endif
							<td style="background:lightyellow;width:350px;font-size:11px;font-weight:bold;text-align:center;border:1px solid lavender;" class="convert_key" id="{{ $lines->key }}">
								<div style="margin:5px;">
									<div>{{ $lines->value }}</div>
									<div>{{ $lines->key }}</div>
								</div>
							</td>
							@if( $index % 3 == 2)
								<tr>
							@endif
						@endforeach
						</table>
					</center>
                </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){

	$('.convert_key').mouseover(function(){
		$(this).css("background-color","#FBEFF8");
	}).mouseout(function(){
		$(this).css("background-color","lightyellow");
	});
	
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.opener.sub_win.close();
	});

	//変換表のキーを押したら出力文言設定にキーを挿入
	$('.convert_key').on('click', function(){
		//変換表のキーを取得
		var keyword = this.id;

		//変換表のキーの長さ取得
		var key_len = keyword.length;

		//出力文言設定画面
		//テキストエリアのオブジェクト取得
		var dom = window.opener.document.getElementById('contents{{ $id }}');

		//テキストエリアのフォーカスの位置を取得
		var focus_pos = dom.selectionStart;
		
		//テキストエリア内の文字列全体の長さを取得
		var sentence_length = dom.value.length;
		
		//テキストエリア内の文字列先頭からフォーカス位置までの文字列を取得
		var fowward = dom.value.substr(0, focus_pos);

		//テキストエリア内のフォーカス位置から最後までの文字列を取得
		var backward = dom.value.substr(focus_pos, sentence_length);

		//テキストエリア内のフォーカス位置に変換表のキーを追加
		dom.value = fowward + keyword + backward;
		
		//テキストエリア内のフォーカス位置をキー追加後に設定
		dom.selectionStart = focus_pos + key_len;
		dom.selectionEnd = focus_pos + key_len;
		dom.focus();
	});

	//ウィンドウのリサイズ
	window.resizeTo(document.documentElement.scrollWidth,document.documentElement.scrollHeight + 70);
	
});
</script>

</body>
</html>

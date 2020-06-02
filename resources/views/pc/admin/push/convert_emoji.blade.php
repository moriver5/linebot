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
    <title>LINE BOT 管理</title>

    <!-- Styles -->
    <link href="{{ asset('css/admin/app.css') }}" rel="stylesheet" />
	
	<!-- jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- 絵文字変換表の表示 -->
	<script type='text/javascript'>

		function showDing(i) {
//		   document.write("<td style='font-size:12px;font-family:monospace;border-right-width:0;padding-left:3px;' class='convert_key' id='"+i+"'>");
//		   document.write("&amp;#");
//		   document.write(i.toString(10));
//		   document.write(";");
//		   document.write("</td>");
		   document.write("<td class=\"convert_key\" ");
		   document.write("style=\"");
		   document.write("text-align:center;font-size: 120%;border-left-width: 0;");
		   document.write("\" id='"+i+"'>");
		   document.write("&#x");
		   document.write(i.toString(16));
		   document.write(";");
		   document.write("</td>");

		}

		function showRangeInTable(start, end) { 
		   var i, k = 0;
		   document.write("<tr>");
		   for (i=start; i<=end; i++) {
			  showDing(i);
			  if (++k % 15 == 0) {
				 document.write("</tr>");
				 document.writeln();
				 document.write("<tr>");
			  }
		   }
		   document.write("</tr>");
		   document.writeln();
		}
	</script>
</head>
<body>
<br />
<div>
    <div class="col">
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">
					<b>絵文字変換表</b>
					<span class="convert_windows_close" style="font-size:14px;background:darkgray;float:right;padding:2px 4px 2px 4px;">close</span>
				</div>
                <div class="panel-body">
					<span style="font-size:12px;font-weight:bold;color:black;margin-bottom:10px;">※表示させたい絵文字をクリックすると絵文字がコピーされます。</span>
					<center>
						<table border='1' align='center' width='100%'>
						<script>
							showRangeInTable(0x1f300, 0x1f9ff);
						</script>
						</table>
					</center>
                </div>
			</div>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin/utility.js') }}?ver={{ $ver }}"></script>
<script type="text/javascript">
$(document).ready(function(){

	$('.convert_key').mouseover(function(){
		$(this).css("background-color","tan");
	}).mouseout(function(){
		$(this).css("background-color","white");
	});
	
	//閉じるをクリック
	$('.convert_windows_close').on('click', function(){
		window.opener.sub_win.close();
	});

	//変換表のキーを押したら出力文言設定にキーを挿入
	$('.convert_key').on('click', function(){
		//絵文字取得
		var pic = document.getElementById(this.id).textContent;
/*
		//絵文字の長さ取得
		var pic_len = pic.length;

		//出力文言設定画面
		//テキストエリアのオブジェクト取得
		var dom = window.opener.document.getElementById('{{$id}}');

		//テキストエリアのフォーカスの位置を取得
		var focus_pos = dom.selectionStart;
		
		//テキストエリア内の文字列全体の長さを取得
		var sentence_length = dom.value.length;
		
		//テキストエリア内の文字列先頭からフォーカス位置までの文字列を取得
		var fowward = dom.value.substr(0, focus_pos);

		//テキストエリア内のフォーカス位置から最後までの文字列を取得
		var backward = dom.value.substr(focus_pos, sentence_length);

		//テキストエリア内のフォーカス位置に変換表のキーを追加
		dom.value = fowward + pic + backward;
		
		//テキストエリア内のフォーカス位置をキー追加後に設定
		dom.selectionStart = focus_pos + pic_len;
		dom.selectionEnd = focus_pos + pic_len;
		dom.focus();
*/
		//ここからクリップボードにコピー
		//picを含んだtextareaをbodyタグの末尾に設置
		$(document.body).append("<textarea id=\"tmp_copy\" style=\"position:fixed;right:100vw;font-size:16px;\" readonly=\"readonly\">" + pic + "</textarea>");

		//elmはtextareaノード
		var elm = $("#tmp_copy")[0];

		//select()でtextarea内の文字を選択
		elm.select();

		//rangeでtextarea内の文字を選択
		var range = document.createRange();
		range.selectNodeContents(elm);
		var sel = window.getSelection();
		sel.removeAllRanges();
		sel.addRange(range);
		elm.setSelectionRange(0, 999999);

		//execCommandを実施
		document.execCommand("copy");

		//クリップボードにコピーここまで
		//textareaを削除
		$(elm).remove();
	});

	//ウィンドウのリサイズ
	window.resizeTo(document.documentElement.scrollWidth,document.documentElement.scrollHeight + 70);
	
});
</script>

</body>
</html>

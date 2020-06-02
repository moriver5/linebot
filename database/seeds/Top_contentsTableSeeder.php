<?php

use Illuminate\Database\Seeder;

class Top_contentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$now_date = date("Y/m/d h:m:s");
		$sdate = '2016-01-01 00:00:00';
		$edate = '2018-09-01 00:00:00';
		$sort_date = '2016-01-01';

		$listData = [
			[
				'title'		=> '【制作中】ご参加枠極小激レアキャンペーン',
				'type'		=> 1,
				'groups'	=> '',
				'img'		=> '1.png',
				'html_body'	=> '<html>
<head>
<title>ご参加枠極小激レアキャンペーン</title>
<style>
html,body{margin:0;padding:0;background:#000;}
.campaign_detail{width:610px;margin:0 auto;background:#daa520;color:#333;}
.campaign_detail dl{margin:0;padding:0 15px 15px 15px;}
.campaign_detail dl dt{margin:10px 0;padding:0;}
.campaign_detail dl dd{margin:0;padding:10px;font-size:14px;border:2px solid #000;background:#FFF;}
.campaign_detail img{max-width:100%;}
</style>
</head>
<body>
<div class="campaign_detail">

<!--※↓編集ここから-->

<img src="http://jra-yosou.net/image/display_image/campaign04.png" alt="ご参加枠極小激レアキャンペーン">

<dl>
<dt><img src="http://jra-yosou.net/image/display_image/h1_01.png" alt="ご提供内容"></dt>
<dd><!--↓ご提供内容-->
【入力枠】
</dd>
</dd>
<dt><img src="http://jra-yosou.net/image/display_image/h1_02.png" alt="ご提供日時"></dt>
<dd><!--↓ご提供日時-->
-%date-<br>
</dd>
<dt><img src="http://jra-yosou.net/image/display_image/h1_03_2.png" alt="推定配当金額"></dt>
<dd><!--↓推定配当金額-->
万円<br>

</dd>
<dt><img src="http://jra-yosou.net/image/display_image/h1_04.png" alt="提供鞍数"></dt>
<dd><!--↓提供鞍数-->
各日２鞍ずつの【計4鞍】+-%keiba_race02-<br>
</dd>
<dt><img src="http://jra-yosou.net/image/display_image/h1_05.png" alt="買い目点数"></dt>
<dd><!--↓買い目点数-->
-%kaime-<br>
</dd>
<dt><img src="http://jra-yosou.net/image/display_image/h1_06.png" alt="推奨投資額"></dt>
<dd><!--↓推奨投資額-->
-%invest-<br>
</dd><dt><img src="http://jra-yosou.net/image/display_image/h1_07_2.png" alt="ご参加費用"></dt>
<dd><!--↓ご参加費用-->
【入力枠】
</dd>
<dt><img src="http://jra-yosou.net/image/display_image/h1_08.png" alt="情報公開日時"></dt>
<dd><!--↓情報公開日時-->
【入力枠】
</dd>
</dl>

<!--※↑編集ここまで-->

</div>
</body>
</html>',
			],
			[
				'title'		=> '【制作中】高配当キャンペーン',
				'type'		=> 1,
				'groups'	=> '',
				'img'		=> '2.png',
				'html_body'	=> '<html>
<head>
<title>高配当キャンペーン</title>
<style>
html,body{margin:0;padding:0;background:#000;}
.campaign_detail{width:610px;margin:0 auto;background:#daa520;color:#333;}
.campaign_detail dl{margin:0;padding:0 15px 15px 15px;}
.campaign_detail dl dt{margin:10px 0;padding:0;}
.campaign_detail dl dd{margin:0;padding:10px;font-size:14px;border:2px solid #000;background:#FFF;}
.campaign_detail img{max-width:100%;}
</style>
</head>
<body>
<div class="campaign_detail">

<!--※↓編集ここから-->

<img src="http://premium-h.jp/image/display_image/campaign02.png" alt="高配当キャンペーン">

<dl>
<dt><img src="http://premium-h.jp/image/display_image/h1_01.png" alt="ご提供内容"></dt>
<dd><!--↓ご提供内容-->
【入力枠】
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_02.png" alt="ご提供日時"></dt>
<dd><!--↓ご提供日時-->
-%date-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_03_2.png" alt="推定配当金額"></dt>
<dd><!--↓推定配当金額-->
万円<br>

</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_04.png" alt="提供鞍数"></dt>
<dd><!--↓提供鞍数-->
各日２鞍ずつの【計4鞍】+-%keiba_race02-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_05.png" alt="買い目点数"></dt>
<dd><!--↓買い目点数-->
-%kaime-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_06.png" alt="推奨投資額"></dt>
<dd><!--↓推奨投資額-->
-%invest-<br>
</dd>
</dd><dt><img src="http://premium-h.jp/image/display_image/h1_07_2.png" alt="ご参加費用"></dt>
<dd><!--↓ご参加費用-->
【入力枠】
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_08.png" alt="情報公開日時"></dt>
<dd><!--↓情報公開日時-->
【入力枠】
</dd>
</dl>

<!--※↑編集ここまで-->

</div>
</body>
</html>',
			],
			[
				'title'		=> '【制作中】超ギアレアキャンペーン',
				'type'		=> 1,
				'groups'	=> '',
				'img'		=> '3.png',
				'html_body'	=> '<html>
<head>
<title>超ギアレアキャンペーン</title>
<style>
html,body{margin:0;padding:0;background:#000;}
.campaign_detail{width:610px;margin:0 auto;background:#daa520;color:#333;}
.campaign_detail dl{margin:0;padding:0 15px 15px 15px;}
.campaign_detail dl dt{margin:10px 0;padding:0;}
.campaign_detail dl dd{margin:0;padding:10px;font-size:14px;border:2px solid #000;background:#FFF;}
.campaign_detail img{max-width:100%;}
</style>
</head>
<body>
<div class="campaign_detail">

<!--※↓編集ここから-->

<img src="http://premium-h.jp/image/display_image/campaign05.png" alt="超ギアレアキャンペーン">

<dl>
<dt><img src="http://premium-h.jp/image/display_image/h1_01.png" alt="ご提供内容"></dt>
<dd><!--↓ご提供内容-->
【入力枠】
</dd>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_02.png" alt="ご提供日時"></dt>
<dd><!--↓ご提供日時-->
-%date-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_03_2.png" alt="推定配当金額"></dt>
<dd><!--↓推定配当金額-->
万円<br>

</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_04.png" alt="提供鞍数"></dt>
<dd><!--↓提供鞍数-->
各日２鞍ずつの【計4鞍】+-%keiba_race02-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_05.png" alt="買い目点数"></dt>
<dd><!--↓買い目点数-->
-%kaime-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_06.png" alt="推奨投資額"></dt>
<dd><!--↓推奨投資額-->
-%invest-<br>
</dd><dt><img src="http://premium-h.jp/image/display_image/h1_07_2.png" alt="ご参加費用"></dt>
<dd><!--↓ご参加費用-->
【入力枠】
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_08.png" alt="情報公開日時"></dt>
<dd><!--↓情報公開日時-->
【入力枠】
</dd>
</dl>

<!--※↑編集ここまで-->

</div>
</body>
</html>',
			],
			[
				'title'		=> '【制作中】超高配当キャンペーン',
				'type'		=> 1,
				'groups'	=> '',
				'img'		=> '4.png',
				'html_body'	=> '<html>
<head>
<title>超高配当キャンペーン</title>
<style>
html,body{margin:0;padding:0;background:#000;}
.campaign_detail{width:610px;margin:0 auto;background:#daa520;color:#333;}
.campaign_detail dl{margin:0;padding:0 15px 15px 15px;}
.campaign_detail dl dt{margin:10px 0;padding:0;}
.campaign_detail dl dd{margin:0;padding:10px;font-size:14px;border:2px solid #000;background:#FFF;}
.campaign_detail img{max-width:100%;}
</style>
</head>
<body>
<div class="campaign_detail">

<!--※↓編集ここから-->

<img src="http://premium-h.jp/image/display_image/campaign03.png" alt="超高配当キャンペーン">

<dl>
<dt><img src="http://premium-h.jp/image/display_image/h1_01.png" alt="ご提供内容"></dt>
<dd><!--↓ご提供内容-->
【入力枠】
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_02.png" alt="ご提供日時"></dt>
<dd><!--↓ご提供日時-->
-%date-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_03_2.png" alt="推定配当金額"></dt>
<dd><!--↓推定配当金額-->
万円<br>

</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_04.png" alt="提供鞍数"></dt>
<dd><!--↓提供鞍数-->
各日２鞍ずつの【計4鞍】+-%keiba_race02-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_05.png" alt="買い目点数"></dt>
<dd><!--↓買い目点数-->
-%kaime-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_06.png" alt="推奨投資額"></dt>
<dd><!--↓推奨投資額-->
-%invest-<br>
</dd></dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_07_2.png" alt="ご参加費用"></dt>
<dd><!--↓ご参加費用-->
【入力枠】
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_08.png" alt="情報公開日時"></dt>
<dd><!--↓情報公開日時-->
【入力枠】
</dd>
</dl>

<!--※↑編集ここまで-->

</div>
</body>
</html>',
			],
			[
				'title'		=> '【制作中】地方競馬キャンペーン',
				'type'		=> 1,
				'groups'	=> '',
				'img'		=> '5.png',
				'html_body'	=> '<html>
<head>
<title>地方競馬キャンペーン</title>
<style>
html,body{margin:0;padding:0;background:#000;}
.campaign_detail{width:610px;margin:0 auto;background:#daa520;color:#333;}
.campaign_detail dl{margin:0;padding:0 15px 15px 15px;}
.campaign_detail dl dt{margin:10px 0;padding:0;}
.campaign_detail dl dd{margin:0;padding:10px;font-size:14px;border:2px solid #000;background:#FFF;}
.campaign_detail img{max-width:100%;}
</style>
</head>
<body>
<div class="campaign_detail">

<!--※↓編集ここから-->

<img src="http://premium-h.jp/image/display_image/campaign06.png" alt="地方競馬キャンペーン">

<dl>
<dt><img src="http://premium-h.jp/image/display_image/h1_01.png" alt="ご提供内容"></dt>
<dd><!--↓ご提供内容-->
<div style="text-align:center;">
<div style="background:#daa520;color:#fff;font-weight:bold;display:block;text-align:center;text-shadow:1px 1px 1px rgba(0,0,0,0.8);">
<span style="font-size:32px;color:#ffffff;">競馬新聞はもう古い！<br>
</span>
<span style="font-size:28px;color:#D3D3D3;">全国でも数少ない「地方競馬高配当情報」がここで入手できます</span><br>

</div>
<div style="font-style:italic;font-weight:bold;font-size:28px;color:#D3D3D3;">
◆ＴＣＫ女王盃(Jpn3)的中キャンペーン◆<br>
</div>
<div style="background:#D3D3D3;font-size:18px;color:#ffffff;display:block;font-weight:bold;">1/25(水)は地方競馬紙記者も精度を認めた大井競馬で少し遅めのボーナスお年玉を獲得!!<br>
</div>
プレミアムが誇る高確率的中情報【南関競馬】のボーナスお年玉を獲得の為の極上投資馬券を1/25(水)限定でご提供させて頂きます。<br>
<br>
年明け最初の大井重賞は､牝馬のダートグレード競走｡<br>
全国から集まった女傑が真冬のダート女王の座を目指します｡<br>
このところはJRA所属馬の攻勢が続いていますが、近6年では地方所属馬が2回の優勝を果たす健闘を見せており､今年も混戦が予想されます。<br>
<br>
まさに牝馬の登竜門というべきレース。<br>
<br>
この度当社プレミアムでは、牝馬戦、しかもダート戦において昨年58戦中42勝している情報元より確勝情報を高額で購入致しました。<br>
<br>
牝馬戦以外のレースにおいても大井開催の競馬ではトップ的中率を誇る情報元！<br>
<br>
しかも本情報元は地方競馬紙3紙の記者が本情報の精度を認めており、ご参加頂ければ獲ったも同然。<br>
<br>
少々遅めの「ボーナスお年玉」を是非獲得してください!!<br>
<br>
牝馬ダートの覇者による【完全独占情報】<br>
▼　▼　▼　▼　▼<br>
最高推定額80万オーバー!!<br>
<br>
<div style="background:#D3D3D3;font-size:18px;color:#ffffff;display:block;font-weight:bold;">※緊急特別ボーナスお年玉企画※<br>
</div>
完全独占した情報で新春初大井重賞大攻略!!ボーナスお年玉獲得ＳＰ!!<br>
<br>
『地方競馬で80万近くの配当を獲得するのは可能か？』<br>
<br>
※勿論可能です※<br>
<br>
今回ご提供させて頂くレースは新春初大井競馬での重賞レースになります。<br>
平日のレースとはいえ、大多数の競馬ファンがレースに参加する事は間違いありません。<br>
参加する人数が多いという事はオッズの偏りも激しく推移する可能性が大。<br>
<br>
人気馬、不人気馬、オッズには捉われず、「牝馬ダート戦」のレース展開を確実に網羅した情報元だからこそ、確定的な的中・高配当にお導きできます。<br>
<br>
<div style="background:#D3D3D3;font-size:18px;color:#ffffff;display:block;font-weight:bold;">『2016年牝馬ダート戦は58戦中42勝!!』<br>
</div>
<br>
今回の情報元の分析能力は、まさに牝馬ダート戦おいては「無敵」と言っても過言ではありません。<br>
牝馬に限ったレースでは無敵ですが、それ以外のレースにおいても絶対的自信を持っております。<br>
<br>

※※※※※※※※※※浦和キャンペーン誰も入らなかったらココにキャプチャ載せる※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※
<br>
<hr>上記の的中結果はほんのごく一部ですが、配当面、的中面ともに最高クラスの情報元。<br>
<br>
しかしながら、中央競馬と比べ、情報も少なく、その地域に特化した競走馬や騎手が存在するので、予想をするのも困難では御座います。<br>
<br>
プレミアムをご利用の会員様の中でも特にお声が多いのが『地方競馬に興味はあるけど、どこの会場に絞って馬券を買えばいいのか分からない…』『中央競馬と違ってどの馬が強くてどの馬が穴をあけるか検討がつかない…』と、ご質問も承っております。<br>
<br>
的中結果をご覧頂けた通り、『数々の実績を残している有力情報元』が『今回完全監修』を務めますので、ご利用頂く会員様は、開催当日に当社の指定通りに馬券を購入頂くだけで高額配当を掴む事も夢では御座いません。<br>
<br>
難しい事は一切御座いません。地方競馬の利点でもある低投資にて高額配当を狙えるレースが1/25(水)開催大井競馬にて実現しようとしております。<br>
<br>
<hr><span style="font-weight:bold;color:#D3D3D3;font-size:20px;">※緊急告知※1/25(水)大井競馬大攻略◆ＴＣＫ女王盃(Jpn3)的中キャンペーン◆<br>
</span><hr>
<br>
<a href="settlement_list.php">ご参加はコチラから▼▼</a><br>
<br>
<div style="background:#D3D3D3;font-size:18px;color:#ffffff;display:block;font-weight:bold;">■豪華５特典付きの特別提供</div><br>
<br>
今回キャンペーンにご参加頂くともれなく豪華特典が『５つ』も付いてくる!!<br>
<br>
【特典１】<br>
１万円相当ポイントプレゼント!!<br>
<br>
【特典２】<br>
1月25日(水)開催【ＴＣＫ女王盃(Jpn3)】厳選買い目情報プレゼント!!<br>
<br>
【特典３】<br>
軍資金獲得割引による『７０％』当社情報料負担!!<br>
※今回は当社プレミアムご利用会員様に安心して当社情報をご利用頂き、高確率にて会員様に利益還元する為の特別情報としてご提供させて頂く為、７０％の割引価格にてご提供させて頂きます。<br>
<br>
【特典４】<br>
全額返還制度完備!!<br>
※万全のバックアップ体制として、配当計２０万円に到達されなかった場合、今回のご参加費用を全額分返還ポイント返還!<br>
<br>
【特典５】<br>
バックアップ態勢もバッチリ!!無料補填提供完備!!<br>
※万が一配当計２０万円まで到達いかなかった場合、次回以降の中央競馬開催日より、配当妙味買い目情報を無料補填として情報料+馬券を回収できる的中をお届けできるまでご提供。<br>
※すべて推奨投資額にて<br>
<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_02.png" alt="ご提供日時"></dt>
<dd><!--↓ご提供日時-->
1/25(水)<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_03_2.png" alt="推定配当金額"></dt>
<dd><!--↓推定配当金額-->
80万円<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_04.png" alt="提供鞍数"></dt>
<dd><!--↓提供鞍数-->
２鞍+ TCK女王盃(Jpn3)<br>
<dt><img src="http://premium-h.jp/image/display_image/h1_05.png" alt="買い目点数"></dt>
<dd><!--↓買い目点数-->
-%kaime-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_06.png" alt="推奨投資額"></dt>
<dd><!--↓推奨投資額-->
-%invest-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_07_2.png" alt="ご参加費用"></dt>
<dd><!--↓ご参加費用-->
【入力枠】
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_08.png" alt="情報公開日時"></dt>
<dd><!--↓情報公開日時-->
【入力枠】
</dd>
</dl>

<!--※↑編集ここまで-->

</div>
</body>
</html>',
			],
			[
				'title'		=> '的中精度最大キャン',
				'type'		=> 1,
				'groups'	=> '',
				'img'		=> '6.png',
				'html_body'	=> '-%headmenu-<html>
<head>
<title>的中制度最大級キャンペーン</title>
<style>
html,body{margin:0;padding:0;background:#000;}
.campaign_detail{width:610px;margin:0 auto;background:#daa520;color:#333;}
.campaign_detail dl{margin:0;padding:0 15px 15px 15px;}
.campaign_detail dl dt{margin:10px 0;padding:0;}
.campaign_detail dl dd{margin:0;padding:10px;font-size: 14px;border:2px solid #000;background:#FFF;}
.campaign_detail img{max-width:100%;}
</style>
</head>
<body>
<div class="campaign_detail">

<!--※↓編集ここから-->

<img src="http://premium-h.jp/image/display_image/campaign01.png" alt="的中制度最大級キャンペーン">

<dl>
<dt><img src="http://premium-h.jp/image/display_image/h1_01.png" alt="ご提供内容"></dt>
<dd><!--↓ご提供内容-->


ないよーがないよー





</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_02.png" alt="ご提供日時"></dt>
<dd><!--↓ご提供日時-->
-%date-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_03_2.png" alt="推定配当金額"></dt>
<dd><!--↓推定配当金額-->
【】　万円<br>
【】　万円<br>
【】　万円<br>

</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_04.png" alt="提供鞍数"></dt>
<dd><!--↓提供鞍数-->
各日２鞍ずつの【計4鞍】+-%keiba_race02-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_05.png" alt="買い目点数"></dt>
<dd><!--↓買い目点数-->
-%kaime-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_06.png" alt="推奨投資額"></dt>
<dd><!--↓推奨投資額-->
-%invest-<br>
</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_07_2.png" alt="ご参加費用"></dt>
<dd><!--↓ご参加費用-->







</dd>
<dt><img src="http://premium-h.jp/image/display_image/h1_08.png" alt="情報公開日時"></dt>
<dd><!--↓情報公開日時-->
レース前日18：30～当日13：00（コースにより異なります）<br>
<br>
※稀に情報公開メールが迷惑メールと誤認され不着の場合がございます。<br>
お手数ですが必ず上記情報公開時刻になりましたら、「情報公開」ページを会員様ご自身でご確認ください。<br>
※上記情報公開時刻になりましても公開にならない場合はお手数ですがご連絡をください。<br>
<br>
情報公開後は当サイトの情報公開画面にて当日の厳選買い目情報をご確認頂けます。<br><br>
その後は当社の指定通りに馬券購入/投票頂く流れとなります。<br>
<br>
※地方競馬はJRAのPATがご利用頂けない場合がございますので、インターネット投票にて馬券をお買い求めになる場合はご注意ください。<br>

<a href="settlement_list.php"><img src="http://premium-h.jp/image/display_image/pre_cpbuy.jpg" width="393" height="153"></a><br>

</dd>
</dl>

<!--※↑編集ここまで-->

</div>
</body>
</html>',
			],
			[
				'title'		=> 'ナマの声　内容',
				'type'		=> 2,
				'groups'	=> '',
				'img'		=> '7.png',
				'html_body'	=> '<div style="text-align:center;">
<font size="5">

■1月21日(日)　AJCC（G2）追い切り情報■<br>
<br>
≪1/17(水)美浦Ｗコース≫<br>
<br>
レジェンドセラーは、２頭を追いかけ、直線はインへ。鞍上のＧＯサインにしっかりと応え、豪快に中ラムセスバローズに２馬身、外ゴールデンブレイヴに１馬身先着した。内を回ったとはいえ、ラスト１Ｆ１２秒３は好時計。<br>
状態はキープできているだけではなく緩さがなくなりしっかりしてきた。<br>

<br>

※キャンペーンでご提供する情報は、別のキャンペーン情報元が提供するものですので、こちらに記載の情報とは異なる場合ありますので予めご了承ください。<br>
</font>',
			],
			[
				'title'		=> '見てください',
				'type'		=> 2,
				'groups'	=> '',
				'img'		=> '8.png',
				'html_body'	=> '<div style="text-align:center;">
<font size="5">
【今週の好走濃厚馬情報】<br>
今週の馬券に絡むことが濃厚な馬をご紹介!!<br>
1/21(日)<br>
中京１Ｒ　 <br>
9:50発走　<br>
◎(11)ツーエムアリエス	<br>
○(12)シンゼンアイル	<br>
▲(4)アイファーブレーヴ	<br>
<br><br>
中山12Ｒ　 <br>
16:20発走　<br>
◎(3)フローレスマジック	<br>
○(12)ジョンブドール	<br>
▲(6)アルメリアブルーム	<br>
<br>
<br>
※キャンペーンでご提供する情報は、別のキャンペーン情報元が提供するものですので、こちらに記載の情報とは異なる場合ありますので予めご了承ください。<br>
</font>',
			],
			[
				'title'		=> '重賞激走馬',
				'type'		=> 2,
				'groups'	=> '',
				'img'		=> '9.png',
				'html_body'	=> '<div style="text-align:center;">
<font size="5">

1月21日(日)AJCC（G2）
<br>
<br>
◎ゴールドアクター<br>
<br>
美浦坂路を２本駆け上がった。中川師は「去年の秋に入厩して放牧に出した時より、今の方が状態がいいです。体も太い感じはしません」と、順調な調整に手応え。<br>
<br>
※キャンペーンでご提供する情報は、別のキャンペーン情報元が提供するものですので、こちらに記載の情報とは異なる場合ありますので予めご了承ください。<br>
</font>',
			],
			[
				'title'		=> '※ポイント割引価格※35600円割引（356ptご利用）※■1/22(月)全国地方競馬的中キャンペーン◆【SS】2鞍/3連単・15点以内/推定配当300万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※37000円割引（370ptご利用）※■1/22(月)全国地方競馬的中キャンペーン◆【グレート】2鞍/3連単・10点以内/推定配当770万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※35600円割引（356ptご利用）※■1/24(水)全国地方競馬的中キャンペーン◆【SS】2鞍/3連単・15点以内/推定配当300万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※37000円割引（370ptご利用）※■1/23(火)全国地方競馬的中キャンペーン◆【グレート】2鞍/3連単・10点以内/推定配当770万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※75200円割引（752ptご利用）※■1/24(水)全国地方競馬的中キャンペーン◆【SSS】2鞍/3連単・10点以内/推定配当550万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※75200円割引（752ptご利用）※■1/25(木)全国地方競馬的中キャンペーン◆【SSS】2鞍/3連単・10点以内/推定配当550万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※75200円割引（752ptご利用）※■1/22(月)全国地方競馬的中キャンペーン◆【SSS】2鞍/3連単・10点以内/推定配当550万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※35600円割引（356ptご利用）※■1/23(火)全国地方競馬的中キャンペーン◆【SS】2鞍/3連単・15点以内/推定配当300万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※75200円割引（752ptご利用）※■1/23(火)全国地方競馬的中キャンペーン◆【SSS】2鞍/3連単・10点以内/推定配当550万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※37000円割引（370ptご利用）※■1/24(水)全国地方競馬的中キャンペーン◆【グレート】2鞍/3連単・10点以内/推定配当770万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※37000円割引（370ptご利用）※■1/25(木)全国地方競馬的中キャンペーン◆【グレート】2鞍/3連単・10点以内/推定配当770万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
			[
				'title'		=> '※ポイント割引価格※35600円割引（356ptご利用）※■1/25(木)全国地方競馬的中キャンペーン◆【SS】2鞍/3連単・15点以内/推定配当300万円',
				'type'		=> 1,
				'groups'	=> '1,2,3,4,5,8,9,12,14,16,18',
				'img'		=> '',
				'html_body'	=> '',
			],
		];

		foreach($listData as $lines){
			DB::table('top_contents')->insert([
				'title'				=> $lines['title'],
				'type'				=> $lines['type'],
				'open_flg'			=> 1,
				'link_flg'			=> 0,
				'groups'			=> $lines['groups'],
				'order_num'			=> 1,
				'start_date'		=> $sdate,
				'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $sdate).'00',
				'end_date'			=> $edate,
				'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $edate).'00',
				'img'				=> $lines['img'],
				'html_body'			=> $lines['html_body'],
				'sort_date'			=> $sort_date,
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date,
			]);
		}
    }
}

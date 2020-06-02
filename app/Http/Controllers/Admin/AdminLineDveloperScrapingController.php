<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Goutte;

class AdminLineDveloperScrapingController extends Controller
{
	private $log_obj;

	public function __construct()
	{

	}

	public function getLineChannel()
	{
		$goutte = new \Goutte\Client();

		//ユーザーエージェント設定(設定してもしなくてもどちらでも大丈夫かも)
		$goutte->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 ');

		//Youtubeランキングサイトへアクセス
		$response = $goutte->request('GET', "https://ytranking.net/");

		//スクレイピングでデータ取得
		$response->filter("ul.channel-list li")->each(function($li){
			$rank		 = $li->filter("p.rank")->text();					//順位
			$thumbnail	 = $li->filter("p.thumbnail img")->attr("src");		//サムネイルURL
			$title		 = $li->filter("p.title")->text();					//チャンネル名

			$regist_num = "";
			$views_num = "";
			$video_num = "";
			$li->filter("aside p")->each(function($p, $i) use(&$regist_num, &$views_num, &$video_num){
				//登録者数
				if( $i == 0 ){
					$regist_num = preg_replace("/people(.+)/", "$1", $p->text());
				}

				//再生回数
				if( $i == 1 ){
					$views_num = preg_replace("/play_circle_filled(.+)/", "$1", $p->text());
				}

				//動画本数
				if( $i == 2 ){
					$video_num = preg_replace("/videocam(.+)/", "$1", $p->text());
				}
			});
echo "{$rank} {$thumbnail} {$title}　{$regist_num} {$views_num} {$video_num}<br>";

			//ここでDBへ登録処理
		});
/*
		$response = $goutte->request('GET', "https://account.line.biz/login?scope=line&redirectUri=https%3A%2F%2Fdevelopers.line.biz%2Flogin%3Fbox%3D%2Fconsole%2F");

//error_log(print_r($goutte->html(),true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		preg_match("/<meta\sname=\"x\-csrf\"\scontent=\"([\s\S]+?)\">/u", $response->html(), $csrf);
//error_log(print_r($csrf,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		$response = $goutte->request('POST', 'https://account.line.biz/login/line?type=login', ['_csrf' => $csrf[1]]);
		$response = $goutte->request('GET', 'https://developers.line.biz/console/');
		$response = $goutte->request('GET', 'https://developers.line.biz/api/v1/channel/?providerId=1597988364');
//error_log(print_r($response,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
exit;
 */
/*
		$goutte->filter('ul.List_Product-top')->each(function($ul){
			$ul->filter('li#Product-LineLogin')->each(function($li){
				$login_url = $li->filter('a')->attr('href');
//error_log($login_url.":url\n",3,"/data/www/line/storage/logs/nishi_log.txt");

				$goutte = \Goutte::request('GET', 'https://developers.line.biz/'.$login_url);
				$login_form = $goutte->filter('form')->form();
//error_log(print_r($login_form,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//					$login_form['_csrf'] = $csrf;
					

			});
		});
 */
	}
}

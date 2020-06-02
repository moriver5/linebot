<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\LineOfficialAccount;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;

use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\ExternalLinkBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\VideoBuilder;

use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;

class LineImageMapPushMessageController extends Controller
{
	public function __construct()
	{

	}


	/*
	 *  ボタンテンプレートを配信する
	 */
	public function sendLineImageMap($bot, $channel_id, $push_line_id, $list_line_id, $db_data = [])
	{
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();
//error_log(print_r($db_data,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log(print_r($list_line_id,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log("{$push_line_id}:name1\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
				['channelSecret' => $line_account->line_channel_secret]
			);

		$list_area = json_decode($db_data->area_json);

		//画像サイズ取得
		list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$channel_id.'/img/'.$db_data->img);
//error_log(print_r($list_area,true).":$width, $height, $type, $attr name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//横幅固定：1040
		$basesize = new BaseSizeBuilder($height, 1040);

		$listImageMapAct = [];
		foreach($list_area as $lines){
			$listArea = explode(",", $lines);
			$imagemap_area = new AreaBuilder($listArea[0], $listArea[2], $listArea[1], $listArea[3]);
			$imagemap_uri_act = new ImagemapUriActionBuilder($listArea[4], $imagemap_area);
			$listImageMapAct[] = $imagemap_uri_act;
		}

//		$image_id = preg_replace("/(imagemap_\d+)\.(jpg|jpeg|png)/", "$1", $db_data->img);

        $imagemap = new ImagemapMessageBuilder(
			config('const.base_url')."/php/line/imagemap/{$channel_id}/{$db_data->img}",
			$db_data->alttext,
			$basesize,
			$listImageMapAct
		);

		$messageBuilder = new MultiMessageBuilder();
		$messageBuilder->add($imagemap);

		$response = $bot->multicast($list_line_id, $messageBuilder, true);

		if( !$response->isSucceeded() ){
			error_log('Failed! '. $response->getRawBody(),3,"/data/www/line/storage/logs/nishi_log.txt");
		}

	}
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineOfficialAccount;
use App\Model\LinePushLog;
use App\Model\Line_click_user;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;

use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;

use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\ExternalLinkBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\VideoBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;

use Utility;

class LinePushMessageController extends Controller
{
	private $listLineEmotion = [];

	public function __construct()
	{
		$this->listLineEmotion = $this->_getMakeListLineEmotion();
	}

	public function sendLineOneUserPushMessage($bot, $basic_id, $push_line_id, $list_line_id, $list_push_msg= [], $push_image = "")
	{
		foreach($list_line_id as $line_id){
			$msgCount = 0;

			$messageBuilder = new MultiMessageBuilder();

	//		$messageBuilder->add(new VideoMessageBuilder('https://youtu.be/oxU-NWTKCtc', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJ_Jzx3-nnmODzIxnTvLfOzbavWnf8N1dq12QD6wmDgbvhuQGQ'));

			//送信メッセージに画像追加
			if( !empty($push_image) ){
				$msgCount++;
				$messageBuilder->add(new ImageMessageBuilder(
					config('const.base_url')."/php/line/push/img/original/{$basic_id}/{$push_line_id}",
					config('const.base_url')."/php/line/push/img/preview/{$basic_id}/{$push_line_id}"
				));
			}

			foreach($list_push_msg as $name => $push_msg){
				$push_msg = trim($push_msg);

				if( preg_match("/(\d){5}<>(\d){8}/", $push_msg) > 0 ){
					list($packageId, $stickerId) = explode("<>", $push_msg);
					$push_msg = new StickerMessageBuilder($packageId, $stickerId);

				//イメージマップ
				}elseif( preg_match("/^imglinkmsg\d_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
					//画像名とURLに分割
					list($img, $linked_url, $altText) = explode("|", $push_msg);
	/*
					//ショートURL取得
					$short_url = Utility::getShortUrl($linked_url);

					//実際にアクセスするショートURL
					$linked_url = config('const.base_url').'/'.$short_url;
	 */
					$short_url = Utility::getUniqueShortUrl($basic_id, $push_line_id, $linked_url, $line_id);
					$linked_url = config('const.base_url').'/line/'.$push_line_id.'/'.$short_url.'/img';
	//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					//画像サイズ取得
					list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
	//error_log("{$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					//横幅固定：1040
					$basesize = new BaseSizeBuilder($height, 1040);

					$imagemap_area = new AreaBuilder(0, 0, $width, $height);
					$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
					$listImageMapAct[] = $imagemap_uri_act;

					$push_msg = new ImagemapMessageBuilder(
						config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
						$altText,
						$basesize,
						$listImageMapAct
					);

				//画像
				}elseif( preg_match("/^msg\d_(\d)+\.(png|jpg|jpeg)/", $push_msg) > 0 ){
	//error_log("{$basic_id}/{$name}/{$push_msg}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					//送信メッセージに画像追加
					$push_msg = new ImageMessageBuilder(
						config('const.base_url')."/php/line/push/img/original/{$basic_id}/{$name}/{$push_msg}",
						config('const.base_url')."/php/line/push/img/preview/{$basic_id}/{$name}/{$push_msg}"
					);

				//テキスト
				}else{
					//URLをショートURLへ変換
					$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $push_line_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
						$short_url = Utility::getShortUrl($match[0]);
						return config('const.base_url').'/'.$short_url;
 */
						$short_url = Utility::getUniqueShortUrl($basic_id, $push_line_id, $match[0], $line_id);
						return config('const.base_url').'/line/'.$push_line_id.'/'.$short_url;
					}, $push_msg);
//error_log($push_msg."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
					$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
					$push_msg = new TextMessageBuilder($push_msg);
				}
				$messageBuilder->add($push_msg);

				$msgCount++;

				if( $msgCount == 5 ){
					break;
				}
			}
//error_log("send\n",3,"/data/www/line/storage/logs/nishi_log.txt");
			//特定の１ユーザーにLINEメッセージを送信
//			$bot->pushMessage([$line_id], $messageBuilder, true);
			$bot->multicast([$line_id], $messageBuilder, true);
		}
	}

	public function sendLinePushMessage($bot, $basic_id, $push_line_id, $list_line_id, $list_push_msg= [], $push_image = "")
	{
		$msgCount = 0;

		$messageBuilder = new MultiMessageBuilder();

//		$messageBuilder->add(new VideoMessageBuilder('https://youtu.be/oxU-NWTKCtc', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJ_Jzx3-nnmODzIxnTvLfOzbavWnf8N1dq12QD6wmDgbvhuQGQ'));

		//送信メッセージに画像追加
		if( !empty($push_image) ){
			$msgCount++;
			$messageBuilder->add(new ImageMessageBuilder(
				config('const.base_url')."/php/line/push/img/original/{$basic_id}/{$push_line_id}",
				config('const.base_url')."/php/line/push/img/preview/{$basic_id}/{$push_line_id}"
			));
		}

		foreach($list_push_msg as $name => $push_msg){
			$push_msg = trim($push_msg);

			if( preg_match("/(\d){5}<>(\d){8}/", $push_msg) > 0 ){
				list($packageId, $stickerId) = explode("<>", $push_msg);
				$push_msg = new StickerMessageBuilder($packageId, $stickerId);

			//イメージマップ
			}elseif( preg_match("/^imglinkmsg\d_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
				//画像名とURLに分割
				list($img, $linked_url, $altText) = explode("|", $push_msg);
/*
				//ショートURL取得
				$short_url = Utility::getShortUrl($linked_url);

				//実際にアクセスするショートURL
				$linked_url = config('const.base_url').'/'.$short_url;
 */
				$short_url = Utility::getUniqueShortUrl($basic_id, $push_line_id, $linked_url, $line_id);
				$linked_url = config('const.base_url').'/line/'.$push_line_id.'/'.$short_url.'/img';
//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//画像サイズ取得
				list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
//error_log("{$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//横幅固定：1040
				$basesize = new BaseSizeBuilder($height, 1040);

				$imagemap_area = new AreaBuilder(0, 0, $width, $height);
				$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
				$listImageMapAct[] = $imagemap_uri_act;

				$push_msg = new ImagemapMessageBuilder(
					config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
					$altText,
					$basesize,
					$listImageMapAct
				);

			//画像
			}elseif( preg_match("/^msg\d_(\d)+\.(png|jpg|jpeg)/", $push_msg) > 0 ){
//error_log("{$basic_id}/{$name}/{$push_msg}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//送信メッセージに画像追加
				$push_msg = new ImageMessageBuilder(
					config('const.base_url')."/php/line/push/img/original/{$basic_id}/{$name}/{$push_msg}",
					config('const.base_url')."/php/line/push/img/preview/{$basic_id}/{$name}/{$push_msg}"
				);

			//テキスト
			}else{
				//URLをショートURLへ変換
				$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $push_line_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
					$short_url = Utility::getShortUrl($match[0]);
					return config('const.base_url').'/'.$short_url;
*/
					$short_url = Utility::getUniqueShortUrl($basic_id, $push_line_id, $match[0], $line_id);
					return config('const.base_url').'/line/'.$push_line_id.'/'.$short_url;
				}, $push_msg);

				//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
				$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
				$push_msg = new TextMessageBuilder($push_msg);
			}
			$messageBuilder->add($push_msg);

			$msgCount++;

			if( $msgCount == 5 ){
				break;
			}
		}

		//特定の１ユーザーにLINEメッセージを送信
//		$bot->pushMessage($listLineId, new TextMessageBuilder($push_message), true);

		$bot->multicast($list_line_id, $messageBuilder, true);

	}

	/*
	 * 
	 */
	public function sendLineReplyMessage($bot, $basic_id, $line_id, $reply_token, $db_data)
	{
		$messageBuilder = new MultiMessageBuilder();
/*
		if( !empty($db_data->image) ){
			//送信メッセージに画像追加
			$messageBuilder->add(new ImageMessageBuilder(
				config('const.base_url')."/php/line/img/original/{$basic_id}/{$db_data->id}/{$line_id}",
				config('const.base_url')."/php/line/img/preview/{$basic_id}/{$db_data->id}/{$line_id}"
			));
		}
*/
		//送信メッセージにテキスト追加
		if( !empty($db_data->msg1) ){
			$push_msg = trim($db_data->msg1);
//error_log($push_msg."::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");
			if( preg_match("/(\d){5}<>(\d){8}/", $push_msg) > 0 ){
				list($packageId, $stickerId) = explode("<>", $push_msg);
				$push_msg = new StickerMessageBuilder($packageId, $stickerId);

			//イメージマップ
			}elseif( preg_match("/^imglinkmsg\d+_\d+_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
				//画像名とURLに分割
				list($img, $linked_url, $altText) = explode("|", $push_msg);

				$short_url = Utility::getUniqueShortUrl($basic_id, 0, $linked_url, $line_id);
				$linked_url = config('const.base_url').'/line/0/'.$short_url.'/img';
//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//画像サイズ取得
				list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
//error_log("{$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//横幅固定：1040
				$basesize = new BaseSizeBuilder($height, 1040);

				$imagemap_area = new AreaBuilder(0, 0, $width, $height);
				$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
				$listImageMapAct[] = $imagemap_uri_act;

				$push_msg = new ImagemapMessageBuilder(
					config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
					$altText,
					$basesize,
					$listImageMapAct
				);

			}elseif( preg_match("/(\d+|msg\d+_\d+|msg\d+_\d+_\d+)\.(png|jpg|jpeg)/u", $push_msg, $image_id) > 0 ){
				//送信メッセージに画像追加
				$push_msg = new ImageMessageBuilder(
					config('const.base_url')."/php/line/img/original/{$basic_id}/{$push_msg}/{$line_id}",
					config('const.base_url')."/php/line/img/preview/{$basic_id}/{$push_msg}/{$line_id}"
				);
			}else{
				//URLをショートURLへ変換
				$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
					$short_url = Utility::getShortUrl($match[0]);
					return config('const.base_url').'/'.$short_url;
*/
					$short_url = Utility::getUniqueShortUrl($basic_id, 0, $match[0], $line_id);
					return config('const.base_url').'/line/0/'.$short_url;
				}, $push_msg);

				//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
				$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
				$push_msg = new TextMessageBuilder($push_msg);
			}
			$messageBuilder->add($push_msg);
		}

		if( !empty($db_data->msg2) ){
			$push_msg = trim($db_data->msg2);
//error_log($push_msg."::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");
			if( preg_match("/\d{5}<>\d{8}/", $push_msg) > 0 ){
				list($packageId, $stickerId) = explode("<>", $push_msg);
				$push_msg = new StickerMessageBuilder($packageId, $stickerId);

			//イメージマップ
			}elseif( preg_match("/^imglinkmsg\d+_\d+_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
				//画像名とURLに分割
				list($img, $linked_url, $altText) = explode("|", $push_msg);

				$short_url = Utility::getUniqueShortUrl($basic_id, 0, $linked_url, $line_id);
				$linked_url = config('const.base_url').'/line/0/'.$short_url.'/img';
//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//画像サイズ取得
				list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
//error_log("{$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//横幅固定：1040
				$basesize = new BaseSizeBuilder($height, 1040);

				$imagemap_area = new AreaBuilder(0, 0, $width, $height);
				$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
				$listImageMapAct[] = $imagemap_uri_act;

				$push_msg = new ImagemapMessageBuilder(
					config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
					$altText,
					$basesize,
					$listImageMapAct
				);

			}elseif( preg_match("/(\d+|msg\d+_\d+|msg\d+_\d+_\d+)\.(png|jpg|jpeg)/u", $push_msg, $image_id) > 0 ){
				//送信メッセージに画像追加
				$push_msg = new ImageMessageBuilder(
					config('const.base_url')."/php/line/img/original/{$basic_id}/{$push_msg}/{$line_id}",
					config('const.base_url')."/php/line/img/preview/{$basic_id}/{$push_msg}/{$line_id}"
				);
			}else{
				//URLをショートURLへ変換
				$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
					$short_url = Utility::getShortUrl($match[0]);
					return config('const.base_url').'/'.$short_url;
*/
					$short_url = Utility::getUniqueShortUrl($basic_id, 0, $match[0], $line_id);
					return config('const.base_url').'/line/0/'.$short_url;
				}, $push_msg);

				//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
				$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
				$push_msg = new TextMessageBuilder($push_msg);
			}
			$messageBuilder->add($push_msg);
		}

		if( !empty($db_data->msg3) ){
			$push_msg = trim($db_data->msg3);
//error_log($push_msg."::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");
			if( preg_match("/\d{5}<>\d{8}/", $push_msg) > 0 ){
				list($packageId, $stickerId) = explode("<>", $push_msg);
				$push_msg = new StickerMessageBuilder($packageId, $stickerId);

			//イメージマップ
			}elseif( preg_match("/^imglinkmsg\d+_\d+_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
				//画像名とURLに分割
				list($img, $linked_url, $altText) = explode("|", $push_msg);
//error_log("wwwwwwwwww:{$img}, {$linked_url}, {$altText}\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				$short_url = Utility::getUniqueShortUrl($basic_id, 0, $linked_url, $line_id);
				$linked_url = config('const.base_url').'/line/0/'.$short_url.'/img';
//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//画像サイズ取得
				list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
//error_log(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img." {$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//横幅固定：1040
				$basesize = new BaseSizeBuilder($height, 1040);

				$imagemap_area = new AreaBuilder(0, 0, $width, $height);
				$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
				$listImageMapAct[] = $imagemap_uri_act;

				$push_msg = new ImagemapMessageBuilder(
					config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
					$altText,
					$basesize,
					$listImageMapAct
				);

			}elseif( preg_match("/(\d+|msg\d+_\d+|msg\d+_\d+_\d+)\.(png|jpg|jpeg)/u", $push_msg, $image_id) > 0 ){
				//送信メッセージに画像追加
				$push_msg = new ImageMessageBuilder(
					config('const.base_url')."/php/line/img/original/{$basic_id}/{$push_msg}/{$line_id}",
					config('const.base_url')."/php/line/img/preview/{$basic_id}/{$push_msg}/{$line_id}"
				);
			}else{
				//URLをショートURLへ変換
				$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
					$short_url = Utility::getShortUrl($match[0]);
					return config('const.base_url').'/'.$short_url;
*/
					$short_url = Utility::getUniqueShortUrl($basic_id, 0, $match[0], $line_id);
					return config('const.base_url').'/line/0/'.$short_url;
				}, $push_msg);

				//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
				$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
				$push_msg = new TextMessageBuilder($push_msg);
			}
			$messageBuilder->add($push_msg);
		}

		if( !empty($db_data->msg4) ){
			$push_msg = trim($db_data->msg4);
			if( preg_match("/\d{5}<>\d{8}/", $push_msg) > 0 ){
				list($packageId, $stickerId) = explode("<>", $push_msg);
				$push_msg = new StickerMessageBuilder($packageId, $stickerId);

			//イメージマップ
			}elseif( preg_match("/^imglinkmsg\d+_\d+_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
				//画像名とURLに分割
				list($img, $linked_url, $altText) = explode("|", $push_msg);

				$short_url = Utility::getUniqueShortUrl($basic_id, 0, $linked_url, $line_id);
				$linked_url = config('const.base_url').'/line/0/'.$short_url.'/img';
//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//画像サイズ取得
				list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
//error_log("{$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//横幅固定：1040
				$basesize = new BaseSizeBuilder($height, 1040);

				$imagemap_area = new AreaBuilder(0, 0, $width, $height);
				$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
				$listImageMapAct[] = $imagemap_uri_act;

				$push_msg = new ImagemapMessageBuilder(
					config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
					$altText,
					$basesize,
					$listImageMapAct
				);

			}elseif( preg_match("/(\d+|msg\d+_\d+|msg\d+_\d+_\d+)\.(png|jpg|jpeg)/u", $push_msg, $image_id) > 0 ){
				//送信メッセージに画像追加
				$push_msg = new ImageMessageBuilder(
					config('const.base_url')."/php/line/img/original/{$basic_id}/{$push_msg}/{$line_id}",
					config('const.base_url')."/php/line/img/preview/{$basic_id}/{$push_msg}/{$line_id}"
				);
			}else{
				//URLをショートURLへ変換
				$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
					$short_url = Utility::getShortUrl($match[0]);
					return config('const.base_url').'/'.$short_url;
*/
					$short_url = Utility::getUniqueShortUrl($basic_id, 0, $match[0], $line_id);
					return config('const.base_url').'/line/0/'.$short_url;
				}, $push_msg);

				//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
				$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
				$push_msg = new TextMessageBuilder($push_msg);
			}
			$messageBuilder->add($push_msg);
		}

		if( !empty($db_data->msg5) ){
			$push_msg = trim($db_data->msg5);
			if( preg_match("/\d{5}<>\d{8}/", $msg) > 0 ){
				list($packageId, $stickerId) = explode("<>", $push_msg);
				$push_msg = new StickerMessageBuilder($packageId, $stickerId);

			//イメージマップ
			}elseif( preg_match("/^imglinkmsg\d+_\d+_\d+\.(png|jpg|jpeg)\|.+/", $push_msg) > 0 ){
				//画像名とURLに分割
				list($img, $linked_url, $altText) = explode("|", $push_msg);

				$short_url = Utility::getUniqueShortUrl($basic_id, 0, $linked_url, $line_id);
				$linked_url = config('const.base_url').'/line/0/'.$short_url.'/img';
//error_log("{$img}, {$linked_url}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//画像サイズ取得
				list($width, $height, $type, $attr) = getimagesize(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$basic_id.'/img/'.$img);
//error_log("{$width}, {$height}::name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				//横幅固定：1040
				$basesize = new BaseSizeBuilder($height, 1040);

				$imagemap_area = new AreaBuilder(0, 0, $width, $height);
				$imagemap_uri_act = new ImagemapUriActionBuilder($linked_url, $imagemap_area);
				$listImageMapAct[] = $imagemap_uri_act;

				$push_msg = new ImagemapMessageBuilder(
					config('const.base_url')."/php/line/imagemap/{$basic_id}/{$img}",
					$altText,
					$basesize,
					$listImageMapAct
				);

			}elseif( preg_match("/(\d+|msg\d+_\d+|msg\d+_\d+_\d+)\.(png|jpg|jpeg)/u", $push_msg, $image_id) > 0 ){
				//送信メッセージに画像追加
				$push_msg = new ImageMessageBuilder(
					config('const.base_url')."/php/line/img/original/{$basic_id}/{$push_msg}/{$line_id}",
					config('const.base_url')."/php/line/img/preview/{$basic_id}/{$push_msg}/{$line_id}"
				);
			}else{
				//URLをショートURLへ変換
				$push_msg = preg_replace_callback("/https?:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+/", function($match) use($basic_id, $line_id){
//error_log(print_r($match,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
/*
					$short_url = Utility::getShortUrl($match[0]);
					return config('const.base_url').'/'.$short_url;
*/
					$short_url = Utility::getUniqueShortUrl($basic_id, 0, $match[0], $line_id);
					return config('const.base_url').'/line/0/'.$short_url;
				}, $push_msg);

				//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
				$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $push_msg);
				$push_msg = new TextMessageBuilder($push_msg);
			}
			$messageBuilder->add($push_msg);
		}

		//友達追加したユーザーのLINEへメッセージ送信
		$bot->replyMessage($reply_token, $messageBuilder);

		//イベント送信元LINEへメッセージ応答
//			$bot->replyText($reply_token, $reply_message);
	}

	/*
	 * 
	 */
	public function getLinePushMessages($basic_id)
	{
		
	}

	/*
	 * LINE独自の絵文字のUnicodeをプッシュメッセージで絵文字に変換
	 */
	private function _getLineEmotion($list_emotion_unicode)
	{
		$emotion_unicode = preg_replace("/^0x(.+)$/", "$1", $list_emotion_unicode[0]);

		// 16進エンコードされたバイナリ文字列をデコード
		$bin = hex2bin(str_repeat('0', 8 - strlen($emotion_unicode)) . $emotion_unicode);

		// UTF8へエンコード
		$emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');

		return $emoticon;
	}

	/*
	 * 
	 */
	private function _getMakeListLineEmotion()
	{
		$listEmotion = [];

		$listBase16 = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"];

		foreach($listBase16 as $char1){
			$emotion_code = "0x1000".$char1;
			foreach($listBase16 as $char2){
				$listEmotion[] = $emotion_code.$char2;
			}		
		}

		return $listEmotion;
	}

	/*
	 * 
	 */
	public function delClickUser($line_id){
		$delete = Line_click_user::where('user_line_id', $line_id)->delete();
	}
}

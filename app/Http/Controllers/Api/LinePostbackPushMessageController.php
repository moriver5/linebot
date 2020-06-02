<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineOfficialAccount;
use App\Model\Line_2choices_template;
use App\Model\Line_2choices_log;
use App\Model\Line_4choices_template;
use App\Model\Line_4choices_log;
use App\Model\Line_postback_template;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class LinePostbackPushMessageController extends Controller
{
	private $listLineEmotion = [];

	public function __construct()
	{
		$this->listLineEmotion = $this->_getMakeListLineEmotion();
	}

	public function sendReplyLinePostbackTemplate($bot, $channel_id, $reply_token, $event)
	{
		$messageBuilder = new MultiMessageBuilder();

		parse_str($event->getPostbackData(), $list_postback_data);
//error_log(print_r($list_postback_data,true).":name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log($event->getPostbackData().":name12\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//シナリオ
		if( empty($list_postback_data['postback']) ){
			//4択
			if( !empty($list_postback_data['act']) && $list_postback_data['act'] == '4ch' ){
				$db_data = Line_4choices_log::join('line_4choices_templates', 'line_4choices_templates.id', '=', 'line_4choices_logs.master_id')
					->where('line_4choices_templates.id', $list_postback_data['master_id'])
					->where('line_4choices_logs.id', $list_postback_data['scenario'])
					->first();
			//2択
			}else{
				$db_data = Line_2choices_log::join('line_2choices_templates', 'line_2choices_templates.id', '=', 'line_2choices_logs.master_id')
					->where('line_2choices_templates.id', $list_postback_data['master_id'])
					->where('line_2choices_logs.id', $list_postback_data['scenario'])
					->first();
			}

			//アクション選択の格納用配列
			$listAct = [];

			//選択１
			if( !empty($db_data->value1) ){
				if( $db_data->act1 == 1 ){
					//URIアクション
					$listAct[] = new UriTemplateActionBuilder($db_data->label1, $db_data->value1);

				}elseif( $db_data->act1 == 2 ){
					//メッセージアクション
					$listAct[] = new MessageTemplateActionBuilder($db_data->label1, $db_data->value1);

				}elseif( $db_data->act1 == 3 || 
						 $db_data->act1 == 4 ){
					//ポストバックアクション
					$listAct[] = new PostbackTemplateActionBuilder($db_data->label1, $db_data->value1);
				}
			}

			//選択２
			if( !empty($db_data->value2) ){
				if( $db_data->act2 == 1 ){
					//URIアクション
					$listAct[] = new UriTemplateActionBuilder($db_data->label2, $db_data->value2);

				}elseif( $db_data->act2 == 2 ){
					//メッセージアクション
					$listAct[] = new MessageTemplateActionBuilder($db_data->label2, $db_data->value2);

				//ポストバックアクション
				}elseif( $db_data->act2 == 3 || 
						 $db_data->act2 == 4 ){
					$listAct[] = new PostbackTemplateActionBuilder($db_data->label2, $db_data->value2);
				}
			}

			//選択３
			if( !empty($db_data->value3) ){
				if( $db_data->act3 == 1 ){
					//URIアクション
					$listAct[] = new UriTemplateActionBuilder($db_data->label3, $db_data->value3);

				}elseif( $db_data->act3 == 2 ){
					//メッセージアクション
					$listAct[] = new MessageTemplateActionBuilder($db_data->label3, $db_data->value3);

				}elseif( $db_data->act3 == 3 || 
						 $db_data->act3 == 4 ){
					//ポストバックアクション
					$listAct[] = new PostbackTemplateActionBuilder($db_data->label3, $db_data->value3);
				}
			}

			//選択４
			if( !empty($db_data->value4) ){
				if( $db_data->act4 == 1 ){
					//URIアクション
					$listAct[] = new UriTemplateActionBuilder($db_data->label4, $db_data->value4);

				}elseif( $db_data->act4 == 2 ){
					//メッセージアクション
					$listAct[] = new MessageTemplateActionBuilder($db_data->label4, $db_data->value4);

				}if( $db_data->act4 == 3 || 
					 $db_data->act4 == 4 ){
					//ポストバックアクション
					$listAct[] = new PostbackTemplateActionBuilder($db_data->label4, $db_data->value4);
				}
			}

			//４択
			if( !empty($list_postback_data['act']) && $list_postback_data['act'] == '4ch' ){
				$image_id = preg_replace("/(button_\d+)\.(jpg|jpeg|png)/", "$1", $db_data->img);

				// ボタンテンプレートを作る
				// 引数：タイトル、シナリオメッセージ、画像URL、アクション選択の配列、画像比率、画像サイズ
				$templateBuilder = new ButtonTemplateBuilder($db_data->push_title, $db_data->msg, config('const.base_url')."/php/line/push/img/button/{$channel_id}/{$image_id}/{$list_postback_data['master_id']}", $listAct, $db_data->img_ratio, $db_data->img_size);

			//２択
			}else{
				// Confirmテンプレートを作る
				$templateBuilder = new ConfirmTemplateBuilder($db_data->msg, [$action1, $action2]);
			}

			$message = new TemplateMessageBuilder($db_data->push_title, $templateBuilder);
			$messageBuilder->add($message);

		//postback
		}else{
			$db_data = Line_postback_template::where('line_basic_id', $channel_id)->where('id', $list_postback_data['postback'])->first();

			$img_prefix = config('const.postback_img_prefix');

			//送信メッセージにテキスト追加
			if( !empty($db_data->msg1) ){
				$msg = trim($db_data->msg1);
//error_log($msg."::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				if( preg_match("/(\d){5}<>(\d){8}/", $msg) > 0 ){
					list($packageId, $stickerId) = explode("<>", $msg);
					$push_msg = new StickerMessageBuilder($packageId, $stickerId);
				}elseif( preg_match("/({$img_prefix}msg\d+_\d+)\.(png|jpg|jpeg)/u", $msg, $image_id) > 0 ){
					//送信メッセージに画像追加
					$push_msg = new ImageMessageBuilder(
						config('const.base_url')."/php/line/push/img/postback/original/{$channel_id}/img1/{$db_data->id}",
						config('const.base_url')."/php/line/push/img/postback/preview/{$channel_id}/img1/{$db_data->id}"
					);
				}else{
					//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
					$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $db_data->msg1);
					$push_msg = new TextMessageBuilder($push_msg);
				}
				$messageBuilder->add($push_msg);
			}

			if( !empty($db_data->msg2) ){
				$msg = trim($db_data->msg2);
//error_log($msg."::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				if( preg_match("/\d{5}<>\d{8}/", $msg) > 0 ){
					list($packageId, $stickerId) = explode("<>", $msg);
					$push_msg = new StickerMessageBuilder($packageId, $stickerId);
				}elseif( preg_match("/({$img_prefix}msg\d+_\d+)\.(png|jpg|jpeg)/u", $msg, $image_id) > 0 ){
//error_log(print_r($image_id,true)."::dddd\n",3,"/data/www/line/storage/logs/nishi_log.txt");
					//送信メッセージに画像追加
					$push_msg = new ImageMessageBuilder(
						config('const.base_url')."/php/line/push/img/postback/original/{$channel_id}/img2/{$db_data->id}",
						config('const.base_url')."/php/line/push/img/postback/preview/{$channel_id}/img2/{$db_data->id}"
					);
				}else{
					//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
					$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $db_data->msg2);
					$push_msg = new TextMessageBuilder($push_msg);
				}
				$messageBuilder->add($push_msg);
			}

			if( !empty($db_data->msg3) ){
				$msg = trim($db_data->msg3);
				if( preg_match("/\d{5}<>\d{8}/", $msg) > 0 ){
					list($packageId, $stickerId) = explode("<>", $msg);
					$push_msg = new StickerMessageBuilder($packageId, $stickerId);
				}elseif( preg_match("/({$img_prefix}msg\d+_\d+)\.(png|jpg|jpeg)/u", $msg, $image_id) > 0 ){
					//送信メッセージに画像追加
					$push_msg = new ImageMessageBuilder(
						config('const.base_url')."/php/line/push/img/postback/original/{$channel_id}/img3/{$db_data->id}",
						config('const.base_url')."/php/line/push/img/postback/preview/{$channel_id}/img3/{$db_data->id}"
					);
				}else{
					//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
					$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $db_data->msg3);
					$push_msg = new TextMessageBuilder($push_msg);
				}
				$messageBuilder->add($push_msg);
			}

			if( !empty($db_data->msg4) ){
				$msg = trim($db_data->msg4);
				if( preg_match("/\d{5}<>\d{8}/", $msg) > 0 ){
					list($packageId, $stickerId) = explode("<>", $msg);
					$push_msg = new StickerMessageBuilder($packageId, $stickerId);
				}elseif( preg_match("/({$img_prefix}msg\d+_\d+)\.(png|jpg|jpeg)/u", $msg, $image_id) > 0 ){
					//送信メッセージに画像追加
					$push_msg = new ImageMessageBuilder(
						config('const.base_url')."/php/line/push/img/postback/original/{$channel_id}/img4/{$db_data->id}",
						config('const.base_url')."/php/line/push/img/postback/preview/{$channel_id}/img4/{$db_data->id}"
					);
				}else{
					//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
					$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $db_data->msg4);
					$push_msg = new TextMessageBuilder($push_msg);
				}
				$messageBuilder->add($push_msg);
			}

			if( !empty($db_data->msg5) ){
				$msg = trim($db_data->msg5);
				if( preg_match("/\d{5}<>\d{8}/", $msg) > 0 ){
					list($packageId, $stickerId) = explode("<>", $msg);
					$push_msg = new StickerMessageBuilder($packageId, $stickerId);
				}elseif( preg_match("/({$img_prefix}msg\d+_\d+)\.(png|jpg|jpeg)/u", $msg, $image_id) > 0 ){
					//送信メッセージに画像追加
					$push_msg = new ImageMessageBuilder(
						config('const.base_url')."/php/line/push/img/postback/original/{$channel_id}/img5/{$db_data->id}",
						config('const.base_url')."/php/line/push/img/postback/preview/{$channel_id}/img5/{$db_data->id}"
					);
				}else{
					//LINE独自の絵文字のUnicodeが含まれていれば絵文字に変換
					$push_msg = preg_replace_callback("/".implode("|", $this->listLineEmotion)."/", array($this, '_getLineEmotion'), $db_data->msg5);
					$push_msg = new TextMessageBuilder($push_msg);
				}
				$messageBuilder->add($push_msg);
			}
		}

		$response = $bot->replyMessage($reply_token, $messageBuilder);

		//エラー
		if( !$response->isSucceeded() ){
			error_log('Failed! '. $response->getRawBody(),3,"/data/www/line/storage/logs/nishi_log.txt");
		}
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
}

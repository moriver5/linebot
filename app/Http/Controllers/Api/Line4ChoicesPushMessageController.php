<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineOfficialAccount;
use App\Model\Line_4choices_template;
use App\Model\Line_4choices_log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class Line4ChoicesPushMessageController extends Controller
{
	public function __construct()
	{

	}


	/*
	 *  ボタンテンプレートを配信する
	 */
	public function sendLine4ChoicesTemplate($bot, $channel_id, $push_line_id, $list_line_id, $db_data = [])
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

		$image_id = preg_replace("/(button_\d+)\.(jpg|jpeg|png)/", "$1", $db_data->img);

		// ボタンテンプレートを作る
		$button = new ButtonTemplateBuilder($db_data->push_title, $db_data->msg, config('const.base_url')."/php/line/push/img/button/{$channel_id}/{$image_id}/{$push_line_id}", $listAct, $db_data->img_ratio, $db_data->img_size);

		// ボタンメッセージを作る
		$button_message = new TemplateMessageBuilder($db_data->push_title, $button);

		$messageBuilder = new MultiMessageBuilder();
		$messageBuilder->add($button_message);

		$response = $bot->multicast($list_line_id, $messageBuilder, true);

		//エラー
		if( !$response->isSucceeded() ){
			error_log('Failed! '. $response->getRawBody(),3,"/data/www/line/storage/logs/nishi_log.txt");
		}
	}

	/*
	 * 　ユーザーから確認テンプレートのアクションに応じた
	 * 　確認テンプレートをユーザーへ配信する
	 */
	public function sendReplyLine4ChoicesTemplate($bot, $channel_id, $reply_token, $event)
	{
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();
//error_log(print_r($event->getPostbackData(),true).":name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		parse_str($event->getPostbackData(), $list_postback_data);

		$db_data = Line_4choices_template::join('line_4choices_logs', 'line_4choices_logs.master_id', '=', 'line_4choices_templates.id')
			->where('line_4choices_logs.master_id', $list_postback_data['master_id'])
			->where('line_4choices_logs.id', $list_postback_data['next_id'])
			->first();

		//アクション選択の格納用配列
		$listAct = [];

		//選択１
		if( $db_data->act1 == 1 ){
			//URIアクション
			$listAct[] = new UriTemplateActionBuilder($db_data->label1, $db_data->value1);

		}elseif( $db_data->act1 == 2 ){
			//メッセージアクション
			$listAct[] = new MessageTemplateActionBuilder($db_data->label1, $db_data->value1);

		}elseif( $db_data->act1 == 3 ){
			//ポストバックアクション
			$listAct[] = new PostbackTemplateActionBuilder($db_data->label1, $db_data->value1);
		}

		//選択２
		if( $db_data->act2 == 1 ){
			//URIアクション
			$listAct[] = new UriTemplateActionBuilder($db_data->label2, $db_data->value2);
		}elseif( $db_data->act2 == 2 ){
			//メッセージアクション
			$listAct[] = new MessageTemplateActionBuilder($db_data->label2, $db_data->value2);
		}elseif( $db_data->act2 == 3 ){
			//ポストバックアクション
			$listAct[] = new PostbackTemplateActionBuilder($db_data->label2, $db_data->value2);
		}

		// Confirmテンプレートを作る
		$confirm = new ConfirmTemplateBuilder($db_data->msg, $listAct);

		// Confirmメッセージを作る
		$confirm_message = new TemplateMessageBuilder($db_data->push_title, $confirm);

		$messageBuilder = new MultiMessageBuilder();
		$messageBuilder->add($confirm_message);

		$response = $bot->replyMessage($reply_token, $messageBuilder);

		//エラー
		if( !$response->isSucceeded() ){
			error_log('Failed! '. $response->getRawBody(),3,"/data/www/line/storage/logs/nishi_log.txt");
		}
 
	}

}

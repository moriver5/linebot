<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineOfficialAccount;
use App\Model\Line_2choices_template;
use App\Model\Line_2choices_log;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class Line2ChoicesPushMessageController extends Controller
{
	public function __construct()
	{

	}


	/*
	 *  確認テンプレートを配信する
	 */
	public function sendLine2ChoicesTemplate($bot, $channel_id, $push_line_id, $list_line_id, $db_data = [])
	{
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();
//error_log(print_r($db_data,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log(print_r($list_line_id,true)."name\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//error_log("{$push_line_id}:name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
				['channelSecret' => $line_account->line_channel_secret]
			);

		//URIアクション
		if( $db_data->act1 == 1 ){
			$action1 = new UriTemplateActionBuilder($db_data->label1, $db_data->value1);

		}
		//メッセージアクション
		if( $db_data->act1 == 2 ){
			$action1 = new MessageTemplateActionBuilder($db_data->label1, $db_data->value1);

		}
		//ポストバックアクション
		if( $db_data->act1 == 3 || 
			$db_data->act1 == 4 ){
			$action1 = new PostbackTemplateActionBuilder($db_data->label1, $db_data->value1);

		}
		//URIアクション
		if( $db_data->act2 == 1 ){
			$action2 = new UriTemplateActionBuilder($db_data->label2, $db_data->value2);

		}
		//メッセージアクション
		if( $db_data->act2 == 2 ){
			$action2 = new MessageTemplateActionBuilder($db_data->label2, $db_data->value2);

		}
		//ポストバックアクション
		if( $db_data->act2 == 3 || 
			$db_data->act2 == 4 ){
			$action2 = new PostbackTemplateActionBuilder($db_data->label2, $db_data->value2);

		}

		// Confirmテンプレートを作る
		$confirm = new ConfirmTemplateBuilder($db_data->msg, [$action1, $action2]);

		// Confirmメッセージを作る
		$confirm_message = new TemplateMessageBuilder($db_data->push_title, $confirm);

		$messageBuilder = new MultiMessageBuilder();
		$messageBuilder->add($confirm_message);

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
	public function sendReplyLine2ChoicesTemplate($bot, $channel_id, $reply_token, $event)
	{
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();
//error_log(print_r($event->getPostbackData(),true).":name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		parse_str($event->getPostbackData(), $list_postback_data);

		$db_data = Line_2choices_template::join('line_2choices_logs', 'line_2choices_logs.master_id', '=', 'line_2choices_templates.id')
			->where('line_2choices_logs.master_id', $list_postback_data['master_id'])
			->where('line_2choices_logs.id', $list_postback_data['next_id'])
			->first();

		//URIアクション
		if( $db_data->act1 == 1 ){
			$action1 = new UriTemplateActionBuilder($db_data->label1, $db_data->value1);

		}
		//メッセージアクション
		if( $db_data->act1 == 2 ){
			$action1 = new MessageTemplateActionBuilder($db_data->label1, $db_data->value1);

		}
		//ポストバックアクション
		if( $db_data->act1 == 3 ){
			$action1 = new PostbackTemplateActionBuilder($db_data->label1, $db_data->value1);

		}
		//メッセージアクション
		if( $db_data->act2 == 1 ){
			$action2 = new UriTemplateActionBuilder($db_data->label2, $db_data->value2);

		}
		//ポストバックアクション
		if( $db_data->act2 == 2 ){
			$action2 = new MessageTemplateActionBuilder($db_data->label2, $db_data->value2);

		}
		if( $db_data->act2 == 3 ){
			$action2 = new PostbackTemplateActionBuilder($db_data->label2, $db_data->value2);

		}

		// Confirmテンプレートを作る
		$confirm = new ConfirmTemplateBuilder($db_data->msg, [$action1, $action2]);

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

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LineOfficialAccount;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class LineConfirmPushMessageController extends Controller
{
	public function __construct()
	{

	}

	/*
	 *  確認テンプレートを配信する
	 */
	public function sendLineConfirmTemplate($channel_id)
	{
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
				['channelSecret' => $line_account->line_channel_secret]
			);

		// 「はい」ボタン
		$yes_post = new PostbackTemplateActionBuilder("ドミノピザ", "1");
		
		// 「いいえ」ボタン
		$no_post = new PostbackTemplateActionBuilder("ピザーラ", "2");
		
		// Confirmテンプレートを作る
		$confirm = new ConfirmTemplateBuilder("どちらのピザ屋さんが好きですか？？？", [$yes_post, $no_post]);
		
		// Confirmメッセージを作る
		$confirm_message = new TemplateMessageBuilder("質問です。。", $confirm);

		$message = new MultiMessageBuilder();
		$message->add($confirm_message);

//		$bot->replyMessage($line_account->line_token, $message);
		$bot->multicast(['Ub0f5b61bde8379b913a52bb3299c3123'], $message, true);
	}

	/*
	 * 　ユーザーから確認テンプレートのアクションに応じた
	 * 　確認テンプレートをユーザーへ配信する
	 */
	public function sendReplyLineConfirmTemplate($channel_id, $event)
	{
		$line_account = LineOfficialAccount::where('line_basic_id', $channel_id)->first();

		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
				['channelSecret' => $line_account->line_channel_secret]
			);

		// 「はい」ボタン
		$yes_post = new PostbackTemplateActionBuilder("エビマヨネーズ", "page=1");
		
		// 「いいえ」ボタン
		$no_post = new PostbackTemplateActionBuilder("ギガミート", "page=-1");
		
		// Confirmテンプレートを作る
		$confirm = new ConfirmTemplateBuilder("ドミノピザの中でどちらが好きですか？？？？", [$yes_post, $no_post]);
		
		// Confirmメッセージを作る
		$confirm_message = new TemplateMessageBuilder("次の質問です。。。", $confirm);

		$message = new MultiMessageBuilder();
		$message->add($confirm_message);

//		$bot->replyMessage($line_account->line_token, $message);
		$bot->multicast(['Ub0f5b61bde8379b913a52bb3299c3123'], $message, true);
	}
}

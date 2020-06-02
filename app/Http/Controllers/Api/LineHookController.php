<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\LinePushMessageController;
use App\Http\Controllers\Api\LinePostbackPushMessageController;
use App\Services\Line\FollowService;
use App\Services\Line\RecieveTextService;
use App\Http\Controllers\Api\LineConfirmPushMessageController;
use App\Model\LineOfficialAccount;
use App\Model\LineContents;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineHookController extends Controller
{
	private $push_msg;

	public function __construct(Request $request)
	{
		$this->push_msg = new LinePushMessageController();
	}

	public function callback(Request $request, $basic_id = null)
	{
//error_log("{$basic_id}:test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		//リクエストがあったチャンネル情報をDBから取得
		$line_account = LineOfficialAccount::where('line_basic_id', $basic_id)->first();
//error_log(print_r($line_account,true).":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		//DBに登録チャンネルがないとき
		if( !isset($line_account->line_basic_id) ){
			return;
		}

		$access_token = $line_account->line_token;
		$channel_secret = $line_account->line_channel_secret;

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($access_token),
				['channelSecret' => $channel_secret]
			);

		$signature = $_SERVER['HTTP_'.LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

		if (!LINEBot\SignatureValidator::validateSignature($request->getContent(), $channel_secret, $signature)) {
			abort(400);
		}

		$line_id = "";

		$events = $bot->parseEventRequest($request->getContent(), $signature);
		foreach ($events as $event) {
//error_log(print_r($event,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

			$reply_token = $event->getReplyToken();
			$reply_message = 'その操作はサポートしてません。.[' . get_class($event) . '][' . $event->getType() . ']';
			$line_id = $event->getUserId();

			//Webhookイベントの分岐
			switch (true){
				//友達登録
				case $event instanceof LINEBot\Event\FollowEvent:
error_log("FollowEvent\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
					//line_click_userにある以前のデータを削除
					$this->push_msg->delClickUser($line_id);

					$service = new FollowService($bot, $basic_id);

					$service->updateTrackAccess($basic_id, $line_id);

					$reply_message = $service->followExecute($event);

					$db_data = LineContents::where('line_basic_id', $basic_id)->where('type', 2)->first();

					$this->push_msg->sendLineReplyMessage($bot, $basic_id, $line_id, $reply_token, $db_data);

					break;

				//友達ブロック
				case $event instanceof LINEBot\Event\UnfollowEvent:
error_log("UnFollowEvent\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

					$service = new FollowService($bot, $basic_id);
					$reply_message = $service->unfollowExecute($event)
						? "友達登録ありがとうございますm(_ _)m"
						: '友達登録されたけど、登録処理に失敗したから、何もしないよ';

					$this->push_msg->sendLineReplyMessage($bot, $basic_id, $line_id, $reply_token, $reply_message);

					break;

				//テキストメッセージの受信
				case $event instanceof LINEBot\Event\MessageEvent\TextMessage:
error_log("TextMessage\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

					$service = new RecieveTextService($bot, $basic_id);
					$service->recieveMessageExecute($event);

					$db_data = LineContents::where('line_basic_id', $basic_id)->where('type', 1)->first();

					$this->push_msg->sendLineReplyMessage($bot, $basic_id, $line_id, $reply_token, $db_data);

					break;

				//画像メッセージの受信
				case $event instanceof LINEBot\Event\MessageEvent\ImageMessage:
error_log("ImageMessage\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
/*
					$service = new RecieveTextService($bot, $basic_id);
					$reply_message = $service->recieveMessageExecute($event);

					$this->push_msg->sendLineReplyMessage($bot, $basic_id, $line_id, $reply_token, $reply_message);
*/
					break;

				//位置情報の受信
				case $event instanceof LINEBot\Event\MessageEvent\LocationMessage:
error_log("LocationMessage\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
/*
					$service = new RecieveLocationService($bot);
					$reply_message = $service->execute($event);
 */
					$this->push_msg->sendLineReplyMessage($bot, $basic_id, $line_id, $reply_token, $reply_message);
					break;

				//選択肢とか選んだ時に受信するイベント(postback)
				case $event instanceof LINEBot\Event\PostbackEvent:
error_log("PostbackEvent\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log(print_r($event->getEventBody(),true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//					$confirm_obj = new Line2ChoicesPushMessageController();
//					$confirm_obj->sendReplyLine2ChoicesTemplate($bot, $basic_id, $reply_token, $event);
					$postback_obj = new LinePostbackPushMessageController();
					$postback_obj->sendReplyLinePostbackTemplate($bot, $basic_id, $reply_token, $event);
					break;

				default:
//error_log("default\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//					$body = $event->getEventBody();
			}
		}
	}
}

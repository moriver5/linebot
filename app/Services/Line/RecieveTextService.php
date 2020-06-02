<?php
namespace App\Services\Line;
use App\Model\LineUser;
use App\Model\LineUserProfile;
use App\Model\LineUserMessage;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use DB;
use PDO;

class RecieveTextService
{
    /**
     * @var LINEBot
     */
    private $bot;
	private $basic_id;

    /**
     * Follow constructor.
     * @param LINEBot $bot
     */
    public function __construct(LINEBot $bot, $basic_id)
    {
        $this->bot = $bot;
		$this->basic_id = $basic_id;
    }

    /**
     * 登録
     * @param FollowEvent $event
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function recieveMessageExecute(TextMessage $event)
    {
		try {
			$dbh = DB::connection('mysql')->getPdo();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策
//			throw new \PDOException("テスト例外エラー");
		} catch (\PDOException $e) {
//error_log("pdo error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			return false;
		}

//error_log(print_r($event,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log($event->getType().":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

		$reply_token = $event->getReplyToken();
		$line_id = $event->getUserId();
		$msg_text = $event->getText();

        try {
            $dbh->beginTransaction();

			$rsp = $this->bot->getProfile($line_id);

			if ( !$rsp->isSucceeded() ) {
//error_log("isSucceeded\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
				$dbh->commit();

				return false;
			}else{

//error_log("1\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
				$line_user_message = new LineUserMessage([
					'line_basic_id'	 => $this->basic_id,
					'user_line_id'	 => $line_id,
					'act_flg'		 => 3,
					'reply_token'	 => $reply_token,
					'msg'			 => $msg_text,
				]);

				$line_user_message->save();

				$dbh->commit();

				return true;
			}

        } catch (Exception $e) {
            $dbh->rollback();
            return false;
        }
 
    }
}
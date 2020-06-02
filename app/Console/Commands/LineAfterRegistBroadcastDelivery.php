<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\LinePushMessageController;
use App\Model\LineOfficialAccount;
use App\Model\LinePushLog;
use App\Model\LineUser;
use App\Model\Registered_msg_queue;
use LINE\LINEBot;
use Carbon\Carbon;
use DB;

class LineAfterRegistBroadcastDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:after_regist_broadcast_delivery {line_push_id} {after_minute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		//毎日定時配信のデータを取得
		$db_push_data = LinePushLog::where('id', $this->argument('line_push_id'))->first();
//error_log(print_r($db_push_data,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		//メルマガ配信先リスト取得
		$db_user_data = DB::select('select distinct registered_msg_queues.user_line_id from line_users inner join line_push_logs on line_push_logs.line_basic_id = line_users.line_basic_id inner join registered_msg_queues on registered_msg_queues.line_push_id = line_push_logs.id where line_users.follow_flg = 1 and line_users.disable = 0 and registered_msg_queues.line_push_id = '.$this->argument('line_push_id').' and line_push_logs.line_basic_id = "'.$db_push_data->line_basic_id.'" and line_push_logs.updated_at <= line_users.updated_at and now() >= (registered_msg_queues.created_at + INTERVAL '.$this->argument('after_minute').' minute)');
//error_log($this->argument('after_minute').":".print_r($db_user_data,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//return false;
		if( !empty($db_user_data) ){
			//現在時刻
			$now_date = Carbon::now();

			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

			$list_line_id = [];
			foreach($db_user_data as $lines){
				$list_line_id[] = $lines->user_line_id;
//error_log("LINE ID：".$lines->user_line_id."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

				DB::insert('insert ignore into line_messages_history_logs(line_push_id, user_line_id, sort_date, created_at, updated_at) values('
				.$this->argument('line_push_id').',"'
				.$lines->user_line_id.'",'
				.$sort_date.',"'
				.$now_date.'","'
				.$now_date.'")');
			}

			$line_account = LineOfficialAccount::where('line_basic_id', $db_push_data->line_basic_id)->first();

			//DBに登録チャンネルがないとき
			if( !isset($line_account->line_basic_id) ){
				return;
			}

			$bot = new LINEBot(
					new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
					['channelSecret' => $line_account->line_channel_secret]
				);

			$push_msg_obj = new LinePushMessageController();

			$push_image = "";

			//画像があれば
			if( !empty($db_push_data->image) ){
				$push_image = $db_push_data->image;
			}

			$list_push_msg = [];
			$list_push_msg['msg1'] = $db_push_data->msg1;

			if( !empty($db_push_data->msg2) ){
				$list_push_msg['msg2'] = $db_push_data->msg2;
			}

			if( !empty($db_push_data->msg3) ){
				$list_push_msg['msg3'] = $db_push_data->msg3;
			}

			if( !empty($db_push_data->msg4) ){
				$list_push_msg['msg4'] = $db_push_data->msg4;
			}

			if( !empty($db_push_data->msg5) ){
				$list_push_msg['msg5'] = $db_push_data->msg5;
			}

			//LINEメッセージ配信
			$push_msg_obj->sendLineOneUserPushMessage($bot, $db_push_data->line_basic_id, $this->argument('line_push_id'), $list_line_id, $list_push_msg, $push_image);

			foreach($list_line_id as $user_line_id){
//error_log("PUSH ID：".$this->argument('line_push_id')." LINE ID：".$user_line_id."\n",3,"/data/www/line/storage/logs/nishi_log.txt");
				Registered_msg_queue::where('line_push_id', $this->argument('line_push_id'))->where('user_line_id', $user_line_id)->delete();
			}
		}
    }
}

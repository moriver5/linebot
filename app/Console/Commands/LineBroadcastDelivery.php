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

class LineBroadcastDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:broadcast_delivery {line_push_id} {send_type?}';

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
		//現在時刻
		$now_date = Carbon::now();

		//毎日定時配信のデータを取得
		$db_push_data = LinePushLog::where('id', $this->argument('line_push_id'))->first();

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//メルマガ配信先リスト取得
		$db_user_data = LineUser::select(['line_users.id', 'line_users.user_line_id'])
			->where('line_basic_id', $db_push_data->line_basic_id)
			->where('follow_flg', 1)
			->where('disable', 0)
			->get();

		$list_line_id = [];
		foreach($db_user_data as $lines){
			$list_line_id[] = $lines->user_line_id;

			DB::insert('insert ignore into line_messages_history_logs(line_push_id, user_line_id, sort_date, created_at, updated_at) values('
			.$this->argument('line_push_id').',"'
			.$lines->user_line_id.'",'
			.$sort_date.',"'
			.$now_date.'","'
			.$now_date.'")');
		}

		if( !empty($db_user_data) ){
			if( $this->argument('send_type') != 4 ){
				//現在時刻
				$now_date = Carbon::now();

				//履歴を残す以外(send_status:4以外)
				//メルマガ配信日時 配信状況：1(送信中)
				$update = LinePushLog::where('id', $this->argument('line_push_id'))
					->update([
						'send_date' => $now_date,
						'send_status' => 1]);
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
			$push_msg_obj->sendLinePushMessage($bot, $db_push_data->line_basic_id, $this->argument('line_push_id'), $list_line_id, $list_push_msg, $push_image);

			if( $this->argument('send_type') != 4 ){
				//履歴を残す以外(send_status:4以外)
				//メルマガ配信状況の更新→配信済:send_status→2
				$update = LinePushLog::where('id', $this->argument('line_push_id'))->update(['send_status' => 2, 'send_count' => count($list_line_id)]);
			}else{
				foreach($list_line_id as $user_line_id){
					Registered_msg_queue::where('line_push_id', $this->argument('line_push_id'))->where('user_line_id', $user_line_id)->delete();
				}
			}
		}
    }
}

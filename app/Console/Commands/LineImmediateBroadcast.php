<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\LinePushMessageController;
use App\Model\LineOfficialAccount;
use App\Model\LinePushLog;
use App\Model\LineTempImmediateMsg;
use LINE\LINEBot;
use Carbon\Carbon;

class LineImmediateBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:broadcast {line_basic_id} {line_push_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '友達登録しているユーザーのLINEへメッセージ配信';

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
		$db_data = LineTempImmediateMsg::select(['user_line_id'])
			->where('line_push_id', $this->argument('line_push_id'))
			->get();
//error_log(print_r($db_data,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		if( !empty($db_data) ){
			$now_date = Carbon::now();

			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

			//履歴を残す(send_status:4以外)
			//メルマガ配信日時 配信状況：1(送信中)
			$update = LinePushLog::where('id', $this->argument('line_push_id'))
				->update([
					'send_date' => $now_date,
					'send_status' => 1]);

			$list_line_id = [];
			foreach($db_data as $lines){
/*
				DB::insert('insert ignore into melmaga_history_logs(melmaga_id, client_id, sort_date, created_at, updated_at) values('
				.$this->argument('melmaga_id').','
				.$lines->id.','
				.$sort_date.',"'
				.$now_date.'","'
				.$now_date.'")');

				$err_flg = Utility::checkNgWordEmail($lines->user_line_id);

				//禁止ワードが含まれていたら
				if( !is_null($err_flg) ){
					continue;
				}
 */
				$list_line_id[] = $lines->user_line_id;
			}

			$line_account = LineOfficialAccount::where('line_basic_id', $this->argument('line_basic_id'))->first();

			//DBに登録チャンネルがないとき
			if( !isset($line_account->line_basic_id) ){
				return;
			}

			$bot = new LINEBot(
					new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
					['channelSecret' => $line_account->line_channel_secret]
				);

			$push_msg_obj = new LinePushMessageController();

			//LINE配信内容を取得
			$db_push_data = LinePushLog::where('id', $this->argument('line_push_id'))->first();
//error_log(print_r($db_push_data,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

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

			//履歴を残す以外(send_status:4以外)
			//メルマガ配信状況の更新→配信済:send_status→1
			$update = LinePushLog::where('id', $this->argument('line_push_id'))->update(['send_count' => count($db_data), 'send_status' => 2]);

		}
    }
}

<?php

namespace App\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Console\Command;
use App\Model\LinePushLog;
use Carbon\Carbon;
use DB;

class LineAfterRegistBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:after_regist_broadcast';

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

		//配信予定日時を過ぎた配信待ちのデータ取得
//		$db_data = DB::select('select distinct line_push_logs.id from registered_msg_queues inner join line_users on registered_msg_queues.user_line_id = line_users.user_line_id inner join line_push_logs on registered_msg_queues.line_push_id = line_push_logs.id where line_users.follow_flg = 1 and DATE_ADD(date(registered_msg_queues.created_at), INTERVAL line_push_logs.send_after_day DAY) = curdate() and line_push_logs.send_status = 0 and line_push_logs.send_type = 4 and subtime(line_push_logs.send_regular_time, "'.$now_date->hour.':'.$now_date->minute.'") < 0 and subtime(line_push_logs.send_regular_time, "'.$now_date->hour.':'.$now_date->minute.'") < "-'.config('const.after_regist_time').'"');
		$db_data = DB::select('select distinct line_push_logs.id, line_push_logs.send_after_minute from registered_msg_queues inner join line_users on registered_msg_queues.user_line_id = line_users.user_line_id inner join line_push_logs on registered_msg_queues.line_push_id = line_push_logs.id where line_users.follow_flg = 1 and line_push_logs.send_status = 0 and line_push_logs.send_type = 4 and line_push_logs.updated_at <= now() and now() >= (line_users.created_at + INTERVAL line_push_logs.send_after_minute minute)');
//error_log(print_r($db_data,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//配信予定日時を過ぎ、配信状況待ちのデータあれば
		if( !empty($db_data) ){
			//配列からメルマガIDを取り出す
			foreach($db_data as $lines){
//error_log("プッシュID：{$lines->id} 経過時間：{$lines->send_after_minute}\n",3,"/data/www/line/storage/logs/nishi_log.txt");

				//別プロセスでメルマガIDごとに配信
				$process = new Process(config('const.artisan_command_path')." line:after_regist_broadcast_delivery {$lines->id} {$lines->send_after_minute} > /dev/null");

				//非同期実行(/data/www/line/app/Console/Commands/LineEverydayBroadcastDelivery.php)
				$process->start();

				//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
				//1.5秒待機
				usleep(1500000);
			}
		}
    }
}

<?php

namespace App\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Console\Command;
use App\Model\LinePushLog;
use Carbon\Carbon;
use DB;

class LineEverydayBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:everyday_broadcast';

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

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//配信予定日時を過ぎた配信待ちのデータ取得
		$db_data = DB::select('select * from line_push_logs where send_status = 0 and send_type = 2 and subtime(send_regular_time, "'.$now_date->hour.':'.$now_date->minute.'") < 0 and subtime(line_push_logs.send_regular_time, "'.$now_date->hour.':'.$now_date->minute.'") < "-'.config('const.after_regist_time').'"');
//error_log(print_r($db_data,true)."\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//配信予定日時を過ぎ、配信状況待ちのデータあれば
		if( !empty($db_data) ){
			//配列からメルマガIDを取り出す
			foreach($db_data as $lines){
				//別プロセスでメルマガIDごとに配信
				$process = new Process(config('const.artisan_command_path')." line:broadcast_delivery {$lines->id} > /dev/null");

				//非同期実行(/data/www/line/app/Console/Commands/LineEverydayBroadcastDelivery.php)
				$process->start();

				//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
				//1.5秒待機
				usleep(1500000);
			}
		}

	}
}

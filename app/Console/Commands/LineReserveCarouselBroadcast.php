<?php

namespace App\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Console\Command;
use App\Model\Line_carousel_template;
use Carbon\Carbon;

class LineReserveCarouselBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:reserve_carousel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '友達登録しているユーザーのLINEへカルーセルテンプレートを予約配信';

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
		$db_data = Line_carousel_template::query()
			->where('sort_reserve_send_date', '<=', $sort_date)
			->where('send_type', 1)
			->where('send_status', 0)
			->orderBy('sort_reserve_send_date', 'desc')
			->get();

		//配信予定日時を過ぎ、配信状況待ちのデータあれば
		if( !empty($db_data) ){
			foreach($db_data as $lines){
				//別プロセスでメルマガIDごとに配信
				$process = new Process(config('const.artisan_command_path')." line:reserve_carousel_broadcast_delivery {$lines->id} > /dev/null");

				//非同期実行(/data/www/melmaga/app/Console/Commands/MelmagaReserveSendDelivery.php)
				$process->start();

				//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
				//1.5秒待機
				usleep(1500000);
			}
		}

    }
}

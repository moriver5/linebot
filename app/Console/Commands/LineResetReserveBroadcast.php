<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\LinePushLog;
use Carbon\Carbon;
use DB;

class LineResetReserveBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'line:reset_reserve_broadcast';

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
		//毎日定時配信をリセット
		LinePushLog::where('send_status', 2)->whereIn('send_type', [2, 3])->update([
			'send_count'	=> 0,
			'send_status'	=> 0,
			'send_date'		=> null
		]);
    }
}

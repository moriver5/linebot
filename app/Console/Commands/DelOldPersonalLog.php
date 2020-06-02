<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DelOldPersonalLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'personal_logs:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '180日間を過ぎたパーソナルログを削除';

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
        //180日間を過ぎたログを削除
		$delete = DB::delete("delete from personal_access_logs where now() >= (created_at + interval ".config('const.personal_access_logs_expire_minute')." minute)");

    }
}

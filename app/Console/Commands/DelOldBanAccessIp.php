<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DelOldBanAccessIp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ban_access_ips:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1日以上経過したアクセスIPを削除';

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
        //1日以上経過したデータを削除
		$delete = DB::delete("delete from ban_access_ips where now() >= (created_at + interval ".config('const.del_ban_access_ip_inserval').")");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class MakeEncryptPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:make {password?} {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '管理者のパスワードを生成する。オプションに管理者IDを指定すると生成されたパスワードでDBのパスワードが更新される。';

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
		//オプションにパスワードが指定されていたら
		if( !empty($this->argument("password")) ){
			$password_raw	 = $this->argument("password");		
		}else{
			//パスワードを生成
			$password_raw	 = str_random(config('const.password_length'));
		}

		$password		 = bcrypt($password_raw);

		//オプションに管理者IDが指定されていたら
		if( !empty($this->argument("id")) ){
			$id	 = $this->argument("id");		
			$update = DB::table("admins")->where("id", $id)->update(["password" => $password]);
		}

		echo $password_raw."\n";
    }
}

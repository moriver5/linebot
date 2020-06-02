<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use App\Mail\SendMail;
use Mail;
use Utility;

class MailDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:delivery {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予想一覧→アクセス一覧の全員にメール配信';

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
		//予想IDを条件に配信先メールアドレス取得
		$query = User::query();
		$db_data = $query->join('visitor_logs', 'users.id', '=', 'visitor_logs.client_id')->where('forecast_id', $this->argument('id'))->get();

		if( !empty($db_data) ){
			//smtp取得
			list($options['host_ip'], $options['port']) = Utility::getSmtpHost('melmaga');

			//
			$options['client_id'] = $db_data->id;

			foreach($db_data as $lines){
				//1秒待機
				usleep(1000000);
//error_log($lines->mail_address."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

				$err_flg = Utility::checkNgWordEmail($lines->mail_address);

				//禁止ワードが含まれていたら
				if( !is_null($err_flg) ){
					continue;
				}

				//<USER_EMAIL>の文字列をユーザーのメールアドレスへ変換
				$data['contents'] = preg_replace("/".config('const.user_email')."/", $lines->mail_address, $data['contents']);

				//メール送信
				Mail::to($lines->mail_address)->send( new SendMail($options, $data) );
			}
		}
    }
}

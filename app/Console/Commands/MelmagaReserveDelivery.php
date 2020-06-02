<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Model\User;
use App\Model\Melmaga_log;
use App\Model\Confirm_email;
use App\Mail\SendMail;
use Mail;
use Carbon\Carbon;
use Utility;

class MelmagaReserveDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melmaga:reserve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メルマガの予約配信用コマンド';

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
		$db_data = Melmaga_log::query()
			->where('sort_reserve_send_date', '<=', $sort_date)
			->whereIn('send_status', [0,4])
			->orderBy('sort_reserve_send_date', 'desc')
			->get();

		//配信予定日時を過ぎ、配信状況待ちのデータあれば
		if( !empty($db_data) ){
			//配列からメルマガIDを取り出す
			foreach($db_data as $lines){
				//HTMLメールフラグ(デフォルトはテキストメール)
				$mail_html_flg	= false;
				$body			= $lines->text_body;

				//HTMLメールなら
				if( !empty($db_melmaga->html_body) ){
					$mail_html_flg	= true;
					$body			= $lines->html_body;
				}

				//メルマガIDへ変換
				$body = preg_replace("/".config('const.melmaga_id')."/", $lines->id, $body);

				$from_mail = Utility::getConvertData($lines->from_mail);
				$from_name = Utility::getConvertData($lines->from_name);
				$subject = Utility::getConvertData($lines->subject);

				//送信元メールアドレスをアカウントとドメインに分割
				list($account, $domain) = explode("@", $from_mail);

				//送信元メールアドレスのドメインで返信先メールアドレスを生成
				$replay_to_mail	 = config('const.replay_to_mail').'@'.$domain;

				//送信元メールアドレスのドメインでリターンパスのメールアドレスを生成
				$return_path_mail	= config('const.return_path_to_mail').'@'.$domain;

				//送信元情報設定
				$options = [
					'replay_to'		=> $replay_to_mail,
					'received'		=> $domain,
					'return_path'	=> $return_path_mail,
					'html_flg'		=> $mail_html_flg,
					'from'			=> $from_mail,
					'from_name'		=> $from_name,
					'subject'		=> $subject,
					'template'	 => config('const.admin_edit_mail'),
				];

				//送信データ設定
				$data = [
					'contents'		=> Utility::getConvertData($body),
				];
//error_log("メルマガID：{$lines->id}\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

				//確認アドレス宛に送信するにチェックが入っていた場合
				if( empty($lines->send_status) ){
					//確認アドレス宛にメルマガ送信
					$db_email = Confirm_email::query()->get();
					if( count($db_email) > 0 ){
						foreach($db_email as $confirm){
							$err_flg = Utility::checkNgWordEmail($confirm->email);

							//禁止ワードが含まれていたら
							if( !is_null($err_flg) ){
								continue;
							}

							Mail::to($confirm->email, $confirm->name)->send( new SendMail($options, $data) );
						}
					}
				}

				//別プロセスでメルマガIDごとに配信
				$process = new Process(config('const.artisan_command_path')." melmaga:reserve_send {$lines->id} {$lines->send_status} > /dev/null");

				//非同期実行(/data/www/melmaga/app/Console/Commands/MelmagaReserveSendDelivery.php)
				$process->start();

				//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
				//1.5秒待機
				usleep(1500000);
			}
		}
    }
}

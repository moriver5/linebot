<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Mail\SendMail;
use App\Model\Registered_mail_queue;
use Mail;
use Carbon\Carbon;
use DB;
use Utility;

class RegisteredSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registered:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '登録後送信メールの送信';

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

		//有効の登録後送信メールのデータ取得
		$db_data = DB::select("select * from registered_mails where enable_flg = 1 and title != '' and ( body != '' or html_body != '' )");
//error_log(print_r($db_data,true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

		//登録後送信メールのデータがあれば
		if( !empty($db_data) ){
			$query = Registered_mail_queue::query();

			//抽出項目に値が入力されていれば
			if( !empty($db_data[0]->item_value) ){
				$query->where(config('const.registered_send_item')[$db_data[0]->item_type], config('const.registered_like_type')[$db_data[0]->like_type], $db_data[0]->item_value);
			}

			//グループ
			if( !empty($db_data[0]->groups) ){
				$query->whereIn('group_id', explode(",", $db_data[0]->groups));
			}

			//現在時刻から指定時間を引く
			$dt = new Carbon();
			$interval_date = $dt->subSecond($db_data[0]->specified_time * 60);

			//指定時間経過
			$db_mail = $query->where("created_at", '<=', $interval_date)->get();

			//指定時間経過したメールアドレスを取得
			if( count($db_mail) > 0 ){
				//smtp取得
				list($options['host_ip'], $options['port']) = Utility::getSmtpHost('setting');

				//配列から送信先メールアドレスを取り出す
				foreach($db_mail as $lines){
					//HTMLメールフラグ(デフォルトはテキストメール)
					$mail_html_flg	= false;
					$body			= $db_data[0]->body;

					//HTMLメールなら
					if( !empty($db_data[0]->html_body) ){
						$mail_html_flg	= true;
						$body			= $db_data[0]->html_body;
					}

					//%変換設定文字列
					$convert_from_name = config('const.convert_mail_from_name');
					$convert_from_mail = config('const.convert_from_mail');

					//変換後の文字列を取得
					list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_data[0]->title, $convert_from_name, $convert_from_mail, $lines->group_id);
//error_log("{$body}, {$subject}, {$from_name}, {$from_mail}\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

					//送信元アドレスが設定されていない場合、処理停止
					if( preg_match("/@/", $from_mail) == 0 ){
						return false;
					}

					//送信元メールアドレスをアカウントとドメインに分割
					list($account, $domain) = explode("@", $from_mail);

					//送信元メールアドレスのドメインで返信先メールアドレスを生成
					$replay_to_mail	 = config('const.replay_to_mail').'@'.$domain;

					//送信元メールアドレスのドメインでリターンパスのメールアドレスを生成
					$return_path_mail	= config('const.return_path_to_mail').'@'.$domain;

					list($host_ip, $port) = Utility::getSmtpHost('setting');

					//送信元情報設定
					$options = [
						'replay_to'		=> $replay_to_mail,
						'received'		=> $domain,
						'return_path'	=> $return_path_mail,
						'host_ip'		=> $host_ip,
						'port'			=> $port,
						'html_flg'		=> $mail_html_flg,
						'from'			=> $from_mail,
						'from_name'		=> $from_name,
						'subject'		=> $subject,
						'template'		=> config('const.admin_edit_mail'),
					];

					//送信データ設定
					$data = [
						'contents'		=> $body,
					];

					$err_flg = Utility::checkNgWordEmail($lines->mail);

					//禁止ワードが含まれていたら
					if( !is_null($err_flg) ){
						continue;
					}

					$options['client_id'] = $lines->client_id;

					//<USER_EMAIL>の文字列をユーザーのメールアドレスへ変換
					$data['contents'] = preg_replace("/".config('const.user_email')."/", $lines->mail, $data['contents']);

					Mail::to($lines->mail)->queue( new SendMail($options, $data) );
					
					//送信後、削除
					$delete = DB::delete("delete from registered_mail_queues where client_id = '{$lines->client_id}'");

					//1秒間スリープ
					usleep(1000000);
				}
			}

			//121分経過したメールアドレスを取得
			$db_mail = DB::select("select * from registered_mail_queues where now() >= (created_at + interval 121 minute)");

			//登録後送信メールの条件に当てはまらないで残っているメールアドレスを削除
			if( count($db_mail) > 0 ){
				foreach($db_mail as $lines){
					$delete = DB::delete("delete from registered_mail_queues where client_id = '{$lines->client_id}'");
				}
			}

		//登録後送信メールがなければregistered_mail_queuesテーブルに登録されているデータを削除
		}else{
			$db_client_id = DB::select("select * from registered_mail_queues limit 1");
//error_log(print_r($db_client_id,true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

			//registered_mail_queuesテーブルにデータがあれば
			if( count($db_client_id) > 0 ){
				$delete = DB::delete("delete from registered_mail_queues");
			}
		}
    }
}

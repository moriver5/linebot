<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use App\Model\Melmaga_log;
use App\Model\Melmaga_temp_immediate_mail;
use App\Model\Confirm_email;
use App\Mail\SendMail;
use App\Model\Melmaga_history_log;
use Mail;
use Carbon\Carbon;
use Utility;
use DB;

class MelmagaImmediateDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melmaga:delivery {melmaga_id} {send_status} {history_flg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メルマガの即時配信用コマンド';

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
		//メルマガの件名、内容を取得
		$db_melmaga = Melmaga_log::where('id', $this->argument('melmaga_id'))->first();

		//HTMLメールフラグ(デフォルトはテキストメール)
		$mail_html_flg	= false;
		$body			= $db_melmaga->text_body;

		//HTMLメールなら
		if( !empty($db_melmaga->html_body) ){
			$mail_html_flg	= true;
			$body			= $db_melmaga->html_body;
		}

		//メルマガIDへ変換
		$body = preg_replace("/".config('const.melmaga_id')."/", $this->argument('melmaga_id'), $body);

		$from_mail = Utility::getConvertData($db_melmaga->from_mail);
		$from_name = Utility::getConvertData($db_melmaga->from_name);
		$subject = Utility::getConvertData($db_melmaga->subject);

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

		//メルマガ配信IDを条件に配信先メールアドレスのリスト取得
		$query = User::query();
		$db_data = $query->join('melmaga_temp_immediate_mails', 'users.id', '=', 'melmaga_temp_immediate_mails.client_id')->where('melmaga_temp_immediate_mails.melmaga_id', $this->argument('melmaga_id'))->get();

		if( !empty($db_data) ){
			//確認アドレス宛に送信するにチェックが入っていた場合
			if( !empty($this->argument('history_flg')) ){
				//確認アドレス宛にメルマガ送信
				$db_email = Confirm_email::query()->get();
				if( count($db_email) > 0 ){
					foreach($db_email as $lines){
//error_log("確認アドレス：".$lines->email."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

						$err_flg = Utility::checkNgWordEmail($lines->email);

						//禁止ワードが含まれていたら
						if( !is_null($err_flg) ){
							continue;
						}

						Mail::to($lines->email, $lines->name)->send( new SendMail($options, $data) );
					}
				}
			}

			$now_date = Carbon::now();

			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

			//履歴を残す(send_status:4以外)
			if( $this->argument('send_status') != 4 ){
				//メルマガ配信日時 配信状況：1(送信中)
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))
					->update([
						'send_date' => $now_date,
						'send_status' => 1]);

			//履歴を残さない
			}else{
				//メルマガ配信日時
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))
					->update(['send_date' => $now_date]);
			}

			//リレーサーバーを使用するとき
			if( $db_melmaga->send_method == 1 ){
				//smtp取得
				list($options['host_ip'], $options['port']) = Utility::getSmtpHost('melmaga');
				if( is_null($options['host_ip']) ){
					$db_melmaga = Melmaga_log::where('id', $this->argument('melmaga_id'))->update([
						'send_method' => null
					]);
				}
			}

			//配列から１つ取り出しメール送信
			foreach($db_data as $lines){
				//0.1秒待機
				usleep(100000);
//error_log("メルマガ：".$lines->mail_address."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

				DB::insert('insert ignore into melmaga_history_logs(melmaga_id, client_id, sort_date, created_at, updated_at) values('
				.$this->argument('melmaga_id').','
				.$lines->id.','
				.$sort_date.',"'
				.$now_date.'","'
				.$now_date.'")');
/*
				$melmaga_history = new Melmaga_history_log([
					'melmaga_id'	=> $this->argument('melmaga_id'),
					'client_id'		=> $lines->id,
					'sort_date'		=> $sort_date,
					'created_at'	=> $now_date,
					'updated_at'	=> $now_date
				]);

				//DB保存
				$melmaga_history->save();
*/
				$err_flg = Utility::checkNgWordEmail($lines->mail_address);

				//禁止ワードが含まれていたら
				if( !is_null($err_flg) ){
					continue;
				}

				$options['client_id'] = $lines->id;

				//usersテーブルのremember_tokenへ変換
				$data['contents'] = preg_replace("/".config('const.access_key')."/", $lines->remember_token, $data['contents']);

				//<USER_EMAIL>の文字列をユーザーのメールアドレスへ変換
				$data['contents'] = preg_replace("/".config('const.user_email')."/", $lines->mail_address, $data['contents']);

				try {
					//メール送信
					Mail::to($lines->mail_address)->queue( new SendMail($options, $data) );

					//メール送信数をカウント
					$delete = Melmaga_log::where('id', $this->argument('melmaga_id'))->increment('send_count', 1);

					//メール送信後、リストから削除
					$delete = Melmaga_temp_immediate_mail::where('melmaga_id', $this->argument('melmaga_id'))->where('client_id', $lines->id)->delete();
				}catch (\Exception $e) {

				}
			}

			//履歴を残す以外(send_status:4以外)
			if( $this->argument('send_status') != 4 ){
				//メルマガ配信状況の更新→配信済:send_status→1
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))->update(['send_status' => 2]);

			//履歴を残さない場合(send_status:4)
			}else{
				//メルマガ配信状況の更新→履歴を残さない場合の配信済:send_status→5
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))->update(['send_status' => 5]);				
			}
		}
    }
}

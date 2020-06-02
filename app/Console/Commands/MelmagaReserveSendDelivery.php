<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use App\Model\Melmaga_log;
use App\Model\Melmaga_temp_immediate_mail;
use App\Mail\SendMail;
use App\Model\Melmaga_history_log;
use Mail;
use Carbon\Carbon;
use DB;
use Utility;

class MelmagaReserveSendDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melmaga:reserve_send {melmaga_id} {send_status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メルマガの予約配信で実際にメルマガIDごとに配信するコマンド';

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
		//メルマガ配信先リスト取得用のQuery情報を取得(配信日時時点の抽出ユーザーへ配信するための対応)
		$db_query = Melmaga_log::query()->select('from_mail','from_name','subject','text_body','html_body','query','bindings','exclusion_groups','send_method')->where('id', $this->argument('melmaga_id'))->first();

		//HTMLメールフラグ(デフォルトはテキストメール)
		$mail_html_flg	= false;
		$body			= $db_query->text_body;

		//HTMLメールなら
		if( !empty($db_query->html_body) ){
			$mail_html_flg	= true;
			$body			= $db_query->html_body;
		}

		//メルマガIDに変換
		$body = preg_replace("/".config('const.melmaga_id')."/", $this->argument('melmaga_id'), $body);

		$from_mail = Utility::getConvertData($db_query->from_mail);
		$from_name = Utility::getConvertData($db_query->from_name);
		$subject = Utility::getConvertData($db_query->subject);

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
			'template'		=> config('const.admin_edit_mail'),
		];

		//送信データ設定
		$data = [
			'contents'		=> Utility::getConvertData($body),
		];

		if( !empty($db_query->bindings) ){
			//現在時刻
			$now_date = Carbon::now();

			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

			//メルマガ配信先リスト取得
			$db_data = DB::select($db_query->query, explode(",", $db_query->bindings));

			$list_user = [];
			foreach($db_data as $lines){
				$list_user[] = $lines->id;
			}

			//除外グループ取得
			if( !is_null($db_query->exclusion_groups) ){
				$exclusion_groups = User_group::select('client_id')->whereIn('user_groups.group_id', explode(",",$db_query->exclusion_groups))->distinct()->get();
				$list_exclusion_groups = [];
				foreach($exclusion_groups as $lines){
					$list_exclusion_user[] = $lines->client_id;
				}
				$list_user = array_diff($list_user, $list_exclusion_user);
			}

			if( !empty($list_user) ){
				foreach($list_user as $client_id){
					//メール配信先テーブル(melmaga_temp_immediate_mails)にメルマガ予約配信先のクライアントIDを登録
					$melmaga_mails = new Melmaga_temp_immediate_mail([
						'melmaga_id'	=> $this->argument('melmaga_id'),
						'client_id'		=> $client_id,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);

					//DB保存
					$melmaga_mails->save();

					$melmaga_history = new Melmaga_history_log([
						'melmaga_id'	=> $this->argument('melmaga_id'),
						'client_id'		=> $client_id,
						'sort_date'		=> $sort_date,
						'created_at'	=> $now_date,
						'updated_at'	=> $now_date
					]);

					//DB保存
					$melmaga_history->save();
				}
			}
		}

		//メルマガ配信IDを条件に配信先メールアドレスのリスト取得
		$query = User::query();
		$db_user_data = $query->join('melmaga_temp_immediate_mails', 'users.id', '=', 'melmaga_temp_immediate_mails.client_id')
			->where('melmaga_temp_immediate_mails.melmaga_id', $this->argument('melmaga_id'))
			->get();

		if( !empty($db_user_data) ){
			//現在時刻
			$now_date = Carbon::now();

			//履歴を残す以外(send_status:4以外)
			if( $this->argument('send_status') != 4 ){
				//メルマガ配信日時 配信状況：1(送信中)
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))
					->update([
						'send_date' => $now_date,
						'send_status' => 1]);
			}else{
				//メルマガ配信日時
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))
					->update(['send_date' => $now_date]);
			}

			//リレーサーバーを使用するとき
			if( $db_query->send_method == 1 ){
				//smtp取得
				list($options['host_ip'], $options['port']) = Utility::getSmtpHost('melmaga');
				if( is_null($options['host_ip']) ){
					$db_melmaga = Melmaga_log::where('id', $this->argument('melmaga_id'))->update([
						'send_method' => null
					]);
				}
			}

			//配列からメアドを１つ取り出し配信
			foreach($db_user_data as $users){
				//0.1秒待機
				usleep(100000);
//error_log($this->argument('melmaga_id').":".$users->mail_address."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

				$err_flg = Utility::checkNgWordEmail($users->mail_address);

				//禁止ワードが含まれていたら
				if( !is_null($err_flg) ){
					continue;
				}

				$options['client_id'] = $users->id;

				//usersテーブルのremember_tokenへ変換
				$data['contents'] = preg_replace("/".config('const.access_key')."/", $users->remember_token, $data['contents']);

				//<USER_EMAIL>の文字列をユーザーのメールアドレスへ変換
				$data['contents'] = preg_replace("/".config('const.user_email')."/", $users->mail_address, $data['contents']);

				try {
					//メール送信
					Mail::to($users->mail_address)->send( new SendMail($options, $data) );

					//メール送信数をカウント
					$delete = Melmaga_log::where('id', $this->argument('melmaga_id'))->increment('send_count', 1);

					//メール送信後、リストから削除
					$delete = Melmaga_temp_immediate_mail::where('melmaga_id', $this->argument('melmaga_id'))->where('client_id', $users->id)->delete();
				}catch (\Exception $e) {

				}
			}

			//履歴を残す以外(send_status:4以外)
			if( $this->argument('send_status') != 4 ){
				//メルマガ配信状況の更新→配信済:send_status→2
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))->update(['send_status' => 2]);

			//履歴を残さない場合(send_status:4)
			}else{
				//メルマガ配信状況の更新→履歴を残さない場合の配信済:send_status→5
				$update = Melmaga_log::where('id', $this->argument('melmaga_id'))->update(['send_status' => 5]);				
			}
		}
    }
}

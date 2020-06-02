<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use Auth;
use App\Model\User;
use App\Model\User_group;
use Session;
use Carbon\Carbon;
use Utility;
use DB;
use Validator;

class FileUpload extends Command
{
	private $log_obj;
	private $log_mx_obj;
	private $log_failed_obj;
	private $log_sys_obj;
	
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'file:upload {filename} {ad_cd?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'ファイルアップロードをバックグラウンドで実行';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		//不正メールアドレス用のログ
		$this->log_bad_obj		 = new SysLog(config('const.import_error_log_name'), config('const.save_import_file_dir').config('const.import_error_email_file_name'));

		//重複エラー用のログ
		$this->log_obj			 = new SysLog(config('const.import_error_log_name'), config('const.save_import_file_dir').config('const.import_error_file_name'));

		//MXドメインエラー用のログ
		$this->log_mx_obj		 = new SysLog(config('const.import_mx_domain_error_log_name'), config('const.save_import_file_dir').config('const.import_mx_domain_error_file_name'));

		//ID生成に失敗用のログ
		$this->log_failed_obj	 = new SysLog(config('const.import_failed_log_name'), config('const.save_import_file_dir').config('const.import_failed_create_id_file_name'));

		//システム的なエラー用ログ(アップロードファイルが存在しないときにログ出力)
		$this->log_sys_obj		 = new SysLog(config('const.system_error_log_name'), config('const.system_log_dir_path').config('const.system_error_file_name'));
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		//{filename}の引数受取り
		$upload_file = $this->argument('filename');

		//{ad_cd?}の引数受取り
		$ad_cd		 = $this->argument('ad_cd');

		//アップロードファイルが存在していたら
		if( file_exists(config('const.save_import_file_dir').$upload_file) ) {
			//アップロードファイルをオープン
			$fp = fopen(config('const.save_import_file_dir').$upload_file, "r");

			//ID生成の最大ループ回数に達したときのフラグ
			$skip_flg = false;

			$now_date = Carbon::now();

			//アップロードファイルからデータ取り出し
			while( ($listData = fgetcsv($fp, 0, ",")) !== FALSE ){
				$email = mb_strtolower(trim($listData[0]));

				//メールアドレスの妥当性チェック(妥当性/最大長さ)
				$validator = Validator::make(['email' => $email], ['email' => 'bail|required|email|max:'.config('const.email_length')]);

				//エラーがあればスキップ
				if ( $validator->fails() ) {
					$this->log_bad_obj->addLog($email);
					continue;
				}
/*
				if( preg_match("/@/",$listData[0]) > 0 ){
					$email = mb_strtolower(trim($listData[0]));

					$strlen = strlen($email);
					//メールアドレスの長さが255バイト以下なら
					if( $strlen >= config('const.email_length') ){
						break;
					}
				}
*/
				//mail_addressの重複確認
				$exist_user = User::where('mail_address', $email)->count();

				//メールアドレスの重複あり、ログ出力、登録スキップして次へ
				if( !empty($exist_user) ){
					//ログ出力
					$this->log_obj->addLog($email);
					continue;
				}

				//MXドメインが存在するか確認
				$exist_flg = Utility::checkMxDomain($email);

				//MXドメインが存在しなかったらログ出力、スキップ
				if( !is_null($exist_flg) ){
					//ログ出力
					$this->log_mx_obj->addLog($email);
					continue;
				}

				//usersテーブルのデータ
				$db_value = [
					'mail_address'				=> $email,
					'regist_date'				=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
					'last_access'				=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
					'created_at'				=> $now_date,
					'updated_at'				=> $now_date,
				];

				//備考があれば
				if( isset($listData[6]) ){
					$db_value['description'] = trim($listData[6]);
				}

				$client_id = DB::table('users')->insertGetId($db_value);

				//user_groupテーブルのデータ
				$db_value = [
					'client_id'					=> $client_id,
					'group_id'					=> trim($listData[1]),
					'remember_token'			=> session_create_id(),
					'regist_date'				=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
					'last_access'				=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00',
					'created_at'				=> $now_date,
					'updated_at'				=> $now_date,
				];

				//登録状況があれば
				if( isset($listData[2]) ){
					$db_value['status'] = trim($listData[2]);
				}else{
					$db_value['status'] = 1;					
				}

				//広告コードがあれば
				if( isset($ad_cd) ){
					$db_value['ad_cd'] = $ad_cd;
				}else{
					//エクセルデータに広告コードがあれば
					if( isset($listData[3]) ){
						$db_value['ad_cd'] = trim($listData[3]);
					}else{
						$db_value['ad_cd'] = null;
					}
				}

				//性別があれば
				if( isset($listData[4]) ){
					$db_value['sex'] = trim($listData[4]);
				}

				//年代があれば
				if( isset($listData[5]) ){
					$db_value['age'] = trim($listData[5]);
				}

				$user = new User_group($db_value);

				//DB保存
				$user->save();

				//0.1秒間スリープ
				usleep(100000);
			}
			fclose($fp);
		}else{
			//アップロードファイルが存在しないときにログ出力
			$this->log_sys_obj->addLog(__('messages.upload_file_not_exist'));
		}
	}
}

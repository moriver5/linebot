<?php

namespace App\Libs;

use App\Model\Access_log;
use App\Model\Year_pv_log;
use App\Model\Month_pv_log;
use App\Model\Day_pv_log;
use DB;
use Carbon\Carbon;

class ClientLog
{
	protected $custom_log;

	/*
	 * 
	 */
	public function __construct()
	{

	}

	/*
	 * access_logテーブルにログを書き込む
	 */
	public function addLogDb($login_id, $pay_type)
	{
		DB::transaction(function() use($login_id, $pay_type){
			//DB::transaction内で実行すれば自動コミットされる
			//単体でDB::insertする場合はDB::commit()しないと反映されない
			DB::insert("insert ignore into access_logs("
				. "login_id, "
				. "pay_type, "
				. "login_date, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "{$login_id}, "
				. "{$pay_type}, "
				. "'".date('Y-m-d')."', "
				. "'".Carbon::now()."', "
				. "'".Carbon::now()."')");
		});
	}

	/*
	 * month_pv_logsテーブルにログを書き込む
	 */
	public function addPvLogDb($disp_name = null)
	{
//error_log(print_r($_SERVER,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
		if( !empty($_SERVER['REQUEST_URI']) ){
			$pv_name = $_SERVER['REQUEST_URI'];
		}

		if( !empty($disp_name) ){
			$pv_name = $disp_name;
		}

		if( !empty($pv_name) ){
			//PV集計する上で除外するURL
			if( preg_match("/(\/member\/home\/.+|\.jpg|\.png|\.gif|\.css|\.js)/", $pv_name) ){
				return false;
			}

			//URLに付加されている?afl=〇〇〇移行の文字列を削除
//			$pv_name = preg_replace("/^(.+?\?afl=.+?)&.+$/", "$1", $pv_name);

			DB::transaction(function() use($pv_name){
				$now_date	 = Carbon::now();
				$ymd_date = date("Ymd");
				$ym_date = date("Ym");
				$id = md5($pv_name);

				//DB::transaction内で実行すれば自動コミットされる
				//単体でDB::insertする場合はDB::commit()しないと反映されない
				DB::insert("insert ignore into month_pv_logs("
					. "id, "
					. "access_date, "
					. "url, "
					. "total, "
					. "created_at, "
					. "updated_at) "
					. "values("
					. "'{$id}', "
					. "'{$ymd_date}', "
					. "'{$pv_name}', "
					. "1, "
					. "'".$now_date."', "
					. "'".$now_date."') "
					. "on duplicate key update "
					. "total = total + 1");

				DB::insert("insert ignore into year_pv_logs("
					. "id, "
					. "access_date, "
					. "url, "
					. "total, "
					. "created_at, "
					. "updated_at) "
					. "values("
					. "'{$id}', "
					. "'{$ym_date}', "
					. "'{$pv_name}', "
					. "1, "
					. "'".$now_date."', "
					. "'".$now_date."') "
					. "on duplicate key update "
					. "total = total + 1");
			});
		}
	}

	/*
	 * day_pv_logsテーブルにログを書き込む
	 */
	public function addAdLogDb($ad_cd = null, $login_id = null)
	{
		if( !empty($ad_cd) ){
			DB::transaction(function() use($ad_cd, $login_id){
				$now_date	 = Carbon::now();
				$ymd_date = date("Ymd");
				//ログイン前
				if( is_null($login_id) ){
					DB::insert("insert into day_pv_logs("
						. "ad_cd, "
						. "access_date, "
						. "created_at, "
						. "updated_at) "
						. "values("
						. "'{$ad_cd}', "
						. "'{$ymd_date}', "
						. "'".$now_date."', "
						. "'".$now_date."')");

				//ログイン後
				}else{
					DB::insert("insert into day_pv_logs("
						. "ad_cd, "
						. "login_id, "
						. "access_date, "
						. "created_at, "
						. "updated_at) "
						. "values("
						. "'{$ad_cd}', "
						. "'{$login_id}', "
						. "'{$ymd_date}', "
						. "'".$now_date."', "
						. "'".$now_date."')");
				}
			});
		}
	}

	/*
	 * personal_access_logsテーブルにログを書き込む
	 */
	public function addPersonalAccessLogDb($login_id, $malmaga_id = null)
	{
		if( !empty($_SERVER['REQUEST_URI']) ){
			$page_name = $_SERVER['REQUEST_URI'];
			$listPageName = config('const.member_access_page');
			foreach($listPageName as $key => $value){
				if( preg_match("/{$key}/", $page_name) > 0 ){
					$page_name = $value."<>{$page_name}";
					break;
				}
			}
		}

		//PV集計する上で除外するURL
		if( preg_match("/(\/member\/home\/.+|\.jpg|\.png|\.gif|\.css|\.js)/", $page_name) ){
			return false;
		}

		DB::transaction(function() use($login_id, $malmaga_id, $page_name){
			$now_date	 = Carbon::now();

			//DB::transaction内で実行すれば自動コミットされる
			//単体でDB::insertする場合はDB::commit()しないと反映されない

			//メルマガIDがあるとき
			if( !empty($malmaga_id) ){
				DB::insert("insert ignore into personal_access_logs("
					. "login_id, "
					. "melmaga_id, "
					. "page,"
					. "created_at, "
					. "updated_at) "
					. "values("
					. "{$login_id}, "
					. "{$malmaga_id}, "
					. "'{$page_name}', "
					. "'{$now_date}', "
					. "'{$now_date}')");

			//メルマガIDがないとき
			}else{
				DB::insert("insert ignore into personal_access_logs("
					. "login_id, "
					. "page,"
					. "created_at, "
					. "updated_at) "
					. "values("
					. "{$login_id}, "
					. "'{$page_name}', "
					. "'{$now_date}', "
					. "'{$now_date}')");	
			}
		});
	}
}

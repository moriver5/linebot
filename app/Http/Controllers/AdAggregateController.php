<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Ad_access_log;
use Carbon\Carbon;
use DB;
use PDO;
use Cookie;

class AdAggregateController extends Controller
{
	private $dbh;

	public function __construct()
	{
		//DB接続
		$this->pdoDbCon();
	}

	private function pdoDbCon(){
		//DB接続
		try {
			$this->dbh = DB::connection('mysql')->getPdo();
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策

		} catch (\PDOException $e) {
//			abort('403', __("messages.pdo_connection_err_msg"));
		}
	}

    public function index(Request $request, $string = null){
		$channel_id	= $request->input('pk_campaign');
		$ad_cd		= $request->input('ad_cd');
		$toDay		= date("Ymd");

		//バナーIDごとにクッキー名設定
//		$adcode_access_cookie_name = config('const.line_channel_access_cookie').$channel_id;

		//集計処理
		try{ 
			//トランザクション開始
			$this->dbh->beginTransaction();

			$stmt = $this->dbh->prepare("select * from ad_access_logs where line_basic_id = :line_basic_id and ad_cd = :ad_cd and access_date = :access_date for update");
			$stmt->bindValue(":line_basic_id", $channel_id);
			$stmt->bindValue(":ad_cd", $ad_cd);
			$stmt->bindValue(":access_date", (int)date('Ymd'), PDO::PARAM_INT);
			$stmt->execute();
//error_log("{$adcode_access_cookie_name} {$channel_id} {$ad_cd} ".date('Ymd').":db\n",3,"/data/www/line/storage/logs/nishi_log.txt");
			$pv = 0;
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$pv = $row['pv'];
			}

			//集計データがない場合
			if( $pv == 0 ){
				//クッキー設定
//				Cookie::queue(Cookie::make($adcode_access_cookie_name, $toDay, config('const.line_cookie_expire_time'), '/', $_SERVER['HTTP_HOST']));

				$stmt = $this->dbh->prepare("insert ignore into  ad_access_logs(line_basic_id, ad_cd, pv, access_date, created_at, updated_at) values(:line_basic_id, :ad_cd, :pv, :access_date, :created_at, :updated_at) on duplicate key update pv = pv + 1, updated_at = '{$toDay}'");
				$stmt->bindValue(":line_basic_id", $channel_id);
				$stmt->bindValue(":ad_cd", $ad_cd);
				$stmt->bindValue(":pv", 1);
				$stmt->bindValue(":access_date", (int)date('Ymd'), PDO::PARAM_INT);
				$stmt->bindValue(":created_at", date('Y-m-d H:i:s'));
				$stmt->bindValue(":updated_at", date('Y-m-d H:i:s'));
				$stmt->execute();

			}else{
				//1回もアクセスされていないとき
//				if( empty(Cookie::get($adcode_access_cookie_name)) ){
					//クッキー設定
//					Cookie::queue(Cookie::make($adcode_access_cookie_name, $toDay, config('const.line_cookie_expire_time'), '/', $_SERVER['HTTP_HOST']));

					//PV・UUカウントアップ
//					$stmt = $this->dbh->prepare("update ad_access_logs set pv = pv + 1, uu = uu + 1, updated_at = :updated_at where line_basic_id = :line_basic_id and ad_cd = :ad_cd and access_date = :access_date");
//				}else{
					//PVカウントアップ
					$stmt = $this->dbh->prepare("update ad_access_logs set pv = pv + 1, updated_at = :updated_at where line_basic_id = :line_basic_id and ad_cd = :ad_cd and access_date = :access_date");
//				}
				$stmt->bindValue(":updated_at", date('Y-m-d H:i:s'));
				$stmt->bindValue(":line_basic_id", (int)$channel_id, PDO::PARAM_INT);
				$stmt->bindValue(":ad_cd", $ad_cd);
				$stmt->bindValue(":access_date", (int)date('Ymd'), PDO::PARAM_INT);
				$stmt->execute();
			}
			$this->dbh->commit();

		}catch(\Exception $e){
			$this->dbh->rollback();
//			abort('403', __("messages.pdo_connection_err_msg"));
		}
	}
}

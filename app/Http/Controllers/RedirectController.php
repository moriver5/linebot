<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Line_short_url;
use App\Model\Line_click_user;
use App\Model\TrackAccessLog;
use App\Model\TrackImageLog;
use App\Model\Ad_access_log;
use App\Model\LineContents;
use File;
use DB;
use PDO;
use Carbon\Carbon;

class RedirectController extends Controller
{
	private $dbh;

	/*
	 * リダイレクト先URLへ遷移
	 */
	public function index($push_id, $short_url, $type = null)
	{
//error_log("redirect1\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log("redirect2:{$push_id}, {$short_url}, {$type}\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		//リダイレクト先URL取得
		$db_data = Line_click_user::where('line_push_id', $push_id)->where('short_url', $short_url)->first();

		//リダイレクト先URLが存在しなかったら
		if( empty($db_data) ){
			return ;
		}

		if( $db_data->read == 0 ){
			if( $db_data->click == 0 ){
				//友だち登録集計処理
				$this->insertTrackAccess($db_data->line_basic_id, $db_data->user_line_id);
			}

			if( is_null($type) ){
				//readを1にする
				$update = Line_click_user::where('line_push_id', $push_id)->where('short_url', $short_url)->increment('read');

				//リダイレクト先へ遷移
				return redirect()->away($db_data->url);
			}
		}

		//クリック数を+１にする
		$update = Line_click_user::where('line_push_id', $push_id)->where('short_url', $short_url)->increment('click');
		$update = Line_click_user::where('line_push_id', $push_id)->where('short_url', $short_url)->update([
			'sort_date' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Carbon::now()).'00'
		]);

		//リダイレクト先へ遷移
		return redirect()->away($db_data->url);
	}

	/*
	 * 
	 */
	public function insertTrackAccess($basic_id, $line_id)
	{
		$ua = '';
		if( isset($_SERVER['HTTP_USER_AGENT']) ){
			$ua = $_SERVER['HTTP_USER_AGENT'];
		}

		$access_ip = '';
		if( isset($_SERVER['REMOTE_ADDR']) ){
			$access_ip = $_SERVER['REMOTE_ADDR'];
		}

		$request_uri = '';
		if( isset($_SERVER['REQUEST_URI']) ){
			$request_uri = $_SERVER['REQUEST_URI'];
		}

		$track_img_log = new TrackImageLog([
			'env_data'			 => json_encode($_SERVER),
			'script_name'		 => $request_uri,
			'line_basic_id'		 => $basic_id,
			'user_line_id'		 => $line_id,
			'access_ip'			 => $access_ip,
			'access_ua'			 => $ua,
		]);
		$track_img_log->save();

		$toDay = date("Ymd");

		//DB接続
		$this->_pdoDbCon();

		//集計処理
		try{ 
			//トランザクション開始
			$this->dbh->beginTransaction();

			//登録済データ取得
			$stmt = $this->dbh->prepare("select line_basic_id from track_access_logs where line_basic_id = :line_basic_id and user_line_id = :user_line_id and status = 1 limit 1 for update");
			$stmt->bindValue(":line_basic_id", $basic_id);
			$stmt->bindValue(":user_line_id", $line_id);
			$stmt->execute();

			$line_basic_id = null;
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$line_basic_id = $row['line_basic_id'];
			}

			//同じチャンネル・LINE IDで登録がなければ
			if( is_null($line_basic_id) ){
				$this->dbh->commit();

				//トランザクション開始
				$this->dbh->beginTransaction();

				//LINE友だち未登録データ取得
				$stmt = $this->dbh->prepare("select line_basic_id, asp_id, ad_cd, xuid from track_access_logs where csrf_token is not null and line_basic_id = :line_basic_id and user_line_id = :user_line_id and status = 0 order by updated_at desc limit 1 for update");
				$stmt->bindValue(":line_basic_id", $basic_id);
				$stmt->bindValue(":user_line_id", $line_id);
				$stmt->execute();

				$db_data = [];
				while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
					$db_data = [
						'line_basic_id'	 => $row['line_basic_id'],
						'asp_id'		 => $row['asp_id'],
						'ad_cd'			 => $row['ad_cd'],
						'xuid'			 => $row['xuid'],
					];
				}

				//データがあれば
				if( !empty($db_data) ){
					//track_access_logsテーブルのstatusを友だち登録済(status=1)にする
					$stmt = $this->dbh->prepare("update track_access_logs set access_date = :access_date, status = 1 where csrf_token is not null and line_basic_id = :line_basic_id and user_line_id = :user_line_id and status = 0");
					$stmt->bindValue(":access_date", $toDay);
					$stmt->bindValue(":line_basic_id", $basic_id);
					$stmt->bindValue(":user_line_id", $line_id);
					$stmt->execute();

					//指定条件でad_access_logsテーブルのデータをロックし取得
					$stmt = $this->dbh->prepare("select reg from ad_access_logs where line_basic_id = :line_basic_id and ad_cd = :ad_cd and access_date = :access_date for update");
					$stmt->bindValue(":line_basic_id", $basic_id);
					$stmt->bindValue(":ad_cd", $db_data['ad_cd']);
					$stmt->bindValue(":access_date", $toDay);
					$stmt->execute();

					$reg = 0;
					while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
						$reg = $row['reg'];
					}

					//登録者をカウントアップ
					$stmt = $this->dbh->prepare("update ad_access_logs set reg = :reg + 1 where line_basic_id = :line_basic_id and ad_cd = :ad_cd and access_date = :access_date");
					$stmt->bindValue(":reg", (int)$reg, PDO::PARAM_INT);
					$stmt->bindValue(":line_basic_id", $basic_id);
					$stmt->bindValue(":ad_cd", $db_data['ad_cd']);
					$stmt->bindValue(":access_date", $toDay);
					$stmt->execute();

					//指定条件でline_usersテーブルからロックしデータ取得
					$stmt = $this->dbh->prepare("select count(*) as count,ad_cd from line_users where line_basic_id = :line_basic_id and user_line_id = :user_line_id for update");
					$stmt->bindValue(":line_basic_id", $basic_id);
					$stmt->bindValue(":user_line_id", $line_id);
					$stmt->execute();

					$count = 0;
					$ad_cd = '';
					while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
						$count = $row['count'];
						$ad_cd = $row['ad_cd'];
					}

					//データがあれば
					if( $count > 0 && empty($ad_cd) ){
						//広告コード登録
						$stmt = $this->dbh->prepare("update line_users set ad_cd = :ad_cd where line_basic_id = :line_basic_id and user_line_id = :user_line_id");
						$stmt->bindValue(":ad_cd", $db_data['ad_cd']);
						$stmt->bindValue(":line_basic_id", $basic_id);
						$stmt->bindValue(":user_line_id", $line_id);
						$stmt->execute();
					}

					$this->dbh->commit();

					//成果情報のアクセス先を取得
					$listSocketParam = $this->_getSocketParameter($db_data['asp_id'], $db_data['xuid']);

					if( $listSocketParam !== false ){
						//成果情報送信
						$this->sendXuidFam($listSocketParam[0], $listSocketParam[1], 80);
					}
				}else{
					$this->dbh->commit();
				}

			//登録データあり
			}else{
				$this->dbh->commit();
			}
		}catch(\Exception $e){
			$this->dbh->rollback();
//			abort('403', __("messages.pdo_connection_err_msg"));
		}

	}

	public function _getSocketParameter($asp_id, $xuid)
	{
		//ASPなし($asp_id=0)
		if( empty($asp_id) ){
			return false;
		}

		//キックバックURL取得
		$db_data = DB::select("select * from line_asps where id = {$asp_id}");

		//データがあれば
		if( count($db_data) > 0 ){
			//キックバックURLからドメインとパスを分割
			if( preg_match("/^https?:\/\/(.+?)(\/.+)$/", $db_data[0]->kickback_url, $matches) > 0 ){
				//パラメータに含まれる_xuidに値を付加
				$matches[2] = preg_replace("/_xuid=.*&?/", "_xuid={$xuid}", $matches[2]);
				return [$matches[1], $matches[2]];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	private function _pdoDbCon(){
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

}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TrackAccessLog;
use App\Model\TrackImageLog;
use App\Model\Ad_access_log;
use App\Model\LineContents;
use File;
use DB;
use PDO;

class LineImageController extends Controller
{
	private $dbh;

	public function __construct(Request $request)
	{

	}

	/*
	 * 排他制御バージョン
	 * LINEの友だち登録の集計処理を行う
	 */
	public function showFollowImage(Request $request, $basic_id, $image, $line_id)
	{
//error_log("{$basic_id}, {$image}, {$line_id}\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
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
		$this->pdoDbCon();

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
					$listSocketParam = $this->getSocketParameter($db_data['asp_id'], $db_data['xuid']);

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

		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$image;

		header('Content-Type: '.File::mimeType($file));
		return readfile($file);
	}

	/*
	 * 排他制御ができていないバージョン
	 * LINEの友だち登録の集計処理を行う
	 */
	public function showFollowImage2(Request $request, $basic_id, $image, $line_id)
	{
//error_log(print_r($_SERVER,true).":test1\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log(print_r($request->all(),true).":test1\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		$track_img_log = new TrackImageLog([
			'env_data'			 => json_encode($_SERVER),
			'script_name'		 => $_SERVER['REQUEST_URI'],
			'line_basic_id'		 => $basic_id,
			'user_line_id'		 => $line_id,
			'access_ip'			 => $_SERVER['REMOTE_ADDR'],
			'access_ua'			 => $_SERVER['HTTP_USER_AGENT'],
		]);
		$track_img_log->save();

		$toDay = date("Ymd");

		//同じチャンネル・LINE IDですでに登録されているデータ取得
		$db_data = DB::select("select line_basic_id from track_access_logs where line_basic_id = '{$basic_id}' and user_line_id = '{$line_id}' and status = 1 limit 1");

		//同じチャンネル・LINE IDで登録がなければ
		if( count($db_data) == 0 ){

			//同一のアクセスIP、QRコードから友達登録したユーザーがトークルームを過去に一度も開いてないデータを取得
			$db_data = DB::select("select line_basic_id, asp_id, ad_cd, xuid from track_access_logs where csrf_token is not null and line_basic_id = '{$basic_id}' and user_line_id = '{$line_id}' and status = 0 limit 1");

			//データがあれば
			if( !empty($db_data[0]->line_basic_id) ){
				//status=1にしトークルーム閲覧済にする
				DB::update("update track_access_logs set access_date = {$toDay}, status = 1 where csrf_token is not null and line_basic_id = '{$basic_id}' and user_line_id = '{$line_id}' and status = 0");

				//ad_access_logsテーブルのregをカウントアップ
				DB::update("update ad_access_logs set reg = reg + 1 where line_basic_id = '{$basic_id}' and ad_cd = '".$db_data[0]->ad_cd."' and access_date = {$toDay}");

				//登録チャンネルユーザーの広告コードを登録
				DB::update("update line_users set ad_cd = '".$db_data[0]->ad_cd."' where follow_flg = 1 and line_basic_id = '{$basic_id}' and user_line_id = '{$line_id}'");

				//成果情報のアクセス先を取得
				list($domain, $param) = $this->getSocketParameter($db_data[0]->asp_id, $db_data[0]->xuid);

				//成果情報送信
				$this->sendXuidFam($domain, $param, 80);
			}else{
				//同一のアクセスIP、QRコードから友達登録したユーザーがトークルームを過去に一度も開いてないデータを取得(2件以上取得される可能性あり)
				$db_data = DB::select("select line_basic_id, asp_id, ad_cd, xuid from track_access_logs where csrf_token is not null and line_basic_id = '{$basic_id}' and access_ip = '{$_SERVER['REMOTE_ADDR']}' and status = 0");

				//データがあれば(データが2件以上の可能性もあり)
				if( !empty($db_data[0]->line_basic_id) ){
					//status=1、user_line_id=トークルームを開いたユーザーのLINE IDでトークルームを閲覧済にする(データが2件以上の場合は同じLINE IDで更新される)
					DB::update("update track_access_logs set access_date = {$toDay}, user_line_id = '{$line_id}', status = 1 where csrf_token is not null and line_basic_id = '{$basic_id}' and access_ip = '{$_SERVER['REMOTE_ADDR']}' and status = 0");

					//登録チャンネルユーザーの広告コードを登録
					DB::update("update line_users set ad_cd = '".$db_data[0]->ad_cd."' where follow_flg = 1 and line_basic_id = '{$basic_id}' and user_line_id = '{$line_id}'");

					//成果情報のアクセス先を取得
					list($domain, $param) = $this->getSocketParameter($db_data[0]->asp_id, $db_data[0]->xuid);

					if( $domain !== false ){
						//Famに成果情報送信
						$this->sendXuidFam($domain, $param, 80);
					}
				}
			}
		}

		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$image;
//error_log($file.":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

		header('Content-Type: '.File::mimeType($file));
		return readfile($file);
	}

	/*
	 * 
	 */
	public function showPushImage(Request $request, $basic_id, $msg, $line_push_id)
	{
//error_log(print_r($_SERVER,true).":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log(print_r($request->all(),true).":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log('/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$msg.'_'.$line_push_id.'.png'."::msg\n",3,"/data/www/line/storage/logs/nishi_log.txt");
//		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$msg.'_'.$line_push_id.'.png';
		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$line_push_id;

		header('Content-Type: '.File::mimeType($file));
//		header('Content-Type: image/png');
		return readfile($file);
	}

	/*
	 * 
	 */
	public function showCarouselPushImage(Request $request, $basic_id, $msg, $line_push_id)
	{
		$db_data = DB::select("select * from line_carousel_templates where id = {$line_push_id}");

		$img = '';
		if( $msg == 'img1' ){
			$img = $db_data[0]->img1;
		}elseif( $msg == 'img2' ){
			$img = $db_data[0]->img2;			
		}elseif( $msg == 'img3' ){
			$img = $db_data[0]->img3;
		}elseif( $msg == 'img4' ){
			$img = $db_data[0]->img4;
		}elseif( $msg == 'img5' ){
			$img = $db_data[0]->img5;
		}elseif( $msg == 'img6' ){
			$img = $db_data[0]->img6;
		}elseif( $msg == 'img7' ){
			$img = $db_data[0]->img7;
		}elseif( $msg == 'img8' ){
			$img = $db_data[0]->img8;
		}elseif( $msg == 'img9' ){
			$img = $db_data[0]->img9;
		}elseif( $msg == 'img10' ){
			$img = $db_data[0]->img10;
		}
		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$img;

		header('Content-Type: '.File::mimeType($file));
		return readfile($file);
	}

	/*
	 * 
	 */
	public function showButtonPushImage(Request $request, $basic_id, $msg, $line_push_id)
	{
		$db_data = DB::select("select * from line_4choices_templates where id = {$line_push_id}");

		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$db_data[0]->img;

		header('Content-Type: '.File::mimeType($file));
		return readfile($file);
	}

	/*
	 * 
	 */
	public function showPostbackPushImage(Request $request, $basic_id, $msg, $postback_id)
	{
		$db_data = DB::select("select * from line_postback_templates where id = {$postback_id}");

		$img = '';
		if( $msg == 'img1' ){
			$img = $db_data[0]->msg1;
		}elseif( $msg == 'img2' ){
			$img = $db_data[0]->msg2;			
		}elseif( $msg == 'img3' ){
			$img = $db_data[0]->msg3;
		}elseif( $msg == 'img4' ){
			$img = $db_data[0]->msg4;
		}elseif( $msg == 'img5' ){
			$img = $db_data[0]->msg5;
		}
		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$img;
//error_log(print_r($file,true).":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

		header('Content-Type: '.File::mimeType($file));
		return readfile($file);
	}

	public function showImageMapImage(Request $request, $basic_id, $image_id, $image_width)
	{
//error_log("image map:{$basic_id}, {$image_id}, {$image_width}\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		list($image, $extension) = explode(".", $image_id);
		$file = '/data/www/storage/line/storage/app/public/line/'.$basic_id.'/img/'.$image.'_'.$image_width.'.'.$extension;

		header('Content-Type: '.File::mimeType($file));
		return readfile($file);
	}

	/*
	 * 広告の成果をFamに送信
	 */
	public function sendXuidFam($domain, $get_param, $port)
	{
		try{
			$fp = fsockopen($domain, $port, $errno, $errstr, config('const.fam_socket_timeout'));

			$out = [
					'GET '.$get_param.' HTTP/1.1',
					'Host:'.$domain,
			];

			fwrite($fp, implode($out, "\r\n") . "\r\n\r\n");
			fclose($fp);
		}catch(\Exception $e){
			
		}
	}

	public function getSocketParameter($asp_id, $xuid)
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

}

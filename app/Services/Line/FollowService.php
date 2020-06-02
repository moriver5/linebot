<?php
namespace App\Services\Line;
use App\Model\LineUser;
use App\Model\LineUserProfile;
use App\Model\TrackAccessLog;
use App\Model\Registered_msg_queue;
use App\Model\LinePushLog;
use App\Model\Ad_access_log;
use LINE\LINEBot;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnFollowEvent;
use DB;
use PDO;
use Carbon\Carbon;

class FollowService
{
    /**
     * @var LINEBot
     */
    private $bot;
	private $basic_id;

    /**
     * Follow constructor.
     * @param LINEBot $bot
     */
    public function __construct(LINEBot $bot, $basic_id)
    {
        $this->bot = $bot;
		$this->basic_id = $basic_id;
    }

    /**
     * 登録
     * @param FollowEvent $event
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function followExecute(FollowEvent $event)
    {
		try {
			$dbh = DB::connection('mysql')->getPdo();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策
//			throw new \PDOException("テスト例外エラー");
		} catch (\PDOException $e) {
//error_log("pdo error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			return false;
		}

//error_log(print_r($event,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log($event->getType().":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

		$line_id = $event->getUserId();

        try {
            $dbh->beginTransaction();

			$rsp = $this->bot->getProfile($line_id);

			if ( !$rsp->isSucceeded() ) {
//error_log($rsp->getHTTPStatus.":".$rsp->getRawBody()." isSucceeded\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
				$dbh->commit();

				return false;
			}else{

				$stmt = $dbh->prepare("select * from line_users where line_basic_id = :line_basic_id and user_line_id = :user_line_id for update");
				$stmt->bindValue(":line_basic_id", $this->basic_id);
				$stmt->bindValue(":user_line_id", $line_id);
				$stmt->execute();

				$list_data = [];
				while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
					$list_data =  [
						'line_basic_id'		=> $row['line_basic_id'],
						'user_line_id'		=> $row['user_line_id'],
					];
				}

				//１番最初のフォロー
				if( empty($list_data) ){
//error_log("1\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
					$line_user = new LineUser([
						'line_basic_id'	 => $this->basic_id,
						'user_line_id'	 => $line_id,
						'follow_flg'	 => 1,
						'disable'		 => 0,
						'access_date'	 => date('Ymd')
					]);

					$line_user->save();

					$profile = $rsp->getJSONDecodedBody();

					if( empty($profile['statusMessage']) ){
						$profile['statusMessage'] = "";
					}
					if( empty($profile['pictureUrl']) ){
						$profile['pictureUrl'] = "";
					}
//error_log(print_r($profile,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
					DB::insert("insert ignore into line_user_profiles (user_line_id, name, image, message) values(?, ?, ?, ?)", [$profile['userId'], $profile['displayName'], $profile['pictureUrl'], $profile['statusMessage']]);
/*
					$line_user_prof = new LineUserProfile([
						'user_line_id'	 => $profile['userId'],
						'name'			 => $profile['displayName'],
						'image'			 => $profile['pictureUrl'],
						'message'		 => $profile['statusMessage'],
					]);

					$line_user_prof->save();
*/
				//２回目以降のフォロー/アンフォロー
				}else{
					$now_date = Carbon::now();

					//フォロー
					if( $event->getType() == "follow" ){
						$stmt = $dbh->prepare("update line_users set follow_flg = :follow_flg, access_date = :access_date, updated_at = :updated_at where line_basic_id = :line_basic_id and user_line_id = :user_line_id");
						$stmt->bindValue(":follow_flg", 1, PDO::PARAM_INT);
						$stmt->bindValue(":access_date", (int)date('Ymd'), PDO::PARAM_INT);
//						$stmt->bindValue(":created_at", $now_date);
						$stmt->bindValue(":updated_at", $now_date);
						$stmt->bindValue(":line_basic_id", $this->basic_id);
						$stmt->bindValue(":user_line_id", $line_id);
						$stmt->execute();
					}
				}

				//登録後配信データ取得
				$push_db_data = LinePushLog::where('line_basic_id', $this->basic_id)->where('send_type', 4)->where('send_status', 0)->get();

				//登録後配信データがあれば
				if( !empty($push_db_data) ) {
					foreach($push_db_data as $lines){
						//既にある同じデータは削除
						$delete = Registered_msg_queue::where('line_push_id', $lines->id)->where('user_line_id', $line_id)->delete();


						//登録後配信データごとに登録したユーザーIDを登録
						$regist_msg_queue = new Registered_msg_queue([
							'line_push_id'	 => $lines->id,
							'user_line_id'	 => $line_id,
						]);

						$regist_msg_queue->save();
					}
				}

				$dbh->commit();

				return true;
			}

        } catch (Exception $e) {
            $dbh->rollback();
            return false;
        }
    }

    /**
     * 解除
     * @param FollowEvent $event
     * @return bool
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function unfollowExecute(UnFollowEvent $event)
    {
		try {
			$dbh = DB::connection('mysql')->getPdo();
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
			$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策
//			throw new \PDOException("テスト例外エラー");
		} catch (\PDOException $e) {
//error_log("pdo error\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
			return false;
		}

//error_log(print_r($event,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log($event->getType().":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

		$line_id = $event->getUserId();

        try {
            $dbh->beginTransaction();

			$rsp = $this->bot->getProfile($line_id);

			if ( !$rsp->isSucceeded() ) {
//error_log("isSucceeded\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
				$stmt = $dbh->prepare("select * from line_users where line_basic_id = :line_basic_id and user_line_id = :user_line_id for update");
				$stmt->bindValue(":line_basic_id", $this->basic_id);
				$stmt->bindValue(":user_line_id", $line_id);
				$stmt->execute();

				$list_data = [];
				while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
					$list_data =  [
						'line_basic_id'		=> $row['line_basic_id'],
						'user_line_id'		=> $row['user_line_id'],
						'block24h'			=> $row['block24h'],
						'created_at'		=> $row['created_at']
					];
				}

				//２回目以降のフォロー/アンフォロー
				if( !empty($list_data) ){
					//アンフォロー
					if( $event->getType() == "unfollow" ){
						$now_date = Carbon::now();
						
						$elapsed_day = $now_date->diffInHours($list_data['created_at']);
						if( $elapsed_day < 24 ){
							if( $list_data['block24h'] == 0 ){
								$list_data['block24h'] = 1;
							}
						}

						$stmt = $dbh->prepare("update line_users set follow_flg = :follow_flg, block24h = :block24h, access_date = :access_date, updated_at = :updated_at where line_basic_id = :line_basic_id and user_line_id = :user_line_id");
						$stmt->bindValue(":follow_flg", 0, PDO::PARAM_INT);
						$stmt->bindValue(":block24h", $list_data['block24h'], PDO::PARAM_INT);
						$stmt->bindValue(":access_date", (int)date('Ymd'), PDO::PARAM_INT);
						$stmt->bindValue(":updated_at", $now_date);
						$stmt->bindValue(":line_basic_id", $this->basic_id);
						$stmt->bindValue(":user_line_id", $line_id);
						$stmt->execute();
					}
				}

				$dbh->commit();

				return false;
			}else{

				$profile = $rsp->getJSONDecodedBody();
//error_log(print_r($profile,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");

				return true;
			}

        } catch (Exception $e) {
            $dbh->rollback();
            return false;
        }
    }

	/*
	 * 
	 */
	public function updateTrackAccess($basic_id, $line_id)
	{
/*
		$db_data = TrackAccessLog::where('line_basic_id', $basic_id)
					->where('access_ip', $access_ip)
					->where('ad_cd', $ad_cd)
					->where('status', 0)
					->first();
 */
//		$db_data = DB::select("select line_basic_id from track_access_logs where csrf_token is not null and line_basic_id = '{$basic_id}' and status = 0 and now() <= (created_at + interval 15 minute)");
		$db_data = DB::select("select line_basic_id from track_access_logs where csrf_token is not null and line_basic_id = '{$basic_id}' and status = 0 limit 1");
//error_log(print_r($db_data,true)."\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log(count($db_data).":count\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		if( count($db_data) > 0 && !empty($db_data[0]->line_basic_id) ){
//			$db_data = DB::update("update track_access_logs set user_line_id = '{$line_id}' where csrf_token is not null and line_basic_id = '{$basic_id}' and status = 0 and now() <= (created_at + interval 15 minute)");
			$db_data = DB::update("update track_access_logs set user_line_id = '{$line_id}' where csrf_token is not null and line_basic_id = '{$basic_id}' and status = 0");
		}else{
			$date = date("Ymd");

			//リファラ―取得
			$referrer = '';
			if( empty($referrer) ){
				if( isset($_SERVER['HTTP_REFERER']) ){
					list($url, $params) = explode("?", $_SERVER['HTTP_REFERER']);
					$listParam = explode("&", $params);
					foreach($listParam as $param){
						list($key, $value) = explode("=", $param);
						if( $key == 'referrer' ){
							$referrer = $value;
						}
					}
				}
			}

			$insert_val = [
				'line_basic_id'		 => $basic_id,
				'user_line_id'		 => $line_id,
				'env_data'			 => json_encode($_SERVER),
				'line_basic_id'		 => $basic_id,
				'csrf_token'		 => csrf_token(),
				'xuid'				 => '99k',
				'asp_id'			 => 0,
				'ad_cd'				 => '99k',
				'status'			 => 0,
				'access_date'		 => $date
			];

			if( isset($_SERVER['REQUEST_URI']) ){
				$insert_val = array_merge($insert_val, ['script_name' => $_SERVER['REQUEST_URI']]);
			}

			if( !empty($referrer) ){
				$insert_val = array_merge($insert_val, ['access_referrer' => $referrer]);
			}

			if( isset($_SERVER['REMOTE_ADDR']) ){
				$insert_val = array_merge($insert_val, ['access_ip' => $_SERVER['REMOTE_ADDR']]);
			}

			if( isset($_SERVER['HTTP_USER_AGENT']) ){
				$insert_val = array_merge($insert_val, ['access_ua' => $_SERVER['HTTP_USER_AGENT']]);
			}

			$track_access_log = new TrackAccessLog($insert_val);

			$track_access_log->save();

			//広告集計
			$db_ad_access = Ad_access_log::where('line_basic_id', $basic_id)->where('ad_cd', '99k')->where('access_date', $date)->first();

			if( empty($db_ad_access) ){
				$ad_access_log = new Ad_access_log([
					'line_basic_id'		=> $basic_id,
					'ad_cd'				=> '99k',
					'pv'				=> 0,
					'uu'				=> 0,
					'reg'				=> 0,
					'access_date'		=> $date
				]);

				$ad_access_log->save();
			}
		}
	}
}
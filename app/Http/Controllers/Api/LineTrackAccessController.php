<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\TrackAccessLog;

class LineTrackAccessController extends Controller
{
	public function __construct(Request $request)
	{

	}

	/*
	 * 
	 */
	public function insertTrackAccess(Request $request, $basic_id = null, $ad_cd = null)
	{
//error_log(print_r($_SERVER,true).":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
//error_log(print_r($request->all(),true).":test\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		//リファラ―取得
		$referrer = $request->input("referrer");
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
			'env_data'			 => json_encode($_SERVER),
			'line_basic_id'		 => $basic_id,
			'csrf_token'		 => csrf_token(),
			'xuid'				 => $request->input("xuid"),
			'asp_id'			 => $request->input("asp"),
			'ad_cd'				 => $ad_cd,
			'status'			 => 0,
			'access_date'		 => date("Ymd")
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
	}
}

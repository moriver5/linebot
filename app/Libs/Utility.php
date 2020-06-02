<?php

namespace App\Libs;

use Auth;
use App\Model\Convert_table;
use App\Model\Admin;
use App\Model\Agency;
use App\Model\User;
use App\Model\Achievement;
use App\Model\Voice;
use App\Model\Email_ng_word;
use App\Model\Relay_server;
use App\Model\Banner;
use App\Model\Landing_page;
use App\Model\Line_short_url;
use App\Model\Line_click_user;
use Carbon\Carbon;

class Utility
{
	/*
	 * 
	 */
	public function getClicksLog(){
		
	}

	/*
	 * URLから短縮URL文字列を生成する
	 */
	public function getShortUrl($url, $length = null)
	{
		//$lengthの指定がない場合、デフォルト値を使用
		if( is_null($length) ){
			$length = config('const.short_url_length');
		}

		$db_data = Line_short_url::where('url', $url)->first();

		//ショートURLがあれば
		if( !empty($db_data) ){
			return $db_data->short_url;

		//ショートURLがなければ
		}else{
			$stop_count = 0;
			do{
				//ランダム文字列生成
				$short_url = $this->makeRandStr($length);

				//ランダム文字列が既に存在するか確認
				$count = Line_short_url::where('short_url', $short_url)->count();
				if( $count == 0 ){
					//短縮URLを保存
					$db_obj = new Line_short_url([
						'url'		=> $url,
						'short_url'	=> $short_url
					]);

					$db_obj->save();
				}

				//11回以上作成しても同じ文字列ならループを出る
				if( $stop_count++ > 10 ){
					break;
				}
			}while( $count > 0 );
		}

		return $short_url;
	}

	/*
	 * URLから短縮URLを生成する
	 */
	public function getAccessShortUrl($url, $length = null)
	{
		return config('const.base_url').'/'.$this->getShortUrl($url, $length);
	}

	/*
	 * 
	 */
	public function getUniqueShortUrl($basic_id, $push_id = 0, $url, $line_id, $length = null)
	{
		//$lengthの指定がない場合、デフォルト値を使用
		if( is_null($length) ){
			$length = config('const.short_url_length');
		}
//error_log("short url\n",3,"/data/www/line/storage/logs/nishi_log.txt");
		$query = Line_click_user::query();

		//既にショートURLがあれば
		$db_data = $query->where('line_push_id', $push_id)->where('user_line_id', $line_id)->where('url', $url)->first();
		if( !empty($db_data) ){
			return $db_data->short_url;
		}

		//同じPUSH IDでLINE IDごとにショートURLを生成
		do{
			//ショートURLを生成
			$short_url = $this->makeRandStr($length);

			//ショートURLが既に存在するのか確認
			$count = $query->where('line_push_id', $push_id)->where('short_url', $short_url)->count();

			//ショートURLがなければ保存
			if( $count == 0 ){
				$db_obj = new Line_click_user([
					'line_basic_id'		=> $basic_id,
					'line_push_id'		=> $push_id,
					'user_line_id'		=> $line_id,
					'short_url'			=> $short_url,
					'url'				=> $url
				]);

				$db_obj->save();
			}
		}while( $count > 0 );

		return $short_url;
	}

	/*
	 * ランダムな英数字の文字列を作成
	 */
	function makeRandStr($length) {
		$str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
		$r_str = null;
		for ($i = 0; $i < $length; $i++) {
			$r_str .= $str[rand(0, count($str) - 1)];
		}
		return $r_str;
	}

	/*
	 * 
	 */
	public function getDayOfWeek($date)
	{
		if( empty($date) ){
			return;
		}

		list($date, $time) = explode(" ", $date);
		list($year, $mon, $day) = explode("-", $date);

		//指定日の曜日を取得する
		$timestamp = mktime(0, 0, 0, $mon, $day, $year);
		$date = date('w', $timestamp);

		//配列を使用し、要素順に(日:0〜土:6)を設定する
		$week = [
		  '日', //0
		  '月', //1
		  '火', //2
		  '水', //3
		  '木', //4
		  '金', //5
		  '土', //6
		];

		return sprintf("%2d", $mon).'/'.sprintf("%d", $day).' ('.$week[$date].')';
	}

	/*
	 * 
	 */
	public function getWeeks($weeks)
	{
		if( empty($weeks) ){
			return;
		}

		$listWeeks = explode(",", $weeks);

		//配列を使用し、要素順に(日:0〜土:6)を設定する
		$week = [
		  '日', //0
		  '月', //1
		  '火', //2
		  '水', //3
		  '木', //4
		  '金', //5
		  '土', //6
		];

		$listConvertWeeks = [];
		foreach($listWeeks as $week_index){
			$listConvertWeeks[] = $week[$week_index];
		}
		
		return implode(",", $listConvertWeeks);
	}

	/*
	 * 
	 */
	public function checkMatchWeek($date, $list_target_index)
	{
		if( empty($list_target_index) ){
			return;
		}

		list($date, $time) = explode(" ", $date);
		list($year, $mon, $day) = explode("-", $date);

		//指定日の曜日を取得する
		$timestamp = mktime(0, 0, 0, $mon, $day, $year);
		$week_index = date('w', $timestamp);

		//今日の曜日と比較対象の曜日が違っていたら
		if( !preg_match("/^\d+$/", array_search($week_index, $list_target_index)) ){
			return false;;
		}

		//今日の曜日と比較対象の曜日が同じなら
		return true;
	}

	/*
	 * 
	 */
	public function getMakePostData($list_post_data){
		$list_exclusion_param = implode("|", config('const.exclusion_param'));

		$listPostData = [];
		foreach($list_post_data as $key => $value){
			if( preg_match("/{$list_exclusion_param}/", $key) == 0 ){
				$listPostData[] = "{$key}={$value}";
			}
		}

		//ポストデータがあるとき
		if( !empty($listPostData) ){
			return "?".implode("&", $listPostData);
		}else{
			return;
		}
	}

	/*
	 * アクセス元IPアドレスを取得
	 */
	public function getClientIp(){
		$list_key = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
		foreach ($list_key as $key){
			if ( array_key_exists($key, $_SERVER) === true ){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
	}

	/*
	 * /etc/httpd/conf.d/v_dev.m-invest.info.confのServerAliasの設定を見て
	 * ドメインのリストを取得する
	 * 引数：
	 */
	public function getDomainList($setting_full_path_file){
		$setting_domain = `cat {$setting_full_path_file} | grep -e ServerAlias -e ServerName | sed -E "s/\s*(ServerAlias|ServerName)\s*//g"`;
		$list_domain = explode("\n", trim($setting_domain));
		$list_domain = array_unique($list_domain);
		return $list_domain;
	}

	/*
	 * convert_tablesテーブルから変換文字列リストを取得し変換を行う
	 * 引数：
	 */
	public function getConvertData($convert_str, $group_id = null){
		//%変換文字列を取得
//		$db_convert_str = Convert_table::get();
		$query = Convert_table::query();
		
		if( !empty($group_id) ){
			$query->where('group_id', $group_id);
		}
		
		$db_convert_str = $query->get();
		
		//%変換処理
		foreach($db_convert_str as $lines){	
			//%変換処理
			$convert_str = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $convert_str);
		}
		
		return $convert_str;
	}

	/*
	 * convert_tablesテーブルから変換文字列リストを取得し変換を行う
	 * 引数：メール本文、メール件名
	 */
	public function getMailConvertData($body, $subject, $from_name, $from_mail, $group_id = null){
		//%変換文字列を取得
//		$db_convert_str = Convert_table::get();
		$query = Convert_table::query();
		
		if( !empty($group_id) ){
			$query->where('group_id', $group_id);
		}
		
		$db_convert_str = $query->get();
		
		//%変換処理
		foreach($db_convert_str as $lines){	
			//%変換処理
			$from_name = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $from_name);
			$from_mail = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $from_mail);
			$subject = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $subject);
			$body	 = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $body);
		}
		
		return [$body, $subject, $from_name, $from_mail];
	}

	/*
	 * 管理ページのデフォルトのパラメータを取得
	 */
	public function getAdminDefaultDispParam()
	{
		//認証情報取得
		$user = \Auth::guard('admin')->user();

		//オブジェクトがなかったらfalse
		if( empty($user) ){
			return false;
		}

		$db_data = Admin::where('email', $user->email)->first();
		
		//画面文言用変数
		return [
			'id'			=> $user->id,
			'login_id'		=> $user->email,
			'auth_type'		=> $db_data->type,
		];
	}

	/*
	 * 代理店管理ページのデフォルトのパラメータを取得
	 */
	public function getAgencyDefaultDispParam()
	{
		//認証情報取得
		$user = \Auth::guard('agency')->user();

		//オブジェクトがなかったらfalse
		if( empty($user) ){
			return false;
		}

		$db_data = Agency::where('login_id', $user->login_id)->first();
		
		//画面文言用変数
		return [
			'agency_id'		=> $db_data->id,
			'name'			=> $db_data->name,
			'login_id'		=> $db_data->login_id,
			'password_raw'	=> $db_data->password_raw,
			'memo'			=> $db_data->memo,
		];
	}
	
	/*
	 * 顧客会員ページのデフォルトのパラメータを取得
	 */
	public function getDefaultDispParam()
	{
		//認証情報取得
		$user = \Auth::guard('user')->user();
		
		//画面文言用変数
		return [
			'login_id'		=> $user->login_id,
			'client_id'		=> $user->id,
			'point'			=> $user->point,
			'tel'			=> $user->credit_certify_phone_no,
			'email'			=> $user->mail_address,
			'group_id'		=> $user->group_id,
			'ad_cd'			=> $user->ad_cd,
			'status'		=> $user->status,
			'pay_count'		=> $user->pay_count,
			'password_raw'	=> $user->password_raw,
		];
	}

	/*
	 * メールアドレスが携帯かPCか判定
	 * return 携帯：null PC：false
	 */
	public function judgeMobileDevice($email){
		$MOBILR_MATCH = implode("|", config('const.list_mobile_domain'));

		//PCドメインなら
		if( preg_match("/{$MOBILR_MATCH}/", $email) == 0 ){
			return false;
		}

		//携帯ドメインなら
		return null;
	}

	public function calcFileSize($size)
	{
		$b = 1024;    // バイト
		$mb = pow($b, 2);   // メガバイト
		$gb = pow($b, 3);   // ギガバイト

		switch(true){
			case $size >= $gb:
				$target = $gb;
				$unit = 'GB';
				break;
			case $size >= $mb:
				$target = $mb;
				$unit = 'MB';
				break;
			default:
				$target = $b;
				$unit = 'KB';
			break;
		}

		$new_size = round($size / $target, 2);
		$file_size = number_format($new_size, 2, '.', ',') . $unit;

		return $file_size;
	}

	/*
	 * メールアドレスに禁止ワードがかるかどうかチェック
	 */
	public function checkNgWordEmail($email)
	{
		$db_data = Email_ng_word::where('type', 'mail')->first();
		if( !empty($db_data->word) ) {
			$ng_word_match = preg_replace("/,/", "|", preg_quote($db_data->word, '/'));
			if( preg_match("/{$ng_word_match}/", $email) > 0 ){
				return false;
			}
		}
		return null;
	}

	/*
	 * 設定されているSMTPを取得
	 */
	public function getSmtpHost($type)
	{
		$smtp_ip	 = null;
		$port		 = null;

		$db_data = Relay_server::where('type', $type)->first();

		if( !empty($db_data->ip) ){
			$smtp_ip = $db_data->ip;
		}

		if( !empty($db_data->port) ){
			$port = $db_data->port;
		}

		return [$smtp_ip, $port];
	}

	/*
	 * バナーを取得する
	 */
	public function getBanner()
	{
		//バナーデータ取得
		$db_banner_data = Banner::where('disp_flg', 1)
		->orderBy('created_at','asc')->get();

		//データがあれば
		if( !empty($db_banner_data) ){
			//javascriptタグの開始終了タグをエスケープ
			foreach($db_banner_data as $lines){
				$lines->banner = Utility::escapeJsTag($lines->banner);
			}
		}

		return $db_banner_data;
	}

	/*
	 *	javascriptの開始終了タグをエスケープする
	 */
	public function escapeJsTag($contents)
	{
		if( !empty($contents) ){
			return preg_replace("/<(.*?)script(.*?)>/", "&lt;$1script$2&gt;", $contents);
		}
	}

	/*
	 *	MXドメインが存在するか確認
	 */
	public function checkMxDomain($email)
	{
		list($account, $mail_domain) = explode("@", trim($email));

		//MXドメインを取得
		$exist_flg = getmxrr($mail_domain, $listMxDomain);

		//MXドメインがない場合
		if( !$exist_flg ){
			return false;
		}

		return null;
	}

	/*
	 * 現在時刻が引数で指定した期間内かどうかチェック
	 * $start_date:yyyy-mm-dd hh:mm:ss(開始時刻)
	 * $end_date:yyyy-mm-dd hh:mm:ss(終了時刻)
	 */
	function checkNowDateWithinPeriod($start_date, $end_date){
		$start_date = new Carbon($start_date);
		$end_date = new Carbon($end_date);

		$now_date = Carbon::now();

		//期間内：true
		//期間外：false
		return Carbon::parse($now_date)->between($start_date, $end_date);
	}

}


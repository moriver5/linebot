<?php
namespace App\Validator;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Validation\Validator;
use App\Http\Requests\KeibaRequest;
use App\Model\User;
use App\Model\Admin;
use DB;

class CustomValidator extends Validator
{
	/*
	 * 
	 */
	public function validateCheckUploadImageWidth($attribute, $image, $rule)
	{
		//アップロード画像のサイズ取得
		list($width, $height, $type, $attr) = getimagesize($image->path());
//error_log("$width, $height, $type, $attr:name\n",3,"/data/www/line/storage/logs/nishi_log.txt");

		//アップロード画像の幅が1040px以外ならエラー
		if( $width != 1040 ){
			return false;
		}

		return true;
	}

	/*
	 * 同ドメイン内で同名のファイルが作成されているか確認
	 */
	public function validateCheckExistLpFile($attribute, $page_name, $rule)
	{
		//
		$db_data = DB::select("select * from landing_pages_contents where lp_id = {$rule[0]} and name = '{$page_name}'");

		//データが存在したら
		if( !empty($db_data[0]->lp_id) ){
			return false;
		}

		return true;
	}

	/*
	 * 24時間以内で同じグループかつ同じアクセス元IPからの登録かどうかチェック
	 */
	public function validateCheckAccessIp($attribute, $email, $rule)
	{
		//許可IP
		$list_ip = config('const.admin_access_allow_ip');
		if( !empty($list_ip) ){
			$ip_match = join("|", $list_ip);
			$ip_match = preg_replace("/\./", "\\.", $ip_match);
			if( preg_match("/$ip_match/", $_SERVER['REMOTE_ADDR']) > 0 ){
				return true;
			}
		}

		//同じグループ・同じアクセス元IP・24時間以内のデータを取得
		$db_data = DB::select("select * from ban_access_ips where access_ip = '{$rule[0]}' and group_id = {$rule[1]} and now() <= (created_at + interval ".config('const.del_ban_access_ip_inserval').")");
//error_log(print_r($db_data,true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

		//データが存在したら
		if( !empty($db_data[0]->limit_access) && $db_data[0]->limit_access >= config('const.ip_access_limit') && !empty($db_data[0]->access_ip) ){
			return false;
		}

		return true;
	}

	/*
	 * 管理者のパスワードが設定されているのかチェック
	 */
	public function validateCheckEmptyPassword($attribute, $password, $login_id)
	{
		//本登録データを取得
		$db_data = Admin::where('email', $login_id)->first();

		//パスワードが存在しなかったら
		if( empty($db_data->password) ){
			return false;
		}
		
		return true;
	}

	/*
	 * ステータスが本登録かどうかチェック
	 */
	public function validateCheckRegistStatus($attribute, $login_id, $rule)
	{
		//本登録データを取得
		$db_data = User::where('login_id', $login_id)->where('status', config('const.regist_status')[0][0])->first();

		//本登録データが存在しなかったら
		if( empty($db_data) ){
			return false;
		}
		
		return true;
	}

	/*
	 * ドメインが存在するかチェック
	 */
	public function validateCheckExistDomain($attribute, $domain, $rule)
	{
		$ip = gethostbyname($domain);
		$long = ip2long($ip);

		//ドメインが存在しなかったら
		if( $long === false || $ip !== long2ip($long) ){ 
			return false;
		}
		
		return true;
	}

	/*
	 * DBに登録されているメールアドレスと同じかどうかチェック
	 */
	public function validatePasswordRegistedMatch($attribute, $password, $rule)
	{
		//DBに登録されているパスワードと同じか確認
		$db_data = User::where('password', bcrypt($password))->first();

		//DBに登録されているパスワードと違っていたら
		if( empty($db_data) ){
			return false;
		}
		
		return true;
	}

	/*
	 * メールアドレスのドメイン名が指定のドメインかチェック
	 */
	public function validateCheckEmailDomain($attribute, $email, $rule)
	{
		$domain = '';
		
		//ドメインが存在すれば
		$listEmail = explode("@", $email);
		if( !empty($listEmail[1]) ){
			$domain = $listEmail[1];
		}
		
		//リストのドメインを|で結合し正規表現に利用
		$listDomain = config('const.admin_member_domain');
		$admin_member_domain = implode("|",$listDomain);
		
		//指定のドメイン名が含まれていなければfalse
		if( !preg_match('/'.$admin_member_domain.'$/', $domain) ){
			return false;
		}
		
		return true;
	}
	
	/*
	 * メールアドレスのアカウント文字の形式が正しいのかチェック
	 */
	public function validateEmailAccount($attribute, $account, $rule)
	{
		//メールアカウントに使用できない文字が含まれていれば
		if( $account != '' && !preg_match("/^[a-z0-9\.\-\_]+$/", $account) ){
			return false;
		}
		
		return true;
	}
	
	/*
	 * 空白文字が含まれているかチェック
	 */
	public function validateIsSpace($attribute, $value, $rule)
	{
		//空白が文字が含まれていればNG
		if( preg_match("/[\s]/", $value) ){
			return false;
		}
		
		return true;
	}

	/*
	 * 入力されているかチェック
	 */
	public function validateIsRequired($attribute, $value, $rule)
	{
		//空白が文字が含まれていればNG
		if( preg_match("/^[\s]$/", $value) ){
			return false;
		}
		
		return true;
	}
	
	/*
	 * アカウントが承認済かどうか確認
	 * adminsテーブルのtypeが0以外なら承認済
	 */
	public function validateCheckApproved($attribute, $email, $rule)
	{
		$db_data = Admin::where('email', $email)->first();
		
		//adminsテーブルのtypeが0なら未承認
		if( $db_data->type == 0 ){
			return false;
		}
		
		return true;
	}
	
	/*
	 * 半角数字と-のみ含まれているかどうかチェック
	 */
	public function validateCheckAddPoint($attribute, $number, $rule)
	{	
		//指定フォーマットかどうか確認
		//例：-100、100
		if( preg_match("/^\-?[0-9]+$/", $number) == 0 ){
			return false;
		}
		
		return true;
	}

	/*
	 * 正しいリレーサーバーかチェック
	 */
	public function validateCheckRelayServer($attribute, $relay_server, $rule)
	{	
		//正しいIPドレス(リレーサーバー)かどうか確認
		if( $relay_server != '' && preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $relay_server) == 0 ){
			return false;
		}
		
		return true;
	}

	/*
	 * ファイル名が使用可能かチェック
	 */
	public function validateCheckFileName($attribute, $file_name, $rule)
	{	
		//禁止文字が含まれているか確認
		if( $file_name == '' || preg_match("/^[a-zA-Z0-9\.]+$/", $file_name) == 0 ){
			return false;
		}
		
		return true;
	}

	/*
	 *	MXドメインが存在するか確認
	 */
	public function validateCheckMxDomain($attribute, $email, $rule)
	{
		list($account, $mail_domain) = explode("@", $email);

		//MXドメインを取得
		$exist_flg = getmxrr($mail_domain, $listMxDomain);

		//MXドメインがない場合
		if( !$exist_flg ){
			return false;
		}

		return true;
	}

	/*
	 * laravelのvalidateのalpha_numが日本語許可のため半角英数字チェックを別途作成
	 * 半角英数字のみかチェック
	 */
	public function validateAlphaNumCheck($attribute, $str, $rule)
	{
		//文字列に半角英数字以外が含まれていたら
		if( $str != '' && !preg_match("/^[a-z0-9]+$/", $str) ){
			return false;
		}
		
		return true;
	}

	/*
	 * 日付のフォーマットチェック
	 */
	public function validateDateFormatCheck($attribute, $date, $rule)
	{
		//指定フォーマット以外なら
		if( $date != '' && !preg_match("/^\d{4}[\-\/]\d{2}[\-\/]\d{2}(\s\d{2}:\d{2})?(:\d{2})?$/", $date) ){
			return false;
		}

		return true;
	}

	/*
	 * 半角数字のフォーマットなのかチェック
	 */
	public function validateDigitalNumCheck($attribute, $num, $rule)
	{
		//半角数字のカンマ区切り以外ならエラー
		if( $num != '' && !preg_match("/^(\d+?){1,}$/", $num) ){
			return false;
		}

		return true;
	}

	/*
	 * 半角数字の半角カンマ区切りのフォーマットなのかチェック
	 */
	public function validateOnlyNumCheck($attribute, $groups, $rule)
	{
		//半角数字のカンマ区切り以外ならエラー
		if( $groups != '' && !preg_match("/^(\d+,?){1,}$/", $groups) ){
			return false;
		}

		return true;
	}

	/*
	 * 存在するグループIDなのかチェック
	 */
	public function validateExistGroupIdCheck($attribute, $groups, $rule)
	{
		$db_data = DB::table('groups')->pluck('id');

		//DBから取得したグループIDの配列を生成
		$listRegGroupId = [];
		if( !empty($db_data) ){
			foreach($db_data as $id){
				$listRegGroupId[] = $id;
			}
		}

		//入力されたグループIDの配列を生成
		$listGroupId = explode(",", $groups);

		//DBに登録されているグループIDと入力されたグループIDの差分
		//DBに存在しないグループIDが結果に返される
		$listNoMatchGroupId = array_diff($listGroupId, $listRegGroupId);

		//空ならエラー
		if( $groups != '' && !empty($listNoMatchGroupId) ){
			return false;
		}

		return true;
	}

	/*
	 * 存在するキャンペーンIDなのかチェック
	 */
	public function validateExistCampaignIdCheck($attribute, $campaigns, $rule)
	{
		//TOPコンテンツからキャンペーンIDを取得
		$db_data = DB::table('top_contents')->pluck('id');

		//DBから取得したキャンペーンIDの配列を生成
		$listRegCampaignId = [];
		if( !empty($db_data) ){
			foreach($db_data as $id){
				$listRegCampaignId[] = $id;
			}
		}

		//入力されたキャンペーンIDの配列を生成
		$listCampaignId = explode(",", $campaigns);

		//DBに登録されているキャンペーンIDと入力されたキャンペーンIDの差分
		//DBに存在しないキャンペーンIDが結果に返される
		$listNoMatchCampaignId = array_diff($listCampaignId, $listRegCampaignId);

		//存在しないキャンペーンIDがあればエラー
		if( $campaigns != '' && !empty($listNoMatchCampaignId) ){
			return false;
		}

		return true;
	}

	/*
	 * 使用可能な文字なのかチェック
	 */
	public function validateUseCharCheck($attribute, $str, $rule)
	{
		//指定の文字列以外ならエラー
		if( $str != '' && !preg_match("/^[a-zA-Z0-9&!#$%'()*+,-:;<=>?@^_{}\"\|~\.\\\\[\]\/]+$/", $str) ){
			return false;
		}

		return true;
	}

	/*
	 *	文字列に絵文字が含まれているかチェック
	 */
	function validateEmojiCheck($attribute, $str, $rule)
	{
		//スマホの絵文字が含まれていればエラー
		if( $str != '' && preg_match('/[\xF0-\xF7][\x80-\xBF][\x80-\xBF][\x80-\xBF]/', $str) > 0 ) {
			return false;
		}

		//ガラケーの絵文字が含まれていればエラー
		if( $str != '' && preg_match('/(?:\xEE[\x80\x81\x84\x85\x88\x89\x8C\x8D\x90-\x9D\xAA-\xAE\xB1-\xB3\xB5\xB6\xBD-\xBF]|\xEF[\x81-\x83])[\x80-\xBF]/', $str) > 0 ) {
			return false;
		}

		return true;
	}

	/*
	 *	サロゲートペアの文字列が含まれているかチェック
	 */
	function validateSurrogatePairCheck($attribute, $str, $rule)
	{
		//サロゲートペアが含まれていればエラー
		if ( $str != '' && preg_match('/[\xd8-\xdb][\x00-\xff][\xdc-\xdf][\x00-\xff]/u', mb_convert_encoding($str, 'UTF-16', 'UTF-8')) > 0 ) {
			return false;
		}

		return true;
	}
}

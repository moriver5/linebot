<?php

namespace App\Http\Controllers\Auth;

use App\Model\User;
use App\Model\Mail_content;
use App\Convert_table;
use Illuminate\Http\Request;
use App\Http\Requests\KeibaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\SendMail;
use App\Libs\SysLog;
use App\Model\Grant_point;
use App\Model\Registered_mail_queue;
use App\Model\Group;
use App\Model\Landing_page;
use Utility;
use Mail;
use Session;
use Carbon\Carbon;
use DB;
use Cookie;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/member/home';
	protected $log_obj;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
		$this->log_obj = new SysLog(config('const.client_nonmember_log_name'), config('const.client_log_dir_path').config('const.client_nonmember_file_name'));
	}
	
	/**
	 * メールアドレス登録ボタン押下後、呼び出される
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function create(Request $request)
	{
		//ランディングページにgroup_idとlpidのパラメータが未設定ならエラー画面表示
		if( empty($request->input('group_id')) || 
			empty($request->input('lpid')) ){
			return view('errors.request_err');
		}

		//存在しないgroup_idならエラー画面表示
		$exist_data = Group::where('id', $request->input('group_id'))->first();
		if( empty($exist_data) ){
			return view('errors.none_gid_err');	
		}

		//存在しないlpidならエラー画面表示
		$exist_data = Landing_page::where('id', $request->input('lpid'))->first();
		if( empty($exist_data) ){
			return view('errors.none_lpid_err');	
		}

		//アクセス元IP取得
		$access_ip = Utility::getClientIp();

		//登録メールアドレスのエラーチェック
		//メールアドレスの未入力/形式/長さ/重複チェック
		$validator = $this->validate($request, [
//		$validator = Validator::make($request->all(),[
//			'email'		 => 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain|check_access_ip:'.$access_ip.','.$request->input('group_id'),
			'email'		 => 'bail|required|email|max:'.config('const.email_length').'|check_mx_domain',
			'group_id'	 => 'bail|required',
			'lpid'		 => 'bail|required',
		]);

		//POSTデータ取得(メールアドレス&グループID)
		$to_email = mb_strtolower(trim($request->input('email')));

		//メールアドレスに禁止ワードが含まれているかチェック
		$err_flg = Utility::checkNgWordEmail($to_email);

		//ページ名を取得
		$page_name = $request->input('page_name');
/*
		//エラーがある場合
		if ( $validator->fails() ) {
			$file = config('const.lp_default_page')[4];
			if( !empty($page_name) ){
				$file = $page_name.'/'.config('const.lp_default_page')[4];
			}
error_log(print_r($validator->errors(),true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
			//完了ページへリダイレクト
			return redirect($file);
        }
*/
		//メールアドレスに禁止ワードが含まれていたらランディングページへリダイレクト
		if( !is_null($err_flg) ){
			$file = '';
			if( !empty($page_name) ){
				$file = $page_name;
			}
			return redirect($file);
		}

		//グループID取得
		$group_id = mb_strtolower(trim($request->input('group_id')));

		//許可IP
		$list_ip = config('const.admin_access_allow_ip');
		if( !empty($list_ip) ){
			$ip_match = join("|", $list_ip);
			$ip_match = preg_replace("/\./", "\\.", $ip_match);

			//許可IPリストではなかった場合
			if( preg_match("/$ip_match/", $access_ip) == 0 ){
				//同じグループ・同じアクセス元IP・24時間以内のデータを取得
				$db_data = DB::select("select * from ban_access_ips where access_ip = '{$access_ip}' and group_id = {$group_id} and now() <= (created_at + interval ".config('const.del_ban_access_ip_inserval').")");

				//データが存在したら
				if( !empty($db_data[0]->limit_access) && $db_data[0]->limit_access >= config('const.ip_access_limit') && !empty($db_data[0]->access_ip) ){
					$file = config('const.lp_default_page')[5];
					if( !empty($page_name) ){
						$mailerror_data = DB::select("select url_open_flg from sub_landing_pages_contents where page_name = '{$page_name}' and name= '{$file}' and url_open_flg = 1");
						if( !empty($mailerror_data) ){
							$file = $page_name.'/'.config('const.lp_default_page')[5];
						}
					}

					//任意のポストデータがあればゲットパラメータに変換
					$query_string = Utility::getMakePostData($request->all());
					$file .= $query_string;

					//mailerrorページへリダイレクト
					return redirect($file);
				}
			}
		}

		//既にメルマガ登録済かチェック
		$db_data = User::join('user_groups', 'users.id', '=', 'user_groups.client_id')
			->select('users.mail_address', 'user_groups.remember_token', 'users.send_flg', 'users.disable as mail_disable', 'user_groups.client_id', 'user_groups.group_id', 'user_groups.status', 'user_groups.disable as melmaga_disable')
			->where('users.mail_address', $to_email)
			->where('user_groups.group_id', $group_id)->first();

		//画面に表示するパラメータ
		$disp_data = [
			'title'		=> '', 
		];

		//登録状況によりメール送信
		//購読済:1
		//退会済:2
		//ブラック:3
		if( !empty($db_data) ){
			//登録メールアドレスが無効のとき(usersテーブルのdisableが1)
			//購読メルマガが無効のとき(user_groupsテーブルのdisableが1)
			//メルマガ配信が全停止の場合(usersテーブルのsend_flgが0)
			if( $db_data->mail_disable == 1 || $db_data->melmaga_disable == 1 || $db_data->send_flg == 0 ){
				//テンプレート画面表示で停止
				return view(config('const.disable_mail'));

			//ブラック：3
			}elseif( $db_data->status == config('const.regist_status')[2][0] ){
				//テンプレート画面表示で停止
				return view(config('const.black_email'));

			//購読済：1
			}elseif( $db_data->status == config('const.regist_status')[0][0] ){

				//自動メールのデータ取得
				$db_cnt = Mail_content::where('group_id', $group_id)->where('type', 3)->first();

				//データがあれば
				if( !empty($db_cnt) ){

					//%変換設定では変換できない文字列の処理
					$body = $db_cnt->body;
					$body = preg_replace("/\-%token\-/", $db_data->remember_token, $body);

					//変換後の文字列を取得
					list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail, $group_id);
/*
					//置換文字が含まれていれば置換
					$from_name	= Utility::getConvertData($from_name, $group_id);
					$from_mail	= Utility::getConvertData($from_mail, $group_id);
					$subject	= Utility::getConvertData($subject, $group_id);
					$body		= Utility::getConvertData($body, $group_id);
*/
					//送信元メールアドレスをアカウントとドメインに分割
					list($account, $domain) = explode("@", $from_mail);

					//送信元メールアドレスのドメインで返信先メールアドレスを生成
					$replay_to_mail	 = config('const.replay_to_mail').'@'.$domain;

					//送信元メールアドレスのドメインでリターンパスのメールアドレスを生成
					$return_path_mail	= config('const.return_path_to_mail').'@'.$domain;

					list($host_ip, $port) = Utility::getSmtpHost('setting');

					//送信元情報設定
					$options = [
						'replay_to'		=> $replay_to_mail,
						'received'		=> $domain,
						'return_path'	=> $return_path_mail,
						'host_ip'		=> $host_ip,
						'port'			=> $port,
						'from'			=> $from_mail,
						'from_name'		=> $from_name,
						'subject'		=> $subject,
						'template'		=> config('const.registered'),
					];

					//送信データ設定
					$data = [
						'contents'		=> $body,
					];

					//登録メールアドレス先へメール送信
					Mail::to($to_email)->send( new SendMail($options, $data) );

					$disp_data = array_merge(['msg_flg' => 2 ],$disp_data);
				}else{
					//データがないときのメッセージ
					$disp_data = array_merge(['msg_flg' => 0 ],$disp_data);
				}

				$file = config('const.lp_default_page')[5];
				if( !empty($page_name) ){
					$mailerror_data = DB::select("select url_open_flg from sub_landing_pages_contents where page_name = '{$page_name}' and name= '{$file}' and url_open_flg = 1");
					if( !empty($mailerror_data) ){
						$file = $page_name.'/'.config('const.lp_default_page')[5];
					}
				}

				//任意のポストデータがあればゲットパラメータに変換
				$query_string = Utility::getMakePostData($request->all());
				$file .= $query_string;

				//完了ページへリダイレクト
				return redirect($file);
			}
		}
	
		//アクセスキー生成
		$remember_token = session_create_id();		

		$now_date = Carbon::now();

		$db_user_data = User::where('mail_address', $to_email)->first();

		//usersテーブルに登録されていなければ
		if( empty($db_user_data) ){
			$db_value = [
				'mail_address'		 => $to_email,
				'created_at'		 => $now_date,
				'regist_date'		 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $now_date),
				'last_access'		 => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $now_date),
				'updated_at'		 => $now_date
			];

			//DBにメールアドレスを登録する
			$client_id = DB::table('users')->insertGetId($db_value);

		//usersテーブルに登録されていれば
		}else{
			//登録メールアドレスが無効のとき(usersテーブルのdisableが1)
			//メルマガ配信が全停止の場合(usersテーブルのsend_flgが0)
			if( !empty($db_data) && ( $db_data->mail_disable == 1 || $db_data->send_flg == 0 ) ){
				//テンプレート画面表示で停止
				return view(config('const.disable_mail'));
			}

			$client_id = $db_user_data->id;
		}

		//アフィリエイトパラメータを保存
		$ad_cd = Cookie::get('reg_afi_code');
		if( !empty($request->input('ad_cd')) ) {
			$ad_cd = $request->input('ad_cd');
		}

		$cookie_name = 'reg_af_cd'.preg_replace("/\./", "_", $_SERVER['HTTP_HOST'])."_".$ad_cd;

		//広告別にメルマガ登録したユーザーのクッキーを生成
		Cookie::queue(Cookie::make($cookie_name, $ad_cd, null, '/', $_SERVER['HTTP_HOST']));

		$db_ban_data = DB::select("select * from ban_access_ips where access_ip = '{$access_ip}' and group_id = {$group_id}");
		if( empty($db_ban_data) || ( !empty($db_ban_data) && $db_ban_data[0]->limit_access < config('const.ip_access_limit') ) ){
			DB::insert("update ad_access_logs set reg = reg + 1, updated_at = '{$now_date}' where domain = '{$_SERVER['HTTP_HOST']}' and ad_cd = '{$ad_cd}' and access_date = ".date("Ymd"));
		}

		//user_groupsテーブルに登録するデータ
		$db_value = [
			'client_id'		=> $client_id,
			'group_id'		=> $group_id,
			'ad_cd'			=> $ad_cd,
			'remember_token'=> $remember_token,
			'regist_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $now_date),
			'last_access'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $now_date),
			'created_at'	=> $now_date,
			'updated_at'	=> $now_date
		];

		//性別があれば
		if( !empty($request->input('sex')) ){
			$db_value['sex'] = $request->input('sex');
		}

		//年代があれば
		if( !empty($request->input('age')) ){
			$db_value['age'] = $request->input('age');
		}

		try{
			DB::transaction(function() use($db_data, $db_value, $client_id, $group_id){
//				throw new \Exception("テスト例外エラー");
				//退会済の場合はupdate
				if( !empty($db_data) && $db_data->status == config('const.regist_status')[1][0] ){
					$db_value['status']				 = 1;
					$db_value['quit_datetime']		 = null;
					$db_value['sort_quit_datetime']	 = null;
					DB::table('user_groups')->where('client_id', $client_id)->where('group_id', $group_id)->update($db_value);

				//未登録の場合はuser_groupsテーブルへ登録
				}else{
					DB::table('user_groups')->insert($db_value);
				}
			});

		//エラーなら元のページへリダイレクト
		}catch(\Exception $e){
			$file = '';
			if( !empty($page_name) ){
				$file = $page_name;
			}
			return redirect($file);
		}

		//登録完了メールデータを取得
		//mail_contentsテーブルのtypeによりメール文を判別
		//購読済：1
		//退会済：2
		//ブラック：3
		$db_cnt = Mail_content::where('group_id', $group_id)->where('type', 1)->first();

		//メール文のデータがあれば
		if( !empty($db_cnt) ){
			//%変換設定では変換できない文字列の処理
			$body = preg_replace("/\-%token\-/", "$remember_token", $db_cnt->body);

			//<USER_EMAIL>の文字列をユーザーのメールアドレスへ変換
			$body = preg_replace("/".config('const.user_email')."/", $to_email, $body);

			//変換後の文字列を取得
			list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

			//送信元メールアドレスをアカウントとドメインに分割
			list($account, $domain) = explode("@", $from_mail);

			//送信元メールアドレスのドメインで返信先メールアドレスを生成
			$replay_to_mail	 = config('const.replay_to_mail').'@'.$domain;

			//送信元メールアドレスのドメインでリターンパスのメールアドレスを生成
			$return_path_mail	= config('const.return_path_to_mail').'@'.$domain;

			list($host_ip, $port) = Utility::getSmtpHost('setting');

			//仮登録メール送信
			//送信元情報設定
			$options = [
				'replay_to'		=> $replay_to_mail,
				'received'		=> $domain,
				'return_path'	=> $return_path_mail,
				'client_id'		=> $client_id,
				'host_ip'		=> $host_ip,
				'port'			=> $port,
				'from'			=> $from_mail,
				'from_name'		=> $from_name,
				'subject'		=> $subject,
				'template'		=> 'emails.provision_regist',
			];

			//送信データ設定
			$data = [
				'contents'		=> $body,
			];

			//登録メールアドレス先へメール送信
			Mail::to($to_email)->send( new SendMail($options, $data) );

			$disp_data = array_merge(['msg_flg' => 1 ],$disp_data);

		}else{
			//データがないときのメッセージ
			$disp_data = array_merge(['msg_flg' => 0 ],$disp_data);
		}

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['regist'].",{$to_email}");

		//登録後送信メールのデータ
		$registered_data = [
			'client_id'	=> $client_id,
			'ad_cd'		=> $request->cookie('reg_afi_code'),
			'group_id'	=> $group_id,
			'mail'		=> $to_email,
		];

		try{
			DB::transaction(function() use($request, $to_email, $client_id, $group_id, $access_ip, $now_date){
//				throw new \Exception("テスト例外エラー");
				//登録後送信メール用にメールアドレスを登録
				DB::insert("insert ignore into registered_mail_queues(client_id,ad_cd,group_id,mail,created_at,updated_at) values('{$client_id}', '{$request->cookie('reg_afi_code')}', {$group_id}, '{$to_email}', '{$now_date}', '{$now_date}')");

				//アクセス元IPをDBに登録
				DB::insert("insert into ban_access_ips(access_ip,group_id,limit_access,created_at,updated_at) values('{$access_ip}', {$group_id}, 1, '{$now_date}', '{$now_date}') on duplicate key update limit_access = limit_access + 1, created_at = if(now() < (created_at + interval ".config('const.del_ban_access_ip_inserval')."), created_at, '{$now_date}')");
			});

		//エラーなら元のページへリダイレクト
		}catch(\Exception $e){
			$file = '';
			if( !empty($page_name) ){
				$file = $page_name;
			}
			return redirect($file);
		}

		$file = config('const.lp_default_page')[4];
		if( !empty($page_name) ){
			$file = $page_name.'/'.config('const.lp_default_page')[4];
		}

		//任意のポストデータがあればゲットパラメータに変換
		$query_string = Utility::getMakePostData($request->all());
		$file .= $query_string;

		//完了ページへリダイレクト
		return redirect($file);
	}
}

<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Model\Admin;
use Illuminate\Http\Request;
use App\Http\Requests\KeibaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Session;
use Carbon\Carbon;
use App\Mail\SendMail;
use Mail;
use Utility;

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
	protected $redirectTo = '/admin/member/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
//		$this->middleware('guest:admin');
	}
	
	/**
	 * メールアドレス登録ボタン押下後、呼び出される
	 *
	 * @return \Illuminate\Http\Response
	 */
	protected function create(Request $request)
	{
		//ログインIDのエラーチェック
		//ログインIDの未入力/長さ/重複チェック
		$this->validate($request, [
			'email'	 => 'bail|required|email|check_email_domain|max:'.config('const.email_length').'|unique:admins,email|check_mx_domain',
		]);
		
		//アクセスキー生成
		$remember_token = session_create_id();		
		
		//メールアドレス取得
		$email = $request->input('email');

		$err_flg = Utility::checkNgWordEmail($email);

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}
		
		$db_value = [
			'email'				=> $email,
			'remember_token'	=> $remember_token,
		];
		
		//DBにメールアドレスを登録し仮登録する
		$user = new Admin($db_value);
		
		//DB保存
		$user->save();

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//送信元情報設定
		$options = [
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'return_path'=> config('const.return_path_to_mail').'@'.config('const.base_domain'),
			'from'		 => config('const.mail_from'),
			'from_name'	 => config('const.mail_admin_from_name'),
			'subject'	 => config('const.mail_admin_provision_subject'),
			'template'	 => config('const.admin_provision_regist'),
		];
		
		//送信データ設定
		$data = [
			'password_setting_url'	=> config('const.base_admin_url').config('const.admin_password_set_path').'/'.$remember_token,
		];

		//メールアドレス先へメール送信
		Mail::to($email)->queue( new SendMail($options, $data) );
		
		return null;
	}

	/**
	 * アカウント未登録の方がパスワード設定用URLへアクセスしたときに呼び出される
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function passwordSetting($sid)
	{
		//アクセスキーがDBに登録されているか確認
		$db_data = Admin::where('remember_token', $sid)->first();

		//アクセスキーがDBに登録されていない場合(仮登録前)
		if( empty($db_data) ){
			//アカウント登録画面へリダイレクト
			return redirect(config('const.admin_regist_path'));
		}else{
			//すでに本登録済だった場合
			if( !empty($db_data->password) ){
				//管理画面ログイン前へリダイレクト
				return redirect(config('const.admin_base_path'));
			}
		}
		
		$disp_data = [
			'remember_token' => $sid
		];

		//パスワード設定画面表示
		return view('admin.auth.passwords.password_setting', $disp_data);
	}

	/**
	 * アカウント未登録の方のパスワード設定処理
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function passwordSettingSend(Request $request)
	{
		//トークン取得
		$remember_token = $request->remember_token;
		
		//パスワード取得
		$password = $request->password;
		
		//パスワードのエラーチェック
		//パスワードの未入力
		$this->validate($request, [
			'password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'',
		]);
		
		//アクセスキーがDBに登録されているか確認
		$db_data = Admin::where('remember_token', $remember_token)->first();

		//アクセスキーがDBに登録されていない場合(仮登録前)
		if( empty($db_data) ){
			//トップページへリダイレクト
			return redirect(config('const.admin_nonmember_top_path'));
		}else{
			//すでに本登録済だった場合
			if( !empty($db_data->password) ){
				//管理画面ログイン前へリダイレクト
				return redirect(config('const.admin_nonmember_top_path'));
			}
		}

		$err_flg = Utility::checkNgWordEmail($db_data->email);

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}

		//remember_tokenを条件にadminsテーブルのpasswordを登録
		$update		 = Admin::where('remember_token', $remember_token)
			->update([
				'password' => bcrypt($password),
			]);

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//本登録メール送信
		//送信元情報設定
		$options = [
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'return_path'=> config('const.return_path_to_mail').'@'.config('const.base_domain'),
			'from'		 => config('const.mail_from'),
			'from_name'	 => config('const.mail_admin_from_name'),
			'subject'	 => config('const.mail_admin_regist_subject'),
			'template'	 => config('const.admin_regist'),
		];
		
		//Viewと送信データの設定
		$data = [
			'simple_login_url'	=> config('const.base_admin_url').config('const.admin_member_top_path').'/'.$remember_token,
			'login_url'			=> config('const.base_admin_url').config('const.admin_login_path'),
			'login_id'			=> $db_data->email,
			'password'			=> $password,
		];

		//本登録メールアドレス先へメール送信
		Mail::to($db_data->email)->send( new SendMail($options, $data) );

		return null;
	}
	
   /**
	 * アカウント登録済の方がパスワード再設定用URLへアクセスしたときに呼び出される
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function passwordReSetting($sid)
	{
		//アクセスキーがDBに登録されているか確認
		$db_data = Admin::where('remember_token', $sid)->first();

		//アクセスキーがDBに登録されていない場合(仮登録前)
		if( empty($db_data) ){
			//アカウント登録画面へリダイレクト
			return redirect(config('const.admin_regist_path'));
		}
		
		$disp_data = [
			'remember_token' => $sid
		];

		//パスワード設定画面表示
		return view('admin.auth.passwords.password_resetting', $disp_data);
	}
	
	/**
	 * アカウント登録済の方のパスワード再設定処理
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function passwordReSettingSend(Request $request)
	{
		//トークン取得
		$remember_token = $request->remember_token;
		
		//パスワード取得
		$password = $request->password;
		
		//パスワードのエラーチェック
		//パスワードの未入力
		$this->validate($request, [
			'password'				 => 'required|string|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
			'password_confirmation'	 => 'required|string|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'',
		]);
		
		//アクセスキーがDBに登録されているか確認
		$db_data = Admin::where('remember_token', $remember_token)->first();

		//アクセスキーがDBに登録されていない場合(仮登録前)
		if( empty($db_data) ){
			//トップページへリダイレクト
			return redirect(config('const.nonmember_top_path'));
		}

		$err_flg = Utility::checkNgWordEmail($db_data->email);

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}

		//remember_tokenを条件にadminsテーブルのpasswordを登録
		$update		 = Admin::where('remember_token', $remember_token)
			->update([
				'password' => bcrypt($password),
			]);

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//本登録メール送信
		//送信元情報設定
		$options = [
			'client_id'	 => $db_data->id,
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'return_path'=> config('const.return_path_to_mail').'@'.config('const.base_domain'),
			'from'		 => config('const.mail_from'),
			'from_name'	 => config('const.mail_admin_from_name'),
			'subject'	 => config('const.mail_admin_password_subject'),
			'template'	 => config('const.admin_password_change'),
		];
		
		//Viewと送信データの設定
		$data = [
			'simple_login_url'	=> config('const.base_admin_url').config('const.admin_member_top_path').'/'.$remember_token,
			'login_url'			=> config('const.base_admin_url').config('const.admin_login_path'),
			'login_id'			=> $db_data->email,
			'password'			=> $password,
		];

		//本登録メールアドレス先へメール送信
		Mail::to($db_data->email)->queue( new SendMail($options, $data) );

		return null;
	}

}

<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Mail\SendMail;
use Mail;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;

class LoginController extends Controller
{
	private $log_obj;

	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/admin/member/analytics/statistics/access';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
//		$this->middleware('guest:admin')->except('logout');
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	public function showLoginForm()
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('admin')->check() ) {
			return redirect()->to(config('const.base_admin_url').config('const.admin_member_top_path'));
		}
		
		return view('admin.auth.login');
	}

	public function register()
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('admin')->check() ) {
			return redirect()->to(config('const.base_admin_url').config('const.admin_member_top_path'));
		}
		
		$disp_data = [
			'ver' => time(),
		];
		
		return view('admin.auth.register', $disp_data);
	}

	public function forget()
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('admin')->check() ) {
			return redirect()->to(config('const.base_admin_url').config('const.admin_member_top_path'));
		}
		
		$disp_data = [
			'send_msg' => ''
		];
		
		return view('admin.auth.passwords.reset', $disp_data);
	}
	
	public function forgetSend(Request $request)
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('admin')->check() ) {
			return redirect()->to(config('const.base_admin_url').config('const.admin_member_top_path'));
		}
		
		//メールアドレスの未入力/形式/
		$this->validate($request, [
			'email'	 => 'required|email|max:'.config('const.email_length').'|exists:admins|check_mx_domain',
		]);

		//POSTデータ取得(メールアドレス)
		$to_email = $request->input('email');

		$err_flg = Utility::checkNgWordEmail($to_email);

		//禁止ワードが含まれていたら
		if( !is_null($err_flg) ){
			return null;
		}

		//ログインID・パスワード取得
		$db_data = Admin::where('email', $to_email)->first();

		list($host_ip, $port) = Utility::getSmtpHost('setting');

		//送信元情報設定
		$options = [
			'host_ip'	 => $host_ip,
			'port'		 => $port,
			'return_path'=> config('const.return_path_to_mail').'@'.config('const.base_domain'),
			'from'		 => config('const.mail_from'),
			'from_name'	 => config('const.mail_admin_from_name'),
			'subject'	 => config('const.mail_admin_provision_subject'),
			'template'	 => config('const.admin_password_setting'),
		];

		//送信データ設定
		$data = [
			'password_setting_url'	=> config('const.base_admin_url').config('const.admin_password_reset_path').'/'.$db_data->remember_token,
		];

		//メール送信
		Mail::to($db_data->email)->send( new SendMail($options, $data) );
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['password_update'].",{$db_data->email}");

		return null;
	}
	
	//AuthenticatesUsersのloginのメソッドをadmin用にオーバーライド
	public function login(Request $request)
	{
		$this->validateLogin($request);

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);

			return $this->sendLockoutResponse($request);
		}

		if ($this->attemptLogin($request)) {
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['login'].",{$request->input('email')}");
			
			//ログイン成功時にホスト名とユーザーエージェントをadminsテーブルにupdate
			// add by nishizawa date 2017.9.13
			$update = Admin::where('email', $request->input('email'))
				->update([
					'access_host'		=> $_SERVER['SERVER_NAME'],
					'user_agent'		=> $_SERVER['HTTP_USER_AGENT'],
					'last_login_date'	=> Carbon::now(),
				]);

			return $this->sendLoginResponse($request);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}
	
	//AuthenticatesUsersのvalidateLoginのメソッドをadmin用にオーバーライド
	protected function validateLogin(Request $request)
	{
		$this->validate($request, [
			$this->username() => 'bail|required|string|max:'.config('const.email_length').'|exists:admins|check_approved|check_mx_domain',
			'password' => 'bail|required|string|max:'.config('const.password_max_length').'|check_empty_password:'.$request->input('email'),
		]);
	}
	
	//AuthenticatesUsersのcredentialsのメソッドをadmin用にオーバーライド
	protected function credentials(Request $request)
	{
		//オリジナルコード
		return $request->only($this->username(), 'password');
	}

	public function logout(Request $request)
	{
		$user = Utility::getAdminDefaultDispParam();

		//情報が取得できていなかったら(セッションが切れていたら)
		if( empty($user) ){
			//管理画面のログイン前ページへリダイレクト
			return redirect('/admin/login');
		}

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['logout'].",{$user['login_id']}");

		//セッションを破棄(検索条件)
//		Session::flush();
		$request->session()->forget('ad_search_item');
		$request->session()->forget('ad_search_item_value');
		$request->session()->forget('ad_category');
		$request->session()->forget('ad_aggregate_flg');

		$request->session()->forget('search_type');
		$request->session()->forget('search_like_type');
		$request->session()->forget('search_disp_num');
		$request->session()->forget('sort');
		$request->session()->forget('search_item');
		$request->session()->forget('group_id');
		$request->session()->forget('reg_status');
		$request->session()->forget('reg_sex');
		$request->session()->forget('reg_age');
		$request->session()->forget('start_regdate');
		$request->session()->forget('end_regdate');

		$this->guard()->logout();

		//管理画面のログイン前ページへリダイレクト
		return redirect('/admin/login');
	}

	protected function guard()
	{
		return Auth::guard('admin');
	}

	public function username()
	{
		return 'email';
	}
}

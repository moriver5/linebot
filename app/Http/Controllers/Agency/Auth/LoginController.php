<?php

namespace App\Http\Controllers\Agency\Auth;

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
	protected $redirectTo = '/agency/member';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
//		$this->middleware('guest:agency')->except('logout');
	}

	public function showLoginForm($agency_login_id = null)
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('agency')->check() ) {
			return redirect()->to(config('const.base_agency_url').config('const.admin_member_top_path'));
		}

		$disp_data = [
			'login_id'	=> $agency_login_id
		];

		return view('agency.auth.login', $disp_data);
	}

	public function register()
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('agency')->check() ) {
			return redirect()->to(config('const.base_agency_url').config('const.admin_member_top_path'));
		}
		
		$disp_data = [
			'ver' => time(),
		];
		
		return view('agency.auth.register', $disp_data);
	}

	public function forget()
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('agency')->check() ) {
			return redirect()->to(config('const.base_agency_url').config('const.admin_member_top_path'));
		}
		
		$disp_data = [
			'send_msg' => ''
		];
		
		return view('agency.auth.passwords.reset', $disp_data);
	}
	
	public function forgetSend(Request $request)
	{
		//ログイン認証済の場合、管理画面ログイン後のトップへリダイレクト
		if ( Auth::guard('agency')->check() ) {
			return redirect()->to(config('const.base_agency_url').config('const.admin_member_top_path'));
		}
		
		//ログインIDの未入力/形式/
		$this->validate($request, [
			'login_id'	 => 'required|alpha_num_check|max:'.config('const.agency_login_id_max_length').'|exists:agencies',
		]);

		//POSTデータ取得(ログインID)
		$login_id = $request->input('login_id');

		return null;
	}
	
	//AuthenticatesUsersのloginのメソッドをagency用にオーバーライド
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
			return $this->sendLoginResponse($request);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}
	
	//AuthenticatesUsersのvalidateLoginのメソッドをagency用にオーバーライド
	protected function validateLogin(Request $request)
	{
		$this->validate($request, [
			$this->username() => 'bail|required|string|max:'.config('const.agency_login_id_max_length').'|exists:agencies',
			'password' => 'bail|required|string|max:'.config('const.password_max_length'),
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
		$user = Utility::getAgencyDefaultDispParam();

		//情報が取得できていなかったら(セッションが切れていたら)
		if( empty($user) ){
			//管理画面のログイン前ページへリダイレクト
			return redirect('/agency/login');
		}

		//セッションを破棄(検索条件)
//		Session::flush();
		$request->session()->forget('ad_search_type');
		$request->session()->forget('ad_search_item');
		$request->session()->forget('ad_search_like_type');
		$request->session()->forget('ad_start_date');
		$request->session()->forget('ad_end_date');

		$this->guard()->logout();

		//管理画面のログイン前ページへリダイレクト
		return redirect('/agency/login');
	}

	protected function guard()
	{
		return Auth::guard('agency');
	}

	public function username()
	{
		return 'login_id';
	}
}

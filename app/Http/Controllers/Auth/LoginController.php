<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libs\SysLog;
use App\Libs\ClientLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

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
	protected $redirectTo = '/member/home';
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest:user')->except('logout');
	}

	protected function validateLogin(Request $request)
	{
		$this->validate($request, [
			$this->username() => 'bail|required|string|max:'.config('const.login_id_max_length').'|exists:users|check_regist_status',
			'password' => 'bail|required|string|max:'.config('const.password_max_length'),
		]);
	}

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
			//ログ出力(通常ログイン)
			$log_obj = new SysLog(config('const.client_history_log_name'), config('const.client_log_dir_path').config('const.client_member_history_file_name'));
			$log_obj->addLog(config('const.display_list')['login'].",{$request->input('login_id')}");
			
			//ログ出力(access_logテーブル)
			$client_log_obj	 = new ClientLog();

			//認証情報取得
			$user = \Auth::guard('user')->user();

			$pay_type = 0;
			if( $user->pay_count > 0 ){
				$pay_type = 1;
			}

			//access_logsテーブルへログ出力
			$client_log_obj->addLogDb($user->login_id, $pay_type);

			//PV用ログ
			$client_log_obj->addPvLogDb(config('const.display_list')['login']);

			return $this->sendLoginResponse($request);
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

	protected function guard()
	{
		return Auth::guard('user');
	}
	
	public function username()
	{
		return 'login_id';
	}
}

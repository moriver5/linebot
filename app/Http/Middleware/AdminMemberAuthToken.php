<?php

namespace App\Http\Middleware;

use App\Model\Admin;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;

class AdminMemberAuthToken extends Controller
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		//認証済チェック
		if ( Auth::guard('admin')->check() ) {
			//最終アクセス更新
			$update = Admin::where('email', Auth::guard('admin')->user()->email)
				->update([
					'access_host'		=> $_SERVER['SERVER_NAME'],
					'user_agent'		=> $_SERVER['HTTP_USER_AGENT'],
					'last_login_date'	=> Carbon::now(),
				]);

			//認証済なら管理画面ログイン後のトップページへ
			return $next($request);
		}

		//DBデータ格納用変数
		$db_data = null;

		//リクエストパラメーターからトークン取得
		$sid = $request->route()->parameter('sid');

		//システム管理者からの許可済(usersテーブルのtypeが0以外)＆トークンがあればトークンを条件にDBテーブルからデータ検索
		if( !empty($sid) ){
			$db_data = Admin::where([
				['remember_token', '=', $sid],
				['type', '!=', 0]
			])->first();
		}
			
		//登録データがなければ
		if( empty($db_data) ){
			//ログインID・パスワードでも認証前なので管理画面ログイン前ページへリダイレクト
			return redirect(config('const.base_admin_url'))->with('message', __('messages.check_approved'));
		}

		//システム管理者の許可済＆トークンが登録されていたらlaravelの機能を使用し主キー(adminsテーブルのid)でログイン処理
		//loginUsingId(adminsテーブルの主キー, 継続的ログインにする場合はtrue)
		Auth::guard('admin')->loginUsingId($db_data->id, true);

		$update		 = Admin::where('id', $db_data->id)
			->update([
				'access_host'		=> $_SERVER['SERVER_NAME'],
				'user_agent'		=> $_SERVER['HTTP_USER_AGENT'],
				'last_login_date'	=> Carbon::now(),
			]);
		
		//認証済なら管理画面ログイン後のトップページへ
		return $next($request);
	}
}

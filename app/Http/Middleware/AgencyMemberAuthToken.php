<?php

namespace App\Http\Middleware;

use App\Model\Agency;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;

class AgencyMemberAuthToken extends Controller
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
		if ( Auth::guard('agency')->check() ) {
			//認証済なら管理画面ログイン後のトップページへ
			return $next($request);
		}

		//DBデータ格納用変数
		$db_data = null;

		//リクエストパラメーターからトークン取得
		$sid = $request->route()->parameter('sid');

		//システム管理者からの許可済(usersテーブルのtypeが0以外)＆トークンがあればトークンを条件にDBテーブルからデータ検索
		if( !empty($sid) ){
			$db_data = Agency::where([
				['remember_token', '=', $sid]
			])->first();
		}
			
		//登録データがなければ
		if( empty($db_data) ){
			//ログインID・パスワードでも認証前なので管理画面ログイン前ページへリダイレクト
			return redirect(config('const.base_agency_url'))->with('message', __('messages.check_approved'));
		}

		//システム管理者の許可済＆トークンが登録されていたらlaravelの機能を使用し主キー(agenciesテーブルのid)でログイン処理
		//loginUsingId(agenciesテーブルの主キー, 継続的ログインにする場合はtrue)
		Auth::guard('agency')->loginUsingId($db_data->id, true);
		
		//認証済なら管理画面ログイン後のトップページへ
		return $next($request);
	}
}

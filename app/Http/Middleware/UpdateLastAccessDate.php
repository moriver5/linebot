<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\User;
use Carbon\Carbon;

class UpdateLastAccessDate
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
		//認証情報取得
		$user = \Auth::guard('user')->user();
		
		//顧客の最終アクセス日時を更新
		$update	 = User::where('login_id', $user->login_id)
			->update([
				'last_access_datetime'	 => Carbon::now()
			]);
		
		return $next($request);
	}
}

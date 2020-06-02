<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;

class CheckRole extends Controller
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
		$user = \Auth::guard('admin')->user();

		//無効・制作・オペレーターなら
		if( $user->type < 3 ){
			return redirect(config('const.base_admin_url'))->with('message', __('messages.check_approved'));
		}

        return $next($request);
    }
}

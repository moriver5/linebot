<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\AdminChannelAllowList;

class CheckAllowLineChannel
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

		$basic_id = $request->route()->parameter('channel_id');
//error_log($basic_id.":testtest\n",3,"/data/www/line/storage/logs/nishi_log.txt");			

		$db_data = AdminChannelAllowList::where('admin_id', $user['id'])->where('line_basic_id', $basic_id)->first();

		//LINEチャンネルの利用権限がない場合
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url'))->with('message', __('messages.check_approved'));
		}

        return $next($request);
    }
}

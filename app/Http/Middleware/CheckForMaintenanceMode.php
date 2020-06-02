<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class CheckForMaintenanceMode
{
	protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		//メンテナンスモードでも社内からのアクセスは許可
		$allow = config('const.admin_access_allow_ip');

		//メンテナンスモードがONなら
        if ($this->app->isDownForMaintenance()) {
			//許可IPではない場合
            if (!in_array($request->getClientIp(), $allow)) {
				//メンテナンスモードの文言をDBから取得
				$db_data = \App\Model\Maintenance::first();

				//メンテナンスモード画面を表示
				return response()->view("errors.503", ['db_data' => $db_data]);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class ResponseHeader
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
		$response = $next($request);

		$headers = [
//			'X-Frame-Options'		 => 'DENY',									//クリックジャンキング用
			'X-Content-Type-Options' => 'nosniff',								//
			'X-XSS-Protection'		 => '1; mode=block',						//クロスサイトスクリプティング（XSS）に対するフィルタ機能
			'Cache-Control'			 => 'no-cache, no-store, must-revalidate',
			'Pragma'				 => 'no-cache'
		];

		if( !empty($_SERVER['HTTP_REFERER']) && !preg_match("/\/admin\/member\/lp/", $_SERVER['HTTP_REFERER']) ){
			$headers['X-Frame-Options'] = 'DENY';
		}

		//セキュリティ強化ヘッダー追加
		$response->withHeaders($headers);

		return $response;
    }
}

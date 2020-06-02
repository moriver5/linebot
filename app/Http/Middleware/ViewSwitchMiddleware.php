<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Agent;

class ViewSwitchMiddleware
{
	/**
	 * The view factory implementation.
	 *
	 * @var \Illuminate\Contracts\View\Factory
	 */
	protected $view;

	/**
	 * Create a new instance.
	 *
	 * @param  \Illuminate\Contracts\View\Factory  $view
	 * @return void
	 */
	public function __construct(ViewFactory $view)
	{
		$this->view = $view;
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
/*
 * レスポンシブルデザイン対応のため、コメントアウト
 * 未対応の場合はモバイルのテンプレートファイルを読み込ませるため
 * モバイル用のテンプレートファイルを用意しコメントを外す
		$ua = $request->server->get('HTTP_USER_AGENT');

		//モバイルなら
		if (Agent::isMobile($ua)) {
			$app = app();
			$paths = $app['config']['view.paths'];
			array_unshift($paths, $app['config']['view.mobile_paths']);

			$this->view->setFinder(new FileViewFinder($app['files'], $paths));
		}
*/
		return $next($request);
	}
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libs\ClientLog;
use Utility;
use Carbon\Carbon;
use DB;
use File;
use Cookie;
use Storage;

class RedirectLandingPageController extends Controller
{
	protected $pv_log_obj;
	
	public function __construct(Request $request)
	{
		//PV用ログ
		$this->pv_log_obj		 = new ClientLog();
	}

	/*
	 * ランディングページへリダイレクト
	 */
	public function index(Request $request, $string = '')
	{
		//ランディングページのデータ取得
		$db_data = DB::select("select * from landing_pages where domain = '".$_SERVER['HTTP_HOST']."' and open_flg = 1");

		//データがなかったらエラーページ表示(not foundページ)
		if( empty($db_data) ){
			return response()->view('errors.404');
		}

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['landing_page'].$_SERVER['REQUEST_URI']);

		//ランディングページを表示
		//ファイル指定があるとき
		if( !empty($string) ){
			$http_host = preg_replace("/\./", "\\.", $_SERVER['HTTP_HOST']);
			$http_host = preg_replace("/\-/", "\\-", $http_host);
			
			$string = preg_replace("/{$http_host}\//", "", $string);
			$listFilePath = explode("/", $string);

			$db_sub_data = DB::select("select * from sub_landing_pages where lp_id = {$db_data[0]->id} and page_name = '{$listFilePath[0]}' and open_flg = 1");

			//追加ページのデータがあれば
			if( !empty($db_sub_data) && !in_array('img', $listFilePath) ){
				if( !empty($listFilePath[1]) ){
					$file		 = config('const.storage_home_path').'/'.config('const.storage_lp_path').'/'.config('const.landing_url_path').'/'.$db_sub_data[0]->lp_id.'/'.$listFilePath[0].'/'.$listFilePath[1];
					$link_file	 = config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$db_sub_data[0]->lp_id.'/'.$listFilePath[0].'/'.$listFilePath[1];
				}else{
					$file		 = config('const.storage_home_path').'/'.config('const.storage_lp_path').'/'.config('const.landing_url_path').'/'.$db_sub_data[0]->lp_id.'/'.$listFilePath[0].'/index';					
					$link_file	 = config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$db_sub_data[0]->lp_id.'/'.$listFilePath[0].'/index';					
				}

			}else{
				$real_file = preg_replace("/".config('const.redirect_landing_url_path')."\/{$db_data[0]->id}(.*?)/", "$1", $string);
				$real_file = preg_replace("/^([a-zA-Z0-9\/\._\-]+)$/", "/$1", $real_file);
				$file = config('const.storage_home_path').'/'.config('const.storage_lp_path').'/'.config('const.landing_url_path').'/'.$db_data[0]->id.$real_file;
				$link_file = config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$db_data[0]->id.$real_file;
			}

		//ファイル指定がないとき
		}else{
			$file		 = config('const.storage_home_path').'/'.config('const.storage_lp_path').'/'.config('const.landing_url_path').'/'.$db_data[0]->id.'/index';
			$link_file	 = config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$db_data[0]->id.'/index';
		}

		//ファイルが存在するとき
//		if ( File::exists($file) ) {
		if ( is_link($link_file) || ( preg_match("/(pdf|jpeg|jpg|png|gif|ico|bmp|pict|epsf|tiff|svg|css|js)$/", $file) > 0 && File::exists($file) ) ) {
			$now_date = Carbon::now();
			$ymd_date = date("Ymd");

			if( !empty($request->input('afl')) ){
				$cookie_name_suffix = preg_replace("/\./", "_", $_SERVER['HTTP_HOST'])."_".$request->input('afl');

				//アフィリエイトパラメータを保存
				Cookie::queue(Cookie::make('reg_afi_code', $request->input('afl'), config('const.aff_cookie_expire_time'), '/', $_SERVER['HTTP_HOST']));

				//過去にアクセスしていないユーザーなら
				if( empty(Cookie::get('af_cd'.$cookie_name_suffix)) ){
					Cookie::queue(Cookie::make('af_cd'.$cookie_name_suffix, $request->input('afl'), null, '/', $_SERVER['HTTP_HOST']));
					DB::insert("insert into ad_access_logs(domain, ad_cd, uu, au, access_date, created_at, updated_at) values('{$_SERVER['HTTP_HOST']}', '{$request->input('afl')}', 1, 0, {$ymd_date}, '{$now_date}', '{$now_date}') on duplicate key update uu = uu + 1");
				}
			}

			$counted_cooki_name = '';
			$counted_cookie_val = '';
			$ad_access_data = DB::select("select * from ad_access_logs where domain = '{$_SERVER['HTTP_HOST']}'");
			if( !empty($ad_access_data) ){
				foreach($ad_access_data as $lines){
					$cookie_name_suffix = preg_replace("/\./", "_", $lines->domain)."_".$lines->ad_cd;
					if( !empty(Cookie::get('af_cd'.$cookie_name_suffix)) ){
						$counted_cookie_name = 'counted_reg_af_cd'.$cookie_name_suffix;
						$counted_cookie_val = $lines->ad_cd;
						break;
					}
				}
				
			}else{
				if( !empty($request->input('afl')) ){
					$counted_cookie_name = 'counted_reg_af_cd'.$cookie_name_suffix;
					$counted_cookie_val = $request->input('afl');
				}
			}

			$reg_af_cd = Cookie::get('reg_af_cd'.preg_replace("/\./", "_", $_SERVER['HTTP_HOST'])."_".$counted_cookie_val);

			//登録したユーザーがアクセスしたら
			if( !empty($reg_af_cd) && !empty($counted_cookie_name) && empty(Cookie::get($counted_cookie_name)) && preg_match("/jpeg|jpg|png|gif|ico|bmp|pict|epsf|tiff|svg|css|js|done$/", $file) == 0 ){
				Cookie::queue(Cookie::make($counted_cookie_name, $counted_cookie_val, null, '/', $_SERVER['HTTP_HOST']));
				DB::update("update ad_access_logs set au = au + 1,updated_at = '{$now_date}' where domain = '{$_SERVER['HTTP_HOST']}' and ad_cd = '{$reg_af_cd}' and access_date = {$ymd_date}");					
			}

			//画像ファイルの場合
			if( preg_match("/(jpeg|jpg|png|gif|ico|bmp|pict|epsf|tiff|svg)$/", $file) > 0 ){
//				$file = preg_replace("/".config('const.storage_img_match')."/", "", $file);
//				return redirect($file);
				header('Content-Type: '.File::mimeType($file));
				return readfile($file);

			}else if( preg_match("/(css|js)$/", $file) > 0 ){
				$file = preg_replace("/".config('const.storage_img_match')."/", "", $file);
				return redirect($file);

			//pdfファイルの場合
			}else if( preg_match("/pdf$/", $file) > 0 ){
				$path_parts = pathinfo($file);
				header('Content-Type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $path_parts['basename'] . '"');
				header('Content-Length: ' . filesize($file));
				return readfile($file);

			}else{
				//piwikのIDが設定されていたら
				if( !empty($db_data[0]->piwik_id) ){
					//ファイルまでのパスを取得
					$short_file = preg_replace("/".config('const.storage_lp_match')."/", "", $file);

					//コンテンツを取得
					$contents = Storage::disk('local')->get($short_file);

					//piwikコード取得
					$db_tags = DB::select('select tag from tags_settings where open_flg = 1');

					$listTags = [];
					if( !empty($db_tags) ){
						foreach($db_tags as $lines){
							$tag = preg_replace("/<SITE_PIWIK_ID>/u", $db_data[0]->piwik_id, $lines->tag);
							$listTags[] = $tag;
						}
						$piwik_tag = implode("\n", $listTags);

						//piwikコードをbodyの閉じタグの前に挿入
						$contents = mb_ereg_replace("</body>", $piwik_tag."\n</body>", $contents);
					}

					return $contents;
				}else{
					return File::get($file);
				}
			}

		//ファイルが存在しないとき
		}else{
			return response()->view('errors.404');
		}
	}
}

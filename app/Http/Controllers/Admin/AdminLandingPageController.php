<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Landing_page;
use App\Model\Landing_pages_content;
use App\Model\Landing_pages_preview;
use App\Model\Sub_landing_page;
use App\Model\Sub_landing_pages_content;
use App\Model\Sub_landing_pages_preview;
use App\Model\Group;
use App\Model\User;
use App\Model\Domain;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use File;
use Artisan;

class AdminLandingPageController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * ランディングページ-LP一覧
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = Landing_page::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//件数取得
		$total = $db_data->total();

		//画面表示用配列
		$disp_data = [
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.index', $disp_data);
	}

	/*
	 * ランディングページ-LP一覧(デフォルトのLP一覧)
	 */
	public function listLandingPage($lpid)
	{
		//LPデータ取得
		$db_data = Landing_pages_content::join('landing_pages', 'landing_pages.id', '=', 'landing_pages_contents.lp_id')
			->where('lp_id', $lpid)->paginate(config('const.admin_client_list_limit'));

		//件数取得
		$total = $db_data->total();

		//画面表示用配列
		$disp_data = [
			'add_page_post_url'	=> config('const.base_url')."/admin/member/lp/create/content/{$lpid}/add/page/send",
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.list_landing_page', $disp_data);
	}

	/*
	 * ランディングページ-LP一覧(追加ページのLP一覧)
	 */
	public function listSubLandingPage($lpid)
	{
		//LPデータ取得
		$db_data = Landing_page::join('sub_landing_pages', 'landing_pages.id', '=', 'sub_landing_pages.lp_id')
			->where('sub_landing_pages.lp_id', $lpid)->paginate(config('const.admin_client_list_limit'));

		//件数取得
		$total = $db_data->total();

		//画面表示用配列
		$disp_data = [
			'add_page_post_url'	=> config('const.base_url')."/admin/member/lp/list/{$lpid}/subpage/add/send",
			'add_page_update_post_url'	=> config('const.base_url')."/admin/member/lp/list/{$lpid}/subpage/update/send",
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.list_sub_landing_page', $disp_data);
	}

	/*
	 * ランディングページ-LP一覧(追加ページのLP一覧)
	 */
	public function updatelistSubLandingPage(Request $request, $lpid)
	{
		$listId = $request->input('id');

		$listPageName = $request->input('page_name');

		$listAllPage = $request->input('page');

		$listMemo = $request->input('description');
//error_log(print_r($listMemo,true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
//error_log(print_r($listAllPage,true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
		$listDel = $request->input('del');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		if( !empty($listDel) ){
			foreach($listDel as $index => $page_name){
				//テーブルからデータ削除
				Sub_landing_page::where('lp_id', $lpid)->where('page_name', $page_name)->delete();
				Sub_landing_pages_content::where('lp_id', $lpid)->where('page_name', $page_name)->delete();

				//個別ページのファイル削除
				system("rm -rf ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name);

				//ログ出力
				$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_delete_page'].",{$user['login_id']}");

			}

			if( !empty($listPageName) ){
				$listPageName = array_diff($listPageName, $listDel);
			}
		}

		foreach($listAllPage as $index => $page_name){
			$update_value = [
				'memo'			=> $listMemo[$index]
			];

			//サブページを公開する場合
			if( !empty($listPageName) && in_array($page_name, $listPageName) ){
				$update_value['open_flg'] = 1;

				//mvコマンドで/data/www/storage/melmaga/LP/LPID/ページ名_private /data/www/storage/melmaga/LP/LPID/ページ名へ移動
				system("mv ".config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name."_private ".config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name);

				//　/data/www/storage/melmaga/storage/app/public/LP/LPID/ページ名/img→/data/www/storage/melmaga/LP/LPID/ページ名/imgへシンボリックリンクを張る
				system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name.'/img '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name."/");

			//サブページを非公開する場合
			}else{
				$update_value['open_flg'] = 0;

				//mvコマンドで/data/www/storage/melmaga/LP/LPID/ページ名 /data/www/storage/melmaga/LP/LPID/ページ名_privateへ移動
				system("mv ".config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name." ".config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$page_name."_private");
			}
			//更新処理
			$update = Sub_landing_page::where('lp_id', $lpid)
				->where('page_name', $page_name)
				->update($update_value);
		}

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_update_page'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ランディングページ作成トップ画面
	 */
	public function createLandingPage($id, $name = null)
	{
		//ランディングデータ取得
		$query = Landing_page::query();

		//ページ名のリスト取得
		$db_page_data = $query->join('landing_pages_contents', 'landing_pages.id', '=', 'landing_pages_contents.lp_id')
			->select('name', 'domain')
			->where([
				'landing_pages_contents.lp_id'	=> $id
			])
			->get();

		//ランディングデータ取得
		$query = Landing_page::query();

		$db_data = $query->join('landing_pages_contents', 'landing_pages.id', '=', 'landing_pages_contents.lp_id')
			->where([
				'landing_pages_contents.lp_id'	=> $id,
				'landing_pages_contents.name'	=> $name
			])
			->first();

		//ファイルが存在しなかったらindexを表示
		if( count($db_data) == 0 ){
			return redirect(config('const.base_admin_url').config('const.admin_lp_path')."/create/content/{$id}/index");
		}

		//画面表示用配列
		$disp_data = [
			'add_page_post_url'	=> config('const.base_url')."/admin/member/lp/create/content/{$id}/{$name}/add/page/send",
			'post_url'			=> config('const.base_url')."/admin/member/lp/create/content/{$id}/{$name}/send",
			'preview_url'		=> config('const.base_url')."/admin/member/lp/create/content/{$id}/{$name}/preview",
//			'link_url'			=> 'https://'.$db_page_data[0]->domain."/lp/{$id}/{$name}",
			'link_url'			=> 'https://'.$db_page_data[0]->domain."/{$name}",
			'csrf_token'		=> csrf_token(),
			'id'				=> $id,
			'current_page'		=> $name,
			'list_open_flg'		=> config('const.admin_open_type'),
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.lp.landing_page', $disp_data);
	}

	/*
	 * ランディングページ-プレビュー
	 */
	public function previewLandingPageSend(Request $request, $id, $name)
	{
		//エラーチェック
		$this->validate($request, [
			'lp_content'	=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		$content = $request->input('lp_content');

		$db_data = DB::select("select count(*) as count from landing_pages_previews where lp_id = {$id} and name = '{$name}';");

		try{
			DB::transaction(function() use($db_data, $id, $name, $content){
				//プレビューデータinsert
				if( $db_data[0]->count == 0 ){
					Landing_pages_preview::insert([
						'lp_id'		=> $id,
						'name'		=> $name,
						'content'	=> $content,
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);

				//プレビューデータupdate
				}else{
					$update = Landing_pages_preview::where([
						'lp_id'	=> $id,
						'name'	=> $name
						])->update([
							'content'		=> $content,
							'updated_at'	=> Carbon::now()
					]);
				}
			});
		}catch(\Exception $e){
		}

		return "ok";
	}

	/*
	 * ランディングページ-プレビュー
	 */
	public function previewLandingPage($id, $name)
	{
		//ランディングデータ取得
		$query = Landing_page::query();

		$db_lp_data = Landing_page::where('id',$id)->first();

		$db_data = DB::select("select content from landing_pages_previews where lp_id = {$id} and name = '{$name}';");

		$content = preg_replace("/(src=[\"'])(\/.*?img)/u", "$1https://".$db_lp_data->domain."$2", $db_data[0]->content);
		$content = preg_replace("/(href=[\"'])(\/.*?\.css)/u", "$1https://".$db_lp_data->domain."$2", $content);
		$content = preg_replace("/(href=[\"'])(\/.*?\.pdf)/u", "$1https://".$db_lp_data->domain."$2", $content);

		return response($content);
	}

	/*
	 * ランディングページ作成トップ画面
	 */
	public function createSubLandingPage($id, $page_name = null, $name = null)
	{
		//ページ名のリスト取得
		$db_page_data = Landing_page::join('sub_landing_pages', 'landing_pages.id', '=', 'sub_landing_pages.lp_id')
			->join('sub_landing_pages_contents', 'sub_landing_pages.page_name', '=', 'sub_landing_pages_contents.page_name')
			->select('name', 'domain','sub_landing_pages.page_name')
			->where([
				'landing_pages.id'						=> $id,
				'sub_landing_pages_contents.lp_id'		=> $id,
				'sub_landing_pages_contents.page_name'	=> $page_name
			])
			->get();

		//ランディングデータ取得
		$query = Sub_landing_page::query();

		$db_data = $query->join('sub_landing_pages_contents', 'sub_landing_pages.lp_id', '=', 'sub_landing_pages_contents.lp_id')
			->where([
				'sub_landing_pages_contents.lp_id'		=> $id,
				'sub_landing_pages_contents.page_name'	=> $page_name,
				'sub_landing_pages_contents.name'		=> $name
			])
			->first();

		//ファイルが存在しなかったらindexを表示
		if( count($db_data) == 0 ){
			return redirect(config('const.base_admin_url').config('const.admin_lp_path')."/list/{$id}/subpage/content/{$page_name}/index");
		}

		//画面表示用配列
		$disp_data = [
			'add_page_post_url'			=> config('const.base_url')."/admin/member/lp/list/{$id}/subpage/{$page_name}/add/send",
			'post_url'					=> config('const.base_url')."/admin/member/lp/list/{$id}/subpage/content/{$page_name}/{$name}/send",
			'preview_url'				=> config('const.base_url')."/admin/member/lp/subpage/{$id}/{$page_name}/{$name}/preview",
//			'link_url'					=> 'https://'.$db_page_data[0]->domain."/lp/{$id}/{$name}",
			'link_url'					=> 'https://'.$db_page_data[0]->domain."/{$page_name}/{$name}",
			'id'						=> $id,
			'csrf_token'				=> csrf_token(),
			'current_page'				=> $name,
			'page_name'					=> $page_name,
			'lp_default_page'			=> $db_page_data,
			'list_open_flg'				=> config('const.admin_open_type'),
			'db_data'					=> $db_data,
			'ver'						=> time()
		];

		return view('admin.lp.sub_landing_page', $disp_data);
	}

	/*
	 * ランディングページ-プレビュー
	 */
	public function previewSubLandingPageSend(Request $request, $id, $page_name,  $name)
	{
		//エラーチェック
		$this->validate($request, [
			'lp_content'	=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		$content = $request->input('lp_content');

		$db_data = DB::select("select count(*) as count from sub_landing_pages_previews where lp_id = {$id} and page_name = '{$page_name}' and name = '{$name}';");

		try{
			DB::transaction(function() use($db_data, $id, $page_name, $name, $content){
				//プレビューデータinsert
				if( $db_data[0]->count == 0 ){
					Sub_Landing_pages_preview::insert([
						'lp_id'		=> $id,
						'page_name'	=> $page_name,
						'name'		=> $name,
						'content'	=> $content,
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);

				//プレビューデータupdate
				}else{
					$update = Sub_Landing_pages_preview::where([
						'lp_id'		=> $id,
						'page_name'	=> $page_name,
						'name'		=> $name
						])->update([
							'content'		=> $content,
							'updated_at'	=> Carbon::now()
					]);
				}
			});
		}catch(\Exception $e){
		}

		return "ok";
	}

	/*
	 * ランディングページ-プレビュー
	 */
	public function previewSubLandingPage($id, $page_name, $name)
	{
		//ランディングデータ取得
		$query = Sub_Landing_page::query();

		$db_lp_data = Landing_page::where('id',$id)->first();

		$db_data = DB::select("select content from sub_landing_pages_previews where lp_id = {$id} and page_name = '{$page_name}' and name = '{$name}';");

		$content = preg_replace("/(src=[\"'])(\/.*?img)/u", "$1https://".$db_lp_data->domain."$2", $db_data[0]->content);
		$content = preg_replace("/(href=[\"'])(\/.*?\.css)/u", "$1https://".$db_lp_data->domain."$2", $content);

		return response($content);
	}

	/*
	 * ランディングページ更新処理
	 */
	public function updateLandingPageSend(Request $request, $id, $name)
	{
		//エラーチェック
		$this->validate($request, [
			'lp_content'	=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		//Content取得
		$content = $request->input('lp_content');

		//公開フラグ取得
		$open_flg = $request->input('open_flg');
		
		//削除フラグ取得
		$del_flg = $request->input('del');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//個別ページ削除
		if( $del_flg == 1 ){
			//データを削除
			$delete = Landing_pages_content::where([
				'lp_id'	=> $id,
				'name'	=> $name
				])->delete();

			//個別ページのファイル削除
			system("rm ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$name);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_delete_page'].",{$user['login_id']}");

		//個別ページ更新
		}else{
			try{
				DB::transaction(function() use($id, $name, $open_flg, $content){
//					throw new \Exception("テスト例外エラー");
					//データを更新
					$update = Landing_pages_content::where([
						'lp_id'	=> $id,
						'name'	=> $name
						])->update([
							'url_open_flg'	=> $open_flg,
							'content'		=> $content,
							'updated_at'	=> Carbon::now()
						]);
				});
			}catch(\Exception $e){
				return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
			}

			//ファイルに書き込み
			$file_size = File::put(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$name, Utility::getConvertData($content));

			//シンボリックリンクを張る
			if( !empty($open_flg) ){
				system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$name.' '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$id.'/');
				system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/.htaccess '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$id.'/');

			//シンボリックリンクを削除
			}else{
				system("unlink ".config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$name);
			}

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_update_page'].",{$user['login_id']}");
		}

		return null;
	}

	/*
	 * ランディングページ更新処理
	 */
	public function updateSubLandingPageSend(Request $request, $id, $page_name, $name)
	{
		//エラーチェック
		$this->validate($request, [
			'lp_content'	=> 'bail|required|surrogate_pair_check|emoji_check',
		]);

		//Content取得
		$content = $request->input('lp_content');

		//公開フラグ取得
		$open_flg = $request->input('open_flg');
		
		//削除フラグ取得
		$del_flg = $request->input('del');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//個別ページ削除
		if( $del_flg == 1 ){
			//データを削除
			$delete = Sub_landing_pages_content::where([
				'lp_id'		=> $id,
				'page_name'	=> $page_name,
				'name'		=> $name
				])->delete();

			//個別ページのファイル削除
			system("rm ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/'.$name);

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_delete_page'].",{$user['login_id']}");

		//個別ページ更新
		}else{
			try{
				DB::transaction(function() use($id, $page_name, $name, $open_flg, $content){
//					throw new \Exception("テスト例外エラー");
					//データを更新
					$update = Sub_landing_pages_content::where([
						'lp_id'		=> $id,
						'page_name'	=> $page_name,
						'name'		=> $name
						])->update([
							'url_open_flg'	=> $open_flg,
							'content'		=> $content,
							'updated_at'	=> Carbon::now()
						]);
				});
			}catch(\Exception $e){
				return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
			}

			//ファイルに書き込み
			$file_size = File::put(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/'.$name, Utility::getConvertData($content));

			//シンボリックリンクを張る
			if( !empty($open_flg) ){
				system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/'.$name.' '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/'.$name);
				system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/.htaccess '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$id.'/');

			//シンボリックリンクを削除
			}else{
				system("unlink ".config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/'.$name);
			}

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_update_page'].",{$user['login_id']}");
		}

		return null;
	}

	/*
	 * ランディングページ画面追加
	 */
	public function addLandingPageSend(Request $request, $id)
	{
		//ページ名のチェック
		$this->validate($request, [
			'page'	=> 'check_file_name',
		]);

		//Content取得
		$add_file_name = $request->input('page');

		//ファイルに書き込み
		$file_size = File::put(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$add_file_name, '');

		$now_date = Carbon::now();

		//データ登録
		$lp_content = new Landing_pages_content([
			'lp_id'				=> $id,
			'name'				=> $add_file_name,
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		]);

		//DB保存
		$lp_content->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_add_page'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ランディングページに使用する画像登録画面
	 */
	public function uploadLandingPageImg($id)
	{
		//動的クエリを生成するため
		$db_data = Landing_page::where('id',$id)->first();

		//landing_pagesテーブルに登録されている画像取得
		$list_img = [];
		if( !empty($db_data->img) ){
			$list_img = explode(",", $db_data->img);
		}

		$baseurl = $_SERVER['REQUEST_SCHEME'].'://'.$db_data->domain;

		//画面表示用配列
		$disp_data = [
			'post_url'			=> config('const.base_admin_url').'/'.config('const.lp_create_img_path').'/'.$id.'/delete',
//			'img_url'			=> $baseurl.'/'.config('const.landing_url_path').'/'.$id,
			'img_url'			=> $baseurl.'/img',
			'redirect_url'		=> config('const.base_admin_url').'/'.config('const.lp_create_img_path').'/'.$id,
			'id'				=> $id,
			'list_img'			=> $list_img,
			'ver'				=> time()
		];

		return view('admin.lp.landing_page_img', $disp_data);
	}

	/*
	 * ランディングページに使用する画像登録画面
	 */
	public function uploadSubLandingPageImg($id, $page_name)
	{
		//動的クエリを生成するため
		$db_data = Landing_page::join('sub_landing_pages', 'sub_landing_pages.lp_id', '=', 'landing_pages.id')
			->where('sub_landing_pages.lp_id',$id)
			->where('sub_landing_pages.page_name', $page_name)->first();

		//landing_pagesテーブルに登録されている画像取得
		$list_img = [];
		if( !empty($db_data->img) ){
			$list_img = explode(",", $db_data->img);
		}

		$baseurl = $_SERVER['REQUEST_SCHEME'].'://'.$db_data->domain;

		//画面表示用配列
		$disp_data = [
			'post_url'			=> config('const.base_admin_url').'/'.config('const.lp_subpage_img_path').'/'.$id.'/'.$page_name.'/delete',
//			'img_url'			=> config('const.base_url').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name,
			'img_url'			=> $baseurl.'/'.$page_name.'/img',
			'redirect_url'		=> config('const.base_admin_url').'/'.config('const.lp_subpage_img_path').'/'.$id.'/'.$page_name,
			'id'				=> $id,
			'page_name'			=> $page_name,
			'list_img'			=> $list_img,
			'ver'				=> time()
		];

		return view('admin.lp.sub_landing_page_img', $disp_data);
	}

	/*
	 * ランディングページに使用する画像を削除
	 */
	public function deleteLandingPageImg(Request $request, $id)
	{
		$this->validate($request, [
			'img'	=> 'required',
		]);

		//削除する画像を取得
		$listDelImg = $request->input('img');

		//landing_pageテーブルに登録されている画像を取得
		$listImg = [];
		$db_data = Landing_page::where('id',$id)->first();
		if( !empty($db_data->img) ){
			$listImg = explode(",", $db_data->img);
		}

		$listUpdateImg = [];
		foreach($listImg as $img){
			//DBに登録されている画像が削除リストに含まれていれば
			if( in_array($img, $listDelImg) ){
				//ディレクトリから画像削除
				system("rm ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/img/'.$img);
				continue;
			}
			//登録する画像を配列に格納
			$listUpdateImg[] = $img;
		}

		//landing_pagesテーブルを更新
		$update = Landing_page::where('id', $id)
			->update([
				'img'			=> implode(",", array_unique($listUpdateImg)),
				'updated_at'	=> Carbon::now()
			]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_delete'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ランディングページに使用する画像を削除
	 */
	public function deleteSubLandingPageImg(Request $request, $id, $page_name)
	{
		$this->validate($request, [
			'img'	=> 'required',
		]);

		//削除する画像を取得
		$listDelImg = $request->input('img');

		//landing_pageテーブルに登録されている画像を取得
		$listImg = [];
		$db_data = Sub_landing_page::where('lp_id', $id)->where('page_name', $page_name)->first();
		if( !empty($db_data->img) ){
			$listImg = explode(",", $db_data->img);
		}

		$listUpdateImg = [];
		foreach($listImg as $img){
			//DBに登録されている画像が削除リストに含まれていれば
			if( in_array($img, $listDelImg) ){
				//ディレクトリから画像削除
				system("rm ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/img/'.$img);
				continue;
			}
			//登録する画像を配列に格納
			$listUpdateImg[] = $img;
		}

		//landing_pagesテーブルを更新
		$update = Sub_landing_page::where('lp_id', $id)
			->where('page_name', $page_name)
			->update([
				'img'			=> implode(",", array_unique($listUpdateImg)),
				'updated_at'	=> Carbon::now()
			]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_img_delete'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadLandingPageImgUpload(Request $request)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id = $request->input('edit_id');

		
		$img_name = $file->getClientOriginalName();

		try{
			DB::transaction(function() use($img_name, $id){
//				throw new \Exception("テスト例外エラー");
				$listImg = [];
				$db_data = Landing_page::where('id',$id)->first();
				if( !empty($db_data->img) ){
					$listImg = explode(",", $db_data->img);
				}
				$listImg[] = $img_name;

				$update = Landing_page::where('id', $id)
					->update([
						'img'			=> implode(",", array_unique($listImg)),
						'updated_at'	=> Carbon::now()
					]);
			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/img', $img_name);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 画像のアップロード処理
	 */
	public function uploadSubLandingPageImgUpload(Request $request, $lpid, $page_name)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');

		//landing_pagesテーブルに登録されているidを取得
		$id = $request->input('edit_id');

		$img_name = $file->getClientOriginalName();

		try{
			DB::transaction(function() use($page_name, $img_name, $id){
//				throw new \Exception("テスト例外エラー");
				$listImg = [];
				$db_data = Sub_landing_page::where('lp_id',$id)->where('page_name', $page_name)->first();
				if( !empty($db_data->img) ){
					$listImg = explode(",", $db_data->img);
				}
				$listImg[] = $img_name;

				$update = Sub_landing_page::where('lp_id', $id)->where('page_name', $page_name)
					->update([
						'img'			=> implode(",", array_unique($listImg)),
						'updated_at'	=> Carbon::now()
					]);

			});
		}catch(\Exception $e){
			return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
		}

		//画像の保存先を移動
		$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/img', $img_name);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_img_upload'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ファイルのアップロード処理
	 * ※現在使用してませんが今後使用する可能性があるので残しています。
	 */
	public function uploadCommonLandingPageUpload(Request $request)
	{
		//アップロード画像情報取得
		$file = $request->file('import_file');
		
		$filename		 = $file->getClientOriginalName();
		$file_extension	 = $file->getClientOriginalExtension();
//error_log($filename."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
//error_log($file_extension."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");

		//画像
		if( preg_match("/gif|jpg|png|ico/", $file_extension) > 0 ){
			$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.img_dir_path').'/', $filename);

			system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.img_dir_path').'/'.$filename.' '.config('const.storage_home_path').'/'.config('const.public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.img_dir_path').'/');

		//CSS
		}elseif( preg_match("/css/", $file_extension) > 0 ){
			$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.css_dir_path').'/', $filename);

			system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.css_dir_path').'/'.$filename.' '.config('const.storage_home_path').'/'.config('const.public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.css_dir_path').'/');

		//javascript
		}elseif( preg_match("/js/", $file_extension) > 0 ){
			$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.js_dir_path').'/', $filename);	

			system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.js_dir_path').'/'.$filename.' '.config('const.storage_home_path').'/'.config('const.public_dir_path').'/'.config('const.common_dir_path').'/'.config('const.js_dir_path').'/');

		//上記以外
		}else{
			//ファイルの保存先を/data/www/melmaga/public配下へ移動
			$file->move(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/', $filename);

			// /data/www/melmaga/public/アップロードファイルの保存先へシンボリックリンクを貼る
			system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.$filename.' '.config('const.storage_home_path').'/'.config('const.public_dir_path').'/');
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
//		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_upload'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 共通で使用するLP画面表示
	 * ※現在使用してませんが今後使用する可能性があるので残しています。
	 */
	public function index_common()
	{
		$list_url		 = [];
		$exclude_file	 = preg_replace("/\./", "\\.", implode("|", config('const.exclude_file')));

		//通常のファイル
		$list_files = File::files('/data/www/melmaga/public');
		foreach($list_files as $fullpath_file){
			//システムで使用しているファイルは除外する
			if( preg_match("/{$exclude_file}/", $fullpath_file) > 0 ){
				continue;
			}

			$list_url[] = [
				'path'	 => $fullpath_file,
				'url'	 => config('const.base_url').'/'.pathinfo($fullpath_file, PATHINFO_BASENAME)
			];
//error_log(pathinfo($fullpath_file, PATHINFO_BASENAME)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
		}

		$list_files = File::allFiles('/data/www/melmaga/public/common/css');
		foreach($list_files as $fullpath_file){
			$list_url[] = [
				'path'	 => $fullpath_file,
				'url'	 => config('const.base_url').'/'.config('const.common_dir_path').'/'.config('const.css_dir_path').'/'.pathinfo($fullpath_file, PATHINFO_BASENAME)
			];
		}

		$list_files = File::allFiles('/data/www/melmaga/public/common/images');
		foreach($list_files as $fullpath_file){
			$list_url[] = [
				'path'	 => $fullpath_file,
				'url'	 => config('const.base_url').'/'.config('const.common_dir_path').'/'.config('const.img_dir_path').'/'.pathinfo($fullpath_file, PATHINFO_BASENAME)
			];
		}

		$list_files = File::allFiles('/data/www/melmaga/public/common/js');
		foreach($list_files as $fullpath_file){
			$list_url[] = [
				'path'	 => $fullpath_file,
				'url'	 => config('const.base_url').'/'.config('const.common_dir_path').'/'.config('const.js_dir_path').'/'.pathinfo($fullpath_file, PATHINFO_BASENAME)
			];
		}

		//画面表示用配列
		$disp_data = [
			'base_url'		=> config('const.base_url'),
			'list_file'		=> $list_url,
			'ver'			=> time()
		];

		return view('admin.lp.index_common', $disp_data);
	}

	/*
	 * 共通で使用するLPの削除
	 * ※現在使用してませんが今後使用する可能性があるので残しています。
	 */
	public function deleteCommonLandingPage(Request $request)
	{
		$this->validate($request, [
			'file'	=> 'required',
		]);

		//削除するファイルを取得
		$listDelFile = $request->input('file');

		foreach($listDelFile as $file){
			//シンボリックリンクを削除
			system("unlink ".$file);

			//ファイル自体を削除
			$save_file = preg_replace('/'.config('const.public_dir_path').'/', config('const.storage_public_dir_path'), $file);
			system("rm ".$save_file);
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_img_delete'].",{$user['login_id']}");

		return null;
	}

	/*
	 * LP新規作成画面
	 */
	public function create()
	{
		$list_domain = DB::select('select * from domains where domain not in(select distinct domain from landing_pages)');

		//
		$disp_data = [
			'list_domain'		=> $list_domain,
			'list_open_flg'		=> config('const.admin_open_type'),
			'ver'				=> time()
		];

		return view('admin.lp.create', $disp_data); 
	}

	/*
	 * LP新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'domain'		=> 'required|unique:landing_pages,domain|max:'.config('const.domain_name_max_length').'|check_exist_domain',
			'description'	=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.lp_memo_max_length'),
			'piwik_id'		=> 'alpha_num_check'
		]);

		$now_date = Carbon::now();

		//公開フラグ取得
		$open_flg = $request->input('open_flg');

		//landing_pagesテーブルにinsert
		$id = Landing_page::insertGetId([
			'open_flg'			=> $open_flg,
			'domain'			=> $request->input('domain'),
			'memo'				=> $request->input('description'),
			'piwik_id'			=> $request->input('piwik_id'),
			'sort_date'			=> $now_date,
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		]);

		//landing_pages_contentsテーブルにデフォルトデータをinsert
		foreach(config('const.lp_default_page') as $name){
			//データ登録
			$lp_content = new Landing_pages_content([
				'lp_id'				=> $id,
				'name'				=> $name,
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date
			]);

			//DB保存
			$lp_content->save();
		}

		//ランディングページ用のフォルダを作成
		system("mkdir ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id." ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id."/img;chown -R apache:apache ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id.";sudo chmod -R 775 ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id.";");

		//シンボリックリンクを張る
		if( !empty($open_flg) ){
			system("mkdir ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id.";chown -R apache:apache ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id.";sudo chmod -R 775 ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id.";");
			system("ln -s ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id."/img ".config('const.storage_home_path')."/".config('const.landing_url_path').'/'.$id."/;");
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * LP追加処理
	 */
	public function addSubLandingPageSend(Request $request, $lpid)
	{
		$this->validate($request, [
			'page'	=> 'check_file_name|check_exist_lp_file:'.$lpid.'|unique:sub_landing_pages,page_name,null,lp_id,lp_id,'.$lpid,
		]);

		$now_date = Carbon::now();

		$add_value = [
			'lp_id'				=> $lpid,
			'page_name'			=> $request->input('page'),
			'memo'				=> $request->input('description'),
			'sort_date'			=> $now_date,
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		];

		if( !empty($request->input('description')) ){
			$add_value['memo'] = $request->input('description');
		}

		//landing_pagesテーブルにinsert
		$id = Sub_landing_page::insert($add_value);

		//landing_pages_contentsテーブルにデフォルトデータをinsert
		foreach(config('const.lp_default_page') as $name){
			//データ登録
			$lp_content = new Sub_landing_pages_content([
				'lp_id'				=> $lpid,
				'page_name'			=> $request->input('page'),
				'name'				=> $name,
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date
			]);

			//DB保存
			$lp_content->save();
		}

		//ランディングページ用のフォルダを作成
		system("mkdir ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page')." ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page')."/img;chown -R apache:apache ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page').";sudo chmod -R 775 ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page').";");
		system("mkdir ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page')."_private;chown -R apache:apache ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page')."_private;sudo chmod -R 775 ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$lpid."/".$request->input('page')."_private;");

		//画像フォルダのシンボリックリンクを張る
		system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$request->input('page').'/img '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$request->input('page')."/");

		//シンボリックリンクを張る
//		if( !empty($open_flg) ){
//			system("ln -s ".config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$request->input('page').' '.config('const.storage_home_path').'/'.config('const.landing_url_path').'/'.$lpid.'/'.$request->input('page'));
//		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * ランディングページ画面追加
	 */
	public function addFileSubLandingPageSend(Request $request, $id, $page_name)
	{
		//ページ名のチェック
		$this->validate($request, [
			'page'	=> 'check_file_name|unique:sub_landing_pages_contents,name,null,null,page_name,'.$page_name.',lp_id,'.$id,
		]);

		//Content取得
		$add_file_name = $request->input('page');

		//ファイルに書き込み
		$file_size = File::put(config('const.storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.landing_url_path').'/'.$id.'/'.$page_name.'/'.$add_file_name, '');

		$now_date = Carbon::now();

		//データ登録
		$lp_content = new Sub_landing_pages_content([
			'lp_id'				=> $id,
			'page_name'			=> $page_name,
			'name'				=> $add_file_name,
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		]);

		//DB保存
		$lp_content->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['sub_lp_add'].",{$user['login_id']}");

		return null;
	}

	/*
	 * LP編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Landing_page::where('id',$id)->first();

		//landing_pagesテーブルにデータがなかったら一覧ページにリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_lp_path').'?page='.$page);
		}

		$list_domain = DB::select('select * from domains');

		//画面表示用配列
		$disp_data = [
			'list_domain'		=> $list_domain,
			'list_open_flg'		=> config('const.admin_open_type'),
			'edit_id'			=> $id,
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.lp.edit', $disp_data);
	}

	/*
	 * LP編集処理
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'domain'		=> 'required|unique:landing_pages,domain,'.$request->input('edit_id').',id|max:'.config('const.domain_name_max_length').'|check_exist_domain',
			'description'	=> 'bail|surrogate_pair_check|emoji_check|max:'.config('const.lp_memo_max_length'),
			'piwik_id'		=> 'alpha_num_check'
		]);

		//DBに登録されているID取得
		$id = $request->input('edit_id');

		//公開フラグ取得
		$open_flg = $request->input('open_flg');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//LPページの編集
		if( empty($request->input('del')) ){
			try{
				DB::transaction(function() use($request, $id, $open_flg){
//					throw new \Exception("テスト例外エラー");
					$update = Landing_page::where('id', $id)
						->update([
							'open_flg'		=> $open_flg,
							'memo'			=> $request->input('description'),
							'piwik_id'		=> $request->input('piwik_id'),
							'updated_at'	=> Carbon::now()
						]);
				});
			}catch(\Exception $e){
				return response()->json(['error' => [__("messages.dialog_update_failed")]],400);
			}

			//ランディングページの保存ディレクトリまでのフルパス
			$lp_dir_path = config('const.storage_home_path').'/'.config('const.landing_dir_path').'/';

			//公開する場合
			if( !empty($open_flg) ){
				//ランディングページ用のフォルダが生成されていない場合
				if( !file_exists("{$lp_dir_path}{$id}") && !file_exists("{$lp_dir_path}{$id}_private") ){
					//ランディングページ用のフォルダを作成
					system("mkdir {$lp_dir_path}{$id};chown -R apache:apache {$lp_dir_path}{$id};sudo chmod -R 775 {$lp_dir_path}{$id};");
				}

				//公開する場合、mvコマンドで/data/www/storage/melmaga/LP/LPID_private→/data/www/storage/melmaga/LP/LPIDへ移動
				system("mv ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id."_private ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id);

				// /data/www/storage/melmaga/storage/app/public/LP/LPID/img→/data/www/storage/melmaga/LP/LPID/imgへシンボリックリンクを張る
				system("ln -fs ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id."/img ".config('const.storage_home_path')."/".config('const.landing_url_path').'/'.$id."/;");

			//公開しない場合
			}else{
				//　/data/www/storage/melmaga/storage/app/public/LP/LPID/img→/data/www/storage/melmaga/LP/LPID/imgへのシンボリックリンクを削除
				system("unlink ".config('const.storage_home_path')."/".config('const.landing_url_path').'/'.$id."/img;");

				//非公開する場合、mvコマンドで/data/www/storage/melmaga/LP/LPID→/data/www/storage/melmaga/LP/LPID_privateへ移動
				system("mv ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id." ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id."_private");
			}

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_update'].",{$user['login_id']}");

			return null;

		//LPページの削除
		}else{
			//landing_pageテーブルからデータ削除
			$delete = Landing_page::where('id', $id)->delete();

			//landing_pages_contentsテーブルからデータ削除
			$delete = Landing_pages_content::where('lp_id', $id)->delete();

			//ランディングページ用のフォルダをすべて削除
			system("rm -rf ".config('const.storage_home_path')."/".config('const.storage_public_dir_path')."/".config('const.landing_dir_path')."/".$id.";");
			system("rm -rf ".config('const.storage_home_path')."/".config('const.landing_dir_path')."/".$id.";");

			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['lp_delete'].",{$user['login_id']}");

			return null;
		}
	}

	/*
	 * 検索画面表示
	 */
	public function searchSetting()
	{
		//画面表示用配列
		$disp_data = [
			'session'					=> Session::all(),
			'ver'						=> time(),
			'lp_search_item'			=> config('const.lp_search_item'),
			'lp_search_like_type'		=> config('const.search_like_type'),
			'lp_disp_type'				=> config('const.lp_disp_type'),
			'sort_list'					=> config('const.lp_sort_list'),
		];

		return view('admin.lp.lp_search', $disp_data);
	}

	/*
	 * 検索処理
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Landing_page::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		//
		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.index', $disp_data);
	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Landing_page::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.lp.index', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		//landing_pages_contentsテーブルと結合
		$query->join('landing_pages_contents', 'landing_pages.id', '=', 'landing_pages_contents.lp_id');

		//取得するカラム名を指定
		$query->pluck('landing_pages.id','landing_pages.open_flg','landing_pages.memo','landing_page_contents.url_open_flg','name');

		//landing_pages_contentテーブルのindexから検索
		$query->where('landing_pages_contents.name', 'index');

		//検索項目
		if( !is_null(Session::get('lp_search_item_value')) ){
//			$query->where(Session::get('lp_search_item'), config('const.search_like_type')[Session::get('lp_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('lp_search_like_type')][1], Session::get('lp_search_item_value')));

			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listItem = explode(",", Session::get('lp_search_item_value'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('lp_search_item'), config('const.search_like_type')[Session::get('lp_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('lp_search_like_type')][1], $item ));
				}
			});
		}

		//公開
		if( !empty(Session::get('lp_disp_type')) ){
			$listPageType = config('const.forecast_disp_type');
			$query->where('landing_pages.open_flg', $listPageType[Session::get('lp_disp_type')][0]);
		}

		//ソート
		$sort_item = "id";
		$sort_type = "asc";
		if( !is_null(Session::get('lp_sort')) ){
			$listSortType = config('const.lp_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('lp_sort')][0]);
			$query->orderBy($sort_item, $sort_type);
		}

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->count();

		//顧客データのエクスポート
		}elseif( $exec_type == config('const.search_exec_type_export_key') ){
			$db_data = $query->get();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$db_data = $query;

		//通常検索
		}else{
			$db_data = $query->paginate(config('const.admin_client_list_limit'));
//			$db_data = $query->toSql();
		}

		return $db_data;
	}

	/*
	 * SQL文の条件保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('lp_search_item', $request->input('search_item'));
		}

		//検索の値
		Session::put('lp_search_item_value', $request->input('search_item_value'));

		//LIKE検索タイプ
		Session::put('lp_search_like_type', $request->input('search_like_type'));

		//公開
		Session::put('lp_disp_type', $request->input('disp_type'));

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('lp_sort', $request->input('sort'));
		}
	}

	/*
	 * 一括削除処理
	 */
/*
	public function bulkUpdate(Request $request)
	{

		//ID取得
		$listId = $request->input('id');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['lp_page_delete'].",{$user['login_id']}");

		//削除にチェックが入っていれば
		if( !empty($listId) ){
			foreach($listId as $index => $id){
				if( !empty($request->input('del_flg')[$index]) ){
					$delete = Landing_page::where('id', $request->input('del_flg')[$index])->delete();
				}
			}
		}

		return null;
	}
*/
}

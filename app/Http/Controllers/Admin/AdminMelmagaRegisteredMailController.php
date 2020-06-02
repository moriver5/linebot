<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Registered_mail;
use App\Model\Group;
use Carbon\Carbon;
use Session;
use Utility;

class AdminMelmagaRegisteredMailController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 登録後送信メール画面表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['registered_top'].",{$user['login_id']}");

		//動的クエリを生成するため
		$query = Registered_mail::query();

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

		return view('admin.melmaga.index_registered', $disp_data);
	}

	/*
	 * 登録後送信メールの新規作成画面
	 */
	public function create()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//
		$disp_data = [
			'db_group_data'					=> $db_group_data,
			'melmaga_regist_career'			=> config('const.melmaga_regist_career'),
			'registered_specified_time'		=> config('const.registered_specified_time'),
			'registered_enable_disable'		=> config('const.registered_enable_disable'),
			'melmaga_search_item'			=> config('const.melmaga_search_item'),
			'melmaga_search_type'			=> config('const.melmaga_search_type'),
			'melmaga_device'				=> config('const.melmaga_device'),
			'ver'							=> time()
		];

		return view('admin.melmaga.registered_create', $disp_data); 
	}

	/*
	 * 本登録後送信メールの新規作成処理
	 */
	public function createSend(Request $request)
	{
		$this->validate($request, [
			'title'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'body'			=> 'bail|required|surrogate_pair_check|emoji_check',
			'html_body'		=> 'bail|surrogate_pair_check|emoji_check',
			'remarks'		=> 'bail|surrogate_pair_check|emoji_check',
			'item_value'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		$now_date = Carbon::now();

		$regist_data = [
			'specified_time'	=> $request->input('specified_time'),
			'enable_flg'		=> config('const.registered_enable_disable')[$request->input('enable_flg')][0],
			'item_type'			=> $request->input('item_type'),
			'like_type'			=> $request->input('like_type'),
			'title'				=> $request->input('title'),
			'body'				=> $request->input('body'),
			'html_body'			=> $request->input('html_body'),
			'remarks'			=> $request->input('remarks'),
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		];

		//抽出項目の値が顧客ID以外のとき
		if( $request->input('item_type') != 0 ){
			$listMail = explode(",", $request->input('item_value'));
			$regist_data['item_value'] = "'".implode("','", $listMail)."'";

		//抽出項目の値が顧客IDのとき
		}else{
			$regist_data['item_value'] = $request->input('item_value');				
		}

		//グループ
		if( $request->input('groups') != "" ){
			$regist_data['groups'] = implode(",",$request->input('groups'));
		}

		//registered_mailsテーブルにinsert
		$registered_mail = new Registered_mail($regist_data);

		//データをinsert
		$registered_mail->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['registered_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 登録後送信メールの編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Registered_mail::where('id',$id)->first();

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//landing_pagesテーブルにデータがなかったら一覧ページにリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_registered_mail_path').'?page='.$page);
		}

		//抽出項目の値に含まれているシングルコーテーションを削除
		if( !empty($db_data->item_value) ){
			$db_data->item_value = preg_replace("/'/", "", $db_data->item_value);
		}

		//画面表示用配列
		$disp_data = [
			'db_group_data'					=> $db_group_data,
			'melmaga_regist_career'			=> config('const.melmaga_regist_career'),
			'registered_specified_time'		=> config('const.registered_specified_time'),
			'registered_enable_disable'		=> config('const.registered_enable_disable'),
			'melmaga_search_item'			=> config('const.melmaga_search_item'),
			'melmaga_search_type'			=> config('const.melmaga_search_type'),
			'melmaga_device'				=> config('const.melmaga_device'),
			'edit_id'						=> $id,
			'db_data'						=> $db_data,
			'ver'							=> time()
		];

		return view('admin.melmaga.registered_edit', $disp_data);
	}

	/*
	 * 登録後送信メールの編集画面の編集処理
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'title'			=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'body'			=> 'bail|required|surrogate_pair_check|emoji_check',
			'html_body'		=> 'bail|surrogate_pair_check|emoji_check',
			'remarks'		=> 'bail|surrogate_pair_check|emoji_check',
			'item_value'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['registered_update'].",{$user['login_id']}");

		$update_value = [
			'specified_time'	=> $request->input('specified_time'),
			'enable_flg'		=> config('const.registered_enable_disable')[$request->input('enable_flg')][0],
			'item_type'			=> $request->input('item_type'),
			'like_type'			=> $request->input('like_type'),
			'title'				=> $request->input('title'),
			'body'				=> $request->input('body'),
			'html_body'			=> $request->input('html_body'),
			'remarks'			=> $request->input('remarks'),
			'updated_at'		=> Carbon::now()
		];

		//抽出項目が顧客ID以外のとき
		if( $request->input('item_type') != 0 && !empty($request->input('item_value')) ){
			$listMail = explode(",", $request->input('item_value'));
			$update_value['item_value'] = "'".implode("','", $listMail)."'";

		//抽出項目が顧客IDのとき
		}else{
			$update_value['item_value'] = $request->input('item_value');				
		}

		if( !empty($request->input('groups')) ){
			$update_value['groups'] = implode(",",$request->input('groups'));
		}else{
			$update_value['groups'] = $request->input('groups');
		}

		$update = Registered_mail::where('id', $request->input('edit_id'))->update($update_value);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['forecasts_edit'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 
	 */
	public function searchSetting()
	{
		//画面表示用配列
		$disp_data = [
			'session'						=> Session::all(),
			'ver'							=> time(),
			'registered_search_item'		=> config('const.registered_search_item'),
			'registered_search_like_type'	=> config('const.search_like_type'),
			'registered_specified_time'		=> config('const.registered_specified_time'),
			'registered_enable_disable'		=> config('const.registered_enable_disable'),
			'sort_list'						=> config('const.lp_sort_list'),
		];

		return view('admin.melmaga.registered_search', $disp_data);
	}

	/*
	 * 
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = Registered_mail::query();

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

		return view('admin.melmaga.index_registered', $disp_data);
	}

	/*
	 * 登録後送信メールの検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['registered_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Registered_mail::query();

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

		return view('admin.melmaga.index_registered', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		//検索項目
		if( !is_null(Session::get('registered_search_item_value')) ){
//			$query->where(Session::get('registered_search_item'), config('const.search_like_type')[Session::get('registered_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('registered_search_like_type')][1], Session::get('registered_search_item_value')));

			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listItem = explode(",", Session::get('registered_search_item_value'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('registered_search_item'), config('const.search_like_type')[Session::get('registered_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('registered_search_like_type')][1], $item ));
				}
			});
		}

		//推定時間
		if( !empty(Session::get('registered_specified_time')) ){
			$listSpecifiedTime = config('const.registered_specified_time');
			$query->whereIn('specified_time', explode(",", Session::get('registered_specified_time')));
		}

		//有効/無効
		if( !empty(Session::get('registered_enable_flg')) ){
			$listEnableDisable = config('const.registered_enable_disable');
			$query->where('enable_flg', $listEnableDisable[Session::get('registered_enable_flg')][0]);
		}

		//ソート
		$sort_item = "id";
		$sort_type = "asc";
		if( !is_null(Session::get('registered_sort')) ){
			$listSortType = config('const.registered_sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('registered_sort')][0]);
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
			Session::put('registered_search_item', $request->input('search_item'));
		}

		//検索の値
		Session::put('registered_search_item_value', $request->input('search_item_value'));

		//LIKE検索タイプ
		Session::put('registered_search_like_type', $request->input('search_like_type'));

		//指定時間
		Session::put('registered_specified_time', $request->input('specified_time'));

		//有効・無効
		Session::put('registered_enable_flg', $request->input('enable_flg'));

		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('registered_sort', $request->input('sort'));
		}
	}

	/*
	 * 一括削除処理
	 */
	public function bulkUpdate(Request $request)
	{

		//ID取得
		$listId = $request->input('id');

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['registered_delete'].",{$user['login_id']}");

		//削除にチェックが入っていれば
		if( !empty($listId) ){
			foreach($listId as $index => $id){
				if( !empty($request->input('del')[$index]) ){
					$delete = Registered_mail::where('id', $request->input('del')[$index])->delete();
				}
			}
		}

		return null;
	}
}

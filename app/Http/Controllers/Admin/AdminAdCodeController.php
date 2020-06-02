<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Group;
use App\Model\User;
use App\Model\Ad_code;
use App\Model\Agency;
use App\Model\AdminChannelAllowList;
use App\Model\Line_asp;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;
use Storage;
use File;

class AdminAdCodeController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 広告コード一覧画面表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['adcode_top'].",{$user['login_id']}");

		//ASPのデフォルト設定
		$listAsp[0] = 'なし';

		//ASPデータ取得
		$db_asp_data = Line_asp::get();
		if( count($db_asp_data) > 0 ){
			foreach($db_asp_data as $lines){
				$listAsp[$lines->id] = $lines->asp;
			}
		}

		//動的クエリを生成するため
		$query = Ad_code::query();
		$query->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_codes.line_basic_id');
		$query->leftJoin('agencies', 'agencies.id', '=', 'ad_codes.agency_id');
		$query->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_codes.line_basic_id');
		$query->where('admin_channel_allow_lists.admin_id', '=', $user['id']);
		$query->select('line_official_accounts.name as channel_name', 'ad_codes.id', 'ad_codes.line_basic_id', 'ad_codes.asp_id', 'ad_codes.ad_cd', 'ad_codes.agency_id', 'ad_codes.name as ad_name', 'ad_codes.category', 'ad_codes.url', 'agencies.name');
		$query->orderBy('ad_codes.id');

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//件数取得
		$total = $db_data->total();

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//代理店データ取得
		$db_agency_data = Agency::get();
		$list_agency = ['0' => '指定なし'];

		if( count($db_agency_data) > 0 ){
			foreach($db_agency_data as $lines){
				$list_agency[$lines->id] = $lines->name;
			}
		}

		//画面表示用配列
		$disp_data = [
			'list_asp'			=> $listAsp,
			'list_channel'		=> $db_allow_channel,
			'list_agency'		=> $list_agency,
			'ad_search_item'	=> config('const.ad_search_item'),
			'ad_category'		=> config('const.ad_category'),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.ad.adcode.index', $disp_data);
	}

	/*
	 * 広告コード新規作成画面
	 */
	public function create()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//代理店データ取得
		$db_agency_data = Agency::get();

		//ASPデータ取得
		$db_asp_data = Line_asp::get();

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		$disp_data = [
			'list_asp'						=> $db_asp_data,
			'db_agency_data'				=> $db_agency_data,
			'list_channel'					=> $db_allow_channel,
			'melmaga_regist_career'			=> config('const.melmaga_regist_career'),
			'registered_specified_time'		=> config('const.registered_specified_time'),
			'registered_enable_disable'		=> config('const.registered_enable_disable'),
			'melmaga_search_item'			=> config('const.melmaga_search_item'),
			'melmaga_search_type'			=> config('const.melmaga_search_type'),
			'melmaga_device'				=> config('const.melmaga_device'),
			'ver'							=> time()
		];

		return view('admin.ad.adcode.create', $disp_data); 
	}

	/*
	 * 広告コード新規作成処理
	 */
	public function createSend(Request $request)
	{
		$validate = [
			'ad_cd'			=> 'bail|required|max:'.config('const.ad_code_max_length').'|alpha_num_check|unique:ad_codes,ad_cd',
			'ad_name'		=> 'max:'.config('const.ad_name_max_length'),
			'description'	=> 'max:'.config('const.ad_code_memo_max_length'),
		];

		if( $request->input('agency_id') != "" ){
			$validate = array_merge($validate, ['agency_id' => 'numeric']);
		}

		if( $request->input('url') != "" ){
			$validate = array_merge($validate, ['url' => 'bail|url|active_url']);
		}

		$this->validate($request, $validate);

		$now_date = Carbon::now();

		$regist_data = [
			'line_basic_id'		=> $request->input('line_channel'),
			'group_id'			=> $request->input('group_id'),
			'asp_id'			=> $request->input('asp'),
			'ad_cd'				=> $request->input('ad_cd'),
			'category'			=> $request->input('category'),
			'created_at'		=> $now_date,
			'updated_at'		=> $now_date
		];

		//
		if( $request->input('agency_id') != "" ){
			$regist_data['agency_id'] = $request->input('agency_id');
		}

		//
		if( $request->input('ad_name') != "" ){
			$regist_data['name'] = $request->input('ad_name');
		}

		//
		if( $request->input('url') != "" ){
			$regist_data['url'] = $request->input('url');
		}

		//
		if( $request->input('description') != "" ){
			$regist_data['memo'] = $request->input('description');
		}

		//ad_codesテーブルにインサート
		$ad_codes = new Ad_code($regist_data);

		//データをinsert
		$ad_codes->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['adcode_create'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 広告コード編集画面表示
	 */
	public function edit($page, $id)
	{
		//動的クエリを生成するため
		$db_data = Ad_code::where('id',$id)->first();

		//編集データがない場合、データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_adcode_path'));
		}

		//代理店データ取得
		$db_agency_data = Agency::get();

		$list_agency = ['0' => '指定なし'];
		if( count($db_agency_data) > 0 ){
			foreach($db_agency_data as $lines){
				$list_agency[$lines->id] = $lines->name;
			}
		}

		//ASPデータ取得
		$db_asp_data = Line_asp::get();

		$listAsp[] = ['0', 'なし'];
		if( count($db_asp_data) > 0 ){
			foreach($db_asp_data as $lines){
				$listAsp[] = [$lines->id, $lines->asp];
			}
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//許可LINEチャンネル取得
		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//画面表示用配列
		$disp_data = [
			'list_asp'						=> $listAsp,
			'list_agency_data'				=> $list_agency,
			'list_channel'					=> $db_allow_channel,
			'edit_id'						=> $id,
			'db_data'						=> $db_data,
			'ver'							=> time()
		];

		return view('admin.ad.adcode.edit', $disp_data);
	}

	/*
	 * 広告コード編集画面の編集処理
	 */
	public function store(Request $request)
	{
		$validate = [
			'ad_cd'			=> 'bail|required|max:'.config('const.ad_code_max_length').'|alpha_num_check|unique:ad_codes,ad_cd,'.$request->input('edit_id').',id',
			'ad_name'		=> 'max:'.config('const.ad_name_max_length'),
			'description'	=> 'max:'.config('const.ad_code_memo_max_length'),
		];

		if( $request->input('agency_id') != "" ){
			$validate = array_merge($validate, ['agency_id' => 'numeric']);
		}

		if( $request->input('url') != "" ){
			$validate = array_merge($validate, ['url' => 'bail|url|active_url']);
		}

		$this->validate($request, $validate);

		//削除
		if( $request->input('del') == 1 ){
			Ad_code::where('id', $request->input('edit_id'))->delete();

		//更新
		}else{
			$update_value = [
				'line_basic_id'	=> $request->input('line_channel'),
				'group_id'		=> $request->input('group_id'),
				'asp_id'		=> $request->input('asp'),
				'ad_cd'			=> $request->input('ad_cd'),
				'agency_id'		=> $request->input('agency_id'),
				'category'		=> $request->input('category'),
				'name'			=> $request->input('name'),
				'url'			=> $request->input('url'),
				'memo'			=> $request->input('description'),
				'updated_at'	=> Carbon::now()
			];

			$update = Ad_code::where('id', $request->input('edit_id'))->update($update_value);
		}

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['adcode_edit'].",{$user['login_id']}");

		return null;
	}

	/*
	 * 検索設定画面表示
	 */
	public function searchSetting()
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//画面表示用配列
		$disp_data = [
			'session'						=> Session::all(),
			'ver'							=> time(),
			'list_channel'					=> $db_allow_channel,
			'ad_search_item'				=> config('const.ad_search_item'),
			'ad_category'					=> config('const.ad_category'),
		];

		return view('admin.ad.adcode.search_setting', $disp_data);
	}

	/*
	 * 検索結果のページャーのリンクを押下したときに呼び出される
	 */
	public function search(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//動的クエリを生成するため
		$query = Ad_code::query();
		$query->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_codes.line_basic_id');
		$query->leftJoin('agencies', 'agencies.id', '=', 'ad_codes.agency_id');
		$query->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_codes.line_basic_id');
		$query->where('admin_channel_allow_lists.admin_id', '=', $user['id']);
		$query->select('line_official_accounts.name as channel_name', 'ad_codes.id', 'ad_codes.line_basic_id', 'ad_codes.asp_id', 'ad_codes.ad_cd', 'ad_codes.agency_id', 'ad_codes.name as ad_name', 'ad_codes.category', 'ad_codes.url', 'agencies.name');
		$query->orderBy('ad_codes.id');

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$listAsp = [];

		//ASPのデフォルト設定
		$listAsp[0] = 'なし';

		//ASPデータ取得
		$db_asp_data = Line_asp::get();
		if( count($db_asp_data) > 0 ){
			foreach($db_asp_data as $lines){
				$listAsp[$lines->id] = $lines->asp;
			}
		}

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//代理店データ取得
		$db_agency_data = Agency::get();
		$list_agency = ['0' => '指定なし'];

		if( count($db_agency_data) > 0 ){
			foreach($db_agency_data as $lines){
				$list_agency[$lines->id] = $lines->name;
			}
		}

		//
		$disp_data = [
			'session'			=> Session::all(),
			'list_asp'			=> $listAsp,
			'list_channel'		=> $db_allow_channel,
			'list_agency'		=> $list_agency,
			'ad_search_item'	=> config('const.ad_search_item'),
			'ad_category'		=> config('const.ad_category'),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.ad.adcode.index', $disp_data);
	}

	/*
	 * 検索設定画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['adcode_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = Ad_code::query();
		$query->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_codes.line_basic_id');
		$query->leftJoin('agencies', 'agencies.id', '=', 'ad_codes.agency_id');
		$query->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_codes.line_basic_id');
		$query->where('admin_channel_allow_lists.admin_id', '=', $user['id']);
		$query->select('line_official_accounts.name as channel_name', 'ad_codes.id', 'ad_codes.line_basic_id', 'ad_codes.asp_id', 'ad_codes.ad_cd', 'ad_codes.agency_id', 'ad_codes.name as ad_name', 'ad_codes.category', 'ad_codes.url', 'agencies.name');
		$query->orderBy('ad_codes.id');

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$listAsp = [];

		//ASPのデフォルト設定
		$listAsp[0] = 'なし';

		//ASPデータ取得
		$db_asp_data = Line_asp::get();
		if( count($db_asp_data) > 0 ){
			foreach($db_asp_data as $lines){
				$listAsp[$lines->id] = $lines->asp;
			}
		}

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//代理店データ取得
		$db_agency_data = Agency::get();
		$list_agency = ['0' => '指定なし'];

		if( count($db_agency_data) > 0 ){
			foreach($db_agency_data as $lines){
				$list_agency[$lines->id] = $lines->name;
			}
		}

		$disp_data = [
			'session'			=> Session::all(),
			'list_asp'			=> $listAsp,
			'list_channel'		=> $db_allow_channel,
			'list_agency'		=> $list_agency,
			'ad_search_item'	=> config('const.ad_search_item'),
			'ad_category'		=> config('const.ad_category'),
			'db_data'			=> $db_data,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.ad.adcode.index', $disp_data);
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		if( !empty(Session::get('ad_line_channel')) ){
			$query->whereIn('ad_codes.line_basic_id', explode(",", Session::get('ad_line_channel')));
		}

		//検索項目
		if( !is_null(Session::get('ad_search_item_value')) ){
//			$query->where(Session::get('registered_search_item'), config('const.search_like_type')[Session::get('registered_search_like_type')][0], sprintf(config('const.search_like_type')[Session::get('registered_search_like_type')][1], Session::get('registered_search_item_value')));

			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listItem = explode(",", Session::get('ad_search_item_value'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('ad_search_item'), $item);
				}
			});
		}

		//
		if( !empty(Session::get('ad_category')) ){
			$query->whereIn('ad_codes.category', explode(",", Session::get('ad_category')));
		}

		//代理店
		$query->where('ad_codes.agency_id', Session::get('ad_agency_id'));

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
			$db_data = $query->paginate(50);
//			$sql = $query->toSql();
		}

		return $db_data;
	}

	/*
	 * SQL文の条件保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//LINEチャンネル
		if( !is_null($request->input('line_channel')) ){
			Session::put('ad_line_channel', implode(",", $request->input('line_channel')));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('ad_line_channel');
		}

		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('ad_search_item', $request->input('search_item'));
		}

		//検索の値
		Session::put('ad_search_item_value', $request->input('search_item_value'));

		//
		if( !empty($request->input('category')) ){
			Session::put('ad_category', implode(",", $request->input('category')));
		}else{
			Session::forget('ad_category');
		}

		Session::put('ad_agency_id', $request->input('agency_id'));
	}

	/*
	 * 広告コード一覧画面からの削除処理
	 */
	public function bulkDeleteSend(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ID取得
		$listId = $request->input('id');

		//削除ID取得
		$listDelId = $request->input('del');

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['adcode_del'].",{$user['login_id']}");	

		foreach($listId as $index => $id){
			//配列のエラーチェック
			$this->validate($request, [
				'del.*'		=> 'required',
			]);

			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//テーブルからデータ削除
				Ad_code::where('id', $id)->delete();

			}
		}

		return null;
	}

}

<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\User;
use App\Model\Group;
use App\Model\Point;
use App\Model\Point_log;
use App\Model\Point_setting;
use App\Model\Payment_log;
use App\Model\Magnification_setting;
use App\Model\Top_product;
use App\Model\Client_export_log;
use App\Model\Personal_mail_log;
use App\Model\Create_order_id;
use App\Model\User_group;
use App\Model\Registered_mail_queue;
use App\Mail\SendMail;
use Auth;
use Carbon\Carbon;
use Mail;
use Excel;
use Session;
use Utility;
use DB;
use Storage;
use Response;

class AdminClientController extends Controller
{
	private $log_export_obj;
	private $log_history_obj;
	private $log_obj;
	
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_export_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_export_file_name'));
		$this->log_history_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
		$this->log_obj			 = new SysLog(config('const.operation_point_log_name'), config('const.system_log_dir_path').config('const.operation_point_history_file_name'));
	}
	
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = User_group::query();

		$list_group_data = [];

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		if( !empty($db_group_data) > 0 ){
			foreach($db_group_data as $lines){
				$list_group_data[$lines->id] = $lines->name;
			}
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'	=> $list_group_data,
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}

	/*
	 * USER LISTの一括削除処理
	 */
	public function updateUserSend(Request $request, $page, $client_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//ログ出力
//		$this->log_history_obj->addLog(config('const.admin_display_list')['client_bulk_delete'].",{$user['login_id']}");	

		$validate = [
			'email'			=> 'bail|required|email|max:'.config('const.email_length').'|unique:users,mail_address,'.$client_id.',id|check_mx_domain',
		];

		//MEMOになにか入力されていたら
		if( !empty($request->input('description')) ){
			$validate['description'] = 'json';
		}

		//エラーチェック
		$this->validate($request, $validate);

		//usersテーブルのdisableを1に更新
		$update = User::where('id', $client_id)
			->update([
				'mail_address'	 => $request->input('email'),
				'send_flg'		 => $request->input('send_flg'),
				'disable'		 => $request->input('disable') ? $request->input('disable'):0,
				'description'	 => $request->input('description')
			]);
						
		return null;
	}

	//クライアント検索画面
	public function search(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));
		
		//
		$disp_data = [
			'db_group_data'		=> $db_group_data,
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'		=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}
	
	public function searchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'			=> $db_group_data,
			'session'				=> Session::all(),
			'ver'					=> time(),
			'list_sex'				=> config('const.list_sex'),
			'list_age'				=> config('const.list_age'),
			'client_search_item'	=> config('const.client_search_item'),
			'search_like_type'		=> config('const.search_like_type'),
			'regist_status'			=> config('const.regist_status'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.sort_list'),
		];
		
		return view('admin.client.client_search', $disp_data);
	}
	
	//クライアント検索処理
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_search'].",{$user['login_id']}");

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'db_group_data'		=> $db_group_data,
			'db_data'			=> $db_data,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}

	//クライアント検索処理
	public function searchAdPost(Request $request, $ad_cd)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_search'].",{$user['login_id']}");

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$disp_data = [
			'db_group_data'		=> $db_group_data,
			'db_data'			=> $db_data,
			'ver'				=> time()
		];
		
		return view('admin.client.index', $disp_data);
	}

	/*
	 * 
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索タイプ
		if( !is_null($request->input('search_type')) ){
			Session::put('search_type', $request->input('search_type'));
		}

		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('search_item', $request->input('search_item'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('search_item');
		}
		
		//LIKE検索
		if( !is_null($request->input('search_like_type')) ){
			Session::put('search_like_type', $request->input('search_like_type'));
		}

		//グループ
		if( !is_null($request->input('group_id')) ){
			Session::put('group_id', $request->input('group_id'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('group_id');
		}
		
		//登録状態
		if( !is_null($request->input('reg_status')) ){
			Session::put('reg_status', $request->input('reg_status'));
		}else{
			//チェックがなかったら破棄
			Session::forget('reg_status');
		}

		//性別
		if( !is_null($request->input('sex')) ){
			Session::put('reg_sex', $request->input('sex'));
		}else{
			//チェックがなかったら破棄
			Session::forget('reg_sex');
		}

		//年代
		if( !is_null($request->input('age')) ){
			Session::put('reg_age', $request->input('age'));
		}else{
			//チェックがなかったら破棄
			Session::forget('reg_age');
		}

		//登録日時-開始
		if( !empty($request->input('start_regdate')) ){
			Session::put('start_regdate', $request->input('start_regdate'));
		}else{
			//未入力なら破棄
			Session::forget('start_regdate');
		}

		//登録日時-終了
		if( !empty($request->input('end_regdate')) ){
			Session::put('end_regdate', $request->input('end_regdate'));
		}else{
			//未入力なら破棄
			Session::forget('end_regdate');
		}

		//ソート
		$sort_item = "id";
		$sort_type = "asc";
		if( !is_null($request->input('sort')) ){
			Session::put('sort', $request->input('sort'));
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null($request->input('search_disp_num')) ){
			Session::put('search_disp_num', $request->input('search_disp_num'));
		}
	}
	
	/*
	 * usersテーブルの検索条件を保存されたSessionから設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		$query->join('users', 'user_groups.client_id', '=', 'users.id');

		//削除を省く
//		$query->where('user_groups.disable', 0);

		//検索項目
		if( !is_null(Session::get('search_item')) ){
/*
			$listSearchLikeType = config('const.search_like_type');
			$query->where(Session::get('search_type'), $listSearchLikeType[Session::get('search_like_type')][0], sprintf($listSearchLikeType[Session::get('search_like_type')][1], Session::get('search_item') ));
*/
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listSearchLikeType = config('const.search_like_type');
				$listItem = explode(",", Session::get('search_item'));
				foreach($listItem as $index => $item){
					$query->orWhere(Session::get('search_type'), $listSearchLikeType[Session::get('search_like_type')][0], sprintf($listSearchLikeType[Session::get('search_like_type')][1], $item ));
				}
			});
		}

		//グループ
		if( !empty(Session::get('group_id')) ){
			$query->whereIn('user_groups.group_id', explode(",",Session::get('group_id')));
		}
		
		//登録状態
		if( !is_null(Session::get('reg_status')) ){
			//チェックしたindexを配列で取得
			$listSltStatus = explode(",", Session::get('reg_status'));
			foreach($listSltStatus as $index){
				//チェックしたindexが登録状態リスト配列の添え字になってるので、
				//指定した配列内の１番目の値が登録状態の値となる
				$listStatus[] = config('const.regist_status')[$index][0];
			}
			$query->whereIn('user_groups.status', $listStatus);
		}

		if( !empty(Session::get('reg_sex')) ){
			$query->where('user_groups.sex', '=', Session::get('reg_sex'));
		}

		if( !empty(Session::get('reg_age')) ){
			$query->where('user_groups.age', '=', Session::get('reg_age'));
		}

		//登録日時-開始
		if( !empty(Session::get('start_regdate')) ){
			//現在時刻をyyyymmddhhmmssにフォーマット
			$start_regdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('start_regdate')).'00';
			$query->where('user_groups.regist_date', '>=', $start_regdate);
		}

		//登録日時-終了
		if( !empty(Session::get('end_regdate')) ){
			//現在時刻をyyyymmddhhmmssにフォーマット
			$end_regdate = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", Session::get('end_regdate')).'00';
			$query->where('user_groups.regist_date', '<=', $end_regdate);
		}

		//ソート
		$sort_item = "id";
		$sort_type = "desc";
		if( !is_null(Session::get('sort')) ){
			$listSortType = config('const.sort_list');
			list($sort_item,$sort_type) = explode(",", $listSortType[Session::get('sort')][0]);
			$query->orderBy($sort_item, $sort_type);
		}else{
			$query->orderBy($sort_item, $sort_type);
		}

		//表示件数
		$disp_limit = config('const.admin_client_list_limit');
		if( !is_null(Session::get('search_disp_num')) ){
			$list_disp_limit = config('const.search_disp_num');
			$disp_limit = $list_disp_limit[Session::get('search_disp_num')];
		}

//		$query->groupBy('users.id', 'users.mail_address', 'user_groups.status', 'user_groups.group_id', 'user_groups.ad_cd', 'user_groups.sex', 'user_groups.age', 'user_groups.created_at', 'user_groups.updated_at', 'user_groups.quit_datetime', 'users.description');
		$query->groupBy('users.id');

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$query->select('users.id', 'users.mail_address');
			$db_data = count($query->get());

		//顧客データのエクスポート
		}elseif( $exec_type == config('const.search_exec_type_export_key') ){
			$query->select('users.id', 'users.mail_address', 'user_groups.status', 'user_groups.group_id', 'user_groups.ad_cd', 'user_groups.sex', 'user_groups.age', 'user_groups.created_at', 'user_groups.updated_at', 'user_groups.quit_datetime', 'users.description');
			$db_data = $query->get();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$query->select('users.id', 'users.mail_address');
			$db_data = $query;

		//通常検索
		}else{
			$query->select('users.id', 'users.mail_address', 'users.disable', 'users.send_flg');
			$db_data = $query->paginate($disp_limit);
		}
			
		return $db_data;
	}
	
	//クライアント検索エクスポート
	public function clientExport(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		$listGroup = [];
		$db_group_data = Group::get();
		foreach($db_group_data as $lines){
			$listGroup[$lines->id] = $lines->name;
		}

		//動的クエリを生成するため
		$query = User_group::query();

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_export_key'));
//error_log(print_r($db_data,true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
		//DBから取得したデータを配列に格納
		$listData = [];
		foreach($db_data as $lines){
			$listData[] = [
				$lines->id,
				$lines->mail_address,
				config('const.disp_regist_status')[$lines->status],
				$listGroup[$lines->group_id],
				$lines->ad_cd,
				config('const.list_sex')[$lines->sex],
				config('const.list_age')[$lines->age],
				$lines->created_at,
				$lines->updated_at,
				$lines->quit_datetime,
				$lines->description
			];
		}

		//エクスポートした操作ユーザーの情報をログ出力
		//引数：ログに書き込む内容
		$this->log_export_obj->addLog($user['login_id']);

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_export'].",{$user['login_id']}");

		//エクスポートファイル
		$save_export_file = config('const.excel_file_name').'_'.date('Ymd_His');

		//保存データ設定
		$export_log_db = new Client_export_log([
			'login_id'	=> $user['login_id'],
			'file'		=> $save_export_file,
		]);

		//DB保存
		$export_log_db->save();
		
		//Maatwebsite/Laravel-Excelを使用してExcelデータ出力
		Excel::create($save_export_file, function($excel) use($listData) {
			$excel->sheet(config('const.excel_seet_name'), function($sheet) use($listData) {
				//Excelデータの1行目を出力
				$sheet->row(1, config('const.export_file_header_column'));
				
				//DBデータ件数取得
				$listCount = count($listData);

				//2行目以降ループしながらデータ出力
				for($i=0; $i<$listCount; $i++){
					$sheet->appendRow($listData[$i]);
				}
			});
		})->export('xls');
		
		return null;
	}
	
	private function _saveExportSearchOption(Request $request)
	{
		//エクスポート開始日時
		if( !empty($request->input('start_export_date')) ){
			Session::put('start_export_date', $request->input('start_export_date'));
		}else{
			//未入力なら破棄
			Session::forget('start_export_date');
		}
		
		//エクスポート終了日時
		if( !empty($request->input('end_export_date')) ){
			Session::put('end_export_date', $request->input('end_export_date'));
		}else{
			//未入力なら破棄
			Session::forget('end_export_date');
		}
		
		//ソート
		if( !is_null($request->input('sort')) ){
			Session::put('sort', $request->input('sort'));
		}
	}
	
	private function _getExportSearchOptionData($query)
	{
		//
		if( !is_null(Session::get('start_export_date')) ){
			$query->where('created_at', '>=', Session::get('start_export_date'));
		}

		//
		if( !is_null(Session::get('end_export_date')) ){
			$query->where('created_at', '<=', Session::get('end_export_date'));
		}

		//ソート
		if( !is_null(Session::get('sort')) ){
			$query->orderBy('created_at', config('const.list_export_sort')[Session::get('sort')]);
		}
		
		$db_data = $query->paginate(config('const.admin_client_list_limit'));
		
		return $db_data;
	}
	
	public function clientExportOperationLog(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索ボタンを押下したときだけ検索条件をセッションに保存
		if( $request->input('submit') == 1 ){
			$this->_saveExportSearchOption($request);
		}
			
		//動的クエリを生成するため
		$query = Client_export_log::query();
		
		//検索条件を追加後、データ取得
		$db_data = $this->_getExportSearchOptionData($query);

		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $db_data,
			'ver'				=> time(),
		];
		
		return view('admin.client.export_log', $disp_data); 
	}

	//個別リスト画面
	public function personalList(Request $request, $page, $id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$user_data = User::where('id', $id)->first();

		//DBのusersテーブルからデータ取得
		$db_data = DB::table('user_groups')
				->select('users.mail_address','users.id', 'users.send_flg', 'users.description', 'users.disable as user_disable', 'users.created_at', 'user_groups.client_id', 'user_groups.group_id', 'user_groups.status', 'user_groups.sex', 'user_groups.age', 'user_groups.ad_cd', 'user_groups.disable', 'groups.name')
				->join('users', 'user_groups.client_id', '=', 'users.id')
				->join('groups', 'user_groups.group_id', '=', 'groups.id')
				->where('users.id',$id)
				->paginate(config('const.admin_client_list_limit'));

		//編集データがない場合、顧客データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_client_path'));
		}
		
		//DBのgroupテーブルからデータ取得
		$db_grpup_data = Group::get();

		//戻るリンクのデフォルトを顧客管理一覧に設定
		$back_url = config('const.base_admin_url').'/'.config('const.client_url_path').'?page=';
		
		//閲覧者検索から来た場合の戻るリンク
		if( !empty($request->input('back')) ){
			$back_url = config('const.base_admin_url').'/'.config('const.visitor_url_path').'?page=';
		}

		$back_btn_flg = 1;
		//戻るボタンを表示するかどうか
		if( !is_null($request->input('back_btn')) ){
			$back_btn_flg = $request->input('back_btn');
		}

		if( empty($db_data[0]->mail_address) ){
			return view('admin.client.personal_empty', ['ver' => time(), 'back_url' => $back_url]); 
		}

		//
		$disp_data = [
			'client_id'			=> $id,
			'email'				=> $db_data[0]->mail_address,
			'back_btn_flg'		=> $back_btn_flg,
			'back_url'			=> $back_url,
			'user_data'			=> $user_data,
			'db_data'			=> $db_data,
			'db_grpup_data'		=> $db_grpup_data,
			'list_send_status'	=> config('const.list_send_status'),
			'page'				=> $page,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time(),
		];
		
		return view('admin.client.personal_list', $disp_data); 
	}

	//クライアント編集画面
	public function edit(Request $request, $page, $client_id, $group_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//DBのusersテーブルからデータ取得
		$db_data = User::join('user_groups', 'user_groups.client_id', '=', 'users.id')
			->where('user_groups.client_id', $client_id)
			->where('user_groups.group_id',$group_id)
			->first();

		//編集データがない場合、顧客データ一覧へリダイレクト
		if( empty($db_data) ){
			return redirect(config('const.base_admin_url').config('const.admin_client_path'));
		}
		
		//DBのgroupテーブルからデータ取得
		$db_grpup_data = Group::where('id', $group_id)->first();

		//戻るリンクのデフォルトを顧客管理一覧に設定
		$back_url = config('const.base_admin_url').'/'.config('const.client_melmaga_list_url_path').'/'.$page.'/'.$client_id.'?page=';
		
		//閲覧者検索から来た場合の戻るリンク
		if( !empty($request->input('back')) ){
//			$back_url = config('const.base_admin_url').'/'.config('const.visitor_url_path').'?page=';
		}

		$back_btn_flg = 1;
		//戻るボタンを表示するかどうか
		if( !is_null($request->input('back_btn')) ){
			$back_btn_flg = $request->input('back_btn');
		}

		//
		$disp_data = [
			'client_id'			=> $client_id,
			'back_btn_flg'		=> $back_btn_flg,
			'back_url'			=> $back_url,
			'db_data'			=> $db_data,
			'group_id'			=> $db_grpup_data->id,
			'group_name'		=> $db_grpup_data->name,
			'list_sex'			=> config('const.list_sex'),
			'list_age'			=> config('const.list_age'),
			'page'				=> $page,
			'ver'				=> time(),
		];
		
		return view('admin.client.edit', $disp_data); 
	}
	
	/*
	 * 顧客編集画面からポイントの手動追加画面を表示
	 */
	public function addPoint($id)
	{	
		//pointsテーブルから手動ポイントのリストを取得
//		$list_point = Point::where('pay_type', 'bank')->get();

		$now_date = Carbon::now();

		//倍率設定済の購入ポイント取得
		$query = Magnification_setting::query();
		$query->join('point_settings', 'magnification_settings.category_id', '=', 'point_settings.category_id');
		$query->where('magnification_settings.start_date','<=', $now_date);
		$query->where('magnification_settings.end_date', '>=', $now_date);
		$db_data = $query->get();

		//倍率設定がされていなければ通常設定を取得
		if( count($db_data) == 0 ){
			//magnification_settingsテーブルの通常設定IDを取得
			$db_data = Magnification_setting::first();
			if( !empty($db_data) ){
				//通常設定の購入ポイントを取得
				$query = Point_setting::query();
				$db_data = $query->where('category_id', $db_data->default_id)->get();
			}
		}

		$disp_data = [
			'id'			=> $id,
			'list_point'	=> $db_data,	
			'ver'			=> time(),
		];
		
		return view('admin.client.add_point', $disp_data); 
	}
	
	/*
	 * 顧客編集画面からポイントの手動追加画面を表示
	 */
	public function addPointSend(Request $request, $id)
	{
		//追加するポイントが選択されているかチェック
		$this->validate($request, [
			'point_id'	 => 'required',
		]);

		//選択したポイントをpoint_settingテーブルから取得
		$point = Point_setting::where('id', $request->input('point_id'))->first();

		//usersテーブルから現在のポイント取得
		$current_point = User::query()->where('id',$id)->get(['point'])->first();

		//usersテーブルへポイント追加更新
		$query = User::query();
		$query->where('id',$id)->increment('point', $point->point);

		//更新後のポイント取得用の動的クエリを生成するため
		$query = User::query();

		//更新後のポイントを取得
		$db_data = $query->where('id',$id)->get(['point','login_id'])->first();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ポイントログ履歴用の動的クエリを生成するため
		$log = new Point_log([
			'login_id'					=> $db_data->login_id,
			'add_point'					=> $point->point,
			'prev_point'				=> $current_point->point,
			'current_point'				=> $db_data->point,
			'operator'					=> $user['login_id']
		]);

		//データをinsert
		$log->save();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$id},{$point->point},{$current_point->point},{$db_data->point},{$user['login_id']}");

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_point_add'].",{$user['login_id']}");

		return null;
	}
	
	/*
	 * 顧客編集画面から個別メール画面表示
	 */
	public function editMail($id)
	{	
		//usersテーブルからデータ取得
		$user = User::where('id', $id)->first();
		
		$disp_data = [
			'id'		=> $id,
			'user'		=> $user,
			'ver'		=> time(),
		];
		
		return view('admin.client.edit_mail', $disp_data); 
	}

	/*
	 * 個別メールの送信履歴画面を表示
	 */
	public function historyMailLog($id)
	{	
		//usersテーブルからデータ取得
		$db_data = Personal_mail_log::where('client_id', $id)->get();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_mail_history'].",{$user['login_id']}");
		
		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];

		return view('admin.client.history_mail_log', $disp_data); 
	}

	/*
	 * 個別メールの送信履歴の詳細画面を表示
	 */
	public function historyMailLogDetail($id, $detail_id)
	{
		//送信履歴のデータを取得
		$query = User::query();
		$db_data = $query->join('personal_mail_logs', 'users.id', '=', 'personal_mail_logs.client_id')
			->where([
				'personal_mail_logs.client_id'	=> $id,
				'personal_mail_logs.id'			=> $detail_id
			])->first();
	
		$disp_data = [
			'id'		=> $id,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];
		
		return view('admin.client.history_mail_log_detail', $disp_data); 
	}

	/*
	 * クライアント編集処理
	 */
	public function store(Request $request)
	{
		//編集しているusersテーブルのidを取得
		$group_id = $request->input('group_id');
		$client_id = $request->input('client_id');

		$validate = [
			'ad_cd'			=> 'alpha_num_check',
		];

		//エラーチェック
		$this->validate($request, $validate);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		$now_date = Carbon::now();

		$update_value = [
			'status'					=> $request->input('status'),
			'ad_cd'						=> $request->input('ad_cd'),
			'sex'						=> $request->input('sex'),
			'age'						=> $request->input('age'),
			'disable'					=> $request->input('del') ? $request->input('del'):0
		];

		$update_user_value = [
			'description'				=> $request->input('description')
		];

		//初めて本登録するとき
		if( $request->input('regist_date') == '' && $request->input('status') == config('const.db_regist_status')['1'] ){
			$update_value['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			$update_value['quit_datetime'] = null;
			$update_value['sort_quit_datetime'] = null;

		//退会
		}else if( $request->input('status') == config('const.db_regist_status')['2'] ){
			$update_value['quit_datetime'] = $now_date;
			$update_value['sort_quit_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//ブラック
		}else if( $request->input('status') == config('const.db_regist_status')['3'] ){
			$update_value['status'] = config('const.db_regist_status')['3'];
		}
//error_log($client_id.":".$group_id.":test\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
//error_log(print_r($update_value, true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
//error_log(print_r($update_user_value, true)."\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
		$update = User_group::where('client_id', $client_id)->where('group_id', $group_id)->update($update_value);
		$update = User::where('id', $client_id)->update($update_user_value);

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_edit_update'].",{$user['login_id']}");

		return null;
	}
	
	//クライアント新規作成画面
	public function create()
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//DBのgroupテーブルからデータ取得
		$db_grpup_data = Group::get();
		
		//
		$disp_data = [
			'db_grpup_data'		=> $db_grpup_data,
			'list_sex'			=> config('const.list_sex'),
			'list_age'			=> config('const.list_age'),
			'ver'				=> time(),
		];
		
		return view('admin.client.create', $disp_data); 
	}
	
	public function createSend(Request $request)
	{
		$validate = [
			'email'			=> 'bail|required|email|max:'.config('const.email_length').'|unique:users,mail_address|check_mx_domain',
			'ad_cd'			=> 'alpha_num_check',
		];

		//MEMOになにか入力されていたら
		if( !empty($request->input('description')) ){
			$validate['description'] = 'json';
		}

		//エラーチェック
		$this->validate($request, $validate);

		$now_date = Carbon::now();

		//アクセスキー生成
//		$remember_token = session_create_id();		

		//登録データ
		$create_group_data = [
			'sex'						=> $request->input('sex'),
			'age'						=> $request->input('age'),
			'status'					=> $request->input('status'),
			'group_id'					=> $request->input('group_id'),
			'ad_cd'						=> $request->input('ad_cd'),
			'created_at'				=> $now_date,
			'updated_at'				=> $now_date
		];

		$create_data = [
			'mail_address'				=> mb_strtolower(trim($request->input('email'))),
			'description'				=> $request->input('description'),
//			'remember_token'			=> $remember_token,
			'created_at'				=> $now_date,
			'updated_at'				=> $now_date
		];

		//本登録
		if( $request->input('status') == config('const.db_regist_status')['1'] ){
			$create_group_data['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//退会
		}else if( $request->input('status') == config('const.db_regist_status')['2'] ){
			$create_group_data['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			$create_group_data['quit_datetime'] = $now_date;
			$create_group_data['sort_quit_datetime'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//ブラック
		}else if( $request->input('status') == config('const.db_regist_status')['3'] ){
			$create_group_data['regist_date'] = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';
			$create_group_data['status'] = config('const.db_regist_status')['3'];
		}

		$client_id = DB::table('users')->insertGetId($create_data);

		$create_group_data['client_id'] = $client_id;

		$user = new User_group($create_group_data);

		//DB保存
		$user->save();

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_create'].",{$user['login_id']}");

		return null;
	}
	
	//クライアント-ステータス変更画面
	public function group()
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、データ取得
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));
		
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$list_groups = [];
		if( !empty($db_group_data) ){
			foreach($db_group_data as $lines){
				$list_groups[$lines->id] = ['name' => $lines->name, 'memo' => $lines->memo];
			}
		}

		//
		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_search_count'	=> $db_search_count,
			'db_group_data'		=> $list_groups,
			'ver'				=> time(),
		];
		
		return view('admin.client.group', $disp_data); 
	}
	
	/*
	 * 
	 */
	public function groupSearchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'			=> $db_group_data,
			'session'				=> Session::all(),
			'ver'					=> time(),
			'client_search_item'	=> config('const.client_search_item'),
			'search_like_type'		=> config('const.search_like_type'),
			'regist_status'			=> config('const.regist_status'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.sort_list'),
			'list_sex'				=> config('const.list_sex'),
			'list_age'				=> config('const.list_age'),
		];
		
		return view('admin.client.group_search', $disp_data);
	}
	
	//クライアント-グループ移行画面
	public function groupSearchCount(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、結果件数を表示
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_move_count_key'));

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_group_data'		=> $db_group_data,
			'db_search_count'	=> $db_search_count,
			'ver'				=> time(),
		];

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['group_search'].",{$user['login_id']}");

		return view('admin.client.group', $disp_data); 
	}
	
	public function groupSearchMove(Request $request)
	{
		$this->validate($request, [
			'group_id'	=> 'required'
		]);
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、結果件数を表示
		$db_search_where = $this->_getSearchOptionData($query, config('const.search_exec_type_move_unexecuted_key'));

		//検索結果取得
		$db_data = $db_search_where->get();

		//ユーザーIDごとにグループを格納
		$listUser = [];
		foreach($db_data as $lines){
			$listUser[$lines->client_id][] = $lines->group_id;
		}

		//検索条件でグループ移行
		$update = DB::table('user_groups');
		foreach($listUser as $client_id => $group_ids){
			$update->orWhere(function($query) use($client_id, $group_ids){
				$query->where('user_groups.client_id', $client_id);
				$query->whereIn('user_groups.group_id', $group_ids);
			});
		}
	
		$update->update([
			'group_id'	=> $request->input('group_id')
		]);
	
		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['group_move'].",{$user['login_id']}");

		return null;
	}
	
	//クライアント-ステータス変更画面
	public function status()
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、データ取得
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));
		
		//
		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_search_count'	=> $db_search_count,
			'ver'				=> time(),
		];
		
		return view('admin.client.status', $disp_data); 
	}
	
	/*
	 * 
	 */
	public function statusSearchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();
		
		//画面表示用配列
		$disp_data = [
			'db_group_data'			=> $db_group_data,
			'session'				=> Session::all(),
			'ver'					=> time(),
			'client_search_item'	=> config('const.client_search_item'),
			'search_like_type'		=> config('const.search_like_type'),
			'regist_status'			=> config('const.regist_status'),
			'dm_status'				=> config('const.dm_status'),
			'search_disp_num'		=> config('const.search_disp_num'),
			'sort_list'				=> config('const.sort_list'),
		];
		
		return view('admin.client.status_search', $disp_data);
	}
	
	/*
	 * 顧客データインポート画面表示
	 */
	public function importClientData(Request $request)
	{
		$exist_bad_email_flg = Storage::disk('logs')->exists(config('const.import_error_email_file_name'));
		$exist_mx_domain_flg = Storage::disk('logs')->exists(config('const.import_mx_domain_error_file_name'));
		$exist_duplicate_flg = Storage::disk('logs')->exists(config('const.import_error_file_name'));

		$disp_data = [
			'bad_email_flg'		=> $exist_bad_email_flg,
			'mx_domain_flg'		=> $exist_mx_domain_flg,
			'duplicate_flg'		=> $exist_duplicate_flg,
			'ver'				=> time(),
		];
		
		return view('admin.client.import', $disp_data); 
	}
	
	/*
	 * 顧客データインポート処理
	 */
	public function importClientUpload(Request $request)
	{
		$this->validate($request, [
			'ad_cd'	 => 'alpha_num_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_import'].",{$user['login_id']}");
		
		//広告コード取得
		$ad_cd = $request->input('ad_cd');
		
		//アップロードファイルオブジェクト取得
		$file = $request->file('import_file');

		//アップロードファイル名取得
		$upload_file = $file->getClientOriginalName();
		
		//アップロードファイルのディレクトリ移動
		$file->move( config('const.save_import_file_dir'), $upload_file );

		//バックグラウンドでusersテーブルへアップロードデータをinsertするためのオブジェクト生成
		$process = new Process(config('const.artisan_command_path')." file:upload {$upload_file} {$ad_cd} > /dev/null");

		//非同期実行(/data/www/melmaga/app/Console/Commands/FileUpload.php)
		$process->start();

		$disp_data = [
			'ver'				=> time(),
		];

		return null;
	}

	/*
	 * 
	 */
	public function downLoadBadEmail()
	{
		$stream = fopen('php://temp', 'w');

		//ログ読込み
		$contents = explode("\n",Storage::disk('logs')->get(config('const.import_error_email_file_name')));

		//1行取り出して日付・メールアドレスを抽出
		foreach($contents as $lines){
			//空をスキップ
			if( empty($lines) ){
				continue;
			}

			//日付・メールアドレス抽出
			list($date, $email) = explode(",", preg_replace("/^\[(.+?)\]\s.+?\.INFO:\s(.+?)\s\[\].+/", "$1,$2", $lines));

			//1行をCSV形式で書き込む
			fputcsv($stream, [$date,$email], ',');
		}
		rewind($stream);

		$csv = mb_convert_encoding(str_replace(PHP_EOL, "\r\n", stream_get_contents($stream)), 'SJIS', 'UTF-8');

		$headers = array(
		  'Content-Type' => 'text/csv',
		  'Content-Disposition' => 'attachment; filename="'.config('const.dl_unknown_mx_domain_file_name').'"'
		);

		return Response::make($csv, 200, $headers);
	}

	/*
	 * 
	 */
	public function downLoadUnknownMxDomain()
	{
		$stream = fopen('php://temp', 'w');

		//ログ読込み
		$contents = explode("\n",Storage::disk('logs')->get(config('const.import_mx_domain_error_file_name')));

		//1行取り出して日付・メールアドレスを抽出
		foreach($contents as $lines){
			//空をスキップ
			if( empty($lines) ){
				continue;
			}

			//日付・メールアドレス抽出
			list($date, $email) = explode(",", preg_replace("/^\[(.+?)\]\s.+?\.INFO:\s(.+?)\s\[\].+/", "$1,$2", $lines));

			//1行をCSV形式で書き込む
			fputcsv($stream, [$date,$email], ',');
		}
		rewind($stream);

		$csv = mb_convert_encoding(str_replace(PHP_EOL, "\r\n", stream_get_contents($stream)), 'SJIS', 'UTF-8');

		$headers = array(
		  'Content-Type' => 'text/csv',
		  'Content-Disposition' => 'attachment; filename="'.config('const.dl_unknown_mx_domain_file_name').'"'
		);

		return Response::make($csv, 200, $headers);
	}

	/*
	 * 
	 */
	public function downLoadDuplicateEmail()
	{
		$stream = fopen('php://temp', 'w');

		//ログ読込み
		$contents = explode("\n",Storage::disk('logs')->get(config('const.import_error_file_name')));

		//1行取り出して日付・メールアドレスを抽出
		foreach($contents as $lines){
			//空をスキップ
			if( empty($lines) ){
				continue;
			}

			//日付・メールアドレス抽出
			list($date, $email) = explode(",", preg_replace("/^\[(.+?)\]\s.+?\.INFO:\s(.+?)\s\[\].+/", "$1,$2", $lines));

			//1行をCSV形式で書き込む
			fputcsv($stream, [$date,$email], ',');
		}
		rewind($stream);

		$csv = mb_convert_encoding(str_replace(PHP_EOL, "\r\n", stream_get_contents($stream)), 'SJIS', 'UTF-8');

		$headers = array(
		  'Content-Type' => 'text/csv',
		  'Content-Disposition' => 'attachment; filename="'.config('const.dl_duplicate_file_name').'"'
		);

		return Response::make($csv, 200, $headers);
	}

	/*
	 * 
	 */
	public function deleteBadEmail()
	{
		//ログファイル名取得
		$file = config('const.import_error_email_file_name');

		//ファイル存在チェック
		$exist_bad_email_flg = Storage::disk('logs')->exists($file);

		//ファイル削除
		if( !empty($exist_bad_email_flg) ){
			Storage::disk('logs')->delete($file);
		}

		//顧客データインポート画面へリダイレクト
		return redirect(config('const.base_admin_url').config('const.admin_client_import_path'));
	}

	/*
	 * 
	 */
	public function deleteUnknownMxDomain()
	{
		//ログファイル名取得
		$file = config('const.import_mx_domain_error_file_name');

		//ファイル存在チェック
		$exist_mx_domain_flg = Storage::disk('logs')->exists($file);

		//ファイル削除
		if( !empty($exist_mx_domain_flg) ){
			Storage::disk('logs')->delete($file);
		}

		//顧客データインポート画面へリダイレクト
		return redirect(config('const.base_admin_url').config('const.admin_client_import_path'));
	}

	/*
	 * 
	 */
	public function deleteDuplicateEmail()
	{
		//ログファイル名取得
		$file = config('const.import_error_file_name');

		//ファイル存在チェック
		$exist_duplicate_flg = Storage::disk('logs')->exists($file);

		//ファイル削除
		if( !empty($exist_duplicate_flg) ){
			Storage::disk('logs')->delete($file);
		}

		//顧客データインポート画面へリダイレクト
		return redirect(config('const.base_admin_url').config('const.admin_client_import_path'));
	}

	//クライアント-ステータス変更画面
	public function statusSearchCount(Request $request)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User_group::query();
		
		//検索条件を追加後、結果件数を表示
		$db_search_count = $this->_getSearchOptionData($query, config('const.search_exec_type_count_key'));

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		$disp_data = [
			'session'			=> Session::all(),
			'status_list'		=> config('const.status_list'),
			'db_group_data'		=> $db_group_data,
			'db_search_count'	=> $db_search_count,
			'ver'				=> time(),
		];

		//ログ出力
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_status_change'].",{$user['login_id']}");

		return view('admin.client.status', $disp_data); 
	}
	
	//クライアント-ステータス変更画面
	public function statusSearchList(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//動的クエリを生成するため
		$query = User_group::query();

		//検索条件を追加後、結果データを表示
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_move_data_key'));		

		$listGroup = [];
		$group_data = Group::get();
		foreach($group_data as $lines){
			$listGroup[$lines->id] = $lines->name;
		}

		$disp_data = [
			'list_group'=> $listGroup,
			'db_data'	=> $db_data,
			'ver'		=> time(),
		];

		return view('admin.client.status_list', $disp_data); 
	}
	
	/*
	 * クライアント-ステータス変更画面-ポイント付与処理
	 */
	public function statusPointAdd(Request $request)
	{

		$this->validate($request, [
			'point'	 => 'required|check_add_point',
		]);
		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		try{

			//トランザクション内でSQLを実行
			//自動でロールバックされる
			//デッドロック発生時にdeadrock_max_retry回数再試行
			//最大再試行回数を過ぎたら例外エラー
			DB::transaction(function() use($user, $request){

				//動的クエリを生成するため
				$query = User_group::query();

				//SQL用のオブジェクトを生成し条件を取得
				$query = $this->_getSearchOptionData($query, config('const.search_exec_type_unexecuted_key'));

				//usersテーブルから更新対象のid,pointのリストを取得
				$listUpdateData = $query->get(['id','point']);

				foreach($listUpdateData as $lines){
					//リクエストのポイントを取得
					$add_point = $request->input('point');

					//ポイント計算
					$total_point = $lines->point + $add_point;

					//ポイント < 0ならpoint_logテーブル、usersテーブル、テキストログファイルのポイントを0にする
					if( $total_point <= 0 ){
						if( $lines->point > 0 ){
							$add_point = - $lines->point;
						}else{
							continue;
						}
					}

					//ポイント更新処理用の動的クエリを生成するため
					$query = User::query();

					//ポイント更新
					$query->where('id',$lines->id)->increment('point', $add_point);

					//更新後のポイント取得用の動的クエリを生成するため
					$query = User::query();

					//更新後のポイントを取得
					$db_data = $query->where('id',$lines->id)->get(['point'])->first();

					//ポイントログ履歴用の動的クエリを生成するため
					$log = new Point_log([
						'login_id'					=> $lines->id,
						'add_point'					=> $request->input('point'),
						'prev_point'				=> $lines->point,
						'current_point'				=> $db_data->point,
						'operator'					=> $user['login_id']
					]);

					//データをinsert
					$log->save();

					//ポイント加算
					if( preg_match("/^[0-9]+$/", $request->input('point')) > 0 ){			
						//ログ出力
						$this->log_obj->addLog(config('const.admin_display_list')['point_add'].",{$lines->id},{$request->input('point')},{$lines->point},{$db_data->point},{$user['login_id']}");
						$this->log_history_obj->addLog(config('const.admin_display_list')['point_add'].",{$user['login_id']}");

					//ポイント減算
					}else{
						//ログ出力
						$this->log_obj->addLog(config('const.admin_display_list')['point_sub'].",{$lines->id},{$request->input('point')},{$lines->point},{$db_data->point},{$user['login_id']}");
						$this->log_history_obj->addLog(config('const.admin_display_list')['point_sub'].",{$user['login_id']}");
					}
				}

			});

		} catch (\Exception $e) {
			//ログ出力
			$this->log_obj->addLog(config('const.admin_display_list')['point_except_err'].",{$user['login_id']}");
			
//			return response()->json(['point' => ['例外エラーが発生しました']]);
		}
	
		return null;
	}

	/*
	 * メルマガ履歴画面表示
	 */
	public function historyMelmaga($client_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//メルマガ履歴取得
		$db_data = DB::table('melmaga_history_logs')
				->join('melmaga_logs', 'melmaga_history_logs.melmaga_id', '=', 'melmaga_logs.id')
				->select('melmaga_history_logs.melmaga_id','melmaga_history_logs.client_id','melmaga_history_logs.updated_at','melmaga_history_logs.first_view_datetime','melmaga_history_logs.created_at','melmaga_logs.subject')
				->where('melmaga_history_logs.client_id', $client_id)->orderBy('melmaga_history_logs.melmaga_id', 'desc')->paginate(config('const.admin_client_list_limit'));

		//
		$disp_data = [
			'id'			=> $client_id,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'db_data'		=> $db_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.history_melmaga', $disp_data); 
	}

	/*
	 * アクセス履歴画面表示
	 */
	public function accessHistory($login_id)
	{		
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//アクセス履歴取得
		$db_data = DB::table('personal_access_logs')->where('login_id', $login_id)->orderBy('created_at', 'desc')->paginate(config('const.admin_client_list_limit'));

		//
		$disp_data = [
			'id'			=> $login_id,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'db_data'		=> $db_data,
			'ver'			=> time(),
		];
		
		return view('admin.client.history_personal', $disp_data); 
	}

	/*
	 * USER LISTの一括削除処理
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
		$this->log_history_obj->addLog(config('const.admin_display_list')['client_bulk_delete'].",{$user['login_id']}");	

		//削除処理
		foreach($listId as $index => $id){
			//$listDelIdが配列かつ削除IDがあれば
			if( is_array($listDelId) && in_array($id, $listDelId) ){
				//users/user_groupsテーブルから削除
				User::where('id', $id)->delete();
				User_group::where('client_id', $id)->delete();
				Registered_mail_queue::where('client_id', $id)->delete();
			}
		}
						
		return null;
	}
}

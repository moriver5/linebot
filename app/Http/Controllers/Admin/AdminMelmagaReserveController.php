<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Forecast;
use App\Model\Group;
use App\Model\Domain;
use App\Model\User;
use App\Model\User_group;
use App\Model\Melmaga_log;
use App\Model\Group_category;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminMelmagaReserveController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * メルマガ予約画面表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve'].",{$user['login_id']}");

		//動的クエリを生成するため
		$query = User_group::query();

		//payment_logsテーブルと結合
		$query->join('users', 'users.id', '=', 'user_groups.client_id');

		//メアドのみ取得
		$query->select(['users.id','users.mail_address']);

		//検索条件を追加後、データ取得
		list($db_data, $items) = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = 0;
		$list_data = [];

		//画面表示用配列
		$disp_data = [
			'forecast_category'		=> config('const.forecast_category'),
			'db_data'				=> $db_data,
			'total'					=> 0,
			'currentPage'			=> 1,
			'lastPage'				=> 1,
			'links'					=> '',
			'ver'					=> time()
		];

		return view('admin.melmaga.index_reserve', $disp_data);
	}

	/*
	 * 
	 */
	public function searchSetting()
	{
		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//DBのgroup_categoriesテーブルからデータ取得
		$db_category_data = Group_category::get();

		//画面表示用配列
		$disp_data = [
			'db_group_data'				=> $db_group_data,
			'db_category_data'			=> $db_category_data,
			'session'					=> Session::all(),
			'ver'						=> time(),
			'regist_status'				=> config('const.regist_status'),
			'melmaga_search_item'		=> config('const.melmaga_search_item'),
			'melmaga_search_type'		=> config('const.melmaga_search_type'),
			'melmaga_list_sex'			=> config('const.list_sex'),
			'melmaga_list_age'			=> config('const.list_age'),
		];

		return view('admin.melmaga.melmaga_reserve_search', $disp_data);
	}

	/*
	 * メルマガ-予約配信-配信先抽出
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = User_groups::query();

		//users
		$query->join('users', 'users.id', '=', 'user_groups.client_id');

		//検索条件を追加後、データ取得
		list($db_data, $items) = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = 0;
		if( !empty($db_data) ){
			$total = count($db_data);
		}

		$list_user = [];
		foreach($db_data as $lines){
			$list_user[] = $lines->id;
		}

		//除外グループ取得
		if( !is_null(Session::get('melmaga_exclusion_groups')) ){
			$exclusion_groups = User_group::select('client_id')->whereIn('user_groups.group_id', explode(",",Session::get('melmaga_exclusion_groups')))->distinct()->get();
			$list_exclusion_groups = [];
			foreach($exclusion_groups as $lines){
				$list_exclusion_user[] = $lines->client_id;
			}
			if( !empty($list_exclusion_user) ){
				$total = $total - count(array_intersect($list_user, $list_exclusion_user));
			}
		}

		//
		$disp_data = [
			'session'			=> Session::all(),
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];

		return view('admin.melmaga.index_reserve', $disp_data);
	}

	/*
	 * メルマガ-即時配信-配信先抽出
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User_group::query();

		//users
		$query->join('users', 'users.id', '=', 'user_groups.client_id');

		//メアドのみ取得
		$query->select(['users.id']);

		//検索条件を追加後、データ取得
		list($db_data, $items) = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = 0;
		if( !empty($db_data) ){
			$total = count($db_data);
		}

		$list_user = [];
		foreach($db_data as $lines){
			$list_user[] = $lines->id;
		}

		//除外グループ取得
		if( !is_null(Session::get('melmaga_exclusion_groups')) ){
			$exclusion_groups = User_group::select('client_id')->whereIn('user_groups.group_id', explode(",",Session::get('melmaga_exclusion_groups')))->distinct()->get();
			$list_exclusion_groups = [];
			$list_exclusion_user = [];
			foreach($exclusion_groups as $lines){
				$list_exclusion_user[] = $lines->client_id;
			}
			if( !empty($list_exclusion_user) ){
				$total = $total - count(array_intersect($list_user, $list_exclusion_user));
			}
		}

		//ドメインのリスト取得
		$db_domain = Domain::get();

		//送信元メールアドレスを生成してリスト化
		$list_from_mail = [];
		if( count($db_domain) > 0 ){
			foreach($db_domain as $lines){
				$list_from_mail[] = config('const.replay_to_mail').'@'.$lines->domain;
			}
		}

		$disp_data = [
			'list_from_mail'	=> $list_from_mail,
			'session'			=> Session::all(),
			'total'				=> $total,
			'ver'				=> time()
		];

		return view('admin.melmaga.index_reserve', $disp_data);
	}

	/*
	 * メルマガの予約配信
	 */
	public function sendReserveMelmaga(Request $request){
		$this->validate($request, [
			'from_name'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.from_name_length'),
			'from_mail'		=> 'bail|required|email|max:'.config('const.email_length'),
			'reserve_date'	=> 'bail|required|date_format_check|surrogate_pair_check|emoji_check',
			'subject'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'text_body'		=> 'bail|surrogate_pair_check|emoji_check',
			'html_body'		=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_send'].",{$user['login_id']}");

		//動的クエリを生成するため
		$query = User_group::query();

		//payment_logsテーブルと結合
		$query->join('users', 'users.id', '=', 'user_groups.client_id');

		//メアドのみ取得
		$query->select(['users.id','users.mail_address']);

		//メルマガ送信先のデータ取得
		list($db_data, $items) = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//実行クエリー取得
		$query = DB::getQueryLog();

		//現在時刻
		$now_date = Carbon::now();

		$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('reserve_date')).'00';

		$send_status = 0;
		if( $request->input('history_flg') != 1 ){
			$send_status = 4;
		}

		//メルマガログに送信情報を登録
		$melmaga_id = Melmaga_log::insertGetId([
			'send_status'			=> $send_status,							//送信状況:0(配信待ち)
			'send_count'			=> 0,										//送信数
			'reserve_send_date'		=> $request->input('reserve_date'),			//予約日時
			'sort_reserve_send_date'=> $sort_reserve_date,						//予約日時のyyyymmddhhmmss
			'from_name'				=> $request->input('from_name'),			//送信者
			'from_mail'				=> $request->input('from_mail'),			//送信元アドレス
			'subject'				=> $request->input('subject'),				//件名
			'text_body'				=> $request->input('text_body'),			//テキスト内容
			'html_body'				=> $request->input('html_body'),			//HTML内容
			'query'					=> $query[0]['query'],						//抽出SQL文
			'bindings'				=> implode(",", $query[0]['bindings']),		//抽出SQL文の値
			'items'					=> json_encode($items, JSON_PRETTY_PRINT ),	//抽出項目をJSON形式で保存
			'exclusion_groups'		=> Session::get('melmaga_exclusion_groups'),//除外グループIDを半角カンマでつなげた文字列
			'send_method'			=> $request->input('relay_server_flg'),		//リレーサーバーを使用
			'created_at'			=> $now_date,
			'updated_at'			=> $now_date
		]);

		return null;
	}

	/*
	 * メルマガ予約状況
	 */
	public function statusReserveMelmaga(){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_status'].",{$user['login_id']}");

		//配信ログ取得
		$db_data = Melmaga_log::query()->whereNotNull('reserve_send_date')->orderBy('sort_reserve_send_date', 'desc')->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'db_data'				=> $db_data,
			'cancel_redirect_url'	=> config('const.base_admin_url').'/'.config('const.reserve_status_url_path'),
			'ver'					=> time()
		];

		return view('admin.melmaga.melmaga_reserve_status', $disp_data);
	}

	/*
	 * メルマガ予約状況-キャンセル
	 */
	public function sendCancel($page, $id){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_cancel'].",{$user['login_id']}");

		$now_date = Carbon::now();

		Melmaga_log::where('id', $id)
				->update([
			'send_status'			=> 3,			//送信状況:3(キャンセル)
			'updated_at'			=> $now_date
		]);

		return null;
	}

	/*
	 * 予約状況から選択したメルマガの編集画面を表示
	 */
	public function editReserveMelmaga($page, $melmaga_id){
		//配信ログ取得
		$db_data = Melmaga_log::where('id', $melmaga_id)->first();

		$disp_data = [
			'melmaga_id'		=> $melmaga_id,
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.melmaga.melmaga_reserve_edit', $disp_data);
	}

	/*
	 * 予約状況から選択したメルマガの編集画面の更新処理
	 */
	public function sendEditReserveMelmaga(Request $request, $melmaga_id){
		$this->validate($request, [
			'from_name'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.from_name_length'),
			'from_mail'		=> 'bail|required|email',
			'reserve_date'	=> 'bail|required|date_format_check|surrogate_pair_check|emoji_check',
			'subject'		=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'text_body'		=> 'bail|surrogate_pair_check|emoji_check',
			'html_body'		=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_reserve_update'].",{$user['login_id']}");

		$now_date = Carbon::now();

		$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $request->input('reserve_date')).'00';

		$send_status = 0;
		if( $request->input('history_flg') != 1 ){
			$send_status = 4;
		}

		//メルマガログに送信情報の更新
		Melmaga_log::where('id', $melmaga_id)
				->update([
			'send_status'			=> $send_status,					//送信状況:0(配信待ち)
			'send_count'			=> 0,								//送信数
			'reserve_send_date'		=> $request->input('reserve_date'),	//予約日時
			'sort_reserve_send_date'=> $sort_reserve_date,				//予約日時の年月日
			'subject'				=> $request->input('subject'),		//件名
			'text_body'				=> $request->input('text_body'),	//テキスト内容
			'html_body'				=> $request->input('html_body'),	//HTML内容
			'updated_at'			=> $now_date
		]);

		return null;
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		$items = [];

		//usersテーブルのdm_statusが1(配信希望)
		$query->where('user_groups.status', 1);
		$query->where('user_groups.disable', 0);
		$query->where('users.send_flg', 1);
		$query->where('users.disable', 0);

		//検索項目
		if( !is_null(Session::get('melmaga_search_item_value')) ){
			$items['search_item'] = config('const.melmaga_search_item')[0][1];
			$items['search_value'] = Session::get('melmaga_search_item_value');

			//含む
			if( Session::get('melmaga_search_type') == config('const.melmaga_search_type')[0][0] ){
				$items['search_type'] = '含む';
				$query->whereIn(Session::get('melmaga_search_item'), explode(",",Session::get('melmaga_search_item_value')));

			//含まない
			}else{
				$items['search_type'] = '含まない';
				$query->whereNotIn(Session::get('melmaga_search_item'), explode(",",Session::get('melmaga_search_item_value')));
			}
		}

		//グループ
		if( !is_null(Session::get('melmaga_groups')) ){
			$items['group_id'] = Session::get('melmaga_groups');
			$query->whereIn('user_groups.group_id', explode(",",Session::get('melmaga_groups')));
		}

		//カテゴリ
		if( !is_null(Session::get('melmaga_category')) ){
			$items['category_id'] = Session::get('melmaga_groups');
			$query->whereIn('user_groups.category_id', explode(",",Session::get('melmaga_category')));
		}

		//登録状態
		if( !is_null(Session::get('melmaga_status')) ){
			$items['status'] = config('const.regist_status')[Session::get('melmaga_status')][1];
			$query->where('user_groups.status', config('const.regist_status')[Session::get('melmaga_status')][0]);
		}

		//性別
		if( !is_null(Session::get('melmaga_sex')) ){
			$items['sex'] = config('const.list_sex')[Session::get('melmaga_sex')];
			$query->where('user_groups.sex', Session::get('melmaga_sex'));
		}

		//年代
		if( !is_null(Session::get('melmaga_age')) ){
			$items['age'] = config('const.list_age')[Session::get('melmaga_age')];
			$query->where('user_groups.age', Session::get('melmaga_age'));
		}

		//登録日時-開始日
		if( !is_null(Session::get('melmaga_regist_sdate')) ){
			$items['start_regdate'] = Session::get('melmaga_regist_sdate');
			$query->where('user_groups.created_at', '>=', Session::get('melmaga_regist_sdate'));
		}

		//登録日時-終了日
		if( !is_null(Session::get('melmaga_regist_edate')) ){
			$items['end_regdate'] = Session::get('melmaga_regist_edate');
			$query->where('user_groups.created_at', '<=', Session::get('melmaga_regist_edate'));
		}

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->distinct()->count();

		//顧客データのエクスポート
		}elseif( $exec_type == config('const.search_exec_type_export_key') ){
			$db_data = $query->get();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$db_data = $query;

		//通常検索
		}else{
			DB::enableQueryLog();
			$db_data = $query->distinct()->get();
//			$db_data = $query->toSql();
		}

		return [$db_data, $items];
	}

	/*
	 * SQL文の条件保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('melmaga_search_item', $request->input('search_item'));
		}

		//検索項目の値
		Session::put('melmaga_search_item_value', $request->input('search_item_value'));

		//検索の含む・含まない
		Session::put('melmaga_search_type', $request->input('search_type'));

		//表示グループ
		Session::put('melmaga_groups', $request->input('groups'));

		//除外グループ
		Session::put('melmaga_exclusion_groups', $request->input('exclusion_groups'));

		//カテゴリ
		Session::put('melmaga_category', $request->input('category'));

		//登録状態
		Session::put('melmaga_status', $request->input('status'));

		//性別
		Session::put('melmaga_sex', $request->input('sex'));

		//年代
		Session::put('melmaga_age', $request->input('age'));

		//登録日時-開始日
		Session::put('melmaga_regist_sdate', $request->input('regist_sdate'));

		//登録日時-終了日
		Session::put('melmaga_regist_edate', $request->input('regist_edate'));
	}

}

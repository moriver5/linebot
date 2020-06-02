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
use App\Model\Melmaga_temp_immediate_mail;
use App\Model\Group_category;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminMelmagaController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * メルマガ即時配信トップ画面表示
	 */
	public function index(Request $request)
	{
		//動的クエリを生成するため
		$query = User_group::query();

		//users
		$query->leftJoin('users', 'users.id', '=', 'user_groups.client_id');

		//メアドのみ取得
		$query->select(['users.id','users.mail_address']);

		//検索条件を追加後、データ取得
		list($db_data, $items) = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = 0;
		$list_data = [];

		//画面表示用配列
		$disp_data = [
			'db_data'			=> $db_data,
			'total'				=> 0,
			'currentPage'		=> 1,
			'lastPage'			=> 1,
			'links'				=> '',

			'ver'				=> time()
		];

		return view('admin.melmaga.index', $disp_data);
	}

	/*
	 * 配信先設定画面表示
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

		return view('admin.melmaga.melmaga_search', $disp_data);
	}

	/*
	 * 配信先設定で検索後のページ遷移
	 */
	public function search(Request $request)
	{
		//動的クエリを生成するため
		$query = User_group::query();

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

		return view('admin.melmaga.index', $disp_data);
	}

	/*
	 * 配信先設定で検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//動的クエリを生成するため
		$query = User_group::query();

		//user_groupsテーブルと結合
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

		return view('admin.melmaga.index', $disp_data);
	}

	/*
	 * メルマガの即時配信
	 */
	public function sendImmediateMelmaga(Request $request){
		$this->validate($request, [
			'from_name'	=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.from_name_length'),
			'from_mail'	=> 'bail|required|email|max:'.config('const.email_length'),
			'subject'	=> 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
			'body'		=> 'bail|surrogate_pair_check|emoji_check',
			'html_body'	=> 'bail|surrogate_pair_check|emoji_check',
		]);

		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_send_immediate'].",{$user['login_id']}");

		//動的クエリを生成するため
		$query = User_group::query();

		//payment_logsテーブルと結合
		$query->join('users', 'users.id', '=', 'user_groups.client_id');

		//メアドのみ取得
		$query->select(['users.id','users.mail_address']);

		//メルマガ送信先のデータ取得
		list($db_data, $items) = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		//現在時刻を取得
		$now_date = Carbon::now();

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_reserve_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//確認アドレス宛に送信するかどうか
		$send_status = 0;
		if( $request->input('history_flg') != 1 ){
			$send_status = 4;
		}

		//履歴を残すフラグ
		$history_flg = $request->input('history_flg');
		if( empty($history_flg) ){
			$history_flg = 0;
		}

		//メルマガログに送信情報を登録
		$melmaga_id = Melmaga_log::insertGetId([
			'send_status'			=> $send_status,							//送信状況:0(配信待ち)
			'send_count'			=> 0,										//送信数
			'from_name'				=> $request->input('from_name'),			//送信者
			'from_mail'				=> $request->input('from_mail'),			//送信元アドレス
			'subject'				=> $request->input('subject'),				//件名
			'text_body'				=> $request->input('body'),					//テキスト内容
			'html_body'				=> $request->input('html_body'),			//HTML内容
			'items'					=> json_encode($items, JSON_PRETTY_PRINT),	//抽出項目をJSON形式で保存
			'send_method'			=> $request->input('relay_server_flg'),		//リレーサーバーを使用
			'sort_reserve_send_date'=> $sort_reserve_date,
			'created_at'			=> $now_date,
			'updated_at'			=> $now_date
		]);
//error_log($request->input('relay_server_flg')."::dddd\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
//exit;
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
			$list_user = array_diff($list_user, $list_exclusion_user);
		}

		//即時メルマガ配信先のクライアントIDを登録
		foreach($list_user as $client_id){
			$melmaga_mails = new Melmaga_temp_immediate_mail([
				'melmaga_id'	=> $melmaga_id,
				'client_id'		=> $client_id,
				'created_at'	=> $now_date,
				'updated_at'	=> $now_date
			]);

			//DB保存
			$melmaga_mails->save();
		}

//error_log("{$melmaga_id} {$send_status} {$history_flg}\n",3,"/data/www/melmaga/storage/logs/nishi_log.txt");
		//バックグラウンドでアクセス一覧のユーザーにメール配信
		$process = new Process(config('const.artisan_command_path')." melmaga:delivery {$melmaga_id} {$send_status} {$history_flg} > /dev/null");

		//非同期実行
		$process->start();

		//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
		//1秒待機
		usleep(1000000);

		return null;
	}

	/*
	 * メルマガ送信失敗-再配信
	 */
	public function sendFailedMelmaga(Request $request){
		$melmaga_id = $request->input('melmaga_id');

		Melmaga_log::where('id', $melmaga_id)
				->update([
			'send_status' => 0,			//送信状況:0(配信待ち)
		]);

		//バックグラウンドでアクセス一覧のユーザーにメール配信
		$process = new Process(config('const.artisan_command_path')." melmaga:delivery {$melmaga_id} 0 0 > /dev/null");

		//非同期実行
		$process->start();

		//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
		//1秒待機
		usleep(1000000);
	}

	/*
	 * 
	 */
	public function bulkDeleteSend(Request $request){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//削除ID取得
		$listDelId = $request->input('del');
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['client_bulk_delete'].",{$user['login_id']}");	

		if( !empty($listDelId) ){
			//削除処理
			foreach($listDelId as $id){
				//melmaga_temp_immediate_mailsテーブルから削除
				Melmaga_temp_immediate_mail::where('melmaga_id', $id)->delete();
			}
		}
						
		return null;
	}

	/*
	 * メルマガ配信履歴
	 */
	public function historySendMelmaga(){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_history'].",{$user['login_id']}");

		//配信ログ取得(send_status:4は履歴を残さない)
		$db_data = Melmaga_log::query()->whereNotIn('send_status', [4, 5])->orderBy('sort_reserve_send_date' , 'desc')->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.melmaga.melmaga_history', $disp_data);
	}

	/*
	 * メルマガ配信履歴-配信リスト
	 */
	public function listHistorySendMelmaga($melmaga_id)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_history_list'].",{$user['login_id']}");

		//メルマガ未閲覧データ取得
//		$access_data = DB::table("melmaga_history_logs")->select("client_id", "read_flg")->where('melmaga_id', $melmaga_id)->orderBy("sort_date")->paginate(config('const.admin_client_list_limit'));
		$access_data = DB::table("melmaga_history_logs")
			->select("melmaga_history_logs.client_id", "melmaga_history_logs.read_flg", "melmaga_temp_immediate_mails.client_id as not_send_id")
			->leftJoin("melmaga_temp_immediate_mails", function($join){
				$join->on("melmaga_history_logs.melmaga_id", "=", "melmaga_temp_immediate_mails.melmaga_id");
				$join->on("melmaga_history_logs.client_id", "=", "melmaga_temp_immediate_mails.client_id");
			})
			->where('melmaga_history_logs.melmaga_id', $melmaga_id)
			->orderBy("melmaga_history_logs.sort_date")
			->paginate(config('const.admin_client_list_limit'));
/*
		$listMelmaga = [];
		foreach($access_data as $lines){
			$listMelmaga[$melmaga_id][] = $lines->client_id;
		}
*/
		$disp_data = [
			'total'			=> $access_data->total(),
			'currentPage'	=> $access_data->currentPage(),
			'lastPage'		=> $access_data->lastPage(),
			'links'			=> $access_data->links(),
			'db_data'		=> $access_data,
			'melmaga_id'	=> $melmaga_id,
			'ver'			=> time(),
		];
		
		return view('admin.melmaga.melmaga_history_list', $disp_data);
	}

	/*
	 * 配信履歴から選択したメルマガを確認
	 */
	public function viewHistorySendMelmaga($page, $send_id, $client_id = null){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_history_view'].",{$user['login_id']}");

		//DBのgroupテーブルからデータ取得
		$db_group_data = Group::get();

		//配信ログ取得
		$db_data = Melmaga_log::where('id', $send_id)->first();

		//javascriptの開始終了タグをエスケープする
		$db_data->html_body = Utility::escapeJsTag($db_data->html_body);
		$db_data->text_body = Utility::escapeJsTag($db_data->text_body);

		$disp_data = [
			'groups'			=> $db_group_data,
			'db_data'			=> $db_data,
			'items'				=> json_decode($db_data->items),
			'melmaga_list_sex'			=> config('const.list_sex'),
			'melmaga_list_age'			=> config('const.list_age'),
			'ver'				=> time()
		];

		if( !is_null($client_id) ){
			$user_db_data = User::where('id', $client_id)->first();
			if( empty($user_db_data) ){
				return view('admin.client.personal_empty'); 
			}
			$disp_data['email'] = $user_db_data->mail_address;
		}

		return view('admin.melmaga.view_melmaga_history', $disp_data);
	}

	/*
	 * メルマガ配信失敗一覧
	 */
	public function failedSendMelmaga(){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_failed'].",{$user['login_id']}");

		//メルマガ配信失敗データ取得
		$query = Melmaga_temp_immediate_mail::query();
		$query->leftJoin('melmaga_logs', 'melmaga_temp_immediate_mails.melmaga_id', '=', 'melmaga_logs.id');
		$db_data = $query->select('melmaga_logs.id', 'melmaga_logs.send_date', DB::raw('count(melmaga_temp_immediate_mails.melmaga_id) as count'))
			->groupBy('melmaga_temp_immediate_mails.melmaga_id')
			->orderBy('melmaga_logs.send_date', 'desc')
			->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.melmaga.failed_send_melmaga', $disp_data);
	}

	/*
	 * メルマガ配信失敗一覧-失敗リスト画面表示
	 */
	public function listFailedSendMelmaga($page, $melmaga_id){
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['melmaga_failed_list'].",{$user['login_id']}");

		//メルマガ配信失敗データ取得
		$query = User::query();
		$query->join('melmaga_temp_immediate_mails', 'melmaga_temp_immediate_mails.client_id', '=', 'users.id');
		$db_data = $query->select('users.id', 'users.mail_address')
			->where('melmaga_temp_immediate_mails.melmaga_id', $melmaga_id)
			->paginate(config('const.admin_client_list_limit'));

		$disp_data = [
			'db_data'			=> $db_data,
			'ver'				=> time()
		];

		return view('admin.melmaga.failed_send_emails', $disp_data);
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
			$db_data = $query->distinct()->get();
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

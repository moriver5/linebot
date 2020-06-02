<?php

namespace App\Http\Controllers\Agency;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Result_ad_log;
use App\Model\Ad_access_log;
use App\Model\LineUser;
use App\Model\LineOfficialAccount;
use Utility;
use Session;
use DB;
use Carbon\Carbon;

class AgencyController extends Controller
{
	public function __construct()
	{

	}

	/*
	 * 代理店管理画面のログイン後に表示される画面
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//デフォルトの年度
//		if( is_null(Session::get('ad_start_date')) ){
			$start_date = date('Ym01');
//		}else{
//			$start_date = preg_replace("/\//", "", Session::get('ad_start_date')).'01';
//		}
//		if( is_null(Session::get('ad_end_date')) ){
			$end_date = date('Y/m/d');
			list($year, $month) = explode("/", $end_date);
			$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
			$end_date = preg_replace("/\//", "", $end_date).$last_day;
//		}else{
//			list($year, $month) = explode("/", Session::get('ad_start_date'));
//			$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
//			$end_date = preg_replace("/\//", "", Session::get('ad_end_date')).$last_day;			
//		}

		//チャンネルごとで指定期間のPV、登録数取得
		$db_data = Ad_access_log::query()
			->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
			->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->where('ad_access_logs.access_date', '>=', $start_date)
			->where('ad_access_logs.access_date', '<=', $end_date)
			->where('ad_codes.agency_id', '=', $agency['agency_id'])
			->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id', 'ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
			->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd')
			->paginate(config('const.admin_client_list_limit'));

		//検索条件を追加後、データ取得
//		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$listData = [];
		$listTotal = [
			'pv'		 => 0,
			'reg'		 => 0,
			'unfollow'	 => 0,
		];

		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
//				$sql = "select count(*) as unfollow from line_users where line_basic_id = '".$lines->line_basic_id."' and follow_flg = 0 and ad_cd = '".$lines->ad_cd."' and access_date >= ".$start_date." and access_date <= ".$end_date." and DATEDIFF(updated_at,created_at) < 1 ";
				$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and block24h = 1 and ad_cd = '".$lines->ad_cd."' and access_date >= ".$start_date." and access_date <= ".$end_date;
				
/*
				//チャンネルごと指定期間のブロック数取得
				$db_users = LineUser::query()
					->where('access_date', '>=', $start_date)
					->where('access_date', '<=', $end_date)
					->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();
*/
				$db_users = DB::select($sql);

				if( empty($db_users[0]->unfollow24) ){
					$unfollow24 = 0;
				}else{
					$unfollow24 = $db_users[0]->unfollow24;
				}

				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;
				$listTotal['unfollow'] += $unfollow24;

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow'	=> $unfollow24
				];
			}
		}

		//画面表示用配列
		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'db_data'			=> $listData,
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'list_total'		=> $listTotal,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * バックアップとして残す
	 */
	public function index2(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//デフォルトの年度
//		if( is_null(Session::get('ad_start_date')) ){
			$start_date = date('Ymd');
//		}else{
//			$start_date = preg_replace("/\//", "", Session::get('ad_start_date')).'01';
//		}
//		if( is_null(Session::get('ad_end_date')) ){
			$end_date = date('Y/m/d');
			list($year, $month) = explode("/", $end_date);
			$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
			$end_date = preg_replace("/\//", "", $end_date).$last_day;
//		}else{
//			list($year, $month) = explode("/", Session::get('ad_start_date'));
//			$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
//			$end_date = preg_replace("/\//", "", Session::get('ad_end_date')).$last_day;			
//		}

		//チャンネルごとで指定期間のPV、登録数取得
		$db_data = Ad_access_log::query()
			->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
			->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->where('ad_access_logs.access_date', '>=', $start_date)
			->where('ad_access_logs.access_date', '<=', $end_date)
			->where('ad_codes.agency_id', '=', $agency['agency_id'])
			->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id', 'ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
			->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd')
			->paginate(config('const.admin_client_list_limit'));

		//検索条件を追加後、データ取得
//		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$listData = [];
		$listTotal = [
			'pv'		 => 0,
			'reg'		 => 0,
			'unfollow'	 => 0,
		];

		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				//チャンネルごと指定期間のブロック数取得
				$db_users = LineUser::query()
					->where('access_date', '>=', $start_date)
					->where('access_date', '<=', $end_date)
					->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();

				if( empty($db_users->unfollow) ){
					$unfollow = 0;
				}else{
					$unfollow = $db_users->unfollow;
				}

				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;
				$listTotal['unfollow'] += $unfollow;

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow'	=> $unfollow
				];
			}
		}

		//画面表示用配列
		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'db_data'			=> $listData,
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'list_total'		=> $listTotal,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * 検索ボタンを押下後の検索処理と検索結果を表示
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//合計
		if( empty(Session::get('media_disp_type')) ){
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('ad_codes.agency_id', '=', $agency['agency_id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');
		//日毎
		}else{
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('ad_codes.agency_id', '=', $agency['agency_id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$listData = [];
		$listTotal = [
			'pv'		 => 0,
			'reg'		 => 0,
			'unfollow'	 => 0,
		];

		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
//				$sql = "select count(*) as unfollow from line_users where line_basic_id = '".$lines->line_basic_id."' and follow_flg = 0 and ad_cd = '".$lines->ad_cd."' and DATEDIFF(updated_at,created_at) < 1 ";
				$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and block24h = 1 and ad_cd = '".$lines->ad_cd."'";

				//チャンネルごと指定期間のブロック数取得
//				$query = LineUser::query();
				if( !empty(Session::get('ad_start_date')) ){
					$sql .= "and access_date >= ".preg_replace("/\//", "", Session::get('ad_start_date')).'01 ';
//					$query->where('access_date', '>=', preg_replace("/\//", "", Session::get('ad_start_date')).'01');
				}
				if( !empty(Session::get('ad_end_date')) ){
					list($year, $month) = explode("/", Session::get('ad_end_date'));
					$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
					$sql .= "and access_date <= ".preg_replace("/\//", "", Session::get('ad_end_date')).$last_day;
//					$query->where('access_date', '<=', preg_replace("/\//", "", Session::get('ad_end_date')).$last_day);
				}

				$db_users = DB::select($sql);

/*
				//チャンネルごと指定期間のブロック数取得
				$db_users = $query->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();
*/
				if( empty($db_users[0]->unfollow24) ){
					$unfollow24 = 0;
				}else{
					$unfollow24 = $db_users[0]->unfollow24;
				}

				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;
				$listTotal['unfollow'] += $unfollow24;

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow'	=> $unfollow24
				];
			}
		}

		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'db_data'			=> $listData,
			'list_total'		=> $listTotal,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * 検索処理後のページャーのリンクを押下たときに呼び出される
	 */
	public function search(Request $request)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//合計
		if( empty(Session::get('media_disp_type')) ){
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('ad_codes.agency_id', '=', $agency['agency_id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');
		//日毎
		}else{
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('ad_codes.agency_id', '=', $agency['agency_id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$listData = [];
		$listTotal = [
			'pv'		 => 0,
			'reg'		 => 0,
			'unfollow'	 => 0,
		];

		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
//				$sql = "select count(*) as unfollow from line_users where line_basic_id = '".$lines->line_basic_id."' and follow_flg = 0 and ad_cd = '".$lines->ad_cd."' and DATEDIFF(updated_at,created_at) < 1 ";
				$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and block24h = 1 and ad_cd = '".$lines->ad_cd."'";

				//チャンネルごと指定期間のブロック数取得
				$query = LineUser::query();
				if( !empty(Session::get('ad_start_date')) ){
					$sql .= "and access_date >= ".preg_replace("/\//", "", Session::get('ad_start_date')).'01 ';
//					$query->where('access_date', '>=', preg_replace("/\//", "", Session::get('ad_start_date')).'01');
				}
				if( !empty(Session::get('ad_end_date')) ){
					list($year, $month) = explode("/", Session::get('ad_end_date'));
					$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
					$sql .= "and access_date <= ".preg_replace("/\//", "", Session::get('ad_end_date')).$last_day;
//					$query->where('access_date', '<=', preg_replace("/\//", "", Session::get('ad_end_date')).$last_day);
				}

				$db_users = DB::select($sql);
/*
				//チャンネルごと指定期間のブロック数取得
				$db_users = $query->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();
*/
				if( empty($db_users[0]->unfollow24) ){
					$unfollow24 = 0;
				}else{
					$unfollow24 = $db_users[0]->unfollow24;
				}

				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;
				$listTotal['unfollow'] += $unfollow24;

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow'	=> $unfollow24
				];
			}
		}

		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'search_item'		=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'db_data'			=> $listData,
			'list_total'		=> $listTotal,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'ver'				=> time()
		];
		
		return view('agency.index', $disp_data);
	}

	/*
	 * 検索パラメータをsessionに保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//検索タイプ
		if( !is_null($request->input('search_type')) ){
			Session::put('ad_search_type', $request->input('search_type'));
		}else{
			Session::forget('ad_search_type');
		}

		//検索項目
		if( !is_null($request->input('search_item')) ){
			Session::put('ad_search_item', $request->input('search_item'));
		}else{
			//検索項目が未入力なら破棄
			Session::forget('ad_search_item');
		}
		
		//LIKE検索
		if( !is_null($request->input('search_like_type')) ){
			Session::put('ad_search_like_type', $request->input('search_like_type'));
		}else{
//			Session::forget('ad_search_like_type');
			Session::put('ad_search_like_type', 0);
		}

		//集計-開始
		if( !empty($request->input('start_date')) ){
			Session::put('ad_start_date', $request->input('start_date'));
		}else{
			Session::put('ad_start_date', date('Y').'/'.date('m'));
		}

		//集計-終了
		if( !empty($request->input('end_date')) ){
			Session::put('ad_end_date', $request->input('end_date'));
		}else{
			Session::put('ad_end_date', date('Y').'/'.date('m'));
		}

	}

	/*
	 * 検索処理のパラメータをSQLの条件に設定
	 */
	private function _getSearchOptionData($query)
	{
		//検索項目
		if( !is_null(Session::get('ad_search_item')) ){
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listSearchLikeType = config('const.search_like_type');
				$listItem = explode(",", Session::get('ad_search_item'));
				foreach($listItem as $index => $item){
					if( Session::get('ad_search_like_type') != "" ){
						$query->orWhere(Session::get('ad_search_type'), $listSearchLikeType[Session::get('ad_search_like_type')][0], sprintf($listSearchLikeType[Session::get('ad_search_like_type')][1], $item ));
					}
				}
			});
		}

		$dt = Carbon::now();

		//集計-開始
		if( !empty(Session::get('ad_start_date')) ){
			$query->where('ad_access_logs.access_date', '>=', preg_replace("/\//", "", Session::get('ad_start_date')).'01');
		}else{
			$query->where('ad_access_logs.access_date', '>=', sprintf("%04d%02d01", $dt->year, $dt->month));
		}

		//集計-終了
		if( !empty(Session::get('ad_end_date')) ){
			list($year, $month) = explode("/", Session::get('ad_end_date'));
			$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
			$query->where('ad_access_logs.access_date', '<=', preg_replace("/\//", "", Session::get('ad_end_date')).$last_day);
		}else{
			$last_day = date('t', mktime(0, 0, 0, $dt->month, 1, $dt->year));
			$query->where('ad_access_logs.access_date', '<=', sprintf("%04d%02d%02d", $dt->year, $dt->month, $last_day));
		}

		$db_data = $query->paginate(config('const.admin_client_list_limit'));

		return $db_data;
	}

	/*
	 * 検索結果のdetailリンクを押下後の結果表示
	 * detail画面での集計結果ボタン押下後の検索処理と結果表示
	 */
	public function aggregateMonth(Request $request, $basic_id, $ad_cd)
	{
		//ログイン管理者情報取得
		$agency = Utility::getAgencyDefaultDispParam();

		//検索条件をセッションに保存
//		$this->_saveSearchOption($request);

		if( empty(Session::get('ad_start_date')) ){
			$start_date = date('Ym01');
		}else{
			$start_date = preg_replace("/\//", "", Session::get('ad_start_date')).'01';
		}

		if( empty(Session::get('ad_end_date')) ){
			$end_date = date('Y/m');
		}else{
			$end_date = Session::get('ad_end_date');
		}

		list($year, $month) = explode("/", $end_date);
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$db_data = Ad_access_log::query()
			->join('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
			->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id', 'ad_access_logs.access_date', 'ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
			->where('ad_codes.agency_id', $agency['agency_id'])
			->where('ad_codes.line_basic_id', $basic_id)
			->where('ad_access_logs.ad_cd', $ad_cd)
			->where('ad_access_logs.access_date', '>=', $start_date)
			->where('ad_access_logs.access_date', '<=', preg_replace("/\//", "", $end_date).$last_day)
			->groupBy('ad_access_logs.access_date','ad_access_logs.line_basic_id','ad_access_logs.ad_cd')
			->paginate(config('const.admin_client_list_limit'));

		$listData = [];
		$listTotal = [
			'pv'		 => 0,
			'reg'		 => 0,
			'unfollow'	 => 0,
		];

		$diff = (strtotime($end_date.'/'.$last_day) - strtotime($start_date)) / ( 60 * 60 * 24);
		for($i = 0; $i <= $diff; $i++) {
			setlocale(LC_ALL, 'ja_JP.UTF-8');

			$date = date('Y-m-d', strtotime($start_date . '+' . $i . 'days'));

//			$sql = "select count(*) as unfollow from line_users where line_basic_id = '".$basic_id."' and follow_flg = 0 and ad_cd = '".$ad_cd."' and access_date = ".preg_replace("/\-/", "", $date)." and DATEDIFF(updated_at,created_at) < 1 ";
			$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$basic_id."' and block24h = 1 and ad_cd = '".$ad_cd."' and access_date = ".preg_replace("/\-/", "", $date);

/*
			//チャンネルごと指定期間のブロック数取得
			$db_users = LineUser::where('line_basic_id', $basic_id)
				->where('access_date', '=', preg_replace("/\-/", "", $date))
				->where('ad_cd', $ad_cd)
				->where('follow_flg', 0)
				->select(DB::raw("count(*) as unfollow"))
				->first();
*/
			$db_users = DB::select($sql);

			if( empty($db_users[0]->unfollow24) ){
				$unfollow24 = 0;
			}else{
				$unfollow24 = $db_users[0]->unfollow24;
			}

			$listTotal['unfollow'] += $unfollow24;

			list($year, $mon, $day) = explode("-", $date);

			$listData[$year.$mon.$day] = [
				'channel'	=> '', 
				'basic_id'	=> $basic_id, 
				'ad_id'		=> '',
				'ad_cd'		=> $ad_cd, 
				'agency_id'	=> $agency['agency_id'], 
				'date'		=> $year.'/'.sprintf("%d", $mon).'/'.sprintf("%d", $day).' '.Carbon::create($year, $mon, $day)->formatLocalized('(%a)'),
				'name'		=> '', 
				'pv'		=> 0, 
				'reg'		=> 0, 
				'unfollow'	=> $unfollow24
			];
		}

		if( count($db_data) > 0 ){
			setlocale(LC_ALL, 'ja_JP.UTF-8');
			foreach($db_data as $lines){
				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;

				$listData[$lines->access_date]['channel'] = $lines->channel;
				$listData[$lines->access_date]['ad_id'] = $lines->id;
				$listData[$lines->access_date]['name'] = $lines->name;
				$listData[$lines->access_date]['pv'] = $lines->pv;
				$listData[$lines->access_date]['reg'] = $lines->reg;
			}
		}

		$db_line_account = LineOfficialAccount::where('line_basic_id', $basic_id)->first();

		$disp_data = [
			'agency'			=> $agency['name'],
			'session'			=> Session::all(),
			'basic_id'			=> $basic_id,
			'name'				=> $db_line_account->name,
			'ad_cd'				=> $ad_cd,
			'db_data'			=> $listData,
			'list_total'		=> $listTotal,
			'ver'				=> time()
		];

		return view('agency.index_daily', $disp_data);
	}

}

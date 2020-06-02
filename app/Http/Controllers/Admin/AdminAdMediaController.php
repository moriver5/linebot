<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Group;
use App\Model\User;
use App\Model\Result_ad_log;
use App\Model\Ad_access_log;
use App\Model\TrackAccessLog;
use App\Model\AdminChannelAllowList;
use App\Model\LineUser;
use Auth;
use Carbon\Carbon;
use Session;
use Utility;
use DB;

class AdminAdMediaController extends Controller
{
	private $log_obj;

	public function __construct()
	{
		//ログファイルのインスタンス生成
		//引数：ログの操作項目、ログファイルまでのフルパス
		$this->log_obj	 = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}

	/*
	 * 媒体集計画面表示
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['media_top'].",{$user['login_id']}");

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//デフォルトの年度
		$start_date = date('Ymd');
		$end_date = date('Ymd');

		//チャンネルごとで指定期間のPV、登録数取得
		$db_data = Ad_access_log::query()
			->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->leftJoin('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
			->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->where('ad_access_logs.access_date', '>=', $start_date)
			->where('ad_access_logs.access_date', '<=', $end_date)
			->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
			->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id', 'ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
			->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd')
			->paginate(config('const.admin_client_list_limit'));

		$listData = [];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
//				$sql = "select count(*) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and follow_flg = 0 and ad_cd = '".$lines->ad_cd."' and access_date >= ".$start_date." and access_date <= ".$end_date." and DATEDIFF(updated_at,created_at) < 1";
				$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and block24h = 1 and ad_cd = '".$lines->ad_cd."' and access_date >= ".$start_date." and access_date <= ".$end_date;

				//チャンネルごと指定期間のブロック数取得
				$db_users = LineUser::query()
					->where('access_date', '>=', $start_date)
					->where('access_date', '<=', $end_date)
					->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();

				$db_users24 = DB::select($sql);

				if( empty($db_users24[0]->unfollow24) ){
					$unfollow24 = 0;
				}else{
					$unfollow24 = $db_users24[0]->unfollow24;
				}

				if( empty($db_users->unfollow) ){
					$unfollow = 0;
				}else{
					$unfollow = $db_users->unfollow;
				}

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow24'=> $unfollow24,
					'unfollow'	=> $unfollow
				];
			}
		}

		//画面表示用配列
		$disp_data = [
			'db_data'			=> $listData,
			'total'				=> $db_data->total(),
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'list_channel'		=> $db_allow_channel,
			'start_date'		=> preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $start_date),
			'end_date'			=> preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $end_date),
			'ad_search_item'	=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'ad_search_disp_num'=> config('const.search_disp_num'),
			'ad_category'		=> config('const.ad_category'),
			'ver'				=> time()
		];

		return view('admin.ad.media.index', $disp_data);
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
			'ad_search_item'				=> config('const.ad_media_search_item'),
			'search_like_type'				=> config('const.search_like_type'),
			'ad_search_disp_num'			=> config('const.search_disp_num'),
			'ad_category'					=> config('const.ad_category'),
		];

		return view('admin.ad.media.search_setting', $disp_data);
	}

	/*
	 * 検索結果のページャーのリンクから呼び出される
	 */
	public function search(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//合計
		if( empty(Session::get('media_disp_type')) ){
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->leftJoin('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');
		//日毎
		}else{
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->leftJoin('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');
		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$listData = [];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
//				$sql = "select count(*) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and follow_flg = 0 and ad_cd = '".$lines->ad_cd."' and DATEDIFF(updated_at,created_at) < 1";
				$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and block24h = 1 and ad_cd = '".$lines->ad_cd."'";

				//チャンネルごと指定期間のブロック数取得
				$query = LineUser::query();
				if( !empty(Session::get('media_start_date')) ){
					$sql .= " and access_date >= ".preg_replace("/\//", "", Session::get('media_start_date'));
					$query->where('access_date', '>=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_start_date')));
				}
				if( !empty(Session::get('media_end_date')) ){
					$sql .= " and access_date <= ".preg_replace("/\//", "", Session::get('media_end_date'));
					$query->where('access_date', '<=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_end_date')));
				}

				//チャンネルごと指定期間のブロック数取得
				$db_users = $query->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();

				$db_users24 = DB::select($sql);

				if( empty($db_users24[0]->unfollow24) ){
					$unfollow24 = 0;
				}else{
					$unfollow24 = $db_users24[0]->unfollow24;
				}

				if( empty($db_users->unfollow) ){
					$unfollow = 0;
				}else{
					$unfollow = $db_users->unfollow;
				}

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg,
					'unfollow24'=> $unfollow24,
					'unfollow'	=> $unfollow
				];
			}
		}

		if( !empty(Session::get('media_start_date')) ){
			$start_date = Session::get('media_start_date');
		}else{
			$start_date = date('Ymd');
		}
		if( !empty(Session::get('media_end_date')) ){
			$end_date = Session::get('media_end_date');			
		}else{
			$end_date = date('Ymd');			
		}

		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $listData,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'list_channel'		=> $db_allow_channel,
			'start_date'		=> preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $start_date),
			'end_date'			=> preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $end_date),
			'ad_search_item'	=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'ad_search_disp_num'=> config('const.search_disp_num'),
			'ad_category'		=> config('const.ad_category'),
			'ver'				=> time()
		];

		//合計
		if( empty(Session::get('media_disp_type')) ){
			return view('admin.ad.media.index', $disp_data);

		//日毎
		}else{
			return view('admin.ad.media.index_daily', $disp_data);			
		}

	}

	/*
	 * 検索画面からの検索処理
	 */
	public function searchPost(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['media_search'].",{$user['login_id']}");

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//合計
		if( empty(Session::get('media_disp_type')) ){
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->leftJoin('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');

		//日毎
		}else{
			//チャンネルごとで指定期間のPV、登録数取得
			$query = Ad_access_log::query()
				->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->leftJoin('ad_codes', 'ad_access_logs.ad_cd', '=', 'ad_codes.ad_cd')
				->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
				->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
				->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id','ad_access_logs.ad_cd', 'ad_codes.name', 'ad_codes.id', 'ad_codes.agency_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
				->groupBy('ad_access_logs.line_basic_id','ad_access_logs.ad_cd');

		}

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query, config('const.search_exec_type_data_key'));

		$total = $db_data->total();

		$listData = [];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
//				$sql = "select count(*) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and follow_flg = 0 and ad_cd = '".$lines->ad_cd."' and DATEDIFF(updated_at,created_at) <= 1";
				$sql = "select count(block24h) as unfollow24 from line_users where line_basic_id = '".$lines->line_basic_id."' and block24h = 1 and ad_cd = '".$lines->ad_cd."'";

				$query = LineUser::query();
				if( !empty(Session::get('media_start_date')) ){
					$sql .= " and access_date >= ".preg_replace("/\//", "", Session::get('media_start_date'));
					$query->where('access_date', '>=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_start_date')));
				}
				if( !empty(Session::get('media_end_date')) ){
					$sql .= " and access_date <= ".preg_replace("/\//", "", Session::get('media_end_date'));
					$query->where('access_date', '<=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_end_date')));
				}

				//チャンネルごと指定期間のブロック数取得
				$db_users = $query->where('line_basic_id', $lines->line_basic_id)
					->where('ad_cd', $lines->ad_cd)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();

				$db_users24 = DB::select($sql);

				if( empty($db_users24[0]->unfollow24) ){
					$unfollow24 = 0;
				}else{
					$unfollow24 = $db_users24[0]->unfollow24;
				}

				if( empty($db_users->unfollow) ){
					$unfollow = 0;
				}else{
					$unfollow = $db_users->unfollow;
				}

				$listData[] = [
					'channel'	=> $lines->channel, 
					'basic_id'	=> $lines->line_basic_id, 
					'ad_id'		=> $lines->id,
					'ad_cd'		=> $lines->ad_cd, 
					'agency_id'	=> $lines->agency_id, 
					'name'		=> $lines->name, 
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow24'=> $unfollow24,
					'unfollow'	=> $unfollow
				];
			}
		}

		if( !empty(Session::get('media_start_date')) ){
			$start_date = Session::get('media_start_date');
		}else{
			$start_date = date('Ymd');
		}
		if( !empty(Session::get('media_end_date')) ){
			$end_date = Session::get('media_end_date');			
		}else{
			$end_date = date('Ymd');			
		}

		$disp_data = [
			'session'			=> Session::all(),
			'db_data'			=> $listData,
			'total'				=> $total,
			'currentPage'		=> $db_data->currentPage(),
			'lastPage'			=> $db_data->lastPage(),
			'links'				=> $db_data->links(),
			'list_channel'		=> $db_allow_channel,
			'start_date'		=> preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $start_date),
			'end_date'			=> preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1/$2/$3", $end_date),
			'ad_search_item'	=> config('const.ad_media_search_item'),
			'search_like_type'	=> config('const.search_like_type'),
			'ad_search_disp_num'=> config('const.search_disp_num'),
			'ad_category'		=> config('const.ad_category'),
			'ver'				=> time()
		];

		//合計
		if( empty(Session::get('media_disp_type')) ){
			return view('admin.ad.media.index', $disp_data);

		//日毎
		}else{
//			return view('admin.ad.media.index_daily', $disp_data);			
			return view('admin.ad.media.index', $disp_data);
		}
	}

	/*
	 * SQL文の条件設定
	 */
	private function _getSearchOptionData($query, $exec_type = '')
	{
		//LINEチャンネル
		if( !is_null(Session::get('media_line_channel')) ){
			$query->whereIn('ad_access_logs.line_basic_id', Session::get('media_line_channel'));
		}

		//広告コード
		if( !is_null(Session::get('media_search_item_value')) ){
			//$query->where(function($query){SQL条件})
			//この中で条件を書くとカッコでくくられる。
			//例：(client_id=1 or client_id=2 or client_id=3)
			$query->where(function($query){
				$listItem = explode(",", Session::get('media_search_item_value'));
				$listSearchLikeType = config('const.search_like_type');
				foreach($listItem as $index => $item){
					//99kのときad_access_logsテーブルから検索
					if( $item == '99k' ){
						$query->orWhere('ad_access_logs.ad_cd', $listSearchLikeType[Session::get('media_search_like_type')][0], sprintf($listSearchLikeType[Session::get('media_search_like_type')][1], $item ));						
					}else{
						$query->orWhere(Session::get('media_search_item'), $listSearchLikeType[Session::get('media_search_like_type')][0], sprintf($listSearchLikeType[Session::get('media_search_like_type')][1], $item ));
					}
				}
			});
		}

		//期間-開始
		if( !empty(Session::get('media_start_date')) ){
			$query->where('ad_access_logs.access_date', '>=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_start_date')));
		}

		//期間-終了
		if( !empty(Session::get('media_end_date')) ){
			$query->where('ad_access_logs.access_date', '<=', preg_replace("/(\d{4})\/(\d{2})\/(\d{2})/", "$1$2$3", Session::get('media_end_date')));
		}

		//媒体種別
		if( !empty(Session::get('media_category')) ){
			$query->whereIn('ad_codes.category', Session::get('media_category'));
		}

		//通常検索の結果件数
		if( $exec_type == config('const.search_exec_type_count_key') ){
			$db_data = $query->count();

		//Whereのみで実行なし
		}elseif( $exec_type == config('const.search_exec_type_unexecuted_key') ){
			$db_data = $query;

		//通常検索
		}else{
			$db_data = $query->paginate(config('const.admin_client_list_limit'));
			$sql = $query->toSql();
//error_log("{$sql}\n",3,"/data/www/storage/line/storage/logs/nishi_log.txt");
		}

		return $db_data;
	}

	/*
	 * SQL文の条件保存
	 */
	private function _saveSearchOption(Request $request)
	{
		//LINEチャンネル
		Session::put('media_line_channel', $request->input('line_channel'));

		//検索項目
		Session::put('media_search_item', $request->input('search_item'));
		
		//LIKE検索
		Session::put('media_search_like_type', $request->input('search_like_type'));

		//検索の値
		Session::put('media_search_item_value', $request->input('search_item_value'));

		//期間-開始
		Session::put('media_start_date', $request->input('start_date'));

		//期間-終了
		Session::put('media_end_date', $request->input('end_date'));

		//媒体種別
		Session::put('media_category', $request->input('category'));

		//表示-合計/日毎
		Session::put('media_disp_type', $request->input('disp_type'));

	}
}

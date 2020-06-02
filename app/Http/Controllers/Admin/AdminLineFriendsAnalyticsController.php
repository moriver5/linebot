<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Ad_code;
use App\Model\TrackAccessLog;
use App\Model\TrackImageLog;

use Session;
use Carbon\Carbon;

class AdminLineFriendsAnalyticsController extends Controller
{
	public function __construct()
	{

	}

	public function index(Request $request, $channel_id)
	{
		//ログイン管理者情報取得
//		$agency = Utility::getAgencyDefaultDispParam();

		$query = TrackAccessLog::query()
				->select('track_access_logs.user_line_id', 'track_access_logs.ad_cd', 'track_access_logs.access_ip', 'track_access_logs.access_referrer', 'track_access_logs.created_at', 'line_users.follow_flg', 'ad_codes.id')
				->join('line_users', function($join){
					$join->on('line_users.line_basic_id', '=', 'track_access_logs.line_basic_id');
					$join->on('line_users.user_line_id', '=', 'track_access_logs.user_line_id');
				})
				->join('ad_codes', 'ad_codes.ad_cd', '=', 'track_access_logs.ad_cd')
				->where('track_access_logs.user_line_id', '!=', '')
				->where('track_access_logs.status', 1)
				->where('track_access_logs.line_basic_id', $channel_id)
				->orderBy('track_access_logs.access_date', 'desc');

		//検索条件をセッションに保存
		$this->_saveSearchOption($request);

		//検索条件を追加後、データ取得
		$db_data = $this->_getSearchOptionData($query);

		//画面表示用配列
		$disp_data = [
			'channel_id'	=> $channel_id,
			'session'		=> Session::all(),
			'db_data'		=> $db_data,
			'total'			=> $db_data->total(),
			'currentPage'	=> $db_data->currentPage(),
			'lastPage'		=> $db_data->lastPage(),
			'links'			=> $db_data->links(),
			'ver'			=> time()
		];
		
		return view('admin.channel.analytics.index', $disp_data);
	}

	private function _saveSearchOption(Request $request)
	{
//		$start_date	 = preg_Replace("/\//", "", $request->input('start_date'));
//		$end_date	 = preg_Replace("/\//", "", $request->input('end_date'));
		$start_date	 = $request->input('start_date');
		$end_date	 = $request->input('end_date');
		$ad_cd		 = $request->input('ad_cd');
		
		//集計-開始
		if( !empty($start_date) ){
			Session::put('regist_start_date', $start_date);
		}else{
			if( empty(Session::get('regist_start_date')) ){
				Session::put('regist_start_date', date('Ymd'));
			}
		}

		//集計-終了
		if( !empty($end_date) ){
			Session::put('regist_end_date', $end_date);
		}else{
			if( empty(Session::get('regist_end_date')) ){
				Session::put('regist_end_date', date('Ymd'));
			}
		}

		//広告コード
		if( !empty($ad_cd) ){
			Session::put('line_ad_cd', $ad_cd);
		}else{
			Session::put('line_ad_cd', '');
		}
	}

	private function _getSearchOptionData($query)
	{

		$dt = Carbon::now();

		//広告コード
		if( !empty(Session::get('line_ad_cd')) ){
			$query->where('track_access_logs.ad_cd', Session::get('line_ad_cd'));
		}

		//集計-開始
		if( !empty(Session::get('regist_start_date')) ){
			$query->where('track_access_logs.access_date', '>=', preg_replace("/\//", "", Session::get('regist_start_date')));
		}else{
			$query->where('track_access_logs.access_date', '>=', date('Ymd'));
		}

		//集計-終了
		if( !empty(Session::get('regist_end_date')) ){
			$query->where('track_access_logs.access_date', '<=', preg_replace("/\//", "", Session::get('regist_end_date')));
		}else{
			$query->where('track_access_logs.access_date', '>=', date('Ymd'));
		}

		$db_data = $query->paginate(config('const.search_page_limit'));
			
		return $db_data;
	}

}

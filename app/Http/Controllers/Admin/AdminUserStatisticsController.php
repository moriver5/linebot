<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Payment_log;
use App\Model\LineOfficialAccount;
use App\Model\AdminChannelAllowList;
use App\Model\TrackAccessLog;
use App\Model\LineUser;
use App\Model\Result_ad_log;
use App\Model\Ad_access_log;
use DB;
use Utility;
use Carbon\Carbon;

class AdminUserStatisticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-利用統計-年
	 */
	public function index(Request $request)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['media_top'].",{$user['login_id']}");

		$db_allow_channel = AdminChannelAllowList::join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'admin_channel_allow_lists.line_basic_id')
			->where('admin_id', $user['id'])->get();

		//デフォルトの日付
		$target_date = $request->input('date');

		//日付パラメータが存在しないとき
		if( empty($target_date) ){
			$target_date = date("Ymd");
		}

		//date指定のパラメータがあるとき
		if( empty($target_date) ){
			$dt = new Carbon();
			$target_date = $dt->year.$dt->month.$dt->day;
		}

		//当日
		$today = preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1年$2月$3日", $target_date);
		$year = preg_replace("/(\d{4})(\d{2})(\d{2})/", "$1", $target_date);
		$month = preg_replace("/(\d{4})(\d{2})(\d{2})/", "$2", $target_date);

		//明日
		$dt = new Carbon($target_date);
		$next_date = $dt->addDay()->format('Ymd');

		//前日
		$dt = new Carbon($target_date);
		$prev_date = $dt->subDay()->format('Ymd');

		//チャンネルごとで指定期間のPV、登録数取得
		$db_data = Ad_access_log::query()
			->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->where('ad_access_logs.access_date', '=', $target_date)
			->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
			->select('line_official_accounts.name as channel', 'ad_access_logs.line_basic_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
			->groupBy('ad_access_logs.line_basic_id')
			->get();

		$listData = [];
		$listTotal = ['pv' => 0, 'reg' => 0, 'unfollow' => 0];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				//チャンネルごと指定期間のブロック数取得
				$db_users = LineUser::query()
					->where('access_date', '=', $target_date)
					->where('line_basic_id', $lines->line_basic_id)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();

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
					'unfollow'	=> $unfollow
				];

				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;
				$listTotal['unfollow'] += $unfollow;
			}
		}

		//画面表示用配列
		$disp_data = [
			'year'				=> $year,
			'month'				=> $month,
			'today'				=> $today,
			'next_date'			=> $next_date,
			'prev_date'			=> $prev_date,
			'db_data'			=> $listData,
			'list_total'		=> $listTotal,
			'ver'				=> time()
		];

		return view('admin.analytics.statistics.analysis', $disp_data);
	}

	/*
	 * 集計-利用統計-月
	 */
	public function monthAnalysis($channel_id, $year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_month']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		//去年
		$prev_year = $year - 1;

		$listData = [];
		$listPrevData = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'pv'		=> 0,
				'reg'		=> 0,
				'unfollow'	=> 0,
			];
		}

		//チャンネルごとで指定期間のPV、登録数取得
		$db_data = Ad_access_log::query()
			->join('admin_channel_allow_lists', 'admin_channel_allow_lists.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->join('line_official_accounts', 'line_official_accounts.line_basic_id', '=', 'ad_access_logs.line_basic_id')
			->where('ad_access_logs.access_date', '>=', $year.sprintf("%02d", $month).'01')
			->where('ad_access_logs.access_date', '<=', $year.sprintf("%02d", $month).$last_day)
			->where('ad_access_logs.line_basic_id', '=', $channel_id)
			->where('admin_channel_allow_lists.admin_id', '=', $user['id'])
			->select('line_official_accounts.name as channel', 'ad_access_logs.access_date', 'ad_access_logs.line_basic_id', DB::raw('sum(ad_access_logs.pv) as pv'), DB::raw('sum(ad_access_logs.reg) as reg'))
			->groupBy('ad_access_logs.line_basic_id', 'ad_access_logs.access_date')
			->get();

		$listDbData = [];
		$listTotal = ['pv' => 0, 'reg' => 0, 'unfollow' => 0];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				//チャンネルごと指定期間のブロック数取得
				$db_users = LineUser::query()
					->where('access_date', '=', $lines->access_date)
					->where('line_basic_id', $lines->line_basic_id)
					->where('follow_flg', 0)
					->select(DB::raw("count(*) as unfollow"))
					->first();

				if( empty($db_users->unfollow) ){
					$unfollow = 0;
				}else{
					$unfollow = $db_users->unfollow;
				}

				$listDbData[sprintf("%d", preg_replace("/(\d{4}\d{2})(\d{2})/", "$2", $lines->access_date))] = [
					'pv'		=> $lines->pv, 
					'reg'		=> $lines->reg, 
					'unfollow'	=> $unfollow
				];

				$listTotal['pv'] += $lines->pv;
				$listTotal['reg'] += $lines->reg;
				$listTotal['unfollow'] += $unfollow;
			}
		}

		foreach($listData as $day => $lines){
			if( !empty($listDbData[$day]) ){
				$listData[$day] = $listDbData[$day];
			}
		}

		//PREV/NEXTリンク先パラメータ設定
		$next_year = $year;
		$prev_year = $year;
		$next_month = $month + 1;	
		$prev_month = $month - 1;	
		
		//当月が12月のときのパラメータ設定
		if( $month == 12 ){
			$next_year = $year + 1;
			$next_month = 1;

		//当月が1月のときのパラメータ設定
		}elseif( $month == 1 ){
			$prev_year = $year -1;
			$prev_month = 12;	
		}

		$disp_data = [
			'channel_id'	=> $channel_id,
			'total_day'		=> $last_day,
			'next_year'		=> $next_year,
			'prev_year'		=> $prev_year,
			'next_month'	=> $next_month,
			'prev_month'	=> $prev_month,
			'year'			=> $year,
			'month'			=> $month,
			'tomonth'		=> "{$year}年{$month}月",
			'db_data'		=> $listData,
			'list_total'	=> $listTotal,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.statistics.analysis_month', $disp_data);
	}

	public function dayAnalysis($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_month']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		//去年
		$prev_year = $year - 1;

		$listData = [];
		$listPrevData = [];

		for($i=1;$i<=23;$i++){
			$listData[$i] = [
				'total'			=> 0,
				'pv_total'		=> 0,
				'regist_total'	=> 0,
				'quite_total'	=> 0,
				'order_count'	=> 0,
				'order_amount'	=> 0,
				'buy_count'		=> 0,
				'buy_amount'	=> 0,
			];
			$listPrevData[$i] = [
				'total'			=> 0,
				'pv_total'		=> 0,
				'regist_total'	=> 0,
				'quite_total'	=> 0,
				'order_count'	=> 0,
				'order_amount'	=> 0,
				'buy_count'		=> 0,
				'buy_amount'	=> 0,
			];
		}

		//アクセスログ
		$access_data = DB::table('month_result_access_logs')->whereRaw('access_date >= ? and access_date <= ?', [$year.sprintf("%02d", $month).'01', $year.sprintf("%02d", $month).$last_day])->get();

		//PVログ
		$pv_data = DB::table('month_pv_logs')->select('access_date', DB::raw('sum(total) as total'))->whereRaw('access_date >= ? and access_date <= ?', [$year.sprintf("%02d", $month).'01', $year.sprintf("%02d", $month).$last_day])->groupBy('access_date')->get();

		//登録人数
		$regist_data = DB::select(
						"select DATE_FORMAT(user_groups.regist_date, '%Y%m%d%H%i%S') as access_date, count(user_groups.client_id) as total from users "
						. "inner join user_groups on users.id = user_groups.client_id where "
//						. "users.disable = 0 and "
//						. "user_groups.status = 1 and "
//						. "user_groups.disable = 0 and "
						. "user_groups.regist_date >= '".$year.sprintf("%02d", $month)."01000000' and " 
						. "user_groups.regist_date <= '".$year.sprintf("%02d", $month).$last_day."235959' "
						. "group by user_groups.client_id"
					);

		//退会人数
		$quite_data = DB::select(
						"select DATE_FORMAT(user_groups.sort_quit_datetime, '%Y%m%d%H%i%S') as access_date, count(user_groups.client_id) as total from users "
						. "inner join user_groups on users.id = user_groups.client_id where "
//						. "users.disable = 0 and "
						. "user_groups.status = 2 and "
//						. "user_groups.disable = 0 and "
						. "user_groups.sort_quit_datetime >= '".$year.sprintf("%02d", $month)."01000000' and " 
						. "user_groups.sort_quit_datetime <= '".$year.sprintf("%02d", $month).$last_day."235959' "
						. "group by user_groups.client_id"
					);
/*
		//注文件数・合計金額
		$order_data = DB::select("select sort_date, sum(money) amount from payment_logs where "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);

		//注文件数・合計金額(購入)
		$buy_data = DB::select("select sort_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);
*/
		for($day=1;$day<=$last_day;$day++){
/*
			//注文数
			foreach($order_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listData[$day]['order_count']++;
					$listData[$day]['order_amount'] += $lines->amount;
				}
			}
			//購入数
			foreach($buy_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listData[$day]['buy_count']++;
					$listData[$day]['buy_amount'] += $lines->amount;
				}
			}
 */
			//登録数
			foreach($regist_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})\d{2}\d{2}\d{2}/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listData[$day]['regist_total'] += $lines->total;
				}
			}
			//退会数
			foreach($quite_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})\d{2}\d{2}\d{2}/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listData[$day]['quite_total'] += $lines->total;
				}
			}
			//アクセス数
			foreach($access_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listData[$day]['total'] += $lines->total;
				}
			}
			//PV数
			foreach($pv_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listData[$day]['pv_total'] += $lines->total;
				}
			}
		}

		//去年のデータ
		//アクセスログ
		$access_prev_data = DB::table('month_result_access_logs')->whereRaw('access_date >= ? and access_date <= ?', [$prev_year.sprintf("%02d", $month).'01', $prev_year.sprintf("%02d", $month).$last_day])->get();

		//PVログ
		$pv_prev_data = DB::table('month_pv_logs')->select('access_date', DB::raw('sum(total) as total'))->whereRaw('access_date >= ? and access_date <= ?', [$prev_year.sprintf("%02d", $month).'01', $prev_year.sprintf("%02d", $month).$last_day])->groupBy('access_date')->get();

		//登録人数
		$regist_prev_data = DB::select(
						"select DATE_FORMAT(user_groups.regist_date, '%Y%m%d%H%i%S') as access_date, count(user_groups.client_id) as total from users "
						. "inner join user_groups on users.id = user_groups.client_id where "
//						. "users.disable = 0 and "
//						. "user_groups.status = 1 and "
//						. "user_groups.disable = 0 and "
						. "user_groups.regist_date >= '".$prev_year.sprintf("%02d", $month)."01000000' and " 
						. "user_groups.regist_date <= '".$prev_year.sprintf("%02d", $month).$last_day."235959' "
						. "group by user_groups.client_id"
					);

		//退会人数
		$quite_prev_data = DB::select(
						"select DATE_FORMAT(user_groups.sort_quit_datetime, '%Y%m%d%H%i%S') as access_date, count(user_groups.client_id) as total from users "
						. "inner join user_groups on users.id = user_groups.client_id where "
//						. "users.disable = 0 and "
						. "user_groups.status = 2 and "
//						. "user_groups.disable = 0 and "
						. "user_groups.sort_quit_datetime >= '".$prev_year.sprintf("%02d", $month)."01000000' and " 
						. "user_groups.sort_quit_datetime <= '".$prev_year.sprintf("%02d", $month).$last_day."235959' "
						. "group by user_groups.client_id"
					);
/*
		//注文件数・合計金額
		$order_prev_data = DB::select("select sort_date, sum(money) amount from payment_logs where "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);

		//注文件数・合計金額(購入)
		$buy_prev_data = DB::select("select sort_date, sum(money) amount from payment_logs where "
						. "status in('0','3') and "
						. "sort_date >= ".$year.sprintf("%02d", $month).'01'." and " 
						. "sort_date <= ".$year.sprintf("%02d", $month).$last_day." "
						. "group by sort_date, order_id"
					);
*/
		$listDays = [];
		for($day=1;$day<=$last_day;$day++){
			$listDays[] = $day;
/*
			//注文数
			foreach($order_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listPrevData[$day]['order_count']++;
					$listPrevData[$day]['order_amount'] += $lines->amount;
				}
			}
			//購入数
			foreach($buy_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->sort_date));
				if( $db_day == $day ){
					$listPrevData[$day]['buy_count']++;
					$listPrevData[$day]['buy_amount'] += $lines->amount;
				}
			}
 */
			//登録数
			foreach($regist_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})\d{2}\d{2}\d{2}/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listPrevData[$day]['regist_total'] += $lines->total;
				}
			}
			//退会数
			foreach($quite_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})\d{2}\d{2}\d{2}/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listPrevData[$day]['quite_total'] += $lines->total;
				}
			}
			//アクセス数
			foreach($access_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listPrevData[$day]['total'] += $lines->total;
				}
			}
			//PV数
			foreach($pv_prev_data as $lines){
				$db_day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
				if( $db_day == $day ){
					$listPrevData[$day]['pv_total'] += $lines->total;
				}
			}
		}

		$js_days = '['.implode(",", $listDays).']';

		//PREV/NEXTリンク先パラメータ設定
		$next_year = $year;
		$prev_year = $year;
		$next_month = $month + 1;	
		$prev_month = $month - 1;	
		
		//当月が12月のときのパラメータ設定
		if( $month == 12 ){
			$next_year = $year + 1;
			$next_month = 1;

		//当月が1月のときのパラメータ設定
		}elseif( $month == 1 ){
			$prev_year = $year -1;
			$prev_month = 12;	
		}

		$disp_data = [
			'total_day'		=> $last_day,
			'next_year'		=> $next_year,
			'prev_year'		=> $prev_year,
			'next_month'	=> $next_month,
			'prev_month'	=> $prev_month,
			'year'			=> $year,
			'month'			=> $month,
			'list_days'		=> $js_days,
			'db_data'		=> $listData,
			'db_prev_data'	=> $listPrevData,
			'ver'			=> time(),
		];
		
		return view('admin.analytics.statistics.analysis_month', $disp_data);
	}

	/*
	 * 集計-利用統計-日
	 */
	public function dayAnalysis2($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['user_statistics_day']."{$month},{$user['login_id']}");

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

/*
		//注文件数・合計金額(購入)
		$buy_data = DB::select("select * from payment_logs where "
						. "status in('0','3') and "
						. "sort_date = ".$year.sprintf("%02d", $month).sprintf("%02d", $day)
					);
 */
		$query = Payment_log::query();
		$listData = $query->join('users', 'users.login_id', '=', 'payment_logs.login_id')
				->select('users.last_access_datetime','users.regist_date as user_regist_date','users.mail_address as email','users.pay_count','users.id as client_id','payment_logs.*')
				->whereIn('payment_logs.status', ['0', '3'])
				->where('payment_logs.sort_date', $year.sprintf("%02d", $month).sprintf("%02d", $day))
				->paginate(config('const.admin_client_list_limit'));

		//PREV/NEXTリンク先パラメータ設定
		//明日
		$dt = new Carbon($year.'-'.$month.'-'.$day);
		preg_match("/(\d{4})\-(\d{2})\-(\d{2}).+/", $dt->addDay(), $nextDate);

		$next_year	 = $nextDate[1];
		$next_month	 = $nextDate[2];
		$next_day	 = $nextDate[3];

		//昨日
		$dt = new Carbon($year.'-'.$month.'-'.$day);
		preg_match("/(\d{4})\-(\d{2})\-(\d{2}).+/", $dt->subDay(), $prevDate);
		$prev_year	 = $prevDate[1];
		$prev_month	 = $prevDate[2];
		$prev_day	 = $prevDate[3];

		$disp_data = [
			'total_day'	=> $last_day,
			'next_year'	=> $next_year,
			'prev_year'	=> $prev_year,
			'next_month'=> $next_month,
			'prev_month'=> $prev_month,
			'next_day'	=> $next_day,
			'prev_day'	=> $prev_day,
			'year'		=> $year,
			'month'		=> $month,
			'day'		=> $day,
			'db_data'	=> $listData,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.statistics.analysis_day', $disp_data);
	}

}

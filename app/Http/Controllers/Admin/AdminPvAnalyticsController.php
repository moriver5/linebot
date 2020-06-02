<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Month_pv_log;
use DB;
use Utility;

class AdminPvAnalyticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-PVログ-年
	 */
	public function index($year = null)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//デフォルトの年度
		if( is_null($year) ){
			$toYear = date('Y');
			
		//リクエストからの年度
		}else{
			$toYear = $year;
		}

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['pv_log_top']."{$toYear},{$user['login_id']}");

		$listData = [];
		$listDate = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listDate[$i] = [
				'id'	=> '',
				'total' => 0
			];
		}
		
		$access_data = DB::table('year_pv_logs')->whereRaw('access_date >= ? and access_date <= ?', [$toYear.'01', $toYear.'12'])->paginate(config('const.admin_client_list_limit'));
		foreach($listDate as $month => $data){
			foreach($access_data as $lines){
				if( empty($listData[$lines->url][$month]['total']) ){
					$listData[$lines->url][$month] = $data;
				}
				$db_month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
				if( $db_month == $month ){
					$listData[$lines->url][$month] = [
						'id'	=> $lines->id,
						'total' => $lines->total
					];
				}
			}
		}

		$disp_data = [
			'prev_year'		=> $toYear - 1,
			'next_year'		=> $toYear + 1,
			'year'			=> $toYear,
			'db_data'		=> $listData,
			'total'			=> $access_data->total(),
			'currentPage'	=> $access_data->currentPage(),
			'lastPage'		=> $access_data->lastPage(),
			'links'			=> $access_data->links(),
			'ver'			=> time(),
		];
		
		return view('admin.analytics.pv.analysis', $disp_data);
	}

	/*
	 * 集計-PVログ-月
	 */
	public function monthAnalysis($year, $month, $pv_name)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['pv_log_month']."{$month},{$user['login_id']}");
		
		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$listData = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'total'	 => 0,
			];
		}

		$display = '';
		$access_data = Month_pv_log::whereRaw('id = ? and access_date >= ? and access_date <= ?', [$pv_name, $year.sprintf("%02d", $month).'01', $year.sprintf("%02d", $month).$last_day])->get();
		foreach($access_data as $lines){
			$display = $lines->url;
			$day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
			$listData[$day] = [
				'total'	 => $lines->total,
			];
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
			'total_day'	=> $last_day,
			'next_year'	=> $next_year,
			'prev_year'	=> $prev_year,
			'next_month'=> $next_month,
			'prev_month'=> $prev_month,
			'year'		=> $year,
			'month'		=> $month,
			'pv_name'	=> $pv_name,
			'db_data'	=> $listData,
			'display'	=> $display,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.pv.analysis_month', $disp_data);
	}

}

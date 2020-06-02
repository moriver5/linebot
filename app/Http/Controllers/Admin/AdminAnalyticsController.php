<?php

namespace App\Http\Controllers\Admin;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libs\SysLog;
use App\Model\Admin;
use App\Model\Year_result_access_log;
use App\Model\Month_result_access_log;
use App\Model\Day_result_access_log;
use Utility;

class AdminAnalyticsController extends Controller
{
	private $log_obj;

	//
	public function __construct()
	{
		$this->log_obj = new SysLog(config('const.operation_export_log_name'), config('const.system_log_dir_path').config('const.operation_history_file_name'));
	}
	
	/*
	 * 集計-アクセス解析-年
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
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_top']."{$toYear},{$user['login_id']}");

		$listData = [];

		//12ヵ月分の集計
		for($i=1;$i<=12;$i++){
			$listData[$i] = [
				'no_pay' => 0,
				'pay'	 => 0,
				'total'	 => 0,			
			];
		}
		
		$access_data = Year_result_access_log::whereRaw('access_date >= ? and access_date <= ?', [$toYear.'01', $toYear.'12'])->get();
		foreach($access_data as $lines){
			$month = sprintf("%d", preg_replace("/\d{4}(\d{2})/", "$1", $lines->access_date));
			$listData[$month] = [
				'no_pay' => $lines->no_pay,
				'pay'	 => $lines->pay,
				'total'	 => $lines->total,
			];
		}
		
		$disp_data = [
			'prev_year'	=> $toYear - 1,
			'next_year'	=> $toYear + 1,
			'year'		=> $toYear,
			'db_data'	=> $listData,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.access.analysis', $disp_data);
	}

	/*
	 * 集計-アクセス解析-月
	 */
	public function monthAnalysis($year, $month)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_month']."{$month},{$user['login_id']}");
		
		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		$listData = [];

		for($i=1;$i<=$last_day;$i++){
			$listData[$i] = [
				'no_pay' => 0,
				'pay'	 => 0,
				'total'	 => 0,			
			];
		}

		$access_data = Month_result_access_log::whereRaw('access_date >= ? and access_date <= ?', [$year.sprintf("%02d", $month).'01', $year.sprintf("%02d", $month).$last_day])->get();
		foreach($access_data as $lines){
			$day = sprintf("%d", preg_replace("/\d{4}\d{2}(\d{2})/", "$1", $lines->access_date));
			$listData[$day] = [
				'no_pay' => $lines->no_pay,
				'pay'	 => $lines->pay,
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
			'db_data'	=> $listData,
			'ver'		=> time(),
		];
		
		return view('admin.analytics.access.analysis_month', $disp_data);
	}

	/*
	 * 集計-アクセス解析-日
	 */
	public function dayAnalysis($year, $month, $day)
	{
		//ログイン管理者情報取得
		$user = Utility::getAdminDefaultDispParam();
		
		//ログ出力
		$this->log_obj->addLog(config('const.admin_display_list')['analysis_day']."{$day},{$user['login_id']}");

		$listData = [];
		
		//1日分の集計
		for($i=0;$i<=23;$i++){
			$listData[$i] = [
				'no_pay'	=> 0,
				'pay'		=> 0,
				'total'		=> 0,
				'no_pay24'	=> 0,
				'pay24'		=> 0,
				'total24'	=> 0
			];		
		}

		$access_data = Day_result_access_log::whereRaw('access_date >= ? and access_date <= ?', [$year.sprintf("%02d", $month).sprintf("%02d", $day).'00', $year.sprintf("%02d", $month).sprintf("%02d", $day).'23'])->get();
		foreach($access_data as $lines){
			$hour = sprintf("%d", preg_replace("/\d{4}\d{2}\d{2}(\d{2})/", "$1", $lines->access_date));
			$listData[$hour] = [
				'no_pay'	 => $lines->no_pay,
				'pay'		 => $lines->pay,
				'total'		 => $lines->total,
				'no_pay24'	 => $lines->no_pay24,
				'pay24'		 => $lines->pay24,
				'total24'	 => $lines->total24,
			];
		}

		//PREV/NEXTリンク先パラメータ設定
		$next_year = $year;
		$prev_year = $year;
		$next_month = $month;	
		$prev_month = $month;	
		$next_day = $day + 1;
		$prev_day = $day - 1;

		//月末取得
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
		if( $day == $last_day ){
			$next_month = $month + 1;	
			$next_day = 1;
			$prev_day = $day - 1;
			
			//当月が12月のときのパラメータ設定
			if( $month == 12 ){
				$next_year = $year + 1;
				$next_month = 1;
			}
		}
		
		if( $day == 1 ){
			$prev_month = $month - 1;	
			//月末取得
			$last_day = date('t', mktime(0, 0, 0, $prev_month, 1, $year));
			$prev_day = $last_day;
			
			//当月が1月のときのパラメータ設定
			if( $month == 1 ){
				$prev_year = $year -1;
				$prev_month = 12;	
			}
		}
		
		$disp_data = [
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
		
		return view('admin.analytics.access.analysis_day', $disp_data);
	}
}

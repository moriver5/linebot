<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Admin;
use DB;
use Carbon\Carbon;

class AnalyticsAccessLog extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'access_log:analysis {year?} {month?} {day?} {hour?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'access_logテーブルからデータ集計を行う';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$year	 = date('Y');
		$month	 = date('m');
		$day	 = date('d');
		$hour	 = date('H');

		if( !empty($this->argument("year")) && 
			!empty($this->argument("month")) && 
			!empty($this->argument("day")) && 
			(!empty($this->argument("hour")) || 
			 $this->argument("hour") == 0) ){
			$year	 = $this->argument("year");
			$month	 = $this->argument("month");
			$day	 = $this->argument("day");			
			$hour	 = $this->argument("hour");			
		}

		/*
		 * 年月の集計
		 */
		$last_day = date('t', mktime(0, 0, 0, $month, 1, $year));

		//時間帯別入金あり
		$listPay = DB::select(
			"select date_format(created_at, '%Y/%m') as pay_date,"
			. "count(*) as count "
			. "from access_logs "
			. "where pay_type = 1 and "
			. "login_date >= '{$year}-{$month}-1' and "
			. "login_date <= '{$year}-{$month}-{$last_day}'"
			. "group by pay_date");

		//時間帯別入金なし
		$listNoPay = DB::select(
			"select date_format(created_at, '%Y/%m') as pay_date,"
			. "count(*) as count "
			. "from access_logs "
			. "where pay_type = 0 and "
			. "login_date >= '{$year}-{$month}-1' and "
			. "login_date <= '{$year}-{$month}-{$last_day}'"
			. "group by pay_date");

		//入金あり
		$pay	 = !empty($listPay[0]->count) ? $listPay[0]->count:0;

		//入金なし
		$no_pay	 = !empty($listNoPay[0]->count) ? $listNoPay[0]->count:0;

		//集計結果をyear_result_access_logsテーブルに登録
		DB::transaction(function() use($year, $month, $no_pay, $pay){
			DB::insert("insert ignore into year_result_access_logs("
				. "access_date, "
				. "no_pay, "
				. "pay, "
				. "total, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "{$year}".sprintf("%02d", $month).", "
				. "{$no_pay}, "
				. "{$pay}, "
				. ($no_pay + $pay).", "
				. "'".Carbon::now()."', "
				. "'".Carbon::now()."') "
				. "on duplicate key update "
				. "access_date = {$year}".sprintf("%02d", $month).", "
				. "no_pay = {$no_pay}, "
				. "pay = {$pay}, "
				. "total = ".($no_pay + $pay).";");
		});

		/*
		 * 年月日の集計
		 */
		//時間帯別入金あり
		$listPay = DB::select(
			"select login_date,"
			. "count(*) as count "
			. "from access_logs "
			. "where pay_type = 1 and "
			. "login_date = '{$year}-{$month}-{$day}' "
			. "group by login_date");

		//時間帯別入金なし
		$listNoPay = DB::select(
			"select login_date,"
			. "count(*) as count "
			. "from access_logs "
			. "where pay_type = 0 and "
			. "login_date = '{$year}-{$month}-{$day}' "
			. "group by login_date");

		//入金あり
		$pay	 = !empty($listPay[0]->count) ? $listPay[0]->count:0;

		//入金なし
		$no_pay	 = !empty($listNoPay[0]->count) ? $listNoPay[0]->count:0;

		//集計結果をyear_result_access_logsテーブルに登録
		DB::transaction(function() use($year, $month, $day, $no_pay, $pay){
			DB::insert("insert ignore into month_result_access_logs("
				. "access_date, "
				. "no_pay, "
				. "pay, "
				. "total, "
				. "created_at, "
				. "updated_at) "
				. "values("
				. "{$year}".sprintf("%02d", $month).sprintf("%02d", $day).", "
				. "{$no_pay}, "
				. "{$pay}, "
				. ($no_pay + $pay).", "
				. "'".Carbon::now()."', "
				. "'".Carbon::now()."') "
				. "on duplicate key update "
				. "access_date = {$year}".sprintf("%02d", $month).sprintf("%02d", $day).", "
				. "no_pay = {$no_pay}, "
				. "pay = {$pay}, "
				. "total = ".($no_pay + $pay).";");
		});

		/*
		 * 1日分の集計
		 */

		//時間帯別入金あり
		$listPay = DB::select(
			"select date_format(created_at, '%Y-%m-%d %h') as pay_date,"
			. "count(*) as count "
			. "from access_logs "
			. "where pay_type = 1 and "
			. "login_date = '{$year}-{$month}-{$day}' and "
			. "created_at >= '{$year}-{$month}-{$day} {$hour}:00:00' and "
			. "created_at <= '{$year}-{$month}-{$day} {$hour}:59:59' "
			. "group by pay_date");

		//時間帯別入金なし
		$listNoPay = DB::select(
			"select date_format(created_at, '%Y-%m-%d %h') as pay_date,"
			. "count(*) as count "
			. "from access_logs "
			. "where pay_type = 0 and "
			. "login_date = '{$year}-{$month}-{$day}' and "
			. "created_at >= '{$year}-{$month}-{$day} {$hour}:00:00' and "
			. "created_at <= '{$year}-{$month}-{$day} {$hour}:59:59' "
			. "group by pay_date");
		
		$listData[$hour] = [
			'pay'		=> 0,
			'no_pay'	=> 0,
			'total'		=> 0,
			'pay24'		=> 0,
			'no_pay24'	=> 0,
			'total24'	=> 0,
		];

		//入金あり
		if( !empty($listPay) ){
			foreach($listPay as $line){
				$listData[$hour]['pay'] = $line->count;
			}
		}
		
		//入金なし
		if( !empty($listNoPay) ){
			foreach($listNoPay as $line){
				$listData[$hour]['no_pay'] = $line->count;
			}
		}
		
		//入金ありと入金なしをマージして集計配列に格納
		foreach($listData as $hour => $line){
			$listData[$hour]['total'] = $line['pay'] + $line['no_pay'];
		}

		//集計結果をyear_result_access_logsテーブルに登録
		DB::transaction(function() use($year, $month, $day, $listData){
			foreach($listData as $hour => $line){

				//23h前の時刻を取得
				$listPrevDate = getdate(strtotime("{$year}-{$month}-{$day} {$hour}:0:0 -23 hour"));

				//時間帯別入金あり
				$listPay = DB::select(
					"select count(*) as count "
					. "from access_logs "
					. "where pay_type = 1 and "
					. "created_at >= '{$listPrevDate['year']}-{$listPrevDate['mon']}-{$listPrevDate['mday']} {$listPrevDate['hours']}:0:0' and "
					. "created_at <= '{$year}-{$month}-{$day} {$hour}:59:59' "
					. "group by pay_type");

				//時間帯別入金なし
				$listNoPay = DB::select(
					"select count(*) as count "
					. "from access_logs "
					. "where pay_type = 0 and "
					. "created_at >= '{$listPrevDate['year']}-{$listPrevDate['mon']}-{$listPrevDate['mday']} {$listPrevDate['hours']}:0:0' and "
					. "created_at <= '{$year}-{$month}-{$day} {$hour}:59:59' "
					. "group by pay_type");

				//入金あり
				if( !empty($listPay) ){
					$line['pay24'] = $listPay[0]->count;
				}else{
					$line['pay24'] = 0;					
				}

				//入金なし
				if( !empty($listNoPay) ){
					$line['no_pay24'] = $listNoPay[0]->count;
				}else{
					$line['no_pay24'] = 0;					
				}

				//入金ありと入金なしをマージして集計配列に格納
				$line['total24'] = $line['pay24'] + $line['no_pay24'];

				//集計結果をyear_result_access_logsテーブルに登録
				DB::insert("insert ignore into day_result_access_logs("
					. "access_date, "
					. "no_pay, "
					. "pay, "
					. "total, "
					. "no_pay24, "
					. "pay24, "
					. "total24, "
					. "created_at, "
					. "updated_at) "
					. "values("
					. "{$year}".sprintf("%02d", $month).sprintf("%02d", $day).sprintf("%02d", $hour).", "
					. "{$line['no_pay']}, "
					. "{$line['pay']}, "
					. "{$line['total']}, "
					. "{$line['no_pay24']}, "
					. "{$line['pay24']}, "
					. "{$line['total24']}, "
					. "'".Carbon::now()."', "
					. "'".Carbon::now()."') "
					. "on duplicate key update "
					. "access_date = {$year}".sprintf("%02d", $month).sprintf("%02d", $day).sprintf("%02d", $hour).", "
					. "no_pay = {$line['no_pay']}, "
					. "pay = {$line['pay']}, "
					. "total = {$line['total']}, "
					. "no_pay24 = {$line['no_pay24']}, "
					. "pay24 = {$line['pay24']}, "
					. "total24 = {$line['total24']};");
			}
		});

		//24時間以上前のデータをすべて削除
		DB::transaction(function() use($year, $month, $day, $hour){
			//24時間前の時刻を取得
			$listPrevDate = getdate(strtotime("{$year}-{$month}-{$day} {$hour}:0:0 -23 hour"));

			//使用しない24時間以上前のデータをすべて削除
			DB::delete("delete from access_logs where created_at < '{$listPrevDate['year']}-{$listPrevDate['mon']}-{$listPrevDate['mday']} {$listPrevDate['hours']}:0:0'");
		});
	}
}

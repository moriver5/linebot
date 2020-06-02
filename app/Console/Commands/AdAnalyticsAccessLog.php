<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Admin;
use App\Model\Day_pv_log;
use DB;
use Carbon\Carbon;

class AdAnalyticsAccessLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access_log:ad_analysis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '媒体集計に関するデータ集計を行う';

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

		$start_date	 = "{$year}{$month}{$day}";
		$end_date	 = "{$year}{$month}{$day}";
		
		$yyyymm = "{$year}{$month}";
/*
		//仮登録人数
		$temp_regist_data = DB::select(
						"select ad_cd, count(ad_cd) as total from user_groups where "
						. "status = 0 and "
						. "regist_date >= '{$start_date}000000' and " 
						. "regist_date <= '{$end_date}235959' "
						. "group by ad_cd"
					);

		//登録人数
		$regist_data = DB::select(
						"select ad_cd, count(ad_cd) as total from user_groups where "
						. "status = 1 and "
						. "regist_date >= '{$start_date}000000' and " 
						. "regist_date <= '{$end_date}235959' "
						. "group by ad_cd"
					);

		//退会人数
		$quite_data = DB::select(
						"select ad_cd, count(ad_cd) as total from user_groups where "
						. "status = 2 and "
						. "regist_date >= '{$start_date}000000' and " 
						. "regist_date <= '{$end_date}235959' "
						. "group by ad_cd"
					);

		//注文数・合計金額
		$order_data = DB::select("select ad_cd, count(ad_cd) as count, sum(money) amount from payment_logs where "
						. "substring(sort_date, 1,6) = {$yyyymm} "
						. "group by ad_cd"
					);

		//購入数・合計金額(購入)
		$buy_data = DB::select("select ad_cd, count(ad_cd) as count, sum(money) amount from payment_logs where "
						. "status in('0','3')  and "
						. "substring(sort_date, 1,6) = {$yyyymm} "
						. "group by ad_cd"
					);
 */
/*
		//広告ごとのアクセス数/アクティブ数
		$ad_access_data = DB::select(
						"select login_id,ad_cd,count(access_date) as pv from day_pv_logs where "
						. "access_date = '{$start_date}' "
						. "group by ad_cd, login_id"
					);
*/
		$ad_access_data = DB::select(
						"select domain,ad_cd,uu,au,reg from ad_access_logs where "
						. "access_date = '{$start_date}' "
					);

		$listData = [];
/*
		//仮登録者数
		foreach($temp_regist_data as $lines){
			$listData[$lines->ad_cd]['temp_reg'] = $lines->total;
		}

		//登録者数
		foreach($regist_data as $lines){
			$listData[$lines->ad_cd]['reg'] = $lines->total;			
		}

		//退会者数
		foreach($quite_data as $lines){
			$listData[$lines->ad_cd]['quite'] = $lines->total;			
		}

		//注文数
		foreach($order_data as $lines){
			$listData[$lines->ad_cd]['order'] = $lines->count;			
		}

		//購入数/売上金額
		foreach($buy_data as $lines){
			$listData[$lines->ad_cd]['pay'] = $lines->count;
			$listData[$lines->ad_cd]['amount'] = $lines->amount;
		}
*/
		//広告ごとのアクセス数/アクティブ数
		foreach($ad_access_data as $lines){
			if( empty($listData[$lines->domain][$lines->ad_cd]['temp_reg']) ){
				$listData[$lines->domain][$lines->ad_cd]['temp_reg'] = 0;
			}
			if( empty($listData[$lines->domain][$lines->ad_cd]['quite']) ){
				$listData[$lines->domain][$lines->ad_cd]['quite'] = 0;
			}
			if( empty($listData[$lines->domain][$lines->ad_cd]['order']) ){
				$listData[$lines->domain][$lines->ad_cd]['order'] = 0;
			}
			if( empty($listData[$lines->domain][$lines->ad_cd]['pay']) ){
				$listData[$lines->domain][$lines->ad_cd]['pay'] = 0;
			}
			if( empty($listData[$lines->domain][$lines->ad_cd]['amount']) ){
				$listData[$lines->domain][$lines->ad_cd]['amount'] = 0;
			}


			if( empty($listData[$lines->domain][$lines->ad_cd]['active']) ){
				$listData[$lines->domain][$lines->ad_cd]['active'] = 0;
			}
			$listData[$lines->domain][$lines->ad_cd]['active'] = $lines->au;			

			if( empty($listData[$lines->domain][$lines->ad_cd]['pv']) ){
				$listData[$lines->domain][$lines->ad_cd]['pv'] = 0;
			}
			$listData[$lines->domain][$lines->ad_cd]['pv'] = $lines->uu;

			if( empty($listData[$lines->domain][$lines->ad_cd]['reg']) ){
				$listData[$lines->domain][$lines->ad_cd]['reg'] = 0;
			}
			$listData[$lines->domain][$lines->ad_cd]['reg'] = $lines->reg;			
		}

		//集計結果をyear_result_access_logsテーブルに登録
		DB::transaction(function() use($listData, $start_date){
			$now_date = Carbon::now();
			foreach($listData as $domain => $list_data){
				foreach($list_data as $ad_cd => $lines){
					if( empty($lines['temp_reg']) ){
						$lines['temp_reg'] = 0;
					}
					if( empty($lines['reg']) ){
						$lines['reg'] = 0;					
					}
					if( empty($lines['quit']) ){
						$lines['quit'] = 0;					
					}
					if( empty($lines['order']) ){
						$lines['order'] = 0;
					}
					if( empty($lines['pay']) ){
						$lines['pay'] = 0;
					}
					if( empty($lines['amount']) ){
						$lines['amount'] = 0;
					}
					if( empty($lines['active']) ){
						$lines['active'] = 0;
					}
					if( empty($lines['pv']) ){
						$lines['pv'] = 0;
					}
					DB::insert("insert ignore into result_ad_logs("
						. "domain, "
						. "ad_cd, "
						. "access_date, "
						. "pv, "
						. "temp_reg, "
						. "reg, "
						. "quit, "
						. "active, "
						. "order_num, "
						. "pay, "
						. "amount, "
						. "created_at, "
						. "updated_at) "
						. "values("
						. "'{$domain}', "
						. "'{$ad_cd}', "
						. "{$start_date}, "
						. "{$lines['pv']}, "
						. "{$lines['temp_reg']}, "
						. "{$lines['reg']}, "
						. "{$lines['quit']}, "
						. "{$lines['active']}, "
						. "{$lines['order']}, "
						. "{$lines['pay']}, "
						. "{$lines['amount']}, "
						. "'{$now_date}', "
						. "'{$now_date}') "
						. "on duplicate key update "
						. "pv = {$lines['pv']}, "
						. "temp_reg = {$lines['temp_reg']}, "
						. "reg = {$lines['reg']}, "
						. "quit = {$lines['quit']}, "
						. "active = {$lines['active']}, "
						. "order_num = {$lines['order']}, "
						. "pay = {$lines['pay']}, "
						. "amount = {$lines['amount']};");
				}
			}
		});

		//使用しない昨日のデータをすべて削除
		DB::transaction(function() use($start_date){
			DB::delete("delete from day_pv_logs where access_date < {$start_date}");
		});
	}
}

<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class Top_productsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$now_date = date("Y/m/d h:m:s");
		$sdate = '2016-01-01 00:00:00';
		$edate = '2018-09-01 00:00:00';
		$sort_date = '2016-01-01';

		$listData = [
			[
				'title'		=> '商品１',
				'money'		=> 10000,
				'point'		=> 0,
			],
			[
				'title'		=> '商品２',
				'money'		=> 20000,
				'point'		=> 200,
			],
			[
				'title'		=> '商品３',
				'money'		=> 30000,
				'point'		=> 300,
			],
			[
				'title'		=> '商品4',
				'money'		=> 40000,
				'point'		=> 400,
			],
			[
				'title'		=> '商品5',
				'money'		=> 50000,
				'point'		=> 500,
			],
			[
				'title'		=> '商品6',
				'money'		=> 60000,
				'point'		=> 600,
			],
			[
				'title'		=> '商品7',
				'money'		=> 70000,
				'point'		=> 700,
			],
			[
				'title'		=> '商品8',
				'money'		=> 80000,
				'point'		=> 800,
			],
			[
				'title'		=> '商品9',
				'money'		=> 90000,
				'point'		=> 900,
			],
			[
				'title'		=> '商品10',
				'money'		=> 100000,
				'point'		=> 1000,
			],
			[
				'title'		=> '商品11',
				'money'		=> 110000,
				'point'		=> 1100,
			],
		];
		
//		foreach($listData as $lines){
		for($i=1;$i<20;$i++){
			$dt = new Carbon();
			$sdate = $dt->addDay(rand(0,30));
			$dt = new Carbon();
			$edate = $dt->addDay(rand(31,60));
			DB::table('top_products')->insert([
				'title'				=> 'タイトル'.$i,
				'open_flg'			=> 1,
				'order_num'			=> 1,
				'money'				=> 1000,
				'point'				=> 10,
				'start_date'		=> $sdate,
				'sort_start_date'	=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $sdate).'00',
				'end_date'			=> $edate,
				'sort_end_date'		=> preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $edate).'00',
				'sort_date'			=> $sort_date,
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date,
			]);
		}
    }
}

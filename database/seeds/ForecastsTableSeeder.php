<?php

use Illuminate\Database\Seeder;

class ForecastsTableSeeder extends Seeder
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
				'category'		=> 1,
				'campaigns'		=> 1,
				'point'			=> 0,
				'title'			=> '予想1',
				'comment'		=> 'コメント1',
				'detail'		=> '内容1',
			],
			[
				'category'		=> 1,
				'campaigns'		=> 2,
				'point'			=> 0,
				'title'			=> '予想2',
				'comment'		=> 'コメント2',
				'detail'		=> '内容2',
			],
			[
				'category'		=> 1,
				'campaigns'		=> 3,
				'point'			=> 0,
				'title'			=> '予想3',
				'comment'		=> 'コメント3',
				'detail'		=> '内容3',
			],
			[
				'category'		=> 2,
				'campaigns'		=> 4,
				'point'			=> 100,
				'title'			=> '予想4',
				'comment'		=> 'コメント4',
				'detail'		=> '内容4',
			],
			[
				'category'		=> 2,
				'campaigns'		=> 5,
				'point'			=> 200,
				'title'			=> '予想5',
				'comment'		=> 'コメント5',
				'detail'		=> '内容5',
			],
			[
				'category'		=> 1,
				'campaigns'		=> 6,
				'point'			=> 0,
				'title'			=> '予想6',
				'comment'		=> 'コメント6',
				'detail'		=> '内容6',
			],
			[
				'category'		=> 2,
				'campaigns'		=> 7,
				'point'			=> 0,
				'title'			=> '予想7',
				'comment'		=> 'コメント7',
				'detail'		=> '内容7',
			],
		];
		
		foreach($listData as $lines){
			DB::table('forecasts')->insert([
				'disp_sdate'		=> $sdate,
				'disp_edate'		=> $edate,
				'open_sdate'		=> $sdate,
				'open_edate'		=> $edate,
				'category'			=> $lines['category'],
				'campaigns'			=> $lines['campaigns'],
				'open_flg'			=> 1,
				'point'				=> $lines['point'],
				'title'				=> $lines['title'],
				'comment'			=> $lines['comment'],
				'detail'			=> $lines['detail'],
				'visitor'			=> 0,
				'disp_sort_sdate'	=> $sort_date,
				'disp_sort_edate'	=> $sort_date,
				'open_sort_sdate'	=> $sort_date,
				'open_sort_edate'	=> $sort_date,
				'created_at'		=> $now_date,
				'updated_at'		=> $now_date,
			]);
		}
    }
}

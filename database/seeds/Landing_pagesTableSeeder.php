<?php

use Illuminate\Database\Seeder;

class Landing_pagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$now_date = date("Y/m/d h:m:s");
		$listMemo = ['テスト１','テスト２','テスト３','テスト４','テスト５','テスト６','テスト７','テスト８','テスト９','テスト１０','テスト１１','テスト１２','テスト１３','テスト１４','テスト１５'];
		foreach($listMemo as $memo){
			DB::table('landing_pages')->insert([
				'open_flg' => 1,
				'memo' => $memo,
				'sort_date' => $now_date,
				'created_at' => $now_date,
				'updated_at' => $now_date,
			]);
		}
    }
}

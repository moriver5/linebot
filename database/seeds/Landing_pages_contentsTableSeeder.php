<?php

use Illuminate\Database\Seeder;

class Landing_pages_contentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$now_date = date("Y/m/d h:m:s");
		$listType = ['company','css.css','done','index','js.js','privacy','rule'];

		$db_data = DB::table('landing_pages')->get();
		foreach($db_data as $lines){
			foreach($listType as $type){
				DB::table('landing_pages_contents')->insert([
					'lp_id' => $lines->id,
					'url_open_flg' => 0,
					'name' => $type,
					'content' => '',
					'created_at' => $now_date,
					'updated_at' => $now_date,
				]);
			}
		}
    }
}

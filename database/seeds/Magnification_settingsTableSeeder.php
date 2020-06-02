<?php

use Illuminate\Database\Seeder;

class Magnification_settingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$now_date = date("Y/m/d h:m:s");

		DB::table('magnification_settings')->insert([
			'type' => 'registed',
			'default_id' => 1,
			'category_id' => 3,
			'start_date' => '2017-10-30 12:17:00',
			'end_date' => '2018-06-30 12:17:00',
			'created_at' => $now_date,
			'updated_at' => $now_date,
		]);
    }
}

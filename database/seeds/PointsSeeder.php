<?php

use Illuminate\Database\Seeder;

class PointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('points')->insert([
			'pay_type'	 => 'bank',
			'money'		 => 5000,
			'point'		 => 50,
			'disp_msg'	 => ''
		]);
		DB::table('points')->insert([
			'pay_type'	 => 'bank',
			'money'		 => 10000,
			'point'		 => 100,
			'disp_msg'	 => '',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('points')->insert([
			'pay_type'	 => 'bank',
			'money'		 => 30000,
			'point'		 => 340,
			'disp_msg'	 => '(40ポイント/4,000分サービス)',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
		DB::table('points')->insert([
			'pay_type'	 => 'bank',
			'money'		 => 50000,
			'point'		 => 580,
			'disp_msg'	 => '(80ポイント/8,000分サービス)',
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
    }
}

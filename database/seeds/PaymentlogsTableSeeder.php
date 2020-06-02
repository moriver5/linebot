<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaymentlogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		for($i=0;$i<1000;$i++){

			$dt = new Carbon();
			$regist_date = $dt->subDay(rand(1,200));

			$now_date = Carbon::now();

			DB::table('payment_logs')->insert([
				'pay_type' => rand(0,3),
				'login_id' => rand(1,999999),
				'type' => rand(0,1),
				'product_id' => rand(1,20),
				'order_id' => $i,
				'money' => rand(1000,100000),
				'point' => rand(0,1000),
				'regist_date' => $regist_date,
				'pay_count' => rand(0,100),
				'sort_date' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5", $regist_date),
				'created_at' => $now_date,
				'updated_at' => $now_date,
			]);
		}
    }
}

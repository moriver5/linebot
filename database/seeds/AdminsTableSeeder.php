<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$listMail = [
			'fmember.nishizawa@gmail.com',
			'm-nishizawa@full-member.jp'
		];
		for($t=0;$t<count($listMail);$t++){
			$ad_cd = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 8);

			date_default_timezone_set('UTC');
			$start = strtotime('2016-5-01 00:00:00'); // 0
			$end = strtotime('2017-01-30 14:00:00'); // 2147483647
			$create_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			$start = strtotime('2017-2-01 00:00:00'); // 0
			$end = strtotime('2017-03-30 14:00:00'); // 2147483647
			$update_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			$start = strtotime('2017-04-01 00:00:00'); // 0
			$end = strtotime('2017-09-14 14:00:00'); // 2147483647
			$last_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			DB::table('admins')->insert([
//				'name' => str_random(10),
				'password' => bcrypt('moritomo'),
//				'email' => str_random(10).'@gmail.com',
				'email' => $listMail[$t],
				'type' => '4',
				'remember_token' => session_create_id(),
				'access_host' => 'jra-yosou.net',
				'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
				'created_at' => $create_date,
				'updated_at' => $update_date,
				'last_login_date' => $last_date
			]);
		}
    }
}

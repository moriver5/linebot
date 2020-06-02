<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$count = 0;
		for($t=0;$t<10000;$t++){
			$count++;
			if( ($count % 100) == 0 ){
				echo $count."\n";
			}
			date_default_timezone_set('UTC');

			$start = strtotime('2017-6-01 00:00:00'); // 0
			$end = strtotime('2018-07-30 14:00:00'); // 2147483647
			$create_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			$start = strtotime('2018-08-01 00:00:00'); // 0
			$end = strtotime('2018-10-16 14:00:00'); // 2147483647
			$update_date = date("Y/m/d h:m:s", mt_rand($start, $end)) . PHP_EOL;

			$client_id = DB::table('users')->insertGetId([
				'mail_address' => 'fmember.nishizawa+'.mt_rand(50000000000,999999999999).'@gmail.com',
				'remember_token' => session_create_id(),
				'regist_date' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:)(\d{2})?/", "$1$3$5$6$7$9", $create_date),
				'last_access' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:)(\d{2})?/", "$1$3$5$6$7$9", $create_date),
				'created_at' => $create_date,
				'updated_at' => $update_date
			]);

//			for($i=0;$i<3;$i++){
				DB::table('user_groups')->insert([
					'client_id'		=> $client_id,
					'group_id'		=> mt_rand(3,14),
					'regist_date'	=> (int)(preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2}):(\d{2})?/", "$1$3$5$6$7$8", $create_date)),
					'last_access' => preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:)(\d{2})?/", "$1$3$5$6$7$9", $create_date),
					'created_at'	=> $create_date,
					'updated_at'	=> $update_date
				]);
//			}

			usleep(5000);
		}
    }
}

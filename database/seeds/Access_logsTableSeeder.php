<?php

use Illuminate\Database\Seeder;

class Access_logsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$listLoginId = [];
		$listHistory = [0,1];
		
        //
		for($t=0;$t<6;$t++){
			for($s=0;$s<20;$s++){
				$num = rand(30,150);
				for($i=0;$i<$num;$i++){
					$num = rand(1000,3500);
					for($n=0;$n<8;$n++){
						DB::insert('insert ignore into access_logs(login_id,pay_type,login_date,created_at,updated_at) values("'
						.rand(00000,9999999).'",'
						.$listHistory[rand(0,1)].',"'
						.date("Y/m/d",  strtotime("-".($s + (30 * $t))." day")).'","'
						.date("Y/m/d H:m:s",  strtotime("-".($s + (30 * $t))." day +{$n} hour")).'","'
						.date("Y/m/d H:m:s",  strtotime("-".($s + (30 * $t))." day +{$n} hour")).'")');
					}
				}
			}
		}
    }
}

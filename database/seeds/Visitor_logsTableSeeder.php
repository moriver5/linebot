<?php

use Illuminate\Database\Seeder;

class Visitor_logsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$listForecastId = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];

		$list_data = [];
		$db_data = DB::table('users')->get();
		foreach($db_data as $lines){
			$list_data[] = $lines->id;
		}

		for($t=0;$t<count($listForecastId);$t++){
			$limit = rand(1,100);
			shuffle($list_data);
			
			$count = 0;
			foreach($list_data as $id){
				$count++;
				DB::table('visitor_logs')->insert([
					'forecast_id'	=> $listForecastId[rand(0,count($listForecastId)-1)],
					'client_id'		=> $id,
					'created_at'	=> date("Y/m/d h:m:s"),
					'updated_at'	=> date("Y/m/d h:m:s"),
				]);
				if( $count >= $limit ){
					break;
				}
			}
		}
    }
}

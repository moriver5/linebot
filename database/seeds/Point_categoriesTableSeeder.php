<?php

use Illuminate\Database\Seeder;

class Point_categoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$now_date = date("Y/m/d h:m:s");

		DB::table('point_categories')->insert([
			'name' => '通常',
			'remarks' => 'デフォルトポイント設定',
			'created_at' => $now_date,
			'updated_at' => $now_date,
		]);
		DB::table('point_categories')->insert([
			'name' => '1.5倍',
			'remarks' => '通常時の１．５倍のポイント設定',
			'created_at' => $now_date,
			'updated_at' => $now_date,
		]);
		DB::table('point_categories')->insert([
			'name' => '2倍',
			'remarks' => '通常時の２倍のポイント設定',
			'created_at' => $now_date,
			'updated_at' => $now_date,
		]);
		DB::table('point_categories')->insert([
			'name' => '5倍',
			'remarks' => '通常時の５倍のポイント設定',
			'created_at' => $now_date,
			'updated_at' => $now_date,
		]);

		$listPointSetting = [
				[[5000,50,''],[10000,100,''],[30000,340,'(40ポイント/4,000分サービス)'],[50000,580,'(80ポイント/8,000分サービス)']],
				[[5000,75,''],[10000,150,'']],
				[[10000,200,'(ポイント【２倍】サービス中)'],[20000,400,'(ポイント【２倍】サービス中)'],[30000,600,'(ポイント【２倍】サービス中)'],[40000,800,'(ポイント【２倍】サービス中)'],[50000,1000,'(ポイント【２倍】サービス中)']],
				[[10000,500,'(ポイント【５倍】サービス中)'],[20000,1000,'(ポイント【５倍】サービス中)'],[30000,1500,'(ポイント【５倍】サービス中)'],[40000,2000,'(ポイント【５倍】サービス中)'],[50000,2500,'(ポイント【５倍】サービス中)']],
		];

		$db_data = DB::table('point_categories')->get();
		foreach($db_data as $lines){
			foreach($listPointSetting as $list_data){
				foreach($list_data as $data){
					DB::table('point_settings')->insert([
						'category_id' => $lines->id,
						'money' => $data[0],
						'point' => $data[1],
						'disp_msg' => $data[2],
						'created_at' => $now_date,
						'updated_at' => $now_date,
					]);
				}
			}
		}
    }
}

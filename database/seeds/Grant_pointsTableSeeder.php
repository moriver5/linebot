<?php

use Illuminate\Database\Seeder;

class Grant_pointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('grant_points')->insert([
			'type' => 'registed',
			'point' => 100,
			'created_at' => date("Y/m/d h:m:s"),
			'updated_at' => date("Y/m/d h:m:s"),
		]);
    }
}

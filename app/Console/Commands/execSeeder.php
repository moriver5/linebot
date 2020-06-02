<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class execSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'すべてのSeederを実行';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
		$listSeeder = [
//			'Access_logsTableSeeder',
			'AdminsTableSeeder',
			'ContentsTableSeeder',
			'Convert_tablesTableSeeder',
			'Grant_pointsTableSeeder',
			'GroupsTableSeeder',
			'Landing_pagesTableSeeder',
			'Landing_pages_contentsTableSeeder',
			'Magnification_settingsTableSeeder',
			'Mail_contentsTableSeeder',
			'Point_categoriesTableSeeder',
			'Point_logsTableSeeder',
			'PointsSeeder',
//			'UsersTableSeeder',
			'Visitor_logsTableSeeder',
			'Top_contentsTableSeeder',
			'Top_productsTableSeeder',
			'ForecastsTableSeeder',
		];
		foreach($listSeeder as $class_name){
			Artisan::call('db:seed', ['--class' => $class_name]);
		}
    }
}

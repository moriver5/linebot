<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		//
		Commands\FileUpload::class,
		Commands\AnalyticsAccessLog::class,
		Commands\AdAnalyticsAccessLog::class,
		Commands\MailDelivery::class,
		Commands\MelmagaImmediateDelivery::class,
		Commands\MelmagaReserveDelivery::class,
		Commands\MelmagaReserveSendDelivery::class,
		Commands\RegisteredSendMail::class,
		Commands\execSeeder::class,
		Commands\DelOldPersonalLog::class,
		Commands\MakeEncryptPassword::class,
		Commands\DelOldBanAccessIp::class,
		Commands\LineImmediateBroadcast::class,
		Commands\LineReserveBroadcast::class,
		Commands\LineReserveBroadcastDelivery::class,
		Commands\LineBroadcastDelivery::class,
		Commands\LineAfterRegistBroadcast::class,
		Commands\LineAfterRegistBroadcastDelivery::class,
		Commands\LineEverydayBroadcast::class,
		Commands\LineEveryWeekBroadcast::class,
		Commands\LineResetReserveBroadcast::class,
		Commands\LineReserveCarouselBroadcast::class,
		Commands\LineReserveCarouselBroadcastDelivery::class,
		Commands\LineReserve2ChoicesBroadcast::class,
		Commands\LineReserve2ChoicesBroadcastDelivery::class,
		Commands\LineReserve4ChoicesBroadcast::class,
		Commands\LineReserve4ChoicesBroadcastDelivery::class,
		Commands\LineReserveImageMapBroadcast::class,
		Commands\LineReserveImageMapBroadcastDelivery::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		// $schedule->command('inspire')
		//			->hourly();
	}

	/**
	 * Register the Closure based commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		require base_path('routes/console.php');
	}
}

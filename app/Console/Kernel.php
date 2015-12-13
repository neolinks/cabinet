<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
        'App\Console\Commands\Drivers\Shashka',
        'App\Console\Commands\Drivers\Violation',
        'App\Console\Commands\Drivers\Overmilleage',
        'App\Console\Commands\Drivers\FakeGPS',
        'App\Console\Commands\Drivers\Overshift',
        'App\Console\Commands\Drivers\NegativeBalance',
        'App\Console\Commands\Drivers\Operations',
        'App\Console\Commands\Drivers\Update',
        'App\Console\Commands\Drivers\Delete',
        'App\Console\Commands\Drivers\Create',
        'App\Console\Commands\Crews\Update',
        'App\Console\Commands\Crews\Info',
        'App\Console\Commands\Crews\Inline',
        'App\Console\Commands\Crews\Smens',
        'App\Console\Commands\Cars\Info',
        'App\Console\Commands\Cars\Update',
        'App\Console\Commands\Wialon\Cars',
        'App\Console\Commands\Wialon\Violations',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('drivers:shashka --city=1')
		         ->dailyAt('00:02');				 
		$schedule->command('drivers:negative_balance --city=1')
		         ->dailyAt('00:02');				 
		$schedule->command('drivers:overmilleage --city=1')
		         ->dailyAt('00:02');
		$schedule->command('drivers:fakeGPS --city=1')
		         ->everyFiveMinutes();	
		$schedule->command('drivers:overshift --city=1')
		         ->dailyAt('00:15');						 
		$schedule->command('drivers:update --city=1')
		         ->everyFiveMinutes();
		$schedule->command('crews:update --city=1')
		         ->everyFiveMinutes();
		$schedule->command('crews:info --city=1')
		         ->everyFiveMinutes();
		$schedule->command('crews:inline --city=1')
		         ->cron('*/2 * * * *');
		$schedule->command('crews:smens--city=1')
		         ->everyFiveMinutes();
		$schedule->command('cars:info --city=1')
		         ->cron("* */6 * * *");
		$schedule->command('cars:update --city=1')
		         ->cron("* */6 * * *");
		$schedule->command('wialon:cars --city=1')
		         ->cron('*/2 * * * *');
		$schedule->command('wialon:violations --city=1')
		         ->cron('*/3 * * * *');

	}

}

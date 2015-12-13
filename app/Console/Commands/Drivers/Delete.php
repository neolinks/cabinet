<?php namespace App\Console\Commands\Drivers;

use App\Models\Driver;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Delete extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:delete';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
	public function fire(Almaty $almaty,Astana $astana)
	{
        $city = $this->option('city');
        $driverId = $this->argument('id');
        if($city == 1){
            $this->city = $almaty;
        }else{
            $this->city = $astana;
        }

        Driver::on($this->city->db)->delete($driverId);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['id', InputArgument::REQUIRED, 'Driver ID'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['city', null, InputOption::VALUE_REQUIRED, 'Identification city.', 1],
		];
	}

}

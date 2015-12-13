<?php namespace App\Console\Commands\Drivers;

use App\Models\Driver;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Create extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:create';

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
        $driver = Driver::on($this->city->db)->find($driverId);
        if($driver != null && $driver->type == 'driver'){
            $this->call('drivers:operations',[
                'driver'=>$driver->id,
                'op'=>1,
                'summ'=>15000,
                'reason'=>'Бонусное пополнение счета',
                'time'=>time(),
                '--city'=>$city
            ]);
            $driver->is_new = 0;
            $driver->save();
        }
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

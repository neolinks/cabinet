<?php namespace App\Console\Commands\Drivers;

use App\Models\Driver;
use App\Models\Violations;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Violation extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:violation';

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
	public function fire(Almaty $almaty, Astana $astana)
	{
        $city = $this->option('city');
        if($city == 1){
            $this->city = $almaty;
        }else{
            $this->city = $astana;
        }
        if($this->city->violation == "0")
            return false;
        $start_day = strtotime('now 00:00:00');
        $end_day = strtotime('now 23:59:00');
        $violations = Violations::getUncheckedViolations($this->city->db,$start_day,$end_day);
        if(!$violations->isEmpty()){
            foreach($violations as $v){
                $driver = Driver::getDriverInfoByCar($this->city->db,$v->vio_nomer);
                if(isset($driver->driver_id)){
                    $this->call('drivers:operations',[
                        'driver'=>$driver->driver_id,
                        'op'=>0,
                        'summ'=>$this->city->violation_price,
                        'reason'=>'Штраф за превышение скорости',
                        'time'=>time(),
                        'comment'=>$v->vio_text,
                        '--city'=>$city
                    ]);
                    $v->status = 1;
                    $v->save();
                }
            }
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
			['city', null, InputOption::VALUE_REQUIRED, 'An example option.', 1],
		];
	}

}

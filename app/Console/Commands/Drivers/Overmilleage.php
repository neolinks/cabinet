<?php namespace App\Console\Commands\Drivers;

use App\Libs\Wialon;
use App\Models\Driver;
use App\Models\Overmileage;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Overmilleage extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:overmilleage';

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
        $time = time()-86400;
        $city = $this->option('city');
        if($city == 1){
            $this->city = $almaty;
        }else{
            $this->city = $astana;
        }
        if($this->city->overmilleage == "0")
            return false;

        $wialon = new Wialon($this->city);
        $wialon->get_report();
        $milleages = Overmileage::on($this->city->db)->
            where('run','=','0')->
            where('time','>',$time)->
            get();
        if(!$milleages->isEmpty()){
            foreach($milleages as $v){
                $km = $v->mileage - 350;
                $pcm = $this->city->overmilleage_price;
                $summ = $km * $pcm;
                $driver = Driver::getDriverInfoByCar($this->city->db,$v->gn);
                if(isset($driver->driver_id)){
                    $this->call('drivers:operations',[
                        'driver'=>$driver->driver_id,
                        'op'=>0,
                        'summ'=>$summ,
                        'reason'=>'Суточный перепробег',
                        'time'=>time(),
                        '--city'=>$city
                    ]);
                    $v->run = 1;
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
			['city', null, InputOption::VALUE_REQUIRED, 'Identify city.', 1],
		];
	}

}

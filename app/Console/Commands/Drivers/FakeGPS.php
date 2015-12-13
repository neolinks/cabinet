<?php namespace App\Console\Commands\Drivers;

use App\Commands\Helpers;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FakeGPS extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:fakeGPS';

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
        if($this->city->obman_gps == "0")
            return false;
        $results = DB::connection($this->city->db)->table('cars')->
            join('crews','crews.car','=','cars.id')->
            join('crews_inline as ci','crews.id','=','ci.id')->
            join('wialon_cars as wi','cars.gn','=','wi.gn')->
            where('ci.status','=','waiting')->
            select('crews.driver', 'cars.gn', 'crews.id', 'ci.status', 'wi.pos_x AS wi_lon', 'wi.pos_y AS wi_lat', 'ci.lat AS tm_lat', 'ci.lon AS tm_lon')->
            get();
        if(!empty($results)){
            foreach($results as $v){
                if(isset($v->tm_lat) && isset($v->tm_lon) && isset($v->wi_lat) && isset($v->wi_lon)){
                    $diff = Helpers::distance($v->tm_lat, $v->tm_lon, $v->wi_lat, $v->wi_lon) / 1000;
                    $diff = round($diff,1);
                    if($diff >= 4.5){
                        if(isset($v->driver)){
                            $tm_address = Helpers::get_address_by_coords($v->tm_lat,$v->tm_lon);
                            $wialon_address = Helpers::get_address_by_coords($v->wi_lat, $v->wi_lon);
                            $this->call('drivers:operations',[
                                'driver'=>$v->driver,
                                'op'=>0,
                                'summ'=>$this->city->obman_gps_price,
                                'reason'=>'Обман GPS',
                                'time'=>time(),
                                'comment'=>"ТМ Адрес: $tm_address, Wialon Адрес: $wialon_address Разница: $diff км",
                                '--city'=>$city
                            ]);
                        }
                    }
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

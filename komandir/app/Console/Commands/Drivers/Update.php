<?php namespace App\Console\Commands\Drivers;

use App\Libs\TMBase;
use App\Models\Driver;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Update extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:update';

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
        $time = time();
        $city = $this->option('city');
        if($city == 1){
            $this->city = $almaty;
        }else{
            $this->city = $astana;
        }
        $tm = new TMBase($this->city);
        $res = $tm->get_drivers_info();
        if($res->code == 0){
            foreach($res->data->drivers_info as $value){
                $driver = Driver::on($this->city->db)->find($value->driver_id);
                $type = $this->getDriverType($value->name);
                if(!isset($driver)){
                    $insertion = [
                        'id'         => $value->driver_id,
                        'name'       => $value->name,
                        'mobile'     => $value->mobile_phone,
                        'balans'	 => $value->balance,
                        'is_locked'	 => $value->is_locked,
                        'is_new'	 => 1,
                        'uptime'	 => $time,
                        'deleted'	 => 0,
                        'type'		 => $type,

                    ];
                    Driver::on($this->city->db)->insert($insertion);
                    $this->call('drivers:create',['id'=>$value->driver_id,'--city'=>$city]);
                }else{
                    $driver->name = $value->name;
                    $driver->type = $type;
                    $driver->is_locked = $value->is_locked;
                    $driver->mobile = $value->mobile_phone;
                    $driver->balans = $value->balance;
                    $driver->uptime = $time;
                    $driver->save();
                }
            }
        }
        $this->endUpdate($time);
	}
    private function endUpdate($time){
        Driver::on($this->city->db)->where('uptime','<',$time)->delete();
    }
    private function getDriverType(&$name){
        if(starts_with($name, 'Должник')){
            $name = trim(mb_substr($name,8));
            $type = 'dolg';
        }else if(starts_with($name,'База')){
            $name = trim(mb_substr($name, 5));
            $type = 'base';
        }else{
            $name = $name;
            $type = 'driver';
        }
        return $type;
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
			['city', null, InputOption::VALUE_REQUIRED, 'Identify City.', 1],
		];
	}

}

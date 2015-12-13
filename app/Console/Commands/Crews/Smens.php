<?php namespace App\Console\Commands\Crews;

use App\Libs\TMBase;
use App\Models\Crews;
use App\Models\Driver;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;
use Psy\Exception\ErrorException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Smens extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crews:smens';

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
        $start_day = strtotime('now 00:00:00');
        $end_day = strtotime('now 23:59:59');
        $insertCollection = Collection::make();
        $updateCollection = Collection::make();
        if($city == 1){
            $this->city = $almaty;
        }else{
            $this->city = $astana;
        }
        $tm = new TMBase($this->city);
        $shifts = $tm->get_shifts($start_day, $end_day);
        DB::beginTransaction();
        foreach($shifts as $v){
            $crew = Crews::on($this->city->db)->where('driver',$v->driver_id)->first();
            $insertion = [
                'id'    =>  empty($v->shift_id) ? 'NULL' : $v->shift_id,
                'driver'=>  empty($v->driver_id) ? 'NULL' : $v->driver_id,
                'begin' =>  empty($v->plan_shift_start_time) ? 'NULL' : strtotime($v->plan_shift_start_time),
                'end'   =>  empty($v->plan_shift_finish_time) ? 'NULL' : strtotime($v->plan_shift_finish_time),
                'crew'  =>  isset($crew->id) ? $crew->id : 'NULL',
                'type'  =>  empty($v->shift_state) ? 'NULL' : $v->shift_state,
                'uptime'=>  time()
            ];
            $shift = \App\Models\Smens::on($this->city->db)->find($v->shift_id);
            if(empty($shift)){
                $insertCollection->push($insertion);
                $driver = Driver::on($this->city->db)->find($v->driver_id);
                if (!empty($driver) && $v->plan_shift_cost != 1 && $this->city->transfer_to_debtors == "1"){
                    $locked_alias = Driver::getLockedDriver($this->city->db,$driver->name);
                    if(!empty($locked_alias)){
                        $summ = $locked_alias->balans > (-2000) ? abs($locked_alias->balans) : $this->city->transfer_to_debtor_price;
                            $this->call('drivers:operations',['driver'=>$driver->id,'op'=>'0','summ'=>$summ,'reason'=>'Перевод в счет долга','time'=>time(),'--city'=>$city]);
                            $this->call('drivers:operations',['driver'=>$locked_alias->id,'op'=>'1','summ'=>$summ,'reason'=>'Перевод в счет долга','time'=>time(),'--city'=>$city]);
                            $locked_a2 = Driver::on($this->city->db)->find($locked_alias->id);
                            if(isset($locked_a2) && $locked_a2->balans >=0){
                                $this->call('drivers:delete',['id'=>$locked_a2->id,'--city'=>$city]);
                            }
                    }
                }
            }
        }
        if(!$insertCollection->isEmpty())
            \App\Models\Smens::on($this->city->db)->insert($insertCollection->toArray());
        DB::commit();
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
            ['city', null, InputOption::VALUE_REQUIRED, 'The city', 1],
		];
	}

}

<?php namespace App\Console\Commands\Drivers;

use App\Commands\Helpers;
use App\Libs\Wialon;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Overshift extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:overshift';

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
        if($this->city->overshift == "0")
            return false;

        $start = microtime(true);
        $slise_size = 150;
        $crews = file_get_contents('http://api.komandir.kz/ovsh/main2.php');
        $crews = json_decode($crews,true);
        $wialon = new Wialon($this->city);
        $report_from_wialon = [];
        for($i = 0; $i < count($crews); $i += $slise_size){
            $tmp = array_slice($crews, $i, $i+$slise_size, true);
            $tmp =  $wialon->get_test_report($tmp);
            $tmp = json_decode($tmp);
            $report_from_wialon = array_merge($tmp, $report_from_wialon);
        }
        $results = [];
        foreach($report_from_wialon as $v){
            if(isset($v->reportResult->stats)){
                $cnt = str_replace(' km', '', $v->reportResult->stats[0][1]);
                if($cnt > 2){
                    $gn = substr($v->reportResult->stats[1][1],0,5);
                    array_push($results,['gn'=>$gn,'cnt'=>$cnt]);
                }
            }
        }
        $results = Helpers::merge_two_arrays($results,$crews,'gn');

        foreach($results as $v){
            if(isset($v['cnt'])){
                $driver = DB::connection($this->city->db)->table('crews_info')->where('crew_id','=',$v['id'])->get(['driver_id']);
                $sum = $v['cnt'] * $this->city->overshift_price;
                if(!empty($driver)){
                    foreach($driver as $val){
                        $this->call('drivers:operations',[
                            'driver'=>$val->driver_id,
                            'op'=>0,
                            'summ'=>$sum,
                            'reason'=>'Перепробег вне смены',
                            'time'=>time(),
                            '--city'=>$city

                        ]);
                        DB::connection($this->city->db)->table('overshift')->insert(['time'=>time(),'crews_id'=>$v['id'],'km'=>$v['cnt'],'begin'=>$v['begin_time'],'end'=>$v['end_time']]);
                        DB::connection($this->city->db)->table('overshift_called')->insert(['crew_id'=>$v['id'],'time'=>time()]);
                    }
                }

            }
        }
        $end_time = microtime(true);
        $this->info($end_time-$start);
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

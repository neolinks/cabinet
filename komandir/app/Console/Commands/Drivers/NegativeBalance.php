<?php namespace App\Console\Commands\Drivers;

use App\Models\Driver;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class NegativeBalance extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:negative_balance';

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
        if($this->city->negative_balance == "0")
            return false;

        $drivers= Driver::getDriversWithNegativeBalance($this->city->db);
        if(!empty($drivers)){
            foreach($drivers as $v){
                if($v->is_locked){
                    $cof = 0.001;
                }else if(abs($v->balans) < 2000){
                    $cof = 0.05;
                }else if(abs($v->balans) >= 2000 && abs($v->balans) < 5000){
                    $cof = 0.03;
                }else if(abs($v->balans) >= 5000 && abs($v->balans) < 7000){
                    $cof = 0.02;
                }else if(abs($v->balans) >= 7000 && abs($v->balans) < 12000){
                    $cof = 0.01;
                }else if(abs($v->balans) >= 12000){
                    $cof = 0.005;
                }
                $summ = abs($v->balans) * $cof;
                $this->call('drivers:operations',['driver'=>$v->id,'op'=>'0','summ'=>$summ,'reason'=>'Штраф за отрицательный баланс','time'=>time(),'--city'=>1]);
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
            ['city', null, InputOption::VALUE_REQUIRED, 'The city', 1],
		];
	}

}

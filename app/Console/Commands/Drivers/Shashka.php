<?php namespace App\Console\Commands\Drivers;

use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Shashka extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:shashka';

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


    protected $city;
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
        if($this->city->shashka == "0")
            return false;
        $without_shashka = DB::connection($this->city->db)->table('crews_info')->where('shashka','=','1')->get(['driver_id']);
        if(!empty($without_shashka)){
            foreach($without_shashka as $v){
                $this->call('drivers:operations',[
                    'driver'=>$v->driver_id,
                    'op'=>'0',
                    'summ'=>$this->city->shashka_price,
                    'reason'=>'Снятие за планшет',
                    'time'=>time(),
                    '--city'=>1
                ]);
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
			['example', InputArgument::OPTIONAL, 'An example argument.'],
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

<?php namespace App\Console\Commands\Wialon;

use App\Libs\Wialon;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Violations extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'wialon:violations';

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
        $insertCollection = Collection::make();
        $updateCollection = Collection::make();
        $wialon = new Wialon($this->city);
        $start_day = strtotime('now 00:00:00');
        $end_day = strtotime('now 23:59:00');
        $drivers  =  $wialon->get_drivers();
        $get_exec =  $wialon->select_exec();

        $violations = \App\Models\Violations::on($this->city->db)->whereBetween('vio_date',[$start_day, $end_day])->get(['id','vio_nomer','vio_date','updated']);

        if(!$violations->isEmpty()){
            foreach($violations as $db){
                foreach($get_exec as $key => $a){
                    $number_car = $a->r[0]->c[1];
                    $number_car = substr($number_car, 0, 5);
                    $vio = $a->r[0]->c[3]->t;
                    $date_vio_timestamp = $a->r[0]->c[2]->v;
                    if($db->vio_date == $date_vio_timestamp && $number_car == $db->vio_nomer){
                        $db->updated = date('Y-m-d H:i:s',time());
                        $db->save();
                        unset($get_exec[$key]);
                    }
                }
            }
        }
        if(!isset($get_exec->error) && !empty($get_exec)){
            foreach($get_exec as $k => $a){
                $number_car = substr($a->r[0]->c[1], 0, 5);
                $vio = $a->r[0]->c[3]->t;
                $date_vio = $a->r[0]->c[2]->v;
                $insertion = [
                    'vio_nomer'  =>  $number_car,
                    'vio_date'  =>  $date_vio,
                    'vio_text'  =>  $vio,
                    'status'  =>  0,
                    'city'  =>  $this->city->city,
                    'updated'  =>  date('Y-m-d H:i:s',time()),
                ];
                $insertCollection->push($insertion);
            }
        }
        \App\Models\Violations::on($this->city->db)->insert($insertCollection->toArray());
        $this->endUpdate($start_day);
        $this->call('drivers:violation',['--city' => $city]);
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
			['city', null, InputOption::VALUE_REQUIRED, 'Identify city', 1],
		];
	}

    private function endUpdate($start_day)
    {
        $date = date('Y-m-d H:i:s',$start_day);
        \App\Models\Violations::on($this->city->db)->where('status','=','1')->where('updated','<',$date)->delete();
    }

}

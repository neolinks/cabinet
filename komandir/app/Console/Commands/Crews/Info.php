<?php namespace App\Console\Commands\Crews;

use App\Libs\TMBase;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Info extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crews:info';

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
        $tm = new TMBase($this->city);
        $crews = $tm->get_all_crews();
        $collection = Collection::make();
        DB::beginTransaction();
        if(!empty($crews)){
            DB::connection($this->city->db)->table('crews_info')->truncate();
            foreach($crews as $v){
                $insertion = [
                    'crew_id'=>empty($v->crew_id) ? 'NULL' : $v->crew_id,
                    'code' => empty($v->code) ? 'NULL' : $v->code,
                    'name'=>empty($v->name) ? 'NULL' : $v->name,
                    'driver_id'=>empty($v->driver_id) ? 'NULL' : $v->driver_id,
                    'car_id'=>empty($v->car_id) ? 'NULL' : $v->car_id,
                    'shashka'=>empty($v->car_id) ? 'NULL' : $v->has_light_house,
                    'updated'=>date('Y-m-d H:i:s',time()),
                ];
                $collection->push($insertion);
            }
            DB::connection($this->city->db)->table('crews_info')->insert($collection->toArray());
        }
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
            ['city', null, InputOption::VALUE_REQUIRED, 'Identify city', 1],
		];
	}

}

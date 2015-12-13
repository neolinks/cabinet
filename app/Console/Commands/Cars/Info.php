<?php namespace App\Console\Commands\Cars;

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
	protected $name = 'cars:info';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update database database table `cars_info`';

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
        $cars = $tm->get_cars_info();
        $collection = Collection::make();
        if(!empty($cars)){
            DB::beginTransaction();
            DB::connection($this->city->db)->table('cars_info')->truncate();
            foreach($cars as $v){
                $insertion = [
                    'car_id'    => empty($v->car_id) ? 'NULL' : $v->car_id,
                    'code'      => empty($v->code) ? 'NULL' : $v->code,
                    'name'      => empty($v->name) ? 'NULL' : $v->name,
                    'gos_nomer' => empty($v->gos_number) ? 'NULL' : $v->gos_number,
                    'color'     => empty($v->color) ? 'NULL' : $v->color,
                    'mark'      => empty($v->mark) ? 'NULL' : $v->mark,
                    'model'     => empty($v->model) ? 'NULL' : $v->model,
                    'short_name'=> empty($v->short_name) ? 'NULL' : $v->short_name,
                    'production_year' => empty($v->production_year) ? 'NULL' : $v->production_year,
                    'is_locked' => empty($v->is_locked) ? 'NULL' : $v->is_locked,
                    'updated'=> date('Y-m-d H:i:s',time()),
                ];
                $collection->push($insertion);
            }
            DB::connection($this->city->db)->table('cars_info')->insert($collection->toArray());
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
			['city', null, InputOption::VALUE_REQUIRED, 'Identify city.', 1],
		];
	}

}

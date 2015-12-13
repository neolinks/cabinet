<?php namespace App\Console\Commands\Cars;

use App\Libs\TMBase;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Update extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cars:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update Cars Table Database.';

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
            DB::connection($this->city->db)->table('cars')->truncate();
                foreach($cars as $v){
                    $insertion = [
                        'id'=>          empty($v->car_id) ? 'NULL' : $v->car_id,
                        'code'=>        empty($v->code) ? 'NULL' : $v->code,
                        'gn' =>         empty($v->gos_number) ? 'NULL' : $v->gos_number,
                        'color' =>      empty($v->color) ? 'NULL' : $v->color,
                        'mark' =>       empty($v->mark) ? 'NULL' : $v->mark,
                        'is_locked' =>  empty($v->is_locked) ? 'NULL' : $v->is_locked,
                        'deleted' =>    0,
                        'uptime' =>     time(),
                        'last_call' =>  0
                    ];
                $collection->push($insertion);
            }
            DB::connection($this->city->db)->table('cars')->insert($collection->toArray());
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
			['city', null, InputOption::VALUE_REQUIRED, 'Identification city', 1],
		];
	}

}

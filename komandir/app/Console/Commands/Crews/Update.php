<?php namespace App\Console\Commands\Crews;

use App\Libs\TMBase;
use App\Models\Crews;
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
	protected $name = 'crews:update';

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
            DB::connection($this->city->db)->table('crews')->truncate();
            foreach($crews as $v){
                $insertion = [
                    'id'      => isset($v->crew_id) ? $v->crew_id : 0,
                    'code'    => isset($v->code) ? $v->code : 0,
                    'car'     => isset($v->car_id) ? $v->car_id : NULL,
                    'driver'  => isset($v->driver_id) ?  $v->driver_id : NULL,
                    'uptime'  => time(),
                    'deleted' => 0
                ];
                $collection->push($insertion);
            }
            DB::connection($this->city->db)->table('crews')->insert($collection->toArray());
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

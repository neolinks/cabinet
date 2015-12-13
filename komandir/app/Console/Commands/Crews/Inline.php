<?php namespace App\Console\Commands\Crews;

use App\Libs\TMBase;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Inline extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crews:inline';

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
        $crews = $tm->get_crew_coords();
        $collection = Collection::make();
        DB::beginTransaction();
        if(!empty($crews)){
            DB::connection($this->city->db)->table('crews_inline')->truncate();
            foreach($crews as $v){
                $insertion = [
                    'id'     => empty($v->crew_id) ? 'NULL' : $v->crew_id,
                    'code'   => empty($v->crew_code) ? 'NULL' : $v->crew_code,
                    'lat'    => empty($v->lat) ? 'NULL' : $v->lat,
                    'lon'    => empty($v->lon) ? 'NULL' : $v->lon,
                    'status' => empty($v->state_kind) ? 'NULL' : $v->state_kind,
                    'uptime' => time(),
                ];
                $collection->push($insertion);
            }
            DB::connection($this->city->db)->table('crews_inline')->insert($collection->toArray());
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

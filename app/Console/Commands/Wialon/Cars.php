<?php namespace App\Console\Commands\Wialon;

use App\Libs\Wialon;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Cars extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'wialon:cars';

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
        $wialon = new Wialon($this->city);
        $collection = Collection::make();
        $cars = $wialon->get_units();

        if(!empty($cars)){
            DB::beginTransaction();
            DB::connection($this->city->db)->table('wialon_cars')->truncate();
            foreach($cars as $v){
                $gn = substr($v->nm,0,5);
                $insertion = [
                    "wia_id"=>$v->id,
                    'gn'=>isset($gn) ? $gn : '',
                    'last_message'=>isset($v->pos) ? $v->pos->t : 0,
                    'pos_x'=>isset($v->pos) ? $v->pos->x : 0,
                    'pos_y'=>isset($v->pos) ? $v->pos->y : 0,
                    'uptime'=>time(),
                ];
                $collection->push($insertion);
            }
            DB::connection($this->city->db)->table('wialon_cars')->insert($collection->toArray());
            DB::commit();
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
			['city', null, InputOption::VALUE_REQUIRED, 'Identify city.', 1],
		];
	}

}

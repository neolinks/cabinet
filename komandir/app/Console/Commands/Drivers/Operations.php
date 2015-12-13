<?php namespace App\Console\Commands\Drivers;

use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Operations extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'drivers:operations';

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
        $data = [
            'driver' => $this->argument('driver'),
            'op'     => $this->argument('op'),
            'summ'   => $this->argument('summ'),
            'reason' => $this->argument('reason'),
            'time'   => $this->argument('time'),
        ];
        \App\Models\Operations::on($this->city->db)->insert($data);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['driver', InputArgument::REQUIRED, 'An example argument.'],
			['op', InputArgument::REQUIRED, 'An example argument.'],
			['summ', InputArgument::REQUIRED, 'An example argument.'],
			['reason', InputArgument::REQUIRED, 'An example argument.'],
			['time', InputArgument::REQUIRED, 'An example argument.'],
            ['comment', InputArgument::OPTIONAL,'Comment of the Operation'],
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

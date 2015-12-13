<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/9/15
 * Time: 10:34 PM
 */

namespace App\Http\Controllers;


use App\Commands\DriverCommands;

use App\Commands\Helpers;
use App\Libs\TMBase;
use App\Libs\Wialon;
use App\Models\Crews;
use App\Models\Smens;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriversController extends Controller{
    private $city;
    private $dr;
    public function __construct(Request $request, Astana $astana, Almaty $almaty){
        if($request->input('city')==1)
            $this->city = $almaty;
        else if($request->input('city') == 2)
            $this->city =$astana;
        $this->dr = new DriverCommands($this->city);
    }
    public function update()
    {
        $res = $this->dr->updateDrivers();
        return view('update',['city'=>$this->city]);
    }
    public function im(){
        $res = new Wialon($this->city);
        return view('v',['res'=>$res]);
    }
    private function getDriverType(&$name){
        if(starts_with($name, 'Должник')){
            $name = trim(mb_substr($name,8));
            $type = 'dolg';
        }else if(starts_with($name,'База')){
            $name = trim(mb_substr($name, 5));
            $type = 'base';
        }else{
            $name = $name;
            $type = 'driver';
        }
        return $type;
    }
    public function negativeBalance(){
        $day_st = strtotime(date('Y-m-d', time()));
        $has_subzero = Driver::getDriversWithNegativeBalance($this->city->db);
        foreach ($has_subzero as $v){
            $cof = $v->is_locked == 1 ? 0.001 : 0.05;
            $summ = abs($v->balans) * $cof;
            $check = DB::connection($this->city->db)->table('operations')->
                where('driver', $v->id)->
                where('reason', 'op3')->
                where('time', '>', $day_st)->
                first();
            if (empty($check))
            {
                $this->dr->MoneyOperations($v->id,0,1,'TEST ',' NEGA BALANCE'.$summ);
            }
        }
    }
    public function shashka(){
        $res = $this->dr->shashka();
        return view('v',['res'=>$res]);
    }
    public function overshift(){
        $res = $this->dr->overshift();
        return view('v',['res'=>$res]);
    }
    public function obman_gps()
    {
        $units = $this->dr->obmanGPS();
        return view('drivers',['drivers'=>$units]);
    }
    public function violation()
    {
        $violations = $this->dr->violation();
        return view('v',['res'=>$violations]);
    }
    public function overmilleage(){
        $res = $this->dr->overmileage();
        return view('v',['res'=>$res]);
    }
}
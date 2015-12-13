<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/10/15
 * Time: 9:48 PM
 */

namespace App\Http\Controllers;


use App\Commands\CrewCommands;
use App\Commands\WialonCommands;
use Illuminate\Http\Request;
use App\Services\Almaty;
use App\Services\Astana;


class CrewsController extends Controller{
    private $crewCommand;
    private $city;
    public function __construct(Request $request, Almaty $almaty, Astana $astana){
        if($request->input('city')==1)
            $this->city = $almaty;
        else if($request->input('city') == 2)
            $this->city = $astana;
        $this->crewCommand = new CrewCommands($this->city);
        $this->wialon = new WialonCommands($this->city);
    }
    public function update(){
        $res = $this->crewCommand->update();
        return view('v',['res'=>$res]);
    }
    public function violation()
    {
       $res =  $this->wialon->updateViolations();
        return view('v',['res'=>$res]);
    }
    public function updateSmens(){
        $res = $this->crewCommand->updateSmens();
        return view('v',['res'=>$res]);
    }
    public function crews_info(){
        $res = $this->crewCommand->crews_info();

        return view('v',['res'=>$res]);
    }
    public function crews_inline(){
        $res = $this->crewCommand->crew_inline();
        return view('v',['res'=>$res]);
    }
    public function update_wia_cars(){
        $res = $this->wialon->update_wialon_cars();
        return view('v',['res'=>$res]);
    }
} 
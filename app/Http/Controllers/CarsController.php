<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/17/15
 * Time: 4:27 PM
 */

namespace App\Http\Controllers;


use App\Libs\TMBase;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cars;
class CarsController extends Controller{
    public function __construct(Request $request, Almaty $almaty, Astana $astana) {
        if($request->input('city')==1)
            $this->city = $almaty;
        else if($request->input('city') == 2)
            $this->city = $astana;
        $this->tm = new TMBase($this->city);
    }
    public function update(){
        $time = time();
        $update = 0;
        $insert = 0;
        $cars = $this->tm->get_cars_info();
        if(!empty($cars)){
            foreach($cars as $v){
                $insert_data = [
                    'id'=>$v->car_id,
                    'code'=>$v->code,
                    'gn' => $v->gos_number,
                    'color' => $v->color,
                    'mark' => $v->mark,
                    'is_locked' => $v->is_locked,
                    'uptime' => $time,
                    'deleted' => 0,
                    'last_call' => 0
                ];
                $car = DB::connection($this->city->db)->table('cars')->where('id', $v->car_id)->first();

                if(!empty($car)){
                    unset($insert_data['id']);
                    DB::connection($this->city->db)->table('cars')->where('id', $v->car_id)->update($insert_data);
                    $update++;
                }else
                {
                    DB::connection($this->city->db)->table('cars')->insert($insert_data);
                    $insert++;
                }
            }
        }
        return view('v',['res'=>[$update,$insert]]);
    }
    public function cars_info(){
        $cars = $this->tm->get_cars_info();
        $query = "SELECT * FROM cars_info";

        $res = DB::connection($this->city->db)->select($query);
        foreach($cars as $v){
            $insert_data=[
                'car_id' => empty($v->car_id) ? 'NULL' : $v->car_id,
                'code' => empty($v->code) ? 'NULL' : $v->code,
                'name' => empty($v->name) ? 'NULL' : $v->name,
                'gos_nomer' => empty($v->gos_number) ? 'NULL' : $v->gos_number,
                'color' => empty($v->color) ? 'NULL' : $v->color,
                'mark' => empty($v->mark) ? 'NULL' : $v->mark,
                'model' => empty($v->model) ? 'NULL' : $v->model,
                'short_name' => empty($v->short_name) ? 'NULL' : $v->short_name,
                'production_year' => empty($v->production_year) ? 'NULL' : $v->production_year,
                'is_locked' => empty($v->is_locked) ? 'NULL' : $v->is_locked,
                'updated'=> "NOW()",
            ];
            $car = DB::connection($this->city->db)->table('cars_info')->where('car_id', $v->car_id)->first();
            if(!empty($car)){
                DB::connection($this->city->db)->table('cars_info')->where('car_id', $v->car_id)->update($insert_data);
            }else
            {
                DB::connection($this->city->db)->table('cars_info')->insert($insert_data);
            }
        }
        return view('v',['res'=>$res]);
    }
} 
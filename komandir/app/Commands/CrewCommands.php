<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/10/15
 * Time: 5:50 PM
 */

namespace App\Commands;


use App\Libs\TMBase;
use Illuminate\Support\Facades\DB;
use App\Models\Crews;
use App\Models\Driver;
class CrewCommands {
    private $time;
    private $tm;
    private $city;
    private $start_day;
    private $end_day;
    public function __construct($city){
        $this->time = time();
        $this->tm = new TMBase($city);
        $this->city = $city;
        $this->start_day = strtotime('now 00:00:00');
        $this->end_day = strtotime('now 23:59:59');
        $this->dr = new DriverCommands($city);
    }
    public function update(){
        $res_from_tm = $this->tm->get_all_crews();
        $time = time();
        DB::connection($this->city->db)->table('crews')->truncate();
        foreach($res_from_tm as $v){
            $insert_data = [
                'id'=>$v->crew_id,
                'code'=>$v->code,
                'car'=>$v->car_id,
                'driver'=>$v->driver_id,
                'uptime'=>$time,
                'deleted'=> 0
            ];
            $crew = DB::connection($this->city->db)->table('crews')->where('id', $v->crew_id)->first();
            if (!empty($crew))
            {
                unset($insert_data['id']);
                DB::connection($this->city->db)->table('crews')->where('id', $v->crew_id)->update($insert_data);
            }
            else
            {
                DB::connection($this->city->db)->table('crews')->insert($insert_data);
            }
        }
        return $res_from_tm;
    }
    public function crew_inline(){
        $res = $this->tm->get_crew_coords();
        DB::connection($this->city->db)->table('crews_inline')->truncate();
        foreach($res as $v){
            $insert_data = [
                'crew_id'=>$v->crew_id,
                'lat'=>$v->lat,
                'lon'=>$v->lon,
                'status'=>$v->state_kind,
                'uptime'=>time(),
            ];
            DB::connection($this->city->db)->table('crews_inline')->insert($insert_data);
        }
        return $res;
    }
    public function updateSmens(){
        $res = $this->tm->get_shifts($this->start_day, $this->end_day);
        $time = time();
        foreach($res as $v){
            $crew = DB::connection($this->city->db)->table('crews')->where('driver',$v->driver_id)->first();
            $insert_data = [
                'id'=>$v->shift_id,
                'driver'=>$v->driver_id,
                'begin'=>strtotime($v->plan_shift_start_time),
                'end'=>strtotime($v->plan_shift_finish_time),
                'crew'=>isset($crew->id) ? $crew->id : 0,
                'type'=>$v->shift_state,
                'uptime'=>$time
            ];
            $smen = DB::connection($this->city->db)->table('smens')->where('id',$v->shift_id)->first();
            if(!empty($smen)){
                unset($insert_data['id']);
                DB::connection($this->city->db)->table('smens')->where('id', $v->shift_id)->update($insert_data);
            }else{
                DB::connection($this->city->db)->table('smens')->insert($insert_data);
                $buy_driver = Driver::on($this->city->db)->find($v->driver_id);
                if (!empty($buy_driver) && $v->plan_shift_cost != 1){
                    $locked_alias = Driver::on($this->city->db)->
                        where('name',$buy_driver->name)->
                        where('type','dolg')->
                        where('balans','<','0')->first();
                    if(!empty($locked_alias)){
                        $summ = $locked_alias->balans > (-2000) ? abs($locked_alias->balans) : 2000;

                        $this->dr->MoneyOperations($buy_driver->id,0,1,'TEST','TEST TRANSFER TO DEB'.$summ);
                        $this->dr->MoneyOperations($locked_alias->id,1,1,'TEST','TEST TRANSFER TO DEB'.$summ);
                        $locked_a2 = Driver::find($locked_alias->id);
                        if(isset($locked_a2) && $locked_a2->balans >=0){
                            $this->dr->deleteDriver($locked_a2->id);
                        }
                    }
                }
            }
        }
        return $res;
    }
    public function crews_info()
    {
        DB::connection($this->city->db)->table('crews_info')->truncate();
        $res = $this->tm->get_all_crews();
        foreach($res as $v){
            $insert_data = [
                'crew_id'=>empty($v->crew_id) ? 'NULL' : $v->crew_id,
                'code' => empty($v->code) ? 'NULL' : $v->code,
                'name'=>empty($v->name) ? 'NULL' : $v->name,
                'driver_id'=>empty($v->driver_id) ? 'NULL' : $v->driver_id,
                'car_id'=>empty($v->car_id) ? 'NULL' : $v->car_id,
                'shashka'=>empty($v->car_id) ? 'NULL' : $v->has_light_house,
            ];
            $crew = DB::connection($this->city->db)->table('crews_info')->where('crew_id',$v->crew_id)->first();
            if(!empty($crew)){
                DB::connection($this->city->db)->table('crews_info')->where('crew_id',$v->crew_id)->update($insert_data);
            }else{
                DB::connection($this->city->db)->table('crews_info')->where('crew_id',$v->crew_id)->insert($insert_data);
            }
        }
        return $res;
    } //done it on artisan
} 
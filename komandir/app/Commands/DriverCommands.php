<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/10/15
 * Time: 5:49 PM
 */

namespace App\Commands;


use App\Models\Driver;
use App\Libs\TMBase;
use App\Libs\Wialon;
use Illuminate\Support\Facades\DB;
use App\Models\Crews;
use App\Models\Violations;
class DriverCommands {
    protected $tm, $wialon, $city,$start_day,$end_day;
    public function __construct($city){
        $this->city = $city;
        $this->tm = new TMBase($city);
        $this->wialon = new Wialon($city);
        $this->start_day = strtotime('now 00:00:00');
        $this->end_day = strtotime('now 23:59:59');
    }
    public function get_milleage(){
        $res =  $this->wialon->get_milleage_report();
        if(!empty($res)){
            foreach($res as $v){
                $insert_data = [
                    'gn' => substr($v->c[1],0,5),
                    'begin'=>$v->t1,
                    'end'=>$v->t2,
                    'milleage'=>$v->c[2],
                ];
                DB::connection($this->city->db)->table('milleage')->insert($insert_data);
            }
        }
        return $res;
    }
    public function updateDrivers(){
        $res = $this->tm->get_drivers_info();
        $time = time();
        if($res->code == 0){
            foreach($res->data->drivers_info as  $value){
                $driver = DB::connection($this->city->db)->table('drivers')->where('id',$value->driver_id)->first();

                if(starts_with($value->name, 'Должник')){
                    $name = trim(mb_substr($value->name,8));
                    $type = 'dolg';
                }else if(starts_with($value->name,'База')){
                    $name = trim(mb_substr($value->name, 5));
                    $type = 'base';
                }else{
                    $name = $value->name;
                    $type = 'driver';
                }
                if(empty($driver)){
                    $insertion = [
                        'id'         => $value->driver_id,
                        'name'       => $name,
                        'mobile'     => $value->mobile_phone,
                        'balans'	 => $value->balance,
                        'is_locked'	 => $value->is_locked,
                        'is_new'	 => 1,
                        'uptime'	 => $time,
                        'deleted'	 => 0,
                        'type'		 => $type,

                    ];
                    DB::connection($this->city->db)->table('drivers')->insert($insertion);
                    $this->newDriver($value->driver_id);
                }else{
                    $update = [
                        'uptime'=>$time,
                        'deleted'=>0,
                        'balans'=>$value->balance
                    ];
                    if (
                        $name != $driver->name || $value->mobile_phone != $driver->mobile || $value->is_locked != $driver->is_locked || $type != $driver->type
                    ){
                        $up_arr['name'] = $name;
                        $up_arr['type'] = $type;
                        $up_arr['is_locked'] = $value->is_locked;
                        $up_arr['mobile'] = $value->mobile_phone;
                    }
                    DB::connection($this->city->db)->table('drivers')->where('id', $driver->id)->update($update);
                }
            }
        }
    }

    public function MoneyOperations($id,$sum,$oper_type,$reason='',$comment='')
    {
        $driver = Driver::on($this->city->db)->find($id);
        if(!empty($driver)){
            $driver->balans = $oper_type==0 ? $driver->balans - $sum : $driver->balans+ $sum;
            $driver->save();
            $idata = [
                'driver'=>$id,
                'op'=>$oper_type,
                'summ'=>$sum,
                'reason'=>$reason,
                'time'=>time()
            ];
            DB::connection($this->city->db)->table('operations')->insert($idata);

        }
    }
    private function newDriver($id){
        $driver = Driver::on($this->city->db)->find($id);
        $driver->is_new = 0;
        $driver->save();
        if ($driver->type == 'driver')
        {
            //$this->MoneyOperations($id, 15000, 1, 'op2');
        }
    }
    public function overshift()
    {
        //return  $this->test();
        $crews = DB::connection($this->city->db)->select("SELECT crews.id, cars.gn FROM  `crews` INNER JOIN  `cars` ON crews.car = cars.id");
        $crews = $this->rebuildCrews($crews);
        $crews = $this->buildPeriods($crews);

        $ps_res = [];
        $res = [];
        foreach($crews as $v){
            $i = 0;
            if(isset($v->ps)){
                foreach($v->ps as $ps){

                    $ps_res = ['car_id'=>$v->wi,'begin_time'=>$ps['begin'],'end_time'=>$ps['end'],'i'=>$i];
                    $g = $this->wialon->get_c_report($ps_res);

                    $cnt = (float) str_replace(' km', '', $g[1]->reportResult->stats[0][1]);

                    if($cnt > 2){
                        $res[$v->id][] = ['id'=>$v->id,'cnt'=>$cnt,'ps'=>$ps,'code'=>$v->gn];
                    }
                    $i++;
                }
            }

        }

        return $res;
    }
    public function violation()
    {
        $violations = Violations::getUncheckedViolations($this->city->db,$this->start_day,$this->end_day);
        foreach($violations as $v){
            $driver = Driver::getDriverInfoByCar($this->city->db,$v->vio_nomer);
            if(isset($driver->driver_id)){
                 $this->MoneyOperations($driver->driver_id,1,0,'TEST','TEST VIOLATION');
                 $v->status = 1;
                 $v->save();
            }
        }
        return $violations;
    }
    public function overmileage()
    {
        $time = time()-86400;
        $this->wialon->get_report();
        $res = DB::connection($this->city->db)->table('overmileage')->
            where('run','=','0')->
            where('time','>',$time)->
            get();
        foreach($res as $v){
            $km = $v->mileage - 350;
            $pcm = 20;
            $summ = $km * $pcm;

            $driver = Driver::getDriverInfoByCar($this->city->db,$v->gn);
            if(isset($driver->driver_id)){
               // $v->driver_id = $driver->driver_id;
                $this->tm->driver_operation($driver->driver_id,0,1,'TEST','TEST OVERMILEAGE'.$summ);
            }
        }
        $q = 'UPDATE `overmileage` SET `run`=1 WHERE `time`>'.(time() - 86400);
        DB::connection($this->city->db)->update($q);
        return $res;
    }
    public function shashka(){
        $res = DB::on($this->city->db)->table('crews_info')->where('shashka','=','1')->get(['driver_id']);
        foreach($res as $v){
            $this->tm->driver_operation($v->driver_id,0,1,'TEST','TEST SHASHKA');
        }
    }
    public function obmanGPS()
    {
        $results = DB::connection($this->city->db)->table('cars')->
            join('crews','crews.car','=','cars.id')->
            join('crews_inline as ci','crews.id','=','ci.crew_id')->
            join('wialon_cars as wi','cars.gn','=','wi.gn')->
            where('ci.status','=','waiting')->
            select('crews.driver', 'cars.gn', 'crews.id', 'ci.status', 'wi.pos_x AS wi_lon', 'wi.pos_y AS wi_lat', 'ci.lat AS tm_lat', 'ci.lon AS tm_lon')->
            get();
        foreach($results as $v){
            if(isset($v->tm_lat) && isset($v->tm_lon) && isset($v->wi_lat) && isset($v->wi_lon)){
                $diff = Helpers::distance($v->tm_lat, $v->tm_lon, $v->wi_lat, $v->wi_lon) / 1000;
                if($diff>4.5){
                    $id = $v->driver;
                    $this->MoneyOperations($id,0,1,'TEST','TEST OBMAN GPS');
                }
            }
        }
    }



    private function getSmens(){
        $res = DB::connection($this->city->db)->table('smens')->
                whereBetween('smens.begin',[$this->start_day-86400,$this->start_day])->
                whereBetween('smens.end',[$this->start_day-86400,$this->start_day])->
                where('type','<>',"failed")->
                get(['crew','begin','end']);

        if(!empty($res)){
            foreach($res as $v){
                $v->begin = ($v->begin <= $this->start_day-86400) ? $this->start_day-86400 : ($v->begin - (3600));
                switch (date('H', $v->end))
                {
                    case '09':
                    case '10':
                        $v->end += (3600 * 4);
                        break;
                    default:
                        $v->end = $v->end  >= $this->start_day ? $this->start_day : ($v->end  + (3600 * 2));
                }
            }
        }

        $set = [];
        foreach($res as $k){
            $set[$k->crew] = $k;
        }
        return $set;
    }
    private function rebuildCrews($crews){
        $delete_query = "DELETE FROM `overshift_called` WHERE `time` < ?";
        DB::connection($this->city->db)->delete($delete_query,[$this->start_day-86400]);
        if($crews){
            $tmp = [];
            foreach ($crews as $v)
            {
                $tmp[$v->id] = $v;
            }
            $crews = $tmp;
            unset($tmp);
            $select_query = "SELECT crew_id FROM `overshift_called`";
            $called_crews = DB::connection($this->city->db)->select($select_query);
            foreach($called_crews as $v){
                array_except($crews,$v->crew_id);
            }
            $q_str = "SELECT wia_id, gn FROM wialon_cars";
            $wialon_cars = DB::connection($this->city->db)->select($q_str);
            $wia_cars = [];
            foreach($wialon_cars as $w){
                $wia_cars[$w->gn]=$w->wia_id;
            }
            unset($wialon_cars);

            foreach($crews as $k=>$c){
                if(!isset($wia_cars[$c->gn])){
                    $crews = array_except($crews, array($k));
                }else{
                    $c->wi = $wia_cars[$c->gn];
                }
            }

            if(empty($crews)){
                die('All crews is called ');
            }
            return $crews;
        }
        else{
            return false;
        }

    }
    public function getException($crew){
        $query = "SELECT begin,end From exc where gn = ? AND (end > ? OR end=0)";
        $exception = DB::connection($this->city->db)->select($query, [$crew->gn,$this->start_day-86400]);
        $ret = [];
        if(empty($exception)){
            return false;
        }else{
            foreach($exception as $k => $v){
                $ret['begin'] = $v->begin <= $this->start_day-86400 ? $this->start_day-86400  : $v->begin;
                $ret['end'] = ($v->end >= $this->start_day or $v->end == 0) ? $this->start_day : $v->end;
            }
        }
        return $ret;
    }
    private function buildPeriods($crews){
        $shifts = $this->getSmens();
        foreach($crews as $k => &$v){
            if(array_key_exists($v->id,$shifts)){
                $crews[$k]->sm = $shifts[$v->id];
            }
            $re2 = $this->getException($v);
            if($re2)
                $crews[$k]->sm = $re2;
        }

        $flags = [];
        foreach($crews as $v){
            if(!empty($v->sm)){
                $flags[$v->id] = [];
                for($i = ($this->start_day-86400); $i <= $this->start_day; $i += 60){
                    if(!isset($flags[$v->id][$i])){
                        $flags[$v->id][$i] = true;
                    }
                    {
                        if($i > ($v->sm->begin) && $i <= ($v->sm->end)){
                            $flags[$v->id][$i] = false;
                        }
                    }
                }
            }
            else{
                for($i = ($this->start_day-86400); $i<= $this->start_day; $i += 60){
                    if(!isset($flags[$v->id][$i])){
                        $flags[$v->id][$i] = true;
                    }
                }
            }
        }

        $xor_per = [];

        foreach($flags as $k => $v){
            $l1 = 0;
            $l2 = 0;
            foreach($flags[$k] as $k1 => $v1){
                if ((!isset($flags[$k][$k1 - 60]) || !$flags[$k][$k1 - 60]) && $v1)
                {
                    $xor_per[$k][$l1]['begin'] = $k1;
                    $l1++;
                }
                if ((!isset($flags[$k][$k1 + 60]) || !$flags[$k][$k1 + 60]) && $v1)
                {
                    /**
                     * Если время в итерации равно времени конца суток
                     *  укажем концом периода замера конец суток
                     *  иначе нахерато добавим минуту
                     */
                    $xor_per[$k][$l2]['end'] = ($k1 == $this->start_day) ? $this->start_day : $k1 + 60;
                    $l2++;
                }
            }
            if (isset($xor_per[$k]))
            {
                $crews[$k]->ps = $xor_per[$k];
                unset($xor_per[$k]);
            }
            else
            {
                array_except($crews,$k);
            }
        }
        return $crews;
    }

    public function deleteDriver($id)
    {
        $driver = Driver::on($this->city->db)->find($id);
        $driver->deleted = time();
        $driver->save();
    }
    private function test(){

        return $crews= DB::connection($this->city->db)->table('crews')->
                leftJoin('cars','crews.car','=','cars.id')->
                leftJoin('wialon_cars','cars.gn','=','wialon_cars.gn')->
                leftJoin('smens',function($join){
                    $join->on('crews.id','=','smens.id')->
                        where('smens.begin','>=',(string)$this->start_day-86400)->
                        where('smens.begin','<=',(string)$this->start_day)->
                        where('smens.end','>=',(string)$this->start_day-86400)->
                        where('smens.end','<=',(string)$this->start_day)->
                        where('type','<>','failed');
                })->
                whereNotIn('crews.id',function($q){
                    $q->select('crew_id')->from('overshift_called');
                })->
                whereNotNull('wialon_cars.wia_id')->select(['cars.gn','crews.id as crew','wialon_cars.wia_id','smens.begin','smens.end'])->
                get(['cars.gn','crews.id as crew','wialon_cars.wia_id','smens.begin','smens.end']);
    }

}
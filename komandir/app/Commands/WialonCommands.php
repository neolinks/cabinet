<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/10/15
 * Time: 5:48 PM
 */

namespace App\Commands;
use App\Libs\Wialon;
use Illuminate\Support\Facades\DB;
class WialonCommands {
    private $time;
    private $start_day;
    private $end_day;
    private $city;
    private $wialon;
    public function __construct($city){
        $this->time = time();
        $this->start_day = strtotime('now 00:00:00');
        $this->end_day = strtotime('now 23:59:59');
        $this->city = $city;
        $this->wialon = new Wialon($this->city);
    }
    public function updateViolations(){
        $drivers = $res = $this->wialon->get_drivers();
        $get_exec = $this->wialon->select_exec();
        $query = "select * from violation where vio_date between ? and ?";
        $violations = DB::connection($this->city->db)->select($query,[$this->start_day,$this->end_day]);
        $insert = 0;
        $update = 0;
        if(!empty($violations)){
            foreach($violations as $k => $vs){
                $db_date = $vs->vio_date;
                $db_text = $vs->vio_text;
                foreach($get_exec as $key => $a){
                    $number_car = $a->r[0]->c[1];
                    $number_car = substr($number_car, 0, 5);
                    $vio = $a->r[0]->c[3]->t;
                    $date_vio_timestamp = $a->r[0]->c[2]->v;
                    $date_vio = $date_vio_timestamp;
                    if($db_date == $date_vio_timestamp && $number_car == $vs->vio_nomer){
                        $query = "UPDATE `violation` SET `updated`= NOW() WHERE id = ?";
                        DB::connection($this->city->db)->update($query,[$vs->id]);
                        unset($get_exec[$key]);
                        $update++;
                    }
                }
            }
            foreach ($get_exec as $key => $a) {
                $number_car = substr($a->r[0]->c[1], 0, 5);
                $vio = $a->r[0]->c[3]->t;
                $date_vio = $a->r[0]->c[2]->v;
                $time = time();
                $insert++;
                $query = 'INSERT INTO `violation` (vio_nomer, vio_date, vio_text, status, city, updated) VALUES (?,?,?,?,?,?)';
                DB::connection($this->city->db)->insert($query,[$number_car,$date_vio,$vio,0,$this->city->city,$time]);
            }
        }else{
            if(!isset($get_exec->error)){
                foreach($get_exec as $k => $a){
                    $number_car = substr($a->r[0]->c[1], 0, 5);
                    $vio = $a->r[0]->c[3]->t;
                    $date_vio = $a->r[0]->c[2]->v;
                    $time = time();
                    $insert++;
                    $query = 'INSERT INTO violation (vio_nomer, vio_date, vio_text, status, city, updated) VALUES (?,?,?,?,?,?)';
                    DB::connection(    $this->city->db)->insert($query,[$number_car,$date_vio,$vio,0,$this->city->city,$time]);
                }
            }
        }
        return [$insert, $update];
    }
    public function update_wialon_cars(){
        $wia_cars = $this->wialon->get_units();
        DB::connection($this->city->db)->table('wialon_cars')->truncate();
        foreach($wia_cars as $v){
            $gn = substr($v->nm,0,5);
            $insert_data = [
                "wia_id"=>$v->id,
                'gn'=>$gn,
                'last_message'=>isset($v->pos) ? $v->pos->t : 0,
                'pos_x'=>isset($v->pos) ? $v->pos->x : 0,
                'pos_y'=>isset($v->pos) ? $v->pos->y : 0,
                'uptime'=>time(),
            ];
            DB::connection($this->city->db)->table('wialon_cars')->insert($insert_data);
        }
    }
} 
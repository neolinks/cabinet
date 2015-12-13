<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/10/15
 * Time: 4:44 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Driver extends Model{
    protected $table = 'drivers';
    public $timestamps = false;

    public static function getDriversWithNegativeBalance($db){
        return self::on($db)->
            where('deleted','=','0')->
            where('type','=','driver')->
            where('balans', '<', 0)->
            get(['id', 'balans', 'is_locked']);
    }

    public static function getDriverInfoByCar($db,$car_number){
        return DB::connection($db)->table('crews_info')
            ->join('cars_info','crews_info.car_id','=','cars_info.car_id')
            ->where('cars_info.gos_nomer','=',$car_number)->first();
    }
    public static function getLockedDriver($db,$name){
        return self::on($db)->
            where('name',$name)->
            where('deleted','=','0')->
            where('type','dolg')->
            where('balans','<','0')->
            first();
    }
} 
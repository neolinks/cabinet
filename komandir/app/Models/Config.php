<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/22/15
 * Time: 8:42 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model{
    protected $table = 'config';
    public $timestamps = false;

    public static function getPricesConfig($db){
        return self::on($db)->where('group_id','=','2')->get(['id','group_id','prop','value','display_name']);
    }
    public static function getEngineConfig($db){
        return self::on($db)->where('group_id','=','1')->get(['id','group_id','prop','value','display_name']);
    }
    public static function getOtherConfig($db){
        return self::on($db)->where('group_id','=','3')->get(['id','group_id','prop','value','display_name']);
    }
    public static function getConfig($db,$key){
        return self::on($db)->where('prop','=',$key)->first();
    }
} 
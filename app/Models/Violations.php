<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/22/15
 * Time: 11:30 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Violations extends Model{
    protected $table = 'violation';
    public $timestamps = false;

    public static function getUncheckedViolations($db,$begin_of_day,$end_of_day){
        return Violations::on($db)->
            where('status','=','0')->
            whereBetween('vio_date',[$begin_of_day,$end_of_day])->
            get();
    }
} 
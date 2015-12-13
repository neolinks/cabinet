<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/12/15
 * Time: 6:20 PM
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;

class Almaty {
    public $city = 'Almaty';
    public $db = 'almaty';
    public $firebird = 'firebird_almaty';
    public $index = 1;
    
    public $vio_q_params = [
        'rrid' => 193207,
        'rtid' => 4,
        'roid' => 10724413
    ];
    public $ovsh_q_params = [
        'rrid' => 193207,
        'rtid' => 15,
    ];
    public $overmilleage_q_params = [
        'rrid' => 193207,
        'rtid' => 13,
        'roid' => 10724413,
        'max' => 350,
    ];
    public static $conf_arr = [];
    public function __construct(){
        $this->wiauser = self::getConfig('wiauser')->value;
        $this->wiapass = self::getConfig('wiapass')->value;
        $this->tm_host = self::getConfig('tm_host')->value;
        $this->tm_port = self::getConfig('tm_port')->value;
        $this->secret_key = self::getConfig('secret_key')->value;
        $this->violation = self::getConfig('violation')->value;
        $this->overmilleage = self::getConfig('overmilleage')->value;
        $this->shashka = self::getConfig('shaska')->value;
        $this->obman_gps = self::getConfig('obman_gps')->value;
        $this->overshift = self::getConfig('overshift')->value;
        $this->negative_balance = self::getConfig('negative_balance')->value;
        $this->transfer_to_debtors = self::getConfig('transfer_to_debtors')->value;
        $this->violation_price = self::getConfig('violation_price')->value;
        $this->shashka_price = self::getConfig('shashka_price')->value;
        $this->transfer_to_debtor_price = self::getConfig('transfer_to_debtor_price')->value;
        $this->overshift_price = self::getConfig('overshift_price')->value;
        $this->obman_gps_price = self::getConfig('obman_gps_price')->value;
        $this->overmilleage_price = self::getConfig('overmilleage_price')->value;
        $this->wiatoken = self::getConfig('wia_token')->value;
    }
    private static function getConfig($key){
        $config = DB::table('config')->where('prop','=',$key)->first();
        self::$conf_arr[$config->prop] = $config->value;
        return $config;
    }
} 
<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/12/15
 * Time: 6:21 PM
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
class Astana{

    public $city = 'Astana';
    public $db = 'astana';
    public $firebird = 'firebird_astana';
    public $index = 2;

    public $vio_q_params = [
        'rrid'	 => 5967536,
        'rtid'	 => 4,
        'roid'	 => 6847118
    ];
    public $ovsh_q_params = [
        'rrid'	 => 5967536,
        'rtid'	 => 5,
    ];
    public $overmilleage_q_params = [
        'rrid'	 => 5967536,
        'rtid'	 => 3,
        'roid'	 => 6847118,
        'max'    => 350,
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
        $config = DB::connection('astana')->table('config')->where('prop','=',$key)->first();
        self::$conf_arr[$config->prop] = $config->value;
        return $config;
    }
} 
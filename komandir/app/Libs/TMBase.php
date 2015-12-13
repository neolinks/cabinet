<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/9/15
 * Time: 10:14 PM
 */

namespace App\Libs;


class TMBase {
    private $host;
    private $port;
    private $secret_key;

    public function __construct($city){
        $this->city = $city;
        $this->host = $city->tm_host;
        $this->port = $city->tm_port;
        $this->secret_key = $city->secret_key;
    }
    private function call_get($method, $params=false){

        if($params!=false)
            $q_p=http_build_query($params);
        else
            $q_p = false;

        $ch = curl_init($this->host);
        $uri = $this->host.':'.$this->port. '/common_api/1.0/' . $method . '?' . $q_p;
        $headers = [
            'Signature: ' . md5($q_p . $this->secret_key),
            'Content-Type: application/x-www-from-urlencoded',
        ];
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);
        return !empty($response) ? $response : false;
    }
    public function test(){
        $q = [
            'driver_id'=>224,
            'oper_sum'=>1.0,
            'oper_type'=>'expense',
        ];
        return $this->call_post('create_driver_operation',$q);
    }
    public function driver_operation($id, $summ, $type, $name, $comment='')
    {
        $q_p = [
            'driver_id'	 => $id,
            'oper_sum'	 => $summ,
            'oper_type'	 => $type == 0 ? 'expense' : 'receipt',
            'name'		 => $name,
            'comment'	 => $comment,
        ];
        $ret = $this->call_post('create_driver_operation', $q_p);
        return $ret;
    }

    private function call_post($method, $params = false)
    {
        $q_p = json_encode($params);
        $ch = curl_init();
        $uri = $this->host.':'.$this->port . '/common_api/1.0/' . $method;
        $headers = [
            'Signature: ' . md5($q_p . $this->secret_key),
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $q_p);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return !empty($response) ? $response : false;
    }
    public function get_all_crews(){
        $a = [
            'not_working_crews'=>'true'
        ];
        return json_decode($this->call_get('get_crews_info',$a))->data->crews_info;
    }
    public function get_online_crews(){
        $a = [
            'not_working_crews'=>'false'
        ];
        return json_decode($this->call_get('get_crews_info'));
    }
    public function get_drivers_info(){
        return json_decode($this->call_get('get_drivers_info',['locked_drivers'=>'true']));
    }
    public function get_cars_info(){
        $a = [
            'locked_cars'=>'true',
        ];
        return json_decode($this->call_get('get_cars_info',$a))->data->crews_info;
    }
    public function get_crew_coords(){
        $a = [];
        return json_decode($this->call_get('get_crews_coords'))->data->crews_coords;
    }
    public function get_shifts($start_time,$end_time){
        $a = [
            'start_time'=>date('YmdHis',$start_time),
            'finish_time'=>date('YmdHis',$end_time),
        ];
        return json_decode($this->call_get('get_driver_shifts',$a))->data->shifts;
    }
    public function get_driver_info($driver_id){
        $a = [
            'need_photo'=>'false'
        ];
        $res = $this->call_get('get_driver_info',$a);

        if(json_decode($res)->code !==0){
            return false;
        }else{
            $res = $res->data;
        }
        return $res;
    }
    public function update_order(array $params){
        return json_decode($this->call_post('update_order',$params));
    }

    public function get_shift_plans($begin, $end)
    {
        $data = [
            'start_time'=>$begin,
            'finish_time'=>$end,
        ];
        return json_decode($this->call_get('get_driver_plan_shifts',$data));
    }
} 
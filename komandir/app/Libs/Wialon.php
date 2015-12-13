<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/9/15
 * Time: 10:25 PM
 */

namespace App\Libs;


use Illuminate\Support\Facades\DB;
class Wialon{
    private $wiahost = 'https://hst-api.wialon.com/wialon/ajax.html';
    private $wiauser;
    private $wiapass;
    private $sid;
    private $city;
    public function __construct($city)
    {
        $this->wiauser = $city->wiauser;
        $this->wiapass = $city->wiapass;
        $this->token = $city->wiatoken;
        $this->city = $city;
        $auth = self::login();
        if (!$auth)
        {
            print('auth error');
        }
        else
        {
            $this->sid = $auth['sid'];
        }
    }
    public function test(){
        $curl = curl_init($this->wiahost);

        $params = '{token:"' . $this->token .'"}';

        $data = 'svc=token/login&params=' . $params;
        curl_setopt($curl, CURLOPT_URL, $this->wiahost . '?' . $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/x-www-form-urlencode'
            ));
        $out = json_decode(curl_exec($curl));

        return $out;
    }
    public function destruct()
    {
        self::get_wia('core/logout', '{}', $this->sid);
    }
    public function get_unit($id){
        $params = "{'id' : $id,'flags':'4194305'}";
        $r = json_decode(self::get_wia('core/search_items',$params,$this->sid));
    }
    public function get_units(){
        $params = '{"spec":{"itemsType":"avl_unit","propName":"sys_id","propValueMask":"*","sortType":"sys_id","propName":"list"},"force":1,"flags":4194305,"from":0,"to":0}';
        $r = json_decode(self::get_wia('core/search_items', $params, $this->sid))->items;
        return $r;
    }
    private function login()
    {
        $params = '{token:"' . $this->token .'"}';
        $r = json_decode(self::get_wia('token/login', $params));
        if (isset($r->eid))
        {
            $data = array('error' => false, 'sid' => $r->eid);
        }
        else
        {
            $data['error'] = true;
        }
        unset($r);
        return $data;
    }

    private function get_wia($func, $params, $sid = false)
    {
        $params = str_replace(' ', '', $params);
        $params = str_replace("\n", '', $params);
        $params = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '),
            '', $params);
        $curl = curl_init($this->wiahost);
        if ($sid == false)
        {
            $data = 'svc=' . $func . '&params=' . $params;
        }
        else
        {
            $data = 'svc=' . $func . '&params=' . $params . '&sid=' . $sid;
        }
        curl_setopt($curl, CURLOPT_URL, $this->wiahost . '?' . $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/x-www-form-urlencode'
            ));
        $out = curl_exec($curl);
        curl_close($curl);
        unset($curl);
        return $out;
    }

    public function get_drivers()
    {
        $params = '{"spec":{"itemsType":"avl_resource","propName":"sys_name","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":256,"from":0,"to":0}';
        $r = json_decode(self::get_wia('core/search_items', $params, $this->sid))->items;
        return $r;
    }

    //получаю список всех отчетов
    public function get_exec()
    {
        $params = '{"spec":{"itemsType":"avl_resource","propName":"reporttemplates","propValueMask":"*","sortType":"reporttemplates"},"force":1,"flags":0x3FFFFFFFFFFFFFFF,"from":0,"to":0}';
        $r = json_decode(self::get_wia('core/search_items', $params, $this->sid))->items;
        return $r;
    }
    public function get_c_report(array $data)
    {
        $end_time = $data["end_time"];
        $start_time = $data["begin_time"];
        $end_time = $end_time - 1;

        $params = '
			{
				"params":[
				   {
					  "svc":"report/cleanup_result",
					  "params":{

					  }
				   },
				   {
					  "svc":"report/exec_report",
					  "params":{
						 "reportResourceId":' . $this->city->ovsh_q_params['rrid'] . ',
						 "reportTemplateId":' . $this->city->ovsh_q_params['rtid'] . ',
						 "reportTemplate":null,
						 "reportObjectId":' . $data['car_id'] . ',
						 "reportObjectSecId":0,
						 "interval":{
							"flags":0,
							"from":' . $start_time . ',
							"to":' . $end_time . '
						 }
					  }
				   }
				],
				"flags":0
			 }';
        $vowels = array("\n", "\s", "\t");
        $params = str_replace($vowels, "", $params);
        $r = json_decode(self::get_wia('core/batch', $params, $this->sid));
        return $r;
    }
    public function get_test_report(array $data){
    $params = '{
        "params":[
            {
            "svc":"report/cleanup_result",
            "params":{}
            },';
            foreach($data as $v){
                $end_time = $v["end_time"];
                $start_time = $v["begin_time"];
                $end_time-=1;
                $params.='{
                    "svc":"report/exec_report",
                    "params":{
                        "reportResourceId":' . $this->city->ovsh_q_params["rrid"] . ',
                        "reportTemplateId":' . $this->city->ovsh_q_params["rtid"] . ',
                        "reportTemplate":null,
                        "reportObjectId":' . $v["car_id"] . ',
                        "reportObjectSecId":0,
                        "interval":{
                            "flags":0,
                            "from":' . $start_time . ',
                            "to":' . $end_time . '
                        }
                    }
                },';
            }
            $params.='],
            "flags":0
            }';

        $vowels = array("\n", "\s", "\t", "\r");
        $params = str_replace($vowels, "", $params);
        $r = self::get_wia('core/batch', $params, $this->sid);
        return $r;
    }
    public function select_exec()
    {
        $start_time = strtotime('now 00:00:00');
        $end_time = strtotime('now 23:59:59');
        $params = '
			{
				"params":
				[
					{
						"svc":"report/cleanup_result",
						"params":{}
					},
					{
						"svc":"report/exec_report",
						"params":
						{
							"reportResourceId":' . $this->city->vio_q_params['rrid'] . ',
							"reportTemplateId":' . $this->city->vio_q_params['rtid'] . ',
							"reportTemplate":null,
							"reportObjectId":' . $this->city->vio_q_params['roid'] . ',
							"reportObjectSecId":0,
							"interval":
							{
								"flags":0,
								"from":' . $start_time . ',
								"to":' . $end_time . '
							}
						}
					}
				],
				"flags":0
			}';
        $vowels = array("\n", "\s", "\t");
        $params = str_replace($vowels, "", $params);
        $r = json_decode(self::get_wia('core/batch', $params, $this->sid));
        if(!$r[0]->error){
            $p2 = '{
				"tableIndex":0,
                "config":{
                    "type":"range",
                    "data":{
				        "from":0,
				        "to":' . $r[1]->reportResult->tables[0]->rows . ',
				        "level":2
			        }
		        }
			}';
            $vowels = array("\n", "\s", "\t");
            $params = str_replace($vowels, "", $p2);
            $r = json_decode(self::get_wia('report/select_result_rows', $params, $this->sid));
            return $r;
        }
    }
    public function get_milleage_report()
    {
        $end_time = strtotime(date('d-m-Y', time()));
        $start_time = $end_time - 86400;

        $params = '
			{
				"params":
				[
					{
						"svc":"report/cleanup_result",
						"params":{}
					},
					{
						"svc":"report/exec_report",
						"params":
						{
							"reportResourceId":' . $this->city->overmilleage_q_params['rrid']. ',
							"reportTemplateId":' .  $this->city->overmilleage_q_params['rtid'] . ',
							"reportTemplate":null,
							"reportObjectId":' .  $this->city->overmilleage_q_params['roid'] . ',
							"reportObjectSecId":0,
							"interval":
							{
								"flags":0,
								"from":' . $start_time . ',
								"to":' . $end_time . '
							}
						}
					}
				],
				"flags":0
			}';
        $vowels = array("\n", "\s", "\t");
        $params = str_replace($vowels, "", $params);
        $tmp_r = self::get_wia('core/batch', $params, $this->sid);
        $r = json_decode($tmp_r);
        $p2 = '{
				"tableIndex":0,
				"indexFrom":0,
				"indexTo":' . $r[1]->reportResult->tables[0]->rows . '
			}';

        $vowels = array("\n", "\s", "\t");
        $params = str_replace($vowels, "", $p2);
        return $r = json_decode(self::get_wia('report/get_result_rows', $params, $this->sid));
    }
    public function get_report()
    {

        $end_time = strtotime(date('d-m-Y', time()));
        $start_time = $end_time - 86400;

        $params = '
			{
				"params":
				[
					{
						"svc":"report/cleanup_result",
						"params":{}
					},
					{
						"svc":"report/exec_report",
						"params":
						{
							"reportResourceId":' . $this->city->overmilleage_q_params['rrid']. ',
							"reportTemplateId":' .  $this->city->overmilleage_q_params['rtid'] . ',
							"reportTemplate":null,
							"reportObjectId":' .  $this->city->overmilleage_q_params['roid'] . ',
							"reportObjectSecId":0,
							"interval":
							{
								"flags":0,
								"from":' . $start_time . ',
								"to":' . $end_time . '
							}
						}
					}
				],
				"flags":0
			}';
        $vowels = array("\n", "\s", "\t");
        $params = str_replace($vowels, "", $params);
        $tmp_r = self::get_wia('core/batch', $params, $this->sid);
        $r = json_decode($tmp_r);
        $p2 = '{
				"tableIndex":0,
				"indexFrom":0,
				"indexTo":' . $r[1]->reportResult->tables[0]->rows . '
			}';

        $vowels = array("\n", "\s", "\t");
        $params = str_replace($vowels, "", $p2);
        $r = json_decode(self::get_wia('report/get_result_rows', $params, $this->sid));
        foreach ($r as $v)
        {
            $total_mileage = [];
            if (preg_match('/(\d*\.?\d{2}?).*?/', $v->c[2], $total_mileage))
            {
                if ($total_mileage[0] >= $this->city->overmilleage_q_params["max"])
                {
                    preg_match('/(\d{3}[A-Z]{2}).*?/', $v->c[1], $name);
                    $res = DB::connection($this->city->db)->table('overmileage')->
                        where('time','=',$end_time)->
                        where('gn','=',$name[0])->count();
                    if (empty($res))
                    {
                        $insert_data = [
                            'gn'=>$name[0],
                            'time'=>$end_time,
                            'mileage'=>$total_mileage[0],
                        ];
                        DB::connection($this->city->db)->table('overmileage')->insert($insert_data);
                    }
                }
            }
        }
    }
    public function engine($data)
    {
        return $this->exec_cmd($data['id'], urlencode('Двига ' . $data['act']));
    }
    public function engine_for_name($data)
    {
        if (preg_match('/^[0-9]{3}[A-Z]{2}$/', $data['name']))
        {
            $db = $this->db_connect();

            $params = '{"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"' . $data['name'] . '*","sortType":"sys_name"},"force":1,"flags":1,"from":0,"to":0}';
            $r = json_decode(self::get_wia('core/search_items', $params, $this->sid));
//			print '<pre>';
//			print_r($r);
            if (!$r)
            {
                return 'false';
            }
            else
            {
                $id = $r->items[0]->id;
                return self::exec_cmd($id, urlencode('Двига ' . $data['act']));
            }
        }
        else
        {
            return 'false';
        }

    }
    public function get_all_cars()
    {
        $params = '{"spec":{"itemsType":"avl_unit","propName":"sys_id","propValueMask":"*","sortType":"sys_name"},"force":1,"flags":' . 0x401 . ',"from":0,"to":0}';
        $r = json_decode(self::get_wia('core/search_items', $params, $this->sid));

        if (empty($r) or ( isset($r->error) AND $r->error != 0))
        {
            return 'false';
        }
        else
        {
            return $r->items;
        }
    }
    public  function exec_cmd($id, $cmd)
    {
        $params = "{'itemId':$id,'commandName':'$cmd','linkType':'','param':1,'timeout':0,'flags':1}";
        return $r = json_decode(self::get_wia('unit/exec_cmd', $params, $this->sid));

        if (isset($r->error) and $r->error != 0)
        {
            return 'false';
        }
        else
        {
            return 'true';
        }
    }

} 
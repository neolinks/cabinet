<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/6/15
 * Time: 8:26 PM
 */

namespace App\Http\Controllers;

use App\Libs\TMBase;
use App\Libs\Wialon;
use App\Models\Cars;
use App\Models\Compensation;
use App\Models\Config;
use App\Models\Driver;
use App\Services\Almaty;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Astana;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Role;
use Illuminate\Support\Facades\Lang;

class PersonalController extends Controller{
    public function __construct(Request $request, Astana $astana, Almaty $almaty){
        $this->middleware('auth');
        if($request->has('city')){
            if($request->input('city')==1)
                $this->city = $almaty;
            else if($request->input('city') == 2)
                $this->city =$astana;
        }else{
            $this->city = $almaty;
        }
        $this->wialon = new Wialon($this->city);
        $this->tm = new TMBase($this->city);
    }
    public function index(){
        return view('personal.index');
    }
    public function engine(Request $request){
        if($request->user()->hasRole('employee') || $request->user()->hasRole('admin')){
            $res = $this->wialon->get_all_cars();
            return view('personal.engine',['cars'=>$res,'city'=>$this->city->index]);
        }else{
            return redirect('/personal/');
        }
    }
    public function engineAction(Request $request){
        $user = $request->user();
        if($user->hasRole('employee') || $user->hasRole('admin')){
            if($request->has('engine_on')){
                if($request->has('id')){
                    $data = [
                        'id'=>$request->input('id'),
                        'act'=>'off',
                    ];
                    $res = $this->wialon->engine($data);
                    $descr = $this->getRole($user).' '.$user->name.' заглушил машину '.id;
                    $request->user()->userLogs('engine',$request->user()->name,$descr);
                    return view('v',['res'=>$res]);
                }
            }
            else if($request->has('engine_off')){
                if($request->has('id')){
                    $descr = $this->getRole($user).' '.$user->name.' разглушил машину '.id;
                    $request->user()->userLogs('engine',$request->user()->name,$descr);
                    $res = $this->wialon->exec_cmd($request->get('id'),'off');
                    return view('v',['res'=>$res]);
                }
            }
        }else{
            return redirect('/personal/');
        }
    }
    public function exception(Request $request){
        if($request->user()->hasRole('employee') || $request->user()->hasRole('admin')){
            $cars = DB::connection($this->city->db)->table('wialon_cars')
                ->orderBy('gn','asc')
                ->get(['gn']);
            $excs = DB::connection($this->city->db)->table('exc')->
                where('end','>',time())->
                orWhere('end','=',0)->
                orderBy('end','asc')->get(['id','begin','end','gn']);
            $user_logs = DB::table('users_logs')->
                where('event','=','exception')->
                where('city','=',$this->city->index)->
                orderBy('id','desc')->
                take(30)->
                get(['descr','date']);
            return view('personal.exception',['cars'=>$cars,'city'=>$this->city->index,'exceptions'=>$excs,'user_logs'=>$user_logs]);
        }else{
            return redirect('/personal/');
        }
    }
    public function postException(Request $request){
        $user = $request->user();
        if($request->has('add_exc') && $request->has('gn') && $request->has('duration')){
            $exc = DB::connection($this->city->db)->table('exc')->
                where('gn','=',$request->input('gn'))->
                where(function($query){
                    $query->where('end','=',0)->
                    orWhere('end','>',time());
                })->
                first();
            $data = [
                'gn'=>$request->input('gn'),
                'begin'=>Carbon::now()->timestamp,
                'end'=>($request->input('duration')) == 0 ? 0 : time()+($request->input('duration')*3600),
            ];
            if(!empty($exc)){
                DB::connection($this->city->db)->table('exc')->where('id','=',$exc->id)->update($data);
            }else{
                DB::connection($this->city->db)->table('exc')->insert($data);
            }
            $descr = $this->getRole($user).' '.$user->name.' добавил в исключение автозаглушки '.$request->get('gn').' на '.($request->get('duration') == 0 ? '' : $request->get('duration')).' '. Lang::choice('message.times',$request->get('duration'),[],'ru');
            $user->userLogs('exception',$user->name,$descr,$this->city->index);
            return redirect()->back();
        }
        if($request->has('del') && $request->has('gn')){
            $exc =
                DB::connection($this->city->db)->table('exc')->
                where('id','=',$request->input('id'))->
                first();
            $data = [
                'end'=>time()-10,
            ];
            if(!empty($exc)){
                DB::connection($this->city->db)->table('exc')->where('id','=',$exc->id)->update($data);
            }
            $descr = $this->getRole($user).' '.$user->name.' удалил из исключение автозаглушки '.$request->get('gn');
            $user->userLogs('exception',$user->name,$descr,$this->city->index);
            return redirect()->back();
        }
    }
    public function overmilleage(Request $request){
        $time = strtotime(date('Ymd'));
        if($request->has('time_start') && $request->has('time_finish')){
            $vowels = ['.',' ', ':'];
            $start_time = strtotime(str_replace($vowels,"",urldecode($request->input('time_start'))));
            $finish_time = strtotime(str_replace($vowels,"",urldecode($request->input('time_finish'))));
        }else{
            $start_time = $time;
            $finish_time = $time + 86400;
        }
        $res = DB::connection($this->city->db)->table('overmileage')->
            whereBetween('time',[$start_time,$finish_time])->
            get();
        return view('personal.overmilleage',['res'=>$res,'start_time'=>$start_time,'finish_time'=>$finish_time]);
    }
    public function overshift(Request $request){
        $time = strtotime(date('Ymd'));
        if($request->has('time_start') && $request->has('time_finish')){
            $vowels = ['.',' ', ':'];
            $start_time = strtotime(str_replace($vowels,"",urldecode($request->input('time_start'))));
            $finish_time = strtotime(str_replace($vowels,"",urldecode($request->input('time_finish'))));
        }else{
            $start_time = $time;
            $finish_time = $time + 86399;
        }
        $res = DB::connection($this->city->db)->table('overshift')->
            join('crews','crews.id','=','overshift.crews_id')->
            join('cars','cars.id','=','crews.car')->
            join('drivers','drivers.id','=','crews.driver')->
            where('drivers.type','=','driver')->
            whereBetween('overshift.time',[$start_time,$finish_time])->
            orderBy('overshift.km','desc')->get(['overshift.km','overshift.time','overshift.begin','overshift.end','crews.code','crews.id','crews.driver','cars.gn','drivers.name']);
        return view('personal.overshift',['res'=>$res,'start_time'=>$start_time,'finish_time'=>$finish_time]);
    }
    public function ordersFromSite(Request $request){
        if($request->has('order_id')){
            $res = DB::table('users_logs')->
                where('order_id','=',$request->input('order_id'))->get();
            if(empty($res)){
                $res = new \stdClass();
                $res->descr = 'Ничего не найдено по такому ID';
                $res = ['0'=>$res];
            }
        }else{
            $res = null;
        }
        return view('personal.orders',['res'=>$res]);
    }
    public function carList(){
        $fields = ['cars.gn as tm_gn', 'crews.code', 'wialon_cars.gn as w_gn', 'wialon_cars.last_message'];

        $second = DB::connection($this->city->db)->table('cars')->select('cars.gn as tm_gn', 'crews.code', 'wialon_cars.gn as w_gn', 'wialon_cars.last_message')->
            rightJoin('wialon_cars', 'cars.gn', '=', 'wialon_cars.gn')->
            leftJoin('crews','cars.id','=','crews.car');

        $res = DB::connection('almaty')->table('cars')->
            leftJoin('wialon_cars', 'cars.gn', '=', 'wialon_cars.gn')->
            leftJoin('crews','cars.id','=','crews.car')->
            where('cars.gn','<>','888KC')->
            where('cars.gn','<>','999')->union($second)->
            orderBy('tm_gn')->
            get($fields);
        $res2 = DB::connection('astana')->table('cars')->
            leftJoin('wialon_cars', 'cars.gn', '=', 'wialon_cars.gn')->
            leftJoin('crews','cars.id','=','crews.car')->
            where('cars.gn','<>','888KC')->
            where('cars.gn','<>','999')->union($second)->
            orderBy('tm_gn')->
            get($fields);
        return view('personal.carlist',['res'=>$res, 'res2'=>$res2,'time'=>time()]);
    }
    public function configs(Request $request){
        if($request->user()->hasRole('admin')){
            $engine_configs= Config::getEngineConfig($this->city->db);
            $pricese_configs = Config::getPricesConfig($this->city->db);
            $other_configs = Config::getOtherConfig($this->city->db);
            return view('personal.configs',['eng_conf'=>$engine_configs,'price_conf'=>$pricese_configs,'other_conf'=>$other_configs,'city'=>$this->city->index]);
        }
    }
    public function configActions(Request $request){
        $prop = $request->get('prop');
        $value = $request->get('value');
        $config = Config::getConfig($this->city->db,$prop);
        $config->value = $value;
        $config->save();
        return redirect('/personal/configs?city='.$this->city->index);
    }
    public function shiftsToCredit(Request $request){
        $drivers = Driver::on($this->city->db)->
            where('type','=','driver')->
            orWhere('type','=','base')->
            orderBy('name')->
            get(['id','name']);
        return view('personal.shiftsToCredit',['drivers'=>$drivers]);
    }
    public function postShiftsToCredit(Request $request){
        $driver_name = $request->get('driver_name');
        $balance = $request->get('balance');
        $shift_cost = $request->get('shift');
        if(isset($driver_name) && isset($balance) && isset($shift_cost)){
            $main_id = Driver::where('name','=',$driver_name)->
                where(function($query) {
                    $query->where('type','=','driver')->
                        orWhere('type','=','base');
                })->select(['id'])->first();
            $debtor_id = Driver::where('name','=',$driver_name)->
                where('type','=','dolg')->
                select(['id'])->first();
            $summ_for_add = ($balance >= 0) ? $shift_cost - $balance : $shift_cost  + abs($balance);
            $summ_for_minus = $summ_for_add + 1000;
            return "Сумма добавление к основному ИД: $summ_for_add, Сумма к должнику $summ_for_minus";
        }
    }
    public function hasSecondID(Request $request){
        $name = $request->get('name');
        $debtor_driver = Driver::where('type','=','dolg')->
            where('name','=',$name)->
            where('deleted','=','0')->
            first();
        if(!isset($debtor_driver)){
            return json_encode(false);
        }else{
            $driver = Driver:: where('name','=',$debtor_driver->name)->
                where(function($query) {
                    $query->where('type','=','driver')->
                        orWhere('type','=','base');
                })->
                where('deleted','=','0')->
                select(['id','name','balans'])->first();
            return json_encode($driver);
        }
    }
    public function getShifts(Request $request=null){
        $begin = Carbon::today()->format('YmdHis');
        $end = Carbon::tomorrow()->format('YmdHis');
        $res = $this->tm->get_shift_plans($begin,$end);
        $shifts =[];
        foreach($res->data->plan_shifts as $v){
            if($v->plan_shift_cost > 10)
                $shifts[$v->plan_shift_id] = [
                    'id'=>$v->plan_shift_id,
                    'name'=>$v->plan_shift_name,
                    'price'=>$v->plan_shift_cost,
                    'begin'=>strtotime($v->plan_shift_start_time),
                    'end'=>strtotime($v->plan_shift_finish_time),
                ];
        }
        return json_encode($shifts);
    }
    public function newDebtorID(Request $request){
        return view('personal.newDebtorID');
    }
    public function compensation(Request $request){
        $crews = Cars::orderBy('code')->get(['gn','id','code']);
        $shifts = json_decode($this->getShifts(),true);
        $compensations = DB::table('compensation')->where('end','=','0')->get(['*']);
        $user_logs = DB::table('users_logs')->
            where('event','=','compensation')->
            where('city','=',$this->city->index)->
            orderBy('id','desc')->
            take(30)->
            get(['descr','date']);
        return view('personal.compensation',['crews'=>$crews,'shifts'=>$shifts,'compensations'=>$compensations,'user_logs'=>$user_logs]);
    }
    public function postCompensation(Request $request){

        if($request->has('id') && $request->has('end_repair')){
            $id = $request->get('id');
            $end_repair = str_replace('.','-',$request->get('end_repair'));
            $end_repair = Carbon::parse($end_repair)->timestamp;
            $compensation = Compensation::find($id);
            if($end_repair > $compensation->shift_end){
                $end_repair = $compensation->shift_end;
            }
            if($compensation->begin > $end_repair)
                return 'Введенные данные не были правильны пожалуйста повторите';
            $price_for_one_second = $compensation->shift_sum / ($compensation->shift_end - $compensation->shift_start);
            $in_repair_second =$end_repair - $compensation->begin;
            $sum = round($price_for_one_second * $in_repair_second,2);
            $driver = Driver::getDriverInfoByCar($this->city->db,$compensation->gn);
            if(empty($driver)){
                return 'Извините водитель не найден на такой машине';
            }
            //driver operations here

            $compensation->end = $end_repair;
            $compensation->save();
            $user = $request->user();
            $difference = Carbon::createFromTimestamp($end_repair)->diffInHours(Carbon::createFromTimestamp($compensation->begin));
            if($difference == 0){
                $difference = Carbon::createFromTimestamp($end_repair)->diffInMinutes(Carbon::createFromTimestamp($compensation->begin));
                $log_description = $user->name." компенсировал водителю $compensation->gn за $difference мин";
            }else{
                $log_description =$this->getRole($user)." " .$user->name." компенсировал водителю $compensation->gn за $difference ". Lang::choice('message.times',$difference,[],'ru');
            }
            $user->userLogs('compensation',$user->name,$log_description,$this->city->index);
            return "Компенсация в размере $sum за $difference часов";

        }
        return 'Извините произошла ошибка повторите позже';
    }
    public function addForWaiting(Request $request){
        $shifts = json_decode($this->getShifts(),true);
        $crew_code = $request->get('crew_code');
        $begin_repair = str_replace('.','-',$request->get('begin_repair'));
        $shift_id = $request->get('shift_id');
        $begin_repair = Carbon::parse($begin_repair)->timestamp;
        if(isset($crew_code) && isset($begin_repair) && isset($shift_id)){
            if($begin_repair < $shifts[$shift_id]['begin']){
                $begin_repair = $shifts[$shift_id]['begin'];
            }
            $insertion = [
                'gn'=>$crew_code,
                'begin'=>$begin_repair,
                'shift_start'=>$shifts[$shift_id]['begin'],
                'shift_end'=>$shifts[$shift_id]['end'],
                'shift_sum'=>$shifts[$shift_id]['price']
            ];
            $user = $request->user();
            $log_description = $this->getRole($user). " " . $user->name." добавил в список ожидающих $crew_code";
            $user->userLogs('compensation','$user->name',$log_description,$this->city->index);
            Compensation::insert($insertion);
        }else{
             return 'Данные введеные неправильно';
        }
        return "$crew_code успешно добавлен в список ожидающих";
    }
    public function sellingShift(Request $request){

    }
    private function getRole($user){
        $role = head($user->roles->toArray());
        $role = $role['display_name'];
        return $role;
    }
}

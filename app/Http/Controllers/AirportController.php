<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/26/15
 * Time: 3:51 PM
 */

namespace App\Http\Controllers;
use App\Libs\TMBase;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Services\Almaty;
use App\Services\Astana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AirportController extends Controller {
    public function __construct(Request $request,Almaty $almaty,Astana $astana){
        $this->middleware('auth');
        if($request->has('city')){
            if($request->input('city')==1)
                $this->city = $almaty;
            else if($request->input('city') == 2)
                $this->city =$astana;
        }else{
            $this->city = $almaty;
        }
        $this->tm = new TMBase($this->city);
    }
    public function orders(){
        $orders = file_get_contents('http://api.komandir.kz/libs/port.php?city='.$this->city->index);
        $orders  = json_decode($orders);
        foreach($orders as $k => $v){
            CarbonInterval::setLocale('ru');
            $dt1 = Carbon::parse($v->SOURCE_TIME);
            $dt2 = Carbon::now()->subMinutes(5);
            $difference = $dt1->diffInMinutes($dt2, true);
            $hours  = intval($difference / 60);
            $minutes = $difference % 60;
            $last_changed = DB::table('users_logs')->
                where('city','=',$this->city->index)->
                where('order_id','=',$v->ID)->
                orderBy('date','desc')->
                select('user_name')->
                first();
            $orders[$k]->LAST_CHANGED = isset($last_changed->user_name) ? $last_changed->user_name : NULL;
            $orders[$k]->DIFFERENCE = ($hours == 0 && $minutes == 0) ? 'Подано' :  CarbonInterval::hours($hours)->minutes($minutes);
            //$orders[$k]->DIFFERENCE = $dt1->diffInMinutes($dt2, true);
        }

        $crews = DB::connection($this->city->db)->table('crews_inline')->
                                  where('status','=','waiting')->
                                  where('code','<>','0')->
                                  orderBy('code')->
                                  get(['code']);
        return view('personal.airport',['res'=>$orders,'crews'=>$crews,'city'=>$this->city->index]);
    }
    public function noteEdit(Request $request){
       $user = $request->user();
       $city = $request->get('city');
       $order_id = $request->get('id');
       $comment = $request->get('value');
       $data = array(
           'order_id' => intval($order_id),
           'comment' => $comment
       );
        $user->userLogs("update_order",$user->name,"Пользователь $user->name изменил(а) заметку  в аэропорту",$this->city->index,$order_id);
        return $comment." Updated";
//       $this->tm->update_order($data);
//       $res = json_decode(file_get_contents("http://api.komandir.kz/libs/test.php?city=".$this->city->index ."&order_id=$order_id"));
//       return $res->NOTE;
    }
    public function timeEdit(Request $request){
        $city = $request->get('city');
        $order_id = $request->get('id');
        $source_time = $request->get('value');
        $source_time =  Carbon::parse($source_time)->format('YmdHis');
        $data = array(
            'order_id'=>intval($order_id),
            'source_time'=>$source_time,
        );
        return $source_time;
        $user->userLogs("update_order",$user->name,"Пользователь $user->name изменил(а) время подачи в аэропорту",$this->city->index,$order_id);
//        $this->tm->update_order($data);
    }
} 
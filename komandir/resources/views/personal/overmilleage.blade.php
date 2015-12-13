@extends('layouts.aircraft')
@section('title','Отчет по суточному перепробегу')
@section('content')
<form method="GET" class="form-inline">
    <span style="font-family: 'Exo 2';font-style: normal;font-weight: 200;font-size:25px;color:#414042;">Период с</span>
    <input class="form-control" onclick="NewCssCal('time_start', 'yyyyMMdd', 'arrow', true, '24', false);" name="time_start" id="time_start" type="text" value="{{date('Y.m.d H:i',$start_time)}}">
    <span style="font-family: 'Exo 2';font-style: normal;font-weight: 200;font-size:25px;color:#414042;"> по: </span>
    <input  class="form-control" onclick="NewCssCal('time_finish', 'yyyyMMdd', 'arrow', true, '24', false);" name="time_finish" id="time_finish" type="text" value="{{date('Y.m.d H:i',$finish_time)}}">
    <input  class="form-control" type="radio" name="city" value="1" checked=""> Almaty
    <input class="form-control" type="radio" name="city" value="2"> Astana
    <input class="form-control btn btn-default"type="submit" value="Показать" id="asdq">
</form>
    <script src="/js/datetimepicker_css.js"></script>
<? $sum = 0; ?>
@if(!empty($res))
<article class="module width_3_quarter" style="margin: 30px 0 0 0">
    <header><h3 class="tabs_involved">Суточные перепробеги</h3></header>
    <table class="tablesorter" cellspacing="0">
        <thead>
        <tr>
            <th>Гос. Номер</th>
            <th>Время</th>
            <th>Километраж</th>
            <th>Сумма</th>
        </tr>
        </thead>
        <tbody>
        @foreach($res as $v)
            <tr>
                <td>{{$v->gn}}</td>
                <td>{{date('Y/m/d H:m',$v->time)}}</td>
                <td>{{$v->mileage-350}}</td>
                <td>{{($v->mileage-350)*20}}</td>
                <? $sum += ($v->mileage-350)*20;?>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr >
                <td></td>
                <td></td>
                <td><span style="font-weight: bold; font-size:20px">Сумма</span></td>
                <td><span style="font-weight: bold; font-size:20px">{{$sum}} тг.</span></td>
            </tr>
        </tfoot>
    </table>
</article>
@endif
@stop


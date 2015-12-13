@extends('layouts.aircraft')
@section('title','Отчет по перепробегу вне смены')
@section('content')
<style>

</style>
<form method="GET" class="form-inline">
    <div class="form-group">
        <label for="time_start" style="font-size:20px;">Период с</label>
        <input class="form-control" onclick="NewCssCal('time_start', 'yyyyMMdd', 'arrow', true, '24', false);" name="time_start" id="time_start" type="text" value="{{date('Y.m.d H:i',$start_time)}}">
    </div>
    <div class="form-group">
        <label for="time_finish" style="font-size:20px;">по:</label>
        <input class="form-control" onclick="NewCssCal('time_finish', 'yyyyMMdd', 'arrow', true, '24', false);" name="time_finish" id="time_finish" type="text" value="{{date('Y.m.d H:i',$finish_time)}}">
    </div>
    <div class="form-group">
        <label class="radio-inline"><input type="radio" name="city" value="1" checked=""> Almaty</label>
        <label class="radio-inline"><input type="radio" name="city" value="2"> Astana</label>
        <input type="submit" value="Показать" id="asdq" class="btn btn-default">
    </div>
</form>
<script src="/js/datetimepicker_css.js"></script>
<? $sum = 0; ?>
@if(!empty($res))
    <header><h3 class="">Перепробеги вне смены</h3></header>
    <table class="tablesorter table table-striped" cellspacing="0">
        <thead>
        <tr>
            <th>Дата</th>
            <th class="text-center">Период замера</th>
            <th class="text-center">Гос. Номер</th>
            <th class="text-center">Позывной</th>
            <th>Водитель</th>
            <th class="text-center">КМ вне смены</th>
            <th class="text-right">Сумма штрафа</th>
        </tr>
        </thead>
        <tbody>
        @foreach($res as $v)
        <tr>
            <td>{{date('m-d',$v->time)}}</td>
            <td class="text-center">*{{date('H:i',$v->begin)}}--{{date('H:i',$v->end-1)}}*</td>
            <td class="text-center">{{$v->gn}}</td>
            <td class="text-center">{{$v->code}}</td>
            <td>{{$v->name}}</td>
            <td class="text-center">{{$v->km}}</td>
            <td class="text-right">{{$v->km*60}} тг.</td>
            <? $sum += $v->km;?>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr >
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center"><span style="font-weight: bold; font-size:20px">Итого</span></td>
            <td class="text-center"><span style="font-weight: bold; font-size:20px">{{$sum }} км</span></td>
            <td class="text-right"><span style="font-weight: bold; font-size:20px">{{$sum*60}} тг.</span></td>
        </tr>
        </tfoot>
    </table>
@endif
@stop


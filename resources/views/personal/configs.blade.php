@extends('layouts.aircraft')
@section('title','Файлы настройки штрафов и включение/отключение штрафов и тд')
@section('content')
<div class="tab-content">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#on_off">Включение отключение штрафов</a></li>
        <li><a data-toggle="tab" href="#prices">Цены штрафов</a></li>
        <li><a data-toggle="tab" href="#others">Другие настройки</a></li>
    </ul>
    <div id="on_off" class="tab-pane fade in active">
        <table class="table ">
            <thead>
            <tr>
                <th>Название</th>
                <th>Действие</th>
            </tr>
            </thead>
           <tbody>
               @foreach($eng_conf as $conf)
               <tr class="active">
                   <td><p class="lead">{{$conf->display_name}}</p></td>
                   <td><a class="btn btn-{{$conf->value == 0 ? 'success': 'danger'}}" href="/personal/configActions?city={{$city}}&prop={{$conf->prop}}&value={{abs($conf->value - 1)}}">{{$conf->value == 0 ? 'Включить': 'Выключить'}}</a></td>
               </tr>
               @endforeach
           </tbody>
        </table>
    </div>
    <div id="prices" class="tab-pane fade">
        <table class="table ">
            <thead>
            <tr>
                <th>Название</th>
                <th>Цена и действии</th>
            </tr>
            </thead>
            <tbody>
            @foreach($price_conf as $conf1)
            <tr class="active">
                <td><p class="lead">{{$conf1->display_name}}</p></td>
                <td><form class="form-inline" action="/personal/configActions" method="get" style="margin-top:20px">
                        <input type="text" class="form-control" value="{{$conf1->value}}" name="value">
                        <input type="hidden" value="{{$conf1->prop}}" name="prop"/>
                        <input type="hidden" value="{{$city}}" name="city"/>
                        <button type="submit" class="btn btn-primary">Изменить</button>
                    </form></td>
            </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    <div id="others" class="tab-pane fade">
        <table class="table">
            <thead>
            <tr>
                <th>Название</th>
                <th>Цена и действии</th>
            </tr>
            </thead>
            <tbody>
            @foreach($other_conf as $conf2)
            <tr class="active">
                <td><p class="lead">{{$conf2->display_name}}</p></td>
                <td><form class="form-inline" action="/personal/configActions" method="get" style="margin-top:20px">
                        <input type="text" class="form-control" value="{{$conf2->value}}" name="value">
                        <input type="hidden" value="{{$conf2->prop}}" name="prop"/>
                        <input type="hidden" value="{{$city}}" name="city"/>
                        <button type="submit" class="btn btn-primary">Изменить</button>
                    </form></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
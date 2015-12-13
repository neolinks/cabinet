@extends('layouts.aircraft')
@section('title','Список машин')
@section('content')

<style>
    .tablesorter thead tr th{
        cursor: pointer;
    }
</style>
<article class="module width_full">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#almaty">Алматы</a></li>
        <li><a data-toggle="tab" href="#astana">Астана</a></li>
    </ul>



<!--    <header><h3 class="tabs_involved">Content Manager</h3>-->
<!--        <ul class="tabs">-->
<!--            <li class="active"><a href="#tab1">Алматы</a></li>-->
<!--            <li><a href="#tab2">Астана</a></li>-->
<!--        </ul>-->
<!--    </header>-->
    <div class="tab-content">
        <div id="almaty" class="tab-pane fade in active">
            <table class="table table-bordered table-striped tablesorter carlist_table" cellspacing="0">
                <thead>
                <tr>
                    <th class="header">Гос. Номер</th>
                    <th class="header">Наличие в TM</th>
                    <th class="header">Экипаж[позывной]</th>
                    <th class="header">Wialon[посл. сообщ]</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($res))
                @foreach($res as $v)
                <tr>
                    <td class="">{{isset($v->tm_gn) ? $v->tm_gn : $v->w_gn}}</td>
                    <td class="{{isset($v->tm_gn) ? 'success' : 'danger'}}">{{isset($v->tm_gn) ? "Есть в ТМ" : "Нет в ТМ"}}</td>
                    <td class="{{isset($v->code) ? 'success' : 'danger'}}">{{isset($v->code) ? $v->code : "Нет в экипажах"}}</td>
                    @if($v->last_message != 0)
                    @if($v->last_message > $time-1800)
                    <td class='success'>На связи[{{date('Y-m-d H:i',$v->last_message)}}]</td>
                    @else
                    <td class='warning'>Нет связи более получаса[{{date('Y-m-d H:i',$v->last_message)}}]</td>
                    @endif
                    @else
                    <td class='danger'>Нет в Wialon</td>
                    @endif

                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div id="astana" class="tab-pane fade">
            <table class="table tablesorter carlist_table" cellspacing="0">
                <thead>
                <tr>
                    <th class="header">Гос. Номер</th>
                    <th class="header">Наличие в TM</th>
                    <th class="header">Экипаж[позывной]</th>
                    <th class="header">Wialon[посл. сообщ]</th>
                </tr>
                </thead>
                <tbody>
                @if(!empty($res2))
                @foreach($res2 as $v)
                <tr>
                    <td class="">{{isset($v->tm_gn) ? $v->tm_gn : $v->w_gn}}</td>
                    <td class="{{isset($v->tm_gn) ? 'success' : 'danger'}}">{{isset($v->tm_gn) ? "Есть в ТМ" : "Нет в ТМ"}}</td>
                    <td class="{{isset($v->code) ? 'success' : 'danger'}}">{{isset($v->code) ? $v->code : "Нет в экипажах"}}</td>
                    @if($v->last_message != 0)
                    @if($v->last_message > $time-1800)
                    <td class='defined'>На связи[{{date('Y-m-d H:i',$v->last_message)}}]</td>
                    @else
                    <td class='last_msg'>Нет связи более получаса[{{date('Y-m-d H:i',$v->last_message)}}]</td>
                    @endif
                    @else
                    <td class='not_defined'>Нет в Wialon</td>
                    @endif

                </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>


</article>
@stop
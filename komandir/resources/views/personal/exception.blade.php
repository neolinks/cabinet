@extends('layouts.aircraft')
@section('title','Исключение')
@section('info','Занесение автомобиля в данный список исключает его из всех автоматических сервисов - автозаглушка, суточный перепробег, пробег вне смены')
@section('content')

<form class="form-inline" action="/personal/exception" onsubmit="return checkExceptionForm(this);" method="POST">
    <div class="form-group">

        <select name="gn" class="form-control">
            <option value="" disabled selected>Номер машины</option>
            @foreach($cars as $v)
            <option value="{{$v->gn}}">{{$v->gn}}</option>
            @endforeach
        </select>

    </div>
    <div class="form-group">
        <select name="duration" class="form-control">
            <option value="" disabled selected>Количество часов</option>
            <option value="0">Навсегда</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="4">4</option>
            <option value="6">6</option>
            <option value="12">12</option>
            <option value="24">24</option>
            <option value="48">48</option>
        </select>
    </div>
    <div class="form-group">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="city" value="{{$city}}">
        <input class="btn btn-default" type="submit" name="add_exc" value="Добавить">
    </div>
</form>
<h3 style="margin-bottom:35px">Список исключении</h3>

<h4>{{empty($exception)  ? 'Исключении нет' : 'Всего исключении: '. count($exception)}}</h4>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Гос. Номер</th>
        <th>Время Начала</th>
        <th>Время Завершения</th>
        <th style="width: 3.5em;">Действия</th>
    </tr>
    </thead>
    <tbody>
        @if(!empty($exceptions))
        @foreach($exceptions as $k => $v)
            <tr>
                <td>{{$k+1}}</td>
                <td>{{$v->gn}}</td>
                <td>{{date('Y/m/d H:i',$v->begin)}}</td>
                <td>{{($v->end == 0) ? 'Не определено' : date('Y/m/d H:i',$v->end)}}</td>
                <td>
                    <form method="post" action="/personal/exception">
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <input type="hidden" name="id" value="{{$v->id}}">
                        <input type="hidden" name="gn" value="{{$v->gn}}"/>
                        <input type="hidden" name="city" value="{{$city}}">
                        <button type="submit" value="del" name="del" class="btn btn-primary">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        @endif
    </tbody>

</table>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Действии</h3>
    </div>
    <div class="panel-body">
        <ul class="list-unstyled">
            @foreach($user_logs as $log)
            <li>
                <div class="col-sm-9"><p class="">{{$log->descr}}</p></div>
                <div class="col-sm-3 text-left"><p>{{$log->date}}</p></div>
            </li>
            @endforeach
        </ul>
    </div>
</div>

<script>


    function checkExceptionForm(form){
        if(0 == document.getElementsByTagName('select')[0].selectedIndex){
            alert("Выберите машину");
            document.getElementsByTagName('select')[0].focus();
            return false;
        }
        if(0 == document.getElementsByTagName('select')[1].selectedIndex){
            alert("Выберите кол. часов");
            document.getElementsByTagName('select')[1].focus();
            return false;
        }
    }
</script>
@stop
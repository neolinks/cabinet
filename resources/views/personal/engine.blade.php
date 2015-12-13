@extends('layouts.aircraft')
@section('title','Раз/заглушка')
@section('content')
<form class="form-inline" id="engine_form" action="/personal/engine_action" method="GET">
    <div class="form-group">
        <select name="id" class="form-control">
            <option disabled selected>ID Объекта</option>
            @foreach($cars as $v)
                <option value="{{$v->id}}">{{$v->nm}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" id="engine_on" name="engine_on" value="engine_on" class="btn btn-danger">Заглушить</button>
        <button type="submit" id="engine_off" name="engine_off" value="engine_off" class="btn btn-success">Разглушить</button>
        <input type="hidden" name="city" value="{{$city}}">
    </div>
</form>
@stop
@extends('layouts.aircraft')
@section('title','Заказы с сайта')
@section('content')

<form method="GET" action="" class="form-inline">
    <div class="form-group">
        <label for="order_id"/>Введите ID заказа</label>
        <input type="text" class="form-control" name="order_id" placeholder="ID заказа">
        <input type="submit" value="Найти" class="btn btn-default">
        <input type="hidden" name="city" value="1">
    </div>
</form>
<article class="module_width_full clear" style="padding: 20px 0;font-family: 'Exo 2';font-style: normal;font-weight: 200;font-size:15px;  ">
    @if(!empty($res))
    @foreach($res as $v)
    <?=str_replace('%20',' ',htmlspecialchars_decode($v->descr))?><hr/>
    @endforeach
    @endif
</article>


@stop
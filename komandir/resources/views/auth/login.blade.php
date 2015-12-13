@extends('layouts.light')

@section('content')
<div class="dialog">
    <div class="panel panel-default">
        <p class="panel-heading no-collapse">Авторизация</p>
        <div class="panel-body">
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong> Возникла ошибка </strong><br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form method="post" action="/auth/login">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group" method="POST" action="/auth/login">
                    <label>Login</label>
                    <input type="text" name="login" class="form-control span12">
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" class="form-control span12 form-control">
                </div>
                <button class="btn btn-primary pull-right">Войти</button>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
    <p class="text-center">Если у вас нет аккаута обратитесь к администратору</p>
</div>
@endsection

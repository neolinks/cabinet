@extends('layouts.aircraft')
@section('title','Создание нового пользователя')
@section('content')
<script type="text/javascript">
    function checkForm(form){
        $(".alert-danger ul").empty();
        if(form.login.value == ''){
            $(".alert-danger ul").append('<li>Логин пустой</li>');
            form.login.focus();
            return false;
        }
        re = /^\w+$/;
        if(!re.test(form.login.value)){
            $(".alert-danger ul").append('<li>Логин должен содержать только буквы и цифры</li>');
            form.login.focus();
            return false;
        }
        if(form.password.value == form.confirm_password.value){
            if(form.password.value.length != 0 && form.password.value.length < 6){
                $(".alert-danger ul").append('<li>Пароль не должен быть меньше 6 символов</li>');
                form.password.focus();
                return false;
            }
        }else{
            $(".alert-danger ul").append('<li>Пароли не совпадают</li>');
            form.confirm_password.focus();
            return false;
        }
    }
</script>

<div class="alert alert-danger">
    <ul>
    </ul>
</div>
<div class="row">
    <div class="col-md-4">
        <br>
        <form action="/personal/user/create" onsubmit="return checkForm(this);" method="POST">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="id"/>
            <div class="form-group">
                <label>Login</label>
                <input type="text" name="login" class="form-control">
            </div>
            <div class="form-group">
                <label>ФИО</label>
                <input type="text" name="name"  class="form-control">
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="text" name="email"  class="form-control">
            </div>
            <div class="form-group">
                <label for="password" class="control-label">Пароль</label>
                <input class="form-control" type="password" name="password" value="" id="password"/>

            </div>
            <div class="form-group">
                <label for="confirm_password" class="control-label">Потверждение пароля</label>
                <input class="form-control" type="password" name="confirm_password" value="" id="confirm_password"/>
            </div>
            <div class="form-group">
                <label for="role_select" class="control-label">Роль</label>
                <select name="role_select" id="role_select" class="form-control">
                    @foreach($roles as $role)
                        <option value="{{$role->id}}">{{$role->display_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="btn-toolbar list-toolbar">
                <button class="btn btn-primary"><i class="fa fa-save"></i> Создать</button>
            </div>
        </form>
    </div>
</div>

@stop
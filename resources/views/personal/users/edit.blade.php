@extends('layouts.aircraft')
@section('title',"Изменить пользователя")
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
        if(form.password.value.length != 0 && form.confirm_password.value.length != 0){
            if(form.password.value == form.confirm_password.value){
                if(form.password.value.length < 6){
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
    }
</script>

<div class="alert alert-danger">
    <ul>
    </ul>
</div>
<div class="row">
    <div class="col-md-4">
        <br>
        <form action="/personal/user_edit" onsubmit="return checkForm(this);" method="POST">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="id" value="{{$user->id}}"/>
            <div class="form-group">
                <label>Login</label>
                <input type="text" name="login" value="{{$user->login}}" class="form-control">
            </div>
            <div class="form-group">
                <label>ФИО</label>
                <input type="text" name="name" value="{{$user->name}}" class="form-control">
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="text" name="email" value="{{$user->email}}" class="form-control">
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
                        <?php $role_id = head($user->roles->toArray()); $role_id = $role_id['id'] ?>
                        <option value="{{$role->id}}" {{$role->id == $role_id ? 'selected' : false}}>{{$role->display_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="btn-toolbar list-toolbar">
                <button class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>
                <a href="#confirm_delete" data-href="/personal/user/delete/{{$user->id}}" data-toggle="modal" class="btn btn-danger">Удалить</a>
            </div>
        </form>
    </div>
</div>

<div class="modal small fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Потверждение удаление</h3>
            </div>
            <div class="modal-body">
                <p class="error-text"><i class="fa fa-warning modal-icon"></i>Вы действительно хотите удалить пользователя?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Отменить</button>
                <a class="btn btn-danger delete_href">Удалить</a>
            </div>
        </div>
    </div>
</div>

<script>
    $('#confirm_delete').on('show.bs.modal', function(e) {
        $(this).find('.delete_href').attr('href', $(e.relatedTarget).data('href'));
        console.log($(this).find('.btn-danger').attr('href'));
    });
</script>
@stop
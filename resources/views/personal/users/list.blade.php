@extends('layouts.aircraft')
@section('title','Список пользователей')
@section('content')
<div class="main-content">

    <div class="btn-toolbar list-toolbar">
        <a href="/personal/user/create" class="btn btn-primary"><i class="fa fa-plus"></i>Создать</a>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Login</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Роли</th>
            <th style="width: 3.5em;">Действия</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($users))
        @foreach($users as $k => $v)
        <tr>
            <td>{{$k+1}}</td>
            <td>{{$v->login}}</td>
            <td>{{$v->name}}</td>
            <td>{{$v->email}}</td>
            <td><?php $a = head($v->roles->toArray());?>{{$a['display_name']}}</td>
            <td>
                <a href="/personal/user/edit/{{$v->id}}"><i class="fa fa-pencil"></i></a>
                <a href="#confirm_delete" data-href="/personal/user/delete/{{$v->id}}" role="button" data-toggle="modal"><i class="fa fa-trash-o"></i></a>
            </td>
        @endforeach
        @endif
        </tr>
        </tbody>
    </table>

    <?php echo $users->render(); ?>

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
</div>
<script>
    $('#confirm_delete').on('show.bs.modal', function(e) {
        $(this).find('.delete_href').attr('href', $(e.relatedTarget).data('href'));
        console.log($(this).find('.btn-danger').attr('href'));
    });
</script>

@stop
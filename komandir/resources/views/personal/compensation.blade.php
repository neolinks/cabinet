@extends('layouts.aircraft')
@section('title','Компенсация за ремонт машины')
@section('content')
<script src="/js/datetimepicker_css.js"></script>
<div class="loading" style="display: none"></div>
<div class="modal fade" id="responseModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <p id="responseText" style="font-size: 18px;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="reloadPage();" class="btn btn-default" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@if(!empty($crews))
<div class="container-fluid">
<form action="/personal/add_for_waiting" class="col-xs-4" method="post" id="addForWaitingID">
    <div class="form-group">
        <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <label for="crew_code_list">Позывной</label>
        <select name="crew_code" id="crew_code_list" class="form-control">
            <option selected disabled>Выберите Позывной</option>
            @foreach($crews as $crew)
            <option value="{{$crew->gn}}">{{$crew->code}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="shift_id">Начало ремонта</label>
        <input onclick="NewCssCal('begin_repair', 'yyyyMMdd', 'arrow', true, '24', false);" name="begin_repair" id="begin_repair" type="text" value="<?=date('Y.m.d H:i',time())?>" class="form-control">
    </div>
    <div class="form-group">
        <label for="shift_id">СМЕНА</label>
        <select name="shift_id" id="shifts" class="form-control">
            <option selected disabled>Выберите смену</option>
            @foreach($shifts as $shift)
                <option value="{{$shift['id']}}">{{$shift['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <submit class="btn btn-primary" style="float: right" onclick="addWaitingSubmit()">Добавить в ожидание</submit>
    </div>
</form>
@endif
<h4 style="padding-top: 50px;clear: both;margin-bottom: -20px;">Список ожидающих компенсацию</h4>
<table class="table-bordered col-xs-12 comp_table" style="margin-top:40px">
    <thead style="text-align: center">
    <tr>
        <th>#</th>
        <th>Позывной</th>
        <th>Начало ремонта</th>
        <th>Конец ремонта</th>
        <th style="width:150px;">Действие</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($compensations))
        @foreach($compensations as $k => $value)
            <tr>

                <td>{{$k}}</td>
                <td>{{$value->gn}}</td>
                <td>{{date('Y.m.d H:i',$value->begin)}}</td>
                <td style="width: 250px;">
                    <form id="compensation_form{{$k}}" action="/personal/compensation/" method="post">
                        <input type="hidden" value="{{csrf_token()}}" name="_token"/>
                        <input onclick="NewCssCal('end_repair{{$k}}', 'yyyyMMdd', 'arrow', true, '24', false);" name="end_repair" id="end_repair{{$k}}" type="text" value="{{date('Y.m.d H:i',time())}}" class="form-control" style="text-align: center;">
                        <input type="hidden" name="id" value="{{$value->id}}"/>
                    </form>
                </td>
                <td>
                    <submit class="btn btn-primary" onclick="CompensationFormSubmit('compensation_form{{$k}}')">Компенсировать</submit>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
</div>
<div class="container-fluid">
<div class="panel panel-default" style="margin-top:30px">
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
</div>
<script>
    jQuery(function ($){
        $(document).ajaxStop(function(){
            $(".loading").hide();
        });
        $(document).ajaxStart(function(){
            $(".loading").show();
        });
    });
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
    function CompensationFormSubmit(formname){
        var formID = '#'+formname;
        var formSerialized = $(formID).serialize();
        $.ajax({
            type : 'post',
            url : '/personal/compensation',
            data : formSerialized,
            async : true,
            success : function (data){
                $("#responseText").html(data);
                $("#responseModal").modal('show');
            }
        })
    }
    function addWaitingSubmit(formname){
        $.ajax({
            type:'post',
            url:'/personal/add_for_waiting',
            data: $("#addForWaitingID").serialize(),
            async : true,
            success : function(data){
                $("#responseText").html(data);
                $("#responseModal").modal('show');
            }
        });
        return false;
    }
    function reloadPage(){
        window.location.reload();
    }
</script>
@stop
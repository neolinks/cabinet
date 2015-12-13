@extends('layouts.aircraft')
@section('title','Продажа смены в кредит')
@section('content')
@if(!empty($drivers))
<form name="shifts_credit" id="shifts_credit" method='POST' action="" class="col-xs-5">
    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
    <label for="driver_list_select">Водитель:</label>
    <select name="driver_name" id="driver_list_select" class="form-control">
        <option selected disabled>Выберите Водителя</option>
        @foreach($drivers as $v)
            <option value="{{$v->name}}">{{$v->name}}</option>
        @endforeach
    </select>
    <div id="ajax_loader" style="display: none; z-index: 0">
        <div class="center">
            <img style="position: absolute; margin: 10px 0 0 40%; width: 50px;" alt="" src="/images/loading-icons/spinner.gif" />
        </div>
    </div>
</form>

@else

@endif
<script type="text/javascript">
    jQuery(function ($){
        $(document).ajaxStop(function(){
            $("#ajax_loader").hide();
        });
        $(document).ajaxStart(function(){
            $("#ajax_loader").show();
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#driver_list_select").change(function(){
            $("#2_id_error").remove();
            $("#balance").remove();
            $("#create").remove();
            $("#wrap_shift").remove();
            var selectvalue = $(this).val();
            $.ajax({
                url : '/personal/has_second_id?name='+selectvalue,
                async : true,
                success : function(data){
                    data = $.parseJSON(data);
                    if(data == false){
                        $("#shifts_credit").append('<div class="alert alert-danger" id="2_id_error"><strong> У этого водителя нет 2 ИД </strong></div>');
                    }else{
                        $("#shifts_credit").append("<div id='balance'><label for='balance'>Баланс: </label> <input readonly class='form-control' type='text' name='balance' value='"+data.balans+"'/></div>");
                        $.ajax({
                            url : '/personal/get_shifts',
                            async : true,
                            success : function(data){
                                data = $.parseJSON(data);
                                $("#shifts_credit").append('<div id="wrap_shift"><label>СМЕНЫ - (Цена)</label><select class="form-control" name="shift" id="shift_list"></select></div>');
                                $.each(data,function(key,value){
                                    $('#shift_list')
                                        .append($("<option></option>")
                                            .attr('value',value['price'])
                                            .text(value['name'] + "  -  ("+ value['price']+" тг.)"));
                                });
                                $("#wrap_shift").append('<div class="form_group"><input type="Submit" class="btn btn-primary form-control" value="Продать"/></div>');
                            }
                        });
                    }
                }
            });
        });
    });

</script>
@stop
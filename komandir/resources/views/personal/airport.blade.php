@extends('layouts.light')
@section('content')
<meta http-equiv="refresh" content="60">
<table class="table table-striped-own table-condensed table-bordered">
    <thead class="vertical-top">
        <tr class="">
            <th>ID</th>
            <th>Адрес подачи</th>
            <th>Время подачи</th>
            <th>Время до подачи</th>
            <th>Телефон</th>
            <th>Телефон отзвона</th>
            <th>Компания</th>
            <th>Пассажир</th>
            <th>Телефон водителя</th>
            <th>По<br>зыв<br>ной</th>
            <th>Желаемый экипаж</th>
            <th style="width: 14%">Заметка</th>
            <th>Создал</th>
            <th>Посл<br>Изм</th>
        </tr>
    </thead>
    <tbody class="vertical-middle">
        @foreach($res as $v)
            <tr id="{{$v->ID}}" >
                <td class="ORDER" id="ORDER_{{$v->ID}}">{{$v->ID}}</td>
                <td class="SOURCE" id="SOURCE_{{$v->ID}}" style="width: 4%">{{$v->SOURCE}}</td>
                <td class="SOURCE_TIME" id="{{$v->ID}}" style="width:8%">{{$v->SOURCE_TIME}}</td>
                <td>{{$v->DIFFERENCE}}</td>
                <td class="PHONE" id="PHONE_{{$v->ID}}">{{$v->PHONE}}</td>
                <td class="PHONE_TO_DIAL" id="PHONE_TO_DIAL_{{$v->ID}}">{{$v->PHONE_TO_DIAL}}</td>
                <td class="NAME" id="NAME_{{$v->ID}}">{{$v->NAME}}</td>
                <td class="PASSENGER" id="PASSENGER_{{$v->ID}}">{{$v->PASSENGER}}</td>
                <td class="MOBILE_PHONE" id="MOBILE_PHONE_{{$v->ID}}">{{$v->MOBILE_PHONE}}</td>
                <td class="CREW_CODE" id="CREW_CODE_{{$v->ID}}" style="width: 50px">{{$v->CODE}}</td>
                <td class="PRIOR_CREW_ID" id="PRIOR_CREW_ID_{{$v->ID}}" style="width: 50px">
                    @if(isset($v->CODE))
                    {{$v->PRIOR_CREW_ID}}
                    @elseif(isset($v->PRIOR_CREW_ID))
                        {{$v->PRIOR_CREW_ID}}
                    @else
                    <form action="">
                        <select>
                            @foreach($crews as $crew)
                            <option value="{{$crew->code}}">{{$crew->code}}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                </td>
                <td class="NOTE"  id="{{$v->ID}}">{{$v->NOTE}}</td>
                <td class="CREATOR" id="CREATOR_{{$v->ID}}">{{$v->CREATOR}}</td>
                <td class="LAST_CHANGED">{{$v->LAST_CHANGED}}</td>

            </tr>
        @endforeach
    </tbody>
</table>
@if($city == 1)
<script>
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
    $(document).ready(function() {
        $.editable.addInputType('timepicker',{
            element : function(settings, original) {
                console.log(original);
                var date = $('<input type="datetime-local" id="data123" value="">');
                $(this).append(date);
                /* Hidden input to store value which is submitted to server. */
                var hidden = $('<input type="hidden">');
                $(this).append(hidden);
                return(hidden);
            },
            content : function(string, settings, original){
                var dateValue = string.substr(0,10);
                var timeValue = string.substr(11,string.length);
                $(this).children("#data123").val(dateValue+'T'+timeValue);
            },
            submit: function (settings, original) {
                var value = $('#data123').val();
                var dateValue = value.substr(0,10)
                var timeValue = value.substr(11,value.length);
                console.log(dateValue + ' ' +timeValue);
                $("input", this).val(dateValue + ' ' +timeValue);
                location.reload();
            }
        });
        $('.NOTE').editable('/personal/airport/note_edit', {
            type      : 'textarea',
            cancel    : 'Отмена',
            submit    : 'Изменить',
            indicator : '<img src="/images/loading-icons/loading7.gif">',
            submitdata :{ city : "{{$city}}"}
        });
        $('.SOURCE_TIME').editable('/personal/airport/time_edit', {
            type      : 'timepicker',
            cancel    : 'Отмена',
            submit    : 'Изменить',
            indicator : '<img src="/images/loading-icons/loading7.gif">',
            submitdata :{ city : "{{$city}}"}
        });

    });
    $("#data123").on("click",function(){
        //location.reload();
    })
</script>
@endif
@endsection
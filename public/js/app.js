/**
 * Created by ernar on 11/7/15.
 */

$("#engine_on").bind('click',function(){
    var msg = $("#engine_form").serialize();
    console.log(msg);

//    $.ajax({
//        type: 'POST',
//        url : '/personal/engine_action',
//        data : msg,
//    })
})

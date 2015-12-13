@extends('app')
@section('content')<script src="https://code.jquery.com/jquery-1.11.3.js"></script>

<div id="user">
Username: <a href="#" id="user_name" data-type="text" data-pk="1" title="Enter username">111111</a><br>
Group: <a href="#" id="group_id" data-type="select" data-pk="1'" data-value="2" data-source="groups.php" title="Select group"></a><br>
</div>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/css/jquery-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/js/jquery-editable-poshytip.min.js"></script>

<script>
    document.ready(function() {
        $('#user a').editable({url: 'example.com'});
    });
</script>
@endsection
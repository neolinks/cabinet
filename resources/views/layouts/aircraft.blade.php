<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="/lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/lib/font-awesome/css/font-awesome.css">
    <script src="/lib/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
    <script src="/lib/jquery.form.min.js"></script>
    <script src="/lib/jQuery-Knob/js/jquery.knob.js" type="text/javascript"></script>
    <script type="text/javascript">

        $(function() {
            $(".knob").knob();
        });
    </script>
    <link rel="stylesheet" type="text/css" href="/stylesheets/theme.css">
    <link rel="stylesheet" type="text/css" href="/stylesheets/premium.css">

</head>
<body class=" theme-blue">

<!-- Demo page code -->

<script type="text/javascript">
    $(document).ready(function()
        {
            $(".tablesorter").tablesorter();
        }
    );
    $(function() {
        var match = document.cookie.match(new RegExp('color=([^;]+)'));
        if(match) var color = match[1];
        if(color) {
            $('body').removeClass(function (index, css) {
                return (css.match (/\btheme-\S+/g) || []).join(' ')
            })
            $('body').addClass('theme-' + color);
        }

        $('[data-popover="true"]').popover({html: true});

    });
</script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<style type="text/css">
    #line-chart {
        height:300px;
        width:800px;
        margin: 0px auto;
        margin-top: 1em;
    }
    .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover {
        color: #fff;
    }
</style>

<script type="text/javascript">
    $(function() {
        var uls = $('.sidebar-nav > ul > *').clone();
        uls.addClass('visible-xs');
        $('#main-menu').append(uls.clone());
    });
</script>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Le fav and touch icons -->
<link rel="shortcut icon" href="../assets/ico/favicon.ico">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">


<!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
<!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
<!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
<!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->

<!--<![endif]-->

<div class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="" href="index.html"><img class="logotip" src="/images/logotip.png" alt=""/></a></div>
    @if (!Auth::guest())
    <div class="navbar-collapse collapse" style="height: 1px;">
        <ul id="main-menu" class="nav navbar-nav navbar-right">
            <li class="dropdown hidden-xs">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user padding-right-small" style="position:relative;top: 3px;"></span> {{Auth::user()->name}}
                    <i class="fa fa-caret-down"></i>
                </a>

                <ul class="dropdown-menu">
                    <li><a tabindex="-1" href="/auth/logout">Выйти</a></li>
                </ul>
            </li>
        </ul>

    </div>
    @endif
</div>
</div>

@if(Auth::check())
<div class="sidebar-nav">
    <ul>
        @if(Auth::user()->hasRole('dispatcher'))
        <li><a href="#" data-target=".airport" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-fighter-jet"></i> Заказы в аэропорту<i class="fa fa-collapse"></i></a></li>
        <li><ul class="airport nav nav-list collapse in">
                <li><a href="/personal/airport?city=1"><span class="fa fa-caret-right"></span> Алматы</a></li>
                <li ><a href="/personal/airport?city=2"><span class="fa fa-caret-right"></span> Астана</a></li>
            </ul></li>
        @else
        <li>
            <a href="#" data-target=".dashboard-menu" class="nav-header" data-toggle="collapse">
            <i class="fa fa-fw fa-dashboard"></i> Раз/заглушка<i class="fa fa-collapse"></i></a>
        </li>
        <li><ul class="dashboard-menu nav nav-list collapse in">
                <li><a href="/personal/engine?city=1"><span class="fa fa-caret-right"></span> Алматы</a></li>
                <li ><a href="/personal/engine?city=2"><span class="fa fa-caret-right"></span> Астана</a></li>
            </ul></li>
        <li><a href="#" data-target=".exceptions" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-road"></i> Исключение<i class="fa fa-collapse"></i></a></li>
        <li><ul class="exceptions nav nav-list collapse in">
                <li><a href="/personal/exception?city=1"><span class="fa fa-caret-right"></span> Алматы</a></li>
                <li ><a href="/personal/exception?city=2"><span class="fa fa-caret-right"></span> Астана</a></li>
            </ul></li>
        <li><a href="#" data-target=".others" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-certificate"></i> Прочее<i class="fa fa-collapse"></i></a></li>
        <li><ul class="others nav nav-list collapse in">
                <li><a href="/personal/orders"><span class="fa fa-caret-right"></span>Заказы с сайта</a></li>
                <li ><a href="/personal/carlist"><span class="fa fa-caret-right"></span>Список машин</a></li>
                <li ><a href="/personal/last_connect"><span class="fa fa-caret-right"></span>Обрывы виалон</a></li>
                <li ><a href="/personal/shifts_to_credit"><span class="fa fa-caret-right"></span>Продажа смены в кредит</a></li>
                <li ><a href="/personal/new_debtor_id"><span class="fa fa-caret-right"></span>Открытие 2 ИД Должник</a></li>
                <li ><a href="/personal/compensation"><span class="fa fa-caret-right"></span>Компенсация за ремонт</a></li>
            </ul></li>
        <li><a href="#" data-target=".reports" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-briefcase"></i> Отчеты<i class="fa fa-collapse"></i></a></li>
        <li><ul class="reports nav nav-list collapse in">
                <li><a href="/personal/overshift"><span class="fa fa-caret-right"></span> Перепробеги вне смены</a></li>
                <li ><a href="/personal/overmilleage"><span class="fa fa-caret-right"></span> Суточный перепробег</a></li>
            </ul></li>
        @endif
        @if(Auth::check() && Auth::user()->hasRole('admin'))
        <li><a href="#" data-target=".administration" class="nav-header" data-toggle="collapse"><i class="fa fa-fw fa-tasks"></i> Администрирование<i class="fa fa-collapse"></i></a></li>
        <li><ul class="administration nav nav-list collapse in">
                <li><a href="/personal/user/list"><span class="fa fa-caret-right"></span>Список пользователей</a></li>
                <li ><a href="/personal/configs?city=1"><span class="fa fa-caret-right"></span>Настройки Алматы</a></li>
                <li ><a href="/personal/configs?city=2"><span class="fa fa-caret-right"></span>Настройки Астаны</a></li>
            </ul></li>
        @endif
    </ul>
</div>
@endif

<div class="content">
    <div class="header">

        <h1 class="page-title">@yield('title')</h1>
        <p class="lead">@yield('info')</p>
    </div>
    <div class="main-content">
    @yield('content')
    </div>
</div>


<script src="/lib/bootstrap/js/bootstrap.js"></script>
<script type="text/javascript">
    $("[rel=tooltip]").tooltip();
    $(function() {
        $('.demo-cancel-click').click(function(){return false;});
    });
</script>


</body></html>

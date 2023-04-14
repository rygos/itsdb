<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>ITS-DB - @yield('title')</title>

    <link rel="stylesheet" type="text/css" href="/css/app.css"
          media="screen"/>

    <meta property="og:type" content="website"/>

    <script type="text/javascript">
        <!--
        var pixelWidth = screen.width;
        var Pouet = {};
        Pouet.isMobile = false;
        //-->
    </script>
    <script src="/js/app.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js"></script>
    <script   src="https://code.jquery.com/jquery-1.9.1.min.js"   integrity="sha256-wS9gmOZBqsqWxgIVgA8Y9WcQOa7PgSIX+rPA0VL2rbQ="   crossorigin="anonymous"></script>

    <!--[if lt IE 9]>
    <script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->
    <!--[if IE]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

    @if(Auth::id() == 2)
        <style>
            body, a:hover, textarea, input {
                cursor: url(http://cur.cursors-4u.net/cursors/cur-2/cur223.cur), default !important;
            }
        </style>
    @endif

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<header>
    <h1>ITS-DB</h1>
</header>
<nav id="topbar">
    @include('_partials.navigation')
</nav>
<div id='content'>
    @yield('content')
</div>
<nav id="bottombar">
    @include('_partials.navigation')
</nav>
<footer>
    <ul>
        <li>Made with Love and Beer</li>
    </ul>
</footer>
</body>

@if(isset($term))
    <script>
        $(document).ready(function() {
            var src_str = $("#pouetbox_prodlist").html();
            var term = "{{ $term }}";
            term = term.replace(/(\s+)/,"(<[^>]+>)*$1(<[^>]+>)*");
            var pattern = new RegExp("("+term+")", "gi");

            src_str = src_str.replace(pattern, "<mark>$1</mark>");
            src_str = src_str.replace(/(<mark>[^<>]*)((<[^>]+>)+)([^<>]*<\/mark>)/,"$1</mark>$2<mark>$4");

            $("#pouetbox_prodlist").html(src_str);
        });
    </script>
@endif
</html>


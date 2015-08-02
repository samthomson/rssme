<!-- Stored in resources/views/layouts/master.blade.php -->

<html>
    <head>
        <title>rss - @yield('title', 'news aggregator')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
        <!--<link href="{{ asset("css/app.css") }}" rel="stylesheet"-->
        <link rel="stylesheet" href="{{ elixir('css/all.css') }}">

        <link rel='stylesheet' id='g_font-css'  href='http://fonts.googleapis.com/css?family=Noto+Sans%3A400%2C700%2C400italic%2C700italic&#038;ver=3.5.1' type='text/css' media='all' />

        <link href='http://fonts.googleapis.com/css?family=PT+Sans+Caption:700' rel='stylesheet' type='text/css'>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    </head>
    <body ng-app="rssme">
        <div ng-controller="MainUI">
            @yield('content')
        </div>
        <script type="text/javascript" src="{{ elixir('js/all.js') }}"></script>
    </body>
</html>
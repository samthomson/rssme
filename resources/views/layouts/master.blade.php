<!-- Stored in resources/views/layouts/master.blade.php -->

<html>
    <head>
        <title>rss - @yield('title', 'news aggregator')</title>
        <!--<link href="{{ asset("css/app.css") }}" rel="stylesheet"-->
        <link rel="stylesheet" href="{{ elixir('css/all.css') }}">
    </head>
    <body>
        <div class="">
            @yield('content')
        </div>
    </body>
</html>
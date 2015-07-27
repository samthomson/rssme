<!-- Stored in resources/views/layouts/master.blade.php -->

<html>
    <head>
        <title>rss - @yield('title', 'news aggregator')</title>
        <!--<link href="{{ asset("css/app.css") }}" rel="stylesheet"-->
        <link rel="stylesheet" href="{{ elixir('css/all.css') }}">

        <link rel='stylesheet' id='g_font-css'  href='http://fonts.googleapis.com/css?family=Noto+Sans%3A400%2C700%2C400italic%2C700italic&#038;ver=3.5.1' type='text/css' media='all' />
    </head>
    <body>
        @yield('content')

        <script type="text/javascript" src="{{ elixir('js/all.js') }}"></script>
    </body>
</html>
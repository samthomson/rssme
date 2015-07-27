var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('app.scss',
    	'resources/assets/css');
});

elixir(function(mix) {
    mix.styles([
        "bootstrap.css",
        "bootstrap-theme.css",
        "app.css"
    ]);
});

elixir(function(mix) {
    mix.scripts([
        "jquery-2.1.4.min.js",
        "angular-1.4.3.min.js",
        "rssme_app.js",
        "bootstrap.min.js"
    ]);
});


elixir(function(mix) {
    mix.version(["css/all.css", "js/all.js"]);
});
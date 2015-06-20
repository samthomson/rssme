<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::get('/login', function () {
	//  login/register
    return view('app/login');
});


Route::group(['middleware' => 'auth'], function () {
	Route::get('/', function () {
		// make app view or force them to login/register
	    return view('app/home');
	});
});
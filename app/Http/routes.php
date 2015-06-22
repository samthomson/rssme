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



Route::get('/auth/login', function () {
	//  login/register
    return view('app/login');
});


Route::group(['middleware' => 'auth'], function () {
	Route::get('/', ['uses' => 'Feeds@makeHome']);

	Route::get('/feeds/manage', function () {
		// list all feeds
	    return view('app/feeds/manage');
	});
	Route::get('/feeds/add', function () {
		// make feed add form
	    return view('app/feeds/add');
	});
	Route::post('/feeds/add', ['uses' => 'Feeds@create']);

	Route::delete('/feeds/{id}', ['uses' => 'Feeds@delete']);
	Route::get('/feeds/{id}', ['uses' => 'Feeds@edit']);

});


Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);


/* spoof */
Route::get('/pullallfeeds', ['uses' => 'Feeds@pullAll']);
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

//
// serve the angular app
//
Route::get('/', ['uses' => 'Feeds@serveAngularApp']);



Route::post('/app/auth/login',  ['uses' => 'CustomAuthController@login']);
Route::post('/app/auth/logout',  ['uses' => 'CustomAuthController@logout']);


Route::controllers([/*
	'app/auth' => 'Auth\CustomAuthController',*/
	'password' => 'Auth\PasswordController',
]);

Route::group(['middleware' => 'auth'], function () {

	Route::get('/app/feeds/manage', function () {
		// list all feeds
	    return view('app/feeds/manage');
	});
	Route::get('/app/feeds/add', function () {
		// make feed add form
	    return view('app/feeds/add');
	});
	Route::post('/app/feeds/add', ['uses' => 'Feeds@create']);

	Route::delete('/app/feeds/{id}', ['uses' => 'Feeds@delete']);
	Route::get('/app/feeds/{id}', ['uses' => 'Feeds@edit']);
	Route::post('/app/feeds/{id}', ['uses' => 'Feeds@update']);

	Route::get('/app/user/feedsandcategories', ['uses' => 'Feeds@feedsAndCategories']);
});




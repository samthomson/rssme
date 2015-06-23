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
	Route::post('/feeds/{id}', ['uses' => 'Feeds@update']);

});


Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);


/* spoof */
Route::get('/pullallfeeds', ['uses' => 'Feeds@pullAll']);


Route::get('/test', function () {

	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));

            

    $xmlFeed = file_get_contents('http://techcrunch.com/feed/', false, $context);
    //$xmlFeed = self::removeColonsFromRSS($xmlFeed);
    $xmlFeed = simplexml_load_string($xmlFeed);


    foreach($xmlFeed->channel->item as $oItem){

    	$namespaces = $oItem->getNameSpaces( true );
/*
    	foreach ( $oItem->getNameSpaces( true ) as $key => $children )
    	{
    		$$key = $oItem->children( $children );
    		//print_r($$key);

    		echo "t: ".$oItem->media->thumbnail['url'];
    		print_r($$key->media->thumbnail);
    		//print_r($$key->thumbnail[0]);
    	}
*/
    	$namespaces = $oItem->getNameSpaces( true );
		$media = $oItem->children( $namespaces['media'] );

		//echo (string)$media->thumbnail['url'];

		$thumb = $oItem->children('media', true)->thumbnail->attributes()->url;

		echo $thumb;
		
	}
});
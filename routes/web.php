<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
	$router->get('/', function() {
		return "Api Implemented";
	});

	$router->get('/products', 'ProductController@index');

	$router->group(['middleware' => 'jwt.auth'], function() use ($router) {
		$router->get('/orders', 'OrderController@index');
	});



	$router->post(
		'auth/login',
		[
			'uses' => 'AuthController@authenticate'
		]
	);
});



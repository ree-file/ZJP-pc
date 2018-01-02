<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

	$router->resources([
		'users' => UsersController::class,
		'supplies' => SuppliesController::class,
		'nests' => NestsController::class,
		'orders' => OrdersController::class
	]);
	$router->get('/orders/{order}/abandon', 'OrdersController@abandon');
	$router->get('/analyse', 'HomeController@analyse');
});

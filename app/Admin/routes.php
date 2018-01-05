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
		'nests' => NestsController::class,
		'rechargeApplications' => RechargeApplicationsController::class,
		'withdrawalApplications' => WithdrawalApplicationsController::class,
		'orders' => OrdersController::class
	]);

	$router->get('/users/{user}/cards', 'UsersController@editCards');

	$router->get('/orders/{order}/abandon', 'OrdersController@abandon');
	$router->get('/analyse', 'HomeController@analyse');
});

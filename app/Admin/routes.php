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
		'cards' => CardsController::class,
		'nests' => NestsController::class,
		'contracts' => ContractsController::class
	]);

	$router->get('/api/users', 'CardsController@users');
	$router->get('/api/nests', 'NestsController@nests');
});

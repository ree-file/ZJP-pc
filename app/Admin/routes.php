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
		'recharge_applications' => RechargeApplicationsController::class,
		'withdrawal_applications' => WithdrawalApplicationsController::class,
		'cards' => CardsController::class,
		'transfer_records' => TransferRecordsController::class,
		'transaction_records' => TransactionRecordsController::class,
		'invest_records' => InvestRecordsController::class,
		'income_records' => IncomeRecordsController::class,
		'contracts' => ContractsController::class
	]);

	$router->get('/analyse', 'HomeController@analyse');

	$router->get('/notice', 'HomeController@editNotice');
	$router->post('/notice', 'HomeController@updateNotice');
	$router->get('/refresh', 'HomeController@refreshConfig');
});

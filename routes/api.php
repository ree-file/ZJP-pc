<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'login', 'prefix' => 'v1'], function () {
	Route::get('/nest', 'Api\NestsController@nest');
	Route::get('/order', 'Api\OrdersController@order');
	Route::get('/orders', 'Api\OrdersController@orders');
	Route::get('/user', 'Api\UsersController@user');
	Route::get('/user/cards', 'Api\UsersController@cards');
	Route::get('/user/nests', 'Api\UsersController@nests');
	Route::get('/user/nest', 'Api\UsersController@nest');
	Route::get('/user/orders', 'Api\UsersController@orders');
	Route::get('/user/supplies', 'Api\UsersController@supplies');
	Route::post('/password', 'Api\LoginController@changePassword');
	Route::post('/users', 'Api\UsersController@store');
	Route::post('/supplies', 'Api\SuppliesController@store');
	Route::post('/supplies/market', 'Api\SuppliesController@activeToMarket');
	Route::post('/supplies/active', 'Api\SuppliesController@marketToActive');
	Route::post('/cards', 'Api\CardsController@store');
	Route::post('/cards/delete', 'Api\CardsController@delete');
	Route::post('/nests', 'Api\NestsController@store');
	Route::post('/contracts', 'Api\ContractsController@store');
	Route::post('/contracts/upgrade', 'Api\ContractsController@upgrade');
	Route::post('/contracts/extract', 'Api\ContractsController@extract');
	Route::post('/orders', 'Api\OrdersController@store');
	Route::post('/orders/abandon', 'Api\OrdersController@abandon');
	Route::post('/orders/buy', 'Api\OrdersController@buy');
});

Route::group(['prefix' => 'v1'], function () {
	Route::post('/login', 'Api\AuthenticateController@login');
	Route::post('/logout', 'Api\AuthenticateController@logout');
	Route::get('/common', 'Api\CommonController@common');
	Route::post('/forget', 'Api\LoginController@resetPassword');
	Route::post('/check', 'Api\LoginController@checkCode');
	Route::post('/reset', 'Api\LoginController@resetPassword');
});

// 访问
Route::group(['middleware' => 'jwt.auth', 'prefix' => 'v2'], function () {
	Route::get('/nest', 'Api\NestsController@nest');
	Route::get('/order', 'Api\OrdersController@order');
	Route::get('/orders', 'Api\OrdersController@orders');
	Route::get('/user', 'Api\UsersController@user');
	Route::get('/user/cards', 'Api\UsersController@cards');
	Route::get('/user/nests', 'Api\UsersController@nests');
	Route::get('/user/nest', 'Api\UsersController@nest');
	Route::get('/user/orders', 'Api\UsersController@orders');
	Route::get('/user/supplies', 'Api\UsersController@supplies');
	Route::post('/password', 'Api\LoginController@changePassword');
	Route::post('/users', 'Api\UsersController@store');
	Route::post('/supplies', 'Api\SuppliesController@store');
	Route::post('/supplies/market', 'Api\SuppliesController@activeToMarket');
	Route::post('/supplies/active', 'Api\SuppliesController@marketToActive');
	Route::post('/cards', 'Api\CardsController@store');
	Route::post('/cards/delete', 'Api\CardsController@delete');
	Route::post('/nests', 'Api\NestsController@store');
	Route::post('/contracts', 'Api\ContractsController@store');
	Route::post('/contracts/upgrade', 'Api\ContractsController@upgrade');
	Route::post('/contracts/extract', 'Api\ContractsController@extract');
	Route::post('/orders', 'Api\OrdersController@store');
	Route::post('/orders/abandon', 'Api\OrdersController@abandon');
	Route::post('/orders/buy', 'Api\OrdersController@buy');
});

// 刷新令牌
Route::group(['middleware' => 'jwt.refresh', 'prefix' => 'v2'], function () {
	Route::get('/refresh', 'Api\AuthenticateController@refresh');
});


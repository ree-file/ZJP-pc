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

// 公共路由，不需登录
Route::group(['prefix' => 'v1'], function () {
	Route::post('/login', 'Api\AuthenticateController@login');
	Route::get('/logout', 'Api\AuthenticateController@logout');
	Route::get('/common', 'Api\CommonController@index');
	Route::post('/forget-password', 'Api\CommonController@forgetPassword');
	Route::post('/reset-password', 'Api\CommonController@resetPassword');
});

// 刷新令牌
Route::group(['middleware' => 'jwt.refresh', 'prefix' => 'v1'], function () {
	Route::post('/refresh', 'Api\AuthenticateController@refresh');
});

// 访问
Route::group(['middleware' => ['jwt.auth', 'auth.freezed'], 'prefix' => 'v1'], function () {
	Route::get('/orders', 'Api\OrdersController@index');
	Route::get('/orders/{order}', 'Api\OrdersController@show');
	Route::patch('/orders/{order}/abandon', 'Api\OrdersController@abandon');
	Route::get('/nests', 'Api\NestsController@index');
	Route::get('/nests/{nest}', 'Api\NestsController@show');
	Route::get('/nests/{nest}/records', 'Api\NestsController@records');
	Route::post('/cards', 'Api\CardsController@store');
	Route::delete('/cards/{card}', 'Api\CardsController@destroy');
	Route::post('/private/change-password', 'Api\PrivateController@changePassword');
	Route::post('/private/store-security-code', 'Api\PrivateController@storeSecurityCode');
	Route::post('/private/forget-security-code', 'Api\PrivateController@forgetSecurityCode');
	Route::post('/private/reset-security-code', 'Api\PrivateController@resetSecurityCode');
	// 私有资源登录可查看到个人的信息
	Route::get('/private', 'Api\PrivateController@user');
	Route::get('/private/cards', 'Api\PrivateController@cards');
	Route::get('/private/nests', 'Api\PrivateController@nests');
	Route::get('/private/simple-nests', 'Api\PrivateController@simpleNests');
	Route::get('/private/orders', 'Api\PrivateController@orders');
	Route::get('/private/supplies', 'Api\PrivateController@supplies');
});

// 需要安全密码支付的路由
Route::group(['middleware' => ['jwt.auth', 'auth.freezed', 'auth.pay'], 'prefix' => 'v1'], function () {
	Route::post('/users', 'Api\UsersController@store');
	Route::post('/nests', 'Api\NestsController@store');
	Route::post('/orders', 'Api\OrdersController@store');
	Route::post('/supplies', 'Api\SuppliesController@store');
	Route::post('/private/transfer-money', 'Api\PrivateController@transferMoney');
	Route::patch('/nests/{nest}/reinvest', 'Api\NestsController@reinvest');
	Route::patch('/nests/{nest}/upgrade', 'Api\NestsController@upgrade');
	Route::patch('/contracts/{contract}/extract', 'Api\ContractsController@extract');
	Route::patch('/orders/{order}/buy', 'Api\OrdersController@buy');
});


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
	// 登录
	Route::post('/login', 'Api\AuthenticateController@login');
	// 登出
	Route::get('/logout', 'Api\AuthenticateController@logout');
	// 公共配置信息
	Route::get('/common', 'Api\CommonController@index');
	// 忘记密码
	Route::post('/forget-password', 'Api\CommonController@forgetPassword');
	// 充值密码
	Route::post('/reset-password', 'Api\CommonController@resetPassword');
});

// 单次验证路由
Route::group(['middleware' => 'jwt.refresh', 'prefix' => 'v1'], function () {
	// 刷新令牌
	Route::post('/refresh', 'Api\AuthenticateController@refresh');
});

// 需要验证的路由
Route::group(['middleware' => ['jwt.auth', 'auth.freezed'], 'prefix' => 'v1'], function () {
	// 创建银行卡
	Route::post('/cards', 'Api\CardsController@store');
	// 删除银行卡
	Route::delete('/cards/{card}', 'Api\CardsController@destroy');
	// 修改密码
	Route::post('/private/change-password', 'Api\PrivateController@changePassword');
	// 创建安全密码
	Route::post('/private/store-security-code', 'Api\PrivateController@storeSecurityCode');
	// 忘记安全密码密码
	Route::post('/private/forget-security-code', 'Api\PrivateController@forgetSecurityCode');
	// 重置安全密码
	Route::post('/private/reset-security-code', 'Api\PrivateController@resetSecurityCode');
	// 个人信息
	Route::get('/private/user', 'Api\PrivateController@user');
	// 个人银行卡
	Route::get('/private/cards', 'Api\PrivateController@cards');
	// 个人收益，分页，可选择请求今日个人收益
	Route::get('/private/income', 'Api\PrivateController@incomeRecords');
	// 个人收益统计
	Route::get('/private/income-analyse', 'Api\PrivateController@incomeRecordsAnalyse');
	// 个人猫窝
	Route::get('/private/nests', 'Api\PrivateController@nests');
	// 猫窝详情（包含下级统计信息）
	Route::get('/nests/{nest}', 'Api\NestsController@show');
	// 猫窝合约详情
	Route::get('/nests/{nest}/contracts', 'Api\NestsController@contracts');
	// 猫窝投资记录
	Route::get('/nests/{nest}/invest-records', 'Api\NestsController@investRecords');
	// 猫窝收益记录
	Route::get('/nests/{nest}/income-records', 'Api\NestsController@incomeRecords');
	// 市场单列表
	Route::get('/orders', 'Api\OrdersController@index');
	// 个人市场单
	Route::get('/private/orders', 'Api\PrivateController@orders');
	// 下架市场单
	Route::post('/orders/{order}/abandon', 'Api\OrdersController@abandon');
	// 个人充值申请
	Route::get('/private/recharge-applications', 'Api\PrivateController@rechargeApplications');
	// 个人提现申请
	Route::get('/private/withdrawal-applications', 'Api\PrivateController@withdrawalApplications');
	// 个人转账记录
	Route::get('/private/transfer-records', 'Api\PrivateController@transferRecords');
});

// 需要验证的路由、安全密码支付的路由
Route::group(['middleware' => ['jwt.auth', 'auth.freezed', 'auth.pay'], 'prefix' => 'v1'], function () {
	// 发起充值申请
	Route::post('/recharge', 'Api\PaymentController@rechargeApplicationStore');
	// 发起提现申请
	Route::post('/withdraw', 'Api\PaymentController@withdrawalApplicationStore');
	// 发起转账
	Route::post('/transfer', 'Api\PaymentController@transferRecordStore');
	// 为他人创建用户或为他人买巢
	Route::post('/users', 'Api\UsersController@store');
	// 为自己购买巢
	Route::post('/nests', 'Api\NestsController@store');
	// 出售巢
	Route::post('/nests/{nest}/sell', 'Api\NestsController@sell');
	// 猫窝复投
	Route::post('/nests/{nest}/reinvest', 'Api\NestsController@reinvest');
	// 猫窝升级
	Route::post('/nests/{nest}/upgrade', 'Api\NestsController@upgrade');
	// 购买市场单
	Route::post('/orders/{order}/buy', 'Api\OrdersController@buy');
});


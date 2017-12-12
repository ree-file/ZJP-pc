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


Route::get('/nest', 'Api\NestsController@nest');
Route::post('/login', 'Api\AuthenticateController@login');
Route::get('/logout', 'Api\AuthenticateController@logout');

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/user', 'Api\UsersController@user');
});

Route::group(['middleware' => 'login'], function () {
	Route::get('/nests/{id}', 'Api\NestsController@getNests');
});


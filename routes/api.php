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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('exchange', 'ApiController@create_exchange');

Route::post('crypto_historical_five', 'ApiController@crypto_add_historical_five');

Route::post('update_crypto_pair_value', 'ApiController@update_crypto_pair_value');

Route::post('crypto', 'ApiController@create_crypto');

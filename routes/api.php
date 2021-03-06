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

/**
 * Ping
 */
Route::get('ping', 'ApiController@ping');


/**
 * Exchange
 */
Route::post('exchange', 'ApiController@create_exchange');
Route::get('exchange/stats/{exchange_id}', 'ApiController@exchange_stats');

/**
 * Fiat
 */
Route::get('fiat/historical/{timestamp}/{fiat}/{old_fiat?}', 'ApiController@fiat_historical');
Route::get('fiat/historical_v2/{timestamp}/{fiats}', 'ApiController@fiat_historical_v2');
Route::get('fiat/all_supported', 'ApiController@list_all_fiat');


Route::post('fiat', 'ApiController@create_fiat');



/**
 * Crypto
 */

Route::post('crypto', 'ApiController@create_crypto');
Route::post('crypto/historical', 'ApiController@crypto_add_historical');

Route::get('crypto/historical/value/{start}/{end}/{exchange}/{range}/{from}/{to}', 'ApiController@get_crypto_value_time_range');
Route::get('crypto/historical/ohlc/{start}/{end}/{exchange}/{range}/{from}/{to}', 'ApiController@get_crypto_ohlc_time_range');
Route::get('crypto/historical/volume/{start}/{end}/{exchange}/{range}/{from}/{to}', 'ApiController@get_crypto_volume_time_range');
Route::get('crypto/historical/pairs/{exchange}', 'ApiController@get_all_hist_avail');

Route::get('crypto/asset/info/{crypto_id}', 'ApiController@get_crypto_asset_info');
Route::get('crypto/historical/asset/value/{crypto_id}/{convert_to_id}/{start}/{end}/{range?}/{dry?}', 'ApiController@crypto_asset_value');
Route::get('crypto/historical/asset/volume/{crypto_id}/{convert_to_id}/{start}/{end}/{range?}', 'ApiController@crypto_asset_volume');
Route::get('crypto/historical/asset/all_time/{crypto_id}', 'ApiController@crypto_asset_all_time');

Route::post('update_crypto_pair_value', 'ApiController@update_crypto_pair_value');



//Route::get('crypto/last/{timestamp}/{exchange}/{from}/{to}/{init}', 'ApiController@get_crypto_value_timestamp');
//Route::post('fiat_fix', 'ApiController@fix_fiat_timestamp');

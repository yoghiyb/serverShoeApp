<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', 'Api\UserController@register');
Route::post('login', 'Api\UserController@login');
Route::get('shoe', 'Api\ShoeController@shoe');

// Route Partner Public
Route::post('partners/register', 'Api\PartnerController@register');
Route::post('partners/login', 'Api\PartnerController@login');

Route::middleware('jwt.verify')->group(function(){
    Route::get('shoeall', 'Api\ShoeController@shoeAuth');
    Route::get('user', 'Api\UserController@getAuthenticatedUser');

    // Partner API JWT
    Route::get('partners', 'Api\PartnerController@partners');
    Route::get('partner/{id}', 'Api\PartnerController@show');
    Route::put('partner/{id}', 'Api\PartnerController@update');

    //Order API JWT
    Route::post('order', 'Api\OrderController@create');
    Route::post('order/confirm', 'Api\OrderController@confirmation');
    Route::get('order/list/{id}', 'Api\OrderController@showAllLastOrder');

    //Loaksi API JWT
    Route::post('partner/location', 'Api\LocationController@create');
    Route::get('partner/location/{partner_id}', 'Api\LocationController@show');
    Route::put('partner/location/{partner_id}', 'Api\LocationController@update');
});


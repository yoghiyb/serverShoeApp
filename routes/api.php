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
    Route::patch('partner/shop/{id}', 'Api\PartnerController@updateShop');
    Route::put('partner/image/{id}','Api\PartnerController@handleImage');
    Route::get('partner/image/{id}', 'Api\PartnerController@showImage');

    //Order API JWT
    Route::post('order', 'Api\OrderController@create');
    Route::post('order/confirm', 'Api\OrderController@confirmation');
    Route::get('order/list/{id}', 'Api\OrderController@showAllLastOrder');
    Route::get('order/{order_no}', 'Api\OrderController@showByOrderNo');

    //Loaksi API JWT
    Route::post('partner/location', 'Api\LocationController@create');
    Route::get('partner/location/{partner_id}', 'Api\LocationController@show');
    Route::put('partner/location/{partner_id}', 'Api\LocationController@update');

    //Service API JWT
    Route::post('service', 'Api\ServiceController@store');
    Route::put('service/{id}', 'Api\ServiceController@update');
    Route::get('service/{id}', 'Api\ServiceController@show');
});


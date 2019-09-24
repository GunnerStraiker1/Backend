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

Route::get('/greeting', function (Request $request){
    return 'Hello World';
});

Route::get('products', "ProductController@index");
Route::post('products', "ProductController@store");
Route::put('products/{id}', "ProductController@update");
Route::delete('products/{id}', "ProductController@destroy");

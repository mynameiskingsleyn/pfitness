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

Route::get('auto_complete_zip','Api\SearchController@zipCodeAutoComplete')->name('zip_auto_complete');

Route::get('search_users', 'Api\SearchController@index')->name('api-search-index');
//sendGetUrl:"http://trainme.test/api/search_users_search?zip=48341&dist=2&pa=1&search=Search"
Route::get('search_users_search','Api\SearchController@search')->name('api-search-search');

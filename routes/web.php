<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/','HomeController@index')->name('home');
Route::get('search_users', 'SearchController@index')->name('search-index');
//Route::get('load-zip-codes', 'SearchController@loadZipCodes')->name('loadZip');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/localization/{locale}', 'LocalizationController@index')->name('localize');

Route::get('/fetchzips','SearchController@fetchzips');


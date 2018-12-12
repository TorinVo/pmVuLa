<?php

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

Auth::routes(); 

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home')->middleware('verified');

Route::get('/ticket/{ticket}', 'API\TicketController@showWeb');

Route::get('{path}',"HomeController@index")->where( 'path', '([A-z\d-\/_.]+)?' )->middleware('verified');
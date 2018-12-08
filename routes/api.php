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

Route::apiResources(['image' => 'API\ImageController']);
Route::apiResources(['comment' => 'API\CommentController']);
Route::apiResources(['ticket' => 'API\TicketController']);
Route::post('ticketactions', 'API\TicketController@updateActions');
Route::apiResources(['project' => 'API\ProjectController']);
Route::get('getproject', 'API\ProjectController@getProject');
Route::apiResources(['user' => 'API\UserController']);
Route::get('profile', 'API\UserController@profile');
Route::get('profile/{type}', 'API\UserController@profile');
Route::put('profile', 'API\UserController@updateProfile');
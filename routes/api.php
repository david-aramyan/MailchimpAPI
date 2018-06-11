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

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('lists/get', 'API\MailchimpController@getLists');
    Route::post('lists/add', 'API\MailchimpController@addList');
    Route::post('lists/update/{id}', 'API\MailchimpController@updateList');
    Route::delete('lists/delete/{id}', 'API\MailchimpController@deleteList');

    Route::post('lists/{id}/addMember', 'API\MailchimpController@member');
    Route::post('lists/{id}/updateMember', 'API\MailchimpController@member');
    Route::post('lists/{id}/deleteMember', 'API\MailchimpController@deleteMember');

});

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

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::post('/conversation', "Watson")->name("");
//Route::get('/', "WatsonController@getWatsonConversation")->name("watson.conversation");
Route::get('/', "WatsonController@getIndex")->name("index");

Route::post('/watson/conservation', 'WatsonController@postConversation')->name("watson.post.conservation");


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

Route::get('/launchRealtime','GpsController@launchRealtime');
Route::get('/', 'GpsController@launchRealtime');
Route::post('/applyDate','GpsController@applyDate');
Route::get('/getRealTimeData/{id}','GpsController@getRealTimeData');
Route::post('/authenticate', 'GpsController@authenticate');
Route::post('/saveLocation', 'GpsController@saveLocation');

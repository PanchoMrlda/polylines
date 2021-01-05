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

Route::get('/', 'HomeController@index')->name('home');
Route::get('/devices/config', 'HomeController@index')->name('devices/config');
Route::get('/handwriting', 'HomeController@handwriting')->name('handwriting');
Route::get('/raspberry-pi', 'HomeController@raspberryPi')->name('raspberry-pi');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/dynamo', 'DynamoDbController@show')->name('dynamo');
Route::post('/handwriting/image', 'HandwritingController@image')->name('handwriting/image');

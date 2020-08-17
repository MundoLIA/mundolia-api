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

Route::apiResource('/escuelas', 'SchoolController');

Route::get('/escuelas/{id}', 'SchoolController@show');

Route::get('escuelas', 'SchoolController@index');
Route::get('escuelas    /{id}', 'SchoolController@show');
Route::post('escuelas', 'SchoolController@store');
Route::put('escuelas/{id}', 'SchoolController@update');
Route::delete('escuelas/{id}', 'SchoolController@delete');

//Route::get('/schools', 'SchoolController@index')->name('schools.index');
//Route::get('/schools', 'SchoolController@create')->name('schools.create');
//Route::get('/schools', 'SchoolController@')->name('schools.index');


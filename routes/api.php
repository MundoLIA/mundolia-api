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

Route::get('escuelas', 'SchoolController@index');
Route::get('escuelas/{id}', 'SchoolController@show');
Route::post('escuelas', 'SchoolController@store');
Route::put('escuelas/{id}', 'SchoolController@update');
Route::delete('escuelas/{id}', 'SchoolController@destroy');

Route::get('usuarios', 'UserController@index');
Route::get('usuarios/{uuid}', ['as' => 'usuarios/{uuid}', 'uses'=>'UserController@show']);
Route::post('usuarios', 'UserController@store');
Route::put('usuarios/{uuid}', 'UserController@update');
Route::delete('usuarios/{uuid}', 'UserController@destroy');

Route::get('tipo_licencias', 'LicenseTypeController@index');
Route::get('tipo_licencias/{id}', 'LicenseTypeController@show');
Route::post('tipo_licencias', 'LicenseTypeController@store');
Route::put('tipo_licencias/{id}', 'LicenseTypeController@update');
Route::delete('tipo_licencias/{id}', 'LicenseTypeController@destroy');

Route::get('licencias', 'LicenseController@index');
Route::get('licencias/{id}', 'LicenseController@show');
Route::post('licencias', 'LicenseController@store');
Route::put('licencias/{id}', 'LicenseController@update');
Route::delete('licencias/{id}', 'LicenseController@destroy');

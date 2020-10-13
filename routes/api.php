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
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('access-token', 'API\UserController@accessToken');
    Route::get('escuelas', 'SchoolController@index');
    Route::get('escuelas/{id}', 'SchoolController@show');
    Route::post('escuelas', 'SchoolController@store');
    Route::put('escuelas/{id}', 'SchoolController@update');
    Route::delete('escuelas/{id}', 'SchoolController@destroy');

    Route::get('usuarios', 'UserController@index');
    Route::get('usuarios/{uuid}', ['as' => 'usuarios/{uuid}', 'uses'=>'UserController@show']);
    Route::post('usuarios', 'UserController@store');
    Route::put('usuarios/{uuid}', 'UserController@update');
    Route::delete('usuarios/{id}', 'UserController@destroy');

    Route::get('tipolicencias', 'LicenseTypeController@index');
    Route::get('tipolicencias/{id}', 'LicenseTypeController@show');
    Route::post('tipolicencias', 'LicenseTypeController@store');
    Route::put('tipolicencias/{id}', 'LicenseTypeController@update');
    Route::delete('tipolicencias/{id}', 'LicenseTypeController@destroy');

    Route::get('licencias', 'LicenseController@index');
    Route::get('licencias/{id}', 'LicenseController@show');
    Route::post('licencias', 'LicenseController@store');
    Route::put('licencias/{id}', 'LicenseController@update');
    Route::delete('licencias/{id}', 'LicenseController@destroy');

    Route::get('key/licencias', 'LicenseKeyController@index');
    Route::get('key/licencias/{id}', 'LicenseKeyController@show');
    Route::post('key/licencias', 'LicenseKeyController@store');
    Route::put('key/licencias/{id}', 'LicenseKeyController@update');
    Route::delete('key/licencias/{id}', 'LicenseKeyController@destroy');

    Route::get('tipos/contacto', 'ContactTypeController@index');
    Route::get('tipos/contacto/{id}', 'ContactTypeController@show');
    Route::post('tipos/contacto', 'ContactTypeController@store');
    Route::put('tipos/contacto/{id}', 'ContactTypeController@update');
    Route::delete('tipos/contacto/{id}', 'ContactTypeController@destroy');

    Route::get('contacto', 'ContactController@index');
    Route::get('contacto/{id}', 'ContactController@show');
    Route::post('contacto', 'ContactController@store');
    Route::put('contacto/{id}', 'ContactController@update');
    Route::delete('contacto/{id}', 'ContactController@destroy');

    Route::get('roles', 'RoleController@index');
    Route::get('roles/{id}', 'RoleController@show');
    Route::post('roles', 'RoleController@store');
    Route::put('roles/{id}', 'RoleController@update');
    Route::delete('roles/{id}', 'RoleController@destroy');

    Route::get('lia-schools', 'LiaSchoolController@index');
    Route::get('schools', 'LiaSchoolController@list');
    Route::get('lia-schools-sync', 'LiaSchoolController@sync');

    Route::post('importar/usuarios', 'UserImportController@store');


    //THINKIFIC ROUTES
    Route::get('/usuario/thinkific', 'UserThinkificController@getUsers');

    //Route::post('/usuario/comunidad', 'UserPhpFoxController@getToken');
    Route::post('/usuario/comunidad', 'UserPhpFoxController@getToken');
    Route::post('/comunidad/nuevo/usuario', 'UserPhpFoxController@storeUser');
    Route::post('/comunidad/{user_id}', 'UserPhpFoxController@destroy');

    Route::post('/sincronizar/usuario/', 'SyncUserPlatformController@syncUserplatform');

    Route::post('/usuario/login/', 'UserThinkificController@singleSignThinkific');

    Route::put('/actualizar/usuarios/{uuid}', 'SyncUserPlatformController@updateUser');
});



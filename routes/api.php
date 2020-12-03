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
    Route::post('logout', 'API\UserController@logout');
    Route::post('access-token', 'API\UserController@accessToken');

    Route::get('escuelas', 'SchoolController@index');
    Route::get('escuelas/{id}', 'SchoolController@show');
    Route::post('escuelas', 'SchoolController@store');
    Route::put('escuelas/{id}', 'SchoolController@update');
    Route::delete('escuelas/{id}', 'SchoolController@destroy');

    Route::get('periodos', 'PeriodoController@index');
    Route::get('periodos/{id}', 'PeriodoController@show');
    Route::post('periodos', 'PeriodoController@store');
    Route::put('periodos/{id}', 'PeriodoController@update');
    Route::delete('periodos/{id}', 'PeriodoController@destroy');

    Route::get('inscripciones', 'EnrollmentController@index');
    Route::get('inscripciones/{id}', 'EnrollmentController@show');
    Route::post('inscripciones', 'EnrollmentController@store');
    Route::put('inscripciones/{id}', 'EnrollmentController@update');
    Route::delete('inscripciones/{id}', 'EnrollmentController@destroy');

    Route::get('usuarios', 'UserController@index');
    Route::get('usuarios/{uuid}', ['as' => 'usuarios/{uuid}', 'uses'=>'UserController@show']);
    Route::post('usuarios', 'UserController@store');
    Route::put('usuarios/{uuid}', 'UserController@update');
    Route::delete('usuarios/{uuid}', 'UserController@destroy');

    Route::put('usuariosgroup', 'UserController@updateGroup');

    Route::get('tipos/licencia', 'LicenseTypeController@index');
    Route::get('tipos/licencia/{id}', 'LicenseTypeController@show');
    Route::post('tipos/licencia', 'LicenseTypeController@store');
    Route::put('tipos/licencia/{id}', 'LicenseTypeController@update');
    Route::delete('tipos/licencia/{id}', 'LicenseTypeController@destroy');

    Route::get('licencias', 'LicenseController@index');
    Route::get('licencias/{id}', 'LicenseController@show');
    Route::post('licencias', 'LicenseController@store');
    Route::put('licencias/{id}', 'LicenseController@update');
    Route::delete('licencias/{id}', 'LicenseController@destroy');

    Route::post('emails', 'MassiveEmailController@send');

    //Asignar Licencias
    Route::post('asignar/licencias', 'UserController@assignLicense');

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

    Route::get('grados', 'GradeController@index');
    Route::get('grados/{id}', 'GradeController@show');
    Route::post('grados', 'GradeController@store');
    Route::put('grados/{id}', 'GradeController@update');
    Route::delete('grados/{id}', 'GradeController@destroy');

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

    //Enrollment
    Route::post('/inscripciones/{id}', 'UserThinkificController@enrollment');

    //Route::post('/usuario/comunidad', 'UserPhpFoxController@getToken');
    Route::post('/usuario/comunidad', 'UserPhpFoxController@getToken');
    Route::post('/comunidad/nuevo/usuario', 'UserPhpFoxController@storeUser');
    Route::post('/comunidad/{user_id}', 'UserPhpFoxController@destroy');

    Route::post('/sincronizar/usuario/', 'SyncUserPlatformController@syncUserplatform');

    Route::post('/usuario/login/', 'UserThinkificController@singleSignThinkific');

    Route::put('/actualizar/usuarios/{id}', 'SyncUserPlatformController@updateUser');

    Route::post('sync/usuario/', 'UserThinkificController@syncUser');
    Route::post('platform/usuario/', 'UserThinkificController@syncUserplatform');

    Route::post('/usuario_t/login/', 'UserThinkificController@singleSignThinkific');
    Route::post('/usuario_p/login/', 'UserPhpFoxController@singleSignPhpFox');

    Route::get('/sync/escuelas', 'SyncSchoolController@store');

    Route::get('/sync/escuelas/comunidad', 'SyncGroupComunnityController@syncSchool');
    Route::get('/sync/grados/comunidad', 'SyncGroupComunnityController@syncGroupGrade');

    Route::get('usuario/grupos/comunidad', 'LikeUserGroupController@list');

    Route::get('sync/usuario/comunidad/', 'SyncUserPlatformController@syncUserCommunity');
});



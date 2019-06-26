<?php

header('Access-Control-Allow-Headers: X-CSRF-Token, Access-Control-Request-Headers, Access-Control-Request-Method, Accept, X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization');
header('Access-Control-Allow-Methods: PATCH, GET, POST, PUT, DELETE, OPTIONS');
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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/register', 'UserController@register');

Route::post('/api/login', 'UserController@login');

Route::resource('/api/cars', 'CarController');

//cache
Route::get('/clear.cache', function(){
    $code = Artisan::call('cache:clear');
});

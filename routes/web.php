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

Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/showreport', function () {
    return view('report');
});

Route::get('/getword', 'WordsToPdf@getWord');

Route::get('/callback', 'AuthController@handleProviderCallback');

// Route::get('login/github/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/erpcallback', 'AuthController@handleErpCallback');

Route::get('/erplogin', 'AuthController@redirectToErp');
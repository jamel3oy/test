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

Route::middleware('nextauth')->group(function () {
    // เอา Api Route มาไว้ในนี้
    Route::get('/testx', function (Request $request) {
        return response()->json($request->attributes->get('user'));
    });
});

Route::post('createcontract', 'WordsToPdf@createContract');

Route::post('sign', 'WordsToPdf@sign');

Route::post('testloop', 'WordsToPdf@testLoop');
Route::post('signx', 'SignPdf@sign');

Route::get('test', 'TestCT@test');


Route::get('create4tm', 'WordsToPdf@createWordFromTemplate');



<?php


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

use Illuminate\Support\Facades\Route;

Route::namespace('App\Package\Sign\Http\Controllers')
    ->prefix('api')
    ->group(function () {

        Route::post('sign/in', 'SignController@signIn')->name('sign.in');

    });

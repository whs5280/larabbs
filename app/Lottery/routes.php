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

Route::namespace('App\Lottery\Http\Controllers')
    ->middleware(['bindings'])
    ->prefix('api')
    ->group(function () {

        Route::post('lottery/prize/{prize:id}', 'DrawPrizeController@lottery')->name('lottery.prize')
            ->middleware(['lock:lottery,2']);

});

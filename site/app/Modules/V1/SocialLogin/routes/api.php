<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::group(['prefix' => 'v1', 'namespace' => 'App\Modules\V1\Auth\Http\Controllers'], function () {
        Route::post('/social-login', 'SocialLoginController@login');
    });
});
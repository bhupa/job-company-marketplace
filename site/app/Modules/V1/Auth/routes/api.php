<?php

// Modules/V1/Auth/Routes/api.php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::group(['prefix' => 'v1', 'namespace' => 'App\Modules\V1\Auth\Http\Controllers'], function () {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');

        Route::post('email/verification-notification', 'EmailVerificationController@sendVerificationEmail');
        Route::post('forgot-password', 'EmailForgotPasswordController@forgot');
        Route::post('reset-password', 'EmailForgotPasswordController@reset');
        Route::get('verify-otp', 'EmailForgotPasswordController@verifyOtp');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('change-password', 'EmailForgotPasswordController@changePassword');
            Route::post('update-email', 'EmailVerificationController@updateEmail');
            Route::get('refresh-token', 'AuthController@refreshToken');
            Route::get('logout', 'AuthController@logout');
        });
    });
});

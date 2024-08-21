<?php

use App\Http\Controllers\Api\V1\Esewa\EsewaController
;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Your password protected routes.
Route::get('/verification/notice/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])->name('verification.notice');

Route::get('/onboarding/{any}', function ($any) {
    return response()->file(public_path('onboarding/'.$any));
});

Route::group(['prefix' => 'esewa'], function () {
    Route::get('/epay', [EsewaController::class, 'index']);
    Route::post('/epay', [EsewaController::class, 'store']);
});

Route::get('/{any}', function () {
    return view('home');
})->where('any', '^(?!api).*$')->middleware('basic_auth');

Route::get('term-conditions', function () {
    return view('term-conditions');
});

Route::get('privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('account-deletion-guide', function () {
    return view('account-deletion-guide');
});

// Auth::routes();
Route::get('/verification/notice/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])->name('verification.notice');

Route::get('liap/notifications', [App\Http\Controllers\HomeController::class, 'appStore'])->name('liap.serverNotifications');

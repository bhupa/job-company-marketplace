<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Api\V1\Auth\UserController;
// use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
// use App\Http\Controllers\Api\V1\Auth\EmailForgotPassword;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// require base_path('app/Modules/V1/Auth/routes/api.php');  //this route for auth modules

// verison 1 api
// login register
Route::group(['prefix' => 'v1', 'namespace' => 'V1\Auth'], function () {
    Route::post('otp-count', 'UserController@otpCount');
    Route::post('check-phone', 'UserController@checkPhone');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('user/change-status', 'UserController@changeStatus');
        Route::get('user-details/{id}', 'UserController@getUserDetails');
        Route::post('device-token', 'UserController@addingDeviceToke');
        Route::delete('user/{user}', 'UserController@destroy');
    });
});

Route::group(['prefix' => 'v1', 'namespace' => 'V1'], function () {
    Route::post('getPlan', 'GooglePayController@getPlan');
    Route::get('app-jwt', 'GooglePayController@decode');
    Route::get('stripe/webhook', 'SubscriptionController@webbook');
    Route::post('/app-store/webhook', 'GooglePayController@handleWebhook');
    Route::post('web-hook', 'SubscriptionController@webhook');
    Route::post('/social-login', 'SocialLoginController@login');
    Route::get('category', 'CategoryController@index');
    Route::get('profile', 'ProfileController@index');
    Route::get('force-update', 'ForceUpdateController@index');
    Route::get('account-deletion-guide', 'AccountDeletionGuideController@index');
    Route::get('content', 'ContentController@index');
    Route::get('onboarding', 'OnboardingController@index');

    Route::get('force-update', 'ForceUpdateController@index');
    Route::get('account-deletion-guide', 'AccountDeletionGuideController@index');
});

Route::group(['prefix' => 'v1', 'namespace' => 'V1', 'middleware' => 'auth:sanctum'], function () {
    // Route::post('email/verification-notification', 'Auth\EmailVerificationController@sendVerificationEmail');
    Route::post('translate-en-jp', 'TranslationController@store');
    Route::post('/check-company-exists', 'CompanyController@checkName');
    Route::get('/company', 'CompanyController@index');
    Route::get('company-details', 'CompanyController@getCompanyDetails')->middleware('company_owner');

    Route::get('/job', 'JobController@index');
    Route::get('/job/matches', 'JobController@match')->middleware('jobseeker');
    Route::get('job/{jobs}', 'JobController@show');
    Route::get('job-lists', 'JobController@lists')->middleware('company_owner');

    Route::get('jobseeker-details', 'JobSeekerController@getJobSeekerDetails');
    Route::get('/jobseeker/{jobseeker}', 'JobSeekerController@show');
    Route::get('/jobseeker', 'JobSeekerController@index');

    Route::get('job-location', 'JobLocationController@index');

    Route::get('/tags', 'TagController@index');

    Route::group(['prefix' => 'company'], function () {
        Route::post('store', 'CompanyController@store')->middleware('company_owner');
        Route::post('/{company}', 'CompanyController@update')->middleware('company_owner');
        Route::delete('/{company}', 'CompanyController@destroy')->middleware('company_owner');
        Route::get('/{company}', 'CompanyController@show');
    });

    Route::group(['prefix' => 'image-file'], function () {
        Route::get('/{imagefiles}', 'ImageFileController@show');
        Route::delete('/{imagefiles}', 'ImageFileController@destroy');
    });

    Route::group(['prefix' => 'jobseeker'], function () {
        Route::post('store', 'JobSeekerController@store');
        Route::post('/{jobseeker}', 'JobSeekerController@update');
        Route::delete('/{jobseeker}', 'JobSeekerController@destroy');
    });

    Route::group(['prefix' => 'job'], function () {
        Route::post('/store', 'JobController@store')->middleware('company_owner');
        Route::post('/{jobs}', 'JobController@update')->middleware('company_owner');
        Route::delete('/{jobs}', 'JobController@destroy')->middleware('company_owner');
    });

    Route::group(['prefix' => 'matching', 'namespace' => 'Matching'], function () {
        Route::get('/', 'SwipeController@index');
        Route::post('/request', 'SwipeController@store');
        Route::post('/accept/{matching}', 'SwipeController@accept');
        Route::post('favourite', 'SwipeController@favourite');
        Route::get('/count', 'SwipeController@count');
        Route::post('chat-request', 'ChatRequestController@chatRequest');
        Route::post('rewind', 'RewindController@unRewind');
    });

    Route::get('create-stripe-test-product', 'StripeController@creatTestProduct');

    Route::post('stripe-payment', 'StripeController@paymentProcess');

    Route::post('add-plan', 'SubscriptionController@addPlan');
    Route::get('stripe-stop-autorenew', 'SubscriptionController@pausePlan');
    Route::delete('subscribed-plan/{subscription}', 'SubscriptionController@destroy');
    Route::get('plan-lists', 'SubscriptionController@index');
    Route::post('stripe-payment-intent', 'StripeController@paymentIntent');
    Route::get('subscribed-plan', 'SubscriptionController@getSubscribPlan');

    // this controller for the in app purchase
    Route::get('iap/skus', 'GooglePayController@index');
    Route::post('google-pay-validation', 'GooglePayController@validateGooglePlayReceipt');
    Route::post('in-app-purchase', 'GooglePayController@paymentDetails');

    Route::group(['prefix' => 'chat'], function () {
        Route::get('/user-lists', 'ChatController@index');
        Route::post('/store', 'ChatController@store');
        Route::post('/seen/{chat}', 'ChatController@seen');
        Route::post('email/{chatroom}', 'ChatController@sendEmail');
        Route::post('direct', 'ChatController@directChat');
        Route::get('count', 'ChatController@unseenCount');
    });

    Route::group(['prefix' => 'chat-room'], function () {
        Route::post('/store', 'ChatRoomController@store');
        Route::get('/{chat_room}', 'ChatRoomController@show');
        Route::get('/superchat/{chat_room}', 'ChatRoomController@superChatEmail');
        Route::post('/join', 'ChatRoomController@setChatRoom');
        Route::post('/{chat_room}', 'ChatRoomController@update');
        Route::delete('/{chat_room}', 'ChatRoomController@destroy');
    });
    Route::post('verify-account', 'AccountVerifyController@store');
    Route::get('verify-account', 'AccountVerifyController@getVerifyDetails');

    Route::group(['prefix' => 'violation-report'], function () {
        Route::post('/store', 'ViolationReportsController@store');
        Route::get('/{violationReports}', 'ViolationReportsController@show');
        Route::post('/{violationReports}', 'ViolationReportsController@update');
        Route::delete('/{violationReports}', 'ViolationReportsController@destroy');
    });

    Route::get('profile-complete', 'ProfileController@proilePercentage');

    Route::get('chat-price', 'ChatPriceController@index');

    Route::post('lang-set', 'LanguageController@store');

    Route::post('intro-video', 'IntroVideoController@store');

    Route::get('reason-for-cancellation-form', 'ReasonForCancellationController@index');
    Route::get('reason-for-cancellation-details', 'ReasonForCancellationController@show');

    Route::post('reason-for-cancellation', 'ReasonForCancellationController@store');
});

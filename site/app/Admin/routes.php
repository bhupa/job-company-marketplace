<?php

use App\Admin\Controllers\ContentController;
use Illuminate\Routing\Router;

Admin::routes();

Route::group(
    [
        'prefix' => config('admin.route.prefix'),
        'namespace' => config('admin.route.namespace'),
        'middleware' => config('admin.route.middleware'),
        'as' => config('admin.route.prefix') . '.',
    ],
    function (Router $router) {
        $router->get('/', 'HomeController@index')->name('home');
        $router->resource('companies', CompanyController::class);
        $router->resource('job-categories', JobCategoryController::class);
        $router->resource('jobseekers', JobSeekerController::class);
        $router->resource('users', UsersController::class);
        $router->resource('jobs', JobsController::class);
        $router->resource('otp-checks', OtpCheckController::class);
        $router->resource('flips', FlipCountController::class);
        $router->resource('chat-rooms', ChatRoomController::class);
        $router->resource('chats', ChatController::class);
        $router->resource('verified', VerifiedController::class);
        $router->resource('verify', VerificationController::class);
        $router->resource(
            'chat-violation',
            ChatViolationReportController::class
        );
        $router->resource(
            'profile-violation',
            ProfileViolationReportController::class
        );
        $router->resource('restricted-words', RestrictedWordsController::class);
        $router->resource('image-files', ImageFilesController::class);
        $router->resource('subscribed-users', SubscriptionController::class);
        $router->resource('tags', TagController::class);
        $router->resource('contents', ContentsController::class);
        $router->resource('district', DistrictController::class);
        $router->resource('force-updates', ForceUpdateController::class);
        $router->resource(
            'account-deletion-guides',
            AccountDeletionGuideController::class
        );
        $router->resource(
            'reason-for-cancellations',
            ReasonForCancellationController::class
        );
    }
);

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scantum\Exceptions\AuthenticationException;

class ScantumServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton(AuthenticationException::class, function (
            $app,
            $request,
            $gaurds
        ) {
            return response()->json(['message' => 'Unathenticated'], 401);
        });
    }
}

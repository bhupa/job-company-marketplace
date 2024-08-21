<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // $this->reportable(function (Throwable $e) {
        //     //
        // });
        $this->renderable(function (Exception $e, $request) {
            return $this->handleException($request, $e);
        });
    }

    // public function render($request, Throwable $exception)
    // {
    //     // dd($exception->getMessage());
    //     dd($request->expectsJson());
    //     // if($exception instanceof AuthenticationException){

    //         if($request->is('api/*')){
    //           return new JsonResponse(['message'=>'Unauthenticated'],401);
    //         }
    //         dd('as');
    //     // }
    //     dd('a');
    //     return parent::render($request,$exception);
    // }

    public function handleException(Request $request, Exception $exception)
    {
        $message = $exception->getMessage();
        if ($exception instanceof AuthenticationException) {
            if ($request->is('api/*')) {
                return new JsonResponse(['message' => 'Unauthenticated'], 401);
            }
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Sven\SuperBasicAuth\SuperBasicAuth;

class EnvironmentBasedAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!App::environment('production')) {
            return (new SuperBasicAuth())->handle($request, $next);
            }

        return $next($request);
        
    }
}

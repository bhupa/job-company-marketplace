<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request): ?string
    // {
    //     // dd($request->all());
    //     // return $request->expectsJson() ? null : route('login');
    // }

    public function handle($request, Closure $next, ...$guards)
    {
        if ($this->authenticate($request, $guards)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticate'], 401);
            } else {
                return redirect()->guest('login');
            }
        }
        return $next($request);
    }
}

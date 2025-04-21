<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, don't redirect - just return null
        // This is the key fix that prevents the "Route [login] not defined" error
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Only for web requests, which you're not using
        return route('auth.login');  // Using your existing route name instead of 'login'
    }
}

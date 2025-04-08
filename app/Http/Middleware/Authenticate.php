<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // Check the requested route and redirect accordingly
            if ($request->is('admin/*')) {
                return route('admin.login'); // Redirect to the admin login page
            }

            return route('login'); // Redirect to the default customer login page
        }
    }
}

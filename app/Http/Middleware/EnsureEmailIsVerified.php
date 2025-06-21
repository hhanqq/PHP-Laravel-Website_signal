<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()?->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'error' => 'Email не подтверждён.',
            ], 403);
        }

        return $next($request);
    }
}

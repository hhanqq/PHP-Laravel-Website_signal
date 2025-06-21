<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientDeposit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->pocket_id || (float)$user->sum_depo <= 10) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return $next($request);
    }
}

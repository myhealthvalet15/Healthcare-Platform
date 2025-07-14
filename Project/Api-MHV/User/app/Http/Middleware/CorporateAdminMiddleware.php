<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CorporateAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

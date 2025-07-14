<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthGuardForEmployeeAndCorporate
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('api')->check() || Auth::guard('employee_api')->check()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized.'], 401);
    }
}

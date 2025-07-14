<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Guest
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->cookie('access_token')) {
            return redirect()->route('dashboard-analytics');
        }

        return $next($request);
    }
}

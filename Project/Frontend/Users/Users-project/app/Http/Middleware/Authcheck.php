<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Authcheck
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        $accessToken = $request->cookie('access_token');
        // Log::info($accessToken);
        if (!$accessToken) {
            return redirect()->route('auth-login-basic')->with('error', 'Please log in to access this page.');
        }

        // Check if the 2FA is required (stored in session)
        // if (session('needs_2fa', false)) {
        //     return redirect()->route('auth-2fa')->with('error', 'Please complete the 2FA process.');
        // }

        return $next($request);
    }
}

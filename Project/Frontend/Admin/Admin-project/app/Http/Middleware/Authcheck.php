<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Authcheck
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->cookie('access_token');
        if (!$this->checkToken($request, $accessToken)) {
            return redirect()->route('auth-login-basic')->with('error', 'Please log in to access this page.');
        }
        if (!$accessToken) {
            return redirect()->route('auth-login-basic')->with('error', 'Please log in to access this page.');
        }
        if (session('needs_2fa', false)) {
            return redirect()->route('auth-2fa')->with('error', 'Please complete the 2FA process.');
        }
        return $next($request);
    }

    private function checkToken(Request $request, $token)
    {
        $pageConfigs = ['myLayout' => 'blank'];
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get("https://api-admin.hygeiaes.com/api/getWhoAmI");
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            $responseData = $apiResponse->json();
            if (isset($responseData)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

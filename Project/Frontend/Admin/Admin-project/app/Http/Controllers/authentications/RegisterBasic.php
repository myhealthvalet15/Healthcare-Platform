<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\log;


class RegisterBasic extends Controller
{
    public function index(Request $request)
    {
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-admin.hygeiaes.com/api/getWhoAmI");
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            return redirect()->route('dashboard-analytics');
        }
        return redirect()
            ->back()
            ->withErrors(['registration_disabled' => 'Registration is currently disabled.'])
            ->withInput();
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.authentications.auth-register-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function register(Request $request)
    {
        return redirect()
            ->back()
            ->withErrors(['registration_disabled' => 'Registration is currently disabled.'])
            ->withInput();
        $validator = Validator::make($request->all(), [
            'username' => 'required|string', // Adding min/max validation
            'email' => 'required|string',    // Email format validation
            'password' => 'required|string|min:8', // Password strength validation
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $en = 1;
            $apiResponse = Http::post('https://api-admin.hygeiaes.com/api/register', [
                'email' => $request->email,
                'password' => $request->password,
                'name' => $request->username,
                'en_c' => $en,
            ]);

            if ($apiResponse->successful()) {
                return redirect()
                    ->route('auth-login-basic')
                    ->with('success', 'Registration successful! Please login to continue.');
            } else {
                return response()->json([
                    'error' => 'Registration failed: ' . $apiResponse->body(),
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class LoginBasic extends Controller
{
    public function index(Request $request)
    {
        // TODO: To Rework in this messy function ...
        $pageConfigs = ['myLayout' => 'blank'];
        if (session('needs_2fa')) {
            return view("content.authentications.auth-two-steps-basic", ['pageConfigs' => $pageConfigs]);
        }
        return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
        // TODO: Once Login issue is fixed, this part to be modified ....
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/api/getWhoAmI");
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            $responseData = $apiResponse->json();
            if (isset($responseData)) {
                return redirect()->route('dashboard-analytics');
            } else {
                return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
            }
        } else {
            return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
        }
        return;
        $pageConfigs = ['myLayout' => 'blank'];
        if (session('needs_2fa')) {
            return view("content.authentications.auth-two-steps-basic", ['pageConfigs' => $pageConfigs]);
        }
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/api/getWhoAmI");
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            $responseData = $apiResponse->json();
            if (isset($responseData)) {
                return redirect()->route('dashboard-analytics');
            } else {
                return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
            }
        } else {
            return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
        }
    }
    public function auth2Fa()
    {
        if (!session('needs_2fa', false)) {
            return redirect()->route('auth-login-basic');
        }
        $pageConfigs = ['myLayout' => 'blank'];
        return view("content.authentications.auth-two-steps-basic", ['pageConfigs' => $pageConfigs]);
    }
    public function freshLogin(Request $request)
    {
        $this->deleteSessionsandCookies();
        return redirect('/auth/login');
    }
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_username' => 'required|string',
                'password' => 'required|string',
                'login_reset' => 'boolean',
                'captcha_type' => 'required|string',
            ]);
            $validator->sometimes('g-recaptcha', 'required|string', function ($input) {
                return $input->captcha_type === 'google_v3';
            });
            $validator->sometimes('an-recaptcha', 'required|string|captcha', function ($input) {
                return $input->captcha_type === 'anCaptcha';
            });
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('toastr_error', 'Validation failed.');
            }
            if ($request->captcha_type === 'google_v3') {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('G_CAPTCHA_SECRET_KEY'),
                    'response' => $request->input('g-recaptcha'),
                    'remoteip' => $request->ip(),
                ]);
                $responseData = $response->json();
                if (!$responseData['success'] or $responseData['hostname'] !== $_SERVER['HTTP_HOST']) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['g-recaptcha' => 'Invalid reCAPTCHA.'])
                        ->with('toastr_error', 'reCAPTCHA verification failed.');
                }
            }
            $token = bin2hex(random_bytes(32));
            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("https://api-user.hygeiaes.com/api/login", [
                'email_username' => $request->email_username,
                'password' => $request->password,
                '2FaToken' => $token,
                'login_reset' => $request->login_reset ? $request->login_reset : 0,
                'en_c' => 1,
                // 'Ien_c' => 1,
            ]);
            if ($apiResponse->successful() && $apiResponse->status() == 200) {
                $responseData = $apiResponse->json();
               // print_r($responseData);
                $accessToken = $responseData['tokens']['access_token'] ?? null;
                if (isset($responseData['needs_otp']) && $responseData['needs_otp'] === true or isset($responseData['code_expired']) && $responseData['code_expired'] === true) {
                    session(['needs_2fa' => true]);
                    $message = $responseData['message'] ?? 'Verification required.';
                    return redirect()->route('auth-2fa')->with('2fa_message', $message);
                }
                if ($accessToken) {
                    Cache::forget('menuData');
                    $cookie = cookie(
                        'access_token',
                        $accessToken,
                        60 * 24,
                        '/',
                        null,
                        true,
                        true
                    );
                    session(['needs_2fa' => false]);
                    session(['username' => $responseData['email']]);
                    session(['2faToken' => $token]);
                    session(['corporate_admin_user_id' => $responseData['corporate_admin_user_id']]);
                    session(['master_user_user_id' => $responseData['master_user_user_id']]);
                    session(['first_name' => $responseData['first_name']]);
                    session(['last_name' => $responseData['last_name']]);
                    session(['corporate_id' => $responseData['corporate_id']]);
                    session(['location_id' => $responseData['location_id']]);
                    session(['employee_id' => $responseData['employee_id']]);
                    session(['corporate_name' => $responseData['corporate_name']]);
                    session(['location_name' => $responseData['location_name']]);
                    session(['user_type' => $responseData['user_type']]); 
                  

                    if ($responseData['user_type'] === "CorporateAdminUser") {
                        return redirect()->route('dashboard-analytics')->with('success', 'Login successful!')->withCookie($cookie);
                    } elseif ($responseData['user_type'] === "MasterUser") {
                        return redirect()->route('employee-user-dashboard-analytics')->with('success', 'Login successful!')->withCookie($cookie);
                    }
                } else {
                    $errorMessage = $responseData['message'] ?? 'Invalid Credentials.';
                    return redirect()->back()->withErrors(['error' => $errorMessage])->withInput()->setStatusCode($apiResponse->status());
                }
            } else {
                $errorMessage = $apiResponse->json()['message'] ?? 'Invalid Credentials.';
                return redirect()->back()->withErrors(['error' => $errorMessage])->withInput()->setStatusCode($apiResponse->status());
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Internal Server Error, Please try again.'])->withInput()->setStatusCode(500);
        }
    }
    public function toggle2FA($isEnabled, Request $request)
    {
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post("https://api-user.hygeiaes.com/V1/auth/reset/toggle2FA", [
            'email_username' => session('username'),
            'isEnable' => $isEnabled
        ]);
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            $responseData = $apiResponse->json();
            return response()->json([
                'result' => true,
                'message' => 'Fetched 2FA status successfully',
                'data' => $responseData
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => "Internal server error."
            ], 400);
        }
    }
    public function getUserDetails(Request $request)
    {
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/api/getWhoAmI");
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            $responseData = $apiResponse->json();
            return response()->json([
                'result' => true,
                'message' => 'Fetched 2FA status successfully',
                'data' => $responseData
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => "Internal server error."
            ], 400);
        }
    }
    public function resendOtp(Request $request)
    {
        try {
            if (!session('2faToken') or !session('username') or strlen(session('2faToken')) != 64) {
                $this->deleteSessionsandCookies();
                return response()->json([
                    'success' => false,
                    'message' => 'Bad Request, Login again..'
                ]);
            }
            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("https://api-user.hygeiaes.com/api/resend-otp", [
                '2FaToken' => session('2faToken'),
                'username' => session('username'),
            ]);
            if ($apiResponse->successful()) {
                return response()->json([
                    'success' => $apiResponse['result'] ? $apiResponse['result'] : false,
                    'message' => $apiResponse['message'] ? $apiResponse['message'] : 'Something went wrong, Please try again later..'
                ], $apiResponse->status());
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $apiResponse->json()['message'] ?? 'Failed to resend code'
                ], $apiResponse->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function verifyCode(Request $request, $otp = null)
    {
        if (!is_numeric($otp) or !session('2faToken') or strlen($otp) != 6) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Request'
            ], 422);
        }
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post("https://api-user.hygeiaes.com/api/verify-otp", [
            'verificationToken' => session('2faToken'),
            'otp' => $otp,
            'username' => session('username'),
        ]);
        if ($apiResponse->successful()) {
            $accessToken = $apiResponse['access_token'] ?? null;
            if ($accessToken) {
                session(['needs_2fa' => false]);
                $cookie = cookie(
                    'access_token',
                    $accessToken,
                    60 * 24,
                    '/',
                    null,
                    true,
                    true
                );
                return response()->json([
                    'result' => true,
                    'message' => 'Login successful!',
                    'redirect_url' => route('dashboard-analytics')
                ])->withCookie($cookie);
            }
            return response()->json([
                'result' => false,
                'message' => 'Invalid Session, Login Again',
            ], $apiResponse->status());
        } else {
            return response()->json([
                'result' => false,
                'message' => $apiResponse['message'],
            ], $apiResponse->status());
        }
    }
    public function logout(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/api/logout'); 
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                $this->deleteSessionsandCookies();
                return redirect()->route('auth-login-basic')->with('success', 'Logged out Successfully.');
            }
            return response()->json(['result' => 'error', 'message' => 'Logout Failed'], 500);
        } catch (\Exception $e) {
            return redirect()->route('auth-login')->with('error', 'An error occurred during logout.');
        }
    }
    private function deleteSessionsandCookies()
    {
        Session::flush();
        Cache::forget('menuData');
        Cookie::queue(Cookie::forget('access_token'));
        Cookie::queue(Cookie::forget('cf_clearance'));
        Cookie::queue(Cookie::forget('ext_name'));
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        Cookie::queue(Cookie::forget('laravel_session'));
    }
    public function resetPasswordView(Request $request, $token)
    {
        if (!is_string($token)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Token',
            ], 400);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post("https://api-user.hygeiaes.com/V1/auth/reset/verify-reset-token", [
            'resetToken' => $token,
        ]);
        if ($response->successful()) {
            $pageConfigs = ['myLayout' => 'blank'];
            return view('content.authentications.auth-reset-password', ['pageConfigs' => $pageConfigs]);
        }
        return "<h3>Invalid Reset Token !!</h3>";
    }
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string',
                'confirm_password' => 'required|string',
                'resetToken' => 'required|string',
                'captcha_type' => 'required|string',
            ]);
            $validator->sometimes('g-recaptcha', 'required|string', function ($input) {
                return $input->captcha_type === 'google_v3';
            });
            $validator->sometimes('an-recaptcha', 'required|string|captcha', function ($input) {
                return $input->captcha_type === 'anCaptcha';
            });
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            if ($request->captcha_type === 'google_v3') {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('G_CAPTCHA_SECRET_KEY'),
                    'response' => $request->input('g-recaptcha'),
                    'remoteip' => $request->ip(),
                ]);
                $responseData = $response->json();
                if (!$responseData['success'] or $responseData['hostname'] !== $_SERVER['HTTP_HOST']) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['g-recaptcha' => 'Invalid reCAPTCHA.'])
                        ->with('toastr_error', 'reCAPTCHA verification failed.');
                }
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post("https://api-user.hygeiaes.com/V1/auth/reset/reset-password", [
                'new_password' => $request->input('new_password'),
                'confirm_password' => $request->input('confirm_password'),
                'resetToken' => $request->input('resetToken'),
            ]);
            if ($response->status() === 401) {
                return redirect()->back()->withErrors(['Unauthorized. Please check your credentials.'])->withInput();
            }
            if ($response->successful()) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ])->get('https://api-user.hygeiaes.com/api/logout');
                if ($response->status() === 401) {
                    return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
                }
                if ($response->successful()) {
                    $this->deleteSessionsandCookies();
                    return redirect()->route('auth-login-basic')->with('success', 'Password reset successfully. Please log in.');
                }
                return response()->json(['result' => 'error', 'message' => 'Logout Failed'], 500);
            }
            $errorMessage = $response->json()['message'] ?? 'Invalid Credentials.';
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput()->setStatusCode($response->status());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['An unexpected error occurred. Please try again later.'])->withInput();
        }
    }
    public function requestPasswordReset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'encryptedPassword' => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => false,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post("https://api-user.hygeiaes.com/V1/auth/reset/request-password-reset", [
                'currentPassword' => $request->input('encryptedPassword')
            ]);
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Request raised successful.',
                ], 200);
            }
            $responseData = $response->json();
            return response()->json([
                'result' => $responseData['result'] ?? false,
                'message' => $responseData['message'] ?? 'An error occurred while processing your request.',
                'details' => $responseData
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
}

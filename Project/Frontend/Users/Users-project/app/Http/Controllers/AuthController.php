<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
      //  dd($request->all());

        try {
            $validator = Validator::make($request->all(), [
                'email-username' => 'required',
                'password' => 'required|string|min:6',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Access validated input
            $validatedData = $validator->validated(); // Returns only validated data
            $email = $validatedData['email-username']; // Debugging the password field

            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("https://api-user.hygeiaes.com/api/login", [
                'email_username' => $email,
                'password' => $request->password,
                'en_c' => 1,
            ]);
            $responseData = $apiResponse->json();
            return $responseData;
            if ($apiResponse->successful() && $apiResponse->status() == 200) {
                $responseData = $apiResponse->json();
                $accessToken = $responseData['access_token'] ?? null;
                $adminData = $responseData['admin'] ?? null;

                if ($adminData) {
                    $corporateId = $adminData['corporate_id'] ?? null;
                    $locationId = $adminData['location_id'] ?? null;
                    session(['corporate_id' => $corporateId]);
                    session(['location_id' => $locationId]);
                }
                if (isset($responseData['needs_otp']) && $responseData['needs_otp'] === true or isset($responseData['code_expired']) && $responseData['code_expired'] === true) {
                    session(['needs_2fa' => true]);
                    $message = $responseData['message'] ?? 'Verification required.';
                    return redirect()->route('auth-2fa')->with('2fa_message', $message);
                }
                if ($accessToken) {
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
                    return redirect()->route('dashboard-analytics')->with('success', 'Login successful!')->withCookie($cookie);
                }
            } else {
                $errorMessage = $apiResponse->json()['message'] ?? 'Invalid Credentials.';
                return redirect()->back()->withErrors(['error' => $errorMessage])->withInput()->setStatusCode($apiResponse->status());
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Internal Server Error, Please try again.'])->withInput()->setStatusCode(500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mhvadmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MailController;
use Illuminate\Support\Carbon;
use App\Models\Corporate\MasterUser;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\CorporateAdminUser;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use App\Models\MasterCorporate;

class AuthController extends Controller
{
    private $codeExpireTime = 10;
    public function getWhoAmI(Request $request)
    {
        return response()->json($request->user());
    }
    private function sendOtptoMail(Request $request, $to, $subject, $body, $otp)
    {
        try {
            $mailController = new MailController();
            $mailController->sendEmail($request, $subject, $to, $body, "verification");
            return response()->json([
                'result' => true,
                'message' => 'Verification code sent to your email. Please enter the code to complete login.',
                'needs_otp' => true,
                'otp_sent_now' => true,
                'valid_for' => $this->codeExpireTime
            ], 200);
        } catch (Exception $e) {
            Log::error("Error sending OTP: " . $e->getMessage());
            return response()->json([
                'result' => false,
                'message' => 'Failed to send OTP. Please try again later.',
                'needs_otp' => false,
                'otp_sent_now' => false,
                'code_expired' => false
            ], 500);
        }
    }
    private function doVerification($admin, $resend)
    {
        $current_time = now();
        if (
            $admin->verification_resend_attempts >= 3 &&
            $admin->verification_resend_attempts_locked_until &&
            $current_time->lessThan(Carbon::parse($admin->verification_resend_attempts_locked_until))
        ) {
            $lockedUntil = Carbon::parse($admin->verification_resend_attempts_locked_until);
            $remainingTime = $lockedUntil->diff($current_time);
            return response()->json([
                'result' => false,
                'message' => "Please wait {$remainingTime->h} hour(s) and {$remainingTime->i} minute(s) before requesting another code, on behalf of 3 failiure attempts",
                'needs_otp' => false,
                'otp_sent_now' => false,
                'code_expired' => true,
                'verification_resend_attempts_exceeded' => true,
            ], 429);
        }
        if (
            $admin->verification_resend_attempts >= 3 &&
            $admin->verification_resend_attempts_locked_until &&
            $current_time->greaterThan($admin->verification_resend_attempts_locked_until)
        ) {
            $admin->update([
                'verification_resend_attempts' => 0,
                'verification_code' => null,
                'verification_resend_attempts_locked_until' => null
            ]);
        }
        if ($admin->verification_expires_at == null and $admin->verification_resend_attempts < 1 or $resend === true) {
            return $this->sendOtp($admin);
        }
        if (!$admin->verification_expires_at || $current_time->greaterThan($admin->verification_expires_at)) {
            return response()->json([
                'result' => false,
                'message' => 'Please request a new verification code, as your code has been expired.',
                'needs_otp' => false,
                'code_expired' => true,
                'otp_sent_now' => false,
                'valid_for' => $this->codeExpireTime
            ], 200);
        }
        return response()->json([
            'result' => true,
            'message' => 'Verification code already sent to your email.',
            'needs_otp' => true,
            'code_expired' => false,
            'otp_sent_now' => true,
            'valid_for' => $this->codeExpireTime
        ], 200);
    }
    private function sendOtp($admin)
    {
        $verificationCode = mt_rand(100000, 999999);
        $admin->update([
            'verification_code' => Hash::make($verificationCode),
            'verification_expires_at' => now()->addMinutes(10),
        ]);
        $subject = "Your Verification Code";
        $toEmail = $admin->email;
        $body = "Your verification code is: $verificationCode. This code is valid for 10 minutes.";
        return $this->sendOtptoMail(request(), $toEmail, $subject, $body, $verificationCode);
    }
    private function DecryptData($data, $encodeType = null)
    {
        try {
            if ($encodeType === "base64") {
                $data = base64_decode($data);
            }
            $privateKey = env('PRIVATE_KEY');
            $passphrase = env('PRIVATE_KEY_PHASSPHRASE');
            $privateKeyResource = openssl_pkey_get_private($privateKey, $passphrase);
            $decrypted = '';
            $result = openssl_private_decrypt(base64_decode($data), $decrypted, $privateKeyResource);
            if (!$result) {
                return false;
            }
            return $decrypted;
        } catch (Exception $e) {
            Log::error('Error in DecryptData: ' . $e->getMessage());
            return false;
        }
    }
    private function sendOauthTokenRequest($clientId, $clientSecret, $username, $password, $provider = 'corporate_admin_users')
    {
        if (empty($clientId) || empty($clientSecret) || empty($username) || empty($password)) {
            return false;
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post("https://api-user.hygeiaes.com/oauth/token", [
            "grant_type" => "password",
            "client_id" => $clientId,
            "client_secret" => $clientSecret,
            "username" => $username,
            "password" => $password,
            "provider" => $provider
        ]);
        if ($response->status() == 200) {
            return $response->json();
        }
        return false;
    }
    private function authenticateUserWithEmailHash($username, $password, $token2fa)
    {
        try {
            $user_type = null;
            if (empty($username) || empty($password)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Username and password are required.'
                ], 401);
            }
            $emailHash = $this->genEmailHash($username);
            $corporateLoginTables = explode(',', env('CORPORATE_LOGIN_TABLES'));
            $user = null;
            foreach ($corporateLoginTables as $modelClass) {
                $foundUser = $modelClass::where('email_hash', $emailHash)->first();
                if ($foundUser) {
                    if ($username !== $this->aes256DecryptData($foundUser->email)) {
                        continue;
                    }
                    $user = $foundUser;
                    break;
                }
            }
            if (!$user) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid credentials.'
                ], 401);
            }
            if (get_class($user) === 'App\Models\Corporate\MasterUser') {
                $user_type = "MasterUser";
                $employeeMapping = EmployeeUserMapping::where('user_id', $user->user_id)->first();
                if (!$employeeMapping) {
                    return response()->json([
                        'result' => false,
                        'message' => 'User mapping not found.'
                    ], 401);
                }
                $user->id = $employeeMapping->id;
                $user->employee_id = $employeeMapping->employee_id;
                $user->location_id = $employeeMapping->location_id;
                $user->corporate_id = $employeeMapping->corporate_id;
                $isAuthenticated = $this->sendOauthTokenRequest(
                    env('EMPLOYEE_USERS_PASSPORT_CLIENT_ID'),
                    env('EMPLOYEE_USERS_PASSPORT_CLIENT_SECRET'),
                    $user->email,
                    $password,
                    'employee_users'
                );
            } elseif (get_class($user) === 'App\Models\CorporateAdminUser') {
                $user_type = "CorporateAdminUser";
                $isAuthenticated = $this->sendOauthTokenRequest(
                    env('CORPORATE_ADMIN_USERS_PASSPORT_CLIENT_ID'),
                    env('CORPORATE_ADMIN_USERS_PASSPORT_CLIENT_SECRET'),
                    $user->email,
                    $password,
                );
            } else {
                // TODO: To handle other logins
                return response()->json([
                    'result' => false,
                    'message' => 'User type not supported.'
                ], 401);
            }
            if (!$isAuthenticated) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid credentials.'
                ], 401);
            }
            $user->tokens = $isAuthenticated;
            $corporateDetails = MasterCorporate::where('corporate_id', $user->corporate_id)
                ->where('location_id', $user->location_id)
                ->first();
            if ($corporateDetails) {
                $user->corporate_name = $corporateDetails->corporate_name;
                $user->location_name = $corporateDetails->display_name;
            }
            if ($user->two_factor_enabled) {
                if (empty($token2fa)) {
                    $user->update([
                        'verification_resend_token' => Hash::make(Str::random(6))
                    ]);
                    return $this->doVerification($user, $resend = false);
                }
                if (!Hash::check($token2fa, $user->verification_resend_token)) {
                    return response()->json([
                        'result' => false,
                        'message' => 'Invalid verification code.'
                    ], 401);
                }
            }
            return response()->json([
                'result' => true,
                'message' => 'Login successful.',
                'user_type' => $user_type,
                'tokens' => $user->tokens,
                'token_type' => $user->tokens['token_type'] ?? null,
                'email' => $this->aes256DecryptData($user->email),
                'first_name' => $this->aes256DecryptData($user->first_name),
                'last_name' => $this->aes256DecryptData($user->last_name),
                'corporate_id' => $user->corporate_id,
                'location_id' => $user->location_id,
                'employee_id' => $user->employee_id,
                'location_name' => $user->location_name ?? null,
                'corporate_name' => $user->corporate_name ?? null,
                'corporate_admin_user_id' => $user->corporate_admin_user_id ?? null,
                'master_user_user_id' => $user->user_id ?? null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An unexpected error occurred during login.'
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_username' => 'required|string',
                'password' => 'required|string',
                'en_c' => 'required|integer|in:0,1',
                '2FaToken' => 'string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid request'
                ], 400);
            }
            $userId = $request->input('email_username');
            $password = $request->input('password');
            $isEncrypted = $request->input('en_c');
            $token2fa = $request->input('2FaToken');
            if ($isEncrypted == 1) {
                $userId = $this->DecryptData($userId, "base64");
                $password = $this->DecryptData($password, "base64");
            }
            $admin = $this->authenticateUserWithEmailHash($userId, $password, $token2fa);
            return $admin;
        } catch (Exception $e) {
            return response()->json(['result' => true, 'message' => 'An unexpected error occurred during login'], 500);
        }
    }
    private function aes256DecryptData(string $data)
    {
        if ($data === null) {
            return null;
        }
        $decodedData = base64_decode($data);
        if ($decodedData === false) {
            throw new Exception('Failed to base64 decode data.');
        }
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($decodedData, 0, $ivLength);
        $encryptedData = substr($decodedData, $ivLength);
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $decryptedData = openssl_decrypt($encryptedData, $cipher, $key, 0, $iv);
        if ($decryptedData === false) {
            throw new Exception('Decryption failed');
        }
        return $decryptedData;
    }
    private function genEmailHash($email)
    {
        return hash('sha256', $email);
    }
    public function logout(Request $request)
    {
        try {
            $apiUser = auth('api')->user();
            if ($apiUser && $apiUser->token()) {
                $apiUser->token()->revoke();
            }
            $employeeUser = auth('employee_api')->user();
            if ($employeeUser && $employeeUser->token()) {
                $employeeUser->token()->revoke();
            }
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred during logout'], 500);
        }
    }
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '2FaToken' => 'required|string',
            'username' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors(),
            ], 400);
        }
        $token = $request->input('2FaToken');
        $username = $this->DecryptData($request->input('username'), "base64");
        $admin = Mhvadmin::where(function ($query) use ($username) {
            $query->where('admin_name', $username)
                ->orWhere('email', $username);
        })->whereNotNull('verification_resend_token')->first();
        if (! $admin || ! Hash::check($token, $admin->verification_resend_token)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid token or username',
            ], 400);
        }
        $admin->update([
            'verification_resend_attempts' => $admin->verification_resend_attempts + 1,
        ]);
        $admin->update([
            'verification_attempts' => 0,
        ]);
        if ($admin->verification_resend_attempts === 3) {
            $admin->update([
                'verification_resend_attempts_locked_until' => now()->addHours(3),
            ]);
        }
        return $this->doVerification($admin, $resend = true);
    }
    public function verifyCode(Request $request)
    {
        $current_time = now();
        $validator = Validator::make($request->all(), [
            'otp' => 'required|integer',
            'verificationToken' => 'required|string',
            'username' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid request, ' . $validator->errors()
            ], 400);
        }
        $userId = $this->DecryptData($request->input('username'), 'base64');
        $verificationToken = $request->input('verificationToken');
        $verificationCode = $request->input('otp');
        $admin = Mhvadmin::where(function ($query) use ($userId) {
            $query->where('admin_name', $userId)
                ->orWhere('email', $userId);
        })->first();
        if (!$admin) {
            return response()->json(['result' => 'false', 'message' => 'User not found'], 404);
        }
        if ($admin->two_factor_enabled) {
            if (
                $admin->verified === 1 and
                $admin->verification_code === null and
                $admin->verification_expires_at === null and
                $admin->verification_attempts === 0 and
                $admin->verification_attempts_locked_until === null and
                $admin->verification_resend_attempts === 0 and
                $admin->verification_resend_attempts_locked_until === null and
                $admin->verification_resend_token = null
            ) {
                return response()->json(['result' => 'true', 'message' => "You've already been verified"], 200);
            }
            $isVerifiedCode = password_verify($verificationCode, $admin->verification_code);
            $isVerifiedToken = password_verify($verificationToken, $admin->verification_resend_token);
            $existingVerificationAttempts = $admin->verification_attempts;
            $verificationAttemptsLockedUntil = $admin->verification_attempts_locked_until;
            if (
                $existingVerificationAttempts >= 3 &&
                $verificationAttemptsLockedUntil &&
                $current_time->greaterThan($verificationAttemptsLockedUntil)
            ) {
                $admin->update([
                    'verification_attempts' => 0,
                    'verification_attempts_locked_until' => null
                ]);
            }
            if ($existingVerificationAttempts <= 3 and $current_time->lessThan(Carbon::parse($verificationAttemptsLockedUntil))) {
                $admin->update([
                    'verification_attempts' => $admin->verification_attempts + 1
                ]);
            }
            if ($existingVerificationAttempts >= 3 && $current_time->lessThan(Carbon::parse($verificationAttemptsLockedUntil))) {
                $remainingTime = Carbon::parse($verificationAttemptsLockedUntil)->diff($current_time);
                $formattedTime = '';
                if ($remainingTime->h > 0) {
                    $formattedTime .= $remainingTime->h . ' hour' . ($remainingTime->h > 1 ? 's' : '') . ' ';
                }
                if ($remainingTime->i > 0 || $remainingTime->h > 0) {
                    $formattedTime .= $remainingTime->i . ' minute' . ($remainingTime->i > 1 ? 's' : '') . ' ';
                }
                return response()->json([
                    'result' => false,
                    'message' => "Verification Attempts Exceeded, Please try again in $formattedTime",
                ], 429);
            }
            if ($admin->verification_attempts === 3) {
                $admin->update([
                    'verification_attempts_locked_until' => now()->addHours(3),
                ]);
            }
            if (! $isVerifiedToken) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request, Click login again Button and login again.'
                ], 401);
            }
            if (! $isVerifiedCode) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid OTP'
                ], 401);
            }
            if (now()->greaterThan($admin->verification_expires_at)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Verification Code Expired, Please request new otp and try to login with that.'
                ], 401);
            }
            $admin->verified = 1;
            $admin->verification_code = null;
            $admin->verification_expires_at = null;
            $admin->verification_attempts = 0;
            $admin->verification_attempts_locked_until = null;
            $admin->verification_resend_attempts = 0;
            $admin->verification_resend_attempts_locked_until = null;
            $admin->verification_resend_token = null;
            $admin->save();
            $tokenResult = $admin->createToken('Personal Access Token')->accessToken;
            return response()->json([
                'result' => true,
                'message' => 'Login Successful.',
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'admin_name' => $admin->admin_name,
                'email' => $admin->email
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'Bad request'
            ], 403);
        }
    }
    public function toggle2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_username' => 'required|string',
            'isEnable' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }
        $username = $this->DecryptData($request->input('email_username'), "base64");
        $isEnable = $request->input("isEnable");
        $user = $request->user();
        if ($username === $user->admin_name || $username === $user->email) {
            $user->two_factor_enabled = $isEnable;
            $user->save();
            return response()->json([
                'message' => $isEnable ? '2FA enabled.' : '2FA disabled.',
                'two_factor_enabled' => $user->two_factor_enabled
            ], 200);
        }
        return response()->json([
            'message' => 'Username or email does not match the logged-in user.',
        ], 400);
    }
    public function requestPasswordReset(Request $request)
    {
        $current_time = now();
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }
        $currentPassword = $this->DecryptData($request->input('currentPassword'), "base64");
        $email = $request->user()->email;
        if (!password_verify($currentPassword, $request->user()->password)) {
            return response()->json([
                'result' => false,
                'message' => "You've entered the wrong password",
            ], 401);
        }
        $admin = Mhvadmin::where('email', $email)->first();
        if (!$admin) {
            return response()->json([
                'result' => false,
                'message' => 'Email not found'
            ], 404);
        }
        if (
            $admin->password_reset_attempts >= 3 &&
            $admin->password_reset_attempts_locked_until &&
            $current_time->lessThan(Carbon::parse($admin->password_reset_attempts_locked_until))
        ) {
            $lockedUntil = Carbon::parse($admin->password_reset_attempts_locked_until);
            $remainingTimeInHours = $current_time->diffInHours($lockedUntil, false);
            $remainingTime = "{$remainingTimeInHours} hours";
            return response()->json([
                'result' => false,
                'message' => "You can't change the password for next {$remainingTime}",
                'verification_resend_attempts_exceeded' => true,
            ], 429);
        }
        if (
            $admin->password_reset_attempts >= 3 &&
            $admin->password_reset_attempts_locked_until &&
            $current_time->greaterThan($admin->password_reset_attempts_locked_until)
        ) {
            $admin->update([
                'password_reset_attempts' => 0,
                'password_reset_token' => null,
                'password_reset_attempts_locked_until' => null
            ]);
        }
        $token = Str::random(60);
        $admin->update([
            'password_reset_attempts' => $admin->password_reset_attempts + 1,
            'password_reset_token' => Hash::make($token),
            'password_reset_token_expires_at' => now()->addMinutes(10),
        ]);
        if ($admin->password_reset_attempts === 3) {
            $admin->update([
                'password_reset_attempts_locked_until' => now()->addDays(2),
            ]);
        }
        $resetLink = env('FRONTEND_CHANNEL_URL') . "/auth/reset-password/$token";
        try {
            $mailController = new MailController();
            $mailController->sendEmail(
                $request,
                "Password Reset Request",
                $admin->email,
                "Click the link to reset your password: $resetLink, Only will be valid for next 10 min",
                "password_reset"
            );
            return response()->json([
                'result' => true,
                'message' => 'Password reset link sent to your email.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to send reset link. Please try again later.',
            ], 500);
        }
    }
    public function resetPassword(Request $request)
    {
        $current_time = now();
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string',
            'confirm_password' => 'required|string',
            'resetToken' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid request',
                'errors' => $validator->errors()
            ], 400);
        }
        $admin = $request->user();
        if (!password_verify($request->input('resetToken'), $admin['password_reset_token'])) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Token',
            ], 400);
        }
        if ($current_time->greaterThan($admin->password_reset_token_expires_at)) {
            return response()->json([
                'result' => false,
                'message' => 'Request has been expired. Please request a new reset link.',
            ], 400);
        }
        $new_password = $this->DecryptData($request->input('new_password'), "base64");
        $confirm_password = $this->DecryptData($request->input('confirm_password'), "base64");
        if ($new_password !== $confirm_password) {
            return response()->json([
                'result' => false,
                'message' => 'new password and confirm password doesnt matched.',
            ], 400);
        }
        if (password_verify($new_password, $admin->password)) {
            return response()->json([
                'result' => false,
                'message' => 'New password should be different from the old password.',
            ], 400);
        }
        $options = ['cost' => 12,];
        $password = password_hash($new_password, PASSWORD_BCRYPT, $options);
        $admin->update([
            'password' => $password,
            'password_reset_token' => null,
            'password_reset_token_expires_at' => null,
            'last_password_reset_at' => $current_time
        ]);
        return response()->json([
            'result' => true,
            'message' => 'Password changed successfully.',
        ], 200);
    }
    public function validateResetToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resetToken' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }
        $token = $request->user()->password_reset_token;
        if (password_verify($request->input('resetToken'), $token)) {
            return response()->json([
                'result' => true,
                'message' => 'Reset token verified successfully',
            ], 200);
        }
        return response()->json([
            'result' => false,
            'message' => 'Invalid Reset token.',
        ], 400);
    }
}

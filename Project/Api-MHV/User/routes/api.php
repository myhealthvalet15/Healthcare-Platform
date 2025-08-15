<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;

Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// TODO: To be Moved
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyCode']);
Route::post('/findAdminByToken', [AuthController::class, 'findAdminByToken']);
// TODO: To be Moved
Route::get('/getWhoAmI', [AuthController::class, 'getWhoAmI']);
Route::get('/email/preview', [MailController::class, 'previewEmailTemplate'])->name('email.preview');

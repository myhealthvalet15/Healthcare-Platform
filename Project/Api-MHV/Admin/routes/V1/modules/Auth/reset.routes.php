<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['auth:api'])->group(function () {
    Route::post('/toggle2FA', [AuthController::class, 'toggle2fa']);
    Route::post('/request-password-reset', [AuthController::class, 'requestPasswordReset']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-reset-token', [AuthController::class, 'validateResetToken']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1Controllers\HraController\Master_Tests\MasterTestController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getTest/{id}', [MasterTestController::class, 'getTest']);
    Route::get('/getAllTests', [MasterTestController::class, 'getAllTests']);
    Route::get('/getAllTestNames', [MasterTestController::class, 'getTestNamesAndIds']);
    Route::post('/addTest', [MasterTestController::class, 'addTest']);
    Route::put('/editTest/{id}', [MasterTestController::class, 'updateTest']);
    Route::delete('/deleteTest/{id}', [MasterTestController::class, 'deleteTest']);
});

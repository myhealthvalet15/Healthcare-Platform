<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HraController\Master_Tests\MasterTestController;

Route::middleware(['auth:api'])->group(function () {
    // Here include the routes only accessible for corporate and not employees
    Route::get('/getAllTestNames', [MasterTestController::class, 'getTestNamesAndIds']);
    Route::get('/getTest/{id}', [MasterTestController::class, 'getTest']);
    Route::post('/addTest', [MasterTestController::class, 'addTest']);
    Route::put('/editTest/{id}', [MasterTestController::class, 'updateTest']);
    Route::delete('/deleteTest/{id}', [MasterTestController::class, 'deleteTest']);
});
Route::middleware(['authGuard.corporate.employee'])->group(function () {
    // Here include the routes which can be accessed by both corporate and employees
    Route::get('/getAllTests', [MasterTestController::class, 'getAllTests']);
});

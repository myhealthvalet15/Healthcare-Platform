<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HraController\Factors\FactorController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getallfactors', [FactorController::class, 'getAllFactors']);
    Route::get('/suggestfactorid', [FactorController::class, 'getSuggestedFactorId']);
    Route::post('/add-factors', [FactorController::class, 'store']);
    Route::put('/editfactor/{factorId}', [FactorController::class, 'editFactor']);
    Route::get('/getfactor/{factorId}', [FactorController::class, 'getFactorById']);
    // Route::delete('/deleteallfactors', [FactorController::class, 'deleteAllFactors']);
    Route::delete('/deletefactor/{factorId}', [FactorController::class, 'deleteFactor']);
    Route::get('/getpriority/{factorId}', [FactorController::class, 'getPriority']);
    Route::put('/setpriority/{factorId}', [FactorController::class, 'setPriority']);
    Route::get('/getactivestatus/{factorId}', [FactorController::class, 'getActiveStatus']);
    Route::put('/setactivestatus/{factorId}', [FactorController::class, 'setActiveStatus']);
});

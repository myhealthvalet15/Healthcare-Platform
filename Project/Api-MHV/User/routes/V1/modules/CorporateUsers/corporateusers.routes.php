<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CorporateUsers\CorporateUserController;
use App\Http\Controllers\Corporate\CorporateComponents\ModulesController;

Route::middleware(['auth:api'])->group(function () {
    // Here include the routes only accessible for corporate and not employees
});
Route::middleware(['authGuard.corporate.employee'])->group(function () {
    // Here include the routes which can be accessed by both corporate and employees
    Route::get('/getAllUsersDetails/{corporateId}/{locationId}', [CorporateUserController::class, 'getAllUsersDetails']);
    Route::post('/addCorporateUSer', [CorporateUserController::class, 'addCorporateUSer'])->name('addUser');
    Route::get('/getUserById/{id}', [CorporateUserController::class, 'getUserById'])->name('getUserById');
    Route::put('/updateUser/{id}', [CorporateUserController::class, 'updateUser']);
    Route::get('/getmhcmenu/{corporateId}/{locationId}/{id}', [CorporateUserController::class, 'getmhcMenu']);
    Route::post('/mhcRightsSave', [CorporateUserController::class, 'mhcRightsSave'])->name('mhcRightsSave');
    Route::put('/mhcRightsUpdate', [CorporateUserController::class, 'mhcRightsUpdate']);
    Route::get('/getohcmenu/{corporateId}/{locationId}/{id}', [CorporateUserController::class, 'getohcMenu']);
    Route::post('/ohcRightsSave', [CorporateUserController::class, 'ohcRightsSave'])->name('ohcRightsSave');
    Route::put('/ohcRightsUpdate', [CorporateUserController::class, 'ohcRightsUpdate']);
});

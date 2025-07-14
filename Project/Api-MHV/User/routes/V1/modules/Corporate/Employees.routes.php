<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\EmployeeTypeController;
use App\Http\Controllers\Department\CorporateHl1Controller;

Route::middleware(['auth:api'])->group(function () {
    // Here include the routes only accessible for corporate and not employees
});
Route::middleware(['authGuard.corporate.employee'])->group(function () {
    // Here include the routes which can be accessed by both corporate and employees
    //Employee Type
    Route::get('employees/show/{CorporateId}', [EmployeeTypeController::class, 'show'])->name('show');
    Route::post('employees/add_employeetype/{CorporateId}', [EmployeeTypeController::class, 'add_employeetype'])->name('add_employeetype');
    Route::post('employees/add', [EmployeeTypeController::class, 'store']);
    Route::post('employees/update', [EmployeeTypeController::class, 'update']);
    Route::delete('employees/delete/{id}', [EmployeeTypeController::class, 'destroy']);
    //Department
    Route::get('employees/index', [CorporateHl1Controller::class, 'index'])->name('index');
    Route::post('employees/create', [CorporateHl1Controller::class, 'store']);
    Route::post('employees/update/{id}', [CorporateHl1Controller::class, 'update']);
    Route::delete('employees/delete/{id}', [CorporateHl1Controller::class, 'destroy']);
});

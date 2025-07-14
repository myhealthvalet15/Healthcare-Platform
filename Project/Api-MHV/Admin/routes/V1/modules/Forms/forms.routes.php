<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1Controllers\Forms\CorporateFormController;
Route::middleware(['auth:api'])->group(function () {
    Route::get('/getAllCorporateForms', [CorporateFormController::class, 'displayAllCorporateForms']);
    Route::get('/getAllStates', [CorporateFormController::class, 'displayAllStates']);
    Route::delete('/deleteForms/{id}', [CorporateFormController::class, 'deleteFormsById']);
    Route::post('/addNewForm', [CorporateFormController::class, 'addNewForm']);
    Route::put('/updateForm/{id}', [CorporateFormController::class, 'updateFormById']);
    

});
 
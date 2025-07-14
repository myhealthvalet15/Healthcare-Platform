<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1Controllers\MedicalCondition\MedicalConditionController;
Route::middleware(['auth:api'])->group(function () {
    Route::get('/getAllMedicalCondition', [MedicalConditionController::class, 'fetchAllMedicalCondition']);
    Route::post('/addMedicalCondition', [MedicalConditionController::class, 'addNewMedicalCondition']);
    Route::delete('/deleteMedicalCondition/{id}', [MedicalConditionController::class, 'deleteMedicalConditionById']);
    Route::put('/editMedicalCondition/{id}', [MedicalConditionController::class, 'editMedicalConditionById']);
});
 
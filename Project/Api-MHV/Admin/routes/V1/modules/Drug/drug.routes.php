<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1Controllers\Drugs\DrugIngredientController;
use App\Http\Controllers\V1Controllers\Drugs\DrugTypeController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getAllingredients', [DrugIngredientController::class, 'displayDrugIngredient']);
    Route::post('/addingredients', [DrugIngredientController::class, 'addDrugIngredient']);
    Route::delete('/deleteIngredients/{id}', [DrugIngredientController::class, 'deleteDrugIngredient']);
    Route::put('/editIngredients/{id}', [DrugIngredientController::class, 'updateDrugIngredient']);

    Route::get('/getAllDrugtypes', [DrugTypeController::class, 'fetchAllDrugtypes']);
    Route::post('/addDrugtype', [DrugTypeController::class, 'addNewDrugtype']);
    Route::delete('/deleteDrugtype/{id}', [DrugTypeController::class, 'deleteDrugtypeById']);
    Route::put('/editDrugtype/{id}', [DrugTypeController::class, 'editDrugtypeById']);
});
 
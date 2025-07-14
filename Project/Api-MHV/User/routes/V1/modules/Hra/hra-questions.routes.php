<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HraController\Questions\QuestionController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getQuestion/{id}', [QuestionController::class, 'getQuestion']);
    Route::get('/getQuestionb64/{data}', [QuestionController::class, 'getQuestionb64']);
    Route::get('/getAllQuestions', [QuestionController::class, 'getAllQuestions']);
    Route::post('/addQuestion', [QuestionController::class, 'addQuestion']);
    Route::put('/editQuestion/{id}', [QuestionController::class, 'editQuestion']);
    Route::delete('/deleteQuestion/{id}', [QuestionController::class, 'deleteQuestion']);
});

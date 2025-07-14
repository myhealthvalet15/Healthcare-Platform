<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1Controllers\HraController\Templates\TemplateController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getAllTemplates', [TemplateController::class, 'getAllTemplates']);
    Route::get('/getTemplate/{id}', [TemplateController::class, 'getTemplate']);
    Route::get('/getAllFactorPriority', [TemplateController::class, 'getAllFactorPriority']);
    Route::get('/getFactorPriority/{template_id}', [TemplateController::class, 'getFactorPriority']);
    Route::get('/getQuestionFactorPriority/{template_id}/{factor_id}', [TemplateController::class, 'getQuestionFactorPriority']);
    Route::get('/getAllQuestionFactorPriority/{template_id}', [TemplateController::class, 'getAllQuestionFactorPriority']);
    Route::post('/addTemplate', [TemplateController::class, 'addTemplate']);
    Route::put('/editTemplate/{id}', [TemplateController::class, 'editTemplate']);
    Route::put('/setFactorPriority/{template_id}/', [TemplateController::class, 'setFactorPriority']);
    Route::put('/setQuestionFactorPriority/{template_id}/{factor_id}', [TemplateController::class, 'setQuestionFactorPriority']);
    Route::put('/setTriggerQuestionFactorPriority/{template_id}/{factor_id}/{question_id}', [TemplateController::class, 'setTriggerQuestionFactorPriority']);
    Route::get('/getTriggerQuestionFactorPriority/{template_id}/{factor_id}/{question_id}', [TemplateController::class, 'getTriggerQuestionFactorPriority']);
    Route::delete('/deleteTemplate/{template_id}', [TemplateController::class, 'deleteTemplate']);
    Route::post('/{template_id}/publishTemplate', [TemplateController::class, 'publishTemplate']);
});

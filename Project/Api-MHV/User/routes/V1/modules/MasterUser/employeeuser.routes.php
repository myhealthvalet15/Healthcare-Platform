<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserEmployee\EmployeeUserController;
use App\Http\Controllers\UserEmployee\EmployeeDashboard;

Route::middleware(['auth:employee_api', 'validate.employee.request'])->group(function () {
    Route::get('/getPrescriptionByEmployeeId/{employeeId}', [EmployeeUserController::class, 'getPrescriptionByEmployeeId']);
    Route::get('/getEmployeesDetailById/{employeeId}', [EmployeeUserController::class, 'getEmployeesDetailById']);
    Route::get('/listotcdetailsForEmployeeById/{employeeId}', [EmployeeUserController::class, 'listotcdetailsForEmployeeById']);
    Route::get('/getEmployeeTestForGraph/{master_user_id}/{test_id}', [EmployeeUserController::class, 'getEmployeeTestForGraph']);
    Route::get('/getAllAssignedTemplates', [EmployeeDashboard::class, 'getAllAssignedTemplates']);
    Route::get('/check-template-access/{templateId}', [EmployeeDashboard::class, 'checkTemplateAccess']);
    Route::get('/get-template-questions/{templateId}', [EmployeeDashboard::class, 'getTemplateQuestions']);
    Route::post('/save-hra-template-questionnaire-answers/{templateId}', [EmployeeDashboard::class, 'saveHraTemplateQuestionnaireAnswers']);
    Route::post('/updateEmployeesDetailById/{employeeId}', [EmployeeUserController::class, 'updateEmployeesDetailById']);
    Route::get('getEventsforEmployees/{user_id}', [EmployeeUserController::class, 'getEventsforEmployeesByUserId']);
    Route::get('getEventDetails/{user_id}', [EmployeeUserController::class, 'getEventDetails']);
    Route::post('/submitEventResponse', [EmployeeUserController::class, 'submitEventResponseByEmployeeId']);
    Route::get('getHospitalizationList/{user_id}', [EmployeeUserController::class, 'getHospitalizationListByUserId']);
    Route::get('getMedicalCondition', [EmployeeUserController::class, 'getMedicalCondition']);

});

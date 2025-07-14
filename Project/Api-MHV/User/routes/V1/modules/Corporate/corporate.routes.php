<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Corporate\CorporateStubs;
use App\Http\Controllers\Corporate\corporateEmployees;
use App\Http\Controllers\Corporate\events\EventsController;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth:api'])->group(function () {
    // Here include the routes only accessible for corporate and not employees Route::post('/storeEvents/{corporate_id}/{location_id}', [EventsController::class, 'addEvents']);

    Route::post('/saveCertificateCondition', [corporateEmployees::class, 'saveCertificateCondition']);
    // TODO: To Move This To Add registry Route (Registry Route Page)
    Route::post('/add-test/{employee_id}/op/{op_registry_id}', [corporateEmployees::class, 'addTest']);
    Route::post('/add-test/{employee_id}/prescription/{prescription_id}', [corporateEmployees::class, 'addTest']);
    Route::get('/getAllSymptoms', [corporateEmployees::class, 'getAllSymptoms']);
    Route::get('/getAllDiagnosis', [corporateEmployees::class, 'getAllDiagnosis']);
    Route::get('/getAllMedicalSystem', [corporateEmployees::class, 'getAllMedicalSystem']);
    Route::get('/getAllBodyParts', [corporateEmployees::class, 'getAllBodyParts']);
    Route::get('/getAllNatureOfInjury', [corporateEmployees::class, 'getAllNatureOfInjury']);
    Route::get('/getAllInjuryMechanism', [corporateEmployees::class, 'getAllInjuryMechanism']);
    Route::get('/getMRNumber', [corporateEmployees::class, 'getMRNumber']);
    Route::post('/addHealthRegistry', [corporateEmployees::class, 'addHealthRegistry']);
    Route::get('/getTestForEmployee/{employeeId}/op/{op_registry_id}', [corporateEmployees::class, 'getTestForEmployee']);
    Route::get('/getTestForEmployee/{employeeId}/prescription/{prescription_id}', [corporateEmployees::class, 'getTestForEmployee']);
    Route::get('/getAllSubGroup', [corporateEmployees::class, 'getAllSubGroup']);

    Route::get('/getAllCertificates/{corporateId}', [CorporateStubs::class, 'getAllCertificates']);
    Route::get('/getAllEmployeeData/{corporateId}/{locationId}', [corporateEmployees::class, 'getAllEmployees']);
    Route::post('/getAllEmployeeData/{corporateId}/{locationId}', [corporateEmployees::class, 'getAllEmployees']);
    Route::get('/getAllEmployeeData/{corporateId}/{locationId}', [corporateEmployees::class, 'getAllEmployees']);
    Route::get('/getEmployeeData/{corporateId}/{locationId}/{keyword}', [corporateEmployees::class, 'searchEmployeeDataByKeyword']);
    Route::get('/getDepartmentsHL1/{corporateId}/{locationId}', [corporateEmployees::class, 'getDepartmentsHL1']);
    Route::get('/getDesignations/{corporateId}/{locationId}', [corporateEmployees::class, 'getDesignations']);
    Route::get('/getDoctors/{corporateId}/{locationId}', [corporateEmployees::class, 'getDoctors']);
    Route::get('/getLabs/{corporateId}/{locationId}', [corporateEmployees::class, 'getLabs']);
    Route::get('/getFavourite/{corporateId}/{locationId}', [corporateEmployees::class, 'getFavourite']);
    Route::get('/getEmployeetype/{corporateId}', [corporateEmployees::class, 'getEmployeeType']);
    Route::get('/getContractors/{locationId}', [corporateEmployees::class, 'getContractors']);
    Route::post('/assignHealthPlan/{corporateId}/{locationId}', [corporateEmployees::class, 'assignHealthPlan']);

    Route::post('/saveTestResults', [corporateEmployees::class, 'saveTestResults']);
    Route::get('/getincidentTypeColorCodes/{corporate_id}/{location_id}', [corporateEmployees::class, 'getincidentTypeColorCodes']);
    Route::get('/getAllLocations', [corporateEmployees::class, 'getAllLocations']);


    //Events
    Route::get('/getAllEventsByCorporate/{corporate_id}', [EventsController::class, 'getAllEventsByCorporate']);
    //Route::get('/listEventsBYPostman', [EventsController::class, 'listEventsBYPostman']);
    Route::delete('/destroy/{deleteId}', [EventsController::class, 'destroyEvents']);
    Route::get('/destroy/{deleteId}', [EventsController::class, 'destroyEvents']);
    Route::get('/getEventsById/{id}/{corporate_id}', [EventsController::class, 'editEventsById']);
    Route::put('/updateEvents/{id}', [EventsController::class, 'updateEventsById']);
});
Route::middleware(['authGuard.corporate.employee'])->group(function () {
    // Here include the routes which can be accessed by both corporate and employees
    Route::get('/getAllAssignedHealthPlan/{corporateId}/{locationId}', [corporateEmployees::class, 'getAllAssignedHealthPlan']);
    Route::get('/getAllColorCodes', [corporateEmployees::class, 'getAllColorCodes']);
    Route::get('/getTestDetails/{corporateId}/{locationId}/{testCode}', [corporateEmployees::class, 'getSinglePrescribedTest']);

    // TODO: To Move This To List registry Route (Registry Route Page)
    Route::get('/getAllHealthRegistry/{corporateId}/{locationId}', [corporateEmployees::class, 'getAllHealthRegistry']);
    Route::get('/getAllHealthRegistry/{corporateId}/{locationId}/{employeeId}', [corporateEmployees::class, 'getAllHealthRegistry']);
    // TODO: To Move This To Test Route (Tests Route Page)
    Route::get('/getAllTestsFromPrescribedTest/{corporate_id}/{location_id}', [corporateEmployees::class, 'getAllTestsFromPrescribedTest']);
    Route::get('/getAllTestsFromPrescribedTest/{corporate_id}/{location_id}/{EmployeeUserId}', [corporateEmployees::class, 'getAllTestsFromPrescribedTest']);
    Route::get('/checkEmployeeId/followUp/{isFollowUp}/{employee_id}', [corporateEmployees::class, 'checkEmployeeId']);
    Route::get('/checkEmployeeId/followUp/{isFollowUp}/{employee_id}/op/{op_registry_id}', [corporateEmployees::class, 'checkEmployeeId']);
    Route::get('/checkEmployeeId/followUp/{isFollowUp}/{employee_id}/prescription/{prescription_id}', [corporateEmployees::class, 'checkEmployeeId']);

});

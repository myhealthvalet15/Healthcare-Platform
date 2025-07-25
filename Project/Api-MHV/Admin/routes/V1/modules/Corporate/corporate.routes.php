<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CertificationController;
use App\Http\Controllers\V1Controllers\CorporateController\TestGroups;
use App\Http\Controllers\V1Controllers\CorporateController\linkCorporateToHra;
use App\Http\Controllers\V1Controllers\IncidentType\IncidentTypeController;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getAllCertificates', [CertificationController::class, 'getAllCertificates']);
    // Test Group
    Route::post('/addGroup', [TestGroups::class, 'addGroup']);
    Route::get('/getAllGroup', [TestGroups::class, 'getAllGroup']);
    Route::get('/getSubGroupOfGroup/{groupId}', [TestGroups::class, 'getSubGroupOfGroup']);
    Route::get('/getGroup/{groupId}', [TestGroups::class, 'getGroup']);
    Route::put('/updateGroup', [TestGroups::class, 'updateGroup']);
    Route::delete('/deleteGroup', [TestGroups::class, 'deleteGroup']);

    // Test Sub Group
    Route::post('/addSubGroup', [TestGroups::class, 'addSubGroup']);
    Route::get('/getAllSubGroup', [TestGroups::class, 'getAllSubGroup']);
    Route::get('/getSubGroup/{subGroupId}', [TestGroups::class, 'getSubGroup']);
    Route::put('/updateSubGroup', [TestGroups::class, 'updateSubGroup']);
    Route::delete('/deleteSubGroup', [TestGroups::class, 'deleteSubGroup']);

    // Test Sub Sub Group
    Route::post('/addSubSubGroup', [TestGroups::class, 'addSubSubGroup']);
    Route::get('/getAllSubSubGroup', [TestGroups::class, 'getAllSubSubGroup']);
    Route::get('/getSubSubGroup/{subSubGroupId}', [TestGroups::class, 'getSubSubGroup']);
    Route::put('/updateSubSubGroup', [TestGroups::class, 'updateSubSubGroup']);
    Route::delete('/deleteSubSubGroup', [TestGroups::class, 'deleteSubSubGroup']);

    // link2hra
    Route::post('/link2hra', [linkCorporateToHra::class, 'linkCorporate2Hra']);
    Route::post('/updateCorporateHraLink', [linkCorporateToHra::class, 'updateCorporateHraLink']);
    Route::get('/getCorporateOfHraTemplate', [linkCorporateToHra::class, 'getCorporateOfHraTemplate']);

    Route::get('/getAllIncidentTypes', [IncidentTypeController::class, 'getAllIncidentTypes']);
    Route::post('/addIncidentType', [IncidentTypeController::class, 'addIncidentType']);
    Route::post('/editIncidentType/{incident_type_id}', [IncidentTypeController::class, 'editIncidentType']);
    Route::delete('/deleteIncidentType/{incident_type_id}', [IncidentTypeController::class, 'deleteIncidentType']);
    Route::get('/getAllAssignedIncidentTypes/{corporate_id}', [IncidentTypeController::class, 'getAllAssignedIncidentTypes']);
    Route::post('/assignIncidentTypes/{corporate_id}', [IncidentTypeController::class, 'assignIncidentTypes']);
});

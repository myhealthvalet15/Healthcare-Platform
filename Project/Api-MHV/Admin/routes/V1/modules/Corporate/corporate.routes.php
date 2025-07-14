<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CertificationController;
use App\Http\Controllers\V1Controllers\CorporateController\TestGroups;
use App\Http\Controllers\V1Controllers\CorporateController\linkCorporateToHra;

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
});

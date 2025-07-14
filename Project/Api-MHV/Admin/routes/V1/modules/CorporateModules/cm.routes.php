<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1Controllers\CorporateController\CorporateComponents\ModulesController;
use App\Http\Controllers\V1Controllers\CorporateController\AddUsers\AddUserExcel;

Route::middleware(['auth:api'])->group(function () {
    Route::get('/getAllModules', [ModulesController::class, 'getAllModules']); 
    Route::get('/getAllComponents', [ModulesController::class, 'getAllComponents']);
    Route::get('/getAllComponent/corpId/{corpid}', [ModulesController::class, 'getAllComponentsByCorpId']);
    Route::get('/getsubModules', [ModulesController::class, 'getsubModules']); 
    Route::post('/addComponents', [ModulesController::class, 'addComponents']);
    Route::post('/updateComponents', [ModulesController::class, 'updateComponents']);
    Route::post('/module/add-module', [ModulesController::class, 'addModule']);
    Route::post('/module/get', [ModulesController::class, 'getModule']);
    Route::post('/submodule/get', [ModulesController::class, 'getSubModule']);
    Route::post('/submodule/add-sub-module', [ModulesController::class, 'addSubModule']);
    Route::put('/submodule/edit', [ModulesController::class, 'editSubModule']);
    Route::put('/module/edit', [ModulesController::class, 'editModule']);
    Route::delete('/submodule/delete', [ModulesController::class, 'deleteSubModule']);
    Route::delete('/module/delete', [ModulesController::class, 'deleteModule']);
    // TODO: .. to be moved
    Route::post('/add-corporate-users/addBulkData', [AddUserExcel::class, 'addusersExcel']);
    Route::post('/add-corporate-users/addUsers', [AddUserExcel::class, 'addUsers']);
    Route::get('/getMasterUserDetails', [AddUserExcel::class, 'getMasterUserCount']);
    Route::get('/getAddUserStatus', [AddUserExcel::class, 'getAddCorporateExcelFiles']);
    Route::get('/getAddUserUploadCount', [AddUserExcel::class, 'getAddCorporateUploadCount']);
    Route::get('/getAddUserStatusFileContent/{id}', [AddUserExcel::class, 'getAddCorporateExcelFileContent']);
    //Added By Bhava
    Route::get('/getModule4Submodules', [ModulesController::class, 'getModule4Submodules']); 
    Route::post('/assignFormForLocation', [ModulesController::class, 'assignFormForLocation']); 
    Route::get('/getAssignedForms/{corporate_id}/{location_id}', [ModulesController::class, 'getAssignedForms']); 
    Route::get('/getEmployeeData/{keyword}', [AddUserExcel::class, 'searchEmployeeDataByKeyword']);
   
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Corporate\CorporateComponents\ModulesController;
use App\Http\Controllers\Corporate\AddUsers\AddUserExcel;
use App\Http\Controllers\Corporate\contractor\CorporateContractor;
use App\Http\Controllers\DrugTemplate\DrugTemplateController;
use App\Http\Controllers\Employee\EmployeeList;
use App\Http\Controllers\Corporate\CorporateOhcController;
use App\Http\Controllers\PharmacyStock\PharmacyStockController;
use App\Http\Controllers\Corporate\_CorporateHealthplan;
use App\Http\Controllers\Others\BioMedicalWasteController;
use App\Http\Controllers\Others\InventoryController;
use App\Http\Controllers\Others\InvoiceController;
use App\Http\Controllers\Prescription\PrescriptionController;
use App\Http\Controllers\requests\RequestController;
use App\Http\Controllers\otc\otcController;

Route::middleware(['auth:api'])->group(function () {
    // Here include the routes only accessible for corporate and not employees
});
Route::middleware(['authGuard.corporate.employee'])->group(function () {
    // Here include the routes which can be accessed by both corporate and employees
    Route::get('/getAllModules', [ModulesController::class, 'getAllModules']);
    Route::get('/getAllComponents', [ModulesController::class, 'getAllComponents']);
    Route::get('/getAllComponent/corpId/{corpid}', [ModulesController::class, 'getAllComponentsByCorpId']);
    Route::get('/getAllComponent/accessRights/corpId/{corpid}', [ModulesController::class, 'getAllComponentsByCorpIdByAccessRights']);
    Route::get('/getsubModules', [ModulesController::class, 'getsubModules']);
    Route::post('/addComponents', [ModulesController::class, 'addComponents']);
    Route::post('/updateComponents', [ModulesController::class, 'updateComponents']);
    Route::post('/module/add-module', [ModulesController::class, 'addModule']);
    Route::post('/show/module', [ModulesController::class, 'showmodule']);
    Route::post('/show/submodule', [ModulesController::class, 'show_submodule']);
    Route::post('/submodule/add-sub-module', [ModulesController::class, 'addSubModule']);
    Route::put('/submodule/edit', [ModulesController::class, 'editSubModule']);
    Route::put('/module/edit', [ModulesController::class, 'editModule']);
    Route::delete('/submodule/delete', [ModulesController::class, 'deleteSubModule']);
    Route::delete('/module/delete', [ModulesController::class, 'deleteModule']);
    // TODO: .. to be moved => Upload users routes ... By Praveen
    Route::post('/add-corporate-users/addBulkData', [AddUserExcel::class, 'addUsersExcel']);
    Route::post('/add-corporate-users/addUsers', [AddUserExcel::class, 'addUsers']);
    Route::post('/add-corporate-users/add', [AddUserExcel::class, 'addUser']);
    Route::get('/getMasterUserDetails', [AddUserExcel::class, 'getMasterUserCount']);
    Route::get('/getAddUserStatus', [AddUserExcel::class, 'getAddCorporateExcelFiles']);
    Route::get('/getAddUserUploadCount', [AddUserExcel::class, 'getAddCorporateUploadCount']);
    Route::get('/getAddUserStatusFileContent/{id}', [AddUserExcel::class, 'getAddCorporateExcelFileContent']);
    // TODO: .. to be moved => Corporate HealthPlan routes ... By Praveen
    Route::get('/getAllHealthPlans/{corporateId}', [_CorporateHealthplan::class, 'getAllHealthplans']);
    Route::get('/getHealthPlan/{corporateId}/{healthplanId}', [_CorporateHealthplan::class, 'getHealthplan']);
    Route::post('/addNewHealthPlan', [_CorporateHealthplan::class, 'addHealthplan']);
    Route::put('/updateHealthplan', [_CorporateHealthplan::class, 'updateHealthplan']);
    Route::delete('/deleteHealthplan', [_CorporateHealthplan::class, 'deleteHealthplan']);
    // ....
    // TODO: .. to be moved => Contractors routes ... By Bava
    Route::get('/viewContractors/{locationId}', [CorporateContractor::class, 'viewContractors']);
    Route::post('/createContractors', [CorporateContractor::class, 'createContractors']);
    Route::put('/modifyContractors/{id}', [CorporateContractor::class, 'modifyContractors']);
    Route::put('/removeContractors/{id}', [CorporateContractor::class, 'removeContractors']);
    Route::get('fetchEmployeeType/{corporate_id}', [EmployeeList::class, 'fetchEmployeeType']);
    Route::get('fetchDepartment/{corporate_id}', [EmployeeList::class, 'fetchDepartment']);


    Route::get('/getAllDrugTemplates/{locationId}', [DrugTemplateController::class, 'getAllDrugTemplates']);
    Route::get('/getDrugTypesAndIngredients', [DrugTemplateController::class, 'getDrugTypesAndIngredients'])->name('getDrugTypesAndIngredients');
    Route::post('/addDrugTemplate', [DrugTemplateController::class, 'addDrugTemplate'])->name('addDrugTemplate');
    Route::put('/updateDrugTemplate/{id}', [DrugTemplateController::class, 'updateDrugTemplate'])->name('updateDrugTemplate');
    Route::get('/getDrugTemplatesById/{id}', [DrugTemplateController::class, 'getDrugTemplatesById'])->name('getDrugTemplatesById');


    Route::get('/getAllOHCDetails/{locationId}', [CorporateOhcController::class, 'getAllOHCDetails']);
    Route::get('/getPharmacyDetails/{locationId}', [CorporateOhcController::class, 'getPharmacyDetails']);
    Route::post('/addPharmacyData', [CorporateOhcController::class, 'addPharmacyDataDetails']);
    Route::post('/addCorporateOHCData', [CorporateOhcController::class, 'addCorporateOHCDetails']);
    Route::put('/modifyCorporateOHC/{id}', [CorporateOhcController::class, 'updateCorporateOHCById']);
    Route::put('/modifyCorporatePharmacy/{id}', [CorporateOhcController::class, 'updatCorporatePharmacyByid']);

    Route::get('/getAllPharmacyStock/{locationId}', [PharmacyStockController::class, 'getAllPharmacyStock']);
    Route::get('/getDrugTemplateDetails', [PharmacyStockController::class, 'getAllDrugTemplateDetails']);
    Route::post('/addPharmacyStock', [PharmacyStockController::class, 'addPharmacyStock'])->name('addPharmacyStock');
    Route::put('/updatePharmacyStock/{id}', [PharmacyStockController::class, 'updatePharmacyStock'])->name('updatePharmacyStock');
    Route::get('/getDrugTypesAndIngredients', [PharmacyStockController::class, 'getDrugTypesAndIngredients'])->name('getDrugTypesAndIngredients');
    Route::get('/getSubPharmacyStockById/{id}', [PharmacyStockController::class, 'getSubPharmacyStockById'])->name('getSubPharmacyStockById');
    Route::get('/getStockByAvailability/{id}/{storeId}', [PharmacyStockController::class, 'getStockByAvailability'])->name('getStockByAvailability');

    Route::get('/getMainStockDetails/{id}', [PharmacyStockController::class, 'getMainStockDetails'])->name('getMainStockDetails');
    Route::post('/moveStocktoMainStore', [PharmacyStockController::class, 'moveStocktoMainStore'])->name('moveStocktoMainStore');


    Route::get('/getAllIndustryWaste/{locationId}', [DrugTemplateController::class, 'getAllIndustryWaste']);
    Route::get('/getAllIndustryWasteDetails/{locationId}', [BioMedicalWasteController::class, 'getAllIndustryWasteDetails']);
    Route::post('/addBioWasteData', [BioMedicalWasteController::class, 'addBioWasteData']);
    Route::put('/updateBioMedicalWasteById/{id}', [BioMedicalWasteController::class, 'updateBioMedicalWasteById']);

    Route::get('/getAllInventory/{locationId}', [InventoryController::class, 'getAllInventory']);
    Route::post('/addInventorytoDB', [InventoryController::class, 'addInventorytoDB'])->name('addInventorytoDB');
    Route::get('/getInventoryById/{id}', [InventoryController::class, 'getInventoryById'])->name('getInventoryById');
    Route::put('/updateInventory/{id}', [InventoryController::class, 'updateInventory']);
    Route::get('/getCalibrationHistory/{id}', [InventoryController::class, 'getCalibrationHistory']);

    //Vendor
    Route::post('/addVendor', [InvoiceController::class, 'addVendor'])->name('addVendor');
    Route::get('/getVendorDetails/{locationId}', [InvoiceController::class, 'getVendorDetails']);

    //Invoice
    Route::get('/getAllInvoiceDetails/{locationId}', [InvoiceController::class, 'getAllInvoiceDetails']);
    Route::post('/addInvoice', [InvoiceController::class, 'addInvoice'])->name('addVendor');
    Route::get('/getInvoiceById/{id}', [InvoiceController::class, 'getInvoiceById'])->name('getInvoiceById');
    Route::put('/updateInvoice/{id}', [InvoiceController::class, 'updateInvoice']);
    Route::get('/getPoBalance/{locationId}', [InvoiceController::class, 'getPoBalance']);

    //Prescription
    Route::get('/getAllPrescriptionTemplate/{locationId}', [PrescriptionController::class, 'getAllPrescriptionTemplate']);
    Route::get('/getPrintPrescriptionById/{id}', [PrescriptionController::class, 'getPrintPrescriptionById']);
    Route::post('/addPrescriptionTemplate', [PrescriptionController::class, 'addPrescriptionTemplate'])->name('addPrescriptionTemplate');
    Route::get('/getPrescriptionTemplateById/{id}', [PrescriptionController::class, 'getPrescriptionTemplateById']);
    Route::put('/updatePrescriptionTemplate/{id}', [PrescriptionController::class, 'updatePrescriptionTemplate']);
    Route::get('/getOnlyPrescriptionTemplate/{locationId}', [PrescriptionController::class, 'getOnlyPrescriptionTemplate']);
    Route::post('/addPrescription', [PrescriptionController::class, 'addPrescription'])->name('addEmployeePrescription');
    Route::get('/getEmployeePrescription/{userId}', [PrescriptionController::class, 'getEmployeePrescription']);
    Route::get('/getStockByDrugId/{drugId}', [PrescriptionController::class, 'getStockByDrugId']);
    Route::get('/getStockByDrugIdAndPharmacyId/{drugId}/{pharmacyId}', [PrescriptionController::class, 'getStockByDrugIdAndPharmacyId']);

    //Pending Requests
    Route::get('/getEmployeePrescriptionforPendingRequest/{userId}', [RequestController::class, 'getEmployeePrescriptionforPendingRequest']);
    Route::post('/closePrescription', [RequestController::class, 'closePrescription'])->name('closePrescription');
    Route::post('/issuePartlyPrescription', [RequestController::class, 'issuePartlyPrescription'])->name('issuePartlyPrescription');
    Route::get('/getAllClosedPrescription/{userId}', [RequestController::class, 'getAllClosedPrescription']);

    Route::post('/addPrescriptionForOTC', [otcController::class, 'addPrescriptionForOTC'])->name('addPrescriptionForOTC');
    Route::get('/getAllotcDetails/{locationId}', [otcController::class, 'getAllotcDetails']);

});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeTypeController;
use App\Http\Controllers\Api\InjuryController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\TestgroupController;
use App\Http\Controllers\Api\CorporateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CertificationController;
use App\Http\Controllers\Api\CorporateadminController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// TODO: To be Moved
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyCode']);
// ...
Route::get('location_index', [AddressController::class, 'location_index']);
Route::get('show', [AddressController::class, 'show']);
Route::get('generateUniqueUserId', [CorporateController::class, 'generateUniqueUserId']);
Route::get('uniquelocation_ids', [CorporateController::class, 'uniquelocation_ids']);
Route::post('/findAdminByToken', [AuthController::class, 'findAdminByToken']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    // TODO: To be Moved
    Route::get('/getWhoAmI', [AuthController::class, 'getWhoAmI']);
    // ...
    Route::group(['prefix' => 'injury', 'as' => 'injury.'], function () {
        Route::post('/index', [InjuryController::class, 'index']);
        Route::post('/add', [InjuryController::class, 'create']);
        Route::post('/update/{id}', [InjuryController::class, 'update']);
        Route::delete('/delete/{id}', [InjuryController::class, 'destroy']);
    });
    Route::group(['prefix' => 'doctor', 'as' => 'doctor.'], function () {
        Route::post('/index', [DoctorController::class, 'index']);
        Route::post('/add', [DoctorController::class, 'store']);
        Route::post('/update/{id}', [DoctorController::class, 'update']);
    });
    Route::group(['prefix' => 'address', 'as' => 'address.'], function () {
        Route::post('country', [AddressController::class, 'countryindex']);
        Route::post('addcountry', [AddressController::class, 'countrycreate']);
        Route::post('state', [AddressController::class, 'stateindex']);
        Route::post('addstate', [AddressController::class, 'stateadd']);
        Route::post('city', [AddressController::class, 'cityindex']);
        Route::post('findcountry', [AddressController::class, 'countryfind']);
        Route::post('addcity', [AddressController::class, 'cityadd']);
        Route::post('area', [AddressController::class, 'areaindex']);
        Route::post('findstacountry', [AddressController::class, 'countrystate_find']);
        Route::post('areaadd', [AddressController::class, 'areaadd']);
        Route::post('pincode', [AddressController::class, 'pincodeindex']);
        Route::post('findstaconcity', [AddressController::class, 'countrystatecity_find']);
        Route::post('pincodeaadd', [AddressController::class, 'pincodeadd']);
        Route::post('findareastaconcity', [AddressController::class, 'countrystatecityarea_find']);
        Route::post('area_find', [AddressController::class, 'area_find']);
        Route::post('pincode_find', [AddressController::class, 'pincodefind']);
    });
    // Route::group(['prefix' => 'test_groups', 'as' => 'test_groups.'], function () {
    //     Route::post('index', [TestgroupController::class, 'index']);
    //     Route::post('add', [TestgroupController::class, 'store']);
    // });
    Route::group(['prefix' => 'corporate', 'as' => 'corporate.'], function () {
        Route::post('/add-corporate', [CorporateController::class, 'addcorporate']);
        Route::post('/corporate_user', [CorporateController::class, 'addCorporateUser']);
        Route::post('/corporate_index', [CorporateController::class, 'corporate_index']);

        Route::post('/corporate_address', [CorporateController::class, 'corporate_address']);
        Route::get('/edit_address/{id}/{corporate_id}', [CorporateController::class, 'edit_address']);
        Route::get('editcorporate/{corporate_id}', [CorporateController::class, 'editcorporate']);
        Route::post('update_corporate/{id}', [CorporateController::class, 'updatecorporate']);
        Route::post('update_corporate_address/{id}', [CorporateController::class, 'update_corporate_address']);
        Route::post('corporate_location', [CorporateController::class, 'corporate_location']);
        Route::post('address_location/{id}', [CorporateController::class, 'address_location']);
    });
    Route::post('/corporate_finacials', [CorporateController::class, 'store']);
    Route::group(['prefix' => 'certificate', 'as' => 'certificate.'], function () {
        Route::post('index', [CertificationController::class, 'index']);
        Route::post('show/{id}', [CertificationController::class, 'show']);
        Route::post('create', [CertificationController::class, 'create']);
        Route::post('update/{id}', [CertificationController::class, 'update']);
        Route::post('delete/{id}', [CertificationController::class, 'destroy']);
    });
    Route::group(['prefix' => 'Corporate_admin_user'], function () {
        Route::post('/create', [CorporateadminController::class, 'store']);
        Route::get('/show/{id}/{corporate_id}', [CorporateadminController::class, 'show']);
        Route::post('/update/{id}', [CorporateadminController::class, 'update']);
        Route::post('/location/{id}', [CorporateadminController::class, 'adminuser_locations']);
    });
    Route::group(['prefix' => 'Employeetype', 'as' => 'Employeetype.'], function () {
        Route::post('/index', [EmployeeTypeController::class, 'index']);
    
        Route::post('/add_employeetype', [EmployeeTypeController::class, 'add']);
        Route::post('/add', [EmployeeTypeController::class, 'store']);
        Route::post('/show/{corporate_id}', [EmployeeTypeController::class, 'show']);
        Route::post('/update', [EmployeeTypeController::class, 'update']);
        Route::delete('/delete/{id}', [EmployeeTypeController::class, 'destroy']);
    });
    //Drug Template
    Route::group(['prefix' => 'Drug_template'], function () {
        Route::post('/create', [CorporateadminController::class, 'store']);
        Route::get('/show/{id}/{corporate_id}', [CorporateadminController::class, 'show']);
        Route::post('/update/{id}', [CorporateadminController::class, 'update']);
        Route::post('/location/{id}', [CorporateadminController::class, 'adminuser_locations']);
    });

    Route::get('/corporates', [CorporateController::class, 'mainCorporate']);
    Route::get('/corporates/{corporate_id}/locations', [CorporateController::class, 'getCorporateLocations']);
});

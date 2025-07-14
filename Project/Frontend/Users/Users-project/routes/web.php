<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\dashboard\Crm;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
use App\Http\Controllers\layouts\NavbarFull;
use App\Http\Controllers\layouts\NavbarFullSidebar;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\front_pages\Landing;
use App\Http\Controllers\front_pages\Pricing;
use App\Http\Controllers\front_pages\Payment;
use App\Http\Controllers\front_pages\Checkout;
use App\Http\Controllers\front_pages\HelpCenter;
use App\Http\Controllers\front_pages\HelpCenterArticle;
use App\Http\Controllers\apps\Email;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\apps\Calendar;
use App\Http\Controllers\apps\Kanban;
use App\Http\Controllers\apps\EcommerceDashboard;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\apps\EcommerceProductAdd;
use App\Http\Controllers\apps\EcommerceProductCategory;
use App\Http\Controllers\apps\EcommerceOrderList;
use App\Http\Controllers\apps\EcommerceOrderDetails;
use App\Http\Controllers\apps\EcommerceCustomerAll;
use App\Http\Controllers\apps\EcommerceCustomerDetailsOverview;
use App\Http\Controllers\apps\EcommerceCustomerDetailsSecurity;
use App\Http\Controllers\apps\EcommerceCustomerDetailsBilling;
use App\Http\Controllers\apps\EcommerceCustomerDetailsNotifications;
use App\Http\Controllers\apps\EcommerceManageReviews;
use App\Http\Controllers\apps\EcommerceReferrals;
use App\Http\Controllers\apps\EcommerceSettingsDetails;
use App\Http\Controllers\apps\EcommerceSettingsPayments;
use App\Http\Controllers\apps\EcommerceSettingsCheckout;
use App\Http\Controllers\apps\EcommerceSettingsShipping;
use App\Http\Controllers\apps\EcommerceSettingsLocations;
use App\Http\Controllers\apps\EcommerceSettingsNotifications;
use App\Http\Controllers\apps\AcademyDashboard;
use App\Http\Controllers\apps\AcademyCourse;
use App\Http\Controllers\apps\AcademyCourseDetails;
use App\Http\Controllers\apps\LogisticsDashboard;
use App\Http\Controllers\apps\LogisticsFleet;
use App\Http\Controllers\apps\InvoiceList;
use App\Http\Controllers\apps\InvoicePreview;
use App\Http\Controllers\apps\InvoicePrint;
use App\Http\Controllers\apps\InvoiceEdit;
use App\Http\Controllers\apps\InvoiceAdd;
use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\UserViewBilling;
use App\Http\Controllers\apps\UserViewNotifications;
use App\Http\Controllers\apps\UserViewConnections;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\apps\AccessPermission;
use App\Http\Controllers\pages\UserProfile;
use App\Http\Controllers\pages\UserTeams;
use App\Http\Controllers\pages\UserProjects;
use App\Http\Controllers\pages\UserConnections;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsSecurity;
use App\Http\Controllers\pages\AccountSettingsBilling;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\Faq;
use App\Http\Controllers\pages\Pricing as PagesPricing;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\pages\MiscComingSoon;
use App\Http\Controllers\pages\MiscNotAuthorized;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\LoginCover;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\RegisterCover;
use App\Http\Controllers\authentications\RegisterMultiSteps;
use App\Http\Controllers\authentications\VerifyEmailBasic;
use App\Http\Controllers\authentications\VerifyEmailCover;
use App\Http\Controllers\authentications\ResetPasswordBasic;
use App\Http\Controllers\authentications\ResetPasswordCover;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\ForgotPasswordCover;
use App\Http\Controllers\authentications\TwoStepsBasic;
use App\Http\Controllers\authentications\TwoStepsCover;
use App\Http\Controllers\wizard_example\Checkout as WizardCheckout;
use App\Http\Controllers\wizard_example\PropertyListing;
use App\Http\Controllers\wizard_example\CreateDeal;
use App\Http\Controllers\modal\ModalExample;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\cards\CardAdvance;
use App\Http\Controllers\cards\CardStatistics;
use App\Http\Controllers\cards\CardAnalytics;
use App\Http\Controllers\cards\CardGamifications;
use App\Http\Controllers\cards\CardActions;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\Avatar;
use App\Http\Controllers\extended_ui\BlockUI;
use App\Http\Controllers\extended_ui\DragAndDrop;
use App\Http\Controllers\extended_ui\MediaPlayer;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\StarRatings;
use App\Http\Controllers\extended_ui\SweetAlert;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\extended_ui\TimelineBasic;
use App\Http\Controllers\extended_ui\TimelineFullscreen;
use App\Http\Controllers\extended_ui\Tour;
use App\Http\Controllers\extended_ui\Treeview;
use App\Http\Controllers\extended_ui\Misc;
use App\Http\Controllers\icons\Tabler;
use App\Http\Controllers\icons\FontAwesome;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_elements\CustomOptions;
use App\Http\Controllers\form_elements\Editors;
use App\Http\Controllers\form_elements\FileUpload;
use App\Http\Controllers\form_elements\Picker;
use App\Http\Controllers\form_elements\Selects;
use App\Http\Controllers\form_elements\Sliders;
use App\Http\Controllers\form_elements\Switches;
use App\Http\Controllers\form_elements\Extras;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\StickyActions;
use App\Http\Controllers\form_wizard\Numbered as FormWizardNumbered;
use App\Http\Controllers\form_wizard\Icons as FormWizardIcons;
use App\Http\Controllers\form_validation\Validation;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\tables\DatatableBasic;
use App\Http\Controllers\tables\DatatableAdvanced;
use App\Http\Controllers\tables\DatatableExtensions;
use App\Http\Controllers\charts\ApexCharts;
use App\Http\Controllers\charts\ChartJs;
use App\Http\Controllers\maps\Leaflet;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CorporateUsers\CorporateUserController;
use App\Http\Controllers\Employee\Employees;
use App\Http\Controllers\Employee\EmployeeType;
use App\Http\Controllers\DrugTemplate\DrugTemplateController;
use App\Http\Controllers\Department\CorporateHl1;
use App\Http\Middleware\Authcheck;
use App\Http\Controllers\corporate\addCorporateUsers;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\corporate\corporateContractors;
use App\Http\Controllers\corporate\corporateHealthPlans;
use App\Http\Controllers\corporate\corporateAssignHealthPlans;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\corporate\CorporateOhcController;
use App\Http\Controllers\PharmacyStock\PharmacyStockController;
use App\Http\Controllers\Others\BioMedicalWasteController;
use App\Http\Controllers\Others\InventoryController;
use App\Http\Controllers\Others\InvoiceController;
use App\Http\Controllers\components\ohc\health_registry;
use App\Http\Controllers\components\ohc\Tests;
use App\Http\Controllers\Prescription\PrescriptionController;
use App\Http\Controllers\requests\RequestController;
use App\Http\Controllers\UserEmployee\EmployeeUserController;
use App\Http\Controllers\UserEmployee\EmployeeDashboard;
use App\Http\Controllers\otc\otcController;
use App\Http\Controllers\components\mhc\HealthRiskAssessment;
use App\Http\Controllers\components\events\EventsController;
use Illuminate\Http\Request;

// icons
Route::get('/icons/tabler', [Tabler::class, 'index'])->name('icons-tabler');
Route::get('/icons/font-awesome', [FontAwesome::class, 'index'])->name('icons-font-awesome');
// Login
Route::get('/auth/login', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/login', [LoginBasic::class, 'login'])->name('auth-login');
// .....
// Captcha
Route::get('captcha', function () {
    return captcha_img();
});
Route::get('/reload-captcha', [CaptchaController::class, 'reloadCaptcha'])->name('reload-captcha');
//2FA Page
Route::get('/auth/2falogin', [LoginBasic::class, 'freshLogin']);
Route::get('/auth/2fa', [LoginBasic::class, 'auth2Fa'])->name('auth-2fa');
Route::get('/auth/resend-otp', [LoginBasic::class, 'resendOtp'])->name('auth-resend-otp');
Route::get('/auth/validate-otp/{otp}', [LoginBasic::class, 'verifyCode'])->name('auth-verify-otp');
// .....
// Register
Route::get('/auth/register', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::post('/auth/register', [RegisterBasic::class, 'register'])->name('auth-register');
Route::middleware([Authcheck::class])->group(function () {
    Route::get('/auth/logout', [LoginBasic::class, 'logout'])->name('auth-logout');
    // userdetails which include 2fa and others
    Route::get('/getUserDetails', [LoginBasic::class, 'getUserDetails'])->name('get-2fa');
    // navbar components and sub components
    Route::get('/corporate/corporate-components/getAllComponent/corpId/{corpId}', [corporateContractors::class, 'getAllComponents'])->name('get-components-subcomponents-via-corpid');
    // Route::get('/corporate/components/{component}/{subComponent}', [corporateContractors::class, 'displayDynamicComponentsPage'])->name('corporate-components');
    // Bulk Users Upload
    Route::get('/employees/corporate/add-corporate-users', [addCorporateUsers::class, 'displayImportView'])->name('employee-add-users');
    Route::post('/corporate/upload/add-corporate-users', [addCorporateUsers::class, 'import'])->name('add-corporate-bulk-users');
    // Single User Upload
    Route::get('/corporate/add-corporate-user', [addCorporateUsers::class, 'addSingleCorporateView'])->name('employee-list');
    Route::post('/corporate/upload/add-corporate-user', [addCorporateUsers::class, 'addSingleCorporate'])->name('add-corporate-single-users');
    // Get all with corp id and location id
    Route::get('/corporate/getAllEmployees', [addCorporateUsers::class, 'getAllEmployeeData'])->name('get-corporate-employee-data');
    Route::post('/corporate/getAllEmployeesFilters', [addCorporateUsers::class, 'getAllEmployeeDataFilters'])->name('get-corporate-employee-data-by-filters');
    Route::get('/corporate/getDepartments', [addCorporateUsers::class, 'getDepartmentHL1'])->name('get-departments-hl1');
    Route::get('/corporate/getEmployeeType', [addCorporateUsers::class, 'getEmployeeType'])->name('get-employee-type');
    Route::get('/corporate/getCorporateContractors', [addCorporateUsers::class, 'getContractors'])->name('get-corporate-contractors');
    Route::get('/corporate/getDesignation', [addCorporateUsers::class, 'getDesignation'])->name('get-corporate-designation');
    Route::post('/corporate/assignHealthPlan', [corporateAssignHealthPlans::class, 'assignHealthPlan'])->name('corporate-assign-healthplan');
    Route::get('/mhc/diagnostic-assessment/healthplans', [corporateHealthPlans::class, 'displayHealthPlanPage'])->name('diagnostic-assessment-healthplans');
    Route::get('/mhc/diagnostic-assessment/getHealthPlan/{corporate_id}/{healthplan_id}', [corporateHealthPlans::class, 'getealthPlan'])->name('corporate-healthplans-edit-page');
    Route::get('/mhc/diagnostic-assessment/getAllHealthplans', [corporateHealthPlans::class, 'getAllHealthplans']);
    Route::get('/mhc/diagnostic-assessment/getAllMasterTests', [corporateHealthPlans::class, 'getAllMasterTests']);
    Route::get('/mhc/diagnostic-assessment/getAllSubGroup', [corporateHealthPlans::class, 'getAllMasterTestsBySubGroup']);
    Route::get('/mhc/diagnostic-assessment/getAllMasterTests/{empId}/op/{op_registry_id}', [corporateHealthPlans::class, 'getAllMasterTestsForEmployee']);
    Route::get('/mhc/diagnostic-assessment/getAllMasterTests/{empId}/prescription/{prescription_id}', [corporateHealthPlans::class, 'getAllMasterTestsForEmployee']);
    Route::get('/mhc/diagnostic-assessment/getAllCertificates/{corporateId}', [corporateHealthPlans::class, 'getAllCertificates']);
    Route::get('/mhc/diagnostic-assessment/getAllForms/{corporateId}', [corporateHealthPlans::class, 'getAllForms']);
    Route::post('/mhc/diagnostic-assessment/updateHealthPlan', [corporateHealthPlans::class, 'editHealthPlan']);
    Route::get('/mhc/diagnostic-assessment/deleteHealthPlan/{corporateId}/{healthplanId}', [corporateHealthPlans::class, 'deleteHealthPlan']);
    Route::post('/mhc/diagnostic-assessment/addNewHealthPlan', [corporateHealthPlans::class, 'addHealthPlan']);
    Route::get('/mhc/diagnostic-assessment/getDoctors', [addCorporateUsers::class, 'getDoctors'])->name('get-corporate-doctor');
    Route::get('/mhc/diagnostic-assessment/getLabs', [addCorporateUsers::class, 'getLabs'])->name('get-labs');
    Route::get('/mhc/diagnostic-assessment/getFavourite', [addCorporateUsers::class, 'getFavourite'])->name('get-corporate-favourite');
    Route::get('/mhc/diagnostic-assessment/assign-health-plan', [corporateAssignHealthPlans::class, 'displayAssignHealthPlans'])->name('diagnostic-assessment-assignplan');
    Route::get('/mhc/diagnostic-assessment/assign-health-plan-list', [corporateAssignHealthPlans::class, 'displayAssignHealthPlanList'])->name('diagnostic-assessment-list');
    Route::get('/mhc/diagnostic-assessment/getAllAssignHealthPlans', [corporateAssignHealthPlans::class, 'getAllAssignHealthPlans'])->name('diagnostic-assessment-list');
    Route::get('/mhc/diagnostic-assessment/getAllColorCodes', [corporateAssignHealthPlans::class, 'getAllColorCodes'])->name('diagnostic-assessment-list');
    Route::post('/mhc/diagnostic-assessment/saveCertification', [corporateAssignHealthPlans::class, 'saveCertificateCondition'])->name('diagnostic-assessment-list');
    Route::get('/mhc/diagnostic-assessment/health-plan/{healthplan_id}/prescription-test/{test_id}/{test_code}', [corporateAssignHealthPlans::class, 'displayHealthplanTestPage'])->name('diagnostic-assessment-list');
    Route::get('/mhc/health-risk-assessment/templates', [HealthRiskAssessment::class, 'displayHRATemplatesPage'])->name('health-risk-assessment-templates');
    Route::get('/mhc/health-risk-assessment/getAllAssignedHraTemplates', [HealthRiskAssessment::class, 'getAllAssignedHraTemplates'])->name('health-risk-assessment-templates');
    Route::get('/mhc/health-risk-assessment/getAllHRATemplates', [HealthRiskAssessment::class, 'getAllHRATemplates'])->name('health-risk-assessment-templates');
    Route::get('/mhc/health-risk-assessment/getAllLocations', [HealthRiskAssessment::class, 'getAllLocations'])->name('health-risk-assessment-templates');
    Route::post('/mhc/health-risk-assessment/assignHraTemplate', [HealthRiskAssessment::class, 'assignHRATemplate'])->name('health-risk-assessment-templates');
    Route::put('/mhc/health-risk-assessment/updateAssignedHraTemplate', [HealthRiskAssessment::class, 'updateAssignedHraTemplate'])->name('health-risk-assessment-templates');

    // Events store and update routes
    Route::post('/mhc/events/store-events', [EventsController::class, 'storeEvents'])->name('events-store-events');
    Route::put('/mhc/events/update-events/{id}', [EventsController::class, 'updateEvents'])->name('events-update-events');
    Route::get('/mhc/events/list-events', [EventsController::class, 'listEvents'])->name('list-events');
    Route::get('/mhc/events/add-events', [EventsController::class, 'addNewEvents'])->name('add-new-events');
    Route::get('/mhc/events/get-events', [EventsController::class, 'getAllEventsByCorporate'])->name('get-events');
    Route::delete('/mhc/events/delete/{id}', [EventsController::class, 'destroy']);
    Route::get('/mhc/events/edit-events/{id}', [EventsController::class, 'editEvents'])->name('edit-events');
    Route::get('/mhc/events/modify-events/{id}', [EventsController::class, 'getEventsById'])->name('modify-events');




    Route::get('/ohc/health-registry/add-registry', [health_registry::class, 'displayAddRegistryPage'])->name('health-registry-add-registry');
    Route::get('/ohc/health-registry/add-registry/search/{keyword}', [health_registry::class, 'getDataByKeywordForAddRegistryPage'])->name('health-registry-add-registry');
    Route::get('/ohc/health-registry/add-registry/add-outpatient/{employee_id}', [health_registry::class, 'displayRegistryOutpatientPage'])->name('health-registry-add-registry');
    Route::get('/ohc/health-registry/add-test/{employee_id}/op/{op_registry_id}', [health_registry::class, 'displayAddTestPage'])->name('health-registry-add-registry');
    Route::get('/ohc/health-registry/add-test/{employee_id}', [health_registry::class, 'displayAddTestPage'])->name('health-registry-add-registry');
    Route::post('/ohc/health-registry/add-test/{employee_id}/op/{op_registry_id}', [health_registry::class, 'addTestForEmployee'])->name('health-registry-add-registry');
    Route::get('/ohc/health-registry/add-test/{employee_id}/prescription/{prescription_id}', [health_registry::class, 'displayAddTestPage'])->name('health-registry-add-registry');
    Route::post('/ohc/health-registry/add-test/{employee_id}/prescription/{prescription_id}', [health_registry::class, 'addTestForEmployeeForPrescription'])->name('health-registry-add-registry');
    Route::get('/ohc/health-registry/list-registry', [health_registry::class, 'displayListRegistryPage'])->name('health-registry-list-registry');
    Route::get('/ohc/health-registry/getAllHealthRegistry', [health_registry::class, 'getAllHealthRegistry'])->name('health-registry-get-all-registry');
    Route::get('/ohc/health-registry/getAllHealthRegistry/{employeeId}', [health_registry::class, 'getAllHealthRegistry'])->name('health-registry-get-all-registry');
    Route::get('/ohc/health-registry/edit-registry/edit-outpatient/{employee_id}/op/{op_registry_id}', [health_registry::class, 'displayRegistryOutpatientPage'])->name('health-registry-list-registry');
    Route::get('/ohc/health-registry/view-registry/view-outpatient/{employee_id}/op/{op_registry_id}', [health_registry::class, 'displayRegistryOutpatientPage'])->name('health-registry-list-registry');
    Route::get('/ohc/health-registry/add-follow-up-registry/add-follow-up-outpatient/{employeeId}/op/{opRegistryId}', [health_registry::class, 'displayFollowUpAddRegistryPage'])->name('health-registry-add-registry');

    Route::get('/ohc/test-list', [Tests::class, 'displayTestListPage'])->name('ohc-tests');
    Route::get('/ohc/test-details/{testCode}', [Tests::class, 'displayTestDetailsPage'])->name('ohc-tests');
    Route::get('/ohc/test-details', [Tests::class, 'displayTestDetailsPageDummy'])->name('ohc-tests');
    Route::get('/ohc/getAllTests', [Tests::class, 'getAllTestsFromPrescribedTest'])->name('ohc-getAllTests');

    Route::post('/ohc/test-details/save-results', [Tests::class, 'saveTestResults'])->name('ohc-save-test_results');

    Route::get('/ohc/health-registry/getAllSymptoms', [health_registry::class, 'getAllSymptoms'])->name('health-registry-get-all-symptoms');
    Route::get('/ohc/health-registry/getAllDiagnosis', [health_registry::class, 'getAllDiagnosis'])->name('health-registry-get-all-diagnosis');
    Route::get('/ohc/health-registry/getAllMedicalSystem', [health_registry::class, 'getAllMedicalSystem'])->name('health-registry-get-all-medical-system');
    Route::get('/ohc/health-registry/getAllBodyParts', [health_registry::class, 'getAllBodyParts'])->name('health-registry-get-all-body-parts');
    Route::get('/ohc/health-registry/getAllNatureOfInjury', [health_registry::class, 'getAllNatureOfInjury'])->name('health-registry-get-all-nature-of-injury');
    Route::get('/ohc/health-registry/getAllInjuryMechanism', [health_registry::class, 'getAllInjuryMechanism'])->name('health-registry-get-all-injury-mechanism');
    Route::get('/ohc/health-registry/getMRNumber', [health_registry::class, 'getMRNumber'])->name('health-registry-get-Mr-number');
    Route::post('/ohc/health-registry/saveHealthRegistry', [health_registry::class, 'saveHealthRegistry'])->name('health-registry-add-out-patient');
    Route::post('/ohc/health-registry/saveHealthRegistry/{opRegistryId}', [health_registry::class, 'saveHealthRegistry'])->name('health-registry-add-out-patient');
    Route::get('/ohc/health-registry/getincidentTypeColorCodes', [health_registry::class, 'getincidentTypeColorCodes'])->name('getincidentTypeColorCodes');
    // ....
    //Contractor
    Route::get('/corporate/list-contractors', [corporateContractors::class, 'index'])->name('employee-add-contractor');

    Route::get('/corporate/contractors-list', [corporateContractors::class, 'listcontractors'])->name('employee-add-contractor');
    Route::post('/corporate/contractor', [corporateContractors::class, 'addContractors'])->name('addContractors');
    Route::put('/corporate/updateContractor/{id}', [corporateContractors::class, 'updateContractor']);
    Route::delete('/corporate/deleteContractor/{id}', [corporateContractors::class, 'deleteContractor']);
    Route::get('/corporate/view-uploaded-users/{corporate_id}/{location_id}/{file_name}', [addCorporateUsers::class, 'viewExcelData'])->name('employee-add-users');
    Route::get('/corporate/getMasterUserDetails', [addCorporateUsers::class, 'getMasterUserDetailsCount']);
    Route::delete('/corporate/delete-uploaded-users/', [addCorporateUsers::class, 'deleteExcelData'])->name('delete-corporate-excel');
    Route::post('/corporate/send-uploaded-users/', [addCorporateUsers::class, 'sendExcelData'])->name('add-corporate-excel');
    Route::get('/corporate/getUploadedExcelStatus', [addCorporateUsers::class, 'getUploadedExcelStatus']);
    Route::get('/corporate/getUploadedExcelFileContent/{id}', [addCorporateUsers::class, 'getUploadedExcelFileContent']);
    Route::get('/corporate/getAllCorporates', [addCorporateUsers::class, 'getAllCorporates']);
    Route::get('/corporate/getAllLocations/{corporate_id}', [addCorporateUsers::class, 'getAllLocations']);
    // .....
    //corporate  users
    Route::get('/corporate-users/users-list', [CorporateUserController::class, 'UserList'])->name('corporate-users-list');
    Route::get('/corporate-users/getUserDetails', [CorporateUserController::class, 'getUserDetails'])->name('getUserDetails');
    Route::get('/corporate-users/add-corporate-user', [CorporateUserController::class, 'userAdd'])->name('add-corporate-user');
    Route::post('/corporate-users/insertUser', [CorporateUserController::class, 'insertUser'])->name('insertUser');
    Route::get('/corporate-users/edit-corporate-user/{id}', [CorporateUserController::class, 'userEdit'])->name('edit-corporate-user');
    Route::post('/corporate-users/updateUser/{id}', [CorporateUserController::class, 'updateUser'])->name('updateUser');
    Route::get('/corporate-users/mhc-rights/{id}', [CorporateUserController::class, 'mhcRights'])->name('mhc-rights');
    Route::get('/corporate-users/ohc-rights/{id}', [CorporateUserController::class, 'ohcRights'])->name('ohc-rights');
    Route::get('/corporate-users/get-mhc-menu/{id}', [CorporateUserController::class, 'getmhcMenu'])->name('get-mhc-menu');
    Route::post('/corporate-users/save-mhc-rights', [CorporateUserController::class, 'savemhcRights'])->name('savemhcRights');
    Route::post('/corporate-users/update-mhc-rights', [CorporateUserController::class, 'updatemhcRights'])->name('updatemhcRights');
    Route::get('/corporate-users/get-ohc-menu/{id}', [CorporateUserController::class, 'getohcMenu'])->name('get-ohc-menu');
    Route::post('/corporate-users/save-ohc-rights', [CorporateUserController::class, 'saveohcRights'])->name('saveohcRights');
    Route::post('/corporate-users/update-ohc-rights', [CorporateUserController::class, 'updateohcRights'])->name('updateohcRights');

    //.....
    //empolyeee
    Route::get('/employees/employee-list', [Employees::class, 'displayEmployeeListPage'])->name('employee-list');
    Route::get('/employees/getAllEmployees', [Employees::class, 'getAllEmployeeList'])->name('getAllEmployees');
    Route::get('/employees/employee-type', [EmployeeType::class, 'index'])->name('employee-type');
    Route::post('/updateEmployeeTypes', [EmployeeType::class, 'update'])->name('updateemptype');
    Route::post('/employeetype/add', [EmployeeType::class, 'store'])->name('employeetype_add');
    //Drug template
    Route::get('/drugs/drug-template-list', [DrugTemplateController::class, 'drugTemplateList'])->name('corporate-ohc-drugs-templates');
    Route::get('/drugs/drug-template-add', [DrugTemplateController::class, 'drugTemplateAdd'])->name('drug-template');
    Route::get('/drugs/drug-template-edit/{id}', [DrugTemplateController::class, 'drugTemplateEdit'])->name('drug-template');
    Route::get('/DrugTemplate/getDrugTypesAndIngredients', [DrugTemplateController::class, 'getDrugTypesAndIngredients'])->name('drugTypesAndIngredients');
    Route::post('/DrugTemplate/drug-template/store', [DrugTemplateController::class, 'store'])->name('drugTemplate.store');
    Route::post('/DrugTemplate/drug-template/update/{id}', [DrugTemplateController::class, 'update'])->name('drugTemplate.update');
    //New Style
    Route::get('/drugs/new-style', [DrugTemplateController::class, 'newStyleList'])->name('new-style');

    //hl1department
    Route::get('/mhc/ohc-list', [CorporateOhcController::class, 'corporateOHCList'])->name('corporate-ohc-list');
    Route::get('/corporate/getAllOHCDetails', [CorporateOhcController::class, 'getAllDetails'])->name('corporateohc-list');
    Route::get('/corporate/getAllPharmacyDetails', [CorporateOhcController::class, 'getAllPharmacyDetails'])->name('corporateohc-list');
    Route::post('/corporate/addCorporateOHC', [CorporateOhcController::class, 'addCorporateOHC'])->name('addCorporateOHC');
    Route::post('/corporate/addPharmacy', [CorporateOhcController::class, 'addPharmacy'])->name('addPharmacy');
    Route::post('/corporate/updateCorporateOHC/{id}', [CorporateOhcController::class, 'updateCorporateOHC'])->name('updateCorporateOHC');
    Route::post('/corporate/updatePharmacy/{id}', [CorporateOhcController::class, 'updatePharmacy'])->name('updatePharmacy');
    //Pharmacy Stock
    Route::get('/pharmacy/pharmacy-stock-list', [PharmacyStockController::class, 'pharmacyStockList'])->name('corporate-ohc-drugs-stock');
    Route::get('/PharmacyStock/getPharmacyStockDetails', [PharmacyStockController::class, 'getPharmacyStockDetails'])->name('getPharmacyStockDetails');
    Route::get('/PharmacyStock/pharmacystock-add', [PharmacyStockController::class, 'pharmacyStockAdd'])->name('pharmacystock-add');
    Route::get('/PharmacyStock/getDrugTemplateDetails', [PharmacyStockController::class, 'getDrugTemplateDetails'])->name('getDrugTemplateDetails');
    Route::post('/PharmacyStock/pharmacyStock/store', [PharmacyStockController::class, 'store'])->name('pharmacyStock.store');
    Route::post('/PharmacyStock/pharmacyStock/update/{id}', [PharmacyStockController::class, 'update'])->name('pharmacyStock.update');
    Route::get('/PharmacyStock/getDrugTypesAndIngredients', [PharmacyStockController::class, 'getDrugTypesAndIngredients'])->name('getDrugTypesAndIngredients');
    //Route::get('/PharmacyStock/getSubPharmacyDetails', [PharmacyStockController::class, 'getSubPharmacyDetails'])->name('getSubPharmacyDetails');
    Route::get('/PharmacyStock/getSubPharmacyDetails/{store_id}', [PharmacyStockController::class, 'getSubPharmacyDetails'])->name('getSubPharmacyDetails');
    // Route::get('/PharmacyStock/getPharmacyStockByAvailability/{id}', [PharmacyStockController::class, 'getPharmacyStockByAvailability'])->name('getPharmacyStockByAvailability');
    Route::get('/PharmacyStock/getPharmacyStockByAvailability/{availability}/{storeId}', [PharmacyStockController::class, 'getPharmacyStockByAvailability']);

    Route::get('/pharmacy/pharmacy-move-list', [PharmacyStockController::class, 'pharmacyMoveList'])->name('corporate-ohc-drugs-stock-move');
    Route::post('/pharmacy/moveStock', [PharmacyStockController::class, 'moveStock'])->name('moveStock');


    //others
    //BioMedicalWaste
    Route::get('/others/bio-medical-waste', [BioMedicalWasteController::class, 'bioMedicalWasteList'])->name('bio-medical-waste');
    Route::get('/others/getAllBiowasteDetails', [BioMedicalWasteController::class, 'getAllDetails'])->name('getAllBiowasteDetails');
    Route::post('/others/addBioMedicalWaste', [BioMedicalWasteController::class, 'addBioMedicalWaste'])->name('addBioMedicalWaste');
    Route::post('/others/updateBioMedicalWaste/{id}', [BioMedicalWasteController::class, 'updateBioMedicalWaste'])->name('updateBioMedicalWaste');


    //Inventory
    Route::get('/others/inventory', [InventoryController::class, 'listInventory'])->name('inventory');
    Route::get('/others/inventoryList/', [InventoryController::class, 'inventoryList'])->name('inventory');
    Route::get('/others/inventory-add', [InventoryController::class, 'inventoryAdd'])->name('inventory-add');
    Route::post('/others/store', [InventoryController::class, 'store'])->name('Others.store');
    Route::post('/others/update/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::get('/others/inventory-edit/{id}', [InventoryController::class, 'inventoryEdit'])->name('inventory-edit');
    // In routes/api.php (or routes/web.php if you need a web route)
    Route::get('/others/inventory-history/{id}', [InventoryController::class, 'getCalibrationHistory']);

    //Invoice
    Route::get('/others/invoice', [InvoiceController::class, 'VendorList'])->name('invoice');
    Route::get('/others/getInvoiceDetails', [InvoiceController::class, 'getInvoiceDetails'])->name('getInvoiceDetails');
    Route::get('/others/listVendor', [InvoiceController::class, 'listVendor'])->name('listVendor');
    Route::post('/others/addVendor', [InvoiceController::class, 'addVendor'])->name('addVendor');
    Route::get('/others/getVendorDetails', [InvoiceController::class, 'getVendorDetails'])->name('getVendorDetails');
    Route::get('/others/add-invoice', [InvoiceController::class, 'invoiceAdd'])->name('add-invoice');
    Route::post('/others/insertInvoice', [InvoiceController::class, 'insertInvoice'])->name('insertInvoice');
    Route::get('/others/edit-invoice/{id}', [InvoiceController::class, 'invoiceEdit'])->name('edit-invoice');
    Route::post('/others/updateInvoice/{id}', [InvoiceController::class, 'updateInvoice'])->name('updateInvoice');
    Route::get('/others/getPoBalance', [InvoiceController::class, 'getPoBalance'])->name('getPoBalance');

    //Prescription
    Route::get('/prescription/prescription-view', [PrescriptionController::class, 'prescriptionView'])->name('ohc-prescription-view');
    Route::get('/prescription/prescription-template', [PrescriptionController::class, 'prescriptionTemplate'])->name('ohc-prescription-template');
    Route::post('/prescription/store', [PrescriptionController::class, 'store'])->name('prescription.store');
    Route::get('/prescription/prescription-template-add', [PrescriptionController::class, 'prescriptionTemplateAdd'])->name('prescriptionTemplateAdd');
    Route::get('/prescription/getPrescriptionDetails', [PrescriptionController::class, 'getPrescriptionDetails'])->name('getPrescriptionDetails');
    Route::get('/prescription/prescription-edit/{id}', [PrescriptionController::class, 'prescriptionTemplateEdit'])->name('prescriptionTemplateEdit');
    Route::get('/prescription/prescription-editById/{id}', [PrescriptionController::class, 'prescriptionTemplateEditById'])->name('prescriptionTemplateEditById');
    Route::post('/prescription/prescription-update/{id}', [PrescriptionController::class, 'updatePrescriptionTemplate'])->name('updatePrescriptionTemplate');
    Route::get('/prescription/prescription-add', [PrescriptionController::class, 'prescriptionAdd'])->name('prescriptionAdd');
    Route::get('/prescription/add-employee-prescription/{employee_id}', [PrescriptionController::class, 'addEmployeePrescription'])->name('addEmployeePrescription');
    Route::get('/prescription/add-employee-prescription/{employee_id}/op/{op_registry_id}', [PrescriptionController::class, 'addEmployeePrescription'])->name('addEmployeePrescription');
    Route::get('/prescription/getPrescriptionTemplate', [PrescriptionController::class, 'getPrescriptionTemplate'])->name('getPrescriptionTemplate');
    Route::post('/prescription/store_EmployeePrescription', [PrescriptionController::class, 'store_EmployeePrescription'])->name('store_EmployeePrescription');
    Route::get('/prescription/getAllEmployeePrescription', [PrescriptionController::class, 'getAllEmployeePrescription'])->name('getAllEmployeePrescription');
    Route::get('/prescription/getStockByDrugId/{id}', [PrescriptionController::class, 'getStockByDrugId'])->name('getStockByDrugId');
    Route::get('/prescription/prescription-print-option/{id}', [PrescriptionController::class, 'prescriptionPrintOption'])->name('prescriptionPrintOption');
    Route::get('/prescription/getPrintPrescriptionById/{id}', [PrescriptionController::class, 'getPrintPrescriptionById'])->name('getPrescriptionById');


    Route::get('/requests/pending-requests', [RequestController::class, 'getPendingRequests'])->name('requests-pending-requests');
    Route::post('/requests/close-prescription', [RequestController::class, 'closePrescription'])->name('requests-pending-requests');
    Route::get('/requests/getAllEmployeePrescription', [RequestController::class, 'getAllEmployeePrescription'])->name('getAllEmployeePrescription');
    Route::post('/requests/issue-partly-prescription', [RequestController::class, 'issuePartlyPrescription'])->name('requests-pending-requests');
    Route::get('/requests/complete-requests', [RequestController::class, 'getCompletedRequests'])->name('requests-completed-requests');
    Route::get('/requests/complete-prescription', [RequestController::class, 'getCompletedPrescription'])->name('requests-completed-requests');
    Route::get('/prescription/getStockByDrugIdAndPharmacyId/{drugTemplateId}/{pharmacyId}', [PrescriptionController::class, 'getStockByDrugIdAndPharmacyId']);


    Route::get('/employees/department/hl1', [CorporateHl1::class, 'index'])->name('employee-Department-hl1');
    Route::post('/hl1create', [CorporateHl1::class, 'store'])->name('hl1_store');
    Route::post('/hl1update/{id}', [CorporateHl1::class, 'update'])->name('hl1.update');
    Route::delete('/hl1delete/{id}', [CorporateHl1::class, 'destroy'])->name('hl1.destroy');
    //
    Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
    Route::get('/dashboard/analytics', [Analytics::class, 'index'])->name('dashboard-analytics');
    Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');
    // locale
    Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
    // layout
    Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
    Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
    Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
    Route::get('/layouts/navbar-full', [NavbarFull::class, 'index'])->name('layouts-navbar-full');
    Route::get('/layouts/navbar-full-sidebar', [NavbarFullSidebar::class, 'index'])->name('layouts-navbar-full-sidebar');
    Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('dashboard-analytics');
    Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('dashboard-analytics');
    Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
    Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
    Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
    Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
    Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');
    // Front Pages
    Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
    Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
    Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
    Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
    Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
    Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name('front-pages-help-center-article');
    // apps
    Route::get('/app/email', [Email::class, 'index'])->name('app-email');
    Route::get('/app/chat', [Chat::class, 'index'])->name('app-chat');
    Route::get('/app/calendar', [Calendar::class, 'index'])->name('app-calendar');
    Route::get('/app/kanban', [Kanban::class, 'index'])->name('app-kanban');
    Route::get('/app/ecommerce/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
    Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
    Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
    Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index'])->name('app-ecommerce-product-category');
    Route::get('/app/ecommerce/order/list', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
    Route::get('/app/ecommerce/order/details', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');
    Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
    Route::get('/app/ecommerce/customer/details/overview', [EcommerceCustomerDetailsOverview::class, 'index'])->name('app-ecommerce-customer-details-overview');
    Route::get('/app/ecommerce/customer/details/security', [EcommerceCustomerDetailsSecurity::class, 'index'])->name('app-ecommerce-customer-details-security');
    Route::get('/app/ecommerce/customer/details/billing', [EcommerceCustomerDetailsBilling::class, 'index'])->name('app-ecommerce-customer-details-billing');
    Route::get('/app/ecommerce/customer/details/notifications', [EcommerceCustomerDetailsNotifications::class, 'index'])->name('app-ecommerce-customer-details-notifications');
    Route::get('/app/ecommerce/manage/reviews', [EcommerceManageReviews::class, 'index'])->name('app-ecommerce-manage-reviews');
    Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
    Route::get('/app/ecommerce/settings/details', [EcommerceSettingsDetails::class, 'index'])->name('app-ecommerce-settings-details');
    Route::get('/app/ecommerce/settings/payments', [EcommerceSettingsPayments::class, 'index'])->name('app-ecommerce-settings-payments');
    Route::get('/app/ecommerce/settings/checkout', [EcommerceSettingsCheckout::class, 'index'])->name('app-ecommerce-settings-checkout');
    Route::get('/app/ecommerce/settings/shipping', [EcommerceSettingsShipping::class, 'index'])->name('app-ecommerce-settings-shipping');
    Route::get('/app/ecommerce/settings/locations', [EcommerceSettingsLocations::class, 'index'])->name('app-ecommerce-settings-locations');
    Route::get('/app/ecommerce/settings/notifications', [EcommerceSettingsNotifications::class, 'index'])->name('app-ecommerce-settings-notifications');
    Route::get('/app/academy/dashboard', [AcademyDashboard::class, 'index'])->name('app-academy-dashboard');
    Route::get('/app/academy/course', [AcademyCourse::class, 'index'])->name('app-academy-course');
    Route::get('/app/academy/course-details', [AcademyCourseDetails::class, 'index'])->name('app-academy-course-details');
    Route::get('/app/logistics/dashboard', [LogisticsDashboard::class, 'index'])->name('app-logistics-dashboard');
    Route::get('/app/logistics/fleet', [LogisticsFleet::class, 'index'])->name('app-logistics-fleet');
    Route::get('/app/invoice/list', [InvoiceList::class, 'index'])->name('app-invoice-list');
    Route::get('/app/invoice/preview', [InvoicePreview::class, 'index'])->name('app-invoice-preview');
    Route::get('/app/invoice/print', [InvoicePrint::class, 'index'])->name('app-invoice-print');
    Route::get('/app/invoice/edit', [InvoiceEdit::class, 'index'])->name('app-invoice-edit');
    Route::get('/app/invoice/add', [InvoiceAdd::class, 'index'])->name('app-invoice-add');
    Route::get('/app/user/list', [UserList::class, 'index'])->name('app-user-list');
    Route::get('/app/user/view/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');
    Route::get('/app/user/view/security', [UserViewSecurity::class, 'index'])->name('app-user-view-security');
    Route::get('/app/user/view/billing', [UserViewBilling::class, 'index'])->name('app-user-view-billing');
    Route::get('/app/user/view/notifications', [UserViewNotifications::class, 'index'])->name('app-user-view-notifications');
    Route::get('/app/user/view/connections', [UserViewConnections::class, 'index'])->name('app-user-view-connections');
    Route::get('/app/access-roles', [AccessRoles::class, 'index'])->name('app-access-roles');
    Route::get('/app/access-permission', [AccessPermission::class, 'index'])->name('app-access-permission');
    // pages
    Route::get('/pages/profile-user', [UserProfile::class, 'index'])->name('pages-profile-user');
    Route::get('/pages/profile-teams', [UserTeams::class, 'index'])->name('pages-profile-teams');
    Route::get('/pages/profile-projects', [UserProjects::class, 'index'])->name('pages-profile-projects');
    Route::get('/pages/profile-connections', [UserConnections::class, 'index'])->name('pages-profile-connections');
    Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
    Route::get('/pages/account-settings-security', [AccountSettingsSecurity::class, 'index'])->name('pages-account-settings-security');
    Route::get('/pages/account-settings-billing', [AccountSettingsBilling::class, 'index'])->name('pages-account-settings-billing');
    Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
    Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
    Route::get('/pages/faq', [Faq::class, 'index'])->name('pages-faq');
    Route::get('/pages/pricing', [PagesPricing::class, 'index'])->name('pages-pricing');
    Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
    Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');
    Route::get('/pages/misc-comingsoon', [MiscComingSoon::class, 'index'])->name('pages-misc-comingsoon');
    Route::get('/pages/misc-not-authorized', [MiscNotAuthorized::class, 'index'])->name('pages-misc-not-authorized');
    Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
    Route::get('/auth/register-cover', [RegisterCover::class, 'index'])->name('auth-register-cover');
    Route::get('/auth/register-multisteps', [RegisterMultiSteps::class, 'index'])->name('auth-register-multisteps');
    Route::get('/auth/verify-email-basic', [VerifyEmailBasic::class, 'index'])->name('auth-verify-email-basic');
    Route::get('/auth/verify-email-cover', [VerifyEmailCover::class, 'index'])->name('auth-verify-email-cover');
    Route::get('/auth/reset-password-basic', [ResetPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
    Route::get('/auth/reset-password-cover', [ResetPasswordCover::class, 'index'])->name('auth-reset-password-cover');
    Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
    Route::get('/auth/forgot-password-cover', [ForgotPasswordCover::class, 'index'])->name('auth-forgot-password-cover');
    Route::get('/auth/two-steps-basic', [TwoStepsBasic::class, 'index'])->name('auth-two-steps-basic');
    Route::get('/auth/two-steps-cover', [TwoStepsCover::class, 'index'])->name('auth-two-steps-cover');
    // wizard example
    Route::get('/wizard/ex-checkout', [WizardCheckout::class, 'index'])->name('wizard-ex-checkout');
    Route::get('/wizard/ex-property-listing', [PropertyListing::class, 'index'])->name('wizard-ex-property-listing');
    Route::get('/wizard/ex-create-deal', [CreateDeal::class, 'index'])->name('wizard-ex-create-deal');
    // modal
    Route::get('/modal-examples', [ModalExample::class, 'index'])->name('modal-examples');
    // cards
    Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');
    Route::get('/cards/advance', [CardAdvance::class, 'index'])->name('cards-advance');
    Route::get('/cards/statistics', [CardStatistics::class, 'index'])->name('cards-statistics');
    Route::get('/cards/analytics', [CardAnalytics::class, 'index'])->name('cards-analytics');
    Route::get('/cards/gamifications', [CardGamifications::class, 'index'])->name('cards-gamifications');
    Route::get('/cards/actions', [CardActions::class, 'index'])->name('cards-actions');
    // User Interface
    Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
    Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
    Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
    Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
    Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
    Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
    Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
    Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
    Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
    Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
    Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
    Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
    Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
    Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
    Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
    Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
    Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
    Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
    Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');
    // extended ui
    Route::get('/extended/ui-avatar', [Avatar::class, 'index'])->name('extended-ui-avatar');
    Route::get('/extended/ui-blockui', [BlockUI::class, 'index'])->name('extended-ui-blockui');
    Route::get('/extended/ui-drag-and-drop', [DragAndDrop::class, 'index'])->name('extended-ui-drag-and-drop');
    Route::get('/extended/ui-media-player', [MediaPlayer::class, 'index'])->name('extended-ui-media-player');
    Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
    Route::get('/extended/ui-star-ratings', [StarRatings::class, 'index'])->name('extended-ui-star-ratings');
    Route::get('/extended/ui-sweetalert2', [SweetAlert::class, 'index'])->name('extended-ui-sweetalert2');
    Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');
    Route::get('/extended/ui-timeline-basic', [TimelineBasic::class, 'index'])->name('extended-ui-timeline-basic');
    Route::get('/extended/ui-timeline-fullscreen', [TimelineFullscreen::class, 'index'])->name('extended-ui-timeline-fullscreen');
    Route::get('/extended/ui-tour', [Tour::class, 'index'])->name('extended-ui-tour');
    Route::get('/extended/ui-treeview', [Treeview::class, 'index'])->name('extended-ui-treeview');
    Route::get('/extended/ui-misc', [Misc::class, 'index'])->name('extended-ui-misc');
    // icons
    Route::get('/icons/tabler', [Tabler::class, 'index'])->name('icons-tabler');
    Route::get('/icons/font-awesome', [FontAwesome::class, 'index'])->name('icons-font-awesome');
    // form elements
    Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
    Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');
    Route::get('/forms/custom-options', [CustomOptions::class, 'index'])->name('forms-custom-options');
    Route::get('/forms/editors', [Editors::class, 'index'])->name('forms-editors');
    Route::get('/forms/file-upload', [FileUpload::class, 'index'])->name('forms-file-upload');
    Route::get('/forms/pickers', [Picker::class, 'index'])->name('forms-pickers');
    Route::get('/forms/selects', [Selects::class, 'index'])->name('forms-selects');
    Route::get('/forms/sliders', [Sliders::class, 'index'])->name('forms-sliders');
    Route::get('/forms/switches', [Switches::class, 'index'])->name('forms-switches');
    Route::get('/forms/extras', [Extras::class, 'index'])->name('forms-extras');
    // form layouts
    Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
    Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');
    Route::get('/form/layouts-sticky', [StickyActions::class, 'index'])->name('form-layouts-sticky');
    // form wizards
    Route::get('/form/wizard-numbered', [FormWizardNumbered::class, 'index'])->name('form-wizard-numbered');
    Route::get('/form/wizard-icons', [FormWizardIcons::class, 'index'])->name('form-wizard-icons');
    Route::get('/form/validation', [Validation::class, 'index'])->name('form-validation');
    // tables
    Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
    Route::get('/tables/datatables-basic', [DatatableBasic::class, 'index'])->name('tables-datatables-basic');
    Route::get('/tables/datatables-advanced', [DatatableAdvanced::class, 'index'])->name('tables-datatables-advanced');
    Route::get('/tables/datatables-extensions', [DatatableExtensions::class, 'index'])->name('tables-datatables-extensions');
    // charts
    Route::get('/charts/apex', [ApexCharts::class, 'index'])->name('charts-apex');
    Route::get('/charts/chartjs', [ChartJs::class, 'index'])->name('charts-chartjs');
    // maps
    Route::get('/maps/leaflet', [Leaflet::class, 'index'])->name('maps-leaflet');
    // laravel example
    Route::get('/laravel/user-management', [UserManagement::class, 'UserManagement'])->name('laravel-example-user-management');
    Route::resource('/user-list', UserManagement::class);
    Route::get('/proxy/fetch-employee-type/{corporate_id}', function ($corporate_id) {
        $apiUrl = "https://api-user.hygeiaes.com/V1/corporate/corporate-components/fetchEmployeeType/$corporate_id";
        $response = Http::get($apiUrl);
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    });
    Route::get('/proxy/fetch-contractor-type/{locationId}', function ($locationId) {
        $apiUrl = "https://api-user.hygeiaes.com/V1/corporate/corporate-components/viewContractors/$locationId";
        $response = Http::get($apiUrl);
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    });
    Route::get('/proxy/fetch-department/{corporate_id}', function ($corporate_id) {
        $apiUrl = "https://api-user.hygeiaes.com/V1/corporate/corporate-components/fetchDepartment/$corporate_id";
        $response = Http::get($apiUrl);
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    });

    //OTC
    Route::get('/otc/otc-add', [otcController::class, 'searchOTC'])->name('otc-add-otc');
    Route::get('/otc/add-otc/{empId}', [otcController::class, 'addOTC'])->name('add-otc');
    Route::post('/otc/storeotc', [otcController::class, 'storeOTC'])->name('store');
    Route::get('/otc/list-otc', [otcController::class, 'listOTC'])->name('otc-list-otc');
    Route::get('/otc/listotcdetails', [otcController::class, 'listotcdetails'])->name('otc-list-otc');



    // employee routes below
    Route::get('/UserEmployee/user_dashboard', [EmployeeUserController::class, 'index'])->name('employee-user-dashboard-analytics');
    Route::get('/UserEmployee/userpersonalInformation', [EmployeeUserController::class, 'showPersonalInfo'])->name('employee-user-personal-info');

    Route::post('/UserEmployee/updateProfileDetails/{id}', [EmployeeUserController::class, 'updateProfileDetails'])->name('update-profile-details');

    Route::get('/UserEmployee/userPrescription', [EmployeeUserController::class, 'showPrescription'])->name('employee-user-prescription');
    Route::get('/UserEmployee/getuserPrescription', [EmployeeUserController::class, 'getuserPrescription'])->name('employee-getuser-prescription');
    Route::get('/UserEmployee/userCorporateDataUser', [EmployeeUserController::class, 'showCorporateData'])->name('employee-user-corporate-data');
    Route::get('/UserEmployee/healthMonitoring', [EmployeeUserController::class, 'showHealthMonitoring'])->name('employee-health-monitoring');
    Route::get('/UserEmployee/userReports', [EmployeeUserController::class, 'showReports'])->name('employee-user-reports');
    Route::get('/UserEmployee/userReportsGraph', [EmployeeUserController::class, 'showReportsInGraph'])->name('employee-user-reports-graph');
    Route::get('/UserEmployee/userReportsGraphForMutipleTest', [EmployeeUserController::class, 'showReportsInGraphForMutipleTest'])->name('employee-user-reports-graph-multipletest');

    Route::get('/UserEmployee/userSettings', [EmployeeUserController::class, 'showSettings'])->name('employee-user-settings');

    Route::get('/UserEmployee/diagnosticAssesment', [EmployeeUserController::class, 'diagnosticAssesment'])->name('employee-user-diagnostic-assessment');
    Route::get('/UserEmployee/healthRiskAssement', [EmployeeUserController::class, 'healthRiskAssesment'])->name('employee-user-hra');
    Route::get('/UserEmployee/events', [EmployeeUserController::class, 'events'])->name('employee-user-events');
    Route::get('/UserEmployee/outPatient', [EmployeeUserController::class, 'outPatient'])->name('employee-user-out-patient');
    Route::get('/UserEmployee/otc', [EmployeeUserController::class, 'otc'])->name('employee-user-otc');
    Route::get('/UserEmployee/userTest', [EmployeeUserController::class, 'showTest'])->name('employee-user-test');

    Route::get('/UserEmployee/hospitalization', [EmployeeUserController::class, 'hospitalisationDetails'])->name('employee-user-hospitalization-details');
    Route::get('/UserEmployee/userCondition', [EmployeeUserController::class, 'userMedicalCondition'])->name('employee-user-condition');
    Route::get('/UserEmployee/employeeDetails', [EmployeeUserController::class, 'getemployeeDetails'])->name('employee-user-details');
    //Route::get('/UserEmployee/getuserPrescriptionForOtc', [EmployeeUserController::class, 'getuserPrescriptionForOtc'])->name('employee-getuser-prescriptionfor-otc');
    Route::get('/UserEmployee/listotcdetailsForEmployee', [EmployeeUserController::class, 'listotcdetailsForEmployee'])->name('otc-list-otc');
    Route::get('/UserEmployee/getHealthRegistryForEmployee/{employee_id}/op/{op_registry_id}', [EmployeeUserController::class, 'displayRegistryOutpatientPage'])->name('health-registry-list-registry');
    Route::get('/UserEmployee/getEmployeeTestForGraph/{master_user_id}/{test_id}', [EmployeeUserController::class, 'getEmployeeTestForGraph'])->name('employee-test-reports');

    Route::get('/UserEmployee/getEventsforEmployees', [EmployeeUserController::class, 'getEventsforEmployees'])->name('employee-get-events-for-employees');
    Route::get('/UserEmployee/dashboard/events/getEventDetails', [EmployeeUserController::class, 'getEventDetails'])->name('getEventDetails');

    Route::get('/UserEmployee/dashboard/templates/getAllAssignedTemplates', [EmployeeDashboard::class, 'getAllAssignedTemplates']);
    Route::get('/UserEmployee/dashboard/templates/hra-questionaire/template/{template_id}', [EmployeeDashboard::class, 'displayHraQuestionaireTemplate'])->name('hra-questionaire-template');
    Route::get('/UserEmployee/dashboard/templates/getTemplateQuestions/{templateId}', [EmployeeDashboard::class, 'getTemplateQuestions']);
    Route::post('/UserEmployee/dashboard/templates/saveHraTemplateQuestionnaireAnswers/{templateId}', [EmployeeDashboard::class, 'saveHraTemplateQuestionnaireAnswers'])->name('hra-questionaire-submit');
});
//addtest page
Route::get('/add-test', function () {
    return view('content.components.ohc.others.addtest');
})->name('addtest');
// Route::get('/test-add', function () {
//     return view('content.components.ohc.others.test-add');
// })->name('test-add');
Route::get('/test-add', [health_registry::class, 'RegistryOutpatientPage'])->name('test-add');


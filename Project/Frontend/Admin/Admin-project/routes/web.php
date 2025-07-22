<?php

// use App\Http\Controllers\V1Controllers\HraController\Factors\FactorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\hra\factorController;
use App\Http\Controllers\hra\questionController;
use App\Http\Controllers\hra\templateController;
use App\Http\Controllers\hra\factorPriorityController;
use App\Http\Controllers\hra\questionPriorityController;
use App\Http\Controllers\corporate\addCorporateUsers;
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
use App\Http\Controllers\address\AddressController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\form_wizard\Icons as FormWizardIcons;
use App\Http\Controllers\form_validation\Validation;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\tables\DatatableBasic;
use App\Http\Controllers\tables\DatatableAdvanced;
use App\Http\Controllers\tables\DatatableExtensions;
use App\Http\Controllers\charts\ApexCharts;
use App\Http\Controllers\charts\ChartJs;
use App\Http\Controllers\maps\Leaflet;
use App\Http\Controllers\corporate\CorporateController;
use App\Http\Controllers\corporate\EditCorporateController;
use App\Http\Controllers\corporate\ComponentController;
use App\Http\Controllers\corporate\CertificationController;
use App\Http\Controllers\injury\InjuryController;
use App\Http\Controllers\doctor_qualification\DoctorController;
use App\Http\Controllers\corporate\LocationController;
use App\Http\Middleware\Authcheck;
use App\Http\Controllers\content_delivery\getContent;
use App\Http\Controllers\corporate\TestGroups;
use App\Http\Controllers\corporate\link2hra;
use App\Http\Controllers\corporate\Tests;
use App\Http\Controllers\drug\drug_ingredients\DrugIngredientController;
use App\Http\Controllers\drug\drug_types\DrugTypeController;
use App\Http\Controllers\medicalcondition\MedicalConditionController;
use App\Http\Controllers\forms\CorporateFormController;
use App\Http\Controllers\corporate\IncidentTypeController;

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
    Route::get('/', function () {
        return redirect()->route('dashboard-analytics');
    });
    Route::get('/auth/logout', [LoginBasic::class, 'logout'])->name('auth-logout');
    Route::get('/dashboard/analytics', [Analytics::class, 'index'])->name('dashboard-analytics');
    // ------------------------------------------------------------------------------------------------------------------------------
    Route::middleware(['throttle:hra'])->group(function () {
        // Reset Passwords
        Route::get('/auth/reset-password/{token}', [LoginBasic::class, 'resetPasswordView'])->name('reset-password-view');
        Route::post('/auth/request-token', [LoginBasic::class, 'requestPasswordReset'])->name('request-password');
        Route::post('/auth/reset-password', [LoginBasic::class, 'resetPassword'])->name('reset-password');
        // 2fa
        Route::get('/toggle/2fa/{isEnable}', [LoginBasic::class, 'toggle2FA'])->name('toggle-2fa');
        Route::get('/getUserDetails', [LoginBasic::class, 'getUserDetails'])->name('get-2fa');
        // Content_delivery
        Route::get('/getContent/{type}/{path?}', [getContent::class, 'handleRequest'])
            ->where('path', '.*')
            ->name('getContent');
        // HRA Factors
        Route::get('/hra/factors', [factorController::class, 'factor'])->name('hra-factors');
        Route::get('/hra/fetch-factors', [factorController::class, 'getAllFactors'])->name('hra.fetchFactors');
        Route::post('/hra/add-new-factor', [factorController::class, 'addNewFactor'])->name('hra.addNewFactor');
        Route::put('/hra/edit-factor/{id}', [factorController::class, 'editFactor'])->name('hra.editFactor');
        Route::delete('/hra/delete-factor/{id}', [factorController::class, 'deleteFactor'])->name('hra.deleteFactor');
        // HRA Templates
        Route::get('/hra/templates', [templateController::class, 'index'])->name('hra-templates');
        Route::get('/hra/fetch-templates', [templateController::class, 'getAllTemplates'])->name('hra.fetchTemplates');
        Route::post('/hra/add-new-template', [templateController::class, 'addNewTemplate'])->name('hra.addNewTemplate');
        Route::put('/hra/edit-template/{id}', [templateController::class, 'editTemplate'])->name('hra.editTemplate');
        Route::delete('/hra/delete-template/{id}', [templateController::class, 'deleteTemplate'])->name('hra.deleteTemplate');
        // HRA Questions
        Route::get('/hra/questions', [questionController::class, 'question'])->name('hra-questions');
        Route::get('/hra/get-all-questions', [questionController::class, 'getAllQuestion'])->name('hra-get-all-questions');
        Route::get('/hra/add-questions', [questionController::class, 'index'])->name('hra-add-questions');
        Route::post('/hra/add-question', [questionController::class, 'addQuestion'])->name('hra-add-question');
        Route::get('/hra/master-test-names', [questionController::class, 'getAllMasterTest'])->name('hra.fetchMasterTestNames');
        Route::get('/hra/get-all-master-tests', [questionController::class, 'getAllMasterTests'])->name('hra.fetchMasterTestNames');
        Route::delete('/hra/delete-question/{id}', [questionController::class, 'deleteQuestion'])->name('hra.deleteQuestion');
        Route::post('/hra/edit-question/{id}', [questionController::class, 'editQuestion'])->name('hra-edit-question');
        // HRA Question Priority
        Route::get('/hra/templates/{template_id}/factor-priority/{factor_id}/question-priority/', [questionPriorityController::class, 'questionFactorPriority'])->name('hra-templates');
        Route::get('/hra/templates/{template_id}/factor-priority/view-question-priority/', [questionPriorityController::class, 'viewQuestionFactorPriority'])->name('hra-templates');
        Route::get('/hra/templates/{template_id}/getAllQuestionFactorPriority', [questionPriorityController::class, 'getAllQuestionFactorPriority'])->name('hra-templates');
        Route::put('/hra/templates/set-question-factor-priority/', [questionPriorityController::class, 'setQuestionFactorPriority'])->name('hra.hra-setQuestionFactorPriority');
        Route::get('/hra/templates/trigger-questions/{templateId}/{factorId}/{questionId}', [questionPriorityController::class, 'getExistingTriggerQuestionsView'])->name('hra-templates');
        Route::get('/hra/templates/get-trigger-questions/{templateId}/{factorId}/{questionId}', [questionPriorityController::class, 'getExistingTriggerQuestions'])->name('hra-templates');
        Route::put('/hra/templates/set-trigger-questions/{templateId}/{factorId}/{questionId}', [questionPriorityController::class, 'setTriggerQuestions'])->name('hra-templates');
        Route::post('/hra/templates/publishTemplate', [templateController::class, 'publishTemplate'])->name('hra-templates');
        // HRA Factor-priority
        Route::get('/hra/templates/factor-priority/', [factorPriorityController::class, 'redirectToTemplatePage'])->name('hra-factor-priority-redirect');
        Route::get('/hra/templates/factor-priority/{template_id}', [factorPriorityController::class, 'index'])->name('hra-templates');
        Route::put('/hra/templates/set-factor-priority/', [factorPriorityController::class, 'setFactorPriority'])->name('hra.hra-setFactorPriority');
        Route::get('/hra/templates/{template_id}/factor-priority/{factor_id}/get-question-factor-priority/', [questionPriorityController::class, 'getQuestionFactorPriority'])->name('hra.hra-getQuestionFactorPriority');
        // Corporate
        Route::get('/employees/corporate/add-corporate-users', [addCorporateUsers::class, 'displayImportView'])->name('corporate-add-corporate-users');
        Route::post('/corporate/upload/add-corporate-users', [addCorporateUsers::class, 'import'])->name('add-corporate');
        Route::get('/corporate/view-uploaded-users/{corporate_id}/{location_id}/{file_name}', [addCorporateUsers::class, 'viewExcelData'])->name('corporate-add-corporate-users');
        Route::get('/corporate/getMasterUserDetails', [addCorporateUsers::class, 'getMasterUserDetailsCount']);
        Route::delete('/corporate/delete-uploaded-users/', [addCorporateUsers::class, 'deleteExcelData'])->name('delete-corporate-excel');
        Route::post('/corporate/send-uploaded-users/', [addCorporateUsers::class, 'sendExcelData'])->name('add-corporate-excel');
        Route::get('/corporate/getUploadedExcelStatus', [addCorporateUsers::class, 'getUploadedExcelStatus']);
        Route::get('/corporate/getUploadedExcelFileContent/{id}', [addCorporateUsers::class, 'getUploadedExcelFileContent']);
        Route::get('/corporate/getAllCorporates', [addCorporateUsers::class, 'getAllCorporates']);
        Route::get('/corporate/getAllLocations/{corporate_id}', [addCorporateUsers::class, 'getAllLocations']);

        Route::get('/corporate/search-user', [addCorporateUsers::class, 'displaySearchUsers'])->name('corporate-search-user');
        Route::get('/getEmployeeData/{keyword}', [addCorporateUsers::class, 'searchEmployegetDataByKeyword']);

    });
    // Test Groups
    Route::get('/test-group/tests', [Tests::class, 'showMasterTestsPage'])->name('group-test');
    Route::get('/test-group/add-tests', [Tests::class, 'addMasterTestsPage'])->name('group-add-test');
    Route::post('/test-group/add-tests', [Tests::class, 'addMastertests'])->name('group-add-test');
    Route::get('/test-group/edit-tests/{test_id}', [Tests::class, 'editMasterTestsPage'])->name('group-test');
    Route::post('/test-group/edit-tests/{test_id}', [Tests::class, 'editMastertests'])->name('group-test');
    Route::get('/test-group/all-groups', [TestGroups::class, 'testGroupIndexPage'])->name('group-groups');
    Route::get('/test-group/getAllGroups', [TestGroups::class, 'getAllGroups']);
    Route::post('/test-group/addGroup', [TestGroups::class, 'addNewGroup']);
    Route::post('/test-group/updateGroup', [TestGroups::class, 'updateGroup']);
    Route::post('/test-group/deleteGroup', [TestGroups::class, 'deleteGroup']);
    Route::get('/test-group/getAllSubGroups', [TestGroups::class, 'getAllSubGroups']);
    Route::get('/test-group/getSubGroupOfGroup/{groupId}', [TestGroups::class, 'getSubGroupOfGroup']);
    Route::post('/test-group/addSubGroup', [TestGroups::class, 'addNewSubGroup']);
    Route::post('/test-group/updateSubGroup', [TestGroups::class, 'updateSubGroup']);
    Route::post('/test-group/deleteSubGroup', [TestGroups::class, 'deleteSubGroup']);
    Route::get('/test-group/getAllSubSubGroups', [TestGroups::class, 'getAllSubSubGroups']);
    Route::post('/test-group/addSubSubGroup', [TestGroups::class, 'addNewSubSUbGroup']);
    Route::post('/test-group/updateSubSubGroup', [TestGroups::class, 'updateSubSubGroup']);
    Route::post('/test-group/deleteSubSubGroup', [TestGroups::class, 'deleteSubSubGroup']);
    // Link 2 HRA
    Route::get('/corporate/link-to-hra', [link2hra::class, 'link2hraIndexPage'])->name("corporate-link-to-hra");
    Route::post('/corporate/linkCorporateToHra', [link2hra::class, 'linkCorporate2Hra']);
    Route::post('/corporate/updateCorporateHraLink', [link2hra::class, 'updateCorporateHraLink']);
    Route::get('/corporate/getCorporateOfHraTemplate', [link2hra::class, 'getCorporateOfHraTemplate']);

    Route::get('/corporate/incident-types', [IncidentTypeController::class, 'displayIncidentTypePage'])->name('corporate-incident-types');
    Route::get('/corporate/getAllIncidentTypes', [IncidentTypeController::class, 'getAllIncidentTypes']);
    Route::post('/corporate/addIncidentType', [IncidentTypeController::class, 'addIncidentType']);
    Route::post('/corporate/editIncidentType/{id}', [IncidentTypeController::class, 'editIncidentType']);
    Route::delete('/corporate/deleteIncidentType/{id}', [IncidentTypeController::class, 'deleteIncidentType']);

    Route::get('/corporate/assign-incident-types/{corporate_id}', [IncidentTypeController::class, 'displayAssignIncidentTypePage']);
    Route::get('/corporate/getAllAssignedIncidentTypes/{corporate_id}', [IncidentTypeController::class, 'getAllAssignedIncidentTypes']);
    Route::post('/corporate/assignIncidentTypes/{corporate_id}', [IncidentTypeController::class, 'assignIncidentTypes']);
    // ------------------------------------------------------------------------------------------------------------------------------
    Route::group(['prefix' => 'location'], function () {
        Route::get('/country', [AddressController::class, 'countryindex'])->name('location.country');
        Route::post('addcountry', [AddressController::class, 'countrycreate'])->name('location.save_country');
        Route::get('state', [AddressController::class, 'stateindex'])->name('location.state');
        Route::post('addstate', [AddressController::class, 'stateadd'])->name('stateadd');
        Route::get('city', [AddressController::class, 'cityindex'])->name('location.city');
        Route::post('findcountry', [AddressController::class, 'countryfind'])->name('findcountry');
        Route::post('addcity', [AddressController::class, 'cityadd'])->name('cityadd');
        Route::get('area', [AddressController::class, 'areaindex'])->name('location.area');
        Route::post('findstacountry', [AddressController::class, 'countrystate_find'])->name('findstatecountry');
        Route::post('areaadd', [AddressController::class, 'areaadd'])->name('locationareaadd');
        Route::get('pincode', [AddressController::class, 'pincodeindex'])->name('location.pincode');
        Route::post('findstaconcity', [AddressController::class, 'countrystatecity_find'])->name('findstatecountrycity');
        Route::post('pincodeaadd', [AddressController::class, 'pincodeadd'])->name('location_pincodeadd');
        Route::post('areastaconcity', [AddressController::class, 'countrystatecityarea_find'])->name('areastatecountrycity');
        Route::get('findpincode', [AddressController::class, 'findpincode'])->name('findpincode');
    });
    //corporate
    Route::get('/corporate/add-corporate', [FormWizardNumbered::class, 'index'])->name('corporate-add-corporate');
    Route::post('/addcorporate.corp', [FormWizardNumbered::class, 'addcorporate'])->name('addcorporate.corp');
    Route::post('/findlocation', [AddressController::class, 'findlocation'])->name('findlocation');
    Route::POST('/area_find', [AddressController::class, 'area_find'])->name('area_find');
    Route::get('/corporate/corporate-list', [FormWizardNumbered::class, 'corporateIndex'])->name('corporate-list');
    //corporate locations
    Route::prefix('corporate_locations')->group(function () {
        Route::get('/corporate/{corporate_id}/{corporate_name}/locations', [LocationController::class, 'corporatelocation'])
            ->name('corporate_location');
        Route::post('/corporate/locations', [LocationController::class, 'addCorporate'])
            ->name('corporate.add_location');
    });
    Route::prefix('/corporate')->group(function () {
        Route::get('editcorporate/{id}', [CorporateController::class, 'editcorporate'])->name('corporate.edit');
        Route::get('/edit-address/{id}/{corporate_id}', [EditCorporateController::class, 'editAddress'])->name('corporate.editAddress');
        Route::post('/corporate/updateaddress/{id}', [EditCorporateController::class, 'updateAddress'])->name('corporate.updateaddress');
        Route::put('corporate_update/{id}', [CorporateController::class, 'updatecorporate'])->name('corporate.upupdate');
        Route::get('/edit-employeetypes/{id}/{corporate_id}', [EditCorporateController::class, 'editEmployeeTypes'])->name('corporate.editEmployeeTypes');
        Route::post('/updateEmployeeTypes', [EditCorporateController::class, 'updateEmployeeTypes'])->name('corporate.updateemptype');
        Route::get('/edit-components/{id}/{corporate_id}', [ComponentController::class, 'editComponents'])->name('corporate.editComponents');
        //Added By Bhava for Forms
        Route::get('/assign-forms/{corporate_id}/{location_id}', [ComponentController::class, 'assignForms'])->name('corporate.assignForms');
        Route::get('/corporate/module4-submodules', [ComponentController::class, 'getModule4Submodules'])
        ->name('corporate.module4.submodules');
        Route::post('/corporate/module4-assignSubmodules', [ComponentController::class, 'assignFormForLocation'])
        ->name('corporate.module4.assignSubmodules');
        Route::get('/getassign-forms/{corporate_id}/{location_id}', [ComponentController::class, 'getassignedFormForLocation'])->name('corporate.getAssignedForms');
        Route::post('/updatecomponents', [ComponentController::class, 'updateComponents'])->name('corporate_updatecomponents');
        //Route::get('/edit-financials/{id}', [EditCorporateController::class, 'editFinancials'])->name('corporate.editFinancials');
        Route::get('/edit-admin-users/{id}/{corporate_id}', [EditCorporateController::class, 'editAdminUsers'])->name('corporate.editAdminUsers');
        Route::post('/update-admin-users/{id}', [EditCorporateController::class, 'updateAdminUsers'])->name('corporate.adminuser_update');
    });
    Route::post('employeetype/add', [EditCorporateController::class, 'addemployeetype'])->name('employeetype_add');
    //components
    Route::get('/components/modules', [ComponentController::class, 'modules'])->name('corporate-component-module');
    Route::post('/components/add-module', [ComponentController::class, 'addModule'])->name('components_module.store');
    Route::get('/components/edit-module/{id}', [ComponentController::class, 'editModule'])->name('corporate-component-module');
    Route::put('/components/update-module/{id}', [ComponentController::class, 'updateModule'])->name('update-module');
    Route::get('/components/sub-modules', [ComponentController::class, 'submodules'])->name('corporate-component-sub-module');
    Route::post('/components/add-sub-module', [ComponentController::class, 'addSubModule'])->name('components_submodule_store');
    Route::get('/components/edit-sub-module/{id}', [ComponentController::class, 'editSubModule'])->name('corporate-component-sub-module');
    Route::put('/components/update-sub-module/{id}', [ComponentController::class, 'updateSubModule'])->name('submodule.update');
    //certificate
    Route::group(['prefix' => 'certificate'], function () {
        Route::get('/add-certification', [CertificationController::class, 'index'])->name('corporate-add-certification');
        Route::post('create', [CertificationController::class, 'store'])->name('certification.store');
        Route::get('edit/{id}', [CertificationController::class, 'edit'])->name('certificate.edit');
        Route::put('update/{id}', [CertificationController::class, 'update'])->name('certification.update');
        Route::get('show/{id}', [CertificationController::class, 'show'])->name('certificate.show');
    });
    //injury
    Route::get('/outpatient/injury', [InjuryController::class, 'index'])->name('outpatient-injury');
    Route::post('addinjury', [InjuryController::class, 'create'])->name('injuryadd');
    Route::post('update_injury', [InjuryController::class, 'update'])->name('injuryupdate');
    Route::delete('/delete/{id}', [InjuryController::class, 'destroy'])->name('injurydelete');
    // doctor_qualifications
    Route::group(['prefix' => 'others/doctor-qualifications'], function () {
        Route::get('/', [DoctorController::class, 'index'])->name('others-doctor-qualification');
        Route::post('store', [DoctorController::class, 'store'])->name('doctor.add');
        Route::post('update', [DoctorController::class, 'update'])->name('doctor_update');
    });
    //Drug Ingredients
    Route::get('/drugs/drug-ingredients', [DrugIngredientController::class, 'ingredients'])->name('drugs-ingredients');
    Route::get('/drugs/fetch-ingredients', [DrugIngredientController::class, 'getAllIngredients'])->name('drug.fetchIngredients');
    Route::post('/drugs/add-ingredients', [DrugIngredientController::class, 'addIngredients'])->name('drug.addIngredients');
    Route::put('/drugs/edit-ingredients/{id}', [DrugIngredientController::class, 'editIngredients'])->name('drug.editIngredients');
    Route::delete('/drugs/delete-ingredients/{id}', [DrugIngredientController::class, 'deleteIngredients'])->name('drug.deleteIngredients');
    //Drug Types
    Route::get('/drugs/drug-types', [DrugTypeController::class, 'drugtypes'])->name('drugs-drug-type');
    Route::get('/drugs/fetch-drugtype', [DrugTypeController::class, 'getAllDrugtypes'])->name('drug.fetchDrugTypes');
    Route::post('/drugs/add-drugtype', [DrugTypeController::class, 'addDrugtype'])->name('drug.addDrugtype');
    Route::put('/drugs/edit-drugtype/{id}', [DrugTypeController::class, 'editDrugtype'])->name('drug.editDrugtype');
    Route::delete('/drugs/delete-drugtype/{id}', [DrugTypeController::class, 'deleteDrugtype'])->name('drug.deleteDrugtype');

    //Forms
    Route::get('/forms/list-forms', [CorporateFormController::class, 'listCorporateForms'])->name('list-forms');
    Route::get('/forms/fetch-forms', [CorporateFormController::class, 'getAllForms'])->name('list-forms');
    Route::get('/forms/get-states', [CorporateFormController::class, 'getAllStates'])->name('list-forms');
    Route::post('/forms/add-forms', [CorporateFormController::class, 'addNewForm'])->name('list-forms');
    Route::put('/forms/edit-forms/{id}', [CorporateFormController::class, 'editForm'])->name('list-forms');
    Route::delete('/forms/form-delete/{id}', [CorporateFormController::class, 'deleteForms'])->name('list-forms');


    // Medical Condition
    Route::get('/others/medical-condition/', [MedicalConditionController::class, 'medicalcondition'])->name('others-medical-qualification');
    Route::get('/others/medical-condition/fetch-medicalcondition', [MedicalConditionController::class, 'getAllMedicalCondition'])->name('medicalcondition.fetchMedicalCondition');
    Route::post('/others/medical-condition/add-medicalcondition', [MedicalConditionController::class, 'addMedicalCondition'])->name('medicalcondition.addMedicalCondition');
    Route::put('/others/medical-condition/edit-medicalcondition/{id}', [MedicalConditionController::class, 'editMedicalCondition'])->name('medicalcondition.editMedicalCondition');
    Route::delete('/others/medical-condition/delete-medicalcondition/{id}', [MedicalConditionController::class, 'deleteMedicalCondition'])->name('medicalcondition.deleteMedicalCondition');
    // Unused Routes
    Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');
    // locale
    Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
    // layout
    Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
    Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
    Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
    Route::get('/layouts/navbar-full', [NavbarFull::class, 'index'])->name('layouts-navbar-full');
    Route::get('/layouts/navbar-full-sidebar', [NavbarFullSidebar::class, 'index'])->name('layouts-navbar-full-sidebar');
    // Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('dashboard-analytics');
    // Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('dashboard-analytics');
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
    // authentication
    // Route::get('/auth/login-cover', [LoginCover::class, 'index'])->name('auth-login-cover');
    // Route::get('/auth/register', [RegisterBasic::class, 'index'])->name('auth-register-basic');
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
    // Route::get('/form/wizard-numbered', [FormWizardNumbered::class, 'index'])->name('form-wizard-numbered');
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
});

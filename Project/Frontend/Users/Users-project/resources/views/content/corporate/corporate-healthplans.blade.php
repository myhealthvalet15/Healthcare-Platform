@extends('layouts/layoutMaster')
@section('title', 'Corporate Health Plans')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection
@section('page-script')
@vite([
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/forms-selects.js',
'resources/assets/js/forms-typeahead.js'
])
@endsection
@section('content')
<style>
    .add-new-healthplan-strip {
        background-color: #7367F0;
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .add-new-healthplan-heading {
        color: white;
    }

    .custom-violet {
        background-color: #7367F0;
        color: white;
        border: none;
    }

    .existing-helthplan-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 200px;
        text-align: center;
        margin-top: 75px;
    }

    .spinner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sk-bounce {
        display: flex;
        justify-content: space-between;
        width: 50px;
    }

    .sk-bounce-dot {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        background-color: #007bff;
        border-radius: 50%;
        animation: sk-bounce 1.4s infinite ease-in-out both;
    }
</style>
<style>
    /* Test list and grouping styles */
    .test-list-clickable {
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .test-list-clickable:hover {
        background-color: #e9ecef;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .test-list-clickable i {
        margin-right: 5px;
        color: #696cff;
    }

    .test-group-title {
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 5px;
        color: #9D94F4;
    }

    .test-subgroup-title {
        font-weight: 600;
        margin-top: 5px;
        margin-bottom: 3px;
        margin-left: 15px;
        color: #78DBC7;
    }

    .test-subsubgroup-title {
        font-weight: normal;
        font-style: italic;
        margin-top: 3px;
        margin-bottom: 2px;
        margin-left: 30px;
        color: #DCDBE0;
    }

    .test-item {
        margin-left: 15px;
        color: #000000;
    }

    .subgroup-test-item,
    .subsubgroup-test-item {
        margin-left: 30px;
        color: #000000;
    }

    .subsubgroup-test-item {
        margin-left: 45px;
    }

    /* Modal and header styles */
    .modal-header-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 15px;
    }

    .employee-info,
    .date-info {
        font-weight: 500;
    }

    .date-info {
        text-align: right;
    }

    .status-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Icon styles */
    .icon-base {
        font-size: 1.2rem;
        margin: 0 4px;
        vertical-align: middle;
    }

    /* Badge styles */
    .badge.bg-label-success {
        background-color: rgba(40, 199, 111, 0.12) !important;
        color: #28c76f !important;
    }

    .badge.bg-label-danger {
        background-color: rgba(234, 84, 85, 0.12) !important;
        color: #ea5455 !important;
    }

    /* Table styles */
    .table-custom-striped thead tr {
        background-color: #e0dee8;
    }

    .table-custom-striped tbody tr:nth-child(odd) {
        background-color: #fbfbfb;
    }

    .table-custom-striped tbody tr:nth-child(even) {
        background-color: #f1f2f3;
    }

    /* Body styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #f4f4f4;
    }

    /* Prescription styles */
    .prescription-container {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .doctor-header {
        background: #6b1bc7;
        color: #fff;
        font-weight: bold;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .prescription-id {
        font-weight: bold;
        color: #fcd34d;
    }

    .patient-info {
        background: #d4d4d4;
        padding: 10px 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .icons {
        display: flex;
        gap: 10px;
    }

    .icons i {
        cursor: pointer;
        color: #333;
    }

    /* Table styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f3e8ff;
        color: #333;
    }

    th,
    td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    td:first-child,
    th:first-child {
        text-align: left;
    }

    .drug-name i {
        margin-left: 5px;
        color: #555;
    }
    #healthPlanTable td {
        text-align: left !important;
    }

    #healthPlanTable th {
        text-align: left !important;
    }

    #healthPlanTable td:nth-child(2),
    #healthPlanTable td:nth-child(3),
    #healthPlanTable td:nth-child(4),
    #healthPlanTable td:nth-child(5) {
        text-align: left !important;
    }

    .table td.text-start {
        text-align: left !important;
    }

    #healthPlanTable td {
        padding-left: 12px;
        vertical-align: top;
    }
</style>
<!-- TODO: To validate the input fields while adding new healthplans -->
<div class="modal fade" id="editHealthPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card-body">
                            <form id="editHealthPlanForm" class="row g-6 existingHealthPlanClass"
                                name="editHealthPlanForm">
                                <div class="col-12">
                                    <div class="add-new-healthplan-strip">
                                        <h6 class="m-0 add-new-healthplan-heading">Save
                                            Health Plan
                                        </h6>
                                    </div>
                                </div>
                                <input type="hidden" id="healthplan_id_edit" name="healthplan_id_edit" value>
                                <div class="col-md-4">
                                    <label class="form-label" for="formValidationHealthPlanTitle_edit">Health
                                        Plan
                                        Title</label>
                                    <input type="text" id="formValidationHealthPlanTitle_edit" class="form-control"
                                        placeholder="Title" name="formValidationHealthPlanTitle_edit" />
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="formValidationHealthPlanDescription_edit">Health
                                        Plan
                                        Description</label>
                                    <textarea id="formValidationHealthPlanDescription_edit" class="form-control"
                                        placeholder="Enter a brief description"
                                        name="formValidationHealthPlanDescription_edit"></textarea>
                                </div>
                                <div class="col-md-8">
                                    <label for="select2Success-formValidationSelectMasterTest_edit"
                                        class="form-label">Tests</label>
                                    <div class="select2-primary">
                                        <select id="select2Success-formValidationSelectMasterTest_edit"
                                            name="masterTests"
                                            class="select2 form-select select2Success-formValidationSelectMasterTest_edit"
                                            multiple>
                                            <option selected id="header-formValidationSelectMasterTest_edit" disabled>
                                                Select
                                                1 or more tests</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="select2Success-formValidationSelectCertificate_edit"
                                        class="form-label">Certificates</label>
                                    <div class="select2-primary">
                                        <select id="select2Success-formValidationSelectCertificate_edit"
                                            name="certificates"
                                            class="select2 form-select select2Success-formValidationSelectCertificate_edit"
                                            multiple>
                                            <option selected id="header-formValidationSelectCertificate-edit" disabled>
                                                Select
                                                Certificates</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="select2Success-formValidationSelectForms_edit" class="form-label">Select
                                        Forms</label>
                                    <div class="select2-primary">
                                        <select id="select2Success-formValidationSelectForms_edit" name="forms"
                                            class="select2 form-select select2Success-formValidationSelectForms_edit"
                                            multiple>
                                            <option selected id="header-formValidationSelectForms-edit" disabled>Forms
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gender_edit" class="form-label">gender</label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input name="gender_edit" class="form-check-input" type="checkbox" value="male"
                                            id="male-edit">
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input name="gender_edit" class="form-check-input" type="checkbox"
                                            value="female" id="female-edit">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input name="gender_edit" class="form-check-input" type="checkbox"
                                            value="others" id="others-edit">
                                        <label class="form-check-label" for="others">Others</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div
                                        class="form-check form-check-inline form-check-primary pre-employment-check-box">
                                        <input class="form-check-input pre-employment-check-box" type="checkbox" value
                                            id="pre-employment-check-box-edit">
                                        <label class="form-check-label pre-employment-check-box"
                                            for="pre-employment-check-box">Pre
                                            Employment</label>
                                    </div>
                                    <div class="form-check form-check-inline form-check-success active-check-box">
                                        <input class="form-check-input active-check-box" type="checkbox" value
                                            id="active-check-box-edit" checked>
                                        <label class="form-check-label active-check-box"
                                            for="active-check-box">Active</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="button" name="submitButton" class="btn btn-primary"
                                        onclick="sendUpdatedHealthPlanData()">
                                        <i class="fa-regular fa-pen-to-square"></i>&nbsp;Edit
                                        Health Plan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addNewHealthPlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card-body">
                            <form id="addNewHealthPlanForm" class="row g-6 existingHealthPlanClass"
                                name="addNewHealthPlanForm">
                                <input type="hidden" id="corporate_id" name="corporate_id"
                                    value="{{ session('corporate_id') }}">
                                <div class="col-12">
                                    <div class="add-new-healthplan-strip">
                                        <h6 class="m-0 add-new-healthplan-heading">1.
                                            Enter Health Plan Details Here
                                        </h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="formValidationHealthPlanTitle">Health
                                        Plan
                                        Title</label>
                                    <input type="text" id="formValidationHealthPlanTitle" class="form-control"
                                        placeholder="Title" name="formValidationHealthPlanTitle" />
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label"
                                        for="formValidationHealthPlanDescription">Description</label>
                                    <textarea id="formValidationHealthPlanDescription" class="form-control"
                                        placeholder="Enter a brief description"
                                        name="formValidationHealthPlanDescription"></textarea>
                                </div>
                                <div class="col-md-8">
                                    <label for="select2Success-formValidationSelectMasterTest"
                                        class="form-label">Tests</label>
                                    <div class="select2-primary">
                                        <select id="select2Success-formValidationSelectMasterTest" name="masterTests"
                                            class="select2 form-select select2Success-formValidationSelectMasterTest"
                                            multiple>
                                            <option selected id="header-formValidationSelectMasterTest" disabled>Select
                                                1 or more tests</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="select2Success-formValidationSelectCertificate"
                                        class="form-label">Certificates</label>
                                    <div class="select2-primary">
                                        <select id="select2Success-formValidationSelectCertificate" name="certificates"
                                            class="select2 form-select select2Success-formValidationSelectCertificate"
                                            multiple>
                                            <option selected id="header-formValidationSelectCertificate" disabled>Select
                                                Certificates</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="select2Success-formValidationSelectForms" class="form-label">Select
                                        Forms</label>
                                    <div class="select2-primary">
                                        <select id="select2Success-formValidationSelectForms" name="forms"
                                            class="select2 form-select select2Success-formValidationSelectForms"
                                            multiple>
                                            <option selected id="header-formValidationSelectForms" disabled>Forms
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label">Gender</label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input name="gender" class="form-check-input" type="checkbox" value="male"
                                            id="male">
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input name="gender" class="form-check-input" type="checkbox" value="female"
                                            id="female">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input name="gender" class="form-check-input" type="checkbox" value="others"
                                            id="others">
                                        <label class="form-check-label" for="others">Others</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div
                                        class="form-check form-check-inline form-check-primary pre-employment-check-box">
                                        <input class="form-check-input pre-employment-check-box" type="checkbox" value
                                            id="pre-employment-check-box">
                                        <label class="form-check-label pre-employment-check-box"
                                            for="pre-employment-check-box">Pre
                                            Employment</label>
                                    </div>
                                    <div class="form-check form-check-inline form-check-success active-check-box">
                                        <input class="form-check-input active-check-box" type="checkbox" value
                                            id="active-check-box" checked>
                                        <label class="form-check-label active-check-box"
                                            for="active-check-box">Active</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="submitButton" class="btn btn-primary"
                                        onclick="sendNewHealthPlanData(document.getElementById('corporate_id').value)">
                                        <i class="fa-solid fa-plus" id="submit"></i>&nbsp;Add Health
                                        Plan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="m-0"></h5>
    <div id="healthPlanHeader"></div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row mb-4" id="filtersSection">
            <div class="row w-100 align-items-end">
                <div class="col-md-3">
                    <label for="searchInput" class="form-label">Health Plan
                        Name</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search health plan name...">
                </div>
                <div class="col-md-7">
                    <label for="filterTestSelect" class="form-label">Tests</label>
                    <div class="select2-primary">
                        <select id="filterTestSelect" class="select2 form-select" multiple>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary waves-effect waves-light" id="applyFiltersBtn">Apply</button>
                        <button class="btn btn-outline-secondary waves-effect waves-light"
                            onclick="clearFilters()">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="card">
    <h5 class="card-header custom-violet">Available Healthplans</h5>
    <div class="existing-helthplan-spinner" id="existing-helthplan-spinner" style="display: block;">
        <div class="spinner-container">
            <div class="sk-bounce sk-primary">
                <div class="sk-bounce-dot"></div>
                <div class="sk-bounce-dot"></div>
            </div>
            <label for>Loading Existing Health Plans...</label>
        </div>
    </div>
    <div class="table-responsive text-nowrap" style="display: none;" id="existing-helthplan-table">
        <table class="table" id="healthPlanTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Plan Name / Test List</th>
                    <th>Certificate</th>
                    <th>Forms</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
        </table>
    </div>
</div>
<script src="/lib/js/page-scripts/common.js"></script>
<script>
    var healthPlanPermission = 1;
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.querySelector('#healthPlanHeader');
        if (container) {
            container.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="m-0"></h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewHealthPlanModal">
                            Add New Health Plan
                        </button>
                    </div>
                `;
        }
    });
</script>
<script>
    let storedHealthPlans = [], originalHealthPlans = [], filteredHealthPlans = [];
    function sendNewHealthPlanData(corporate_id) {
        if (!corporate_id) {
            showToast('error', 'Error', 'Invalid corporate ID.');
            return;
        }
        const healthPlanTitle = document.getElementById('formValidationHealthPlanTitle').value;
        const healthPlanDescription = document.getElementById('formValidationHealthPlanDescription').value;
        const masterTestIds = Array.from(document.getElementById('select2Success-formValidationSelectMasterTest').selectedOptions)
            .map(option => Number(option.value))
            .filter(value => !isNaN(value));
        const certificateIds = Array.from(document.getElementById('select2Success-formValidationSelectCertificate').selectedOptions)
            .map(option => Number(option.value))
            .filter(value => !isNaN(value));
        const formIds = Array.from(document.getElementById('select2Success-formValidationSelectForms').selectedOptions)
            .map(option => Number(option.value))
            .filter(value => !isNaN(value));
        const gender = Array.from(document.querySelectorAll('input[name="gender"]:checked'))
            .map(input => input.value);
        if (!healthPlanTitle || gender.length === 0) {
            showToast('error', 'Error', 'Please fill in all required fields.');
            return;
        }
        const isPreEmployment = document.getElementById('pre-employment-check-box').checked ? 1 : 0;
        const activeStatus = document.getElementById('active-check-box').checked ? 1 : 0;
        if (!healthPlanTitle || !gender) {
            showToast('error', 'Error', 'Please fill in all required fields.');
            return;
        }
        const payload = {
            corporate_id: document.getElementById('corporate_id').value,
            healthplan_title: healthPlanTitle,
            healthplan_description: healthPlanDescription,
            master_test_id: masterTestIds,
            certificate_id: certificateIds,
            forms: formIds,
            isPreEmployement: isPreEmployment,
            gender: gender,
            active_status: activeStatus
        };
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to add this health plan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, add it!'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    apiRequest({
                        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/addNewHealthPlan',
                        method: 'POST',
                        data: payload,
                        onSuccess: (res) => {
                            if (res.result) {
                                showToast('success', 'Success', 'Health plan added successfully!');
                                document.getElementById('addNewHealthPlanForm').reset();
                                $('#select2Success-formValidationSelectMasterTest').val(null).trigger('change');
                                $('#select2Success-formValidationSelectCertificate').val(null).trigger('change');
                                $('#select2Success-formValidationSelectForms').val(null).trigger('change');
                                $('#addNewHealthPlanModal').modal('hide');
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                                apiRequest({
                                    url: `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllHealthplans`,
                                    method: 'GET',
                                    onSuccess: (refreshResponse) => {
                                        if (refreshResponse.result && refreshResponse.message) {
                                            populateHealthPlansWithFilter(refreshResponse.message);
                                        }
                                    },
                                    onError: errorMessage => {
                                        showToast('error', 'Error', errorMessage);
                                    }
                                });
                            } else {
                                showToast('error', 'Error', res.message || 'Failed to add health plan.');
                            }
                        },
                        onError: (error) => {
                            showToast('error', 'Error', `Failed to add health plan: ${error}`);
                        }
                    });
                } catch (error) {
                    showToast('error', 'Error', `Unexpected error: ${error.message}`);
                }
            }
        });
    }
    function populateEditModal(plan) {
        document.getElementById('formValidationHealthPlanTitle_edit').value = plan.healthplan_title;
        document.getElementById('formValidationHealthPlanDescription_edit').value = plan.healthplan_description;
        healthPlanId = plan.corporate_healthplan_id;
        document.getElementById('healthplan_id_edit').value = plan.corporate_healthplan_id;
        const genderInputs = document.querySelectorAll('input[name="gender_edit"]');
        genderInputs.forEach(input => {
            if (plan.gender && Array.isArray(JSON.parse(plan.gender))) {
                const genderArray = JSON.parse(plan.gender);
                input.checked = genderArray.includes(input.value);
            } else {
                input.checked = false;
            }
        });
        document.getElementById('active-check-box-edit').checked = plan.active_status === true;
        document.getElementById('pre-employment-check-box-edit').checked = plan.isPreEmployement === true;
        const preselectedValuesForMasterTestId = Array.isArray(plan.master_test_id) ? plan.master_test_id.map(String) : JSON.parse(plan.master_test_id).map(String);
        $('#select2Success-formValidationSelectMasterTest_edit').val(preselectedValuesForMasterTestId).trigger('change');
        const preselectedValuesForCertificateId = Array.isArray(plan.certificate_id) ? plan.certificate_id.map(String) : JSON.parse(plan.certificate_id).map(String);
        $('#select2Success-formValidationSelectCertificate_edit').val(preselectedValuesForCertificateId).trigger('change');
        const preselectedValuesForFormId = Array.isArray(plan.forms) ? plan.forms.map(String) : JSON.parse(plan.forms).map(String);
        $('#select2Success-formValidationSelectForms_edit').val(preselectedValuesForFormId).trigger('change');
    }
    function sendUpdatedHealthPlanData() {
        const healthPlanTitle = document.getElementById('formValidationHealthPlanTitle_edit').value;
        const healthPlanDescription = document.getElementById('formValidationHealthPlanDescription_edit').value;
        const masterTestIds = Array.from(document.getElementById('select2Success-formValidationSelectMasterTest_edit').selectedOptions)
            .map(option => Number(option.value))
            .filter(value => !isNaN(value));
        const certificateIds = Array.from(document.getElementById('select2Success-formValidationSelectCertificate_edit').selectedOptions)
            .map(option => Number(option.value))
            .filter(value => !isNaN(value));
        const formIds = Array.from(document.getElementById('select2Success-formValidationSelectForms_edit').selectedOptions)
            .map(option => Number(option.value))
            .filter(value => !isNaN(value));
        const gender = Array.from(document.querySelectorAll('input[name="gender_edit"]:checked'))
            .map(input => input.value);
        const healthPlanId = document.getElementById('healthplan_id_edit').value;
        if (!healthPlanTitle || gender.length === 0) {
            showToast('error', 'Error', 'Please fill in all required fields.');
            return;
        }
        const isPreEmployment = document.getElementById('pre-employment-check-box-edit').checked ? 1 : 0;
        const activeStatus = document.getElementById('active-check-box-edit').checked ? 1 : 0;
        const payload = {
            corporate_id: document.getElementById('corporate_id').value,
            healthplan_id: healthPlanId,
            healthplan_title: healthPlanTitle,
            healthplan_description: healthPlanDescription,
            master_test_id: masterTestIds,
            certificate_id: certificateIds,
            forms: formIds,
            isPreEmployement: isPreEmployment,
            gender: gender,
            active_status: activeStatus
        };
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to update this health plan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    apiRequest({
                        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/updateHealthPlan',
                        method: 'POST',
                        data: payload,
                        onSuccess: (res) => {
                            if (res.result) {
                                showToast('success', 'Success', 'Health plan updated successfully!');
                                $('#editHealthPlanModal').modal('hide');
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');
                                apiRequest({
                                    url: `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllHealthplans`,
                                    method: 'GET',
                                    onSuccess: (refreshResponse) => {
                                        if (refreshResponse.result && refreshResponse.message) {
                                            populateHealthPlansWithFilter(refreshResponse.message);
                                        }
                                    },
                                    onError: errorMessage => {
                                        showToast('error', 'Error', errorMessage);
                                    }
                                });
                            } else {
                                showToast('error', 'Error', res.message || 'Failed to update health plan.');
                            }
                        },
                        onError: (error) => {
                            showToast('error', 'Error', `Failed to update health plan: ${error}`);
                        }
                    });
                } catch (error) {
                    showToast('error', 'Error', `Unexpected error: ${error.message}`);
                }
            }
        });
    }
    function populateHealthPlans(data) {
        const tableBody = document.querySelector('#healthPlanTable tbody');
        tableBody.innerHTML = '';
        if (data.length === 0) {
            tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center">
                <div class="alert alert-info" role="alert">
                    <i class="ti ti-info-circle me-2"></i>
                    No health plans available.
                </div>
            </td>
        </tr>`;
            return;
        }
        const formNameMap = window.formNameMap || {};
        data.forEach((plan, index) => {
            const tests = plan.master_test_names && plan.master_test_names.length > 0
                ? plan.master_test_names.join(', ')
                : 'N/A';
            const certificates = plan.certificate_names && plan.certificate_names.length > 0
                ? plan.certificate_names
                    .map(name => name.charAt(0).toUpperCase() + name.slice(1))
                    .join(', ')
                : '-';
            let formNames = [];
            try {
                const formIds = Array.isArray(plan.forms) ? plan.forms : JSON.parse(plan.forms);
                formNames = formIds.map(id => formNameMap[id] || `Form ${id}`).join(', ');
            } catch (error) {
                formNames = '-';
            }
            const statusBadge = plan.active_status ?
                '<span class="badge bg-label-primary me-1">Active</span>' :
                '<span class="badge bg-label-danger me-1">Inactive</span>';
            const preEmploymentBadge = plan.isPreEmployement === true ?
                '&nbsp;&nbsp;<span class="badge badge-center rounded-pill bg-label-primary">PE</span>' : '';
            let actionButtons = '';
            actionButtons = `
                    <span class="edit-btn me-3" role="button" style="cursor: pointer;">
                        <i class="ti ti-pencil fs-5"></i>
                    </span>
                    <span class="delete-btn text-danger" data-healthplan-id="${plan.corporate_healthplan_id}" role="button" style="cursor: pointer;">
                        <i class="ti ti-trash fs-5"></i>
                    </span>
                `;
            const row = `
                    <tr data-healthplan-title="${plan.healthplan_title}"  
                        data-healthplan-description="${plan.healthplan_description ?? '-'}"
                        data-certificates="${certificates}" 
                        data-status="${plan.active_status ? 'Active' : 'Inactive'}"   
                        data-forms="${formNames}"
                        data-tests="${tests}">
                        <td>${index + 1}</td>
                        <td>
                            <span class="fw-medium showDetailsBtn text-primary" style="cursor: pointer;">${plan.healthplan_title}</span>${preEmploymentBadge}
                            <br>
                            <span>${plan.healthplan_description ?? '-'}</span>
                        </td>
                        <td>${certificates}</td>
                        <td>${formNames}</td>
                        <td>${statusBadge}</td>
                        <td>
                            ${actionButtons}
                        </td>
                    </tr>
                `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const healthPlanId = this.getAttribute('data-healthplan-id');
                showDeleteConfirmation(healthPlanId);
            });
        });
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const healthPlanId = this.closest('tr').querySelector('.delete-btn').getAttribute('data-healthplan-id');
                apiRequest({
                    url: `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getHealthPlan/${document.getElementById('corporate_id').value}/${healthPlanId}`,
                    method: 'GET',
                    onSuccess: (response) => {
                        if (response.result) {
                            populateEditModal(response.data);
                            $('#editHealthPlanModal').modal('show');
                        } else {
                            showToast('error', 'Error', 'Failed to load health plan data.');
                        }
                    },
                    onError: (error) => {
                        showToast('error', 'Error', 'Failed to fetch health plan data: ' + error);
                    }
                });
            });
        });
    }
    function showDeleteConfirmation(healthPlanId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteHealthPlan(healthPlanId);
            }
        });
    }
    function deleteHealthPlan(healthPlanId) {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/deleteHealthPlan/' + document.getElementById('corporate_id').value + '/' + healthPlanId,
            method: 'get',
            onSuccess: (response) => {
                if (response.result) {
                    showToast('success', 'Deleted', 'Health plan deleted successfully.');
                    apiRequest({
                        url: `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllHealthplans`,
                        method: 'GET',
                        onSuccess: response => {
                            if (response.result && response.message) {
                                populateHealthPlansWithFilter(response.message);
                            }
                        },
                        onError: errorMessage => {
                            showToast('error', 'Error', errorMessage);
                        }
                    });
                } else {
                    showToast('error', 'Error', 'Failed to delete health plan.');
                }
            },
            onError: (error) => {
                showToast('error', 'Error', 'Error deleting health plan: ' + error);
            }
        });
    }
    async function fetchFormNames() {
        try {
            window.formNameMap = {};
            const data = await apiRequest({
                url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllForms/' + document.getElementById('corporate_id').value,
                method: 'GET',
                onSuccess: (response) => {
                    const selectElement = document.getElementById('select2Success-formValidationSelectForms');
                    if (response && response.result && Array.isArray(response.data)) {
                        response.data.forEach((form) => {
                            window.formNameMap[form.form_id] = form.form_name;
                            const option = document.createElement('option');
                            option.value = form.form_id;
                            option.textContent = form.form_name;
                            selectElement.appendChild(option);
                        });
                    } else {
                        showToast('error', 'Invalid data format, ' + JSON.stringify(response));
                    }
                    const selectElement_edit = document.getElementById('select2Success-formValidationSelectForms_edit');
                    if (response && response.result && Array.isArray(response.data)) {
                        response.data.forEach((form) => {
                            const option = document.createElement('option');
                            option.value = form.form_id;
                            option.textContent = form.form_name;
                            selectElement_edit.appendChild(option);
                        });
                    } else {
                        showToast('error', 'Invalid data format, ' + JSON.stringify(response));
                    }
                },
                onError: (error) => {
                    showToast('error', 'Error Fetching Form Records, ' + error);
                }
            });
        } catch (error) {
            showToast('error', 'Error Fetching Form Records ' + error);
        }
    }
    async function fetchTestNames() {
        try {
            const data = await apiRequest({
                url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllMasterTests',
                method: 'GET',
                onSuccess: (response) => {
                    if (response && response.result && Array.isArray(response.data)) {
                        const targets = [
                            document.getElementById('select2Success-formValidationSelectMasterTest'),
                            document.getElementById('select2Success-formValidationSelectMasterTest_edit'),
                            document.getElementById('filterTestSelect')
                        ];
                        targets.forEach((selectElement) => {
                            if (selectElement) {
                                response.data.forEach((test) => {
                                    const option = document.createElement('option');
                                    option.value = test.master_test_id;
                                    option.textContent = test.test_name;
                                    selectElement.appendChild(option);
                                });
                            }
                        });
                    } else {
                        showToast('error', 'Invalid data format, ' + JSON.stringify(response));
                    }
                },
                onError: (error) => {
                    showToast('error', 'Error Fetching Test Records, ' + error);
                }
            });
        } catch (error) {
            showToast('error', 'Error Fetching Test Records ' + error);
        }
    }
    function createTestModal() {
        if (document.getElementById('testListModal')) return;
        const modalHtml = `
            <div class="modal fade" id="testListModal" tabindex="-1" aria-labelledby="testListModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="testListModalLabel">Health Plan Test Structure</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-header-info">
                                <div class="employee-info" id="modalHealthPlanName"></div>
                                <div class="date-info" id="modalHealthPlanDescription"></div>
                            </div>
                            <div class="mb-3">
                                <div id="modalTestList" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    function showTestListModal(healthPlan) {
        createTestModal();
        const modal = document.getElementById('testListModal');
        const nameEl = document.getElementById('modalHealthPlanName');
        const descEl = document.getElementById('modalHealthPlanDescription');
        const listEl = document.getElementById('modalTestList');
        if (!modal || !nameEl || !descEl || !listEl) {
            console.error('Modal elements not found');
            return;
        }
        nameEl.textContent = healthPlan.healthplan_title;
        descEl.textContent = healthPlan.healthplan_description || 'No description available';
        listEl.innerHTML = '';
        let testStructure = {};
        try {
            testStructure = typeof healthPlan.testStructure === 'string'
                ? JSON.parse(healthPlan.testStructure)
                : healthPlan.testStructure;
            if (Object.keys(testStructure).length > 0) {
                renderHierarchicalTests(testStructure, listEl);
            } else {
                listEl.textContent = 'No tests available';
            }
        } catch (e) {
            console.error('Error parsing test structure:', e);
            listEl.textContent = 'Error loading test structure data.';
        }
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modal).show();
        } else {
            console.error('Bootstrap is not available');
        }
    }
    function renderHierarchicalTests(testStructure, container) {
        const isTestObject = test => typeof test === 'object' && test !== null;
        const renderGroup = (group, title, className) => {
            const header = document.createElement('div');
            header.className = className;
            header.textContent = title;
            container.appendChild(header);
        };
        const separateTestsAndGroups = obj => {
            const tests = [], groups = {};
            Object.entries(obj).forEach(([key, val]) => {
                isNaN(parseInt(key)) ? groups[key] = val : tests.push(val);
            });
            return { tests, groups };
        };
        const renderDeepGroups = (group, depthClass) => {
            const { tests, groups } = separateTestsAndGroups(group);
            if (tests.length) renderTestItems(tests, container, depthClass);
            Object.entries(groups).forEach(([subTitle, subGroup]) => {
                renderGroup(null, subTitle, `${depthClass}-title`);
                if (Array.isArray(subGroup)) {
                    renderTestItems(subGroup, container, depthClass);
                } else if (isTestObject(subGroup) && subGroup.name) {
                    renderSingleTest(subGroup, container, depthClass);
                } else if (typeof subGroup === 'string') {
                    renderSingleTest(subGroup, container, depthClass);
                }
            });
        };
        Object.entries(testStructure).forEach(([key, group]) => {
            if (!isNaN(parseInt(key))) {
                renderSingleTest(group, container, 'test-item');
                return;
            }
            renderGroup(group, key, 'test-group-title');
            if (Array.isArray(group)) {
                renderTestItems(group, container, 'test-item');
            } else if (typeof group === 'object') {
                Object.entries(group).forEach(([subKey, subGroup]) => {
                    if (!isNaN(parseInt(subKey))) {
                        renderSingleTest(subGroup, container, 'test-item');
                    } else {
                        renderGroup(null, subKey, 'test-subgroup-title');
                        if (Array.isArray(subGroup)) {
                            renderTestItems(subGroup, container, 'subgroup-test-item');
                        } else {
                            renderDeepGroups(subGroup, 'subsubgroup-test-item');
                        }
                    }
                });
            } else if (typeof group === 'string') {
                renderSingleTest(group, container, 'test-item');
            }
        });
    }
    function renderTestItems(items, container, className) {
        items.forEach(item => renderSingleTest(item, container, className));
    }
    function renderSingleTest(item, container, className) {
        const el = document.createElement('div');
        el.className = className;
        el.textContent = (typeof item === 'object' && item?.name) ? item.name : String(item || 'Unknown Test');
        container.appendChild(el);
    }
    function applyFilters() {
        const search = document.getElementById('searchInput').value.toLowerCase().trim();
        const selectedTests = $('#filterTestSelect').val() || [];
        filteredHealthPlans = originalHealthPlans.filter(plan => {
            const titleMatch = !search || plan.healthplan_title.toLowerCase().includes(search);
            let testMatch = true;
            if (selectedTests.length) {
                const planTestIds = extractTestIdsFromStructure(plan.testStructure);
                const selectedTestStr = selectedTests.map(String);
                const planTestStr = planTestIds.map(String);
                testMatch = selectedTestStr.every(id => planTestStr.includes(id));
            }
            return titleMatch && testMatch;
        });
        populateHealthPlans(filteredHealthPlans);
        const msg = `Showing ${filteredHealthPlans.length} of ${originalHealthPlans.length} health plans`;
        if (search || selectedTests.length) showToast('info', 'Filter Applied', msg);
    }
    function extractTestIdsFromStructure(testStructure) {
        const testIds = [];
        if (!testStructure || typeof testStructure !== 'object') {
            return testIds;
        }
        function traverseStructure(obj) {
            if (Array.isArray(obj)) {
                obj.forEach(item => {
                    if (item && typeof item === 'object' && item.master_test_id) {
                        testIds.push(item.master_test_id);
                    } else if (typeof item === 'object') {
                        traverseStructure(item);
                    }
                });
            } else if (typeof obj === 'object' && obj !== null) {
                Object.values(obj).forEach(value => {
                    if (value && typeof value === 'object' && value.master_test_id) {
                        testIds.push(value.master_test_id);
                    } else if (typeof value === 'object' || Array.isArray(value)) {
                        traverseStructure(value);
                    }
                });
            }
        }
        try {
            const structure = typeof testStructure === 'string' ? JSON.parse(testStructure) : testStructure;
            traverseStructure(structure);
        } catch (e) {
            console.warn('Error parsing test structure for filtering:', e);
        }
        return [...new Set(testIds)];
    }
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        $('#filterTestSelect').val(null).trigger('change');
        filteredHealthPlans = [...originalHealthPlans];
        populateHealthPlans(originalHealthPlans);
        showToast('success', 'Filters Cleared', 'Showing all health plans');
    }
    function initializeFilters() {
        $('#filterTestSelect').on('change', () => { });
        document.getElementById('applyFiltersBtn').addEventListener('click', applyFilters);
        document.getElementById('searchInput').addEventListener('keypress', e => {
            if (e.key === 'Enter') applyFilters();
        });
    }
    function populateHealthPlansWithFilter(data) {
        originalHealthPlans = [...data];
        filteredHealthPlans = [...data];
        populateHealthPlans(data);
    }
    document.addEventListener('keydown', e => {
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            clearFilters();
        }
    });
    $(document).ready(() => {
        const apiUrl = `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllHealthplans`;
        apiRequest({
            url: apiUrl,
            method: 'GET',
            onSuccess: response => {
                if (response.result && response.message) {
                    setTimeout(() => {
                        document.getElementById('existing-helthplan-spinner').style.display = 'none';
                        document.getElementById('existing-helthplan-table').style.display = 'block';
                        populateHealthPlansWithFilter(response.message); storedHealthPlans = response.message;
                    }, 2000);
                } else {
                    showToast('error', 'Error', 'Failed to load health plans.');
                }
            },
            onError: errorMessage => {
                showToast('error', 'Error', errorMessage);
            }
        });
        $('#select2Success-formValidationSelectCertificate').change(function () {
            if ($('#select2Success-formValidationSelectCertificate').val()) {
                $('#header-formValidationSelectCertificate').prop('selected', false);
            } else {
                $('#header-formValidationSelectCertificate').prop('selected', true);
            }
        });
        $('#select2Success-formValidationSelectForms').change(function () {
            if ($('#select2Success-formValidationSelectForms').val()) {
                $('#header-formValidationSelectForms').prop('selected', false);
            } else {
                $('#header-formValidationSelectForms').prop('selected', true);
            }
        });
        $('#select2Success-formValidationSelectMasterTest').change(function () {
            if ($('#select2Success-formValidationSelectMasterTest').val()) {
                $('#header-formValidationSelectMasterTest').prop('selected', false);
            } else {
                $('#header-formValidationSelectMasterTest').prop('selected', true);
            }
        });
        async function fetchCertificateNames() {
            try {
                const data = await apiRequest({
                    url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllCertificates/' + document.getElementById('corporate_id').value,
                    method: 'GET',
                    onSuccess: (response) => {
                        const selectElement = document.getElementById('select2Success-formValidationSelectCertificate');
                        if (response && response.result && Array.isArray(response.data)) {
                            response.data.forEach((test) => {
                                const option = document.createElement('option');
                                option.value = test.certificate_id;
                                option.textContent = test.certification_title;
                                selectElement.appendChild(option);
                            });
                        } else {
                            showToast('error', 'Invalid data format, ' + JSON.stringify(response));
                        }
                        const selectElement_edit = document.getElementById('select2Success-formValidationSelectCertificate_edit');
                        if (response && response.result && Array.isArray(response.data)) {
                            response.data.forEach((test) => {
                                const option = document.createElement('option');
                                option.value = test.certificate_id;
                                option.textContent = test.certification_title;
                                selectElement_edit.appendChild(option);
                            });
                        } else {
                            showToast('error', 'Invalid data format, ' + JSON.stringify(response));
                        }
                    },
                    onError: (error) => {
                        showToast('error', 'Error Fetching Test Records, ' + error);
                    }
                });
            } catch (error) {
                showToast('error', 'Error Fetching Test Records ' + error);
            }
        }
        fetchTestNames();
        fetchFormNames();
        fetchCertificateNames();
        // TODO: Master test shld be required, Validation to be done on
        (function () {
            const addNewHealthPlanForm = document.getElementById('addNewHealthPlanForm');
            const fv = FormValidation.formValidation(addNewHealthPlanForm, {
                fields: {
                    formValidationHealthPlanTitle: {
                        validators: {
                            notEmpty: {
                                message: 'Please enter your health plan title'
                            },
                            stringLength: {
                                min: 6,
                                max: 30,
                                message: 'The healthplan title must be more than 6 and less than 30 characters long'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9 ]+$/,
                                message: 'The healthplan title can only consist of alphabetical, number and space'
                            }
                        }
                    },
                    gender: {
                        validators: {
                            notEmpty: {
                                message: 'Please select at least one gender'
                            },
                            callback: {
                                callback: function () {
                                    return document.querySelectorAll('input[name="gender"]:checked').length > 0;
                                }
                            }
                        }
                    },
                    formValidationSelectMasterTest: {
                        validators: {
                            callback: {
                                message: 'Please select at least one test',
                                callback: function (input) {
                                    const select = document.getElementById('formValidationSelectMasterTest');
                                    const selectedOptions = Array.from(select.selectedOptions);
                                    return selectedOptions.length > 0 &&
                                        !selectedOptions.some(opt => opt.value === 'defaultSelect');
                                }
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger({
                        event: {
                            change: true,
                            blur: true
                        }
                    }),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        eleValidClass: '',
                        rowSelector: function (field, ele) {
                            switch (field) {
                                case 'formValidationSelectMasterTest':
                                    return '.col-md-9';
                                default:
                                    return '.row';
                            }
                        }
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    autoFocus: new FormValidation.plugins.AutoFocus()
                }
            });
            const editHealthPlanForm = document.getElementById('editHealthPlanForm');
            const fvEdit = FormValidation.formValidation(editHealthPlanForm, {
                fields: {
                    formValidationHealthPlanTitle_edit: {
                        validators: {
                            notEmpty: {
                                message: 'Please enter your health plan title'
                            },
                            stringLength: {
                                min: 6,
                                max: 30,
                                message: 'The healthplan title must be more than 6 and less than 30 characters long'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9 ]+$/,
                                message: 'The healthplan title can only consist of alphabetical, number and space'
                            }
                        }
                    },
                    gender_edit: {
                        validators: {
                            notEmpty: {
                                message: 'Please select at least one gender'
                            },
                            callback: {
                                callback: function () {
                                    return document.querySelectorAll('input[name="gender_edit"]:checked').length > 0;
                                }
                            }
                        }
                    },
                    formValidationSelectMasterTest_edit: {
                        validators: {
                            callback: {
                                message: 'Please select at least one test',
                                callback: function (input) {
                                    const select = document.getElementById('formValidationSelectMasterTest');
                                    const selectedOptions = Array.from(select.selectedOptions);
                                    return selectedOptions.length > 0 &&
                                        !selectedOptions.some(opt => opt.value === 'defaultSelect');
                                }
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger({
                        event: {
                            change: true,
                            blur: true
                        }
                    }),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        eleValidClass: '',
                        rowSelector: function (field, ele) {
                            switch (field) {
                                case 'formValidationSelectMasterTest':
                                    return '.col-md-9';
                                default:
                                    return '.row';
                            }
                        }
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    autoFocus: new FormValidation.plugins.AutoFocus()
                }
            });
        })();
        initializeFilters();
        $(document).off('click', '.showDetailsBtn').on('click', '.showDetailsBtn', function () {
            const title = $(this).closest('tr').attr('data-healthplan-title');
            if (!storedHealthPlans.length) {
                showToast('error', 'Error', 'No health plans available.');
                return;
            }
            const healthPlan = storedHealthPlans.find(p => p.healthplan_title === title);
            healthPlan ? showTestListModal(healthPlan) : showToast('error', 'Error', 'Health plan not found.');
        });
    });
</script>
@endsection
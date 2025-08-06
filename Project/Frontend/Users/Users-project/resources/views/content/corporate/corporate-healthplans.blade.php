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
<link rel="stylesheet" href="/lib/css/page-styles/corporate-healthplans.css">
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
<script src="/lib/js/page-scripts/corporate-healthplans.js"></script>
@endsection
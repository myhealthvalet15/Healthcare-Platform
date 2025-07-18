@extends('layouts/layoutMaster')
@section('title', 'Assign Healthplans')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/form-layouts.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('content')
<div class="row mb-12">
    <div class="col-md">
        <div class="card">
            <h5 class="card-header">Assign Healthplan</h5>
            <div class="col" id="assign-healthplan-spinner"
                style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">
                <div class="sk-bounce sk-primary">
                    <div class="sk-bounce-dot"></div>
                    <div class="sk-bounce-dot"></div>
                </div>
                <p class="loading-text" style="margin-top: 10px;">Loading
                    Data...</p>
            </div>
            <div class="card-body" id="assign-healthplan-element" style="display: none;">
                <form class="needs-validation" novalidate>
                    <div class="row g-6">
                        <div class="col-md-4">
                            <label for="employeeTypeSelect2" class="form-label">Employee Type</label>
                            <div class="select2-primary">
                                <select id="employeeTypeSelect2" class="select2 form-select" multiple>
                                    <!-- Employee types will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="departmentSelect2" class="form-label">Department</label>
                            <div class="select2-primary">
                                <select id="departmentSelect2" class="select2 form-select" multiple>
                                    <!-- Departments will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="designationSelect2" class="form-label">Designation</label>
                            <div class="select2-primary">
                                <select id="designationSelect2" class="select2 form-select" multiple>
                                    <!-- Designations will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label for="employeesSelect2" class="form-label">Employees</label>
                            <div class="select2-primary">
                                <select id="employeesSelect2" class="select2 form-select" multiple>
                                    <!-- Employees will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-light fw-medium d-block">&nbsp;</small>
                            <div class="form-check form-check-primary mt-4">
                                <input name="customCheckPrimary" class="form-check-input" type="checkbox" value
                                    id="customCheckPrimary" />
                                <label class="form-check-label" for="customCheckPrimary">
                                    All Employees
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-healthplan">Healthplans</label>
                            <select class="form-select" id="bs-validation-healthplan" required>
                                <option value>Select Healthplans</option>
                                <!-- Healthplans will be populated here -->
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select your
                                healthplan</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-assign-date">Assign
                                Date</label>
                            <input type="text" class="form-control flatpickr-validation" id="bs-validation-assign-date"
                                placeholder="DD-MM-YYYY" required />
                            <div class="valid-feedback"> Looks good! </div>
                            <div class="invalid-feedback"> Please Enter Assign
                                Date </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-due-date">Due Date</label>
                            <input type="text" class="form-control flatpickr-validation" id="bs-validation-due-date"
                                placeholder="DD-MM-YYYY" required />
                            <div class="valid-feedback"> Looks good! </div>
                            <div class="invalid-feedback"> Please Enter Due Date
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-doctor">Doctor</label>
                            <select class="form-select" id="bs-validation-doctor">
                                <option value>Select Doctor</option>
                                <option value="usa">USA</option>
                                <option value="uk">UK</option>
                                <option value="france">France</option>
                                <option value="australia">Australia</option>
                                <option value="spain">Spain</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-favourite">Favourite</label>
                            <select class="form-select" id="bs-validation-favourite">
                                <option value>Select favourite</option>
                                <option value="usa">USA</option>
                                <option value="uk">UK</option>
                                <option value="france">France</option>
                                <option value="australia">Australia</option>
                                <option value="spain">Spain</option>
                            </select>
                        </div>
                    </div>
                    <!-- Added spacing container for the submit button -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/corporate-assign-healthplans.js"></script>
@endsection
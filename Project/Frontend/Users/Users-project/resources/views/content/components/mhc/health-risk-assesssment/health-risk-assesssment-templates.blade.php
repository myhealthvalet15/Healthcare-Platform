@extends('layouts/layoutMaster')
@section('title', 'HRA Templates')
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
@section('page-script')
@vite(['resources/assets/js/form-layouts.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/health-risk-assesssment-templates.css">
<div>
    <div class="card" style="background-color:transparent;border:none;box-shadow:none;outline:none">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" id="formTitle">Add HRA
                            Template</h5>
                        <div class="button-group">
                            <button type="button" id="saveHRATemplateBtn" class="btn btn-primary btn-md">
                                <i class="ti ti-device-floppy me-2"></i>
                                <span id="btnText">Save Template</span>
                            </button>
                            <button type="button" id="cancelEditBtn" class="btn btn-md btn-secondary d-none">
                                <i class="ti ti-x me-1"></i>Cancel Edit
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="hraTemplateForm">
                            <input type="hidden" id="editMode" value="false">
                            <input type="hidden" id="editIndex" value>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label for="hraTemplate" class="form-label">HRA Template <span
                                            class="text-danger">*</span></label>
                                    <select id="hraTemplate" class="select2 form-select form-select-lg"
                                        data-allow-clear="true">
                                        <option value>Loading...</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="location" class="form-label">Location <span
                                            class="text-danger">*</span></label>
                                    <select id="location" class="select2 form-select form-select-lg"
                                        data-allow-clear="true">
                                        <option value>Loading...</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="employeeType" class="form-label">Employee Type <span
                                            class="text-danger">*</span></label>
                                    <select id="employeeType" class="select2 form-select form-select-lg" multiple>
                                        <option value>Loading...</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="department" class="form-label">Department <span
                                            class="text-danger">*</span></label>
                                    <select id="department" class="select2 form-select form-select-lg" multiple>
                                        <option value>Loading...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 mb-3">
                                    <label for="designation" class="form-label">Designation</label>
                                    <select id="designation" class="select2 form-select form-select-lg" multiple>
                                        <option value>Loading...</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control flatpickr-date" type="text" id="fromDate"
                                        placeholder="Select from date" />
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To
                                        Date <span class="text-danger">*</span></label>
                                    <input class="form-control flatpickr-date" type="text" id="toDate"
                                        placeholder="Select to date" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Available Templates</h5>
                        <small class="text-muted">Click on menu items (☰) to
                            view full details</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="hraTemplatesTable">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Template Name</th>
                                        <th>Location</th>
                                        <th>Employee Type</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Start Date/<br>End Date</th>
                                        <th>Edit</th>
                                    </tr>
                                </thead>
                                <tbody id="hraTemplatesTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="mt-2">Loading
                                                templates...</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="multiValueModal" tabindex="-1" aria-labelledby="multiValueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="multiValueModalLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="multiValueContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/health-risk-assesssment-templates.js"></script>
@endsection
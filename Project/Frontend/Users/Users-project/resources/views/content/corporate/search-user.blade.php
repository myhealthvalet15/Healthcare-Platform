@extends('layouts/layoutMaster')
@section('title', 'Employee Search')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('content')
<div class="row g-6 d-flex justify-content-center align-items-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body demo-vertical-spacing demo-only-element">
                <div class="row">
                    <div class="col-7">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                            <input type="text" class="form-control" id="searchEmployees" placeholder="Search by Name/Employee ID/Phone #"
                                aria-label="Search..." aria-describedby="basic-addon-search31">
                        </div>
                    </div>
                    <div class="col-4">
                        <button id="searchBtn" type="submit"
                            class="btn btn-primary waves-effect waves-light w-100">Search Employee</button>
                    </div>
                </div>
                <!-- Search Spinner -->
                <div id="searchSpinner" class="row mt-4" style="display: none;">
                    <div class="col d-flex flex-column align-items-center justify-content-center">
                        <div class="sk-bounce sk-primary mb-2">
                            <div class="sk-bounce-dot"></div>
                            <div class="sk-bounce-dot"></div>
                        </div>
                        <span class="text-primary fw-semibold">Searching your
                            query...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<!-- DataTable with Buttons -->
<div id="resultsCard" class="card" style="display: none;">
    <div class="card-datatable table-responsive pt-0">
        <table id="employeeTable" class="datatables-basic table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Contact Details</th>
                    <th>Employee Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here -->
            </tbody>
        </table>
    </div>
</div>
<script src="/lib/js/page-scripts/search-user.js"></script>
@endsection
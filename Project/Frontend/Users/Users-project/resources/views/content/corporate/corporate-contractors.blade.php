@extends('layouts.layoutMaster')

@section('title', 'DataTables - Advanced Tables')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/contractor-form-validation.js'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/corporate-contractors.css">
<div class="row">
    <div class="col-3">
        <div class="search-container">
            <label for="customSearchInput" class="visually-hidden">Search:</label>
            <input type="search" id="customSearchInput" placeholder="Search contractors." class="form-control" />
        </div>
    </div>
    <div class="col-6 d-flex justify-content-end">
        <div class="position-relative" style="height: 50px;">
            <button class="btn btn-primary btn-sm position-absolute top-0 end-0 m-3 text-white"
                data-bs-toggle="offcanvas" data-bs-target="#addNewModal" aria-controls="addNewModal">
                Add New Contractor
            </button>

        </div>
        <button id="exportExcel" style="border: none;">

            <i class="fa-solid fa-file-excel" style="font-size: 30px;"></i>
        </button>
    </div>
</div>
<br />
<div class="card">
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table">
            <thead>
                <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of
                                Contractors</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th style="display:none;">ID</th>
                    <th>Contractor name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="addNewModal" aria-labelledby="addNewModalLabel"
    data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addNewModalLabel">Add New Contractor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Form inside Offcanvas -->

        <form action="{{ route('addContractors') }}" method="POST" id="addNewForm">


            <div class="col-sm-12 mb-3">
                <label class="form-label" for="contractor_name">Contractor Name</label>
                <input type="text" id="contractor_name" name="contractor_name" class="form-control"
                    placeholder="johndoe" />
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="contractor_email">Email</label>
                <input type="email" id="contractor_email" name="contractor_email" class="form-control"
                    placeholder="john.doe@email.com" />
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="contractor_address">Address</label>
                <input type="text" id="contractor_address" name="contractor_address" class="form-control"
                    placeholder="Address" />
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="add_active_status">Status</label>
                <div class="form-switch mt-1">
                    <input type="hidden" name="active_status" value="0">
                    <input class="form-check-input toggle-active-status" type="checkbox" id="add_active_status"
                        name="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="add_active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>

            </div>

            <div class="col-sm-12">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                <button type="button" class="btn btn-primary" id="saveChangesButton">Save changes</button>
            </div>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-end editModal" tabindex="-1" id="editModal"
    aria-labelledby="offcanvasEditEmployeeTypeLabel" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEditEmployeeTypeLabel">Edit Contractor Details</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form id="editForm" method="POST">

            <div class="col-sm-12 mb-3">
                <label for="edit_contractor_name" class="form-label">Contractor Name</label>
                <input type="text" class="form-control" id="edit_contractor_name" name="contractor_name" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="edit_contractor_email" class="form-label">Email</label>
                <input type="text" class="form-control" id="edit_contractor_email" name="email" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="edit_contractor_address" class="form-label">Address</label>
                <input type="text" class="form-control" id="edit_contractor_address" name="address" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label">Status</label>
                <div class="form-switch mt-1">
                    <!-- Hidden input to capture unchecked status -->
                    <input type="hidden" name="active_status" value="0">
                    <input class="form-check-input toggle-active-status" type="checkbox" id="edit_active_status"
                        name="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="edit_active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
            </div>
            <div class="col-sm-12">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                <button type="submit" class="btn btn-primary" id="updateChangesButton">Update</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Contractor details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this contractor details?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="deleteButton" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/corporate-contractors.js"></script>
@endsection
@extends('layouts/layoutMaster')
@section('title', 'Employee Type Management')
@section('description', 'Manage Employee Types and Active Status')
@section('content')
<style>
    .form-check-input:checked {
        background-color: green !important;
    }

    .form-check-input:not(:checked) {
        background-color: lightcoral !important;
        border: 2px solid lightcoral;
    }

    .form-check-input,
    .status-label {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    .is-invalid {
        border: 2px solid red !important;
    }

    .form-switch {
        display: flex;
        align-items: center;
    }

    .form-switch input {
        margin-right: 10px;
    }

    .row {
        margin-bottom: 15px;
    }

    .btn-group {
        margin-bottom: 20px;
    }

    .status-label {
        font-size: 0.9rem;
    }

    .remove-employee-type {
        font-size: 0.8rem;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .form-control-sm {
        font-size: 0.9rem;
        height: 34px;
    }

    .small-label {
        font-size: 0.85rem;
        color: #666;
    }

    .employee-type-fields {
        padding: 10px;
        border: 1px solid #f0f0f0;
        border-radius: 5px;
        background-color: #fafafa;
        margin-bottom: 15px;
    }

    .employee-type-fields label {
        font-size: 0.9rem;
    }

    .text-end {
        margin-bottom: 15px;
    }

    .offcanvas-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .offcanvas-body input,
    .offcanvas-body select,
    .offcanvas-body textarea {
        margin-bottom: 10px;
    }

    .input-group-text {
        background-color: #f1f1f1;
        border-right: 0;
    }

    .input-group input {
        border-left: 0;
    }

    .form-label {
        font-weight: bold;
    }

    .btn-outline-secondary {
        border-radius: 5px;
    }

    .btn-sm {
        padding: 6px 12px;
    }

    .form-text {
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>
<div class="container">
    <div class="container row">
        <div class="d-flex justify-content-between align-items-center col-md-12 mb-3">
            <div class="col-md-5">
                <p class="mb-2 text-muted">
                    Corporate &raquo; Corporate List
                </p>
                <h3 class="text-primary mb-3">
                    <strong>{{ $corporate_name }}</strong>
                    <p class="text-dark small">Corporate Employee Type Details</p>
                </h3>
            </div>
            <div class="col-md-7 text-end">
                <a href="{{ route('corporate.edit', $id) }}" class="btn btn-dark btn-sm fon" data-bs-toggle="tooltip" title="Edit Corporate">
                    <i class="fas fa-building "></i>
                </a>
                <a href="{{ route('corporate.editAddress', ['id' => $id, 'corporate_id' => $emptype_id]) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Edit Address">
                    <i class="fas fa-map-marker-alt"></i>
                </a>
                <a href="{{ route('corporate.editEmployeeTypes',['id' => $id, 'corporate_id' => $corporate_id]) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit Employee Types">
                    <i class="fas fa-users "></i>
                </a>
                <a href="{{ route('corporate.editComponents', ['id' => $id, 'corporate_id' => $emptype_id]) }}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Edit Components">
                    <i class="fas fa-home "></i>
                </a>
                <a href="{{ route('corporate.editAdminUsers', ['id' => $id, 'corporate_id' => $emptype_id]) }}" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Edit Super Admin">
                    <i class="fas fa-user-tie"></i>
                </a>
            </div>
        </div>
        <div class="text-end mb-3">
            <button type="button" id="add-employee-type" class="btn btn-outline-success btn-sm">
                <i class="ti ti-plus"></i> Add Employee Type
            </button>
        </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddEmployeeType" aria-labelledby="offcanvasAddEmployeeTypeLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAddEmployeeTypeLabel">Add New Employee Type</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework" id="form-add-new-record" method="post" action="{{route('employeetype_add')}}">
                @csrf
                <div class="col-sm-12 fv-plugins-icon-container">
                    <label class="form-label" for="employee_type_name">Employee type</label>
                    <div class="input-group input-group-merge has-validation">
                        <span id="employee_type_name" class="input-group-text"><i class="ti ti-user"></i></span>
                        <input type="text" id="employee_type_name" class="form-control dt-full-name" name="employee_type_name" placeholder="Employee type" aria-label="Employee type" aria-describedby="basicFullname2">
                    </div>
                </div>
                <div class="col-sm-12 fv-plugins-icon-container">
                    <label class="form-label" for="basicPost">Status</label>
                    <div class="form-switch mt-1">
                        <input class="form-check-input toggle-active-status" type="checkbox" name="active_status" id="active_status" value="1">
                        <label class="form-check-label ms-2 small" for="active_status">
                            <span class="status-label">Inactive</span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1 waves-effect waves-light">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
                <input type="hidden" name="corporate_id" value="{{$corporate_id}}">
            </form>
        </div>
    </div>
    <div class="container mt-4" style="max-width: 800px;">
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead class="table-dark header">
                        <tr>
                            <th class=col-md-5><b>Employee Type</b></th>
                            <th class=col-md-4><b>Contractor/Vendor</b></th>
                            <th class=col-md-3><b>Status</b></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <form id="step-3-form" class="p-4 border rounded shadow-sm" method="post" action="{{ route('corporate.updateemptype') }}">
            @csrf
            <input type="hidden" name="corporate_id" value="{{ $emptype_id }}">
            @foreach($emptype as $index => $emptypes)
            <div class="employee-type-fields">
                <input type="hidden" name="employee_type_id[]" value="{{ $emptypes['employee_type_id'] }}">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <input type="text" id="employee_type_name_{{ $index }}" name="employee_type_name[]" class="form-control form-control-sm" placeholder="Enter Employee Type Name {{ $index + 1 }}" value="{{ $emptypes['employee_type_name'] }}">
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" id="contractor_{{ $index }}" class="Contractors" name="Contractors[{{ $index }}]" {{ $emptypes['checked'] == 1 ? 'checked' : '' }}>
                        <label for="contractor_{{ $index }}" class="small-label">Contractor/Vendor</label>
                    </div>
                    <div class="col-md-3">
                        <div class="form-switch mt-1">
                            <input class="form-check-input toggle-active-status" type="checkbox" name="active_status[{{ $index }}]" id="active_status_{{ $index }}" value="1" {{ $emptypes['active_status'] == 1 ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 small" for="active_status_{{ $index }}">
                                <span class="status-label">{{ $emptypes['active_status'] == 1 ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div id="dynamic-fields-container"></div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-sm px-4">Submit</button>
            </div>
        </form>
    </div>
</div>
<script src="/lib/js/page-scripts/edit-emp-type.js"></script>
@endsection
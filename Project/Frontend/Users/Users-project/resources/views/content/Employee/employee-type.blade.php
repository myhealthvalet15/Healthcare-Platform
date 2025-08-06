@extends('layouts/layoutMaster')

@section('title', 'Employee Type Management')
@section('description', 'Manage Employee Types and Active Status')
@section('content')


<link rel="stylesheet" href="/lib/css/page-styles/employee-type.css">

<div class="container mt-4" style="max-width: 1000px;">
    <div class="text-end mb-3">
        <button type="button" id="add-employee-type" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Add Employee Type
        </button>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddEmployeeType"
        aria-labelledby="offcanvasAddEmployeeTypeLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasAddEmployeeTypeLabel">Add New Employee Type</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body flex-grow-1">

            <form class="add-new-record pt-0 row g-2 fv-plugins-bootstrap5 fv-plugins-framework"
                id="form-add-new-record" method="post" action="{{ route('employeetype_add') }}">
                @csrf
                <div class="col-sm-12 fv-plugins-icon-container">
                    <label class="form-label" for="employee_type_name">Employee type</label>
                    <div class="input-group input-group-merge has-validation">
                        <span class="input-group-text"
                            style="background-color:rgb(240, 243, 247);display: flex; align-items: center; justify-content: center; height: 38px; padding: 0.375rem 0.75rem;">
                            <i class="ti ti-user" style="font-size: 14px;"></i> <!-- Smaller icon -->
                        </span>
                        <input type="text" id="employee_type_name" class="form-control dt-full-name"
                            name="employee_type_name" placeholder="   Employee type" aria-label="Employee type"
                            aria-describedby="basicFullname2">
                    </div>
                </div>



                <div class="col-sm-12 fv-plugins-icon-container">
                    <label class="form-label" for="basicPost">Status</label>
                    <div class="form-switch mt-1">
                        <input class="form-check-input toggle-active-status" type="checkbox" name="active_status"
                            id="active_status" value="1">
                        <label class="form-check-label ms-2 small" for="active_status">
                            <span class="status-label">Inactive</span>
                        </label>
                    </div>
                </div>



                <div class="col-sm-12">
                    <button type="reset" class="btn btn-secondary waves-effect"
                        data-bs-dismiss="offcanvas">Cancel</button>

                    <button type="submit"
                        class="btn btn-primary data-submit me-sm-4 me-1 waves-effect waves-light">Submit</button>
                </div>
                <input type="hidden" name="corporate_id" value="{{ $corporate_id }}">
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead class="table_dark header">
                    <tr class="Employee-text">
                        <th class="col-md-5"><b>Employee Type</b></th>
                        <th class="col-md-4"><b>Contractor/Vendor</b></th>
                        <th class="col-md-3"><b>Status</b></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    <form id="step-3-form" class="p-4 border rounded shadow-sm" method="post" action="{{ route('updateemptype') }}">
        @csrf
        <input type="hidden" name="corporate_id" value="{{ $corporate_id }}">

        @foreach ($emptype as $index => $emptypes)
        <div class="row align-items-center mb-4 employee-type-fields">
            <input type="hidden" name="employee_type_id[]" value="{{ $emptypes['employee_type_id'] }}">
            <div class="col-md-5">
                <input type="text" id="employee_type_name_{{ $index }}" name="employee_type_name[]"
                    class="form-control form-control-sm" placeholder="Enter Employee Type Name {{ $index + 1 }}"
                    value="{{ $emptypes['employee_type_name'] }}">
            </div>
            <div class="col-md-4">
                <input type="checkbox" id="contractor_{{ $index }}" class="Contractors" name="Contractors[{{ $index }}]"
                    {{ $emptypes['checked']==1 ? 'checked' : '' }}>
                <label for="contractor_{{ $index }}">Contractor/Vendor</label>
            </div>
            <div class="col-md-3">
                <div class="form-switch mt-1">
                    <input class="form-check-input toggle-active-status" type="checkbox"
                        name="active_status[{{ $index }}]" id="active_status_{{ $index }}" value="1" {{
                        $emptypes['active_status']==1 ? 'checked' : '' }}>
                    <label class="form-check-label ms-2 small" for="active_status_{{ $index }}">
                        <span class="status-label">{{ $emptypes['active_status'] == 1 ? 'Active' : 'Inactive' }}</span>
                    </label>
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

<script src="/lib/js/page-scripts/employee-type.js"></script>

@endsection
@extends('layouts.layoutMaster')
@section('title', 'DataTables - Advanced Tables')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection
<link rel="stylesheet" href="/lib/css/page-styles/corporate-hl1.css">

@section('content')

<div class="row">
    <div class="col-3">
        <div class="search-container">
            <label for="customSearchInput" class="visually-hidden">Search:</label>
            <input type="search" id="customSearchInput" placeholder="Search Departments" class="form-control" />
        </div>
    </div>
    <div class="col-6 d-flex justify-content-end">
        <div class="position-relative" style="height: 50px;">
            <button class="btn btn-primary btn-sm position-absolute top-0 end-0 m-3 text-white" id="openPanelButton"
                style="margin-right:27px;">Add New Department</button>
        </div>
        <button id="exportExcel" style="border: none;">
            <i class="fa-solid fa-file-excel" style="font-size: 30px;"></i>
        </button>
    </div>
</div>
<div class="card">
    <h5 class="card-header" style="background-color:rgb(107, 27, 199); color: white; font-size:15px;">

    </h5>
    <div class="card-body">
    </div>
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table">
            <thead>
                <tr>
                    <th>Department name</th>
                    <th>Departmnt code</th>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddEmployeeType"
    aria-labelledby="offcanvasAddEmployeeTypeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAddEmployeeTypeLabel">Add New
            Department</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"
            id="closePanelButton"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="add-new-record pt-0 row g-2" id="form-add-new-record" method="post" action="/hl1create">
            @csrf
            <div class="col-sm-12">
                <label class="form-label" for="hl1_name">Department Name</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                    <input type="text" id="hl1_name" class="form-control" name="hl1_name"
                        placeholder="Enter department name" required>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="hl1_code">Department Code</label>
                <input type="text" class="form-control" id="hl1_code" name="hl1_code"
                    placeholder="Enter department code" required>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="active_status">Status</label>
                <div class="form-switch mt-1">
                    <input class="form-check-input toggle-active-status" type="checkbox" name="active_status"
                        id="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
            </div>
            <div class="col-sm-12">
                <button type="reset" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary me-sm-4 me-1">Submit</button>
            </div>
            <input type="hidden" name="corporate_id" value="{{$corporate_id}}">
            <input type="hidden" name="location_id" value="{{$location_id}}">
            <input type="hidden" name="corporate_admin_user_id" value="{{$corporate_admin_user_id}}">
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditEmployeeType"
    aria-labelledby="offcanvasEditEmployeeTypeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEditEmployeeTypeLabel">Edit
            Department</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-3">
                <label for="edit_hl1_name" class="form-label">Department
                    Name</label>
                <input type="text" class="form-control" id="edit_hl1_name" name="hl1_name" required>
            </div>
            <div class="mb-3">
                <label for="edit_hl1_code" class="form-label">Department
                    Code</label>
                <input type="text" class="form-control" id="edit_hl1_code" name="hl1_code" required>
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
            <button type="reset" class="btn btn-secondary secondary" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this entry?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="deleteButton" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
<script>
    var dataSet = @json($hl1 ?? []);
</script>
<script src="/lib/js/page-scripts/corporate-hl1.js"></script>
@endsection
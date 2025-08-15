@extends('layouts/layoutMaster')
@section('title', 'Medical Condition')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection
@section('content')
<div class="card">
  <div class="d-flex justify-content-between align-items-center card-header">
    <button class="btn btn-secondary add-new btn-primary waves-effect waves-light" tabindex="0"
      aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
      <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New</span></span>
    </button>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Medical
          Condition</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form id="addMedicalConditionForm">
          <div class="row">
            <div class="col mb-4">
              <label for="condition_name" class="form-label">Medical Condition
                name <span class="text-danger">*</span></label>
              <input type="text" id="condition_name" name="condition_name" class="form-control"
                placeholder="Enter Medical Condition name" required>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="row g-4">
            <div class="col mb-0">
              <label for="status-switch" class="form-label">Status</label>
              <div class="demo-vertical-spacing">
                <label class="switch">
                  <input type="checkbox" class="switch-input" id="status-switch" checked>
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                  <span class="switch-label" id="status-label">Active</span>
                </label>
              </div>
            </div>
          </div>
          <br><br>
          <button type="submit" class="btn btn-primary" id="add-medicalcondition">
            <span class="btn-text">Save Changes</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
          <button type="button" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
      </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Edit Medical
          Condition</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form id="editMedicalConditionForm">
          <div class="row">
            <div class="col mb-4">
              <label for="condition_name_edit" class="form-label">Medical
                Condition <span class="text-danger">*</span></label>
              <input type="text" id="condition_name_edit" name="condition_name_edit" class="form-control"
                placeholder="Edit Name" required>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="row g-4">
            <div class="col mb-0">
              <label for="status_switch_edit" class="form-label">Status</label>
              <div class="demo-vertical-spacing">
                <label class="switch">
                  <input type="checkbox" class="switch-input" id="status_switch_edit">
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                  <span class="switch-label" id="status-label-edit">Active</span>
                </label>
              </div>
            </div>
          </div>
          <br><br>
          <button type="submit" class="btn btn-primary" id="edit-medicalcondition">
            <span class="btn-text">Save Changes</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
          <button type="button" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
          <input type="hidden" id="edit_condition_id">
        </form>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive pt-0">
    <table class="datatables-basic table">
      <thead>
        <tr class="advance-search mt-3">
          <th colspan="3" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted" id="employeeTypeLabel">List of Medical
                Condition</span>
            </div>
          </th>
        </tr>
        <tr>
          <th>Medical Condition</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<script src="/lib/js/page-scripts/medical_condition.js"></script>
@endsection
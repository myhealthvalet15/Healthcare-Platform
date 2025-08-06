@extends('layouts/layoutMaster')

@section('title', 'Corporate OHC')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'

])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([

'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js'
])
@endsection

@section('content')

<!-- Default -->
<div class="row">
  <div class="col-12 mb-6">
    <div class="bs-stepper wizard-numbered mt-2">
      <div class="bs-stepper-header">

        <div class="step" data-target="#account-details">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">Corporate OHC</span>
            </span>
          </button>
        </div>

        <div class="line">
          <i class="ti ti-chevron-right"></i>
        </div>

        <div class="step" data-target="#personal-info">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">OHC Pharmacy</span>
            </span>
          </button>
        </div>

      </div>
      <div class="bs-stepper-content">
        <!-- Account Details -->
        <div id="account-details" class="content">
          <div class="card">
            <div class="card-datatable table-responsive pt-0">
              <table class="datatables-basic table">
                <thead>
                  <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                      <div class="d-flex justify-content-between align-items-center">
                        <!-- Text on the left side -->
                        <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Pharmacy</span>
                      </div>
                    </th>
                  </tr>
                  <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <!-- Modal to add new record -->
          <div class="offcanvas offcanvas-end" id="add-new-record">
            <div class="offcanvas-header border-bottom">
              <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-grow-1">
              <div class="col-sm-12">
                <label class="form-label" for="basicFullname">OHC Name</label>
                <div class="input-group input-group-merge">
                  <span id="basicFullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                  <input type="text" id="ohcname" class="form-control dt-full-name" name="ohcname" aria-describedby="basicFullname2" />
                </div>
              </div>
              <div class="row g-4">
                <div class="col mb-0">
                  <label for="emailBasic" class="form-label">Status</label>
                  <div class="demo-vertical-spacing">
                    <label class="switch">
                      <input type="checkbox" class="switch-input is-valid" id="status-switch" checked="true">
                      <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                      </span>
                      <span class="switch-label" id="status-label">Active</span>
                    </label>
                  </div>
                </div>
              </div>
              <br /><br />
              <div class="col-sm-12">
                <button type="button" class="btn btn-primary" id="add-corporate-ohc">Save
                  Changes</button> <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
              </div>
            </div>
          </div>
          <div class="offcanvas offcanvas-end" id="edit-new-record">
            <div class="offcanvas-header border-bottom">
              <h5 class="offcanvas-title" id="exampleModalLabel">Edit Record</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-grow-1">
              <div class="row">
                <div class="col mb-4">
                  <label for="in-name" class="form-label">OHC Name </label>
                  <input type="text" id="ohc_name_edit" class="form-control">
                </div>
              </div>
              <div class="row g-4">
                <div class="col mb-0">
                  <label for="emailBasic" class="form-label">Status</label>
                  <div class="demo-vertical-spacing">
                    <label class="switch">
                      <input type="checkbox" class="switch-input" id="status_switch_edit" checked="true">
                      <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                      </span>
                      <span class="switch-label" id="status-label-edit">Active</span>
                    </label>
                  </div>
                </div>
              </div>
              <br /><br />
              <div class="col-sm-12">
                <button type="button" class="btn btn-primary" id="edit-corporateOHC">Save
                  Changes</button>
                <button type="reset" class="btn btn-label-danger waves-effect"
                  data-bs-dismiss="offcanvas">Cancel</button>
              </div>
            </div>
          </div>
          <!--/ DataTable with Buttons -->
        </div>
      </div>
      <!-- Personal Info -->
      <div id="personal-info" class="content">
        <div class="card">
          <div class="card-datatable table-responsive pt-0">
            <table id="personal-info-table" class="datatables-basic-pi table">
              <thead>
                <tr class="advance-search mt-3">
                  <th colspan="9" style="background-color:rgb(107, 27, 199);">
                    <div class="d-flex justify-content-between align-items-center">
                      <!-- Text on the left side -->
                      <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Pharmacy</span>
                    </div>
                  </th>
                </tr>
                <tr>
                  <th>Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <!-- Modal to add new record  for Personal tab-->
        <div class="offcanvas offcanvas-end" id="add-new-record1">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body flex-grow-1">
            <div class="col-sm-12">
              <label class="form-label" for="basicFullname">Pharmacy Name</label>
              <div class="input-group input-group-merge">
                <span id="basicFullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                <input type="text" id="pharmacy_name" class="form-control dt-full-name" name="pharmacy_name" aria-describedby="basicFullname2" />
              </div>
            </div>
            <div class="col-sm-12 mt-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="main_pharmacy" name="main_pharmacy">
                <label class="form-check-label" for="main_pharmacy">Main Store</label>
            </div>
        </div>
            <div class="row g-4">
              <div class="col mb-0">
                <label for="emailBasic" class="form-label">Status</label>
                <div class="demo-vertical-spacing">
                  <label class="switch">
                    <input type="checkbox" class="switch-input is-valid" id="status-switch" checked="true">
                    <span class="switch-toggle-slider">
                      <span class="switch-on"></span>
                      <span class="switch-off"></span>
                    </span>
                    <span class="switch-label" id="status-label">Active</span>
                  </label>
                </div>
              </div>
            </div>
            <br /><br />
            <div class="col-sm-12">
              <button type="button" class="btn btn-primary" id="add-corporate-pharmacy">Save
                Changes</button> <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
          </div>
        </div>
        <!--/ DataTable with Buttons -->
        <div class="offcanvas offcanvas-end" id="edit-new-record1">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Edit Record</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body flex-grow-1">
            <div class="row">
              <div class="col mb-4">
                <label for="in-name" class="form-label">Pharmacy Name </label>
                <input type="text" id="pharmacy_name_edit" class="form-control">
              </div>
            </div>
            <div class="col-sm-12 mt-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="main_pharmacy_edit" name="main_pharmacy_edit">
                <label class="form-check-label" for="main_pharmacy_edit">Main Store</label>
            </div>
        </div>
            <div class="row g-4">
                <div class="col mb-0">
                  <label for="emailBasic" class="form-label">Status</label>
                  <div class="demo-vertical-spacing">
                    <label class="switch">
                      <input type="checkbox" class="switch-input" id="status_switch_edit1" checked="true">
                      <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                      </span>
                      <span class="switch-label" id="status-label-edit1">Active</span>
                    </label>
                  </div>
                </div>
              </div>
            <br /><br />
            <div class="col-sm-12">
              <button type="button" class="btn btn-primary" id="edit-pharmacyOHC">Save
                </button>
              <button type="reset" class="btn btn-label-danger waves-effect"
                data-bs-dismiss="offcanvas">Cancel</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script src="/lib/js/page-scripts/corporate-ohc.js"></script>
@endsection
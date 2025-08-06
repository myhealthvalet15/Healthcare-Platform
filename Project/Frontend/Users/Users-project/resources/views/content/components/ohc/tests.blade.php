@extends('layouts/layoutMaster')
@section('title', 'Test List')
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
<link rel="stylesheet" href="/lib/css/page-styles/tests.css">
<script>
  var $apiMenuData = @json($apiMenuData ?? null);
</script>
<div class="card">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form id="testFilterForm">
            <div class="d-flex flex-wrap align-items-end gap-3">
              <div class="mb-3">
                <label for="searchInput" class="form-label">Employee Name /
                  Id</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Employee Name or Employee Id">
              </div>
              <div class="mb-3">
                <label for="fromDate" class="form-label">From Date</label>
                <input class="form-control flatpickr-date small-date" type="text" id="fromDate"
                  placeholder="Select From Date" />
              </div>
              <div class="mb-3">
                <label for="toDate" class="form-label">To Date</label>
                <input class="form-control flatpickr-date small-date" type="text" id="toDate"
                  placeholder="Select To Date" />
              </div>
              <div class="mb-3 wide-select">
                <label for="filterTestSelect" class="form-label">Tests</label>
                <div class="select2-primary">
                  <select id="filterTestSelect" class="select2 form-select" multiple>
                  </select>
                </div>
              </div>
              <div class="mb-3 d-flex gap-2">
                <button type="button" id="applyFilters" class="btn btn-primary btn-md">
                  Apply Filters
                </button>
                <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-md">
                  Clear Filters
                </button>
              </div>
            </div>
          </form>
        </div>
        <div class="mb-3 d-flex justify-content-end me-3">
          <a href="/ohc/add-test" class="btn btn-primary btn-md" id="addtest">
            Add Test
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="col-lg-4 col-md-6">
  <div class="mt-4">
    <div class="modal fade" id="prescriptionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl custom-modal-width" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="prescriptionModalLabel1">Prescriptions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="prescription-container">
              <div class="doctor-header">
                <span>Dr. John Doe</span>
                <span class="prescription-id">MPBoAmzVFigh
                  0504202500013</span>
              </div>
              <div class="patient-info">
                <div>Randall House - 27 / Other
                  (EMP00065)</div>
                <!-- <div class="icons">
                  <i class="fas fa-notes-medical"></i>
                  <i class="fas fa-vial"></i>
                  <i class="fas fa-envelope"></i>
                  <i class="fas fa-print"></i>
                  <i class="fas fa-edit"></i>
                  <i class="fas fa-trash text-danger"></i>
                </div> -->
              </div>
              <table>
                <thead>
                  <tr>
                    <th>DRUG NAME - STRENGTH (TYPE)</th>
                    <th>DAYS</th>
                    <th>🌞</th>
                    <th>🔴</th>
                    <th>🌅</th>
                    <th>🌙</th>
                    <th>AF/BF</th>
                    <th>REMARKS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="drug-name">Quia - 200mg
                      (Drops)</td>
                    <td>2</td>
                    <td>1</td>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>With Food</td>
                    <td>Test</td>
                  </tr>
                  <tr>
                    <td class="drug-name">Molestias -
                      250mg (Foam)</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>0</td>
                    <td>1</td>
                    <td>After Food</td>
                    <td>Ache</td>
                  </tr>
                  <tr>
                    <td class="drug-name">Volini - Spray
                      <i class="fas fa-external-link-alt"></i>
                    </td>
                    <td>1</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>1</td>
                    <td>Before Food</td>
                    <td>Test</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="table-responsive text-nowrap">
    <table class="table table">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Name (Age) - Employee ID</th>
          <th>Department</th>
          <th>Test List</th>
          <th>Test Results</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0"></tbody>
    </table>
  </div>
</div>
<script src="/lib/js/page-scripts/tests.js"></script>
@endsection
@extends('layouts/layoutMaster')
@section('title', 'Completed Request List')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/complete-requests.css">
<div id="issueModal" class="modal" style="display:none;">
  <div class="modal-content">
    <h3>Confirm Prescription Issue</h3>
    <div id="modalBody"></div>
    <div style="margin-top: 20px;">
      <button id="confirmIssue" class="btn btn-primary">Issue</button>
      <button id="cancelIssue" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>
<div id="editIssueModal" class="modal" style="display:none;">
  <div class="modal-content">
    <h3>Confirm Prescription Edit</h3>
    <div id="editModalBody"></div>
    <div style="margin-top: 20px;">
      <button id="confirmEditIssue" class="btn btn-primary">Save Changes</button>
      <button id="cancelEditIssue" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="dt-row-grouping table">
    </table>
  </div>
</div>
<hr class="my-12">
<script src="/lib/js/page-scripts/complete-requests.js"></script>
@endsection
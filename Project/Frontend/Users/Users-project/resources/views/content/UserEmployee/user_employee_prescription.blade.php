@extends('layouts/layoutMaster')
@section('title', 'View - Prescription List')
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
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/user_employee_prescription.css">
<div class="card">
  <div class="d-flex justify-content-between align-items-center mb-3 px-3" id="add-prescription-container"
    style="margin-top:12px;">
    <div id="employee-info-display" class="fw-bold text-start"></div>
    <div id="add-prescription-button" class="text-end"></div>
  </div>
  <div class="card-datatable table-responsive">
    <table class="dt-row-grouping table">
    </table>
  </div>
</div>
<hr class="my-12">
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header" style="line-height: 35px;background-color: rgb(107, 27, 199); color:#fff;">
        <h5 id="printModalLabel" style="color:#fff;">Print Prescription</h5>
      </div>
      <div class="modal-body">
        <div id="printOptions" class="print-selection">
          <select id="printType" class="form-select">
            <option value="a4h">A4 With Header</option>
            <option value="a4">A4 Without Header</option>
            <option value="a5h">A5 With Header</option>
            <option value="a5">A5 Without Header</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <input type="button" value="Print" onclick="printType()" style="width: 96px;
    height: 37px;
    border-radius: 4px;
    color: #fff;
    border: none;background-color: #7367f0;" />
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: rgb(107, 27, 199); color: #fff;">
        <h5 class="modal-title" id="printModalLabel">Prescription Image</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 text-center">
        <img id="prescriptionModalImage" src="" class="img-fluid w-100"
          style="object-fit: contain; max-height: 80vh;" />
      </div>
    </div>
  </div>
</div>
<script>
  const employeeId = "{{ session('employee_id') }}".toLowerCase();
  const employeeDetailsUrl = "{{ route('employee-user-details') }}?employee_id=" + employeeId;
  const employeeName = "{{ session('employee_name') }}";
  const employeeGender = "{{ session('employee_gender') }}";
  const employeeAge = "{{ session('employee_age') }}";
</script>
<script src="/lib/js/page-scripts/user_employee_prescription.js"></script>
@endsection
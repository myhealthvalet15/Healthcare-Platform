@extends('layouts/layoutMaster')
@section('title', 'OTC LIST')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
<style>
    .highlight-row {
    background-color: #ffeb3b !important; /* Yellow background color */
    color: #000; /* Black text color */
}#employeeSummary p {
    margin: 0.5rem 0;
    font-size: 1rem;
}
#employeeSummary i {
    color: #0d6efd;
    margin-right: 6px;
}
#employeeSummary .text-end p {
    text-align: right;
}
.card-body {
    padding: 1.5rem;
    background: linear-gradient(to right, #fdfdff, #f2f6fc);
    border-radius: 12px;
}
.datatables-basic td {
  vertical-align: top !important;
}
</style>
<script>
    const employeeId = @json(session('employee_id'));
    const employeeDetailsUrl = @json(route('employee-user-details'));
</script>
<script src="{{ asset('Bhava/JS/employee-details.js') }}?v={{ time() }}"></script>
<script src="{{ asset('Bhava/JS/otc.js') }}?v={{ time() }}"></script>
@endsection
@section('content')
<div class="card">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-CfG4vD60FqN8o8xDhYOYX0+0V+j0ZWB3mkzM5tPpnL3lmUg4x3fPjZblA4YHXmsZRYZFbZ9wQ6s8NlnUZtd4cw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="card shadow-sm border-0 mb-4" id="employeeSummaryCard">
  <div class="card-body bg-light rounded" id="employeeSummary">
    <div class="row align-items-start justify-content-between">        
      <div class="col-md-6">
        <p><i class="fa-solid fa-user"></i> <span id="empName"></span> - <span id="empId"></span></p>
        <p><i class="fa-solid fa-hourglass-half"></i> <span id="empAge"></span> / <span id="empGender"></span></p>
      </div>
      <div class="col-md-6 text-end">
        <p><i class="fa-solid fa-sitemap"></i> <span id="empDepartment"></span> - <span id="empDesignation"></span></p>
        <p><i class="fa-solid fa-user-tag"></i> <span id="empType"></span> / <span id="empdateOfJoining"></span></p>
      </div>      
    </div>
  </div>
</div>
   <div class="card-datatable table-responsive pt-0" style="margin-top:-30px;">
    <table class="datatables-basic table">
      <thead>
        <tr class="advance-search mt-3">
          <th colspan="9" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of OTC</span>
            </div>
          </th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>  
          <th></th>

        </tr>
      </thead>
    </table>
  </div>
</div>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">

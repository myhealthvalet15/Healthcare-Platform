@extends('layouts/layoutMaster')

@section('title', 'Vendor List')

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
<style>
.dt-buttons {
    display: flex;        /* Flexbox container */
    justify-content: flex-start; /* Align buttons to the left */
}

.dt-buttons .btn {
    margin-right: 10px; /* Optional: Adds space between the buttons */
}

/* Optional: To remove wrap and keep everything in one line, use this */
.dt-buttons {
    flex-wrap: nowrap; /* Ensure no wrapping */
}
</style>
<!-- Default -->
<div class="card">
  <div class="card-datatable table-responsive pt-0">

    <table class="datatables-basic table">
    
    <thead>
      

        <tr class="advance-search mt-3">
          <th colspan="4" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Vendor</span>
            </div>
          </th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>      
        </tr>
      </thead>
    </table>
  </div>
</div>
<!-- Modal to add new record -->
<div class="offcanvas offcanvas-end" id="add-new-record">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="exampleModalLabel">Add New Vendor Details</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Vendor Name </label>
        <input type="text" id="vendor_name" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">PO Number </label>
        <input type="text" id="po_number" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="po_value" class="form-label">PO Value </label>
        <input type="number" id="po_value" class="form-control">
      </div>
    </div> 
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Date </label>
        <input type="date" id="po_date" class="form-control">
      </div>
    </div> 
    

    <div class="col-sm-12">
      <button type="button" class="btn btn-primary" id="add-vendor">Save
        &nbsp;&nbsp;</button> <button type="reset" class="btn btn-outline-secondary"
        data-bs-dismiss="offcanvas">Cancel</button>
    </div>
  </div>
</div>

<!--/ DataTable with Buttons -->
</div>
</div>
<!-- Personal Info -->

</div>
</div>
</div>
</div>
<script>
    var ohcRights = {!! json_encode($ohcRights) !!};
</script>


 <script src="/lib/js/page-scripts/vendor.js?v=time()"></script>

@endsection
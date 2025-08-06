@extends('layouts/layoutMaster')

@section('title', 'Bio Medical Waste')

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
    .legend {
        display: flex;
        gap: 20px;
        justify-content: space-between;
        width: 97%;
    }
    .legend-column {
        display: flex;
        flex-direction: column;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .color-box {
        width: 15px;
        height: 15px;
        border-radius: 3px;
        display: inline-block;
    }
    .red { background-color: red; }
    .yellow { background-color: yellow; }
    .blue { background-color: blue; }
    .white { background-color: white; border: 1px solid #ccc; }
    table th:nth-child(4) {
            color: red;
        }

        /* For Yellow column title */
        table th:nth-child(5) {
            color: yellow;
        }

        /* For Blue column title */
        table th:nth-child(6) {
            color: blue;
        }

        /* For White column title */
        table th:nth-child(7) {
            color: grey;
            background-color: black; /* Optional: set a background to make it visible */
        }
</style>
<!-- <pre>
    {{ print_r($ohcRights, true) }}
</pre> -->


<!-- Default -->
<div class="card">
  <div class="card-datatable table-responsive pt-0">

    <table class="datatables-basic table">
    
    <thead>
      

        <tr class="advance-search mt-3">
          <th colspan="9" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Bio-Medical Waste</span>
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
    <h5 class="offcanvas-title" id="exampleModalLabel">New Bio-Medical Waste</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Date </label>
        <input type="text" id="date" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Red(grams) </label>
        <input type="text" id="red" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Yellow(grams) </label>
        <input type="text" id="yellow" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Blue(grams) </label>
        <input type="text" id="blue" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">White(grams) </label>
        <input type="text" id="white" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Issued By </label>
        <input type="text" id="issued_by" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Recieved By </label>
        <input type="text" id="received_by" class="form-control">
      </div>
    </div>

    <div class="col-sm-12">
      <button type="button" class="btn btn-primary" id="add-bio-waste">Save
        &nbsp;&nbsp;</button> <button type="reset" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
    </div>
  </div>
</div>
<div class="offcanvas offcanvas-end" id="edit-new-record">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="exampleModalLabel">Edit Bio-Medical Waste</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Date </label>
        <input type="text" id="date_edit" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Red(grams) </label>
        <input type="text" id="red_edit" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Yellow(grams) </label>
        <input type="text" id="yellow_edit" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Blue(grams) </label>
        <input type="text" id="blue_edit" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">White(grams) </label>
        <input type="text" id="white_edit" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Issued By </label>
        <input type="text" id="issued_by_edit" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col mb-4">
        <label for="in-name" class="form-label">Recieved By </label>
        <input type="text" id="received_by_edit" class="form-control">
      </div>
    </div>


    <br /><br />
    <div class="col-sm-12">
      <button type="button" class="btn btn-primary" id="edit-BioMedicalWaste">Save
      </button>
      <button type="reset" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
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
<script src="/lib/js/page-scripts/bio-medical-waste.js"></script>
@endsection
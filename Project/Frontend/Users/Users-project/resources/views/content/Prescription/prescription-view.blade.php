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
<link rel="stylesheet" href="/lib/css/page-styles/prescription-view.css">
<script>
    var ohcRights = {!! json_encode($ohcRights) !!};
</script>
@section('content')

<!-- Hospital Modal -->
<div id="hospitalModal" class="custom-modal">
  <div class="custom-modal-content">
    <span class="close-button" onclick="closeModal('hospitalModal')">&times;</span>
    <h3>Outpatient Details</h3>
    <div id="hospitalModalBody" class="hospital-info">
      Loading...
    </div>
  </div>
</div>



<!-- Lab Modal -->
<div id="testListModal" class="custom-modal">
  <div class="custom-modal-content">
    <span class="close-button" onclick="closeModal('testListModal')">&times;</span>
    <h3>Test Details</h3>
    <p id="testListModal">Loading...</p>
  </div>
</div>

<div class="card">
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
       
        <input type="button" value="Print" onclick="printType()"  style="width: 96px;
    height: 37px;
    border-radius: 4px;
    color: #fff;
    border: none;background-color: #7367f0;"  />

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<script src="/lib/js/page-scripts/prescription-view.js"></script>


@endsection
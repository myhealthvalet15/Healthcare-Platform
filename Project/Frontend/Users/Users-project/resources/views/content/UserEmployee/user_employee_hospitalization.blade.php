@extends('layouts/layoutMaster')
@section('title', 'Hospitalization List')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',

])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([

'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',

])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js'
])
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('lib/js/page-scripts/hospitalization-details.js') }}?v={{ time() }}"></script>

@section('content')
<!-- Default -->

<div class="card">
  
  <div class="card-datatable table-responsive pt-0" style="margin-top:10px;">
    <table class="datatables-basic table">    
    <thead>     
        <tr class="advance-search mt-3">
          <th colspan="5" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Hospitalization</span>
            </div>
          </th>
        </tr>
        <tr>
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
<!-- Modal for Viewing Reports -->

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Attachment Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul id="reportList" class="list-group mb-3"></ul>
        <div id="reportPreview" class="text-center">
          <img id="previewImage" src="" alt="Preview" class="img-fluid d-none" style="max-height: 400px;">
        </div>
        <div id="downloadBtnWrapper" class="text-center mt-3">
          <a id="downloadAttachment" href="#" class="btn btn-success" download target="_blank">Download</a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
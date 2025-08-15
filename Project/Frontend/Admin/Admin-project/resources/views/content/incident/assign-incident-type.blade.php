@extends('layouts/layoutMaster')
@section('title', 'Assign Incident Type')
@section('content')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/ui-modals.js', 'resources/assets/js/questions.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
<link rel="stylesheet" href="/lib/css/page-styles/assign-incident-type.css">
<div class="card">
  <div class="d-flex justify-content-between align-items-center card-header">
    <h5 class="mb-0">Incident Types</h5>
    <button type="button" class="btn btn-primary" id="saveChangesBtn" style="display: none;">
      <i class="fas fa-save me-1"></i>Save Changes
    </button>
  </div>
  <div class="table-responsive text-nowrap">
    <div id="preloader" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p>Fetching Incident Types...</p>
    </div>
    <div class="incident-types-container p-4">
      <div class="row">
      </div>
    </div>
  </div>
</div>
<hr class="my-12">
<script src="/lib/js/page-scripts/assign-incident-type.js"></script>
@endsection
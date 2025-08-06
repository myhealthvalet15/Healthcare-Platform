@extends('layouts/layoutMaster')
@section('title', 'User Profile - Profile')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'
])
@endsection
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/pages-profile.js'])
@endsection
@section('content')
<div class="card">
  <div class="card-body">
    <h2>Welcome to Employee Login</h2>
    <div id="hra-templates-wrapper" class="mt-3">
      <h5>Assigned HRA Templates</h5>
      <div id="hra-templates">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100px;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <span class="ms-2">Loading HRA Templates...</span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="eventModalLabel" style="color:#fff;margin-bottom:15px;">🎉 You're Cordially
          Invited</h5>
      </div>
      <div class="modal-body" id="eventModalBody">
        <p class="text-center text-muted">Fetching royal invitations...</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success" onclick="handleEventResponse('yes')">Yes, I will attend</button>
        <button type="button" class="btn btn-outline-secondary" onclick="handleEventResponse('no')">No, maybe
          later</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="factorScoresModal" tabindex="-1" aria-labelledby="factorScoresModalLabel" aria-hidden="true"
  data-bs-backdrop="true" data-bs-keyboard="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded">
      <div class="modal-header bg-primary text-white border-0 pb-4">
        <h5 class="modal-title" id="factorScoresModalLabel" style="color:#fff;">📊 Factor Scores Analysis</h5>
      </div>
      <div class="modal-body pt-4" id="factorScoresModalBody">
        <p class="text-center text-muted">Loading factor scores...</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script src="/lib/js/page-scripts/user_add_hospitalization.js"></script>
<script src="/lib/js/page-scripts/user_dashboard.js"></script>
@endsection
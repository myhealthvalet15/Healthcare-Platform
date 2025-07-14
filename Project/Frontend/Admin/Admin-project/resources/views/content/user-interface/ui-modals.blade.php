@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Modals - UI elements')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/ui-modals.js'])
@endsection

@section('content')
<!-- Bootstrap modals -->

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
  Launch modalss
</button>

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-4">
            <label for="nameBasic" class="form-label">Name</label>
            <input type="text" id="nameBasic" class="form-control" placeholder="Enter Name">
          </div>
        </div>
        <div class="row g-4">
          <div class="col mb-0">
            <label for="emailBasic" class="form-label">Email</label>
            <input type="email" id="emailBasic" class="form-control" placeholder="xxxx@xxx.xx">
          </div>
          <div class="col mb-0">
            <label for="dobBasic" class="form-label">DOB</label>
            <input type="date" id="dobBasic" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!--/ Extended Modals -->
@endsection
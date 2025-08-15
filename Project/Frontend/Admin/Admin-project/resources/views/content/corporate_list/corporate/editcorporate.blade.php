@extends('layouts/layoutMaster')
@section('title', 'My Dashboard')
@section('description', 'Description of my dashboard')
@section('content')
{{-- Corporate ID Display --}}
<div class="container row">
    <div class="d-flex justify-content-between align-items-center col-md-12 mb-3">
        <div class="col-md-5">
            <p class="mb-2 text-muted">
                Corporate &raquo; Corporate List
            </p>
            <h3 class="text-primary mb-3">
                <strong>{{ $corporate['corporate_name'] }}</strong>
                <p class="text-dark small">Corporate Address Details</p>
            </h3>
        </div>
        <div class="col-md-7 text-end">
            <a href="{{ route('corporate.edit', $corporate['id']) }}" class="btn btn-dark btn-sm"
                data-bs-toggle="tooltip" title="Edit Corporate Details">
                <i class="fas fa-building"></i>
            </a>
            <a href="{{ route('corporate.editAddress',['id' => $corporate['id'], 'corporate_id' => $corporate['corporate_id']]) }}"
                class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Edit corporaate address Details">
                <i class="fas fa-map-marker-alt"></i>
            </a>
            @if($corporate['corporate_id'] == $corporate['location_id'])
            <a href="{{ route('corporate.editEmployeeTypes',['id' => $corporate['id'], 'corporate_id' => $corporate['corporate_id']]) }}" class="btn btn-primary btn-sm"
                data-bs-toggle="tooltip" title="Edit Employee Types">
                <i class="fas fa-users"></i>
            </a>
            <a href="{{ route('corporate.editComponents',['id' => $corporate['id'], 'corporate_id' => $corporate['corporate_id']]) }}" class="btn btn-success btn-sm"
                data-bs-toggle="tooltip" title="edit components Details">
                <i class="fas fa-home"></i>
            </a>
            @endif
            <a href="{{ route('corporate.editAdminUsers',['id' => $corporate['id'], 'corporate_id' => $corporate['corporate_id']]) }}"
                class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                title="edit Corporate super  Admin">
                <i class="fas fa-user-tie"></i>
            </a>
        </div>
    </div>
</div>
{{-- Form Section --}}
<div id="form-step-1" class="step-form p-4 shadow rounded">
    <form id="step-1-form" method="post" action="{{ route('corporate.upupdate', $corporate['id']) }}"
        class="mb-4 row g-3 needs-validation" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        {{-- Corporate Name --}}
        <div class="col-sm-6">
            <label for="corporate_name" class="form-label fw-bold">Corporate Name</label>
            <input type="text" id="corporate_name" name="corporate_name" value="{{ $corporate['corporate_name'] }}"
                class="form-control" placeholder="Enter the Corporate Name" required>
        </div>
        {{-- Corporate ID (Read-Only) --}}
        <div class="col-sm-6">
            <label for="corporate_id" class="form-label fw-bold">Corporate ID</label>
            <input type="text" id="corporate_id" name="corporate_id" class="form-control bg-light"
                value="{{ $corporate['corporate_id'] }}" readonly required>
        </div>
        {{-- Corporate Number --}}
        <div class="col-sm-6">
            <label for="corporate_no" class="form-label fw-bold">Corporate Number</label>
            <input type="text" id="corporate_no" name="corporate_no" value="{{ $corporate['corporate_no'] }}"
                class="form-control" placeholder="Enter the Corporate Number" required>
        </div>
        {{-- Display Name --}}
        <div class="col-sm-6">
            <label for="display_name" class="form-label fw-bold">Display Name</label>
            <input type="text" id="display_name" name="display_name" value="{{ $corporate['display_name'] }}"
                class="form-control" placeholder="Enter Display Name">
        </div>
        {{-- Corporate Registration Number --}}
        <div class="col-sm-6">
            <label for="registration_no" class="form-label fw-bold">Corporate Registration No</label>
            <input type="text" id="registration_no" name="registration_no" value="{{ $corporate['registration_no'] }}"
                class="form-control" placeholder="Enter Registration Number" required>
        </div>
        {{-- Industry Segment --}}
        <div class="col-sm-6">
            <label for="industry_segment" class="form-label fw-bold">Industry Segment</label>
            <input type="text" id="industry_segment" name="industry_segment"
                value="{{ $corporate['industry_segment'] }}" class="form-control" placeholder="Enter Industry Segment">
        </div>
        {{-- Profile Image --}}
        <div class="col-sm-12">
            <label for="prof_image" class="form-label fw-bold">Profile Image</label>
            <input type="file" id="prof_image" value="{{ $corporate['prof_image'] }}" name="prof_image" class="form-control">
        </div>
        {{-- Company Profile --}}
        <div class="col-sm-6">
            <label for="company_profile" class="form-label fw-bold">Company Profile</label>
            <input id="company_profile" name="company_profile" value="{{ $corporate['company_profile'] }}"
                class="form-control" placeholder="Enter company profile details">
        </div>
        {{-- Industry --}}
        <div class="col-sm-6">
            <label for="industry" class="form-label fw-bold">Industry</label>
            <input id="industry" name="industry" value="{{ $corporate['industry'] }}" class="form-control"
                placeholder="Enter Industry">
        </div>
        {{-- Created By --}}
        <div class="col-sm-6">
            <label for="created_by" class="form-label fw-bold">Created By</label>
            <input type="text" id="created_by" name="created_by" value="{{ $corporate['created_by'] }}"
                class="form-control" placeholder="Enter creator's name" required>
        </div>
        {{-- GSTIN --}}
        <div class="col-sm-6">
            <label for="gstin" class="form-label fw-bold">Gstin</label>
            <input type="text" id="gstin" name="gstin" value="{{ $corporate['gstin'] }}" class="form-control"
                placeholder="Enter Gstin" required>
        </div>
        {{-- Discount --}}
        <div class="col-sm-6">
            <label for="discount" class="form-label fw-bold">Discount</label>
            <input type="text" id="discount" name="discount" value="{{ $corporate['discount'] }}" class="form-control"
                placeholder="Enter discount" required>
        </div>
        {{-- Created On --}}
        <div class="col-sm-6">
            <label for="created_on" class="form-label fw-bold">Created On</label>
            <input type="date" id="created_on" name="created_on" value="{{ $corporate['created_on'] }}"
                class="form-control" required>
        </div>
        {{-- Valid From --}}
        <div class="col-sm-6">
            <label for="valid_from" class="form-label fw-bold">Valid From</label>
            <input type="date" id="valid_from" name="valid_from" value="{{ $corporate['valid_from'] }}"
                class="form-control">
        </div>
        {{-- Valid Upto --}}
        <div class="col-sm-6">
            <label for="valid_upto" class="form-label fw-bold">Valid Upto</label>
            <input type="date" id="valid_upto" name="valid_upto" value="{{ $corporate['valid_upto'] }}"
                class="form-control">
        </div>
        {{-- Corporate Color --}}
        <div class="col-sm-6">
            <label for="corporate_color" class="form-label fw-bold">Corporate Color</label>
            <input type="color" id="corporate_color" name="corporate_color" value="{{ $corporate['corporate_color'] }}"
                class="form-control">
        </div>
        {{-- Active Status --}}
        <div class="col-sm-6 d-flex align-items-center">
            <label for="active_status" class="form-label fw-bold me-3">Active Status:</label>
            <div class="form-check form-check-inline">
                <input type="radio" id="active" name="active_status" class="form-check-input" value="1"
                    {{ ($corporate['active_status'] ?? 1) == 1 ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="inactive" name="active_status" class="form-check-input" value="0"
                    {{ ($corporate['active_status'] ?? 1) == 0 ? 'checked' : '' }}>
                <label class="form-check-label" for="inactive">Inactive</label>
            </div>
        </div>
        {{-- Action Buttons --}}
        <div class="col-12 text-center mt-4">
            <div class="mb-3">
                <a href="/terms-of-engagement" class="text-decoration-none me-3">Terms of Engagement</a>
                <a href="/privacy-statement" class="text-decoration-none">Privacy Statement</a>
            </div>
            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">Update Corporate Details</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg rounded-pill px-5 ms-3">Back</a>
        </div>
    </form>
</div>
@endsection
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
        };
        const successMessage = $('#toastr-success').data('message');
        if (successMessage) {
            toastr.success(successMessage);
        }
        const errorMessage = $('#toastr-error').data('message');
        if (errorMessage) {
            toastr.error(errorMessage);
        }
    });
</script>
@extends('layouts/layoutMaster')
@section('title', 'Events - Create Event')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/form-layouts.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Create Event</h5>
    </div>
    <div class="card-body">
        <form id="eventForm" method="POST" action="javascript:void(0);">

            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="event_name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                    <div class="invalid-feedback d-block" id="event_name_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="from_date" class="form-label">From Date & Time</label>
                    <input type="text" class="form-control flatpickr-date-time" id="from_date" name="from_date"
                        placeholder="Select start date & time" required>
                    <div class="invalid-feedback d-block" id="from_date_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="to_date" class="form-label">To Date & Time</label>
                    <input type="text" class="form-control flatpickr-date-time" id="to_date" name="to_date"
                        placeholder="Select end date & time" required>
                    <div class="invalid-feedback d-block" id="to_date_error"></div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="event_description" class="form-label">Event Description</label>
                    <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
                    <div class="invalid-feedback d-block" id="event_description_error"></div>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="guest_name" class="form-label">Guest Name</label>
                    <'input type="text" class="form-control" id="guest_name" name="guest_name">
                        <div class="invalid-feedback d-block" id="guest_name_error"></div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select select2" id="department" name="department">
                        <option value="">Select Department</option>
                    </select>
                    <div class="invalid-feedback d-block" id="department_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="employee_type" class="form-label">Employee Type</label>
                    <select class="form-select select2" id="employee_type" name="employee_type">
                        <option value="">Select Employee Type</option>
                    </select>
                    <div class="invalid-feedback d-block" id="employee_type_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="test" class="form-label">Test</label>
                    <select class="form-select select2" id="test" name="test">
                        <option value="">Select Test</option>
                    </select>
                    <div class="invalid-feedback d-block" id="test_error"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</div>
<script src="/lib/js/page-scripts/add-events.js"></script>
@endsection
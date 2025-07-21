@extends('layouts/layoutMaster')
@section('title', 'Add Hospitalization')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([

'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('lib/js/page-scripts/hospitalization-details.js') }}?v={{ time() }}"></script>

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Add Hospitalization</h5>
        </div>

        <div class="card-body">
            <form id="hospitalizationForm" enctype="multipart/form-data">
                @csrf

                <!-- Row 1: Hospital + Hospital Name (if other) + Doctor + Doctor Name (if other) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="hospital_id" class="form-label">Hospital</label>
                        <select name="hospital_id" id="hospital_id" class="form-select" required>
                            <option value="">-- Select Hospital --</option>
                            <option value="1">City Hospital</option>
                            <option value="2">State Medical</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="hospital_name_div" style="display:none;">
                        <label for="hospital_name" class="form-label">Hospital Name</label>
                        <input type="text" name="hospital_name" class="form-control" placeholder="Enter Hospital Name">
                    </div>
                    <div class="col-md-3">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-select" required>
                            <option value="">-- Select Doctor --</option>
                            <option value="101">Dr. Aditi Verma</option>
                            <option value="102">Dr. Rajeev Kumar</option>
                            <option value="103">Dr. Meera Singh</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="doctor_name_div" style="display:none;">
                        <label for="doctor_name" class="form-label">Doctor Name</label>
                        <input type="text" name="doctor_name" class="form-control" placeholder="Enter Doctor Name">
                    </div>
                </div>

                <!-- Row 2: From & To Date/Time -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="from_date" class="form-label">From Date & Time</label>
                        <input type="datetime-local" name="from_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="to_date" class="form-label">To Date & Time</label>
                        <input type="datetime-local" name="to_date" class="form-control" required>
                    </div>
                </div>

                <!-- Row 3: Description + Condition -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" required></textarea>
                    </div>
                  <div class="col-md-6">
                    <label for="conditionSelect" class="form-label">Condition</label>
                    <select id="conditionSelect" name="condition[]" class="form-select">
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>


                </div>

                <!-- Row 4: File Uploads -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Discharge Summary (1 file)</label>
                        <input type="file" name="discharge_summary" class="form-control" accept=".pdf,.jpg,.png" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Summary Reports (up to 3 files)</label>
                        <input type="file" name="summary_reports[]" class="form-control" multiple accept=".pdf,.jpg,.png">
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">



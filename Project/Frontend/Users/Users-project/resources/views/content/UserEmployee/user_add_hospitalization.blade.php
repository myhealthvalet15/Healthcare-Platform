@extends('layouts/layoutMaster')
@section('title', 'Add Hospitalization')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/form-wizard-numbered.js'])
@endsection
@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="employeeHospitalizationForm" enctype="multipart/form-data">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="hospital_id" class="form-label">Hospital</label>
                        <select name="hospital_id" id="hospital_id" class="form-select">
                            <option value>-- Select Hospital --</option>
                            <option value="1">City Hospital</option>
                            <option value="2">State Medical</option>
                            <option value="0">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="hospital_name_div" style="display:none;">
                        <label for="hospital_name" class="form-label">Hospital
                            Name</label>
                        <input type="text" name="hospital_name" class="form-control" placeholder="Enter Hospital Name">
                    </div>
                    <div class="col-md-3">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-select">
                            <option value>-- Select Doctor --</option>
                            <option value="101">Dr. Aditi Verma</option>
                            <option value="102">Dr. Rajeev Kumar</option>
                            <option value="103">Dr. Meera Singh</option>
                            <option value="0">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="doctor_name_div" style="display:none;">
                        <label for="doctor_name" class="form-label">Doctor
                            Name</label>
                        <input type="text" name="doctor_name" class="form-control" placeholder="Enter Doctor Name">
                    </div>
                </div>
                <div class="row g-3 mb-3 align-items-end">
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="from_date" class="form-label">From
                                    Date & Time</label>
                                <input type="datetime-local" name="from_date" class="form-control form-control-sm"
                                    style="height: 38px;">
                            </div>
                            <div class="col-md-6">
                                <label for="to_date" class="form-label">To Date
                                    & Time</label>
                                <input type="datetime-local" name="to_date" class="form-control form-control-sm"
                                    style="height: 38px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="conditionSelect" class="form-label">Condition</label>
                        <select id="conditionSelect" name="condition[]" class="form-select" multiple>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Enter description here..."></textarea>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Discharge Summary (1
                            file)</label>
                        <input type="file" name="discharge_summary" class="form-control" accept=".pdf,.jpg,.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Test Reports (up to 3
                            files)</label>
                        <input type="file" name="summary_reports[]" class="form-control" multiple
                            accept=".pdf,.jpg,.png">
                    </div>
                </div>
                <div id="hospitalizationContainer"></div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/user_add_hospitalization.js?v=time()"></script>
@endsection
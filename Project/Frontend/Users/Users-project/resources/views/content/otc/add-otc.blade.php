@extends('layouts/layoutMaster')
@section('title', 'OTC Details')
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection
@section('page-script')
    @vite(['resources/assets/js/form-validation.js', 'resources/assets/js/forms-selects.js', 'resources/assets/js/extended-ui-sweetalert2.js', 'resources/assets/js/forms-tagify.js', 'resources/assets/js/forms-typeahead.js'])
@endsection

@section('content')


    <style>
        .non-otc-drug .drug-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .drug-name {
            flex: 0 0 51%;
            font-weight: bold;
        }

        .drug-availability {
            flex: 0 0 28%;
            color: green;
            padding-left: 10px;
        }

        .drug-issue {
            flex: 0 0 15%;
        }

        .drug-issue input {
            width: 90%;
            border: 1px solid #dfdfe3;
            border-radius: 6px;
            height: 35px;
        }

        .prescription-header {
            display: flex;
            justify-content: space-between;
            border-top: 1px dashed #4444e5;
            border-bottom: 1px dashed #4444e5;
            color: #4444e5;
            padding: 8px 0;
            font-weight: bold;
        }

        .prescription-header>div:nth-child(1) {
            width: 23%;
        }

        .prescription-header>div:nth-child(2) {
            width: 8%;
        }

        .prescription-header>div:nth-child(3) {
            width: 15%;
        }

        .text-danger {
            color: #dc3545;
            font-size: 13px;
            margin-top: 2px;
        }

        /* Primary color for First-Aid */
        .first-aid-radio:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Warning color for Moderate */
        .moderate-radio:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        /* Danger color for Severe */
        .severe-radio:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .hidden {
            display: none;
        }

        /* Centering the Spinner Container */
        .add-registry-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
        }

        /* Spinner Card Background */
        .spinner-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            text-align: center;
        }

        /* Existing Spinner Styles */
        .spinner-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sk-bounce {
            display: flex;
            justify-content: space-between;
            width: 50px;
        }

        .sk-bounce-dot {
            width: 30px;
            height: 30px;
            margin: 0 5px;
            background-color: #007bff;
            border-radius: 50%;
            animation: sk-bounce 1.4s infinite ease-in-out both;
        }

        @keyframes sk-bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
                opacity: 0.3;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
    <script>
        var employeeData = <?php echo json_encode($employeeData); ?>;
    </script>
    <?php
    // print_r($employeeData);
    ?>
    <div class="add-registry-spinner" id="add-registry-spinner" style="display: block;">
        <div class="spinner-card">
            <div class="spinner-container">
                <div class="sk-bounce sk-primary">
                    <div class="sk-bounce-dot"></div>
                    <div class="sk-bounce-dot"></div>
                </div>
                <label id="spinnerLabeltext">Retrieving data...</label>
            </div>
        </div>
    </div>
    <div style="display: none;" id="add-registry-card">
        <div class="card mb-4">
            <div class="card-header text-white p-2 border-0 rounded-top" style="background-color: #6B1BC7;">
                <div class="row">
                    <div class="col-md-6 d-flex position-relative">
                        <div class="me-3 d-flex align-items-center">
                            <img src="https://t3.ftcdn.net/jpg/01/65/63/94/360_F_165639425_kRh61s497pV7IOPAjwjme1btB8ICkV0L.jpg"
                                alt="Profile" class="rounded" width="60">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="text-warning mb-1" style="color:#ffff00 !important;">
                                <input type="hidden" name="employeeId" id="employeeId"
                                    value="{{ $employeeData['employee_id'] ?? '' }}">
                                <input type="hidden" name="isOutPatientAdded" id="isOutPatientAdded"
                                    value="{{ $employeeData['employee_is_outpatient_added'] ?? '' }}">
                                <input type="hidden" name="isOutPatientAddedAndOpen" id="isOutPatientAddedAndOpen"
                                    value="{{ $employeeData['employee_is_outpatient_open'] ?? '' }}">
                                {{ strtoupper($employeeData['employee_firstname'] ?? '') }}
                                {{ strtoupper($employeeData['employee_lastname'] ?? '') }} -
                                {{ $employeeData['employee_id'] ?? '' }}
                            </h6>
                            <p class="mb-1">
                                {{ $employeeData['employee_age'] ?? 'N/A' }} /
                                {{ $employeeData['employee_gender'] ?? 'N/A' }}
                            </p>
                            <p class="mb-0">
                                {{ ucwords(strtolower($employeeData['employee_designation'] ?? 'N/A')) }},
                                {{ ucwords(strtolower($employeeData['employee_department'] ?? 'N/A')) }}
                            </p>
                        </div>
                        <div
                            class="position-absolute end-0 top-0 bottom-0 d-flex flex-column justify-content-end me-4 py-2">
                            <button type="button" class="btn btn-sm"
                                style="background-color: #ffffff; color: #6a0dad; border: none; padding: 6px 12px; border-radius: 8px; font-weight: bold; width: auto;">
                                Edit Profile
                            </button>
                        </div>
                        <div class="position-absolute end-0 top-0 bottom-0 border-end border-light"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="ps-md-4 d-flex flex-column justify-content-center h-100">
                            <p class="mb-2">
                                <strong>Conditions:</strong>
                                {{ !empty($employeeData['healthParameters']['Published Conditions'])
                                    ? implode(', ', $employeeData['healthParameters']['Published Conditions'])
                                    : 'N/A' }}
                            </p>
                            <p class="mb-2">
                                <strong>Allergy Ingredients:</strong>
                                {{ !empty($employeeData['healthParameters']['Allergic Ingredients'])
                                    ? implode(', ', $employeeData['healthParameters']['Allergic Ingredients'])
                                    : 'N/A' }}
                            </p>
                            <p class="mb-0">
                                <strong>Food Allergy:</strong>
                                {{ !empty($employeeData['healthParameters']['Allergic Foods'])
                                    ? implode(', ', $employeeData['healthParameters']['Allergic Foods'])
                                    : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Reporting Date & Time</label>
                        <input class="form-control form-control-sm" type="datetime-local" id="reporting-datetime" />
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Work Shift</label>
                        <select class="form-select form-select-sm" id="workShift">
                            <option value="4" selected>General Shift</option>
                            <option value="1">First Shift</option>
                            <option value="2">Second Shift</option>
                            <option value="3">Third Shift</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">First Aid by</label>
                        <input type="text" class="form-control form-control-sm" placeholder="John Doe"
                            value="{{ $employeeData['employee_firstname'] . ' ' . $employeeData['employee_lastname'] }}"
                            id="firstAidBy" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Medical Details</h5>
                    <button class="btn btn-outline-primary" id="viewPastData">View Past Data</button>
                </div>
            </div>
        </div>
        <input type="hidden" name="user_id" id="user_id" value="{{ $employeeData['employee_id'] ?? '' }}">

    </div>
    <div class="col-md-12">

        <div class="col-md-2" style="line-height:10px;">
            <p style="color: #7367f0 !important; font-weight: bold; margin-left: 23px;">Pharmacy
                to issue</p>
            <select name="fav_pharmacy" id="fav_pharmacy" class="form-select"
                style="margin-left: 22px;    width: 180px;
    height: 35px;">
                @foreach ($pharmacyData['data'] as $pharmacy)
                    @if ($pharmacy['active_status'] == 1 && $pharmacy['main_pharmacy'] != 1)
                        <!-- Exclude main pharmacy -->
                        <option value="{{ $pharmacy['ohc_pharmacy_id'] }}">{{ $pharmacy['pharmacy_name'] }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="card shadow-sm" id="medicalFields">
            <div class="card-body">
                <div class="row">
                    <!-- Symptoms -->
                    <div class="col-md-4">
                        <label class="form-label text-primary mb-2" style="font-size: 15px;">
                            <strong>Symptoms</strong>
                        </label>
                        <div class="select2-primary symptoms">
                            <select id="select2Primary_symptoms" class="select2 form-select" multiple></select>
                            <div class="text-danger" id="symptomsError" style="font-size: 13px;"></div>
                        </div>

                    </div>

                    <!-- Medical System -->
                    <div class="col-md-4">
                        <label class="form-label text-primary mb-2" style="font-size: 15px;">
                            <strong>Medical System</strong>
                        </label>
                        <div class="select2-primary medical-system">
                            <select id="select2Primary_medical_system" class="select2 form-select" multiple></select>
                            <div class="text-danger" id="systemError" style="font-size: 13px;"></div>
                        </div>
                    </div>

                    <!-- Doctor Notes -->
                    <div class="col-md-4">
                        <label class="form-label text-primary mb-2" style="font-size: 15px;">
                            <strong>Remarks</strong>
                        </label>
                        <textarea class="form-control" rows="1" id="doctorNotes"></textarea>
                        <div class="text-danger" id="remarksError" style="font-size: 13px;"></div>
                    </div>



                </div>
                <div class="prescription-inputs"
                    style="display: flex; flex-direction: column; gap: 15px; padding: 16px 0; border-bottom: 2px #d1d0d4 dashed; font-weight: bold;">
                    <div class="prescription-header"
                        style="display: flex; justify-content: space-between;
                    border-top: 1px dashed #4444e5;
                    border-bottom: 1px dashed #4444e5;
                    color: #4444e5;
                    padding: 8px 0;
                    font-weight: bold;
                ">
                        <div style="width: 23%;">Drug Name - Type - Strength</div>
                        <div style="width: 8%;">Available</div>
                        <div style="width: 15%;">Issue</div>
                    </div>
                    <div id="non_otc_drugs_div" style="line-height:35px;border-bottom:1px solid;">
                        <h4>Non-OTC Drugs</h4>
                        <!-- Non-OTC drugs will be appended here -->
                        <span id="result_0" style="text-align: center; width: 100%;">0</span>

                    </div>
                    <!-- First Prescription Row -->
                    <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
                        <!-- Drug Name Input -->
                        <input type="hidden" name="rowid[]" id="rowid" value="0">
                        <div style="width: 35%;">
                            <div class="drug_name" title="drug_name">
                                <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_0"
                                    style="height:25px;width:85%;font-weight:normal;color:#acaab1 !important;">
                                    <option value>Select a Drug</option>
                                    <!-- Add drug options dynamically -->
                                </select>
                            </div>
                        </div>
                        <div style="width: 26%;margin-left:160px;">

                            <span id="result_0"></span>
                        </div>
                        <!-- Remarks Input -->
                        <div style="width: 15%;">
                            <input type="text" class="form-control" name="issue[]" placeholder="Issue"
                                style="width:90%; height:36px!important; flex: 0 0 15%;">
                            <div class="text-danger prescription-error" style="font-size: 13px;"></div>
                        </div>

                        <!-- Buttons for Add/Remove Rows -->
                        <div style="width: 5%; text-align: center;">
                            <div style="cursor: pointer;" class="margin-t-8 addjs" onclick="addRow1()">
                                <i class="fa-sharp fa-solid fa-square-plus"></i>
                                <!-- Only plus in the first row -->
                            </div>
                        </div>

                    </div>


                </div>




            </div>
            <div class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-auto"
                style="text-align: right; margin-top: 10px;height: 43px;
    width: 114px;margin-right:20px;">
                <input class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-auto" type="button"
                    name="submit_otc" value="Save OTC">
            </div><br />

        </div>

    </div>
    </div>
    </div>
    </div>
    </div>
    <script src="/lib/js/page-scripts/add-otc.js"></script>
@endsection

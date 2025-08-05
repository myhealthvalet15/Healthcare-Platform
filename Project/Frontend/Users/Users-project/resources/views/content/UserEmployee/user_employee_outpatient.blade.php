@extends('layouts/layoutMaster')
@section('title', 'Health Registry')
{{-- VENDOR STYLES --}}
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
{{-- VENDOR SCRIPTS --}}
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection
{{-- PAGE SCRIPTS --}}
@section('page-script')
    @vite(['resources/assets/js/forms-selects.js', 'resources/assets/js/extended-ui-sweetalert2.js', 'resources/assets/js/forms-typeahead.js'])
    <link rel="stylesheet" href="/lib/css/page-styles/user_employee_outpatient.css?v=time()">
    <script>
        const employeeId = @json(session('employee_id'));
        const employeeDetailsUrl = @json(route('employee-user-details'));
    </script>
@endsection
@section('content')
    <div class="card shadow-sm border-0 mb-4" id="employeeSummaryCard">
        <div class="card-body bg-light rounded" id="employeeSummary">
            <div class="row align-items-start justify-content-between">
                <div class="col-md-6">
                    <p><i class="fa-solid fa-user"></i> <span id="empName"></span> -
                        <span id="empId"></span>
                    </p>
                    <p><i class="fa-solid fa-hourglass-half"></i> <span id="empAge"></span> / <span
                            id="empGender"></span>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p><i class="fa-solid fa-sitemap"></i> <span id="empDepartment"></span> - <span
                            id="empDesignation"></span></p>
                    <p><i class="fa-solid fa-user-tag"></i> <span id="empType"></span> / <span
                            id="empdateOfJoining"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <div style="width: 300px;">
            <input type="text" id="registrySearchInput" class="form-control" placeholder="Nature of Injury / Diagnosis"
                style="margin-top:5px; margin-bottom:5px;">
        </div>
    </div>
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-custom-striped">
                <thead>
                    <tr>
                        <th>INCIDENT DATE</th>
                        <th style="text-align:left;">TYPE OF INCIDENT</th>
                        <th style="text-align:left;">NATURE OF INJURY/SYMPTOMS</th>
                        <th style="text-align:left;">MECHANISM OF
                            INJURY/DIAGNOSIS</th>
                        <th style="text-align:left;">BODY PART INJURED/MEDICAL
                            SYSTEM</th>
                        <th>DETAILS</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="health-registry-table-body">
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body" id="detailsModalBody"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="mt-4">
            <div class="modal fade" id="outsideReferralModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="outsideReferralModalLabel">Prescription</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-2">Employee
                                                    Info</h6>
                                                <p class="mb-1"><strong>Employee
                                                        Name:</strong> <span id="employeeName"></span></p>
                                                <p class="mb-0"><strong>Employee
                                                        ESI:</strong> <span id="employeeESI"></span></p>
                                                <p class="mb-0"><strong>MR
                                                        Number:</strong> <span id="mrNumber"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-2">Hospital
                                                    Information</h6>
                                                <p class="mb-1"><strong>Hospital
                                                        Name:</strong> <span id="hospitalName"></span></p>
                                                <p class="mb-0"><strong>Accompanied
                                                        By:</strong> <span id="accompaniedBy"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-2">Transportation
                                                    Details</h6>
                                                <p class="mb-0"><strong>Vehicle
                                                        Type:</strong> <span id="vehicleType"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-none" id="ambulanceDetailsSection">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-2">Ambulance
                                                    Details</h6>
                                                <p class="mb-1"><strong>Driver:</strong>
                                                    <span id="ambulanceDriver"></span>
                                                </p>
                                                <p class="mb-1"><strong>Ambulance
                                                        Number:</strong> <span id="ambulanceNumber"></span></p>
                                                <p class="mb-1"><strong>Out
                                                        Time:</strong> <span id="ambulanceOutTime"></span></p>
                                                <p class="mb-1"><strong>In
                                                        Time:</strong> <span id="ambulanceInTime"></span>
                                                </p>
                                                <p class="mb-1"><strong>Meter
                                                        Out:</strong> <span id="meterOut"></span>
                                                </p>
                                                <p class="mb-0"><strong>Meter
                                                        In:</strong> <span id="meterIn"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="mt-4">
            <div class="modal fade" id="prescriptionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl custom-modal-width" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="prescriptionModalLabel1">
                                Prescription Details <span id="prescriptionDateLabel" class="text-primary fw-bold"></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="prescription-container">
                                <div class="doctor-header">
                                    <span>Dr. John Doe</span>
                                    <span class="prescription-id">MPBoAmzVFigh
                                        0504202500013</span>
                                </div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>DRUG NAME - STRENGTH (TYPE)</th>
                                            <th>DAYS</th>
                                            <th>🌞</th>
                                            <th>🔴</th>
                                            <th>🌅</th>
                                            <th>🌙</th>
                                            <th>AF/BF</th>
                                            <th>REMARKS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/lib/js/page-scripts/user_employee_employee-details.js"></script>
    <script src="/lib/js/page-scripts/user_employee_outpatient.js"></script>
@endsection

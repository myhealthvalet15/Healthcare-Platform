@extends('layouts/layoutMaster')
@section('title', 'Health Registry')
{{-- VENDOR STYLES --}}
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection
{{-- VENDOR SCRIPTS --}}
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js',
])
@endsection
{{-- PAGE SCRIPTS --}}
@section('page-script')
@vite([
'resources/assets/js/forms-selects.js',
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/forms-typeahead.js',
])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/list-registry.css">
<div class="card">
    <div class="card-body">
        <div class="row mb-4" id="filtersSection">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 w-100">
                <div class="flex-fill">
                    <label for="searchInput" class="form-label">Patient/Test
                        Name</label>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Patient/Employee Name or Employee Id">
                </div>
                <div class="flex-fill">
                    <label for="doctorDropdown" class="form-label">Doctor</label>
                    <select id="doctorDropdown" class="form-select">
                        <option value>All Doctors</option>
                    </select>
                </div>
                <div class="flex-fill">
                    <label for="getincidentTypeColorCodes" class="form-label">Incident Colors</label>
                    <select id="getincidentTypeColorCodes" class="form-select">
                        <option value>All Incident Colors</option>
                    </select>
                </div>
                <div class="flex-fill">
                    <label for="fromDate" class="form-label">From Date <span class="text-danger">*</span></label>
                    <input type="text" id="fromDate" class="form-control flatpickr-date" placeholder="Select from date">
                </div>
                <div class="flex-fill">
                    <label for="toDate" class="form-label">To Date <span class="text-danger">*</span></label>
                    <input type="text" id="toDate" class="form-control flatpickr-date" placeholder="Select to date">
                </div>
            </div>
        </div>
        <div class="row g-3 mb-4" id="additionalFilters"
            style="display: none; overflow: hidden; max-height: 0; transition: all 0.4s ease-in-out; opacity: 0;">
            <div class="col-md-3">
                <label for="departmentDropdown" class="form-label">Department</label>
                <select id="departmentDropdown" class="form-select">
                    <option value>All Departments</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="injuryTypeDropdown" class="form-label">Injury
                    Type</label>
                <select id="injuryTypeDropdown" class="form-select">
                    <option value>All Injury Types</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="medicalSystemDropdown" class="form-label">Medical
                    System</label>
                <select id="medicalSystemDropdown" class="form-select">
                    <option value>All Systems</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                        <label class="form-check-label" for="other">Other</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <button class="btn btn-danger" id="advancedFiltersBtn">Advanced Filters</button>
                    <button class="btn btn-primary" id="applyFiltersBtn">Apply</button>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table id="health-registry-table" class="table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>NAME (AGE) - EMPLOYEE ID</th>
                    <th>DEPARTMENT</th>
                    <th>NATURE OF INJURY/SYMPTOMS</th>
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
<div id="reportModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attachment Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" class="img-fluid d-none" alt="Attachment Preview" />
                <div id="downloadBtnWrapper" class="mt-3 d-none">
                    <a id="downloadAttachment" href="#" download class="btn btn-primary">Download</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="outsideReferralModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="color: #ffffff;margin-bottom: 15px;" id="outsideReferralModalLabel">
                    Prescription & Referral Details</h5>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3" id="referralDetailsCard" style="display:none;">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold">Referral Details</h6>
                                <div class="row">
                                    <div class="col-12 mb-2"><strong>Condition Name:</strong> <span
                                            id="conditionName">N/A</span></div>
                                    <div class="col-12 mb-2"><strong>Description:</strong> <span
                                            id="description">N/A</span></div>
                                    <div class="col-md-6 mb-2"><strong>From Date:</strong> <span
                                            id="hospitalizationfromDate">N/A</span></div>
                                    <div class="col-md-6 mb-2"><strong>To Date:</strong> <span
                                            id="hospitalizationToDate">N/A</span></div>
                                    <div class="col-md-6 mb-2"><strong>Hospital Name:</strong> <span
                                            id="hospitalNameCard">N/A</span></div>
                                    <div class="col-md-6 mb-2"><strong>Doctor Name:</strong> <span
                                            id="doctorName">N/A</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3" id="attachmentsCard" style="display:none;">
                    <div class="col-12">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div id="attachmentSection"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold">Employee Information</h6>
                                <p id="employeeName" class="mb-1 fw-medium"></p>
                                <input type="hidden" id="hiddenEmployeeId" name="employee_id">
                                <input type="hidden" name="employee_name" id="hiddenEmployeeName">
                                <input type="hidden" name="employee_email" id="hiddenEmployeeEmail">
                                <input type="hidden" name="employee_department" id="hiddenEmployeeDepartment">
                                <input type="hidden" name="employee_dob" id="hiddenEmployeeDOB">
                                <input type="hidden" name="employee_gender" id="hiddenEmployeeGender">
                                <input type="hidden" name="employee_contact" id="hiddenEmployeeContact">
                                <input type="hidden" name="employee_designation" id="hiddenEmployeeDesignation">
                                <input type="hidden" name="employee_corporate" id="hiddenEmployeeCorporate">
                                <input type="hidden" name="employee_age" id="hiddenEmployeeAge">
                                <input type="hidden" name="employee_user_id" id="hiddenEmployeeUserId">
                                <input type="hidden" name="op_registry_id" id="hiddenOpRegistryId">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold">Hospital Information</h6>
                                <div><strong>Hospital Name:</strong> <span id="hospitalName"></span></div>
                                <div><strong>Accompanied By:</strong> <span id="accompaniedBy"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold">Transportation Details</h6>
                                <div><strong>Vehicle Type:</strong> <span id="vehicleType"></span></div>
                                <div id="ambulanceDetailsSection" class="mt-3 d-none">
                                    <h6 class="fw-semibold">Ambulance Details</h6>
                                    <div><strong>Driver:</strong> <span id="ambulanceDriver"></span></div>
                                    <div><strong>Ambulance Number:</strong> <span id="ambulanceNumber"></span></div>
                                    <div><strong>Out Time:</strong> <span id="ambulanceOutTime"></span></div>
                                    <div><strong>In Time:</strong> <span id="ambulanceInTime"></span></div>
                                    <div><strong>Meter Out:</strong> <span id="meterOut"></span></div>
                                    <div><strong>Meter In:</strong> <span id="meterIn"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="card-title fw-semibold">Additional Information</h6>
                                <div><strong>Employee ESI:</strong> <span id="employeeESI"></span></div>
                                <div><strong>MR Number:</strong> <span id="mrNumber"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateHospitalizationBtn">Update Hospitalization
                    Details</button>
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
                        <h5 class="modal-title" id="prescriptionModalLabel1">Prescriptions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="prescription-container">
                            <div class="doctor-header">
                                <span>Dr. John Doe</span>
                                <span class="prescription-id">MPBoAmzVFigh
                                    0504202500013</span>
                            </div>
                            <div class="patient-info">
                                <div>Randall House - 27 / Other
                                    (EMP00065)</div>
                                <!-- <div class="icons">
                                    <i class="fas fa-notes-medical"></i>
                                    <i class="fas fa-vial"></i>
                                    <i class="fas fa-envelope"></i>
                                    <i class="fas fa-print"></i>
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-trash text-danger"></i>
                                </div> -->
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
                                    <tr>
                                        <td class="drug-name">Quia - 200mg
                                            (Drops)</td>
                                        <td>2</td>
                                        <td>1</td>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>With Food</td>
                                        <td>Test</td>
                                    </tr>
                                    <tr>
                                        <td class="drug-name">Molestias -
                                            250mg (Foam)</td>
                                        <td>2</td>
                                        <td>2</td>
                                        <td>2</td>
                                        <td>0</td>
                                        <td>1</td>
                                        <td>After Food</td>
                                        <td>Ache</td>
                                    </tr>
                                    <tr>
                                        <td class="drug-name">Volini - Spray
                                            <i class="fas fa-external-link-alt"></i>
                                        </td>
                                        <td>1</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>1</td>
                                        <td>Before Food</td>
                                        <td>Test</td>
                                    </tr>
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
<script src="/lib/js/page-scripts/list-registry.js"></script>
@endsection
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
])<style>
    .icon-base {
        font-size: 1.2rem;
        margin: 0 4px;
        vertical-align: middle;
    }

    .badge.bg-label-success {
        background-color: rgba(40, 199, 111, 0.12) !important;
        color: #28c76f !important;
    }

    .badge.bg-label-danger {
        background-color: rgba(234, 84, 85, 0.12) !important;
        color: #ea5455 !important;
    }

    .table-custom-striped thead tr {
        background-color: #e0dee8;
    }

    .table-custom-striped tbody tr:nth-child(odd) {
        background-color: #fbfbfb;
    }

    .table-custom-striped tbody tr:nth-child(even) {
        background-color: #f1f2f3;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #f4f4f4;
    }

    .prescription-container {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .doctor-header {
        background: #6b1bc7;
        color: #fff;
        font-weight: bold;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .prescription-id {
        font-weight: bold;
        color: #fcd34d;
    }

    .patient-info {
        background: #d4d4d4;
        padding: 10px 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .icons {
        display: flex;
        gap: 10px;
    }

    .icons i {
        cursor: pointer;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f3e8ff;
        color: #333;
    }

    th,
    td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    td:first-child,
    th:first-child {
        text-align: left;
    }

    .drug-name i {
        margin-left: 5px;
        color: #555;
    }

    .test-list-clickable {
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .test-list-clickable:hover {
        background-color: #e9ecef;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .test-list-clickable i {
        margin-right: 5px;
        color: #696cff;
    }

    .test-group-title {
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 5px;
        color: #9D94F4;
    }

    .test-subgroup-title {
        font-weight: 600;
        margin-top: 5px;
        margin-bottom: 3px;
        margin-left: 15px;
        color: #78DBC7;
    }

    .test-subsubgroup-title {
        font-weight: normal;
        font-style: italic;
        margin-top: 3px;
        margin-bottom: 2px;
        margin-left: 30px;
        color: #DCDBE0;
    }

    .test-item {
        margin-left: 15px;
        color: #000000;
    }

    .subgroup-test-item,
    .subsubgroup-test-item {
        margin-left: 30px;
        color: #000000;
    }

    .subsubgroup-test-item {
        margin-left: 45px;
    }

    .modal-header-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 15px;
    }

    .employee-info,
    .date-info {
        font-weight: 500;
    }

    .date-info {
        text-align: right;
    }

    .status-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    

#employeeSummary p {
    margin: 0.5rem 0;
    font-size: 1rem;
}

#employeeSummary i {
    color: #0d6efd;
    margin-right: 6px;
}

#employeeSummary .text-end p {
    text-align: right;
}

.card-body {
    padding: 1.5rem;
    background: linear-gradient(to right, #fdfdff, #f2f6fc);
    border-radius: 12px;
}
.modal-body .card {
    background-color: #f9f9f9;
    border-radius: 0.5rem;
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-CfG4vD60FqN8o8xDhYOYX0+0V+j0ZWB3mkzM5tPpnL3lmUg4x3fPjZblA4YHXmsZRYZFbZ9wQ6s8NlnUZtd4cw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script>
    const employeeId = @json(session('employee_id'));
    const employeeDetailsUrl = @json(route('employee-user-details'));
</script>
<script src="/lib/js/page-scripts/employee-details.js"></script>
<script src="/lib/js/page-scripts/out_patient.js"></script>
@endsection

@section('content')


<div class="card shadow-sm border-0 mb-4" id="employeeSummaryCard">
  <div class="card-body bg-light rounded" id="employeeSummary">
    <div class="row align-items-start justify-content-between">
      
      <!-- Left Column -->
      <div class="col-md-6">
        <!-- Name - ID -->
        <p><i class="fa-solid fa-user"></i> <span id="empName"></span> - <span id="empId"></span></p>
        <!-- Age / Gender -->
        <p><i class="fa-solid fa-hourglass-half"></i> <span id="empAge"></span> / <span id="empGender"></span></p>
      </div>

      <!-- Right Column -->
      <div class="col-md-6 text-end">
        <!-- Department - Designation -->
        <p><i class="fa-solid fa-sitemap"></i> <span id="empDepartment"></span> - <span id="empDesignation"></span></p>
        <!-- Employee Type / Date of Joining -->
        <p><i class="fa-solid fa-user-tag"></i> <span id="empType"></span> / <span id="empdateOfJoining"></span></p>
      </div>
      
    </div>
  </div>
</div>
<!-- Advanced filters - now separate from the main filters -->

   
        <div class="d-flex justify-content-end mb-3">
            <div style="width: 300px;">
                <input type="text" id="registrySearchInput" class="form-control" placeholder="Nature of Injury / Diagnosis" style="margin-top:5px; margin-bottom:5px;">
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
                    <th style="text-align:left;">MECHANISM OF INJURY/DIAGNOSIS</th> 
                    <th style="text-align:left;">BODY PART INJURED/MEDICAL SYSTEM</th>                    
                    <th >DETAILS</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0" id="health-registry-table-body">
                <!-- Table data will be populated via JS -->
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
  <div class="modal-dialog modal-xl"> <!-- Change to modal-lg or modal-xl -->
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
            <!-- Employee Info -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2">Employee Info</h6>
                        <p class="mb-1"><strong>Employee Name:</strong> <span id="employeeName"></span></p>
                        <p class="mb-0"><strong>Employee ESI:</strong> <span id="employeeESI"></span></p>
                        <p class="mb-0"><strong>MR Number:</strong> <span id="mrNumber"></span></p>
                    </div>
                </div>
            </div>

            <!-- Hospital Info -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2">Hospital Information</h6>
                        <p class="mb-1"><strong>Hospital Name:</strong> <span id="hospitalName"></span></p>
                        <p class="mb-0"><strong>Accompanied By:</strong> <span id="accompaniedBy"></span></p>
                    </div>
                </div>
            </div>

            <!-- Transportation -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2">Transportation Details</h6>
                        <p class="mb-0"><strong>Vehicle Type:</strong> <span id="vehicleType"></span></p>
                    </div>
                </div>
            </div>

            <!-- Ambulance Details (hidden by default) -->
            <div class="col-md-6 d-none" id="ambulanceDetailsSection">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2">Ambulance Details</h6>
                        <p class="mb-1"><strong>Driver:</strong> <span id="ambulanceDriver"></span></p>
                        <p class="mb-1"><strong>Ambulance Number:</strong> <span id="ambulanceNumber"></span></p>
                        <p class="mb-1"><strong>Out Time:</strong> <span id="ambulanceOutTime"></span></p>
                        <p class="mb-1"><strong>In Time:</strong> <span id="ambulanceInTime"></span></p>
                        <p class="mb-1"><strong>Meter Out:</strong> <span id="meterOut"></span></p>
                        <p class="mb-0"><strong>Meter In:</strong> <span id="meterIn"></span></p>
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
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
@endsection
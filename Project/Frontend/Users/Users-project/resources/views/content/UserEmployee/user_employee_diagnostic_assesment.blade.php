@extends('layouts/layoutMaster')
@section('title', 'Diagnostic Assesment')
@section('page-script')
<link rel="stylesheet" href="/lib/css/page-styles/user_employee_diagnostic_assesment.css?v=time()">
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
                <p><i class="fa-solid fa-user"></i> <span id="empName"></span> - <span id="empId"></span></p>
                <p><i class="fa-solid fa-hourglass-half"></i> <span id="empAge"></span> / <span id="empGender"></span>
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p><i class="fa-solid fa-sitemap"></i> <span id="empDepartment"></span> - <span
                        id="empDesignation"></span></p>
                <p><i class="fa-solid fa-user-tag"></i> <span id="empType"></span> / <span id="empdateOfJoining"></span>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Healthplan List</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Dates</th>
                    <th>Healthplan</th>
                    <th>Doctor / Diagnostic Center</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="healthPlanTableBody">
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="datesModal" tabindex="-1" aria-labelledby="datesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content dates-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="datesModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>All Dates for <span id="employeeName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body dates-modal-body" id="datesModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="certificationModal" tabindex="-1" aria-labelledby="certificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="certificationModalLabel">
                    <span id="modalMode">Certification</span>: <span id="certificationTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="certifiedInfo" class="alert alert-success" style="display: none;">
                    <strong>Certified On:</strong> <span id="certifiedOn"></span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="conditionSelect" class="form-label">Condition <span
                                class="text-danger">*</span></label>
                        <select id="conditionSelect" class="form-select">
                            <option value>Select Condition</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="issueDateInput" class="form-label">Issue
                            Date <span class="text-danger">*</span></label>
                        <input type="date" id="issueDateInput" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="nextAssessmentInput" class="form-label">Next
                            Assessment Date <span class="text-danger">*</span></label>
                        <input type="date" id="nextAssessmentInput" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="remarksInput" class="form-label">Remarks</label>
                        <textarea id="remarksInput" class="form-control" rows="3"
                            placeholder="Enter any remarks or notes..."></textarea>
                    </div>
                    <div class="col-12" id="badgePreview" style="display: none;">
                        <label class="form-label">Badge Preview:</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCertification">Save Certification</button>
            </div>
        </div>
    </div>
</div>
<!-- Date Details Modal -->
<div class="modal fade" id="dateDetailsModal" tabindex="-1" aria-labelledby="dateDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateDetailsModalLabel">Date Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Assigned Date:</strong> <span id="modalAssignedDate"></span></p>
                <p><strong>Due Date:</strong> <span id="modalDueDate"></span></p>
                <p><strong>Diagnosis Date:</strong> <span id="modalDiagnosisDate"></span></p>
                <p><strong>Assessment Date:</strong> <span id="modalAssessmentDate"></span></p>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/user_employee_employee-details.js?v=time()"></script>
<script src="/lib/js/page-scripts/user_employee_diagnostic_assesment.js?v=time()"></script>
@endsection
@extends('layouts/layoutMaster')
@section('title', 'Test List')
<link rel="stylesheet" href="/lib/css/page-styles/user_employee_test.css?v=time()">
@section('content')
<div class="card">
  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Healthplan/Test List</th>
          <th>Doctor and Diagnostic Center</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0"></tbody>
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
      <div class="modal-body" id="certificationModalBody">
        <div id="certifiedInfo" class="alert alert-success" style="display: none;">
          <strong>Certified On:</strong> <span id="certifiedOn"></span>
        </div>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="conditionSelect" class="form-label">Condition <span class="text-danger">*</span></label>
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
<script>
  var $apiMenuData = @json($apiMenuData ?? null);
  const employeeId = "{{ session('employee_id') }}";
  const employeeDetailsUrl = "{{ route('employee-user-details') }}?employee_id=" + employeeId;
</script>
<script src="/lib/js/page-scripts/user_employee_test.js?v=time()"></script>
@endsection
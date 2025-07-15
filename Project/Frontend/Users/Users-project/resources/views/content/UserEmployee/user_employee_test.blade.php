@extends('layouts/layoutMaster')
@section('title', 'Test List')
@section('content')
<style>
  /* Test list and grouping styles */
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
    background-color: white;
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

  /* Modal and header styles */
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

  /* Icon styles */
  .icon-base {
    font-size: 1.2rem;
    margin: 0 4px;
    vertical-align: middle;
  }

  /* Badge styles */
  .badge.bg-label-success {
    background-color: rgba(40, 199, 111, 0.12) !important;
    color: #28c76f !important;
  }

  .badge.bg-label-danger {
    background-color: rgba(234, 84, 85, 0.12) !important;
    color: #ea5455 !important;
  }

  /* Table styles */
  .table-custom-striped thead tr {
    background-color: #e0dee8;
  }

  .table-custom-striped tbody tr:nth-child(odd) {
    background-color: #fbfbfb;
  }

  .table-custom-striped tbody tr:nth-child(even) {
    background-color: #f1f2f3;
  }

  /* Body styles */
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f4f4f4;
  }

  /* Prescription styles */
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

  /* Table styles */
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
    text-align: center;
  }

  .drug-name i {
    margin-left: 5px;
    color: #555;
  }#employeeSummary p {
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



</style>


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

<!-- <div class="modal fade" id="certificationModal" tabindex="-1" aria-labelledby="certificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="certificationModalLabel">
          <i class="fas fa-certificate me-2"></i> Certification Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="certificationModalBody">
         Certification details will be inserted here dynamically 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> -->
<!-- calendar-->
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
<script>
  var $apiMenuData = @json($apiMenuData ?? null);
</script>
<script>
  var SubModulePermission = 1;
  if (typeof $apiMenuData !== 'undefined') {
    $apiMenuData.forEach(function (module) {
      if (module.module_name === 'OHC') {
        module.submodules.forEach(function (submodule) {
          if (submodule.sub_module_name === 'Test') {
            SubModulePermission = submodule.permission;
          }
        });
      }
    });
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let  allHealthPlansData = [];
  let colorData = [];
  document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector('.table-border-bottom-0');const employeeId = "{{ session('employee_id') }}";
    const employeeDetailsUrl = "{{ route('employee-user-details') }}?employee_id=" + employeeId;


    init();
    
    function init() {
      createTestModal(); 
     // fetchEmployeeDetails(employeeId);
      createObservationModal();
      fetchCombinedTestData();
      showLoadingIndicator();
      //fetchTestData();
    }  
    function fetchEmployeeDetails(employeeId) {
    apiRequest({
        url: employeeDetailsUrl + "?employee_id=" + employeeId,
        method: "GET",
        onSuccess: (data) => {
            if (data && data.employee_id) {
                // Fill employee info
                document.getElementById("empId").textContent = data.employee_id;
                document.getElementById("empName").textContent = `${data.employee_firstname} ${data.employee_lastname}`;
                document.getElementById("empAge").textContent = data.employee_age;
                document.getElementById("empGender").textContent = capitalizeFirstLetter(data.employee_gender);
                document.getElementById("empDepartment").textContent = capitalizeFirstLetter(data.employee_department);
                document.getElementById("empDesignation").textContent = capitalizeFirstLetter(data.employee_designation);
                document.getElementById("empType").textContent = capitalizeFirstLetter(data.employee_type_name);
                document.getElementById("empdateOfJoining").textContent = data.dateOfJoining;

                // Show the summary card
                const summaryCard = document.getElementById("employeeSummaryCard");
                if (summaryCard) {
                    summaryCard.style.display = "block";
                    summaryCard.classList.add("animate__animated", "animate__fadeIn");
                }
            } else {
                showErrorInTable('Invalid data format received');
            }

            // Continue with table data if applicable
            if (data.result && data.data && Array.isArray(data.data)) {
                populateTable(data.data, filters);
            }
        },
        onError: (error) => {
            console.error('Error fetching employee details:', error);
            showErrorInTable('Error loading data: ' + error.message);
        }
    });
}
function capitalizeFirstLetter(string) {
    if (typeof string !== 'string' || !string.length) return '';
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
    function showLoadingIndicator() {
      clearTable();
      const preloaderRow = document.createElement('tr');
      const preloaderCell = document.createElement('td');
      preloaderCell.setAttribute('colspan', '6');
      preloaderCell.className = 'text-center';
      const spinner = document.createElement('div');
      spinner.className = 'spinner-border text-primary';
      spinner.setAttribute('role', 'status');
      const spinnerText = document.createElement('span');
      spinnerText.className = 'visually-hidden';
      spinnerText.textContent = 'Loading...';
      spinner.appendChild(spinnerText);
      preloaderCell.appendChild(spinner);
      preloaderRow.appendChild(preloaderCell);
      tableBody.appendChild(preloaderRow);
    }
    function clearTable() {
      while (tableBody.firstChild) {
        tableBody.removeChild(tableBody.firstChild);
      }
    }
    // function fetchTestData() {
    //   apiRequest({
    //     url: 'https://login-users.hygeiaes.com/ohc/getAllTests',
    //     method: 'GET',
    //     onSuccess: function (response) {
    //       if (response.result && Array.isArray(response.data)) {
    //         populateTestTable(response.data);
    //       } else {
    //         showErrorInTable('Invalid data format received');
    //         console.warn('Error: ', response);
    //       }
    //     },
    //     onError: function (error) {
    //       showErrorInTable('Error loading data: ' + error.message);
    //       showToast('error', "Failed to load tests: " + error);
    //     }
    //   });
    // }

  function fetchCombinedTestData() {
  Promise.all([
    new Promise((resolve, reject) => {
      apiRequest({
        url: 'https://login-users.hygeiaes.com/ohc/getAllTests',
        method: 'GET',
        onSuccess: response => resolve(response),
        onError: error => reject(error)
      });
    }),
    new Promise((resolve, reject) => {
      apiRequest({
        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllAssignHealthPlans',
        method: 'GET',
        onSuccess: response => 
        resolve(response),
        onError: error => reject(error)
      });
    })
  ])
  .then(([res1, res2]) => {
    const data1 = Array.isArray(res1.data) ? res1.data : [];
    const data2 = Array.isArray(res2.data) ? res2.data : [];
    allHealthPlansData = data2;
    const combinedData = [...data1, ...data2];
   console.log("combined Data", combinedData);
    populateTestTable(combinedData); 
  })
  .catch(error => {
    showErrorInTable("Failed to load data");
    showToast('error', 'Error', error.message || 'One of the APIs failed');
    console.error("API fetch error:", error);
  });
}
    function createTestModal() {
      if (document.getElementById('testListModal')) return;
      const modalHtml = `
      <div class="modal fade" id="testListModal" tabindex="-1" aria-labelledby="testListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="testListModalLabel">Full Test List</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="modal-header-info">
                <div class="employee-info" id="modalEmployeeName"></div>
                <div class="date-info" id="modalTestDate"></div>
              </div>
              <div class="mb-3">
                <div id="modalTestList" class="mt-2"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    `;
      document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    function createObservationModal() {
      if (document.getElementById('observationModal')) return;
      const modalHtml = `
      <div class="modal fade" id="observationModal" tabindex="-1" aria-labelledby="observationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="observationModalLabel">Medical Observations</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="employee-details mb-3">
                <h6>Employee Information</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Employee ID:</strong> <span id="obsEmployeeId"></span></p>
                    <p><strong>Name:</strong> <span id="obsEmployeeName"></span></p>
                    <p><strong>Age:</strong> <span id="obsEmployeeAge"></span></p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Department:</strong> <span id="obsDepartment"></span></p>
                    <p><strong>Report Date:</strong> <span id="obsReportDate"></span></p>
                    <p><strong>Type of Incident:</strong> <span id="obsIncidentType"></span></p>
                  </div>
                </div>
              </div>
              <div class="medical-notes mb-3">
                <h6>Medical Information</h6>
                <div class="row">
                  <div class="col-md-12">
                    <p><strong>Doctor Notes:</strong> <span id="obsDoctorNotes"></span></p>
                    <p><strong>Medical History:</strong> <span id="obsMedicalHistory"></span></p>
                    <p><strong>Referral:</strong> <span id="obsReferral"></span></p>
                  </div>
                </div>
              </div>
              <!-- Medical Illness specific fields -->
              <div id="medicalIllnessFields" class="incident-details mb-3" style="display: none;">
                <h6>Medical Illness Details</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Body Part:</strong> <span id="obsMIBodyPart"></span></p>
                    <p><strong>Symptoms:</strong> <span id="obsMISymptoms"></span></p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Medical System:</strong> <span id="obsMIMedicalSystem"></span></p>
                    <p><strong>Diagnosis:</strong> <span id="obsMIDiagnosis"></span></p>
                  </div>
                </div>
              </div>
              <!-- Industrial/Outside Accident specific fields -->
              <div id="accidentFields" class="incident-details mb-3" style="display: none;">
                <h6>Accident Details</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Nature of Injury:</strong> <span id="obsNatureInjury"></span></p>
                    <p><strong>Body Part:</strong> <span id="obsBodyPart"></span></p>
                    <p><strong>Mechanism of Injury:</strong> <span id="obsMechanismInjury"></span></p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Severity:</strong> <span id="obsInjurySeverity"></span></p>
                    <p><strong>Side of Body:</strong> <span id="obsBodySide"></span></p>
                    <p id="obsSiteOfInjuryContainer"><strong>Site of Injury:</strong> <span id="obsSiteOfInjury"></span></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    `;
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      const styleElement = document.createElement('style');
      styleElement.textContent = `
      .severity-indicator {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
      }
      .observation-icon {
        cursor: pointer;
        color: #696cff;
        transition: color 0.2s;
        margin-right: 10px;
      }
      .observation-icon:hover {
        color: #484bff;
      }
      .prescription-icon {
        cursor: pointer;
        transition: color 0.2s;
        margin-left: 10px;
      }
      .prescription-icon.active {
        color: #696cff;
      }
      .prescription-icon.active:hover {
        color: #484bff;
      }
      .prescription-icon.inactive {
        color: #c9c9c9;
        cursor: default;
      }
      .prescription-container {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
      }
      .doctor-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
      }
      .patient-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding: 5px 0;
        background-color: #f9f9f9;
      }
      .patient-info .icons {
        display: flex;
        gap: 10px;
      }
      .patient-info .icons i {
        cursor: pointer;
      }
      .prescription-container table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
      }
      .prescription-container th, .prescription-container td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
      }
      .prescription-container th {
        background-color: #f2f2f2;
      }
      .prescription-container td.drug-name {
        text-align: left;
        font-weight: bold;
      }
    `;
      document.head.appendChild(styleElement);
    }
    function populateTestTable(testsData) {
      clearTable();
      testsData.sort((a, b) => {
        const dateA = new Date(a.reporting_date_time);
        const dateB = new Date(b.reporting_date_time);
        return dateB - dateA;
      });
      if (testsData.length > 0) {
        testsData.forEach(test => {
          tableBody.appendChild(createTestRow(test));
        });
      } else {
        showNoDataMessage();
      }
    }
        function updateBadgePreview() {
        let selectedCondition = $('#conditionSelect').val();
        let badgePreview = $('#badgePreview');
        if (selectedCondition && currentCertificationData) {
            let cert = currentCertificationData.certification;
            let currentItem = currentCertificationData.item;
            let isMainCertified = currentItem.certified && currentItem.certified !== null;
            let conditions = [];
            try {
                conditions = typeof cert.condition === 'string'
                    ? JSON.parse(cert.condition)
                    : (Array.isArray(cert.condition) ? cert.condition : []);
            } catch (e) {
                conditions = [];
            }
            let colorIndex = conditions.indexOf(selectedCondition);
            let badgeColor = cert.color_condition &&
                cert.color_condition.length > colorIndex &&
                colorIndex >= 0
                ? cert.color_condition[colorIndex]
                : '#6c757d';
            if (!isMainCertified) {
                badgePreview.html(`
                <div class="badge-preview" style="background-color: ${badgeColor}; color: white;">
                    ${capitalizeWords(cert.certification_title)}: ${selectedCondition}
                </div>
            `).show();
            } else {
                badgePreview.html(`
                <div class="badge-preview" style="background-color: ${badgeColor}; color: white;">
                    ${capitalizeWords(cert.certification_title)}: ${selectedCondition}
                </div>
            `).show();
            }
        } else {
            badgePreview.hide();
        }
    }

    function showNoDataMessage() {
      const noDataRow = document.createElement('tr');
      const noDataCell = document.createElement('td');
      noDataCell.setAttribute('colspan', '6');
      noDataCell.className = 'text-center';
      noDataCell.textContent = 'No test data found';
      noDataRow.appendChild(noDataCell);
      tableBody.appendChild(noDataRow);
    }
    function updateTableHeader() {
      const tableHeader = document.querySelector('.table-light tr');
      if (tableHeader) {
        if (!tableHeader.querySelector('th:nth-child(5)').textContent.includes('Observation')) {
          const observationHeader = document.createElement('th');
          observationHeader.textContent = 'Observation';
          tableHeader.insertBefore(observationHeader, tableHeader.querySelector('th:nth-child(5)'));
        }
      }
    }
    setTimeout(updateTableHeader, 100);
    function extractAllTests(testStructure) {
      let allTests = [];
      Object.keys(testStructure).forEach(groupName => {
        const group = testStructure[groupName];
        if (typeof group === 'object') {
          Object.keys(group).forEach(subGroupName => {
            const subGroup = group[subGroupName];
            if (Array.isArray(subGroup)) {
              subGroup.forEach(test => {
                if (typeof test === 'object' && test.name) {
                  allTests.push(test.name);
                } else if (typeof test === 'string') {
                  allTests.push(test);
                }
              });
            } else if (typeof subGroup === 'object') {
              Object.keys(subGroup).forEach(key => {
                const item = subGroup[key];
                if (Array.isArray(item)) {
                  item.forEach(test => {
                    if (typeof test === 'object' && test.name) {
                      allTests.push(test.name);
                    } else if (typeof test === 'string') {
                      allTests.push(test);
                    }
                  });
                } else if (typeof item === 'object' && item.name) {
                  allTests.push(item.name);
                } else if (typeof item === 'string') {
                  allTests.push(item);
                }
              });
            }
          });
        } else if (typeof group === 'string') {
          allTests.push(group);
        }
      });
      return allTests;
    }

    function createTestRow(test) {
      const row = document.createElement('tr');
      const dateCell = document.createElement('td');
      const dateWrapper = document.createElement('div');
     dateWrapper.className = 'date-with-icon d-flex align-items-center justify-content-center gap-2';
     dateWrapper.style.cursor = 'pointer'; // make whole wrapper look clickable
       dateWrapper.setAttribute('data-item-id', test.id); // store ID
       //
       dateCell.className = 'text-center';

      const dateSpan = document.createElement('span');
      dateSpan.className = 'fw-medium';

      const calendarIcon = document.createElement('i');
      calendarIcon.className = 'fas fa-calendar-alt calendar-icon';
      calendarIcon.setAttribute('title', 'View all dates');

      if (test.reporting_date_time) {
        const testDate = new Date(test.reporting_date_time);
        const formattedDate = `${testDate.getDate().toString().padStart(2, '0')}-${(testDate.getMonth() + 1).toString().padStart(2, '0')}-${testDate.getFullYear().toString().substring(2)}`;
        dateSpan.textContent = formattedDate;
        calendarIcon.style.visibility = 'hidden';
        dateWrapper.appendChild(dateSpan);
        dateWrapper.appendChild(calendarIcon); // show icon

      }
       else if(test.test_date)
      {
        const testDate = new Date(test.test_date);
        const formattedDate = `${testDate.getDate().toString().padStart(2, '0')}-${(testDate.getMonth() + 1).toString().padStart(2, '0')}-${testDate.getFullYear().toString().substring(2)}`;
        dateSpan.textContent = formattedDate; 
        const calendarIcon = document.createElement('i');
calendarIcon.className = 'fas fa-calendar-alt calendar-icon';
calendarIcon.setAttribute('title', 'View all dates');
dateWrapper.appendChild(dateSpan);
dateWrapper.appendChild(calendarIcon);
}

// Create calendar icon
dateWrapper.addEventListener('click', function () {
  openDatesModal(test); 
});
 dateCell.appendChild(dateWrapper);
 row.appendChild(dateCell);

      // const employeeCell = document.createElement('td');
      // const nameSpan = document.createElement('b');
      // nameSpan.textContent = test.name || 'N/A';
      // employeeCell.appendChild(nameSpan);
      const testUrl = `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/health-plan/${test.id}/prescription-test/${test.test_id}/${test.test_code}?readonly=true`;

      const testsCell = document.createElement('td');
      const testsContainer = document.createElement('div');
      
      testsContainer.className = 'd-flex flex-column';
      testsContainer.style.alignItems = 'flex-start';
      let isReadOnly = false;

 // Stack vertically

 //readonly

// 🔹 Healthplan Title as a link
const healthplanTitle = document.createElement('a');
healthplanTitle.className = 'fw-medium text-primary text-decoration-none';
healthplanTitle.href = testUrl;
healthplanTitle.style.cursor = 'pointer';
healthplanTitle.textContent = test.healthplan_title || '';
healthplanTitle.title = test.healthplan_title || '';
healthplanTitle.target = '_blank';
healthplanTitle.rel='noopener noreferrer';

testsContainer.appendChild(healthplanTitle);
    
      const testListRow = document.createElement('div');
      if (test.tests) {
        const allTests = extractAllTests(test.tests);
        if (allTests.length > 0) {
          testListRow.className = 'test-list-clickable d-flex align-items-center justify-content-start gap-1';
          
          const viewIcon = document.createElement('i');
          viewIcon.className = 'fa-solid fa-list-ul';
          testListRow.appendChild(viewIcon);
          
          const textSpan = document.createElement('span');
          textSpan.textContent = allTests.length > 2
            ? `${allTests.slice(0, 2).join(', ')}...`
            : allTests.join(', ');
          testListRow.appendChild(textSpan);

          testListRow.dataset.testStructure = JSON.stringify(test.tests);
          testListRow.dataset.testDate = test.reporting_date_time || 'N/A';
          testListRow.style.cursor = 'pointer';

          testListRow.setAttribute('title', 'Click to view all tests');
          testListRow.addEventListener('click', function () {
            showTestListModal(this.dataset);
          });
        } else {
          testListRow.textContent = 'No tests found';
        }
      } else {
        testListRow.textContent = '';
      }
      testsContainer.appendChild(testListRow);

      // Append full column to row
      testsCell.appendChild(testsContainer);
      row.appendChild(testsCell);
     
       // ---- Doctor and Diagnostic Center Column ----
      const doctorCell = document.createElement('td');
      const doctorName = test.doctor_name || '-';
      const diagCenter = test.diagnosis_center || '-';

      doctorCell.innerHTML = `<span>${doctorName} - ${diagCenter}</span>`;
      row.appendChild(doctorCell);
       //status
const certificationCell = document.createElement('td');
let currentStatus = getItemStatus(test); // <-- Use your own item/test object here

const statusBadge = document.createElement('span');
statusBadge.textContent = currentStatus;
statusBadge.className = `badge mb-2 ${getStatusBadgeClass(currentStatus)}`; 
const certWrapper = document.createElement('div');
certWrapper.className = 'd-flex flex-column gap-1';

if (test.certifications && Array.isArray(test.certifications)) {
  test.certifications.forEach(cert => {
    const badge = document.createElement('span');
    badge.textContent = capitalizeWords(cert.certification_title || 'Unknown');

    // Style
    let badgeStyle = 'badge bg-primary';
    if (test.result_ready) {
      const badgeColor = cert.healthplan_certification?.color_condition || '';
      if (badgeColor) {
        badge.style.backgroundColor = badgeColor;
        badge.style.color = '#fff';
        badge.classList.add('badge');
      } else {
        badge.className = badgeStyle;
      }

      badge.dataset.itemId = test.id;
      badge.dataset.certId = cert.certificate_id;
      badge.dataset.testId = test.test_id;

      badge.style.cursor = 'pointer';
      badge.title = `Click to open ${cert.certification_title}`;

      badge.addEventListener('click', function () {
        openCertificationModal(this); 
      });
    } else {
      badge.title = `${capitalizeWords(cert.certification_title)} - Available after result is ready`;
      badge.className = 'badge bg-secondary';
      badge.style.opacity = '0.6';
    }

    certWrapper.appendChild(badge);
  });
} else {
  certWrapper.textContent = '';
}

// Step 4: Append to row
certificationCell.appendChild(statusBadge);
certificationCell.appendChild(certWrapper);
row.appendChild(certificationCell);
    return row;
}


    function loadColorConditions() {
        let colorData = []
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllColorCodes',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    response.data.forEach(function (item) {
                        const title = item.certification_title?.trim() || '';
                        const conditions = Array.isArray(item.condition) ? item.condition : [];
                        const colors = Array.isArray(item.color_condition) ? item.color_condition : [];
                        for (let i = 0; i < Math.min(conditions.length, colors.length); i++) {
                            const condition = conditions[i]?.trim();
                            const color = colors[i]?.trim();
                            if (condition && color && title) {
                                colorData.push({
                                    certification_title: title,
                                    condition,
                                    color
                                });
                            }
                        }
                    });
                }
            },
            onError: function (error) {
                console.error('Error loading color codes:', error);
            }
        });
        return colorData;
    }
    function getItemStatus(item) {
        if (item.cancelled) return 'Cancelled';
        if (item.certified) return 'Certified';
        if (item.no_show) return 'No Show';
        if (item.result_ready) return 'Result Ready';
        if (item.test_completed) return 'Test Completed';
        if (item.in_process) return 'In Process';
        if (item.schedule) return 'Schedule';
        if (item.pending) return 'Pending';
        return 'Pending';
    }


function openCertificationModal(element) {
  const itemId = element.getAttribute('data-item-id');
  const certId = element.getAttribute('data-cert-id');
  const testId = element.getAttribute('data-test-id');

  const currentItem = allHealthPlansData.find(item => item.id == itemId);
  if (!currentItem || !currentItem.result_ready) {
    showToast('warning', 'Not Available', 'Certification is only available after result is ready.');
    return;
  }

  const currentCert = currentItem.certifications.find(cert => cert.certificate_id == certId);
  if (!currentCert) return;
  currentCertificationData = {
            item: currentItem,
            certification: currentCert,
            itemId: itemId,
            certId: certId,
            testId: testId
        };


  const certBody = document.getElementById('certificationModalBody');
  certBody.innerHTML = `
    <p><strong>Certification Title:</strong> ${currentCert.certification_title}</p>
    <p><strong>Condition:</strong> ${currentCert.healthplan_certification?.condition || 'N/A'}</p>
    <p><strong>Color:</strong> ${currentCert.healthplan_certification?.color_condition || 'N/A'}</p>
    <!-- Add more fields as needed -->
  `;
   populateCertificationModal(currentCert);

  $('#certificationModal').modal('show');
}
    function populateCertificationModal(certification) {
        $('#certificationTitle').text(capitalizeWords(certification.certification_title));
        let currentItem = currentCertificationData.item;
        let isMainCertified = currentItem.certified && currentItem.certified !== null;
        let conditionSelect = $('#conditionSelect');
        let remarksInput = $('#remarksInput');
        let saveCertificationBtn = $('#saveCertification');
        let closeBtnModal = $('.modal-footer .btn-secondary');
        conditionSelect.empty();
        conditionSelect.append('<option value="">Select Condition</option>');
        let conditions = [];
        try {
            conditions = typeof certification.condition === 'string'
                ? JSON.parse(certification.condition)
                : (Array.isArray(certification.condition) ? certification.condition : []);
        } catch (e) {
            console.error('Error parsing conditions:', e);
            conditions = [];
        }
        conditions.forEach(function (condition) {
            conditionSelect.append(`<option value="${condition}">${condition}</option>`);
        });
        let healthplanCert = certification.healthplan_certification;
        if (isMainCertified) {
            $('#modalMode').text('View Certification Details');
            if (healthplanCert && healthplanCert.condition) {
                $('#conditionSelect').closest('.col-md-4').hide();
                $('#issueDateInput').closest('.col-md-4').hide();
                $('#nextAssessmentInput').closest('.col-md-4').hide();
                $('#remarksInput').closest('.col-12').hide();
                $('#certificationViewContent').remove();
                let conditionIndex = conditions.indexOf(healthplanCert.condition);
                let badgeColor = certification.color_condition &&
                    certification.color_condition.length > conditionIndex &&
                    conditionIndex >= 0
                    ? certification.color_condition[conditionIndex].replace(/`/g, '')
                    : '#6c757d';
                const allConditions = colorData.filter(cd =>
                    cd.certification_title.toLowerCase() === certification.certification_title.toLowerCase()
                );
                let allConditionsHtml = '';
                if (allConditions.length > 0) {
                    allConditionsHtml = `
                <div class="mb-3">
                    <strong class="text-dark d-block mb-1">Available Conditions for "${certification.certification_title}" Certificate:</strong>
                    <div class="d-flex flex-wrap align-items-center">
                        ${allConditions.map(cd => `
                            <div class="d-flex align-items-center me-3 mb-2">
                                <span style="width: 12px; height: 12px; background-color: ${cd.color}; display: inline-block; border-radius: 2px; margin-right: 6px;"></span>
                                <span>${cd.condition}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
                }
                let viewContent = `
            <div id="certificationViewContent" class="row g-3">
                <div class="col-12">
                    <div class="alert alert-light border">
                        <h6 class="mb-3 text-dark"><i class="fas fa-certificate me-2"></i>Certification Details</h6>
                        <div class="mb-3 d-flex align-items-center">
                            <strong class="text-dark me-2">Condition:</strong>
                            <span class="me-2" style="width: 12px; height: 12px; background-color: ${badgeColor}; display: inline-block; border-radius: 2px;"></span>
                            <span>${healthplanCert.condition}</span>
                        </div>
                        ${allConditionsHtml}
                        ${healthplanCert.remarks ? `
                            <div class="mb-3">
                                <strong class="text-dark">Remarks:</strong>
                                <div class="mt-1 p-2 bg-light border rounded text-dark">
                                    ${healthplanCert.remarks}
                                </div>
                            </div>
                        ` : ''}
                        <div class="mb-3">
                            <strong class="text-dark">Certified On:</strong>
                            <span class="text-dark fw-medium">${formatDateToDDMMYYYY(currentItem.certified)}</span>
                        </div>
                        ${healthplanCert.next_assessment_date ? `
                            <div class="mb-0">
                                <strong class="text-dark">Next Assessment Date:</strong>
                                <span class="text-dark fw-medium">${formatDateToDDMMYYYY(healthplanCert.next_assessment_date)}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
                $('#badgePreview').before(viewContent);
                $('#certifiedInfo').hide();
                saveCertificationBtn.hide();
                closeBtnModal.hide();
                $('.modal-footer').hide();
            } else {
                $('#certifiedInfo').show();
                $('#certifiedInfo').html(`
            <div class="alert alert-success">
                <strong><em>No specific certification details available for this category.</em></strong>
            </div>
        `);
                $('#conditionSelect').closest('.col-md-4').hide();
                $('#issueDateInput').closest('.col-md-4').hide();
                $('#nextAssessmentInput').closest('.col-md-4').hide();
                $('#remarksInput').closest('.col-12').hide();
                conditionSelect.prop('disabled', true);
                remarksInput.prop('disabled', true);
                saveCertificationBtn.hide();
                closeBtnModal.hide();
                $('.modal-footer').hide();
            }
        }
        else {
            $('#conditionSelect').closest('.col-md-4').show();
            $('#issueDateInput').closest('.col-md-4').show();
            $('#nextAssessmentInput').closest('.col-md-4').show();
            $('#remarksInput').closest('.col-12').show();
            const issueDateTime = healthplanCert?.certified_on;
            const issueDateOnly = issueDateTime ? issueDateTime.split(' ')[0] : '';
            $('#issueDateInput').val(issueDateOnly);
            $('#nextAssessmentInput').val(healthplanCert?.next_assessment_date || '');
            $('#certificationViewContent').remove();
            $('.modal-footer').show();
            closeBtnModal.show();
            let hasExistingData = healthplanCert && (healthplanCert.condition || healthplanCert.remarks);
            if (hasExistingData) {
                $('#modalMode').text('Edit Certification');
                conditionSelect.val(healthplanCert.condition || '');
                remarksInput.val(healthplanCert.remarks || '');
                saveCertificationBtn.text('Update Certification').show();
            } else {
                $('#modalMode').text('Add Certification');
                conditionSelect.val('');
                remarksInput.val('');
                saveCertificationBtn.text('Save Certification').show();
            }
            $('#certifiedInfo').hide();
            conditionSelect.prop('disabled', false);
            remarksInput.prop('disabled', false);
        }
        updateBadgePreview();
    }

function capitalizeWords(str) {
  return (str || '').replace(/\b\w/g, c => c.toUpperCase());
}

function getAllStatuses(item) {
    const statuses = [];
    if (item.cancelled) statuses.push('Cancelled');
    if (item.no_show) statuses.push('No Show');
    if (item.test_completed) statuses.push('Test Completed');
    if (item.result_ready) statuses.push('Result Ready');
    if (item.certified) statuses.push('Certified');
    if (item.in_process) statuses.push('In Process');
    if (item.schedule) statuses.push('Scheduled');
    if (!statuses.length) statuses.push('Pending');
    return statuses;
}
   
    function formatDateToDDMMYYYY(dateString) {
        if (!dateString) return '-';
        try {
            let date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();
            return `${day}/${month}/${year}`;
        } catch (e) {
            return '-';
        }
    }
function openDatesModal(test) {
    // Remove existing modal if already present
    const existingModal = document.getElementById('datesModal');
    if (existingModal) existingModal.remove();

    // Extract employee name
    const employeeName = `${test.first_name || ''} ${test.last_name || ''}`.trim();

    // Format helper
    const formatDate = (dateStr) => {
        if (!dateStr) return 'Not Set';
        const d = new Date(dateStr.replace(' ', 'T'));
        if (isNaN(d)) return 'Not Set';
        return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
    };

    // Prepare date rows
    const dateRows = [
        { label: 'Assigned Date', value: formatDate(test.test_date) },
        { label: 'Due Date', value: formatDate(test.test_due_date) },
        { label: 'Diagnosis Date', value: formatDate(test.in_process) },
        { label: 'Assessment Date', value: formatDate(test.result_ready) },
    ];

    if (test.certified) {
        dateRows.push({ label: 'Certification Date', value: formatDate(test.certified) });
    }

    // Create modal HTML
    const modalHtml = `
    <div class="modal fade" id="datesModal" tabindex="-1" aria-labelledby="datesModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
        <div class="modal-content dates-modal">
          <div class="modal-header">
            <h5 class="modal-title" id="datesModalLabel">
              <i class="fas fa-calendar-alt me-2"></i>All Dates for <span id="employeeName">${employeeName}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="datesModalBody">
            ${dateRows.map(row => `
              <div class="date-entry mb-2">
                <strong>${row.label}</strong> - ${row.value}
              </div>
            `).join('')}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>`;

    // Append to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal using Bootstrap's JS
    const bootstrapModal = new bootstrap.Modal(document.getElementById('datesModal'));
    bootstrapModal.show();
}


        function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'pending':
                return 'bg-label-warning';
            case 'schedule':
                return 'bg-label-info';
            case 'in process':
                return 'bg-label-primary';
            case 'test completed':
                return 'bg-label-success';
            case 'result ready':
                return 'bg-label-success';
            case 'no show':
                return 'bg-label-danger';
            case 'certified':
                return 'bg-label-success';
            case 'cancelled':
                return 'bg-label-dark';
            default:
                return 'bg-label-secondary';
        }
    }


    function showPrescriptionModal(test) {
      if (!test.prescription_data || !test.prescription_data.prescription) {
        showToast('error', "No prescription data available");
        return;
      }
      const modal = document.getElementById('prescriptionModal');
      if (!modal) {
        console.error("Prescription modal not found");
        return;
      }
      const prescriptionData = test.prescription_data;
      const prescriptionDetails = prescriptionData.prescription_details || {};
      const prescription = prescriptionData.prescription;
      const prescriptionContainer = modal.querySelector('.prescription-container');
      if (prescriptionContainer) {
        const doctorHeader = prescriptionContainer.querySelector('.doctor-header span:first-child');
        if (doctorHeader) {
          doctorHeader.textContent = prescription.doctor_name || "Dr. John Doe";
        }
        const prescriptionId = prescriptionContainer.querySelector('.prescription-id');
        if (prescriptionId) { 
          prescriptionId.textContent = prescription.master_doctor_id + " " + prescription.prescription_row_id || "";
        }
        const patientInfo = prescriptionContainer.querySelector('.patient-info div:first-child');
        if (patientInfo) {
          patientInfo.textContent = `${test.name} - ${test.age} / ${prescription.employee_gender || "Other"} (${test.employee_id})`;
        }
        const drugTableBody = prescriptionContainer.querySelector('tbody');
        if (drugTableBody) {
          drugTableBody.innerHTML = '';
          Object.values(prescriptionDetails).forEach(drug => {
            if (!drug || typeof drug !== 'object') return;
            const row = document.createElement('tr');
            const drugNameCell = document.createElement('td');
            drugNameCell.className = 'drug-name';
            drugNameCell.textContent = `${drug.drug_name || 'N/A'} - ${drug.drug_strength || 'N/A'} (${getDrugTypeName(drug.drug_type)})`;
            row.appendChild(drugNameCell);
            const daysCell = document.createElement('td');
            daysCell.textContent = drug.prescribed_days || 'N/A';
            row.appendChild(daysCell);
            const morningCell = document.createElement('td');
            morningCell.textContent = drug.morning || '0';
            row.appendChild(morningCell);
            const afternoonCell = document.createElement('td');
            afternoonCell.textContent = drug.afternoon || '0';
            row.appendChild(afternoonCell);
            const eveningCell = document.createElement('td');
            eveningCell.textContent = drug.evening || '0';
            row.appendChild(eveningCell);
            const nightCell = document.createElement('td');
            nightCell.textContent = drug.night || '0';
            row.appendChild(nightCell);
            const intakeCell = document.createElement('td');
            intakeCell.textContent = getIntakeCondition(drug.intake_condition);
            row.appendChild(intakeCell);
            const remarksCell = document.createElement('td');
            remarksCell.textContent = drug.remarks || '';
            row.appendChild(remarksCell);
            drugTableBody.appendChild(row);
          });
        }
      }
      const bsModal = new bootstrap.Modal(modal);
      bsModal.show();
    }
    function getDrugTypeName(typeId) {
      const types = {
        1: "Tablet",
        2: "Capsule",
        3: "Syrup",
        4: "Drops",
        5: "Injection",
        6: "Cream",
        7: "Ointment",
        8: "Powder",
        9: "Lotion",
        10: "Gel",
        11: "Spray",
        12: "Foam"
      };
      return types[typeId] || "Other";
    }
    function getIntakeCondition(conditionId) {
      const conditions = {
        1: "Before Food",
        2: "After Food",
        3: "With Food",
        4: "Empty Stomach",
        5: "As Directed"
      };
      return conditions[conditionId] || "As Directed";
    }
    function navigateToTestDetails(testCode) {
      if (testCode) {
        window.location.href = `/ohc/test-details/${testCode}`;
      } else {
        console.error("Test code is missing");
        showToast('error', "Cannot view test details: missing test code");
      }
    }
    function showTestListModal(dataset) { console.log("I called"); console.log(dataset);
      const modal = document.getElementById('testListModal');
      const employeeNameElement = document.getElementById('modalEmployeeName');
      const testDateElement = document.getElementById('modalTestDate');
      const testListElement = document.getElementById('modalTestList');
      if (!modal || !employeeNameElement || !testDateElement || !testListElement) {
        console.error('Modal elements not found');
        return;
      }
      testListElement.innerHTML = '';
      employeeNameElement.textContent = `${dataset.employeeName} (${dataset.employeeAge}) - ${dataset.employeeId}`;
      if (dataset.testDate && dataset.testDate !== 'N/A') {
        const testDate = new Date(dataset.testDate);
        const formattedDate = `${testDate.getDate().toString().padStart(2, '0')}-${(testDate.getMonth() + 1).toString().padStart(2, '0')}-${testDate.getFullYear()}`;
        testDateElement.textContent = formattedDate;
      } else {
        testDateElement.textContent = 'N/A';
      }
      let testStructure = {};
      try {
        testStructure = JSON.parse(dataset.testStructure);
      } catch (e) {
        console.error('Error parsing test structure:', e);
      }
      if (Object.keys(testStructure).length > 0) {
        renderHierarchicalTests(testStructure, testListElement);
      } else {
        const noTestsElement = document.createElement('div');
        noTestsElement.textContent = 'No tests available';
        testListElement.appendChild(noTestsElement);
      }
      if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
      } else {
        console.error('Bootstrap is not available');
      }
    }
    function showObservationModal(testData) {
      const modal = document.getElementById('observationModal');
      if (!modal) {
        console.error('Observation modal not found');
        return;
      }
      document.getElementById('obsEmployeeId').textContent = testData.employee_id || 'N/A';
      document.getElementById('obsEmployeeName').textContent = testData.name || 'N/A';
      document.getElementById('obsEmployeeAge').textContent = testData.age || 'N/A';
      document.getElementById('obsDepartment').textContent = testData.department || 'N/A';
      if (testData.reporting_date_time) {
        const reportDate = new Date(testData.reporting_date_time);
        const formattedDate = `${reportDate.getDate().toString().padStart(2, '0')}-${(reportDate.getMonth() + 1).toString().padStart(2, '0')}-${reportDate.getFullYear()} ${reportDate.getHours().toString().padStart(2, '0')}:${reportDate.getMinutes().toString().padStart(2, '0')}`;
        document.getElementById('obsReportDate').textContent = formattedDate;
      } else {
        document.getElementById('obsReportDate').textContent = 'N/A';
      }
      const incidentType = testData.type_of_incident || 'N/A';
      let formattedIncidentType = 'N/A';
      if (incidentType !== 'N/A') {
        formattedIncidentType = incidentType
          .replace('industrialAccident', 'Industrial Accident')
          .replace('outsideAccident', 'Outside Accident')
          .replace('medicalIllness', 'Medical Illness');
      }
      document.getElementById('obsIncidentType').textContent = formattedIncidentType;
      document.getElementById('obsDoctorNotes').textContent = testData.doctor_notes || 'N/A';
      document.getElementById('obsMedicalHistory').textContent = testData.past_medical_history || 'N/A';
      const referral = testData.referral || 'N/A';
      let formattedReferral = 'N/A';
      if (referral !== 'N/A') {
        formattedReferral = referral
          .replace('OutsideReferral', 'Outside Referral')
          .replace('noOutsideReferral', 'No Outside Referral');
      }
      document.getElementById('obsReferral').textContent = formattedReferral;
      const medicalIllnessFields = document.getElementById('medicalIllnessFields');
      const accidentFields = document.getElementById('accidentFields');
      const siteOfInjuryContainer = document.getElementById('obsSiteOfInjuryContainer');
      medicalIllnessFields.style.display = 'none';
      accidentFields.style.display = 'none';
      if (incidentType === 'medicalIllness') {
        medicalIllnessFields.style.display = 'block';
        document.getElementById('obsMIBodyPart').textContent = testData.body_part || 'N/A';
        document.getElementById('obsMISymptoms').textContent = testData.symptoms || 'N/A';
        document.getElementById('obsMIMedicalSystem').textContent = testData.medical_system || 'N/A';
        document.getElementById('obsMIDiagnosis').textContent = testData.diagnosis || 'N/A';
      } else if (incidentType === 'industrialAccident' || incidentType === 'outsideAccident') {
        accidentFields.style.display = 'block';
        document.getElementById('obsNatureInjury').textContent = testData.nature_injury || 'N/A';
        document.getElementById('obsBodyPart').textContent = testData.body_part || 'N/A';
        document.getElementById('obsMechanismInjury').textContent = testData.mechanism_injury || 'N/A';
        const injurySeverityElement = document.getElementById('obsInjurySeverity');
        injurySeverityElement.innerHTML = '';
        if (testData.injury_color_text) {
          const [severityText, colorCode] = testData.injury_color_text.split('_');
          const colorIndicator = document.createElement('span');
          colorIndicator.className = 'severity-indicator';
          colorIndicator.style.backgroundColor = colorCode;
          injurySeverityElement.appendChild(colorIndicator);
          injurySeverityElement.appendChild(document.createTextNode(severityText));
        } else {
          injurySeverityElement.textContent = 'N/A';
        }
        let bodySideText = 'N/A';
        if (testData.body_side) {
          try {
            const bodySide = JSON.parse(testData.body_side);
            const sides = [];
            if (bodySide.left) sides.push('Left');
            if (bodySide.right) sides.push('Right');
            bodySideText = sides.length > 0 ? sides.join(', ') : 'N/A';
          } catch (e) {
            console.error('Error parsing body side:', e);
          }
        }
        document.getElementById('obsBodySide').textContent = bodySideText;
        if (incidentType === 'industrialAccident') {
          siteOfInjuryContainer.style.display = 'block';
          let siteOfInjuryText = 'N/A';
          if (testData.site_of_injury) {
            try {
              const siteOfInjury = JSON.parse(testData.site_of_injury);
              const sites = [];
              if (siteOfInjury.shopFloor) sites.push('Shop Floor');
              if (siteOfInjury.nonShopFloor) sites.push('Non-Shop Floor');
              siteOfInjuryText = sites.length > 0 ? sites.join(', ') : 'N/A';
            } catch (e) {
              console.error('Error parsing site of injury:', e);
            }
          }
          document.getElementById('obsSiteOfInjury').textContent = siteOfInjuryText;
        } else {
          siteOfInjuryContainer.style.display = 'none';
        }
      }
      if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
      } else {
        console.error('Bootstrap is not available');
      }
    }
    function renderHierarchicalTests(testStructure, container) {
      Object.keys(testStructure).forEach(groupName => {
        const group = testStructure[groupName];
        const groupHeader = document.createElement('div');
        groupHeader.className = 'test-group-title';
        groupHeader.textContent = groupName;
        container.appendChild(groupHeader);
        if (typeof group === 'object') {
          Object.keys(group).forEach(subGroupName => {
            const subGroup = group[subGroupName];
            const subGroupHeader = document.createElement('div');
            subGroupHeader.className = 'test-subgroup-title';
            subGroupHeader.textContent = subGroupName;
            container.appendChild(subGroupHeader);
            if (Array.isArray(subGroup)) {
              renderTestItems(subGroup, container, 'subgroup-test-item');
            } else if (typeof subGroup === 'object') {
              let directTests = [];
              let subSubGroups = {};
              Object.keys(subGroup).forEach(key => {
                const item = subGroup[key];
                if (!isNaN(parseInt(key))) {
                  directTests.push(item);
                } else {
                  subSubGroups[key] = item;
                }
              });
              if (directTests.length > 0) {
                renderTestItems(directTests, container, 'subgroup-test-item');
              }
              Object.keys(subSubGroups).forEach(subSubGroupName => {
                const item = subSubGroups[subSubGroupName];
                const subSubGroupHeader = document.createElement('div');
                subSubGroupHeader.className = 'test-subsubgroup-title';
                subSubGroupHeader.textContent = subSubGroupName;
                container.appendChild(subSubGroupHeader);
                if (Array.isArray(item)) {
                  renderTestItems(item, container, 'subsubgroup-test-item');
                } else if (typeof item === 'object' && item.name) {
                  renderSingleTest(item.name, container, 'subsubgroup-test-item');
                } else if (typeof item === 'string') {
                  renderSingleTest(item, container, 'subsubgroup-test-item');
                }
              });
            }
          });
        } else if (typeof group === 'string') {
          renderSingleTest(group, container, 'test-item');
        }
      });
    }
    function renderTestItems(items, container, className) {
      items.forEach(testItem => {
        const testElement = document.createElement('div');
        testElement.className = className;
        if (typeof testItem === 'object' && testItem.name) {
          testElement.textContent = testItem.name;
        } else if (typeof testItem === 'string') {
          testElement.textContent = testItem;
        } else {
          testElement.textContent = 'Unknown Test';
        }
        container.appendChild(testElement);
      });
    }
    function renderSingleTest(testName, container, className) {
      const testElement = document.createElement('div');
      testElement.className = className;
      testElement.textContent = testName;
      container.appendChild(testElement);
    }
    function showErrorInTable(message) {
      clearTable();
      const errorRow = document.createElement('tr');
      const errorCell = document.createElement('td');
      errorCell.setAttribute('colspan', '6');
      errorCell.className = 'text-center text-danger';
      errorCell.textContent = message;
      errorRow.appendChild(errorCell);
      tableBody.appendChild(errorRow);
    }
  });
</script>
@endsection
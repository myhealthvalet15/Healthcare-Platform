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
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector('.table-border-bottom-0');
    let originalTestsData = [];
    let filteredTestsData = [];
    init();
    function init() {
        $('#fromDate, #toDate').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });
        createTestModal();
        createObservationModal();
        showLoadingIndicator();
        fetchTestData();
        fetchTestNames();
        setTimeout(updateTableHeader, 100);
        setupFilterEventListeners();
    }
    function fetchTestNames() {
        try {
            const data = apiRequest({
                url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllMasterTests',
                method: 'GET',
                onSuccess: (response) => {
                    if (response && response.result && Array.isArray(response.data)) {
                        const targets = [
                            document.getElementById('filterTestSelect')
                        ];
                        targets.forEach((selectElement) => {
                            if (selectElement) {
                                response.data.forEach((test) => {
                                    const option = document.createElement('option');
                                    option.value = test.master_test_id;
                                    option.textContent = test.test_name;
                                    selectElement.appendChild(option);
                                });
                            }
                        });
                    } else {
                        showToast('error', 'Invalid data format, ' + JSON.stringify(response));
                    }
                },
                onError: (error) => {
                    showToast('error', 'Error Fetching Test Records, ' + error);
                }
            });
        } catch (error) {
            showToast('error', 'Error Fetching Test Records ' + error);
        }
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
    function fetchTestData() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/ohc/getAllTests',
            method: 'GET',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    originalTestsData = response.data;
                    filteredTestsData = [...response.data];
                    populateTestTable(filteredTestsData);
                } else {
                    showErrorInTable('Invalid data format received');
                    console.warn('Error: ', response);
                }
            },
            onError: function (error) {
                showErrorInTable('Error loading data: ' + error.message);
                showToast('error', "Failed to load tests: " + error);
            }
        });
    }
    function setupFilterEventListeners() {
        const applyFiltersBtn = document.getElementById('applyFilters');
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', applyFilters);
        }
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', clearFilters);
        }
    }
    function applyFilters() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase().trim();
        const fromDateInput = document.getElementById('fromDate').value;
        const toDateInput = document.getElementById('toDate').value;
        const selectedTests = Array.from(document.getElementById('filterTestSelect').selectedOptions)
            .map(option => option.text.toLowerCase());
        filteredTestsData = originalTestsData.filter(test => {
            let nameIdMatch = true;
            if (searchInput) {
                const employeeName = (test.name || '').toLowerCase();
                const employeeId = (test.employee_id || '').toString().toLowerCase();
                nameIdMatch = employeeName.includes(searchInput) || employeeId.includes(searchInput);
            }
            let dateMatch = true;
            if (fromDateInput || toDateInput) {
                if (test.reporting_date_time && test.reporting_date_time !== 'Test Results') {
                    const testDate = new Date(test.reporting_date_time);
                    if (fromDateInput && toDateInput) {
                        const fromDate = new Date(fromDateInput);
                        const toDate = new Date(toDateInput);
                        dateMatch = testDate >= fromDate && testDate <= toDate;
                    } else if (fromDateInput) {
                        const fromDate = new Date(fromDateInput);
                        dateMatch = testDate >= fromDate;
                    } else if (toDateInput) {
                        const toDate = new Date(toDateInput);
                        dateMatch = testDate <= toDate;
                    }
                } else {
                    dateMatch = false;
                }
            }
            let testMatch = true;
            if (selectedTests.length > 0) {
                const testList = extractAllTests(test.tests);
                const testListLower = testList.map(t => t.toLowerCase());
                testMatch = selectedTests.every(selectedTest =>
                    testListLower.some(testName => testName.includes(selectedTest))
                );
            }
            return nameIdMatch && dateMatch && testMatch;
        });
        populateTestTable(filteredTestsData);
        const totalRecords = originalTestsData.length;
        const filteredRecords = filteredTestsData.length;
        if (filteredRecords === 0) {
            showToast('info', 'No records found matching the selected filters');
        } else if (filteredRecords !== totalRecords) {
            showToast('success', `Showing ${filteredRecords} of ${totalRecords} records`);
        }
    }
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('fromDate').value = '';
        document.getElementById('toDate').value = '';
        const filterTestSelect = document.getElementById('filterTestSelect');
        if (filterTestSelect) {
            Array.from(filterTestSelect.options).forEach(option => {
                option.selected = false;
            });
            if (typeof $(filterTestSelect).select2 === 'function') {
                $(filterTestSelect).val(null).trigger('change');
            }
        }
        filteredTestsData = [...originalTestsData];
        populateTestTable(filteredTestsData);
        showToast('info', 'Filters cleared');
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
            const dateA = new Date(a.created_on);
            const dateB = new Date(b.created_on);
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
    function showNoDataMessage() {
        const noDataRow = document.createElement('tr');
        const noDataCell = document.createElement('td');
        noDataCell.setAttribute('colspan', '6');
        noDataCell.className = 'text-center py-4';
        const container = document.createElement('div');
        container.className = 'text-muted';
        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-search mb-2';
        icon.style.fontSize = '2rem';
        const message = document.createElement('p');
        message.className = 'mb-0';
        message.textContent = 'No records found matching your search criteria';
        const suggestion = document.createElement('small');
        suggestion.textContent = 'Try adjusting your filters or search terms';
        container.appendChild(icon);
        container.appendChild(message);
        container.appendChild(suggestion);
        noDataCell.appendChild(container);
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
    function extractAllTests(testStructure) {
        let allTests = [];
        if (!testStructure || typeof testStructure !== 'object') {
            return allTests;
        }
        function recursiveExtract(obj, path = []) {
            if (Array.isArray(obj)) {
                obj.forEach(item => {
                    if (typeof item === 'object' && item.name) {
                        allTests.push(item.name);
                    } else if (typeof item === 'string') {
                        allTests.push(item);
                    } else if (typeof item === 'object') {
                        recursiveExtract(item, path);
                    }
                });
            } else if (typeof obj === 'object') {
                Object.keys(obj).forEach(key => {
                    const value = obj[key];
                    if (typeof value === 'string') {
                        allTests.push(value);
                    } else if (typeof value === 'object' && value.name) {
                        allTests.push(value.name);
                    } else if (typeof value === 'object' || Array.isArray(value)) {
                        recursiveExtract(value, [...path, key]);
                    }
                });
            }
        }
        recursiveExtract(testStructure);
        return [...new Set(allTests)];
    }
    function createTestRow(test) {
        const row = document.createElement('tr');
        const dateCell = document.createElement('td');
        const dateSpan = document.createElement('span');
        dateSpan.className = 'fw-medium';
        if (test.created_on && test.created_on !== 'N/A') {
            const testDate = new Date(test.created_on);
            const formattedDate = `${testDate.getDate().toString().padStart(2, '0')}-${(testDate.getMonth() + 1).toString().padStart(2, '0')}-${testDate.getFullYear().toString().substring(2)}`;
            dateSpan.textContent = formattedDate;
        } else {
            dateSpan.textContent = 'N/A';
        }
        dateCell.appendChild(dateSpan);
        row.appendChild(dateCell);
        const employeeCell = document.createElement('td');
        const nameSpan = document.createElement('b');
        nameSpan.textContent = test.name || 'N/A';
        employeeCell.appendChild(nameSpan);
        employeeCell.appendChild(document.createTextNode(` (${test.age || 'N/A'}) - ${test.employee_id || 'N/A'}`));
        row.appendChild(employeeCell);
        const deptCell = document.createElement('td');
        deptCell.textContent = test.department || 'N/A';
        row.appendChild(deptCell);
        const testsCell = document.createElement('td');
        const testsContainer = document.createElement('div');
        if (test.tests) {
            const allTests = extractAllTests(test.tests);
            if (allTests.length > 0) {
                testsContainer.className = 'test-list-clickable';
                const viewIcon = document.createElement('i');
                viewIcon.className = 'fa-solid fa-list-ul';
                testsContainer.appendChild(viewIcon);
                const textSpan = document.createElement('span');
                textSpan.textContent = allTests.length > 2
                    ? `${allTests.slice(0, 2).join(', ')}...`
                    : allTests.join(', ');
                testsContainer.appendChild(textSpan);
                testsContainer.dataset.testStructure = JSON.stringify(test.tests);
                testsContainer.dataset.employeeName = test.name || 'N/A';
                testsContainer.dataset.employeeId = test.employee_id || 'N/A';
                testsContainer.dataset.employeeAge = test.age || 'N/A';
                testsContainer.dataset.testDate = test.reporting_date_time || 'N/A';
                testsContainer.setAttribute('title', 'Click to view all tests');
                testsContainer.addEventListener('click', function () {
                    showTestListModal(this.dataset);
                });
            } else {
                testsContainer.textContent = 'N/A';
            }
        } else {
            testsContainer.textContent = 'N/A';
        }
        testsCell.appendChild(testsContainer);
        row.appendChild(testsCell);
        const observationCell = document.createElement('td');
        const observationIcon = document.createElement('i');
        observationIcon.className = 'fa-solid fa-stethoscope';
        observationIcon.style.marginRight = '10px';
        if (test.fromOp !== 0) {
            observationIcon.style.cursor = 'pointer';
            observationIcon.style.color = '#696cff';
            observationIcon.title = "Click to view medical observations";
            observationIcon.addEventListener('click', function () {
                showObservationModal(test);
            });
        } else {
            observationIcon.style.cursor = 'default';
            observationIcon.style.color = '#c9c9c9';
            observationIcon.title = "Not available";
        }
        observationCell.appendChild(observationIcon);
        const prescriptionIcon = document.createElement('i');
        prescriptionIcon.className = 'ti ti-prescription';
        prescriptionIcon.style.marginLeft = '10px';
        if (test.fromOp !== 0) {
            const hasPrescription = test.prescription_data && test.prescription_data.prescription;
            if (hasPrescription) {
                prescriptionIcon.style.cursor = 'pointer';
                prescriptionIcon.style.color = '#696cff';
                prescriptionIcon.title = "Click to view prescriptions";
                prescriptionIcon.addEventListener('click', function () {
                    showPrescriptionModal(test);
                });
            } else {
                prescriptionIcon.style.cursor = 'default';
                prescriptionIcon.style.color = '#c9c9c9';
                prescriptionIcon.title = "No prescription available";
            }
        } else {
            prescriptionIcon.style.cursor = 'default';
            prescriptionIcon.style.color = '#c9c9c9';
            prescriptionIcon.title = "Not available";
        }
        observationCell.appendChild(prescriptionIcon);
        row.appendChild(observationCell);
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        const icon = document.createElement('i');
        const status = test.healthplan_status || 'Unknown';
        if (status === 'Pending' || status === 'Cancelled') {
            statusBadge.className = 'badge bg-label-danger d-inline-flex align-items-center gap-1 px-3 py-2 fs-6 status-badge';
            icon.className = status === 'Pending' ? 'fa-solid fa-pencil' : 'fa-solid fa-ban';
        } else if (status === 'Schedule' || status === 'In Process') {
            statusBadge.className = 'badge bg-label-warning d-inline-flex align-items-center gap-1 px-3 py-2 fs-6 status-badge';
            icon.className = 'fa-solid fa-clock';
        } else if (['Test Completed', 'Result Ready', 'No Show', 'Certified'].includes(status)) {
            statusBadge.className = 'badge bg-label-success d-inline-flex align-items-center gap-1 px-3 py-2 fs-6 status-badge';
            icon.className = status === 'Result Ready' ? 'fa-solid fa-binoculars' : 'fa-solid fa-check';
        } else {
            statusBadge.className = 'badge bg-label-info d-inline-flex align-items-center gap-1 px-3 py-2 fs-6 status-badge';
            icon.className = 'fa-solid fa-info-circle';
        }
        statusBadge.appendChild(icon);
        statusBadge.appendChild(document.createTextNode(' ' + status));
        statusBadge.dataset.testCode = test.test_code;
        statusBadge.title = "Click to view test details";
        statusBadge.addEventListener('click', function () {
            navigateToTestDetails(this.dataset.testCode);
        });
        statusCell.appendChild(statusBadge);
        row.appendChild(statusCell);
        return row;
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
    function showTestListModal(dataset) {
        console.log("I called"); console.log(dataset);
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

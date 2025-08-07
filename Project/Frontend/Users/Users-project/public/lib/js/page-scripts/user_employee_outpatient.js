document.addEventListener('DOMContentLoaded', function () {
    fetchHealthRegistryData();
    populateIncidentTypeColorCodes();
    createTestModal();
    const advancedBtn = document.getElementById('advancedFiltersBtn');
    const additionalFilters = document.getElementById('additionalFilters');
    let isVisible = false;
});
function capitalizeFirstLetter(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1).toLowerCase() : '';
}
function populateAddedOutpatientData(data) {
    const formatDate = dt => (dt ? new Date(dt).toLocaleString('en-GB') : 'N/A');
    const entry = data;
    const opData = entry.op_registry_datas || {};
    const opTimes = opData.op_registry_times || {};
    const opRegistry = opData.op_registry || {};
    const prescribedTests = opData.prescribed_test_data || [];
    const testValueMap = prescribedTests.reduce((acc, test) => {
        acc[test.master_test_id] = test.test_results ?? 'Pending';
        return acc;
    }, {});
    const modalBody = document.getElementById('detailsModalBody');
    modalBody.innerHTML = ` <div class="row mb-3">
        <div class="col-12">
            <div class="p-2" style="background-color: rgb(107, 27, 199); border-radius: 4px;">
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color: #fff; font-weight: bold;">
                        Reporting Time: ${formatDate(opTimes.reporting_date_time)}
                    </span>
                    <span style="color: #fff; font-weight: bold;">
                        Incident Type: ${opRegistry.type_of_incident || 'N/A'}
                    </span>
                </div>
            </div>
        </div>
    </div>
        <div class="row mb-4">
            <!-- LEFT COLUMN -->
            <div class="col-md-4">
                <!-- TIMINGS -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-3">
                        <h5 class="text-primary mb-3">Timings</h5>
                        ${createDisplayRow('Reported At', formatDate(opTimes.reporting_date_time))}
                        ${createDisplayRow('Incident At', formatDate(opTimes.incident_date_time))}
                        ${createDisplayRow('Leave From', formatDate(opTimes.leave_from_date_time))}
                        ${createDisplayRow('Leave Upto', formatDate(opTimes.leave_upto_date_time))}
                        ${createDisplayRow('Out Time', formatDate(opTimes.out_date_time))}
                        ${createDisplayRow('Lost Hours', opTimes.lost_hours)}
                    </div>
                </div>
                <!-- VITALS -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-3">
                        <h5 class="text-primary mb-3">Vital Parameters</h5>
                        ${createDisplayRow('Temperature', testValueMap['167'], '°F')}
                        ${createDisplayRow('BP Systolic', testValueMap['168'], 'mmHg')}
                        ${createDisplayRow('BP Diastolic', testValueMap['169'], 'mmHg')}
                        ${createDisplayRow('Pulse Rate', testValueMap['170'], 'Beats/min')}
                        ${createDisplayRow('Respiratory Rate', testValueMap['171'], 'bpm')}
                        ${createDisplayRow('SPO2', testValueMap['172'], '%')}
                        ${createDisplayRow('Random Glucose', testValueMap['173'], 'mg/dl')}
                    </div>
                </div>
            </div>
            <!-- RIGHT COLUMN -->
            <div class="col-md-8">
                <!-- MEDICAL & ADMIN -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="text-primary mb-3">Medical Details</h5>
                        ${createDisplayRow('Type of Incident', opRegistry.type_of_incident)}
                        ${createDisplayRow('Symptoms', opData.symptoms)}
                        ${createDisplayRow('Body Parts', opData.body_parts)}
                        ${createDisplayRow('Medical Systems', opData.medical_systems)}
                        ${createDisplayRow('Diagnosis', opData.diagnosis)}
                        <h5 class="text-primary mt-4 mb-3">Administrative Info</h5>
                        ${createDisplayRow('First Aid By', opRegistry.first_aid_by)}
                        ${createDisplayRow('Fitness Certificate', opRegistry.fitness_certificate ? 'Yes' : 'No')}
                        ${createDisplayRow('Movement Slip', opRegistry.movement_slip ? 'Yes' : 'No')}
                    </div>
                </div>
                <!-- OBSERVATIONS -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="text-primary mb-3">Observations</h5>
                        ${createDisplayRow('Doctor Notes', opRegistry.doctor_notes)}
                        ${createDisplayRow('Past Medical History', opRegistry.past_medical_history)}
                        ${createDisplayRow('Referral', opRegistry.referral)}
                    </div>
                </div>
            </div>
        </div>
    `;
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    modal.show();
}
function createDisplayRow(label, value, unit = '') {
    if (!value) return '';
    return `
        <div class="d-flex justify-content-between mb-2">
            <div class="text-secondary">${label}</div>
            <div class="fw-semibold text-dark text-end">${value} ${unit}</div>
        </div>
    `;
}
function formatDate(dateTimeStr) {
    try {
        if (dateTimeStr.includes('.')) {
            dateTimeStr = dateTimeStr.split('.')[0];
        }
        let date = new Date(dateTimeStr);
        if (isNaN(date.getTime())) {
            return '';
        }
        let year = date.getFullYear();
        let month = (date.getMonth() + 1).toString().padStart(2, '0');
        let day = date.getDate().toString().padStart(2, '0');
        let hours = date.getHours().toString().padStart(2, '0');
        let minutes = date.getMinutes().toString().padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    } catch (e) {
        console.error('Error formatting date:', e);
        return '';
    }
}
function fetchHealthRegistryData(filters = null) {
    apiRequest({
        url: '/ohc/health-registry/getAllHealthRegistry',
        method: 'GET',
        onSuccess: data => {
            console.log('Response Data:', data);
            if (data.result && data.data && Array.isArray(data.data)) {
                populateTable(data.data, filters);
                setTimeout(() => {
                    const searchInput = document.getElementById('registrySearchInput');
                    if (searchInput) {
                        searchInput.addEventListener('input', function () {
                            const searchTerm = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#health-registry-table-body tr');
                            rows.forEach(row => {
                                if (row.querySelector('.spinner-border') || row.textContent.includes('No')) {
                                    row.style.display = '';
                                    return;
                                }
                                const symptomsCell = row.children[2]?.textContent.toLowerCase() || '';
                                const diagnosisCell = row.children[4]?.textContent.toLowerCase() || '';
                                const match = symptomsCell.includes(searchTerm) || diagnosisCell.includes(searchTerm);
                                row.style.display = match ? '' : 'none';
                            });
                        });
                    }
                }, 0);
            } else {
                showErrorInTable('Invalid data format received');
            }
        },
        onError: error => {
            console.error('Error fetching health registry data:', error);
            showErrorInTable('Error loading data: ' + error.message);
        }
    });
}
function showErrorInTable(message) {
    const tableBody = document.getElementById('health-registry-table-body');
    tableBody.innerHTML = '';
    const errorRow = document.createElement('tr');
    const errorCell = document.createElement('td');
    errorCell.setAttribute('colspan', '5');
    errorCell.className = 'text-center text-danger';
    errorCell.textContent = message;
    errorRow.appendChild(errorCell);
    tableBody.appendChild(errorRow);
}
function populateTable(registryData, filters = null) {
    console.log('refgistrydata', registryData);
    const tableBody = document.getElementById('health-registry-table-body');
    tableBody.innerHTML = '';
    registryData.sort((a, b) => {
        const dateA = new Date(a.registry.day_of_registry);
        const dateB = new Date(b.registry.day_of_registry);
        return dateB - dateA;
    });
    if (registryData.length === 0) {
        const noDataRow = document.createElement('tr');
        const noDataCell = document.createElement('td');
        noDataCell.setAttribute('colspan', '5');
        noDataCell.className = 'text-center';
        noDataCell.textContent = 'No health registry data found';
        noDataRow.appendChild(noDataCell);
        tableBody.appendChild(noDataRow);
        return;
    }
    const relationships = buildRegistryRelationships(registryData);
    if (filters === null) {
        registryData.forEach(entry => {
            const row = createTableRow(entry, relationships);
            tableBody.appendChild(row);
        });
        return;
    }
    let filteredResults = 0;
    registryData.forEach(entry => {
        const employeeName = entry.employee_name.toLowerCase();
        const employeeId = entry.employee_id.toLowerCase();
        const doctorId = entry.registry.doctor_id ? entry.registry.doctor_id.toString() : '';
        const fromDate = filters.fromDate ? new Date(filters.fromDate) : null;
        const toDate = filters.toDate ? new Date(filters.toDate) : null;
        let passesInjuryColorFilter = true;
        if (filters.injuryColor) {
            if (filters.injuryColor === 'Medical') {
                passesInjuryColorFilter = entry.registry.injury_color_text === null;
            } else if (entry.registry.injury_color_text) {
                passesInjuryColorFilter = entry.registry.injury_color_text.split('_')[0] === filters.injuryColor;
            } else {
                passesInjuryColorFilter = false;
            }
        }
        let passesDepartmentFilter = true;
        if (filters.departmentName && filters.departmentName !== '') {
            passesDepartmentFilter =
                entry.department && entry.department.toLowerCase() === filters.departmentName.toLowerCase();
        }
        let passesInjuryTypeFilter = true;
        if (filters.injuryTypeId && filters.injuryTypeId !== '') {
            try {
                if (entry.registry.nature_injury && entry.registry.nature_injury !== '[]') {
                    const natureInjuryArray = JSON.parse(entry.registry.nature_injury);
                    passesInjuryTypeFilter = natureInjuryArray.includes(filters.injuryTypeId);
                } else {
                    passesInjuryTypeFilter = false;
                }
            } catch (e) {
                console.error('Error parsing nature_injury JSON:', e);
                passesInjuryTypeFilter = false;
            }
        }
        let passesMedicalSystemFilter = true;
        if (filters.medicalSystemId && filters.medicalSystemId !== '') {
            try {
                if (entry.registry.medical_system && entry.registry.medical_system !== '[]') {
                    const medicalSystemArray = JSON.parse(entry.registry.medical_system);
                    passesMedicalSystemFilter = medicalSystemArray.includes(filters.medicalSystemId);
                } else {
                    passesMedicalSystemFilter = false;
                }
            } catch (e) {
                console.error('Error parsing medical_system JSON:', e);
                passesMedicalSystemFilter = false;
            }
        }
        let passesGenderFilter = true;
        if (filters.gender && filters.gender !== '') {
            passesGenderFilter =
                entry.employee_gender && entry.employee_gender.toLowerCase() === filters.gender.toLowerCase();
        }
        if (
            (employeeName.includes(filters.nameOrTest.toLowerCase()) ||
                employeeId.includes(filters.nameOrTest.toLowerCase())) &&
            passesInjuryColorFilter &&
            passesDepartmentFilter &&
            passesInjuryTypeFilter &&
            passesMedicalSystemFilter &&
            passesGenderFilter &&
            (doctorId === filters.doctorId || filters.doctorId === '') &&
            (!fromDate || new Date(entry.registry.day_of_registry) >= fromDate) &&
            (!toDate || new Date(entry.registry.day_of_registry) <= toDate)
        ) {
            const row = createTableRow(entry, relationships);
            tableBody.appendChild(row);
            filteredResults++;
        }
    });
    if (filteredResults === 0) {
        const noDataRow = document.createElement('tr');
        const noDataCell = document.createElement('td');
        noDataCell.setAttribute('colspan', '5');
        noDataCell.className = 'text-center';
        noDataCell.textContent = 'No results found for the given filters';
        noDataRow.appendChild(noDataCell);
        tableBody.appendChild(noDataRow);
    }
}
function createTableRow(entry, registryRelationships) {
    const row = document.createElement('tr');
    const createCell = (text = 'N/A') => {
        const cell = document.createElement('td');
        cell.classList.add('text-start');
        cell.textContent = text;
        return cell;
    };
    let formattedDate = 'N/A';
    if (entry.registry_times?.reporting_date_time) {
        const date = new Date(entry.registry_times.reporting_date_time);
        formattedDate = `${date.getDate().toString().padStart(2, '0')}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getFullYear().toString().slice(-2)}`;
    }
    row.appendChild(createCell(formattedDate));
    let typeOfIncident = entry.registry?.type_of_incident || 'N/A';
    if (typeOfIncident === 'medicalIllness') {
        typeOfIncident = 'Medical Illness';
    }
    const typeCell = document.createElement('td');
    typeCell.classList.add('text-start');
    const iconContainer = document.createElement('span');
    iconContainer.className = 'd-inline-flex align-items-center';
    const dotIcon = document.createElement('i');
    dotIcon.className = 'fa-solid fa-circle-dot';
    let dotColor = '#808080';
    if (entry.registry && entry.registry.injury_color_text) {
        const colorCodeMatch = entry.registry.injury_color_text.match(/#[0-9A-Fa-f]{6}/);
        if (colorCodeMatch && colorCodeMatch[0]) {
            dotColor = colorCodeMatch[0];
        }
    }
    dotIcon.style.color = dotColor;
    dotIcon.style.fontSize = '0.8rem';
    dotIcon.style.marginRight = '6px';
    iconContainer.appendChild(dotIcon);
    const typeText = document.createElement('span');
    typeText.textContent = typeOfIncident;
    typeText.className = 'fw-medium';
    iconContainer.appendChild(typeText);
    typeCell.appendChild(iconContainer);
    row.appendChild(typeCell);
    let symptomsOrInjury = 'N/A';
    if (entry.registry) {
        if (entry.registry.type_of_incident === 'medicalIllness') {
            symptomsOrInjury = entry.symptom_names?.join(', ') || 'N/A';
        } else {
            symptomsOrInjury = entry.nature_of_injury_names?.join(', ') || 'N/A';
        }
    }
    row.appendChild(createCell(symptomsOrInjury));
    let mechanismAndDiagnosis = [];
    if (Array.isArray(entry.mechanism_injury) && entry.mechanism_injury.length > 0) {
        mechanismAndDiagnosis.push(`Mechanism: ${entry.mechanism_injury.join(', ')}`);
    }
    if (Array.isArray(entry.diagnosis_names) && entry.diagnosis_names.length > 0) {
        const validDiagnosis = entry.diagnosis_names.filter(d => typeof d === 'string' && d.trim() !== '');
        if (validDiagnosis.length > 0) {
            mechanismAndDiagnosis.push(`${validDiagnosis.join(', ')}`);
        }
    } else if (typeof entry.registry?.diagnosis === 'string' && entry.registry.diagnosis.trim() !== '') {
        mechanismAndDiagnosis.push(` ${entry.registry.diagnosis.trim()}`);
    }
    const mechanismDiagnosisCell = mechanismAndDiagnosis.length > 0 ? mechanismAndDiagnosis.join(' | ') : 'N/A';
    row.appendChild(createCell(mechanismDiagnosisCell));
    let bodyPartAndSystem = [];
    if (Array.isArray(entry.body_part) && entry.body_part.length > 0) {
        bodyPartAndSystem.push(`Body Part: ${entry.body_part.join(', ')}`);
    }
    if (Array.isArray(entry.medical_system) && entry.medical_system.length > 0) {
        bodyPartAndSystem.push(`Medical System: ${entry.medical_system.join(', ')}`);
    }
    const bodySystemCell = bodyPartAndSystem.length > 0 ? bodyPartAndSystem.join(' | ') : 'N/A';
    row.appendChild(createCell(bodySystemCell));
    const detailsCell = document.createElement('td');
    const iconFlexContainer = document.createElement('div');
    iconFlexContainer.className = 'd-flex align-items-center';
    const empId = entry.employee_id.toLowerCase() || '0';
    const opId = entry.registry?.op_registry_id || '0';
    const isFollowUp = entry.registry?.follow_up_op_registry_id > 0;
    let badgeClass = entry.registry?.open_status === '1' ? 'bg-label-danger' : 'bg-label-success';
    let badgeText = isFollowUp ? 'F' : 'N';
    const badgeLink = document.createElement('a');
    badgeLink.style.color = 'inherit';
    badgeLink.style.textDecoration = 'none';
    badgeLink.className = 'me-2 d-flex align-items-center justify-content-center';
    const badge = document.createElement('span');
    badge.className = `badge ${badgeClass}`;
    badge.textContent = badgeText;
    if (
        badgeText === 'N' &&
        registryRelationships?.has(entry.registry?.op_registry_id?.toString()) &&
        registryRelationships.get(entry.registry?.op_registry_id?.toString()) === true
    ) {
        badge.style.fontStyle = 'italic';
    }
    badgeLink.appendChild(badge);
    if (badgeText === 'N') {
        const hasOpenChildren =
            registryRelationships?.has(entry.registry?.op_registry_id?.toString()) &&
            registryRelationships.get(entry.registry?.op_registry_id?.toString()) === true;
        if (hasOpenChildren) {
            badgeLink.href = 'javascript:void(0);';
            badgeLink.addEventListener('click', () => {
                if (!document.getElementById('openChildrenModal')) {
                    createOpenChildrenModal();
                }
                const employeeNameElement = document.getElementById('openChildrenEmployeeName');
                if (employeeNameElement) {
                    employeeNameElement.textContent = entry.employee_name || 'N/A';
                }
                const modal = new bootstrap.Modal(document.getElementById('openChildrenModal'));
                modal.show();
            });
        } else if (entry.registry?.open_status === '1') {
            badgeLink.href = 'javascript:void(0);';
            badgeLink.addEventListener('click', () => {
                if (!document.getElementById('followUpModal')) {
                    createFollowUpModal();
                }
                const employeeNameElement = document.getElementById('followUpEmployeeName');
                if (employeeNameElement) {
                    employeeNameElement.textContent = entry.employee_name || 'N/A';
                }
                const modal = new bootstrap.Modal(document.getElementById('followUpModal'));
                modal.show();
            });
        } else {
            badgeLink.href = `/ohc/health-registry/add-follow-up-registry/add-follow-up-outpatient/${empId}/op/${opId}`;
        }
    } else {
        badgeLink.href = 'javascript:void(0);';
        badgeLink.style.pointerEvents = 'none';
    }
    iconFlexContainer.appendChild(badgeLink);
    const viewLink = document.createElement('a');
    viewLink.href = 'javascript:void(0);';
    viewLink.className = 'me-2 d-flex align-items-center justify-content-center';
    viewLink.innerHTML = `<i class="ti ti-eye icon-base"></i>`;
    viewLink.addEventListener('click', () => {
        apiRequest({
            url: `/UserEmployee/getHealthRegistryForEmployee/${entry.employee_id.toLowerCase()}/op/${entry.registry?.op_registry_id ?? ''}`,
            method: 'GET',
            onSuccess: function (data) {
                populateAddedOutpatientData(data);
                new bootstrap.Modal(document.getElementById('detailsModal')).show();
            },
            onError: function () {
                alert('Failed to load employee data.');
            }
        });
    });
    iconFlexContainer.appendChild(viewLink);
    const hasRx = entry.prescriptionsForRegistry?.prescription?.prescription_details;
    const rxLink = document.createElement('a');
    rxLink.href = 'javascript:void(0);';
    rxLink.className = 'me-2 d-flex align-items-center justify-content-center';
    const rxIcon = document.createElement('i');
    rxIcon.className = 'ti ti-prescription icon-base';
    if (hasRx) {
        rxLink.addEventListener('click', () => {
            populatePrescriptionModal(entry, formattedDate);
            new bootstrap.Modal(document.getElementById('prescriptionModal')).show();
        });
    } else {
        rxIcon.style.opacity = '0.5';
        rxLink.style.pointerEvents = 'none';
    }
    rxLink.appendChild(rxIcon);
    iconFlexContainer.appendChild(rxLink);
    const hasTests = entry.prescribed_tests?.length > 0;
    const testLink = document.createElement('a');
    testLink.href = 'javascript:void(0);';
    testLink.className = 'me-2 d-flex align-items-center justify-content-center';
    const testIcon = document.createElement('i');
    testIcon.className = 'ti ti-microscope icon-base';
    if (hasTests) {
        testLink.addEventListener('click', () => {
            fetchTestDataForEmployee(entry.employee_id, entry.registry?.op_registry_id);
        });
    } else {
        testIcon.style.opacity = '0.5';
        testLink.style.pointerEvents = 'none';
    }
    testLink.appendChild(testIcon);
    iconFlexContainer.appendChild(testLink);
    const isReferred = entry.registry?.referral === 'OutsideReferral';
    const hospitalLink = document.createElement('a');
    hospitalLink.href = 'javascript:void(0);';
    hospitalLink.className = 'd-flex align-items-center justify-content-center';
    const hospitalIcon = document.createElement('i');
    hospitalIcon.className = 'ti ti-building-hospital icon-base';
    if (isReferred) {
        hospitalLink.addEventListener('click', () => {
            populateReferralModal(entry);
            new bootstrap.Modal(document.getElementById('outsideReferralModal')).show();
        });
    } else {
        hospitalIcon.style.opacity = '0.5';
        hospitalLink.style.pointerEvents = 'none';
    }
    hospitalLink.appendChild(hospitalIcon);
    iconFlexContainer.appendChild(hospitalLink);
    detailsCell.appendChild(iconFlexContainer);
    row.appendChild(detailsCell);
    return row;
}
function detailsModal(entry) {
    const body = document.getElementById('detailsModalBody');
    body.innerHTML = `
        <p><strong>Employee ID:</strong> ${entry.employee_id}</p>
        <p><strong>Date:</strong> ${entry.registry_times?.reporting_date_time ?? 'N/A'}</p>
        <p><strong>Symptoms/Injury:</strong> ${entry.registry?.type_of_incident === 'medicalIllness'
            ? entry.symptom_names?.join(', ') ?? 'N/A'
            : entry.nature_of_injury_names?.join(', ') ?? 'N/A'
        }</p>
        <!-- Add more fields as needed -->
    `;
}
function fetchTestDataForEmployee(employeeId, registryId) {
    if (!employeeId || !registryId) {
        showToast('error', 'Missing employee ID or registry ID');
        return;
    }
    apiRequest({
        url: 'https://login-users.hygeiaes.com/ohc/getAllTests',
        method: 'GET',
        onSuccess: function (response) {
            if (response.result && Array.isArray(response.data)) {
                const employeeTests = response.data.filter(
                    test => test.employee_id && test.employee_id.toLowerCase() === employeeId.toLowerCase()
                );
                if (employeeTests.length > 0) {
                    showTestListModal({
                        testStructure: JSON.stringify(employeeTests[0].tests || {}),
                        employeeName: employeeTests[0].name || 'N/A',
                        employeeId: employeeTests[0].employee_id || 'N/A',
                        employeeAge: employeeTests[0].age || 'N/A',
                        testDate: employeeTests[0].reporting_date_time || 'N/A',
                        gender: employeeTests[0].gender || 'female',
                        healthPlanStatus: employeeTests[0].healthplan_status || 'N/A'
                    });
                } else {
                    showToast('info', 'No test data found for this employee');
                }
            } else {
                console.warn('Error: ', response);
                showToast('error', 'Failed to load test data');
            }
        },
        onError: function (error) {
            console.error('Error loading test data:', error);
            showToast('error', 'Failed to load tests: ' + error);
        }
    });
}
function buildRegistryRelationships(entries) {
    const relationships = new Map();
    entries.forEach(entry => {
        if (entry.registry?.follow_up_op_registry_id > 0) {
            const parentId = entry.registry.follow_up_op_registry_id.toString();
            const isOpen = entry.registry?.open_status === '1';
            if (relationships.has(parentId)) {
                const currentValue = relationships.get(parentId);
                relationships.set(parentId, currentValue || isOpen);
            } else {
                relationships.set(parentId, isOpen);
            }
        }
    });
    return relationships;
}
function createTestModal() {
    if (document.getElementById('testListModal')) return;
    const modalHtml = `
        <div class="modal fade" id="testListModal" tabindex="-1" aria-labelledby="testListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testListModalLabel">
    Test Details <span id="testDateLabel" class="text-primary fw-bold"></span>
</h5>                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">           
                <div id="modalTestList" style="margin-top: -28px;"></div>
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
        .test-result-table {
        width: 100%;
        margin-top: 5px;
        border-collapse: collapse;
        }
        .test-result-table th, .test-result-table td {
        padding: 6px 10px;
        border: 1px solid #dee2e6;
        font-size: 14px;
        }
        .test-result-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        }
        .test-group-title {
        font-weight: bold;
        font-size: 16px;
        margin-top: 15px;
        padding: 5px 0;
        border-bottom: 2px solid #dee2e6;
        color: #566a7f;
        }
        .test-subgroup-title {
        font-weight: 600;
        font-size: 15px;
        margin-top: 10px;
        padding: 3px 0;
        color: #697a8d;
        margin-left: 10px;
        }
        .test-subsubgroup-title {
        font-weight: 500;
        font-size: 14px;
        margin-top: 8px;
        padding: 2px 0;
        color: #697a8d;
        margin-left: 20px;
        }
        .test-item {
        margin-left: 10px;
        margin-top: 5px;
        }
        .subgroup-test-item {
        margin-left: 20px;
        margin-top: 5px;
        }
        .subsubgroup-test-item {
        margin-left: 30px;
        margin-top: 5px;
        }
        .normal-range {
        font-size: 12px;
        color: #697a8d;
        }
        .test-result-value {
        font-weight: 600;
        color: #566a7f;
        }
        .test-result-normal {
        color: #28a745;
        }
        .test-result-abnormal {
        color: #dc3545;
        }
        .test-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5px;
        }
        .health-plan-status {
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 500;
        font-size: 14px;
        }
        .status-pending {
        background-color: #fff4e5;
        color: #ff9800;
        }
        .status-completed {
        background-color: #e8f5e9;
        color: #4caf50;
        }
        .status-processing {
        background-color: #e3f2fd;
        color: #2196f3;
        }
        `;
    document.head.appendChild(styleElement);
}
async function populateIncidentTypeColorCodes() {
    try {
        const result = await apiRequest({
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getincidentTypeColorCodes',
            method: 'GET'
        });
        const selectElement = document.getElementById('getincidentTypeColorCodes');
        if (!selectElement) {
            console.error('Element with ID getincidentTypeColorCodes not found in the DOM.');
            return;
        }
        const data = result.message;
        if (result.result && typeof data === 'object') {
            selectElement.innerHTML = '';
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = 'All Incident Colors';
            placeholderOption.disabled = true;
            placeholderOption.selected = true;
            selectElement.appendChild(placeholderOption);
            const medicalOption = document.createElement('option');
            medicalOption.value = 'Medical';
            medicalOption.textContent = 'Medical';
            selectElement.appendChild(medicalOption);
            for (const [key, color] of Object.entries(data)) {
                if (key === 'Medical') continue;
                const option = document.createElement('option');
                option.value = key;
                option.textContent = key;
                option.setAttribute('data-color', color);
                selectElement.appendChild(option);
            }
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(selectElement).select2({
                    placeholder: 'All Incident Colors',
                    allowClear: true,
                    width: '100%',
                    templateResult: formatOption,
                    templateSelection: formatOption
                });
            } else {
                createCustomDropdown(selectElement, data);
            }
        } else {
            console.error('Incident type color codes not received as expected');
        }
    } catch (error) {
        console.error('Error fetching Incident colors:', error);
    }
}
function showTestListModal(dataset) {
    const modal = document.getElementById('testListModal');
    const testListElement = document.getElementById('modalTestList');
    testListElement.innerHTML = '';
    const testDateLabel = document.getElementById('testDateLabel');
    if (testDateLabel && dataset.testDate) {
        const dt = new Date(dataset.testDate);
        const formattedDate = `${dt.getDate().toString().padStart(2, '0')}-${(dt.getMonth() + 1).toString().padStart(2, '0')}-${dt.getFullYear()}`;
        testDateLabel.textContent = `– ${formattedDate}`;
    }
    let testStructure = {};
    try {
        testStructure = JSON.parse(dataset.testStructure);
    } catch (e) {
        console.error('Error parsing test structure:', e);
    }
    if (Object.keys(testStructure).length > 0) {
        renderHierarchicalTests(testStructure, testListElement, dataset.gender || 'female');
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
function renderHierarchicalTests(testStructure, container, gender) {
    Object.keys(testStructure).forEach(groupName => {
        const group = testStructure[groupName];
        const groupHeader = document.createElement('div');
        groupHeader.className = 'test-group-title';
        groupHeader.textContent = groupName;
        container.appendChild(groupHeader);
        if (typeof group === 'object' && group !== null) {
            Object.keys(group).forEach(subGroupName => {
                const subGroup = group[subGroupName];
                const subGroupHeader = document.createElement('div');
                subGroupHeader.className = 'test-subgroup-title';
                subGroupHeader.textContent = subGroupName;
                container.appendChild(subGroupHeader);
                const table = document.createElement('table');
                table.className = 'test-result-table';
                container.appendChild(table);
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                ['Test Name', 'Result', 'Unit', 'Reference Range'].forEach(headerText => {
                    const th = document.createElement('th');
                    th.textContent = headerText;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);
                const tbody = document.createElement('tbody');
                table.appendChild(tbody);
                if (Array.isArray(subGroup)) {
                    renderTestItemsAsTable(subGroup, tbody, gender);
                } else if (typeof subGroup === 'object' && subGroup !== null) {
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
                        renderTestItemsAsTable(directTests, tbody, gender);
                    }
                    Object.keys(subSubGroups).forEach(subSubGroupName => {
                        const item = subSubGroups[subSubGroupName];
                        const subgroupHeaderRow = document.createElement('tr');
                        const subgroupHeaderCell = document.createElement('td');
                        subgroupHeaderCell.colSpan = 4;
                        subgroupHeaderCell.className = 'test-subsubgroup-title';
                        subgroupHeaderCell.textContent = subSubGroupName;
                        subgroupHeaderRow.appendChild(subgroupHeaderCell);
                        tbody.appendChild(subgroupHeaderRow);
                        if (Array.isArray(item)) {
                            renderTestItemsAsTable(item, tbody, gender);
                        } else if (typeof item === 'object' && item !== null && item.name) {
                            renderSingleTestAsTableRow(item, tbody, gender);
                        }
                    });
                }
            });
        } else if (typeof group === 'string') {
            const testElement = document.createElement('div');
            testElement.className = 'test-item';
            testElement.textContent = group;
            container.appendChild(testElement);
        }
    });
}
function renderTestItemsAsTable(items, tbody, gender) {
    if (!Array.isArray(items)) {
        console.warn('Expected items to be an array:', items);
        return;
    }
    items.forEach(testItem => {
        if (typeof testItem === 'object' && testItem !== null && testItem.name) {
            renderSingleTestAsTableRow(testItem, tbody, gender);
        } else if (typeof testItem === 'string') {
            const row = document.createElement('tr');
            const nameCell = document.createElement('td');
            nameCell.textContent = testItem;
            row.appendChild(nameCell);
            ['', '', ''].forEach(() => {
                const cell = document.createElement('td');
                cell.textContent = '-';
                row.appendChild(cell);
            });
            tbody.appendChild(row);
        }
    });
}
function renderSingleTestAsTableRow(test, tbody, gender) {
    const row = document.createElement('tr');
    const nameCell = document.createElement('td');
    nameCell.textContent = test.name || 'Unknown Test';
    row.appendChild(nameCell);
    const resultCell = document.createElement('td');
    const resultValue = test.test_result !== null ? test.test_result : '-';
    const resultSpan = document.createElement('span');
    resultSpan.className = 'test-result-value';
    resultSpan.textContent = resultValue;
    resultCell.appendChild(resultSpan);
    row.appendChild(resultCell);
    const unitCell = document.createElement('td');
    unitCell.textContent = test.unit || '-';
    row.appendChild(unitCell);
    const rangeCell = document.createElement('td');
    const rangeText = getFormattedReferenceRange(test, gender);
    rangeCell.className = 'normal-range';
    rangeCell.textContent = rangeText;
    row.appendChild(rangeCell);
    tbody.appendChild(row);
    if (test.test_result !== null && test.test_result !== '') {
        const isNormal = isResultInRange(test.test_result, test, gender);
        resultSpan.classList.add(isNormal ? 'test-result-normal' : 'test-result-abnormal');
    }
}
function getFormattedReferenceRange(test, gender) {
    let range = '';
    try {
        const rangeKey = gender.toLowerCase() === 'male' ? 'm_min_max' : 'f_min_max';
        if (test[rangeKey]) {
            const rangeObj = JSON.parse(test[rangeKey]);
            if (rangeObj.min && rangeObj.max) {
                range = `${rangeObj.min} - ${rangeObj.max}`;
            }
        }
    } catch (e) {
        console.error('Error parsing reference range:', e);
    }
    return range || 'Not available';
}
function isResultInRange(resultValue, test, gender) {
    try {
        const numericResult = parseFloat(resultValue);
        if (isNaN(numericResult)) return true;
        const rangeKey = gender.toLowerCase() === 'male' ? 'm_min_max' : 'f_min_max';
        if (test[rangeKey]) {
            const rangeObj = JSON.parse(test[rangeKey]);
            const min = parseFloat(rangeObj.min);
            const max = parseFloat(rangeObj.max);
            if (!isNaN(min) && !isNaN(max)) {
                return numericResult >= min && numericResult <= max;
            }
        }
    } catch (e) {
        console.error('Error evaluating result range:', e);
    }
    return true;
}
function createFollowUpModal() {
    if (document.getElementById('followUpModal')) return;
    const modalDiv = document.createElement('div');
    modalDiv.className = 'modal fade';
    modalDiv.id = 'followUpModal';
    modalDiv.setAttribute('tabindex', '-1');
    modalDiv.setAttribute('aria-hidden', 'true');
    const modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog';
    modalDialog.setAttribute('role', 'document');
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    const modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header';
    const modalTitle = document.createElement('h5');
    modalTitle.className = 'modal-title';
    modalTitle.textContent = 'Registry Open';
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn-close';
    closeButton.setAttribute('data-bs-dismiss', 'modal');
    closeButton.setAttribute('aria-label', 'Close');
    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(closeButton);
    const modalBody = document.createElement('div');
    modalBody.className = 'modal-body';
    const alertDiv = document.createElement('div');
    alertDiv.className = 'mb-3';
    const alertInner = document.createElement('div');
    alertInner.className = 'alert alert-warning d-flex align-items-center';
    alertInner.setAttribute('role', 'alert');
    const alertIcon = document.createElement('i');
    alertIcon.className = 'ti ti-alert-circle me-2';
    const alertTextDiv = document.createElement('div');
    alertTextDiv.textContent = 'You need to save and close this registry before adding a follow-up case.';
    alertInner.appendChild(alertIcon);
    alertInner.appendChild(alertTextDiv);
    const employeeName = document.createElement('p');
    employeeName.id = 'followUpEmployeeName';
    employeeName.className = 'mb-1';
    alertDiv.appendChild(alertInner);
    alertDiv.appendChild(employeeName);
    const instructionDiv = document.createElement('div');
    instructionDiv.className = 'mb-3';
    const instructionText = document.createElement('p');
    instructionText.textContent =
        'Please save and close the current registry first, then you can proceed with adding a follow-up case.';
    instructionDiv.appendChild(instructionText);
    modalBody.appendChild(alertDiv);
    modalBody.appendChild(instructionDiv);
    const modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer';
    const closeModalButton = document.createElement('button');
    closeModalButton.type = 'button';
    closeModalButton.className = 'btn btn-primary';
    closeModalButton.setAttribute('data-bs-dismiss', 'modal');
    closeModalButton.textContent = 'Close';
    modalFooter.appendChild(closeModalButton);
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);
    modalDialog.appendChild(modalContent);
    modalDiv.appendChild(modalDialog);
    document.body.appendChild(modalDiv);
}
function createOpenChildrenModal() {
    if (document.getElementById('openChildrenModal')) return;
    const modalDiv = document.createElement('div');
    modalDiv.className = 'modal fade';
    modalDiv.id = 'openChildrenModal';
    modalDiv.setAttribute('tabindex', '-1');
    modalDiv.setAttribute('aria-hidden', 'true');
    const modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog';
    modalDialog.setAttribute('role', 'document');
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    const modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header';
    const modalTitle = document.createElement('h5');
    modalTitle.className = 'modal-title';
    modalTitle.textContent = 'Follow-up Case Open';
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn-close';
    closeButton.setAttribute('data-bs-dismiss', 'modal');
    closeButton.setAttribute('aria-label', 'Close');
    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(closeButton);
    const modalBody = document.createElement('div');
    modalBody.className = 'modal-body';
    const alertDiv = document.createElement('div');
    alertDiv.className = 'mb-3';
    const alertInner = document.createElement('div');
    alertInner.className = 'alert alert-warning d-flex align-items-center';
    alertInner.setAttribute('role', 'alert');
    const alertIcon = document.createElement('i');
    alertIcon.className = 'ti ti-alert-circle me-2';
    const alertTextDiv = document.createElement('div');
    alertTextDiv.textContent = 'This registry has one or more open follow-up records that need to be closed first.';
    alertInner.appendChild(alertIcon);
    alertInner.appendChild(alertTextDiv);
    const employeeName = document.createElement('p');
    employeeName.id = 'openChildrenEmployeeName';
    employeeName.className = 'mb-1';
    alertDiv.appendChild(alertInner);
    alertDiv.appendChild(employeeName);
    const instructionDiv = document.createElement('div');
    instructionDiv.className = 'mb-3';
    const instructionText = document.createElement('p');
    instructionText.textContent =
        'Please close all follow-up records associated with this registry before adding new follow-ups.';
    instructionDiv.appendChild(instructionText);
    modalBody.appendChild(alertDiv);
    modalBody.appendChild(instructionDiv);
    const modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer';
    const closeModalButton = document.createElement('button');
    closeModalButton.type = 'button';
    closeModalButton.className = 'btn btn-primary';
    closeModalButton.setAttribute('data-bs-dismiss', 'modal');
    closeModalButton.textContent = 'Close';
    modalFooter.appendChild(closeModalButton);
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);
    modalDialog.appendChild(modalContent);
    modalDiv.appendChild(modalDialog);
    document.body.appendChild(modalDiv);
}
function showClosedRegistryModal(entry) {
    const empId = entry.employee_id.toLowerCase() || '0';
    const opId = entry.registry?.op_registry_id || '0';
    const viewUrl = `/ohc/health-registry/view-registry/view-outpatient/${empId}/op/${opId}`;
    window.location.href = viewUrl;
}
function createClosedRegistryModal() {
    const modalContainer = document.createElement('div');
    modalContainer.className = 'modal fade';
    modalContainer.id = 'closedRegistryModal';
    modalContainer.setAttribute('tabindex', '-1');
    modalContainer.setAttribute('aria-hidden', 'true');
    const modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog';
    modalDialog.setAttribute('role', 'document');
    modalContainer.appendChild(modalDialog);
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    modalDialog.appendChild(modalContent);
    const modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header';
    modalContent.appendChild(modalHeader);
    const modalTitle = document.createElement('h5');
    modalTitle.className = 'modal-title';
    modalTitle.textContent = 'Registry Closed';
    modalHeader.appendChild(modalTitle);
    const closeButton = document.createElement('button');
    closeButton.className = 'btn-close';
    closeButton.setAttribute('data-bs-dismiss', 'modal');
    closeButton.setAttribute('aria-label', 'Close');
    modalHeader.appendChild(closeButton);
    const modalBody = document.createElement('div');
    modalBody.className = 'modal-body';
    modalContent.appendChild(modalBody);
    const firstSection = document.createElement('div');
    firstSection.className = 'mb-3';
    modalBody.appendChild(firstSection);
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger d-flex align-items-center';
    alert.setAttribute('role', 'alert');
    firstSection.appendChild(alert);
    const alertIcon = document.createElement('i');
    alertIcon.className = 'ti ti-alert-circle me-2';
    alert.appendChild(alertIcon);
    const alertText = document.createElement('div');
    alertText.textContent = 'This registry has already been closed and cannot be edited.';
    alert.appendChild(alertText);
    const employeeName = document.createElement('p');
    employeeName.id = 'closedRegistryEmployeeName';
    employeeName.className = 'mb-1';
    firstSection.appendChild(employeeName);
    const contactSection = document.createElement('div');
    contactSection.className = 'mb-3';
    modalBody.appendChild(contactSection);
    const contactText = document.createElement('p');
    contactText.textContent = 'If you need to make changes to this registry, please contact your administrator.';
    contactSection.appendChild(contactText);
    const modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer';
    modalContent.appendChild(modalFooter);
    const okButton = document.createElement('button');
    okButton.type = 'button';
    okButton.className = 'btn btn-primary';
    okButton.setAttribute('data-bs-dismiss', 'modal');
    okButton.textContent = 'OK';
    modalFooter.appendChild(okButton);
    const viewButton = document.createElement('a');
    viewButton.id = 'viewRegistryBtn';
    viewButton.className = 'btn btn-outline-secondary';
    viewButton.textContent = 'View Only';
    modalFooter.appendChild(viewButton);
    document.body.appendChild(modalContainer);
}
function populateReferralModal(entry) {
    console.log(entry);
    document.getElementById('outsideReferralModalLabel').textContent = 'Outside Referral Details';
    document.getElementById('employeeName').textContent = entry.employee_name || 'N/A';
    const referral = entry.outside_referral || {};
    document.getElementById('hospitalName').textContent = referral.hospital_name || 'N/A';
    document.getElementById('accompaniedBy').textContent = referral.accompanied_by || 'N/A';
    document.getElementById('vehicleType').textContent = referral.vehicle_type || 'N/A';
    const ambulanceSection = document.getElementById('ambulanceDetailsSection');
    if (referral.vehicle_type === 'ambulance') {
        ambulanceSection.classList.remove('d-none');
        document.getElementById('ambulanceDriver').textContent = referral.ambulance_driver || 'N/A';
        document.getElementById('ambulanceNumber').textContent = referral.ambulance_number || 'N/A';
        let outTime = 'N/A';
        let inTime = 'N/A';
        if (referral.ambulance_outtime) {
            const outDateTime = new Date(referral.ambulance_outtime);
            outTime = outDateTime.toLocaleString();
        }
        if (referral.ambulance_intime) {
            const inDateTime = new Date(referral.ambulance_intime);
            inTime = inDateTime.toLocaleString();
        }
        document.getElementById('ambulanceOutTime').textContent = outTime;
        document.getElementById('ambulanceInTime').textContent = inTime;
        document.getElementById('meterOut').textContent = referral.meter_out || 'N/A';
        document.getElementById('meterIn').textContent = referral.meter_in || 'N/A';
    } else {
        ambulanceSection.classList.add('d-none');
    }
    document.getElementById('employeeESI').textContent = referral.employee_esi === 1 ? 'Yes' : 'No';
    document.getElementById('mrNumber').textContent = referral.mr_number || 'N/A';
}
function populatePrescriptionModal(entry, formattedDate) {
    const dateLabel = document.getElementById('prescriptionDateLabel');
    if (dateLabel && formattedDate) {
        dateLabel.textContent = `– ${formattedDate}`;
    }
    if (
        !entry.prescriptionsForRegistry ||
        !entry.prescriptionsForRegistry.prescription ||
        !entry.prescriptionsForRegistry.prescription_details
    ) {
        console.error('No prescription data available');
        return;
    }
    const prescriptionData = entry.prescriptionsForRegistry;
    const prescription = prescriptionData.prescription;
    const prescriptionDetails = prescriptionData.prescription_details;
    const doctorHeader = document.querySelector('.doctor-header span:first-child');
    if (doctorHeader) {
        doctorHeader.textContent = prescription.doctor_name || 'Dr. Unknown';
    }
    const prescriptionIdElement = document.querySelector('.prescription-id');
    if (prescriptionIdElement) {
        prescriptionIdElement.textContent = prescription.master_doctor_id + ' ' + prescription.prescription_id || 'N/A';
    }
    const tableBody = document.querySelector('#prescriptionModal table tbody');
    if (tableBody) {
        tableBody.innerHTML = '';
    }
    if (prescriptionDetails && tableBody) {
        Object.values(prescriptionDetails).forEach(detail => {
            if (!detail) return;
            const row = document.createElement('tr');
            const drugNameCell = document.createElement('td');
            drugNameCell.className = 'drug-name';
            let drugNameText = detail.drug_name || 'N/A';
            if (detail.drug_strength) {
                drugNameText += ` - ${detail.drug_strength}`;
            }
            if (detail.drug_type) {
                const drugTypes = {
                    1: 'Tablet',
                    2: 'Capsule',
                    3: 'Syrup',
                    4: 'Drops',
                    5: 'Cream',
                    6: 'Gel',
                    7: 'Lotion',
                    8: 'Ointment',
                    9: 'Foam',
                    10: 'Spray'
                };
                drugNameText += ` (${drugTypes[detail.drug_type] || 'N/A'})`;
            }
            drugNameCell.textContent = drugNameText;
            if (!detail.drug_template_id || detail.drug_template_id === 0) {
                const externalLinkIcon = document.createElement('i');
                externalLinkIcon.className = 'fas fa-external-link-alt';
                externalLinkIcon.style.marginLeft = '5px';
                drugNameCell.appendChild(externalLinkIcon);
            }
            row.appendChild(drugNameCell);
            const daysCell = document.createElement('td');
            daysCell.textContent = detail.prescribed_days || 'N/A';
            row.appendChild(daysCell);
            const morningCell = document.createElement('td');
            morningCell.textContent = detail.morning || '0';
            row.appendChild(morningCell);
            const afternoonCell = document.createElement('td');
            afternoonCell.textContent = detail.afternoon || '0';
            row.appendChild(afternoonCell);
            const eveningCell = document.createElement('td');
            eveningCell.textContent = detail.evening || '0';
            row.appendChild(eveningCell);
            const nightCell = document.createElement('td');
            nightCell.textContent = detail.night || '0';
            row.appendChild(nightCell);
            const intakeCell = document.createElement('td');
            const intakeConditions = {
                1: 'Before Food',
                2: 'After Food',
                3: 'With Food',
                4: 'Empty Stomach',
                5: 'As Needed'
            };
            intakeCell.textContent = intakeConditions[detail.intake_condition] || 'N/A';
            row.appendChild(intakeCell);
            const remarksCell = document.createElement('td');
            remarksCell.textContent = detail.remarks || 'N/A';
            row.appendChild(remarksCell);
            tableBody.appendChild(row);
        });
    }
}

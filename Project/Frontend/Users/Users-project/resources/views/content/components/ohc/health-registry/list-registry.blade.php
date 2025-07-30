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
<style>
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

    /* Remove all borders and create clean row separation */
    #health-registry-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    #health-registry-table th,
    #health-registry-table td {
        text-align: left;
        vertical-align: top;
        padding: 12px 16px;
        border: none;
        /* Remove all borders */
        border-bottom: 1px solid #e9ecef;
        /* Only bottom border for row separation */
    }

    /* Remove border from last row */
    #health-registry-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Header styling */
    #health-registry-table thead th {
        border-bottom: 2px solid #dee2e6;
        /* Slightly thicker line under headers */
        font-weight: 600;
        background-color: transparent;
    }

    /* Specific column widths */
    #health-registry-table th:nth-child(1),
    #health-registry-table td:nth-child(1) {
        width: 12%;
        /* DATE column */
    }

    #health-registry-table th:nth-child(2),
    #health-registry-table td:nth-child(2) {
        width: 25%;
        /* NAME column */
    }

    #health-registry-table th:nth-child(3),
    #health-registry-table td:nth-child(3) {
        width: 15%;
        /* DEPARTMENT column */
    }

    #health-registry-table th:nth-child(4),
    #health-registry-table td:nth-child(4) {
        width: 30%;
        /* NATURE OF INJURY column */
    }

    #health-registry-table th:nth-child(5),
    #health-registry-table td:nth-child(5) {
        width: 18%;
        /* DETAILS column */
    }

    /* Optional: Add hover effect for better UX */
    #health-registry-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Remove any Bootstrap table classes that add borders */
    #health-registry-table.table-bordered {
        border: none;
    }

    #health-registry-table.table-bordered th,
    #health-registry-table.table-bordered td {
        border: none;
        border-bottom: 1px solid #e9ecef;
    }#attachmentSection {
  display: flex;
  gap: 1rem; /* space between cards */
  flex-wrap: nowrap; /* prevent wrapping, or change to wrap if you want */
}


</style>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr(".flatpickr-date", {
            dateFormat: "d/m/Y",
            allowInput: true
        });
        fetchHealthRegistryData();
        populateDoctorDropdown();
        populateIncidentTypeColorCodes();
        createTestModal();
        populateDepartment();
        populateMedicalSystem();
        populateNatureOfInjury();
        const advancedBtn = document.getElementById('advancedFiltersBtn');
        const additionalFilters = document.getElementById('additionalFilters');
        let isVisible = false;
        advancedBtn.addEventListener('click', () => {
            if (isVisible) {
                additionalFilters.style.maxHeight = '0';
                additionalFilters.style.opacity = '0';
                additionalFilters.style.marginTop = '0';
                setTimeout(() => {
                    additionalFilters.style.display = 'none';
                }, 400);
            } else {
                additionalFilters.style.display = 'flex';
                additionalFilters.style.marginTop = '1rem';
                requestAnimationFrame(() => {
                    additionalFilters.style.maxHeight = '500px';
                    additionalFilters.style.opacity = '1';
                });
            }
            advancedBtn.textContent = isVisible ? 'Advanced Filters' : 'Hide Advanced Filters';
            isVisible = !isVisible;
        });
        document.getElementById('applyFiltersBtn').addEventListener('click', function () {
            const nameOrTest = document.getElementById('searchInput').value.trim();
            const doctorId = document.getElementById('doctorDropdown').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const injuryColor = document.getElementById('getincidentTypeColorCodes').value;
            const departmentName = document.getElementById('departmentDropdown').options[document.getElementById('departmentDropdown').selectedIndex].text == "All Departments" ? "" : document.getElementById('departmentDropdown').options[document.getElementById('departmentDropdown').selectedIndex].text;
            const injuryTypeId = document.getElementById('injuryTypeDropdown').value;
            const medicalSystemId = document.getElementById('medicalSystemDropdown').value;
            const gender = getSelectedGender();
            fetchHealthRegistryData({
                nameOrTest,
                doctorId,
                fromDate,
                toDate,
                injuryColor,
                departmentName,
                injuryTypeId,
                medicalSystemId,
                gender
            });
        });
    });
    async function populateDepartment() {
        try {
            const response = await fetch('https://login-users.hygeiaes.com/corporate/getDepartments');
            const result = await response.json();
            const departmentDropdown = document.getElementById('departmentDropdown');
            while (departmentDropdown.options.length > 1) {
                departmentDropdown.remove(1);
            }
            if (result.result && Array.isArray(result.data)) {
                result.data.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.hl1_id;
                    option.textContent = department.hl1_name;
                    departmentDropdown.appendChild(option);
                });
                $(departmentDropdown).select2({
                    placeholder: 'All Departments',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                console.error('Department data not received as expected');
            }
        } catch (error) {
            console.error('Error fetching Department:', error);
        }
    }
    async function populateMedicalSystem() {
        try {
            const response = await fetch('https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem');
            const result = await response.json();
            const medicalSystemDropdown = document.getElementById('medicalSystemDropdown');
            while (medicalSystemDropdown.options.length > 1) {
                medicalSystemDropdown.remove(1);
            }
            if (result.result && Array.isArray(result.message)) {
                result.message.forEach(medicalSystem => {
                    const option = document.createElement('option');
                    option.value = medicalSystem.op_component_id;
                    option.textContent = medicalSystem.op_component_name;
                    medicalSystemDropdown.appendChild(option);
                });
                $(medicalSystemDropdown).select2({
                    placeholder: 'All Systems',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                console.error('Medical System data not received as expected');
            }
        } catch (error) {
            console.error('Error fetching Medical System:', error);
        }
    }
    async function populateNatureOfInjury() {
        try {
            const response = await fetch('https://login-users.hygeiaes.com/ohc/health-registry/getAllNatureOfInjury');
            const result = await response.json();
            const injuryTypeDropdown = document.getElementById('injuryTypeDropdown');
            while (injuryTypeDropdown.options.length > 1) {
                injuryTypeDropdown.remove(1);
            }
            if (result.result && Array.isArray(result.message)) {
                result.message.forEach(injuryType => {
                    const option = document.createElement('option');
                    option.value = injuryType.op_component_id;
                    option.textContent = injuryType.op_component_name;
                    injuryTypeDropdown.appendChild(option);
                });
                $(injuryTypeDropdown).select2({
                    placeholder: 'All Injury Types',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                console.error('Nature of injury data not received as expected');
            }
        } catch (error) {
            console.error('Error fetching Nature of injury:', error);
        }
    }
    async function populateDoctorDropdown() {
        try {
            const response = await fetch('https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getDoctors');
            const result = await response.json();
            const doctorDropdown = document.getElementById('doctorDropdown');
            while (doctorDropdown.options.length > 1) {
                doctorDropdown.remove(1);
            }
            if (result.result && Array.isArray(result.data)) {
                result.data.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.doctor_id;
                    option.textContent = doctor.doctor_name;
                    doctorDropdown.appendChild(option);
                });
                $(doctorDropdown).select2({
                    placeholder: 'All Doctors',
                    allowClear: true,
                    width: '100%'
                });
            } else {
                console.error('Doctor data not received as expected');
            }
        } catch (error) {
            console.error('Error fetching doctors:', error);
        }
    }
    async function populateIncidentTypeColorCodes() {
        try {
            const response = await fetch('https://login-users.hygeiaes.com/ohc/health-registry/getincidentTypeColorCodes');
            const result = await response.json();
            const selectElement = document.getElementById('getincidentTypeColorCodes');
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
    function getSelectedGender() {
        const gender = document.querySelector('input[name="gender"]:checked');
        if (gender) {
            return gender.value;
        } else {
            return null;
        }
    }
    function fetchHealthRegistryData(filters = null) {
        apiRequest({
            url: "/ohc/health-registry/getAllHealthRegistry",
            method: "GET",
            onSuccess: (data) => {
                if (data.result && data.data && Array.isArray(data.data)) {
                    populateTable(data.data, filters);
                } else {
                    showErrorInTable('Invalid data format received');
                }
            },
            onError: (error) => {
                console.error('Error fetching health registry data:', error);
                showErrorInTable('Error loading data: ' + error.message);
            }
        });
    }
    function formatOption(option) {
        if (!option.id || option.id === 'Medical') {
            return option.text;
        }
        const color = $(option.element).data('color') || '#000000';
        const $option = $(
            `<span>
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:${color};margin-right:5px;"></span>
            ${option.text.replace('● ', '')}
        </span>`
        );
        return $option;
    }
    function createCustomDropdown(originalSelect, colorData) {
        originalSelect.style.opacity = '0';
        originalSelect.style.position = 'absolute';
        originalSelect.style.height = '100%';
        originalSelect.style.width = '100%';
        originalSelect.style.left = '0';
        originalSelect.style.top = '0';
        originalSelect.style.zIndex = '1';
        const wrapper = document.createElement('div');
        wrapper.className = 'select-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.display = 'inline-block';
        wrapper.style.minWidth = '150px';
        const customSelect = document.createElement('div');
        customSelect.className = 'custom-select-display';
        customSelect.style.border = '1px solid #ccc';
        customSelect.style.padding = '5px 10px';
        customSelect.style.borderRadius = '4px';
        customSelect.style.backgroundColor = '#fff';
        customSelect.style.cursor = 'pointer';
        customSelect.style.display = 'flex';
        customSelect.style.alignItems = 'center';
        const arrow = document.createElement('span');
        arrow.innerHTML = '▼';
        arrow.style.marginLeft = 'auto';
        arrow.style.fontSize = '10px';
        customSelect.appendChild(arrow);
        function updateCustomSelectDisplay() {
            while (customSelect.firstChild) {
                customSelect.removeChild(customSelect.firstChild);
            }
            const selectedOption = originalSelect.options[originalSelect.selectedIndex];
            const selectedValue = selectedOption.value;
            const selectedText = selectedOption.textContent.replace('● ', '');
            if (selectedValue !== 'Medical' && colorData[selectedValue]) {
                const colorCircle = document.createElement('span');
                colorCircle.style.display = 'inline-block';
                colorCircle.style.width = '10px';
                colorCircle.style.height = '10px';
                colorCircle.style.borderRadius = '50%';
                colorCircle.style.backgroundColor = colorData[selectedValue];
                colorCircle.style.marginRight = '8px';
                customSelect.appendChild(colorCircle);
            }
            customSelect.appendChild(document.createTextNode(selectedText));
            customSelect.appendChild(arrow);
        }
        updateCustomSelectDisplay();
        originalSelect.addEventListener('change', updateCustomSelectDisplay);
        originalSelect.parentNode.insertBefore(wrapper, originalSelect);
        wrapper.appendChild(customSelect);
        wrapper.appendChild(originalSelect);
        return wrapper;
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
                passesDepartmentFilter = entry.department &&
                    entry.department.toLowerCase() === filters.departmentName.toLowerCase();
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
                passesGenderFilter = entry.employee_gender &&
                    entry.employee_gender.toLowerCase() === filters.gender.toLowerCase();
            }
            if ((employeeName.includes(filters.nameOrTest.toLowerCase()) || employeeId.includes(filters.nameOrTest.toLowerCase())) &&
                passesInjuryColorFilter &&
                passesDepartmentFilter &&
                passesInjuryTypeFilter &&
                passesMedicalSystemFilter &&
                passesGenderFilter &&
                (doctorId === filters.doctorId || filters.doctorId === '') &&
                (!fromDate || new Date(entry.registry.day_of_registry) >= fromDate) &&
                (!toDate || new Date(entry.registry.day_of_registry) <= toDate)) {
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
        const createCell = () => {
            const cell = document.createElement('td');
            cell.classList.add('text-start');
            cell.style.verticalAlign = 'top';
            cell.style.padding = '8px 12px';
            return cell;
        };
        const dateCell = createCell();
        if (entry.registry_times && entry.registry_times.reporting_date_time) {
            const reportingDate = new Date(entry.registry_times.reporting_date_time);
            const formattedDate = `${reportingDate.getDate().toString().padStart(2, '0')}-${(reportingDate.getMonth() + 1).toString().padStart(2, '0')}-${reportingDate.getFullYear().toString().substring(2)}`;
            dateCell.textContent = formattedDate;
        } else {
            dateCell.textContent = 'N/A';
        }
        row.appendChild(dateCell);
        const employeeCell = createCell();
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
        employeeCell.appendChild(iconContainer);
        const nameSpan = document.createElement('span');
        nameSpan.className = 'fw-medium';
        nameSpan.textContent = entry.employee_name + ` (${entry.age})` || 'N/A';
        employeeCell.appendChild(nameSpan);
        employeeCell.appendChild(document.createTextNode(' - ' + (entry.employee_id.toLowerCase() || 'N/A')));
        row.appendChild(employeeCell);
        const deptCell = createCell();
        deptCell.textContent = entry.department || 'N/A';
        row.appendChild(deptCell);
        const symptomsCell = createCell();
        let symptomsOrInjury = 'N/A';
        if (entry.registry) {
            if (entry.registry.type_of_incident === 'medicalIllness') {
                if (entry.symptom_names && entry.symptom_names.length > 0) {
                    symptomsOrInjury = entry.symptom_names.join(', ');
                }
            } else {
                if (entry.nature_of_injury_names && entry.nature_of_injury_names.length > 0) {
                    symptomsOrInjury = entry.nature_of_injury_names.join(', ');
                }
            }
        }
        symptomsCell.textContent = symptomsOrInjury;
        row.appendChild(symptomsCell);
        const detailsCell = createCell();
        const iconFlexContainer = document.createElement('div');
        iconFlexContainer.className = 'd-flex align-items-center';
        detailsCell.appendChild(iconFlexContainer);
        const empId = entry.employee_id.toLowerCase() || '0';
        const opId = entry.registry?.op_registry_id || '0';
        const isFollowUp = entry.registry?.follow_up_op_registry_id > 0;
        let badgeClass, badgeText;
        if (entry.registry?.open_status === '1') {
            badgeClass = 'bg-label-danger';
        } else {
            badgeClass = 'bg-label-success';
        }
        badgeText = isFollowUp ? 'F' : 'N';
        const badgeLink = document.createElement('a');
        badgeLink.style.color = 'inherit';
        badgeLink.style.textDecoration = 'none';
        badgeLink.className = 'me-2 d-flex align-items-center justify-content-center';
        const badge = document.createElement('span');
        badge.className = `badge ${badgeClass}`;
        badge.textContent = badgeText;
        if (badgeText === 'N' && registryRelationships &&
            registryRelationships.has(entry.registry?.op_registry_id?.toString()) &&
            registryRelationships.get(entry.registry?.op_registry_id?.toString()) === true) {
            badge.style.fontStyle = 'italic';
        }
        badgeLink.appendChild(badge);
        if (badgeText === 'N') {
            const hasOpenChildren = registryRelationships &&
                registryRelationships.has(entry.registry?.op_registry_id?.toString()) &&
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
        const isOpen = entry.registry?.open_status === '1';
        const mainIconClass = isOpen ? 'ti ti-edit' : 'ti ti-eye';
        const mainLink = document.createElement('a');
        if (isOpen) {
            mainLink.href = `/ohc/health-registry/edit-registry/edit-outpatient/${empId}/op/${opId}`;
        } else {
            mainLink.href = 'javascript:void(0);';
            mainLink.addEventListener('click', () => {
                showClosedRegistryModal(entry);
            });
        }
        mainLink.style.color = 'inherit';
        mainLink.style.textDecoration = 'none';
        mainLink.className = 'me-2 d-flex align-items-center justify-content-center';
        const mainIcon = document.createElement('i');
        mainIcon.className = `${mainIconClass} icon-base`;
        mainLink.appendChild(mainIcon);
        iconFlexContainer.appendChild(mainLink);
        const hasRxPrescriptions = entry.prescriptionsForRegistry &&
            entry.prescriptionsForRegistry.prescription &&
            entry.prescriptionsForRegistry.prescription_details;
        const rxLink = document.createElement('a');
        rxLink.href = 'javascript:void(0);';
        rxLink.style.color = 'inherit';
        rxLink.style.textDecoration = 'none';
        rxLink.className = 'me-2 d-flex align-items-center justify-content-center';
        const rxIcon = document.createElement('i');
        rxIcon.className = 'ti ti-prescription icon-base';
        if (hasRxPrescriptions) {
            rxIcon.style.cursor = 'pointer';
            rxLink.addEventListener('click', () => {
                populatePrescriptionModal(entry);
                const modal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
                modal.show();
            });
        } else {
            rxIcon.style.opacity = '0.5';
            rxLink.style.pointerEvents = 'none';
        }
        rxLink.appendChild(rxIcon);
        iconFlexContainer.appendChild(rxLink);
        const hasPrescribedTests = entry.prescribed_tests && entry.prescribed_tests.length > 0;
        const testLink = document.createElement('a');
        testLink.href = 'javascript:void(0);';
        testLink.style.color = 'inherit';
        testLink.style.textDecoration = 'none';
        testLink.className = 'me-2 d-flex align-items-center justify-content-center';
        const testIcon = document.createElement('i');
        testIcon.className = 'ti ti-microscope icon-base';
        if (!hasPrescribedTests) {
            testIcon.style.opacity = '0.5';
        } else {
            testIcon.style.cursor = 'pointer';
            testLink.addEventListener('click', () => {
                fetchTestDataForEmployee(entry.employee_id, entry.registry?.op_registry_id);
            });
        }
        testLink.appendChild(testIcon);
        iconFlexContainer.appendChild(testLink);
        const hasOutsideReferral = entry.registry?.referral === 'OutsideReferral';
        const hospitalLink = document.createElement('a');
        hospitalLink.href = 'javascript:void(0);';
        hospitalLink.style.color = 'inherit';
        hospitalLink.style.textDecoration = 'none';
        hospitalLink.className = 'd-flex align-items-center justify-content-center';
        const hospitalIcon = document.createElement('i');
        hospitalIcon.className = 'ti ti-building-hospital icon-base';
        if (!hasOutsideReferral) {
            hospitalIcon.style.opacity = '0.5';
        } else {
            hospitalIcon.style.cursor = 'pointer';
            hospitalLink.addEventListener('click', () => {
                const outsideReferralModal = new bootstrap.Modal(document.getElementById('outsideReferralModal'));
                populateReferralModal(entry);
                outsideReferralModal.show();
            });
        }
        hospitalLink.appendChild(hospitalIcon);
        iconFlexContainer.appendChild(hospitalLink);
        row.appendChild(detailsCell);
        return row;
    }
    function fetchTestDataForEmployee(employeeId, registryId) {
        if (!employeeId || !registryId) {
            showToast('error', "Missing employee ID or registry ID");
            return;
        }
        apiRequest({
            url: 'https://login-users.hygeiaes.com/ohc/getAllTests',
            method: 'GET',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    const employeeTests = response.data.filter(test =>
                        test.employee_id && test.employee_id.toLowerCase() === employeeId.toLowerCase()
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
                        showToast('info', "No test data found for this employee");
                    }
                } else {
                    console.warn('Error: ', response);
                    showToast('error', "Failed to load test data");
                }
            },
            onError: function (error) {
                console.error('Error loading test data:', error);
                showToast('error', "Failed to load tests: " + error);
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
                <h5 class="modal-title" id="testListModalLabel">Full Test List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-header-info">
                <div class="employee-info" id="modalEmployeeName"></div>
                <div class="test-info">
                    <div class="date-info" id="modalTestDate"></div> &nbsp;&nbsp;
                    <div class="health-plan-status" id="modalHealthPlanStatus"></div>
                </div>
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
    function showTestListModal(dataset) {
        const modal = document.getElementById('testListModal');
        const employeeNameElement = document.getElementById('modalEmployeeName');
        const testDateElement = document.getElementById('modalTestDate');
        const healthPlanStatusElement = document.getElementById('modalHealthPlanStatus');
        const testListElement = document.getElementById('modalTestList');
        if (!modal || !employeeNameElement || !testDateElement || !healthPlanStatusElement || !testListElement) {
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
        if (dataset.healthPlanStatus) {
            healthPlanStatusElement.textContent = dataset.healthPlanStatus;
            healthPlanStatusElement.className = 'health-plan-status';
            if (dataset.healthPlanStatus.toLowerCase() === 'pending') {
                healthPlanStatusElement.classList.add('status-pending');
            } else if (dataset.healthPlanStatus.toLowerCase() === 'completed') {
                healthPlanStatusElement.classList.add('status-completed');
            } else if (dataset.healthPlanStatus.toLowerCase() === 'processing') {
                healthPlanStatusElement.classList.add('status-processing');
            }
        } else {
            healthPlanStatusElement.textContent = 'N/A';
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
        instructionText.textContent = 'Please save and close the current registry first, then you can proceed with adding a follow-up case.';
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
        instructionText.textContent = 'Please close all follow-up records associated with this registry before adding new follow-ups.';
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
        modalContainer.className = "modal fade";
        modalContainer.id = "closedRegistryModal";
        modalContainer.setAttribute("tabindex", "-1");
        modalContainer.setAttribute("aria-hidden", "true");
        const modalDialog = document.createElement('div');
        modalDialog.className = "modal-dialog";
        modalDialog.setAttribute("role", "document");
        modalContainer.appendChild(modalDialog);
        const modalContent = document.createElement('div');
        modalContent.className = "modal-content";
        modalDialog.appendChild(modalContent);
        const modalHeader = document.createElement('div');
        modalHeader.className = "modal-header";
        modalContent.appendChild(modalHeader);
        const modalTitle = document.createElement('h5');
        modalTitle.className = "modal-title";
        modalTitle.textContent = "Registry Closed";
        modalHeader.appendChild(modalTitle);
        const closeButton = document.createElement('button');
        closeButton.className = "btn-close";
        closeButton.setAttribute("data-bs-dismiss", "modal");
        closeButton.setAttribute("aria-label", "Close");
        modalHeader.appendChild(closeButton);
        const modalBody = document.createElement('div');
        modalBody.className = "modal-body";
        modalContent.appendChild(modalBody);
        const firstSection = document.createElement('div');
        firstSection.className = "mb-3";
        modalBody.appendChild(firstSection);
        const alert = document.createElement('div');
        alert.className = "alert alert-danger d-flex align-items-center";
        alert.setAttribute("role", "alert");
        firstSection.appendChild(alert);
        const alertIcon = document.createElement('i');
        alertIcon.className = "ti ti-alert-circle me-2";
        alert.appendChild(alertIcon);
        const alertText = document.createElement('div');
        alertText.textContent = "This registry has already been closed and cannot be edited.";
        alert.appendChild(alertText);
        const employeeName = document.createElement('p');
        employeeName.id = "closedRegistryEmployeeName";
        employeeName.className = "mb-1";
        firstSection.appendChild(employeeName);
        const contactSection = document.createElement('div');
        contactSection.className = "mb-3";
        modalBody.appendChild(contactSection);
        const contactText = document.createElement('p');
        contactText.textContent = "If you need to make changes to this registry, please contact your administrator.";
        contactSection.appendChild(contactText);
        const modalFooter = document.createElement('div');
        modalFooter.className = "modal-footer";
        modalContent.appendChild(modalFooter);
        const okButton = document.createElement('button');
        okButton.type = "button";
        okButton.className = "btn btn-primary";
        okButton.setAttribute("data-bs-dismiss", "modal");
        okButton.textContent = "OK";
        modalFooter.appendChild(okButton);
        const viewButton = document.createElement('a');
        viewButton.id = "viewRegistryBtn";
        viewButton.className = "btn btn-outline-secondary";
        viewButton.textContent = "View Only";
        modalFooter.appendChild(viewButton);
        document.body.appendChild(modalContainer);
    }
function populateReferralModal(entry) {
    document.getElementById('outsideReferralModalLabel').textContent = 'Outside Referral Details';

    const employeeId = entry.employee_id?.toLowerCase();
    document.getElementById('hiddenEmployeeId').value = employeeId;
const hospitalMap = {
  "1": "City Hospital",
  "2": "State Medical"
};
    const referral = entry.outside_referral || {};
 const hospitalValue = referral.hospital_name;
const hospitalNameElement = document.getElementById('hospitalName');

// If hospitalValue is a known ID, use the mapped name; else use it as-is
hospitalNameElement.textContent = hospitalMap[hospitalValue] || hospitalValue || 'N/A';
    document.getElementById('accompaniedBy').textContent = referral.accompanied_by || 'N/A';
    document.getElementById('vehicleType').textContent = referral.vehicle_type || 'N/A';

    const ambulanceSection = document.getElementById('ambulanceDetailsSection');
    if (referral.vehicle_type === 'ambulance') {
        ambulanceSection.classList.remove('d-none');
        document.getElementById('ambulanceDriver').textContent = referral.ambulance_driver || 'N/A';
        document.getElementById('ambulanceNumber').textContent = referral.ambulance_number || 'N/A';

        const formatDateTime = (dateStr) => {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            return date.toLocaleString();
        };

        document.getElementById('ambulanceOutTime').textContent = formatDateTime(referral.ambulance_outtime);
        document.getElementById('ambulanceInTime').textContent = formatDateTime(referral.ambulance_intime);

        document.getElementById('meterOut').textContent = referral.meter_out || 'N/A';
        document.getElementById('meterIn').textContent = referral.meter_in || 'N/A';
    } else {
        ambulanceSection.classList.add('d-none');
    }

    document.getElementById('employeeESI').textContent = referral.employee_esi === 1 ? 'Yes' : 'No';
    document.getElementById('mrNumber').textContent = referral.mr_number || 'N/A';

    const opRegistryIdSpan = document.getElementById('hiddenOpRegistryId');
    if (opRegistryIdSpan) {
        opRegistryIdSpan.textContent = referral.op_registry_id || 'N/A';
    }

    // Reusable preview function
    function renderAttachmentsSection(details) {
        const attachmentSection = document.getElementById('attachmentSection');
        const attachmentsCard = document.getElementById('attachmentsCard');

        // Clear previous content and hide section by default
        attachmentSection.innerHTML = '';
        attachmentSection.style.display = 'none';
        attachmentsCard.style.display = 'none';

        let hasAttachment = false;

        // Create container for buttons and label
        const buttonRow = document.createElement('div');
        buttonRow.className = 'd-flex flex-wrap align-items-center gap-2';

        // --- Discharge Summary ---
        if (details.attachment_discharge) {
            hasAttachment = true;

            const dischargeBtn = document.createElement('button');
            dischargeBtn.type = 'button';
            dischargeBtn.className = 'btn btn-outline-primary btn-sm';
            dischargeBtn.textContent = 'Discharge Summary';

            dischargeBtn.addEventListener('click', () => {
                openPreview(details.attachment_discharge, 'Discharge Summary');
            });

            buttonRow.appendChild(dischargeBtn);
        }

        // --- Test Reports ---
        let testReportsArray = [];
        try {
            testReportsArray = JSON.parse(details.attachment_test_reports);
        } catch {
            testReportsArray = [];
        }

        if (Array.isArray(testReportsArray) && testReportsArray.length > 0) {
            hasAttachment = true;

            testReportsArray.forEach((attachment, index) => {
                const reportBtn = document.createElement('button');
                reportBtn.type = 'button';
                reportBtn.className = 'btn btn-outline-secondary btn-sm';
                reportBtn.textContent = `Test Report ${index + 1}`;

                reportBtn.addEventListener('click', () => {
                    openPreview(attachment, `Test Report ${index + 1}`);
                });

                buttonRow.appendChild(reportBtn);
            });
        }

        // --- Show or Hide the Card ---
        if (hasAttachment) {
            const label = document.createElement('h6');
            label.className = 'fw-semibold mb-0';
            label.textContent = 'Attachments:';

            buttonRow.prepend(label);
            attachmentSection.appendChild(buttonRow);

            attachmentSection.style.display = 'flex';
            attachmentSection.style.gap = '1rem';
            attachmentsCard.style.display = 'block';
            console.log('Attachments found for this referral.');
        } else {
            console.log('No attachments found for this referral.');
            // No attachments: ensure section is hidden
            attachmentSection.style.display = 'none';
            attachmentsCard.style.display = 'none';
        }
    }

    function openPreview(dataUrl, title = 'Attachment') {
        const mimeType = dataUrl.substring(5, dataUrl.indexOf(';'));
        const newWindow = window.open('', '_blank');

        if (!newWindow) {
            alert('Popup blocked! Please allow popups for this site.');
            return;
        }

        let content = `
        <html>
            <head>
                <title>${title}</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                    img, embed { max-width: 100%; margin-top: 20px; }
                    .btn-download {
                        margin-top: 20px;
                        display: inline-block;
                        background: #0d6efd;
                        color: #fff;
                        padding: 10px 15px;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                </style>
            </head>
            <body>
                <h3>${title}</h3>
        `;

        if (mimeType.startsWith('image/')) {
            content += `<img src="${dataUrl}" alt="${title}" />`;
        } else if (mimeType === 'application/pdf') {
            content += `<embed src="${dataUrl}" width="100%" height="600px" />`;
        } else {
            content += `<p>Unsupported file format for preview.</p>`;
        }

        content += `<br><a href="${dataUrl}" download="${title.replace(/\s+/g, '_')}" class="btn-download">Download</a></body></html>`;

        newWindow.document.write(content);
        newWindow.document.close();
    }

    if (employeeId) {
        fetch(`/ohc/health-registry/get-employee?employee_id=${employeeId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to fetch employee details');
            return res.json();
        })
        .then(employeeData => {
            document.getElementById('employeeName').textContent = `${employeeData.employee_firstname || ''} ${employeeData.employee_lastname || ''}`.trim() || 'N/A';
            const deptElem = document.getElementById('employeeDepartment');
            if (deptElem) deptElem.textContent = employeeData.employee_department_name || 'N/A';

            const hiddenFields = {
                hiddenEmployeeName: `${employeeData.employee_firstname || ''} ${employeeData.employee_lastname || ''}`.trim(),
                hiddenEmployeeEmail: employeeData.employee_email || '',
                hiddenEmployeeDepartment: employeeData.employee_department_name || '',
                hiddenEmployeeDOB: employeeData.employee_dob || '',
                hiddenEmployeeGender: employeeData.employee_gender || '',
                hiddenEmployeeContact: employeeData.employee_contact_number || '',
                hiddenEmployeeDesignation: employeeData.employee_designation || '',
                hiddenOpRegistryId: referral.op_registry_id || '',
                hiddenEmployeeUserId: employeeData.employee_user_id || '',
                hiddenEmployeeCorporate: employeeData.employee_corporate_name || '',
                hiddenEmployeeAge: employeeData.employee_age || '',
            };

            for (const [key, value] of Object.entries(hiddenFields)) {
                let input = document.getElementById(key);
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.id = key;
                    input.name = key;
                    document.getElementById('outsideReferralModal').appendChild(input);
                }
                input.value = value;
            }

            const userId = employeeData.employee_user_id || '';
            const opRegistryId = referral.op_registry_id || '';
            if (userId && opRegistryId) {
                fetch(`/ohc/health-registry/get-hospitalization-by-id/${userId}/${opRegistryId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        // Try to parse JSON error message
                        return res.json().then(data => {
                        throw new Error(data.message || 'Failed to fetch hospitalization details');
                        }).catch(() => {
                        throw new Error('Failed to fetch hospitalization details');
                        });
                    }
                    return res.json();
                })
                .then(hospData => {
                    if (!hospData.result || !hospData.data) {
                        throw new Error('No hospitalization details found');
                    }

                    let details = null;
                    if (hospData && hospData.result && hospData.data) {
                        if (Array.isArray(hospData.data.data) && hospData.data.data.length > 0) {
                            details = hospData.data.data[0];
                        } else if (hospData.data.result && Array.isArray(hospData.data.data) && hospData.data.data.length > 0) {
                            details = hospData.data.data[0];
                        } else if (Array.isArray(hospData.data) && hospData.data.length > 0) {
                            details = hospData.data[0];
                        } else if (typeof hospData.data === 'object' && hospData.data !== null && !Array.isArray(hospData.data)) {
                            details = hospData.data;
                        }
                    }

                    if (details) {
                        document.getElementById('hospitalName').textContent = details.hospital_name || 'N/A';
                        document.getElementById('accompaniedBy').textContent = details.accompanied_by || 'N/A';
                        document.getElementById('vehicleType').textContent = details.vehicle_type || 'N/A';
                        document.getElementById('employeeESI').textContent = details.employee_esi === 1 ? 'Yes' : 'No';
                        document.getElementById('mrNumber').textContent = details.mr_number || 'N/A';
                        document.getElementById('hiddenOpRegistryId').textContent = details.op_registry_id || 'N/A';

                        if (details.vehicle_type === 'ambulance') {
                            ambulanceSection.classList.remove('d-none');
                            document.getElementById('ambulanceDriver').textContent = details.ambulance_driver || 'N/A';
                            document.getElementById('ambulanceNumber').textContent = details.ambulance_number || 'N/A';

                            const formatDateTime = (dateStr) => {
                                if (!dateStr) return 'N/A';
                                const date = new Date(dateStr);
                                return date.toLocaleString();
                            };

                            document.getElementById('ambulanceOutTime').textContent = formatDateTime(details.ambulance_outtime || details.from_datetime);
                            document.getElementById('ambulanceInTime').textContent = formatDateTime(details.ambulance_intime || details.to_datetime);
                            document.getElementById('meterOut').textContent = details.meter_out || 'N/A';
                            document.getElementById('meterIn').textContent = details.meter_in || 'N/A';
                        } else {
                            ambulanceSection.classList.add('d-none');
                        }

                        document.getElementById('conditionName').textContent = (details.condition_names && details.condition_names.length)
                            ? details.condition_names.join(', ')
                            : (details.other_condition_name || 'N/A');

                        document.getElementById('description').textContent = details.description || 'N/A';

                        console.log('from_datetime raw:', details.from_datetime);
                        console.log('to_datetime raw:', details.to_datetime);

                        function formatDate(dateStr) {
                            if (!dateStr) return 'N/A';

                            const date = new Date(dateStr);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                            const year = date.getFullYear();

                            return `${day}-${month}-${year}`;
                        }

                        document.getElementById('hospitalizationfromDate').textContent = formatDate(details.from_datetime);
                        document.getElementById('hospitalizationToDate').textContent = formatDate(details.to_datetime);

                        document.getElementById('hospitalNameCard').textContent = details.hospital_name || 'N/A';
                        document.getElementById('doctorName').textContent = details.doctor_name || 'N/A';

                        // Attach event listeners for attachments here because details object is ready
                        // Render the new attachment section with cards
                        const referralCard = document.getElementById('referralDetailsCard');
                        if (referralCard) referralCard.style.display = 'block';

                        // Also show attachmentsCard if applicable
                        const attachmentsCard = document.getElementById('attachmentsCard');
                        if (attachmentsCard) attachmentsCard.style.display = 'block';
                                                renderAttachmentsSection(details);

                    } else {
                        const attachmentsCard = document.getElementById('attachmentsCard');
                        if (attachmentsCard) {
                            attachmentsCard.style.display = 'none';
                        }
                    }
                })
                .catch(err => {
                    console.error('Error fetching hospitalization details:', err);
                    const referralCard = document.getElementById('referralDetailsCard');
  if (referralCard) referralCard.style.display = 'none';

                    const attachmentsCard = document.getElementById('attachmentsCard');
  if (attachmentsCard) {
    attachmentsCard.style.display = 'none';
  }

                });
            }
        })
        .catch(err => {
            console.error('Error fetching employee info:', err);
            document.getElementById('employeeName').textContent = 'Unknown';
        });
    }
}


    function populatePrescriptionModal(entry) {
        if (!entry.prescriptionsForRegistry ||
            !entry.prescriptionsForRegistry.prescription ||
            !entry.prescriptionsForRegistry.prescription_details) {
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
            prescriptionIdElement.textContent = prescription.master_doctor_id + " " + prescription.prescription_id || 'N/A';
        }
        const patientInfoElement = document.querySelector('.patient-info div:first-child');
        if (patientInfoElement) {
            patientInfoElement.textContent = `${entry.employee_name} - ${entry.age} / ${entry.prescriptionsForRegistry.prescription.employee_gender} ${entry.employee_id}`;
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
    
  document.addEventListener('DOMContentLoaded', function () {
    const updateBtn = document.getElementById('updateHospitalizationBtn');
    if (!updateBtn) {
        console.warn('Update button not found!');
        return;
    }

    updateBtn.addEventListener('click', function () {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/ohc/health-registry/update-hospitalization';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // Append all data as hidden inputs
        const fields = {
            employee_id: document.getElementById('hiddenEmployeeId')?.value || '',
            hospital_name: document.getElementById('hospitalName')?.textContent.trim() || '',
            employee_name: document.getElementById('hiddenEmployeeName')?.value || '',
            employee_email: document.getElementById('hiddenEmployeeEmail')?.value || '',
            employee_department: document.getElementById('hiddenEmployeeDepartment')?.value || '',
            employee_dob: document.getElementById('hiddenEmployeeDOB')?.value || '',
            employee_gender: document.getElementById('hiddenEmployeeGender')?.value || '',
            employee_contact: document.getElementById('hiddenEmployeeContact')?.value || '',
            employee_designation: document.getElementById('hiddenEmployeeDesignation')?.value || '',
            employee_age: document.getElementById('hiddenEmployeeAge')?.value || '',
            employee_user_id: document.getElementById('hiddenEmployeeUserId')?.value || '',
            op_registry_id: document.getElementById('hiddenOpRegistryId')?.value || '',
            employee_corporate: document.getElementById('hiddenEmployeeCorporate')?.value || ''
        };

        for (const key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit(); // 🚀 Redirect via POST
    });
});

</script>
<!-- Modal for showing the attachment -->
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
        <h5 class="modal-title" style="color: #ffffff;margin-bottom: 15px;" id="outsideReferralModalLabel">Prescription & Referral Details</h5>
        </div>
<div class="modal-body">

  <!-- ROW 1: Referral Details (Full Width) -->
 <div class="row g-3 mb-3" id="referralDetailsCard" style="display:none;">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title fw-semibold">Referral Details</h6>
          <div class="row">
            <div class="col-12 mb-2"><strong>Condition Name:</strong> <span id="conditionName">N/A</span></div>
            <div class="col-12 mb-2"><strong>Description:</strong> <span id="description">N/A</span></div>
            <div class="col-md-6 mb-2"><strong>From Date:</strong> <span id="hospitalizationfromDate">N/A</span></div>
            <div class="col-md-6 mb-2"><strong>To Date:</strong> <span id="hospitalizationToDate">N/A</span></div>
            <div class="col-md-6 mb-2"><strong>Hospital Name:</strong> <span id="hospitalNameCard">N/A</span></div>
            <div class="col-md-6 mb-2"><strong>Doctor Name:</strong> <span id="doctorName">N/A</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ROW 2: Attachments (Full Width) -->
<div class="row g-3 mb-3" id="attachmentsCard" style="display:none;">
  <div class="col-12">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div id="attachmentSection"></div>
      </div>
    </div>
  </div>
</div>


  <!-- ROW 3: 4 Cards (Unchanged) -->
  <div class="row g-3 mb-3">

    <!-- Employee Info -->
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h6 class="card-title fw-semibold">Employee Information</h6>
          <p id="employeeName" class="mb-1 fw-medium"></p>

          <!-- Hidden Inputs -->
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

    <!-- Hospital Info -->
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h6 class="card-title fw-semibold">Hospital Information</h6>
          <div><strong>Hospital Name:</strong> <span id="hospitalName"></span></div>
          <div><strong>Accompanied By:</strong> <span id="accompaniedBy"></span></div>
        </div>
      </div>
    </div>

    <!-- Transportation Info -->
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h6 class="card-title fw-semibold">Transportation Details</h6>
          <div><strong>Vehicle Type:</strong> <span id="vehicleType"></span></div>

          <!-- Ambulance Info -->
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

    <!-- Additional Info -->
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

<!-- Modal Footer -->
<div class="modal-footer">
  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
  <button type="button" class="btn btn-primary" id="updateHospitalizationBtn">Update Hospitalization Details</button>
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
@endsection
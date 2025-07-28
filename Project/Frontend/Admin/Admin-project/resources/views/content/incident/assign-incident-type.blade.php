@extends('layouts/layoutMaster')
@section('title', 'Assign Incident Type')
@section('content')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/ui-modals.js', 'resources/assets/js/questions.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
<style>
  .incident-type-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    height: fit-content;
  }

  .injury-type-row {
    border-bottom: 1px solid #f0f0f0;
  }

  .injury-type-row:last-child {
    border-bottom: none;
  }

  .color-picker {
    width: 24px !important;
    height: 24px !important;
    padding: 0 !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem 0 0 0.25rem !important;
    background: none !important;
    outline: none !important;
    box-shadow: none !important;
    cursor: pointer;
    margin: 0 !important;
  }

  .color-picker {
    width: 30px !important;
    height: 30px !important;
    flex: 0 0 30px !important;
    padding: 0 !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    background: none !important;
    outline: none !important;
    box-shadow: none !important;
    cursor: pointer;
    margin: 0 !important;
  }

  .color-picker::-webkit-color-swatch-wrapper {
    padding: 0 !important;
    border: none !important;
    border-radius: 0.25rem 0 0 0.25rem !important;
    margin: 0 !important;
  }

  .color-picker::-webkit-color-swatch {
    border: none !important;
    border-radius: 0.25rem 0 0 0.25rem !important;
    margin: 0 !important;
  }

  .color-picker::-moz-color-swatch {
    border: none !important;
    border-radius: 0.25rem 0 0 0.25rem !important;
    margin: 0 !important;
  }

  .color-hex-input {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    min-width: 90px;
    height: 24px !important;
    line-height: 1;
  }

  .form-check-input {
    margin-right: 8px;
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.2;
  }

  .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
  }

  .form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }

  .input-group-sm>.form-control,
  .input-group-sm>.input-group-text,
  .input-group-sm>.btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
  }

  .input-group-sm .form-control-color {
    width: 30px !important;
    padding: 0;
    border: none !important;
  }

  #saveChangesBtn {
    transition: all 0.3s ease;
  }

  /* Improved column widths and spacing */
  .injury-type-name-col {
    flex: 0 0 48%;
    max-width: 48%;
    padding-right: 0.25rem;
  }

  .injury-type-color-col {
    flex: 0 0 37%;
    max-width: 37%;
    padding-left: 0.25rem;
    padding-right: 0.25rem;
  }

  .injury-type-action-col {
    flex: 0 0 15%;
    max-width: 15%;
    padding-left: 0.25rem;
  }

  .header-name-col {
    flex: 0 0 48%;
    max-width: 48%;
    padding-right: 0.25rem;
  }

  .header-color-col {
    flex: 0 0 37%;
    max-width: 37%;
    padding-left: 0.25rem;
    padding-right: 0.25rem;
  }

  .header-action-col {
    flex: 0 0 15%;
    max-width: 15%;
    padding-left: 0.25rem;
  }

  .input-group {
    gap: 0;
  }

  .color-hex-input {
    border-left: none !important;
    border-radius: 0 0.25rem 0.25rem 0 !important;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .col-lg-4 {
      margin-bottom: 1rem;
    }

    .incident-types-container .row {
      margin: 0;
    }

    .card-header {
      flex-direction: column;
      gap: 0.5rem;
      align-items: flex-start !important;
    }

    .card-header h5 {
      margin-bottom: 0.5rem;
    }

    .injury-type-name-col,
    .header-name-col {
      flex: 0 0 50%;
      max-width: 50%;
    }

    .injury-type-color-col,
    .header-color-col {
      flex: 0 0 35%;
      max-width: 35%;
    }

    .injury-type-action-col,
    .header-action-col {
      flex: 0 0 15%;
      max-width: 15%;
    }
  }

  /* Additional responsive breakpoints for better 3-column layout */
  @media (max-width: 992px) and (min-width: 769px) {
    .col-lg-4 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  }

  @media (max-width: 576px) {
    .col-lg-4 {
      flex: 0 0 100%;
      max-width: 100%;
    }
  }

  .input-group-sm {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .color-picker {
    width: 32px !important;
    height: 32px !important;
    border: none !important;
    padding: 0 !important;
    border-radius: 6px !important;
    cursor: pointer;
    flex-shrink: 0;
  }

  .color-hex-input {
    height: 32px;
    line-height: 1;
    font-size: 0.875rem;
    padding: 0 0.5rem;
    min-width: 100px;
    border-radius: 4px;
  }

  .injury-type-name-col input {
    height: 32px;
    font-size: 0.875rem;
  }

  .injury-type-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .injury-type-action-col button {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    border-radius: 6px;
  }
</style>
<div class="card">
  <div class="d-flex justify-content-between align-items-center card-header">
    <h5 class="mb-0">Incident Types</h5>
    <button type="button" class="btn btn-primary" id="saveChangesBtn" style="display: none;">
      <i class="fas fa-save me-1"></i>Save Changes
    </button>
  </div>
  <div class="table-responsive text-nowrap">
    <div id="preloader" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p>Fetching Incident Types...</p>
    </div>
    <div class="incident-types-container p-4">
      <div class="row">
      </div>
    </div>
  </div>
</div>
<hr class="my-12">
<script>
  let allIncidentTypes = [];
  let assignedIncidentTypes = [];
  let hasChanges = false;
  document.addEventListener('DOMContentLoaded', function () {
    Promise.all([
      fetch('https://mhv-admin.hygeiaes.com/corporate/getAllIncidentTypes'),
      fetch('https://mhv-admin.hygeiaes.com/corporate/getAllAssignedIncidentTypes/MCBoAmzVFigh')
    ])
      .then(responses => Promise.all(responses.map(response => response.json())))
      .then(([incidentTypesData, assignedData]) => {
        if (incidentTypesData.result) {
          allIncidentTypes = incidentTypesData.data;
          if (assignedData.result) {
            assignedIncidentTypes = assignedData.data;
          }
          renderIncidentTypes();
        }
      })
      .catch(error => {
        console.error('Error fetching data:', error);
        const preloader = document.getElementById('preloader');
        preloader.textContent = '';
        const errorMsg = document.createElement('p');
        errorMsg.className = 'text-danger';
        errorMsg.textContent = 'Error loading incident types. Please try again.';
        preloader.appendChild(errorMsg);
      });
    document.getElementById('saveChangesBtn').addEventListener('click', function () {
      saveChanges();
    });
  });
  function renderIncidentTypes() {
    const container = document.querySelector('.incident-types-container .row');
    const preloader = document.getElementById('preloader');
    preloader.style.display = 'none';
    allIncidentTypes.forEach(incidentType => {
      const assignedData = assignedIncidentTypes.find(
        assigned => assigned.incident_type_id === incidentType.incident_type_id
      );
      const incidentTypeCard = createIncidentTypeCard(incidentType, assignedData);
      container.appendChild(incidentTypeCard);
    });
  }
  function createIncidentTypeCard(incidentType, assignedData = null) {
    const cardCol = document.createElement('div');
    cardCol.className = 'col-lg-4 col-md-6 col-sm-12 mb-4';
    const card = document.createElement('div');
    card.className = 'card incident-type-card';
    card.dataset.incidentTypeId = incidentType.incident_type_id;
    const cardHeader = document.createElement('div');
    cardHeader.className = 'd-flex justify-content-between align-items-center card-header py-2';
    const checkboxDiv = document.createElement('div');
    checkboxDiv.className = 'form-check form-switch mb-0';
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.className = 'form-check-input incident-type-toggle';
    checkbox.id = `incident-type-${incidentType.incident_type_id}`;
    checkbox.checked = assignedData !== null;
    const label = document.createElement('label');
    label.className = 'form-check-label fw-semibold';
    label.setAttribute('for', `incident-type-${incidentType.incident_type_id}`);
    label.textContent = incidentType.incident_type_name;
    checkboxDiv.appendChild(checkbox);
    checkboxDiv.appendChild(label);
    cardHeader.appendChild(checkboxDiv);
    const cardBody = document.createElement('div');
    cardBody.className = 'card-body injury-types-container p-3';
    cardBody.style.display = assignedData ? 'block' : 'none';
    const headerRow = document.createElement('div');
    headerRow.className = 'd-flex mb-2 fw-semibold text-muted small';
    const injuryTypeHeader = document.createElement('div');
    injuryTypeHeader.className = 'header-name-col';
    injuryTypeHeader.textContent = 'Injury Type Text';
    const colorHeader = document.createElement('div');
    colorHeader.className = 'header-color-col';
    colorHeader.textContent = 'Injury Type Color';
    const actionHeader = document.createElement('div');
    actionHeader.className = 'header-action-col';
    headerRow.appendChild(injuryTypeHeader);
    headerRow.appendChild(colorHeader);
    headerRow.appendChild(actionHeader);
    cardBody.appendChild(headerRow);
    if (assignedData && assignedData.injury_color_types) {
      const injuryTypes = Object.entries(assignedData.injury_color_types);
      injuryTypes.forEach(([name, color], index) => {
        addInjuryTypeRow(cardBody, incidentType.incident_type_id, index === 0, name, color);
      });
      if (injuryTypes.length < 2) {
        for (let i = injuryTypes.length; i < 2; i++) {
          addInjuryTypeRow(cardBody, incidentType.incident_type_id, i === 0);
        }
      }
    } else {
      addInjuryTypeRow(cardBody, incidentType.incident_type_id, true);
      addInjuryTypeRow(cardBody, incidentType.incident_type_id, false);
    }
    checkbox.addEventListener('change', function () {
      cardBody.style.display = this.checked ? 'block' : 'none';
      markAsChanged();
    });
    card.appendChild(cardHeader);
    card.appendChild(cardBody);
    cardCol.appendChild(card);
    return cardCol;
  }
  function addInjuryTypeRow(container, incidentTypeId, isFirstRow = false, prefilledName = '', prefilledColor = '#000000') {
    const rowId = Date.now() + Math.random();
    const existingRows = container.querySelectorAll('.injury-type-row').length;
    if (existingRows >= 5) {
      showToast('error', 'Maximum of 5 injury types allowed per incident type.');
      return;
    }
    const row = document.createElement('div');
    row.className = 'd-flex mb-2 injury-type-row align-items-center';
    row.dataset.rowId = rowId;
    const nameCol = document.createElement('div');
    nameCol.className = 'injury-type-name-col';
    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.className = 'form-control form-control-sm injury-type-name';
    nameInput.placeholder = 'Enter injury type';
    nameInput.value = prefilledName;
    nameInput.addEventListener('input', markAsChanged);
    nameCol.appendChild(nameInput);
    const colorCol = document.createElement('div');
    colorCol.className = 'injury-type-color-col';
    const colorInputGroup = document.createElement('div');
    colorInputGroup.className = 'input-group input-group-sm';
    const colorInput = document.createElement('input');
    colorInput.type = 'color';
    colorInput.className = 'form-control form-control-color color-picker';
    colorInput.value = prefilledColor;
    colorInput.title = 'Choose a color';
    colorInput.addEventListener('input', markAsChanged);
    const colorTextInput = document.createElement('input');
    colorTextInput.type = 'text';
    colorTextInput.className = 'form-control color-hex-input';
    colorTextInput.placeholder = '#000000';
    colorTextInput.value = prefilledColor;
    colorTextInput.maxLength = 7;
    colorTextInput.addEventListener('input', markAsChanged);
    colorInput.addEventListener('input', function () {
      colorTextInput.value = this.value;
    });
    colorTextInput.addEventListener('input', function () {
      const hexValue = this.value.trim();
      if (/^#[0-9A-Fa-f]{6}$/i.test(hexValue)) {
        colorInput.value = hexValue;
      } else if (/^#[0-9A-Fa-f]{3}$/i.test(hexValue)) {
        const expandedHex = '#' + hexValue[1] + hexValue[1] + hexValue[2] + hexValue[2] + hexValue[3] + hexValue[3];
        colorInput.value = expandedHex;
        this.value = expandedHex;
      }
    });
    colorTextInput.addEventListener('paste', function (e) {
      setTimeout(() => {
        const hexValue = this.value.trim();
        if (/^#[0-9A-Fa-f]{6}$/i.test(hexValue)) {
          colorInput.value = hexValue;
        } else if (/^#[0-9A-Fa-f]{3}$/i.test(hexValue)) {
          const expandedHex = '#' + hexValue[1] + hexValue[1] + hexValue[2] + hexValue[2] + hexValue[3] + hexValue[3];
          colorInput.value = expandedHex;
          this.value = expandedHex;
        }
      }, 10);
    });
    colorTextInput.addEventListener('keyup', function () {
      const hexValue = this.value.trim();
      if (/^#[0-9A-Fa-f]{6}$/i.test(hexValue)) {
        colorInput.value = hexValue;
      } else if (/^#[0-9A-Fa-f]{3}$/i.test(hexValue)) {
        const expandedHex = '#' + hexValue[1] + hexValue[1] + hexValue[2] + hexValue[2] + hexValue[3] + hexValue[3];
        colorInput.value = expandedHex;
        this.value = expandedHex;
      }
    });
    colorInputGroup.appendChild(colorInput);
    colorInputGroup.appendChild(colorTextInput);
    colorCol.appendChild(colorInputGroup);
    const buttonCol = document.createElement('div');
    buttonCol.className = 'injury-type-action-col text-center';
    if (isFirstRow) {
      const addButton = document.createElement('button');
      addButton.className = 'btn btn-sm btn-primary add-injury-type';
      const addIcon = document.createElement('i');
      addIcon.className = 'fas fa-plus';
      addButton.appendChild(addIcon);
      addButton.title = 'Add another injury type';
      addButton.type = 'button';
      addButton.addEventListener('click', function () {
        addInjuryTypeRow(container, incidentTypeId, false);
        updateRowButtons(container, incidentTypeId);
      });
      buttonCol.appendChild(addButton);
    } else if (existingRows >= 2) {
      const removeButton = document.createElement('button');
      removeButton.className = 'btn btn-sm btn-danger remove-injury-type';
      const removeIcon = document.createElement('i');
      removeIcon.className = 'fas fa-minus';
      removeButton.appendChild(removeIcon);
      removeButton.title = 'Remove this injury type';
      removeButton.type = 'button';
      removeButton.addEventListener('click', function () {
        row.remove();
        updateRowButtons(container, incidentTypeId);
        markAsChanged();
      });
      buttonCol.appendChild(removeButton);
    }
    row.appendChild(nameCol);
    row.appendChild(colorCol);
    row.appendChild(buttonCol);
    container.appendChild(row);
  }
  function updateRowButtons(container, incidentTypeId) {
    const rows = container.querySelectorAll('.injury-type-row');
    rows.forEach((row, index) => {
      const buttonCol = row.querySelector('.injury-type-action-col');
      buttonCol.textContent = '';
      if (index === 0 && rows.length < 5) {
        const addButton = document.createElement('button');
        addButton.className = 'btn btn-sm btn-primary add-injury-type';
        const addIcon = document.createElement('i');
        addIcon.className = 'fas fa-plus';
        addButton.appendChild(addIcon);
        addButton.title = 'Add another injury type';
        addButton.type = 'button';
        addButton.addEventListener('click', function () {
          addInjuryTypeRow(container, incidentTypeId, false);
          updateRowButtons(container, incidentTypeId);
        });
        buttonCol.appendChild(addButton);
      } else if (index >= 2) {
        const removeButton = document.createElement('button');
        removeButton.className = 'btn btn-sm btn-danger remove-injury-type';
        const removeIcon = document.createElement('i');
        removeIcon.className = 'fas fa-minus';
        removeButton.title = 'Remove this injury type';
        removeButton.type = 'button';
        removeButton.addEventListener('click', function () {
          row.remove();
          updateRowButtons(container, incidentTypeId);
          markAsChanged();
        });
        buttonCol.appendChild(removeIcon);
      }
    });
  }
  function markAsChanged() {
    if (!hasChanges) {
      hasChanges = true;
      document.getElementById('saveChangesBtn').style.display = 'block';
    }
  }
  function saveChanges() {
    const incidentTypeCards = document.querySelectorAll('.incident-type-card');
    const incidentTypes = [];
    incidentTypeCards.forEach(card => {
      const checkbox = card.querySelector('.incident-type-toggle');
      const incidentTypeId = parseInt(card.dataset.incidentTypeId);
      if (checkbox.checked) {
        const injuryRows = card.querySelectorAll('.injury-type-row');
        const injuryColorTypes = {};
        injuryRows.forEach(row => {
          const nameInput = row.querySelector('.injury-type-name');
          const colorInput = row.querySelector('.color-picker');
          if (nameInput.value.trim() && colorInput.value) {
            injuryColorTypes[nameInput.value.trim()] = colorInput.value;
          }
        });
        if (Object.keys(injuryColorTypes).length > 0) {
          incidentTypes.push({
            id: incidentTypeId,
            injury_color_types: injuryColorTypes
          });
        }
      }
    });
    if (incidentTypes.length === 0) {
      showToast('error', 'Please select at least one incident type with injury types before saving.');
      return;
    }
    const saveBtn = document.getElementById('saveChangesBtn');
    const originalText = saveBtn.textContent;
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    const requestData = {
      incident_types: incidentTypes
    };
    fetch('https://mhv-admin.hygeiaes.com/corporate/assignIncidentTypes/MCBoAmzVFigh', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(requestData)
    })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;
        saveBtn.style.display = 'none';
        hasChanges = false;
        if (data.result) {
          showToast('success', data.data || 'Incident types synced successfully.');
          refreshAssignedData();
        } else {
          throw new Error(data.message || 'Failed to save incident types');
        }
      })
      .catch(error => {
        console.error('Error saving incident types:', error);
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;
        showToast('error', 'Failed to save changes. Please try again.');
      });
  }
  function refreshAssignedData() {
    fetch('https://mhv-admin.hygeiaes.com/corporate/getAllAssignedIncidentTypes/MCBoAmzVFigh')
      .then(response => response.json())
      .then(data => {
        if (data.result) {
          assignedIncidentTypes = data.data;
        }
      })
      .catch(error => {
        console.error('Error refreshing assigned data:', error);
      });
  }
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-injury-type') || e.target.closest('.add-injury-type')) {
      const button = e.target.classList.contains('add-injury-type') ? e.target : e.target.closest('.add-injury-type');
      const container = button.closest('.injury-types-container');
      const incidentTypeId = button.closest('.incident-type-card').dataset.incidentTypeId;
      addInjuryTypeRow(container, incidentTypeId, false);
      updateRowButtons(container, incidentTypeId);
    }
    if (e.target.classList.contains('remove-injury-type') || e.target.closest('.remove-injury-type')) {
      const button = e.target.classList.contains('remove-injury-type') ? e.target : e.target.closest('.remove-injury-type');
      const row = button.closest('.injury-type-row');
      const container = button.closest('.injury-types-container');
      const incidentTypeId = button.closest('.incident-type-card').dataset.incidentTypeId;
      row.remove();
      updateRowButtons(container, incidentTypeId);
      markAsChanged();
    }
  });
</script>
@endsection
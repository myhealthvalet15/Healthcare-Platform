let allIncidentTypes = [];
let assignedIncidentTypes = [];
const corporateId = window.location.pathname.split('/').pop();
let hasChanges = false;
document.addEventListener('DOMContentLoaded', function () {
    Promise.all([
        apiRequest({
            url: '/corporate/getAllIncidentTypes',
            method: 'GET',
            onSuccess: (response) => {
                if (response.result) {
                    allIncidentTypes = response.data;
                }
                return response;
            },
            onError: (error) => {
                console.error('Error fetching incident types:', error);
                const preloader = document.getElementById('preloader');
                preloader.textContent = '';
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-danger';
                errorMsg.textContent = 'Error loading incident types. Please try again.';
                preloader.appendChild(errorMsg);
            }
        }),
        apiRequest({
            url: `/corporate/getAllAssignedIncidentTypes/${corporateId}`,
            method: 'GET',
            onSuccess: (response) => {
                if (response.result) {
                    assignedIncidentTypes = response.data;
                }
                return response;
            },
            onError: (error) => {
                console.error('Error fetching assigned incident types:', error);
            }
        })
    ]).then(() => {
        renderIncidentTypes();
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
    colorInputGroup.appendChild(colorInput);
    colorInputGroup.appendChild(colorTextInput);
    colorCol.appendChild(colorInputGroup);
    const buttonCol = document.createElement('div');
    buttonCol.className = 'injury-type-action-col text-center';
    if (isFirstRow) {
        if (existingRows < 5) {
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
        }
    } else {
        if (existingRows >= 2) {
            const removeButton = document.createElement('button');
            removeButton.className = 'btn btn-sm btn-danger remove-injury-type';
            const removeIcon = document.createElement('i');
            removeIcon.className = 'fas fa-minus';
            removeButton.appendChild(removeIcon);
            removeButton.title = 'Remove this injury type';
            removeButton.type = 'button';
            removeButton.addEventListener('click', function () {
                if (container.querySelectorAll('.injury-type-row').length > 2) {
                    row.remove();
                    updateRowButtons(container, incidentTypeId);
                    markAsChanged();
                } else {
                    showToast('error', 'Minimum 2 injury types required');
                }
            });
            buttonCol.appendChild(removeButton);
        }
    }
    row.appendChild(nameCol);
    row.appendChild(colorCol);
    row.appendChild(buttonCol);
    container.appendChild(row);
}
function updateRowButtons(container, incidentTypeId) {
    const rows = container.querySelectorAll('.injury-type-row');
    const totalRows = rows.length;
    rows.forEach((row, index) => {
        const buttonCol = row.querySelector('.injury-type-action-col');
        buttonCol.innerHTML = '';
        if (index === 0) {
            if (totalRows < 5) {
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
            }
        } else {
            if (totalRows > 2) {
                const removeButton = document.createElement('button');
                removeButton.className = 'btn btn-sm btn-danger remove-injury-type';
                const removeIcon = document.createElement('i');
                removeIcon.className = 'fas fa-minus';
                removeButton.appendChild(removeIcon);
                removeButton.title = 'Remove this injury type';
                removeButton.type = 'button';
                removeButton.addEventListener('click', function () {
                    if (totalRows > 2) {
                        row.remove();
                        updateRowButtons(container, incidentTypeId);
                        markAsChanged();
                    }
                });
                buttonCol.appendChild(removeButton);
            }
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
    apiRequest({
        url: `/corporate/assignIncidentTypes/${corporateId}`,
        method: 'POST',
        data: {
            incident_types: incidentTypes
        },
        onSuccess: (response) => {
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
            saveBtn.style.display = 'none';
            hasChanges = false;
            if (response.result) {
                showToast('success', response.data || 'Incident types synced successfully.');
                refreshAssignedData();
            } else {
                throw new Error(response.message || 'Failed to save incident types');
            }
        },
        onError: (error) => {
            console.error('Error saving incident types:', error);
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
            showToast('error', 'Failed to save changes. Please try again.');
        }
    });
}
function refreshAssignedData() {
    apiRequest({
        url: `/corporate/getAllAssignedIncidentTypes/${corporateId}`,
        method: 'GET',
        onSuccess: (response) => {
            if (response.result) {
                assignedIncidentTypes = response.data;
            }
        },
        onError: (error) => {
            console.error('Error refreshing assigned data:', error);
        }
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
        if (container.querySelectorAll('.injury-type-row').length > 2) {
            row.remove();
            updateRowButtons(container, incidentTypeId);
            markAsChanged();
        } else {
            showToast('error', 'Minimum 2 injury types required');
        }
    }
});

const preloader = document.getElementById('preloader');
const table = document.getElementById('incidenttable');
console.log("stop loading");
const tbody = document.getElementById('incidentbody');
window.onload = function () {
    fetchincidentTypes();
};
if (!table || !tbody) {
    console.warn('Table or tbody not found');
} else {
}
const addincidentButton = document.getElementById('add-new-incident');
const incidentNameInput = document.getElementById('incident-name');
incidentNameInput.addEventListener('input', () => {
    let errorContainer = incidentNameInput.parentElement.querySelector('.fv-plugins-message-container');
    if (errorContainer) {
        errorContainer.remove();
        incidentNameInput.classList.remove('is-invalid');
    }
});
addincidentButton.addEventListener('click', () => {
    console.log("save the button");
    const incidentName = incidentNameInput.value.trim();
    let errorContainer = incidentNameInput.parentElement.querySelector('.fv-plugins-message-container');
    if (errorContainer) {
        errorContainer.remove();
    }
    addincidentButton.disabled = true;
    addincidentButton.innerHTML =
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
         &nbspSaving...`;
    addNewincident(incidentName);
});



function addNewincident(incidentName) {
    if (!incidentName || incidentName.trim().length === 0) {
        addincidentButton.disabled = false;
        addincidentButton.innerHTML = 'Save Changes';
        incidentNameInput.classList.add('is-invalid');
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('fv-plugins-message-container', 'fv-plugins-message-container--enabled', 'invalid-feedback');
        errorMessage.innerHTML = '<div data-field="formValidationUsername" data-validator="notEmpty">The name is required</div>';
        incidentNameInput.parentElement.appendChild(errorMessage);
        showToast('error', 'Incident name is required');
        return false;
    }

    apiRequest({
        url: "/corporate/addIncidentType",
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        data: { incident_type_name: incidentName },
        onSuccess: (responseData) => {
            const modalElement = document.getElementById('addincident');
            const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
            if (responseData.result) {
                const toastType = responseData.result === true ? 'success' : 'error';
                const toastMessage = responseData.data || "Operation completed.";
                showToast(toastType, "Add Incident completed", toastMessage);
                fetchincidentTypes();
                bootstrapModal.hide();
                incidentNameInput.value = '';
                addincidentButton.disabled = false;
                addincidentButton.innerHTML = 'Save Changes';
            } else {
                const toastType = responseData.result === false ? 'success' : 'error';
                const toastMessage = responseData.data || "The incident type name has already been taken.";
                showToast(toastType, "Add Incident Not completed", toastMessage);
                bootstrapModal.hide();
                addincidentButton.disabled = false;
                addincidentButton.innerHTML = 'Save Changes';
            }
        },
            onError: (error) => {
            const toastMessage = error?.data || 'The incident type name has already been taken.';
            showToast('error', "Add Incident Not completed", toastMessage);
            addincidentButton.disabled = false;
            addincidentButton.innerHTML = 'Save Changes';
        },
        onComplete: () => {
            addincidentButton.disabled = false;
            addincidentButton.innerHTML = 'Save Changes';
        }
    });
}

function fetchincidentTypes() {
    console.log("checking the table");
    tbody.innerHTML = '';
    preloader.style.display = 'block';
    apiRequest({
        url: "/corporate/getAllIncidentTypes",
        method: 'GET',
        onSuccess: (response) => {
            preloader.style.display = 'none';

            const incidents = response.data;
            if (!Array.isArray(incidents) || incidents.length === 0) {
                const noDataMessage = document.createElement('tr');
                noDataMessage.innerHTML = `
                    <td colspan="3" class="text-center">No incident available.</td>`;
                tbody.appendChild(noDataMessage);
                table.style.display = 'table';
                return;
            }
            table.style.display = 'table';
            incidents.forEach(incident => {
                const row = document.createElement('tr');
                const incidentNameCell = document.createElement('td');
                incidentNameCell.textContent = incident.incident_type_name;
                row.appendChild(incidentNameCell);
                const actionsCell = document.createElement('td');
                const editIcon = document.createElement('i');
                editIcon.classList.add('ti', 'ti-pencil', 'me-3', 'cursor-pointer');
                editIcon.setAttribute('title', 'Edit');
                editIcon.addEventListener('click', () => {
                    editincident(incident.incident_type_name, incident.incident_type_id);
                });
                actionsCell.appendChild(editIcon);

                const deleteIcon = document.createElement('i');
                deleteIcon.classList.add('ti', 'ti-trash', 'cursor-pointer');
                deleteIcon.setAttribute('title', 'Delete');
                deleteIcon.addEventListener('click', () => {
                    deleteincident(incident.incident_type_id);
                });
                actionsCell.appendChild(deleteIcon);

                row.appendChild(actionsCell);

                tbody.appendChild(row);
            });
        },
        onError: (error) => {
            preloader.innerHTML = `<span>Error fetching data. <br>Status: ${error}.</span>`;
        }
    });
}

function editincident(incidentName, incidentId) {
    document.getElementById('incident_name').value = incidentName;
    const saveButton = document.getElementById('edit-incident');
    saveButton.setAttribute('data-incident-id', incidentId);
    const modalElement = document.getElementById('editincident');
    const bootstrapModal = new bootstrap.Modal(modalElement);
    bootstrapModal.show();
}

document.getElementById('edit-incident').addEventListener('click', function () {
    const incidentId = this.getAttribute('data-incident-id');
    const incidentName = document.getElementById('incident_name').value;

    const activeStatus = true;
    console.log("Incident ID:", incidentId);
    console.log("Incident Name:", incidentName);

    apiRequest({
        url: `/corporate/editIncidentType/${incidentId}`,
        method: 'POST',
        data: {
            incident_type_name: incidentName,
        },
        onSuccess: (response) => {
            console.log("toastresponse", response);
            const toastType = response.result === true ? 'success' : 'error';
            const toastMessage = response.data || "Operation completed.";
            showToast(toastType, "Edit Incident", toastMessage);
            fetchincidentTypes();
            const modalElement = document.getElementById('editincident');
            const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
            bootstrapModal.hide();                    
        },
    onError: (error) => {
        const toastMessage = error?.data || 'The incident type name has already been taken.';
        showToast('error', "Edit Incident", toastMessage);
    }
    });
});
function deleteincident(incidentId) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this incident types? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            apiRequest({
                url: `/corporate/deleteIncidentType/${incidentId}`,
                method: 'DELETE',
                onSuccess: (responseData) => {
                    console.log("checking the delete", responseData);
                    if (responseData.result) {
                        const toastType = responseData.result === true ? 'success' : 'error';
                        const toastMessage = responseData.data || "Operation completed.";
                        showToast(toastType, "Delete Incident completed", toastMessage);
                        fetchincidentTypes();
                    } else {
                        showToast('error', responseData.message || 'Failed to delete the incident.');
                    }
                },
                onError: (error) => {
                    showToast('error', error || 'Something went wrong. Please try again later.');
                }
            });
        }
    });
}






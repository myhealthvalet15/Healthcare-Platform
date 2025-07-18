    const preloader = document.getElementById('preloader');
    const table = document.getElementById('incidenttable');
    const tbody = document.getElementById('incidentbody');

   if (!table || !tbody) {
    console.warn('Table or tbody not found');
    // Removed: return;
} else {
    // Place your code here that uses table and tbody
}

    const addincidentButton = document.getElementById('add-new-incident');
    console.log("checking 1",addincidentButton);
    const incidentNameInput = document.getElementById('incident-name');
    console.log("checking 1",incidentNameInput);

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
        addNewincident(incidentName, activeStatus);
    });
    
    
    
    function addNewincident(incidentName, activeStatus) {
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
            data: { incident_type_name: incidentName},
            onSuccess: (responseData) => {
                const modalElement = document.getElementById('addincident');
                const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
                if (responseData.result === 'success') {
                    showToast(responseData.result, responseData.message);
                    fetchFactors();
                    bootstrapModal.hide();
                    incidentNameInput.value = '';
                    addincidentButton.disabled = false;
                    addincidentButton.innerHTML = 'Save Changes';
                } else {
                    showToast(responseData.result, responseData.message || 'Error occurred while adding factor');
                    bootstrapModal.hide();
                    addincidentButton.disabled = false;
                    addincidentButton.innerHTML = 'Save Changes';
                }
            },
            onError: (error) => {
                showToast('error', error);
                addincidentButton.disabled = false;
                addincidentButton.innerHTML = 'Save Changes';
            },
            onComplete: () => {
                addincidentButton.disabled = false;
                addincidentButton.innerHTML = 'Save Changes';
            }
        });
    }

    function fetchFactors() {
        tbody.innerHTML = '';
        preloader.style.display = 'block';
        apiRequest({
            url: "/hra/fetch-factors",
            method: 'GET',
            onSuccess: (data) => {
                preloader.style.display = 'none';
                if (!data || !Array.isArray(data) || data.length === 0) {
                    const noDataMessage = document.createElement('tr');
                    noDataMessage.innerHTML = `
                    <td colspan="3" class="text-center">No factors available.</td>`;
                    tbody.appendChild(noDataMessage);
                    table.style.display = 'table';
                    return;
                }
                table.style.display = 'table';
                data.forEach(factor => {
                    const row = document.createElement('tr');

                    const factorNameCell = document.createElement('td');
                    factorNameCell.textContent = factor.factor_name;
                    row.appendChild(factorNameCell);

                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge');
                    badge.classList.add(factor.active_status ? 'bg-label-primary' : 'bg-label-secondary');
                    badge.textContent = factor.active_status ? 'Active' : 'Inactive';
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    const actionsCell = document.createElement('td');

                    const editIcon = document.createElement('i');
                    editIcon.classList.add('ti', 'ti-pencil', 'me-3', 'cursor-pointer');
                    editIcon.setAttribute('title', 'Edit');
                    editIcon.addEventListener('click', () => {
                        editFactor(factor.factor_name, factor.active_status, factor.factor_id);
                    });
                    actionsCell.appendChild(editIcon);

                    const deleteIcon = document.createElement('i');
                    deleteIcon.classList.add('ti', 'ti-trash', 'cursor-pointer');
                    deleteIcon.setAttribute('title', 'Delete');
                    deleteIcon.addEventListener('click', () => {
                        deleteFactor(factor.factor_id);
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

    function editFactor(factorName, status, factorId) {
        document.getElementById('factor_name').value = factorName;
        const statusSwitch = document.getElementById('status_switch_edit');
        const statusLabel = document.getElementById('status-label-edit');
        const statusSwitchContainer = statusSwitch.closest('.switch');


        if (status === 'Active' || status === 1) {
            statusSwitch.checked = true;
            statusLabel.textContent = 'Active';
            statusSwitch.classList.add('is-valid');
            statusSwitch.classList.remove('is-invalid');
        } else {
            statusSwitch.checked = false;
            statusLabel.textContent = 'Inactive';
            statusSwitch.classList.add('is-invalid');
            statusSwitch.classList.remove('is-valid');
        }

        statusSwitch.addEventListener('change', function () {
            if (statusSwitch.checked) {
                statusLabel.textContent = 'Active';
                statusSwitch.classList.add('is-valid');
                statusSwitch.classList.remove('is-invalid');
            } else {
                statusLabel.textContent = 'Inactive';
                statusSwitch.classList.add('is-invalid');
                statusSwitch.classList.remove('is-valid');
            }
        });

        document.getElementById('edit-factor').setAttribute('data-factor-id', factorId);
        const modalElement = document.getElementById('editFactor');
        const bootstrapModal = new bootstrap.Modal(modalElement);
        bootstrapModal.show();

        const editFactorButton = document.getElementById('edit-factor');
        editFactorButton.replaceWith(editFactorButton.cloneNode(true));
        const newEditFactorButton = document.getElementById('edit-factor');

        newEditFactorButton.addEventListener('click', function () {
            const factorId = this.getAttribute('data-factor-id');
            const factorName = document.getElementById('factor_name').value;
            const statusSwitch = document.getElementById('status_switch_edit');
            const activeStatus = statusSwitch.checked ? 1 : 0;

            apiRequest({
                url: `/hra/edit-factor/${factorId}`,
                method: 'PUT',
                data: { factor_name: factorName, active_status: activeStatus },
                onSuccess: (response) => {
                    showToast(response.result, response.message);
                    fetchFactors();
                    const modalElement = document.getElementById('editFactor');
                    const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
                    bootstrapModal.hide();
                },
                onError: (error) => {
                    showToast('error', error);
                }
            });


        });

    }


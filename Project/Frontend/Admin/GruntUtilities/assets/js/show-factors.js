document.addEventListener('DOMContentLoaded', function () {
    const preloader = document.getElementById('preloader');
    const table = document.getElementById('factors-table');
    const tbody = document.getElementById('factors-body');

    const statusSwitch = document.getElementById('status-switch');
    const statusLabel = document.getElementById('status-label');
    let activeStatus = '';
    activeStatus = 'Active';
    statusSwitch.addEventListener('change', function () {
        if (statusSwitch.checked) {
            statusLabel.textContent = 'Active';
            statusSwitch.classList.add('is-valid');
            statusSwitch.classList.remove('is-invalid');
            activeStatus = 'Active';
        } else {
            statusLabel.textContent = 'Inactive';
            statusSwitch.classList.add('is-invalid');
            statusSwitch.classList.remove('is-valid');
            activeStatus = 'Inactive';
        }
    });

    if (!table || !tbody) {
        // console.error('Table or tbody not found');
        return;
    }

    const addFactorButton = document.getElementById('add-new-factor');
    const factorNameInput = document.getElementById('factor-name');
    factorNameInput.addEventListener('input', () => {
        let errorContainer = factorNameInput.parentElement.querySelector('.fv-plugins-message-container');
        if (errorContainer) {
            errorContainer.remove();
            factorNameInput.classList.remove('is-invalid');
        }
    });

    addFactorButton.addEventListener('click', () => {
        const factorName = factorNameInput.value.trim();
        let errorContainer = factorNameInput.parentElement.querySelector('.fv-plugins-message-container');
        if (errorContainer) {
            errorContainer.remove();
        }
        addFactorButton.disabled = true;
        addFactorButton.innerHTML =
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
         &nbspSaving...`;
        addNewFactor(factorName, activeStatus);
    });

    function addNewFactor(factorName, activeStatus) {
        if (!factorName || factorName.trim().length === 0) {
            addFactorButton.disabled = false;
            addFactorButton.innerHTML = 'Save Changes';
            factorNameInput.classList.add('is-invalid');
            const errorMessage = document.createElement('div');
            errorMessage.classList.add('fv-plugins-message-container', 'fv-plugins-message-container--enabled', 'invalid-feedback');
            errorMessage.innerHTML = '<div data-field="formValidationUsername" data-validator="notEmpty">The name is required</div>';
            factorNameInput.parentElement.appendChild(errorMessage);
            showToast('error', 'Factor name is required');
            return false;
        }

        apiRequest({
            url: "/hra/add-new-factor",
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            data: { factor_name: factorName, active_status: activeStatus === 'Active' ? 1 : 0 },
            onSuccess: (responseData) => {
                const modalElement = document.getElementById('addFactor');
                const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
                if (responseData.result === 'success') {
                    showToast(responseData.result, responseData.message);
                    fetchFactors();
                    bootstrapModal.hide();
                    factorNameInput.value = '';
                    addFactorButton.disabled = false;
                    addFactorButton.innerHTML = 'Save Changes';
                } else {
                    showToast(responseData.result, responseData.message || 'Error occurred while adding factor');
                    bootstrapModal.hide();
                    addFactorButton.disabled = false;
                    addFactorButton.innerHTML = 'Save Changes';
                }
            },
            onError: (error) => {
                showToast('error', error);
                addFactorButton.disabled = false;
                addFactorButton.innerHTML = 'Save Changes';
            },
            onComplete: () => {
                addFactorButton.disabled = false;
                addFactorButton.innerHTML = 'Save Changes';
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

    function deleteFactor(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to delete this factor? This action cannot be undone.',
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
                    url: `/hra/delete-factor/${id}`,
                    method: 'DELETE',
                    onSuccess: (responseData) => {
                        if (responseData.result === 'success') {
                            showToast('success', responseData.message);
                            fetchFactors();
                        } else {
                            showToast('error', responseData.message || 'Failed to delete the factor.');
                        }
                    },
                    onError: (error) => {
                        showToast('error', error || 'Something went wrong. Please try again later.');
                    }
                });
            }
        });
    }
    fetchFactors();
});         
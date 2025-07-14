document.addEventListener('DOMContentLoaded', function () {
    const preloader = document.getElementById('preloader');
    const table = document.getElementById('templates-table');
    const tbody = document.getElementById('templates-body');

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

    const addTemplateButton = document.getElementById('add-new-template');
    const templateNameInput = document.getElementById('template-name');

    // Remove error message when user starts typing
    templateNameInput.addEventListener('input', () => {
        let errorContainer = templateNameInput.parentElement.querySelector('.fv-plugins-message-container');
        if (errorContainer) {
            errorContainer.remove();
            templateNameInput.classList.remove('is-invalid');
        }
    });

    addTemplateButton.addEventListener('click', () => {
        const templateName = templateNameInput.value.trim();
        let errorContainer = templateNameInput.parentElement.querySelector('.fv-plugins-message-container');
        if (errorContainer) {
            errorContainer.remove();
        }
        addTemplateButton.disabled = true;
        addTemplateButton.innerHTML =
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
         &nbspSaving...`;
        addNewTemplate(templateName, activeStatus);
    });

    function addNewTemplate(templateName, activeStatus) {
        if (!templateName || templateName.trim().length === 0) {
            addTemplateButton.disabled = false;
            addTemplateButton.innerHTML = 'Save Changes';
            templateNameInput.classList.add('is-invalid');
            const errorMessage = document.createElement('div');
            errorMessage.classList.add('fv-plugins-message-container', 'fv-plugins-message-container--enabled', 'invalid-feedback');
            errorMessage.innerHTML = '<div data-field="formValidationUsername" data-validator="notEmpty">The name is required</div>';
            templateNameInput.parentElement.appendChild(errorMessage);
            showToast('error', 'Template name is required');
            return false;
        }

        apiRequest({
            url: "/hra/add-new-template",
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            data: { template_name: templateName, active_status: activeStatus === 'Active' ? 1 : 0 },
            onSuccess: (responseData) => {
                if (responseData.result === 'success') {
                    showToast(responseData.result, responseData.message);
                    fetchTemplates();
                    const modalElement = document.getElementById('addTemplate');
                    const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
                    bootstrapModal.hide();
                    templateNameInput.value = '';
                    addTemplateButton.disabled = false;
                    addTemplateButton.innerHTML = 'Save Changes';
                } else {
                    showToast(responseData.result, responseData.message || 'Error occurred while adding template');
                    bootstrapModal.hide();
                    addTemplateButton.disabled = false;
                    addTemplateButton.innerHTML = 'Save Changes';
                }
            },
            onError: (error) => {
                showToast('error', error);
                addTemplateButton.disabled = false;
                addTemplateButton.innerHTML = 'Save Changes';
            },
            onComplete: () => {
                addTemplateButton.disabled = false;
                addTemplateButton.innerHTML = 'Save Changes';
            }
        });
    }

    function fetchTemplates() {
        tbody.innerHTML = '';
        preloader.style.display = 'block';
        apiRequest({
            url: "/hra/fetch-templates",
            method: 'GET',
            onSuccess: (data) => {
                preloader.style.display = 'none';
                if (!data || !Array.isArray(data) || data.length === 0) {
                    const noDataMessage = document.createElement('tr');
                    noDataMessage.innerHTML = `
                    <td colspan="3" class="text-center">No templates available.</td>`;
                    tbody.appendChild(noDataMessage);
                    table.style.display = 'table';
                    return;
                }
                table.style.display = 'table';
                data.forEach(template => {
                    const row = document.createElement('tr');

                    const templateNameCell = document.createElement('td');
                    templateNameCell.textContent = template.template_name;
                    row.appendChild(templateNameCell);

                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    badge.classList.add('badge');
                    badge.classList.add(template.active_status ? 'bg-label-primary' : 'bg-label-secondary');
                    badge.textContent = template.active_status ? 'Active' : 'Inactive';
                    statusCell.appendChild(badge);
                    row.appendChild(statusCell);

                    const actionsCell = document.createElement('td');

                    const editIcon = document.createElement('i');
                    editIcon.classList.add('ti', 'ti-pencil', 'me-3', 'cursor-pointer');
                    editIcon.setAttribute('title', 'Edit');
                    editIcon.addEventListener('click', () => {
                        editTemplate(template.template_name, template.active_status, template.template_id);
                    });
                    actionsCell.appendChild(editIcon);

                    const deleteIcon = document.createElement('i');
                    deleteIcon.classList.add('ti', 'ti-trash', 'me-3', 'cursor-pointer');
                    deleteIcon.setAttribute('title', 'Delete');
                    deleteIcon.addEventListener('click', () => {
                        deleteTemplate(template.template_id);
                    });
                    actionsCell.appendChild(deleteIcon);

                    const factorPriority = document.createElement('td');
                    factorPriority.classList.add('ti', 'fa-pen-to-square', 'cursor-pointer');
                    factorPriority.setAttribute('title', 'Factor Priority');
                    factorPriority.addEventListener('click', () => {
                        window.location.href = '/hra/templates/factor-priority/' + template.template_id;
                    });
                    actionsCell.appendChild(factorPriority);

                    row.appendChild(actionsCell);

                    tbody.appendChild(row);
                });
            },
            onError: (error) => {
                preloader.innerHTML = `<span>Error fetching data. <br>Status: ${error}.</span>`;
            }
        });
    }

    function editTemplate(templateName, status, templateId) {
        document.getElementById('template_name').value = templateName;
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

        document.getElementById('edit-template').setAttribute('data-template-id', templateId);
        const modalElement = document.getElementById('editTemplate');
        const bootstrapModal = new bootstrap.Modal(modalElement);
        bootstrapModal.show();

        const editTemplateButton = document.getElementById('edit-template');
        editTemplateButton.replaceWith(editTemplateButton.cloneNode(true));
        const newEditTemplateButton = document.getElementById('edit-template');

        newEditTemplateButton.addEventListener('click', function () {
            const templateId = this.getAttribute('data-template-id');
            const templateName = document.getElementById('template_name').value;
            const statusSwitch = document.getElementById('status_switch_edit');
            const activeStatus = statusSwitch.checked ? 1 : 0;

            apiRequest({
                url: `/hra/edit-template/${templateId}`,
                method: 'PUT',
                data: { template_name: templateName, active_status: activeStatus },
                onSuccess: (response) => {
                    showToast(response.result, response.message);
                    fetchTemplates();
                    const modalElement = document.getElementById('editTemplate');
                    const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
                    bootstrapModal.hide();
                },
                onError: (error) => {
                    showToast('error', error);
                }
            });


        });

    }

    function deleteTemplate(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to delete this template? This action cannot be undone.',
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
                    url: `/hra/delete-template/${id}`,
                    method: 'DELETE',
                    onSuccess: (responseData) => {
                        if (responseData.result === 'success') {
                            showToast('success', responseData.message);
                            fetchTemplates();
                        } else {
                            showToast('error', responseData.message || 'Failed to delete the template.');
                        }
                    },
                    onError: (error) => {
                        showToast('error', error || 'Something went wrong. Please try again later.');
                    }
                });
            }
        });
    }
    fetchTemplates();
});

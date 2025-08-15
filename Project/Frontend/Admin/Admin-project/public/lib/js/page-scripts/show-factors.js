document.addEventListener('DOMContentLoaded', function () {
  const preloader = document.getElementById('preloader');
  const table = document.getElementById('factors-table');
  const tbody = document.getElementById('factors-body');
  const statusSwitch = document.getElementById('status-switch');
  const statusLabel = document.getElementById('status-label');
  let activeStatus = 'Active';
  if (statusSwitch && statusLabel) {
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
  }
  if (!table || !tbody) {
    console.error('Required table elements not found');
    return;
  }
  const addFactorButton = document.getElementById('add-new-factor');
  const factorNameInput = document.getElementById('factor-name');
  if (factorNameInput) {
    factorNameInput.addEventListener('input', () => {
      const errorContainer = factorNameInput.parentElement.querySelector('.fv-plugins-message-container');
      if (errorContainer) {
        errorContainer.remove();
        factorNameInput.classList.remove('is-invalid');
      }
    });
  }
  if (addFactorButton) {
    addFactorButton.addEventListener('click', () => {
      const factorName = factorNameInput ? factorNameInput.value.trim() : '';
      const errorContainer = factorNameInput.parentElement.querySelector('.fv-plugins-message-container');
      if (errorContainer) {
        errorContainer.remove();
      }
      addFactorButton.disabled = true;
      while (addFactorButton.firstChild) {
        addFactorButton.removeChild(addFactorButton.firstChild);
      }
      const spinner = document.createElement('span');
      spinner.className = 'spinner-border spinner-border-sm';
      spinner.setAttribute('role', 'status');
      spinner.setAttribute('aria-hidden', 'true');
      addFactorButton.appendChild(spinner);
      addFactorButton.appendChild(document.createTextNode(' Saving...'));
      addNewFactor(factorName, activeStatus);
    });
  }
  function addNewFactor(factorName, activeStatus) {
    if (!factorName || factorName.trim().length === 0) {
      addFactorButton.disabled = false;
      addFactorButton.textContent = 'Save Changes';
      factorNameInput.classList.add('is-invalid');
      const errorMessage = document.createElement('div');
      errorMessage.className = 'fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback';
      const errorDiv = document.createElement('div');
      errorDiv.setAttribute('data-field', 'formValidationUsername');
      errorDiv.setAttribute('data-validator', 'notEmpty');
      errorDiv.textContent = 'The name is required';
      errorMessage.appendChild(errorDiv);
      factorNameInput.parentElement.appendChild(errorMessage);
      showToast('error', 'Factor name is required');
      return false;
    }
    apiRequest({
      url: '/hra/add-new-factor',
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      data: {
        factor_name: factorName,
        active_status: activeStatus === 'Active' ? 1 : 0
      },
      onSuccess: responseData => {
        const modalElement = document.getElementById('addFactor');
        let bootstrapModal = null;
        if (typeof bootstrap !== 'undefined') {
          bootstrapModal = bootstrap.Modal.getInstance(modalElement);
        }
        if (responseData.result === 'success') {
          showToast(responseData.result, responseData.message);
          fetchFactors();
          if (bootstrapModal) {
            bootstrapModal.hide();
          }
          if (factorNameInput) {
            factorNameInput.value = '';
          }
        } else {
          showToast(responseData.result, responseData.message || 'Error occurred while adding factor');
          if (bootstrapModal) {
            bootstrapModal.hide();
          }
        }
      },
      onError: error => {
        showToast('error', error);
      },
      onComplete: () => {
        addFactorButton.disabled = false;
        addFactorButton.textContent = 'Save Changes';
      }
    });
  }
  function fetchFactors() {
    while (tbody.firstChild) {
      tbody.removeChild(tbody.firstChild);
    }
    preloader.style.display = 'block';
    apiRequest({
      url: '/hra/fetch-factors',
      method: 'GET',
      onSuccess: data => {
        preloader.style.display = 'none';
        if (!data || !Array.isArray(data) || data.length === 0) {
          const noDataMessage = document.createElement('tr');
          const td = document.createElement('td');
          td.setAttribute('colspan', '3');
          td.className = 'text-center';
          td.textContent = 'No factors available.';
          noDataMessage.appendChild(td);
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
          badge.className = 'badge';
          badge.classList.add(factor.active_status ? 'bg-label-primary' : 'bg-label-secondary');
          badge.textContent = factor.active_status ? 'Active' : 'Inactive';
          statusCell.appendChild(badge);
          row.appendChild(statusCell);
          const actionsCell = document.createElement('td');
          const editIcon = document.createElement('i');
          editIcon.className = 'ti ti-pencil me-3 cursor-pointer';
          editIcon.setAttribute('title', 'Edit');
          editIcon.addEventListener('click', () => {
            editFactor(factor.factor_name, factor.active_status, factor.factor_id);
          });
          actionsCell.appendChild(editIcon);
          const deleteIcon = document.createElement('i');
          deleteIcon.className = 'ti ti-trash cursor-pointer';
          deleteIcon.setAttribute('title', 'Delete');
          deleteIcon.addEventListener('click', () => {
            deleteFactor(factor.factor_id);
          });
          actionsCell.appendChild(deleteIcon);
          row.appendChild(actionsCell);
          tbody.appendChild(row);
        });
      },
      onError: error => {
        while (preloader.firstChild) {
          preloader.removeChild(preloader.firstChild);
        }
        const errorSpan = document.createElement('span');
        errorSpan.textContent = 'Error fetching data. ';
        const br = document.createElement('br');
        const statusSpan = document.createElement('span');
        statusSpan.textContent = `Status: ${error}.`;
        preloader.appendChild(errorSpan);
        preloader.appendChild(br);
        preloader.appendChild(statusSpan);
      }
    });
  }
  function editFactor(factorName, status, factorId) {
    const factorNameEdit = document.getElementById('factor_name');
    const statusSwitchEdit = document.getElementById('status_switch_edit');
    const statusLabelEdit = document.getElementById('status-label-edit');
    if (factorNameEdit) {
      factorNameEdit.value = factorName;
    }
    if (statusSwitchEdit && statusLabelEdit) {
      if (status === 'Active' || status === 1) {
        statusSwitchEdit.checked = true;
        statusLabelEdit.textContent = 'Active';
        statusSwitchEdit.classList.add('is-valid');
        statusSwitchEdit.classList.remove('is-invalid');
      } else {
        statusSwitchEdit.checked = false;
        statusLabelEdit.textContent = 'Inactive';
        statusSwitchEdit.classList.add('is-invalid');
        statusSwitchEdit.classList.remove('is-valid');
      }
      const newStatusSwitch = statusSwitchEdit.cloneNode(true);
      statusSwitchEdit.parentNode.replaceChild(newStatusSwitch, statusSwitchEdit);
      newStatusSwitch.addEventListener('change', function () {
        if (newStatusSwitch.checked) {
          statusLabelEdit.textContent = 'Active';
          newStatusSwitch.classList.add('is-valid');
          newStatusSwitch.classList.remove('is-invalid');
        } else {
          statusLabelEdit.textContent = 'Inactive';
          newStatusSwitch.classList.add('is-invalid');
          newStatusSwitch.classList.remove('is-valid');
        }
      });
    }
    const editFactorButton = document.getElementById('edit-factor');
    if (editFactorButton) {
      editFactorButton.setAttribute('data-factor-id', factorId);
    }
    const modalElement = document.getElementById('editFactor');
    if (typeof bootstrap !== 'undefined' && modalElement) {
      const bootstrapModal = new bootstrap.Modal(modalElement);
      bootstrapModal.show();
    }
    if (editFactorButton) {
      const newEditFactorButton = editFactorButton.cloneNode(true);
      editFactorButton.parentNode.replaceChild(newEditFactorButton, editFactorButton);
      newEditFactorButton.addEventListener('click', function () {
        const factorId = this.getAttribute('data-factor-id');
        const factorName = document.getElementById('factor_name').value;
        const statusSwitch = document.getElementById('status_switch_edit');
        const activeStatus = statusSwitch ? (statusSwitch.checked ? 1 : 0) : 0;
        apiRequest({
          url: `/hra/edit-factor/${factorId}`,
          method: 'PUT',
          data: {
            factor_name: factorName,
            active_status: activeStatus
          },
          onSuccess: response => {
            showToast(response.result, response.message);
            fetchFactors();
            const modalElement = document.getElementById('editFactor');
            if (typeof bootstrap !== 'undefined' && modalElement) {
              const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
              if (bootstrapModal) {
                bootstrapModal.hide();
              }
            }
          },
          onError: error => {
            showToast('error', error);
          }
        });
      });
    }
  }
  function deleteFactor(id) {
    if (typeof Swal === 'undefined') {
      const confirmed = confirm('Are you sure you want to delete this factor? This action cannot be undone.');
      if (!confirmed) {
        return;
      }
      performDelete(id);
    } else {
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
      }).then(result => {
        if (result.isConfirmed) {
          performDelete(id);
        }
      });
    }
  }
  function performDelete(id) {
    apiRequest({
      url: `/hra/delete-factor/${id}`,
      method: 'DELETE',
      onSuccess: responseData => {
        if (responseData.result === 'success') {
          showToast('success', responseData.message);
          fetchFactors();
        } else {
          showToast('error', responseData.message || 'Failed to delete the factor.');
        }
      },
      onError: error => {
        showToast('error', error || 'Something went wrong. Please try again later.');
      }
    });
  }
  fetchFactors();
});
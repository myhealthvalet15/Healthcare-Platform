@extends('layouts/layoutMaster')
@section('title', 'Medical Condition')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection
@section('content')
<div class="card">
  <div class="d-flex justify-content-between align-items-center card-header">
    <button class="btn btn-secondary add-new btn-primary waves-effect waves-light" tabindex="0"
      aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
      <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New</span></span>
    </button>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Medical
          Condition</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form id="addMedicalConditionForm">
          <div class="row">
            <div class="col mb-4">
              <label for="condition_name" class="form-label">Medical Condition
                name <span class="text-danger">*</span></label>
              <input type="text" id="condition_name" name="condition_name" class="form-control"
                placeholder="Enter Medical Condition name" required>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="row g-4">
            <div class="col mb-0">
              <label for="status-switch" class="form-label">Status</label>
              <div class="demo-vertical-spacing">
                <label class="switch">
                  <input type="checkbox" class="switch-input" id="status-switch" checked>
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                  <span class="switch-label" id="status-label">Active</span>
                </label>
              </div>
            </div>
          </div>
          <br><br>
          <button type="submit" class="btn btn-primary" id="add-medicalcondition">
            <span class="btn-text">Save Changes</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
          <button type="button" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
      </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Edit Medical
          Condition</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form id="editMedicalConditionForm">
          <div class="row">
            <div class="col mb-4">
              <label for="condition_name_edit" class="form-label">Medical
                Condition <span class="text-danger">*</span></label>
              <input type="text" id="condition_name_edit" name="condition_name_edit" class="form-control"
                placeholder="Edit Name" required>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="row g-4">
            <div class="col mb-0">
              <label for="status_switch_edit" class="form-label">Status</label>
              <div class="demo-vertical-spacing">
                <label class="switch">
                  <input type="checkbox" class="switch-input" id="status_switch_edit">
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                  <span class="switch-label" id="status-label-edit">Active</span>
                </label>
              </div>
            </div>
          </div>
          <br><br>
          <button type="submit" class="btn btn-primary" id="edit-medicalcondition">
            <span class="btn-text">Save Changes</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
          <button type="button" class="btn btn-label-danger waves-effect" data-bs-dismiss="offcanvas">Cancel</button>
          <input type="hidden" id="edit_condition_id">
        </form>
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive pt-0">
    <table class="datatables-basic table">
      <thead>
        <tr class="advance-search mt-3">
          <th colspan="3" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted" id="employeeTypeLabel">List of Medical
                Condition</span>
            </div>
          </th>
        </tr>
        <tr>
          <th>Medical Condition</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<script>
  'use strict';
  class MedicalConditionManager {
    constructor() {
      this.dt_basic = null;
      this.addOffcanvas = null;
      this.editOffcanvas = null;
      this.currentEditId = null;
      this.init();
    }
    init() {
      this.initializeElements();
      this.initializeDataTable();
      this.bindEvents();
    }
    initializeElements() {
      const addOffcanvasEl = document.getElementById('offcanvasAddUser');
      const editOffcanvasEl = document.getElementById('offcanvasEditUser');
      if (addOffcanvasEl) {
        this.addOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(addOffcanvasEl);
      }
      if (editOffcanvasEl) {
        this.editOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(editOffcanvasEl);
      }
      this.initializeStatusSwitches();
    }
    initializeStatusSwitches() {
      const addStatusSwitch = document.getElementById('status-switch');
      const addStatusLabel = document.getElementById('status-label');
      if (addStatusSwitch && addStatusLabel) {
        addStatusSwitch.addEventListener('change', () => {
          this.updateStatusLabel(addStatusSwitch, addStatusLabel);
        });
        this.updateStatusLabel(addStatusSwitch, addStatusLabel);
      }
      const editStatusSwitch = document.getElementById('status_switch_edit');
      const editStatusLabel = document.getElementById('status-label-edit');
      if (editStatusSwitch && editStatusLabel) {
        editStatusSwitch.addEventListener('change', () => {
          this.updateStatusLabel(editStatusSwitch, editStatusLabel);
        });
      }
    }
    updateStatusLabel(switchElement, labelElement) {
      if (switchElement.checked) {
        labelElement.textContent = 'Active';
        switchElement.classList.add('is-valid');
        switchElement.classList.remove('is-invalid');
      } else {
        labelElement.textContent = 'Inactive';
        switchElement.classList.add('is-invalid');
        switchElement.classList.remove('is-valid');
      }
    }
    bindEvents() {
      const addForm = document.getElementById('addMedicalConditionForm');
      if (addForm) {
        addForm.addEventListener('submit', (e) => {
          e.preventDefault();
          this.handleAddSubmission();
        });
      }
      const editForm = document.getElementById('editMedicalConditionForm');
      if (editForm) {
        editForm.addEventListener('submit', (e) => {
          e.preventDefault();
          this.handleEditSubmission();
        });
      }
      document.getElementById('offcanvasAddUser')?.addEventListener('hidden.bs.offcanvas', () => {
        this.resetAddForm();
      });
      document.getElementById('offcanvasEditUser')?.addEventListener('hidden.bs.offcanvas', () => {
        this.resetEditForm();
      });
    }
    initializeDataTable() {
      const dt_basic_table = document.querySelector('.datatables-basic');
      if (!dt_basic_table) return;
      this.dt_basic = $(dt_basic_table).DataTable({
        ajax: {
          url: "/others/medical-condition/fetch-medicalcondition",
          dataSrc: (json) => {
            console.log('Fetched data:', json);
            return json;
          }
        },
        columns: [
          {
            data: 'condition_name',
            title: 'Medical Condition'
          },
          {
            data: 'status',
            title: 'Status',
            render: (data) => {
              const statusText = data === 1 ? 'Active' : 'Inactive';
              const statusClass = data === 1 ? 'bg-success' : 'bg-danger';
              return `<span class="badge ${statusClass}">${statusText}</span>`;
            }
          },
          {
            data: null,
            title: 'Actions',
            orderable: false,
            render: (data, type, row) => {
              return `
              <button class="btn btn-sm btn-warning edit-record" 
                      data-id="${this.escapeHtml(row.condition_id)}" 
                      data-medical-condition="${this.escapeHtml(row.condition_name)}" 
                      data-status="${row.status}">
                <i class="ti ti-edit"></i> Edit
              </button>
              <button class="btn btn-sm btn-danger delete-record ms-1" 
                      data-id="${this.escapeHtml(row.condition_id)}">
                <i class="ti ti-trash"></i> Delete
              </button>
            `;
            }
          }
        ],
        order: [[0, 'asc']],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"B>>' +
          '<"col-sm-12"f>' +
          't' +
          '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        language: {
          paginate: {
            next: '<i class="ti ti-chevron-right ti-sm"></i>',
            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
          }
        },
        buttons: [{
          extend: 'excelHtml5',
          text: '<i class="fa-solid fa-file-excel" style="font-size: 30px;"></i>',
          titleAttr: 'Export to Excel',
          filename: 'medical_condition_Export',
          className: 'btn-link ms-3'
        }],
        responsive: true,
        initComplete: () => {
          this.styleDataTable();
          this.bindDataTableEvents();
        }
      });
    }
    styleDataTable() {
      const count = this.dt_basic.data().count();
      const label = document.getElementById('employeeTypeLabel');
      if (label) {
        label.textContent = `List of Medical Condition (${count})`;
      }
      const headerContainer = document.querySelector('.d-flex.justify-content-between.align-items-center.card-header');
      if (headerContainer) {
        const filterContainer = document.querySelector('.dataTables_filter');
        if (filterContainer) {
          const originalWrapper = filterContainer.closest('.dataTables_wrapper');
          if (originalWrapper) {
            const searchWrapper = originalWrapper.querySelector('.dataTables_filter').parentElement;
            if (searchWrapper) {
              searchWrapper.style.display = 'none';
            }
          }
          const originalInput = filterContainer.querySelector('input');
          if (originalInput) {
            const headerSearchContainer = document.createElement('div');
            headerSearchContainer.classList.add('d-flex', 'align-items-center');
            const searchLabel = document.createElement('label');
            searchLabel.textContent = 'Search: ';
            searchLabel.classList.add('me-2', 'mb-0');
            const newSearchInput = document.createElement('input');
            newSearchInput.type = 'text';
            newSearchInput.classList.add('form-control');
            newSearchInput.style.width = '250px';
            newSearchInput.placeholder = originalInput.placeholder;
            newSearchInput.addEventListener('keyup', function () {
              if (window.medicalConditionManager && window.medicalConditionManager.dt_basic) {
                window.medicalConditionManager.dt_basic.search(this.value).draw();
              }
            });
            headerSearchContainer.appendChild(searchLabel);
            headerSearchContainer.appendChild(newSearchInput);
            headerContainer.insertBefore(headerSearchContainer, headerContainer.firstChild);
          }
        }
        const excelBtn = document.querySelector('.dt-buttons .buttons-excel');
        if (excelBtn) {
          let rightContainer = headerContainer.querySelector('.buttons-right');
          if (!rightContainer) {
            rightContainer = document.createElement('div');
            rightContainer.classList.add('d-flex', 'align-items-center', 'gap-2', 'buttons-right');
            const addButton = headerContainer.querySelector('.add-new');
            if (addButton) {
              headerContainer.insertBefore(rightContainer, addButton);
              rightContainer.appendChild(addButton);
            } else {
              headerContainer.appendChild(rightContainer);
            }
          }
          excelBtn.classList.remove('btn-secondary');
          excelBtn.classList.add('btn-link');
          const span = excelBtn.querySelector('span');
          if (span) {
            span.innerHTML = '<i class="fa-sharp fa-solid fa-file-excel" style="font-size:25px; color: #28a745;"></i>';
          }
          const addButton = rightContainer.querySelector('.add-new');
          if (addButton) {
            rightContainer.insertBefore(excelBtn, addButton);
          } else {
            rightContainer.appendChild(excelBtn);
          }
        }
      }
      const selectElement = document.querySelector('.dataTables_length select');
      const targetCell = document.querySelector('.advance-search th .d-flex');
      if (selectElement && targetCell) {
        targetCell.appendChild(selectElement);
        selectElement.classList.add('ms-3');
        selectElement.style.width = '65px';
        selectElement.style.backgroundColor = '#fff';
        const lengthLabel = document.querySelector('.dataTables_length label');
        if (lengthLabel) {
          lengthLabel.remove();
        }
      }
      window.medicalConditionManager = this;
    }
    bindDataTableEvents() {
      $(this.dt_basic.table().body()).on('click', '.edit-record', (e) => {
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        const conditionName = btn.dataset.medicalCondition;
        const status = parseInt(btn.dataset.status);
        this.openEditModal(id, conditionName, status);
      });
      $(this.dt_basic.table().body()).on('click', '.delete-record', (e) => {
        const btn = e.currentTarget;
        const id = btn.dataset.id;
        this.confirmDelete(id);
      });
    }
    handleAddSubmission() {
      const form = document.getElementById('addMedicalConditionForm');
      const conditionName = document.getElementById('condition_name').value.trim();
      const statusSwitch = document.getElementById('status-switch');
      const status = statusSwitch.checked;
      this.clearFormErrors(form);
      if (!conditionName) {
        this.showFieldError('condition_name', 'Medical condition name is required');
        return;
      }
      this.setButtonLoading('add-medicalcondition', true);
      apiRequest({
        url: "/others/medical-condition/add-medicalcondition",
        method: 'POST',
        data: {
          condition_name: conditionName,
          status: status
        },
        onSuccess: (response) => {
          if (response.result === 'success') {
            this.showToast('success', response.message || 'Medical condition added successfully');
            this.closeAddModal();
            this.refreshDataTable();
          } else if (response.result === 'error' || response.result === 'failed') {
            if (response.message && response.message.toLowerCase().includes('already exists')) {
              this.showToast('error', response.message);
              setTimeout(() => {
                this.closeAddModal();
                this.resetAddForm();
              }, 1500);
            } else {
              this.showToast('error', response.message || 'Error occurred while adding medical condition');
            }
          } else {
            this.showToast('error', 'Unexpected response format');
          }
        },
        onError: (xhr, textStatus, errorThrown) => {
          let errorMessage = 'Something went wrong. Please try again.';
          let shouldCloseModal = false;
          if (xhr && xhr.responseText) {
            try {
              const response = JSON.parse(xhr.responseText);
              if (
                (response.result === 'error' || response.result === 'failed') &&
                response.message &&
                response.message.toLowerCase().includes('already exists')
              ) {
                errorMessage = response.message;
                shouldCloseModal = true;
              } else if (response.message) {
                errorMessage = response.message;
                if (response.message.toLowerCase().includes('already exists')) {
                  shouldCloseModal = true;
                }
              } else if (response.errors) {
                const errors = response.errors;
                const firstError = Object.values(errors)[0];
                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
              }
            } catch (e) {
              if (xhr.status === 409) {
                errorMessage = 'Medical condition with this name already exists.';
                shouldCloseModal = true;
              } else if (xhr.status === 422) {
                errorMessage = 'Validation error occurred.';
              } else if (xhr.responseJSON) {
                errorMessage = xhr.responseJSON.message || errorMessage;
                if (xhr.responseJSON.message && xhr.responseJSON.message.toLowerCase().includes('already exists')) {
                  shouldCloseModal = true;
                }
              }
            }
          }
          this.showToast('error', errorMessage);
          if (shouldCloseModal) {
            setTimeout(() => {
              this.closeAddModal();
              this.resetAddForm();
            }, 1500);
          }
        },
        onComplete: () => {
          this.setButtonLoading('add-medicalcondition', false);
        }
      });
    }
    handleEditSubmission() {
      const form = document.getElementById('editMedicalConditionForm');
      const conditionName = document.getElementById('condition_name_edit').value.trim();
      const statusSwitch = document.getElementById('status_switch_edit');
      const status = statusSwitch.checked ? 1 : 0;
      const conditionId = document.getElementById('edit_condition_id').value;
      this.clearFormErrors(form);
      if (!conditionName) {
        this.showFieldError('condition_name_edit', 'Medical condition name is required');
        return;
      }
      this.setButtonLoading('edit-medicalcondition', true);
      apiRequest({
        url: `/others/medical-condition/edit-medicalcondition/${conditionId}`,
        method: 'PUT',
        data: {
          condition_name: conditionName,
          status: status,
          medicalConditionId: conditionId
        },
        onSuccess: (response) => {
          if (response.result === 'success') {
            this.showToast('success', response.message || 'Medical condition updated successfully');
            this.closeEditModal();
            this.refreshDataTable();
          } else if (response.result === 'error' || response.result === 'failed') {
            if (response.message && response.message.toLowerCase().includes('already exists')) {
              this.showToast('error', response.message);
              setTimeout(() => {
                this.closeEditModal();
                this.resetEditForm();
              }, 1500);
            } else {
              this.showToast('error', response.message || 'Error occurred while updating medical condition');
            }
          } else {
            this.showToast('error', 'Unexpected response format');
          }
        },
        onError: (xhr, textStatus, errorThrown) => {
          let errorMessage = 'Something went wrong. Please try again.';
          let shouldCloseModal = false;
          if (xhr && xhr.responseText) {
            try {
              const response = JSON.parse(xhr.responseText);
              if ((response.result === 'error' || response.result === 'failed') && response.message) {
                errorMessage = response.message;
                if (response.message.toLowerCase().includes('already exists')) {
                  shouldCloseModal = true;
                }
              } else if (response.message) {
                errorMessage = response.message;
                if (response.message.toLowerCase().includes('already exists')) {
                  shouldCloseModal = true;
                }
              } else if (response.errors) {
                const errors = response.errors;
                const firstError = Object.values(errors)[0];
                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
              }
            } catch (e) {
              if (xhr.status === 409) {
                errorMessage = 'Medical condition with this name already exists.';
                shouldCloseModal = true;
              } else if (xhr.status === 422) {
                errorMessage = 'Validation error occurred.';
              } else if (xhr.responseJSON) {
                errorMessage = xhr.responseJSON.message || errorMessage;
                if (xhr.responseJSON.message && xhr.responseJSON.message.toLowerCase().includes('already exists')) {
                  shouldCloseModal = true;
                }
              }
            }
          }
          this.showToast('error', errorMessage);
          if (shouldCloseModal) {
            setTimeout(() => {
              this.closeEditModal();
              this.resetEditForm();
            }, 1500);
          }
        },
        onComplete: () => {
          this.setButtonLoading('edit-medicalcondition', false);
        }
      });
    }
    deleteMedicalCondition(conditionId) {
      apiRequest({
        url: `/others/medical-condition/delete-medicalcondition/${conditionId}`,
        method: 'DELETE',
        onSuccess: (response) => {
          if (response.result === 'success') {
            this.showToast('success', response.message);
            this.refreshDataTable();
          } else if (response.result === 'error' || response.result === 'failed') {
            this.showToast('error', response.message || 'Failed to delete medical condition.');
          } else {
            this.showToast('error', 'Unexpected response format');
          }
        },
        onError: (xhr, textStatus, errorThrown) => {
          let errorMessage = 'Something went wrong. Please try again later.';
          if (xhr && xhr.responseText) {
            try {
              const response = JSON.parse(xhr.responseText);
              if ((response.result === 'error' || response.result === 'failed') && response.message) {
                errorMessage = response.message;
              }
            } catch (e) {
            }
          }
          this.showToast('error', errorMessage);
        }
      });
    }
    handleApiResponse(xhr, isSuccess = false) {
      let errorMessage = 'Something went wrong. Please try again.';
      let response = null;
      if (xhr && xhr.responseText) {
        try {
          response = JSON.parse(xhr.responseText);
        } catch (e) {
          console.error('Failed to parse response JSON:', e);
        }
      } else if (xhr && xhr.responseJSON) {
        response = xhr.responseJSON;
      }
      if (response) {
        if (response.result === 'error' && response.message) {
          errorMessage = response.message;
        } else if (response.message) {
          errorMessage = response.message;
        } else if (response.errors) {
          const errors = response.errors;
          const firstError = Object.values(errors)[0];
          errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
        }
      } else {
        if (xhr && xhr.status === 409) {
          errorMessage = 'Medical condition with this name already exists.';
        } else if (xhr && xhr.status === 422) {
          errorMessage = 'Validation error occurred.';
        }
      }
      return {
        success: isSuccess && response && response.result === 'success',
        message: response && response.result === 'success' ?
          (response.message || 'Operation completed successfully') : errorMessage,
        data: response
      };
    }
    handleValidationErrors(errors) {
      if (typeof errors === 'object') {
        Object.keys(errors).forEach(field => {
          const errorMessages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
          const fieldElement = document.getElementById(field) || document.getElementById(field + '_edit');
          if (fieldElement) {
            this.showFieldError(fieldElement.id, errorMessages[0]);
          }
        });
      }
    }
    openEditModal(id, conditionName, status) {
      document.getElementById('condition_name_edit').value = conditionName;
      document.getElementById('edit_condition_id').value = id;
      const statusSwitch = document.getElementById('status_switch_edit');
      const statusLabel = document.getElementById('status-label-edit');
      statusSwitch.checked = status === 1;
      this.updateStatusLabel(statusSwitch, statusLabel);
      if (this.editOffcanvas) {
        this.editOffcanvas.show();
      }
    }
    confirmDelete(conditionId) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this medical condition? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteMedicalCondition(conditionId);
        }
      });
    }
    closeAddModal() {
      if (this.addOffcanvas) {
        const closeBtn = document.querySelector('#offcanvasAddUser .btn-close');
        if (closeBtn) closeBtn.click();
      }
    }
    closeEditModal() {
      if (this.editOffcanvas) {
        this.editOffcanvas.hide();
      }
    }
    resetAddForm() {
      const form = document.getElementById('addMedicalConditionForm');
      if (form) {
        form.reset();
        this.clearFormErrors(form);
        const statusSwitch = document.getElementById('status-switch');
        const statusLabel = document.getElementById('status-label');
        if (statusSwitch && statusLabel) {
          statusSwitch.checked = true;
          this.updateStatusLabel(statusSwitch, statusLabel);
        }
        this.setButtonLoading('add-medicalcondition', false);
      }
    }
    resetEditForm() {
      const form = document.getElementById('editMedicalConditionForm');
      if (form) {
        form.reset();
        this.clearFormErrors(form);
        document.getElementById('edit_condition_id').value = '';
        this.setButtonLoading('edit-medicalcondition', false);
      }
    }
    setButtonLoading(buttonId, loading) {
      const button = document.getElementById(buttonId);
      if (!button) return;
      const textSpan = button.querySelector('.btn-text');
      const spinner = button.querySelector('.spinner-border');
      if (loading) {
        button.disabled = true;
        if (textSpan) textSpan.textContent = 'Saving...';
        if (spinner) spinner.classList.remove('d-none');
      } else {
        button.disabled = false;
        if (textSpan) textSpan.textContent = 'Save Changes';
        if (spinner) spinner.classList.add('d-none');
      }
    }
    showFieldError(fieldId, message) {
      const field = document.getElementById(fieldId);
      if (!field) return;
      field.classList.add('is-invalid');
      const feedback = field.nextElementSibling;
      if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = message;
      }
    }
    clearFormErrors(form) {
      const invalidFields = form.querySelectorAll('.is-invalid');
      invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
          feedback.textContent = '';
        }
      });
    }
    refreshDataTable() {
      if (this.dt_basic) {
        this.dt_basic.ajax.reload((json) => {
          const count = json.length;
          const label = document.getElementById('employeeTypeLabel');
          if (label) {
            label.textContent = `List of Medical Condition (${count})`;
          }
        }, false);
      }
    }
    escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }
    createElement(tag, attributes = {}) {
      const element = document.createElement(tag);
      Object.entries(attributes).forEach(([key, value]) => {
        if (key === 'class') {
          element.className = value;
        } else {
          element.setAttribute(key, value);
        }
      });
      return element;
    }
    showToast(type, message) {
      if (typeof showToast === 'function') {
        showToast(type, message);
      } else {
        console.log(`${type.toUpperCase()}: ${message}`);
      }
    }
  }
  document.addEventListener('DOMContentLoaded', () => {
    new MedicalConditionManager();
  });
</script>
@endsection
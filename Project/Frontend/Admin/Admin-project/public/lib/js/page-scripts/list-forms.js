'use strict';
let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const preloader = document.getElementById('preloader');
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
    const addFormButton = document.getElementById('add-forms');
    const formNameInput = document.getElementById('form_name');
    addFormButton.addEventListener('click', () => {
      const formName = document.getElementById('form_name').value;
      const stateValue = document.getElementById('statename').value;
      const status = document.getElementById('status-switch').checked ? 'Active' : 'Inactive';
      let errorContainer = formNameInput.parentElement.querySelector('.fv-plugins-message-container');
      if (errorContainer) {
        errorContainer.remove();
      }
      addFormButton.disabled = true;
      addFormButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        &nbsp;Saving...`;
      addNewForm(formName, stateValue, status);
    });

    function addNewForm(form_name, statename, status) {
      form_name = form_name.trim();
      statename = parseInt(document.getElementById('statename').value);
      if (!form_name || form_name.length < 3) {
        showToast('error', 'Form Name must be at least 3 characters long.');
        addFormButton.disabled = false;
        addFormButton.innerHTML = 'Save Changes';
        return;
      }
      if (!statename || statename === '' || statename === '0') {
        showToast('error', 'Please select a valid State.');
        addFormButton.disabled = false;
        addFormButton.innerHTML = 'Save Changes';
        return;
      }
      const isActive = status === 'Active';
      apiRequest({
        url: '/forms/add-forms',
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        data: {
          form_name: form_name,
          statename: statename,
          status: isActive
        },
        onSuccess: responseData => {
          if (responseData.result === 'success') {
            showToast('success', responseData.message);
            document.getElementById('form_name').value = '';
            document.getElementById('statename').value = '';
            document.getElementById('status-switch').checked = true;
            document.getElementById('status-label').textContent = 'Active';
            const offcanvasElement = document.getElementById('offcanvasAddUser');
            let offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (!offcanvasInstance) {
              offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
            }
            offcanvasInstance.hide();
            offcanvasElement.addEventListener('hidden.bs.offcanvas', function cleanupBackdrop() {
              const backdrop = document.querySelector('.offcanvas-backdrop');
              if (backdrop) backdrop.remove();
              document.body.classList.remove('offcanvas-backdrop', 'show');
              document.body.style.overflow = '';
              document.body.style.paddingRight = '';
              offcanvasElement.removeEventListener('hidden.bs.offcanvas', cleanupBackdrop);
            });
            if (dt_basic) {
              dt_basic.ajax.reload(null, false);
            }
          } else {
            showToast('error', responseData.message || 'Error occurred while adding form');
          }
        },
        onError: error => {
          showToast('error', error || 'Unexpected error occurred.');
        },
        onComplete: () => {
          addFormButton.disabled = false;
          addFormButton.innerHTML = 'Save Changes';
        }
      });
    }
    [];
  })();
});
$(function () {
  const stateMap = {
    1: 'Andhra Pradesh',
    2: 'Arunachal Pradesh',
    3: 'Assam',
    4: 'Bihar',
    5: 'Chhattisgarh',
    6: 'Goa',
    7: 'Gujarat',
    8: 'Haryana',
    9: 'Himachal Pradesh',
    10: 'Jharkhand',
    11: 'Karnataka',
    12: 'Kerala',
    13: 'Madhya Pradesh',
    14: 'Maharashtra',
    15: 'Manipur',
    16: 'Meghalaya',
    17: 'Mizoram',
    18: 'Nagaland',
    19: 'Odisha',
    20: 'Punjab',
    21: 'Rajasthan',
    22: 'Sikkim',
    23: 'Tamil Nadu',
    24: 'Telangana',
    25: 'Tripura',
    26: 'Uttar Pradesh',
    27: 'Uttarakhand',
    28: 'West Bengal',
    29: 'Andaman and Nicobar Islands',
    30: 'Chandigarh',
    31: 'Dadra and Nagar Haveli and Daman and Diu',
    32: 'Delhi',
    33: 'Jammu and Kashmir',
    34: 'Ladakh',
    35: 'Lakshadweep',
    36: 'Puducherry'
  };
  var dt_basic_table = $('.datatables-basic');
  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: '/forms/fetch-forms',
        dataSrc: function (json) {
          console.log(json);
          return json;
        }
      },
      columns: [
        {
          data: 'form_name',
          title: 'Form Name'
        },
        {
          data: 'state',
          title: 'State',
          render: function (data, type, row) {
            return stateMap[data] || 'Unknown';
          }
        },
        {
          data: 'status',
          title: 'Status',
          render: function (data, type, row) {
            var statusText = '';
            var statusClass = '';
            if (data === 1) {
              statusText = 'Active';
              statusClass = 'bg-success';
            } else if (data === 0) {
              statusText = 'Inactive';
              statusClass = 'bg-danger';
            }
            return `<span class="badge ${statusClass}">${statusText}</span>`;
          }
        },
        {
          data: null,
          title: 'Actions',
          render: function (data, type, row) {
            return `
              <button class="btn btn-sm btn-warning edit-record" 
            data-id="${row.corporate_form_id}" 
            data-form-name="${row.form_name}" 
            data-status="${row.status}" 
            data-statename="${row.state}">
      Edit
    </button>
     <button class="btn btn-sm btn-danger delete-record" data-id="${row.corporate_form_id}">Delete</button>
            `;
          }
        }
      ],
      order: [[2, 'desc']],
      dom:
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"B>>' +
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
      buttons: [
        {
          extend: 'excelHtml5',
          text: '<i class="fa-sharp fa-solid fa-file-excel"></i>',
          titleAttr: 'Export to Excel',
          filename: 'Drug_type_Export',
          className: 'btn-link ms-3'
        }
      ],
      columnDefs: [
        {
          targets: 2,
          render: function (data, type, row) {
            return data == 1 ? 'Active' : 'Inactive';
          }
        }
      ],
      responsive: true,
      initComplete: function () {
        var count = dt_basic.data().count();
        $('#employeeTypeLabel').text(`List of Forms (${count})`);
        this.api().buttons().container().appendTo('#export-buttons');
      }
    });
    $('.dataTables_filter')
      .addClass('search-container')
      .prependTo('.d-flex.justify-content-between.align-items-center.card-header');
    var existingAddButton = $('.d-flex.justify-content-between.align-items-center.card-header .add-new');
    $('.d-flex.justify-content-between.align-items-center.card-header').append(existingAddButton);
    existingAddButton.addClass('ms-auto');
    var excelExportButton = $('.dt-buttons .buttons-excel');
    $('.d-flex.justify-content-between.align-items-center.card-header').append(excelExportButton);
    excelExportButton
      .removeClass('btn-secondary')
      .addClass('btn-link')
      .find('span')
      .addClass('d-flex justify-content-center')
      .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');
    var selectElement = $('.dataTables_length select');
    var targetCell = $('.advance-search th');
    targetCell.append(selectElement);
    $('.dataTables_length label').remove();
    selectElement.css({
      width: '65px',
      'background-color': '#fff'
    });
    selectElement.addClass('ms-3');
    targetCell.find('.d-flex').append(selectElement);
  }
  $('.datatables-basic tbody').on('click', '.edit-record', function () {
    var id = $(this).data('id');
    var form_name = $(this).data('form-name');
    var state = $(this).data('statename');
    var status = $(this).data('status');
    editForm(form_name, state, status, id);
  });
  $('.datatables-basic tbody').on('click', '.delete-record', function (event) {
    var id = $(this).data('id');
    deleteForm(event, id);
  });

  function editForm(form_name, statename, status, id) {
    console.log(id, form_name, statename, status);
    document.getElementById('form_name_edit').value = form_name;
    $('#statename_edit').data('selected-state', statename);
    const stateSelect = document.getElementById('statename_edit');
    const options = stateSelect.options;
    for (let i = 0; i < options.length; i++) {
      if (options[i].value === statename) {
        options[i].selected = true;
        break;
      }
    }
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
    document.getElementById('edit-forms').setAttribute('data-form-id', id);
    const offcanvasElement = document.getElementById('offcanvaEditUser');
    const bootstrapOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
    bootstrapOffcanvas.show();
    const editFormButton = document.getElementById('edit-forms');
    editFormButton.replaceWith(editFormButton.cloneNode(true));
    const newEditFormButton = document.getElementById('edit-forms');
    newEditFormButton.addEventListener('click', function () {
      const formId = this.getAttribute('data-form-id');
      const updatedFormName = document.getElementById('form_name_edit').value;
      const statusSwitch = document.getElementById('status_switch_edit');
      const updatedStatus = statusSwitch.checked ? 1 : 0;
      const updatedState = document.getElementById('statename_edit').value;
      apiRequest({
        url: `/forms/edit-forms/${formId}`,
        method: 'PUT',
        data: {
          form_name: updatedFormName,
          status: updatedStatus,
          statename: updatedState,
          formId: formId
        },
        onSuccess: response => {
          showToast(response.result, response.message);
          let row = dt_basic.row(function (idx, data, node) {
            console.log('Checking row:', data);
            return data.corporate_form_id == formId;
          });
          if (row.any()) {
            row
              .data({
                ...row.data(),
                form_name: updatedFormName,
                status: updatedStatus,
                state: updatedState
              })
              .draw();
            bootstrapOffcanvas.hide();
          } else {
            console.warn('No matching row found for ID:', formId);
          }
        },
        onError: error => {
          showToast('error', error);
        }
      });
    });
  }

  function getStateNameById(stateId) {
    const select = document.getElementById('statename_edit');
    const option = Array.from(select.options).find(opt => opt.value == stateId);
    return option ? option.textContent.trim() : '';
  }

  function deleteForm(event, id) {
    event.preventDefault();
    Swal.fire({
      title: 'Are you sure?',
      text: 'Do you really want to delete this form? This action cannot be undone.',
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
        apiRequest({
          url: `/forms/form-delete/${id}`,
          method: 'DELETE',
          onSuccess: responseData => {
            if (responseData.result === 'success') {
              dt_basic.row($(event.target).closest('tr')).remove().draw();
              showToast('success', responseData.message);
            } else {
              showToast('error', responseData.message || 'Failed to delete form.');
            }
          },
          onError: error => {
            showToast('error', error || 'Something went wrong. Please try again later.');
          }
        });
      }
    });
  }
});
$(document).ready(function () {
  $('#offcanvasAddUser').on('shown.bs.offcanvas', function () {
    const stateSelect = $('#statename');
    stateSelect.empty();
    stateSelect.append(new Option('-Select State-', ''));
    $.ajax({
      url: '/forms/get-states',
      method: 'GET',
      success: function (response) {
        if (Array.isArray(response)) {
          const stateSelect = $('#statename');
          stateSelect.empty();
          stateSelect.append(new Option('-Select State-', ''));
          response.forEach(function (state) {
            stateSelect.append(new Option(state.statename, state.id));
          });
        } else {
          console.error('Response is not an array:', response);
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX error:', error);
      }
    });
  });
  $('#offcanvaEditUser').on('shown.bs.offcanvas', function () {
    const stateSelect = $('#statename_edit');
    stateSelect.empty();
    stateSelect.append(new Option('-Select State-', ''));
    $.ajax({
      url: '/forms/get-states',
      method: 'GET',
      success: function (response) {
        if (Array.isArray(response)) {
          stateSelect.empty();
          stateSelect.append(new Option('-Select State-', ''));
          response.forEach(function (state) {
            stateSelect.append(new Option(state.statename, state.id));
          });
          const selectedState = stateSelect.data('selected-state');
          if (selectedState) {
            stateSelect.val(selectedState);
          }
        } else {
          console.error('Response is not an array:', response);
        }
      },
      error: function (xhr, status, error) {
        console.error('AJAX error:', error);
      }
    });
  });
});

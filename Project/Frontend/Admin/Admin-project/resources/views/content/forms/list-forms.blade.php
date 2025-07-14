@extends('layouts/layoutMaster')
@section('title', 'Corporate Forms')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<script>
  'use strict';
  let fv, offCanvasEl;
  let dt_basic;
  document.addEventListener('DOMContentLoaded', function(e) {
    (function() {
      const preloader = document.getElementById('preloader');
     // const table = document.getElementById('drugtype-table');
      //const tbody = document.getElementById('drugtype-body');
      const statusSwitch = document.getElementById('status-switch');
      const statusLabel = document.getElementById('status-label');
      let activeStatus = '';
      activeStatus = 'Active';
      statusSwitch.addEventListener('change', function() {
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
        let errorContainer = formNameInput.parentElement.querySelector(
          '.fv-plugins-message-container');
        if (errorContainer) {
          errorContainer.remove();
        }
        addFormButton.disabled = true;
        addFormButton.innerHTML =
          `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        &nbsp;Saving...`;
        addNewForm(formName, stateValue, status);
      });
function addNewForm(form_name, statename, status) {
  // Trim values
    form_name = form_name.trim();
  statename = parseInt(document.getElementById('statename').value);

  // Validation
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
       'Accept': 'application/json',
    },
    data: {
      form_name: form_name,
      statename: statename,
      status: isActive
    },
 onSuccess: (responseData) => {
  if (responseData.result === 'success') {
    showToast('success', responseData.message);

    // Clear form fields
    document.getElementById('form_name').value = '';
    document.getElementById('statename').value = '';
    document.getElementById('status-switch').checked = true;
    document.getElementById('status-label').textContent = 'Active';

    // Ensure the offcanvas closes properly
    const offcanvasElement = document.getElementById('offcanvasAddUser');
    let offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);

    if (!offcanvasInstance) {
      offcanvasInstance = new bootstrap.Offcanvas(offcanvasElement);
    }

    offcanvasInstance.hide();

    // Use Bootstrap's event to remove the backdrop safely
    offcanvasElement.addEventListener('hidden.bs.offcanvas', function cleanupBackdrop() {
      const backdrop = document.querySelector('.offcanvas-backdrop');
      if (backdrop) backdrop.remove();

      // Cleanup body styles
      document.body.classList.remove('offcanvas-backdrop', 'show');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';

      // Remove listener after it's used once
      offcanvasElement.removeEventListener('hidden.bs.offcanvas', cleanupBackdrop);
    });

    // Reload DataTable without resetting pagination
    if (dt_basic) {
      dt_basic.ajax.reload(null, false);
    }

  } else {
    showToast('error', responseData.message || 'Error occurred while adding form');
  }
},


    onError: (error) => {
      showToast('error', error || 'Unexpected error occurred.');
    },
    onComplete: () => {
      addFormButton.disabled = false;
      addFormButton.innerHTML = 'Save Changes';
    }
  });
}

[]
    })();
  });
  
  $(function() {
    const stateMap = {
  1: "Andhra Pradesh",
  2: "Arunachal Pradesh",
  3: "Assam",
  4: "Bihar",
  5: "Chhattisgarh",
  6: "Goa",
  7: "Gujarat",
  8: "Haryana",
  9: "Himachal Pradesh",
  10: "Jharkhand",
  11: "Karnataka",
  12: "Kerala",
  13: "Madhya Pradesh",
  14: "Maharashtra",
  15: "Manipur",
  16: "Meghalaya",
  17: "Mizoram",
  18: "Nagaland",
  19: "Odisha",
  20: "Punjab",
  21: "Rajasthan",
  22: "Sikkim",
  23: "Tamil Nadu",
  24: "Telangana",
  25: "Tripura",
  26: "Uttar Pradesh",
  27: "Uttarakhand",
  28: "West Bengal",
  29: "Andaman and Nicobar Islands",
  30: "Chandigarh",
  31: "Dadra and Nagar Haveli and Daman and Diu",
  32: "Delhi",
  33: "Jammu and Kashmir",
  34: "Ladakh",
  35: "Lakshadweep",
  36: "Puducherry"
};

    var dt_basic_table = $('.datatables-basic');
    if (dt_basic_table.length) {
      dt_basic = dt_basic_table.DataTable({
        ajax: {
          url: '/forms/fetch-forms',
          dataSrc: function(json) {
            console.log(json);
            return json;
          }
        },
        columns: [{
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
            render: function(data, type, row) {
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
            render: function(data, type, row) {
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
        order: [
          [2, 'desc']
        ],
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
          text: '<i class="fa-sharp fa-solid fa-file-excel"></i>',
          titleAttr: 'Export to Excel',
          filename: 'Drug_type_Export',
          className: 'btn-link ms-3',
        }],
        columnDefs: [{
          targets: 2,
          render: function(data, type, row) {
            return data == 1 ? 'Active' : 'Inactive';
          }
        }],
        responsive: true,
        initComplete: function() {
          var count = dt_basic.data().count();
          $('#employeeTypeLabel').text(`List of Forms (${count})`);
          this.api().buttons().container()
            .appendTo('#export-buttons');
        }
      });
      
      $('.dataTables_filter').addClass('search-container').prependTo(
        '.d-flex.justify-content-between.align-items-center.card-header');
      var existingAddButton = $(
        '.d-flex.justify-content-between.align-items-center.card-header .add-new');
      $('.d-flex.justify-content-between.align-items-center.card-header').append(
        existingAddButton);
      existingAddButton.addClass('ms-auto');
      var excelExportButton = $('.dt-buttons .buttons-excel');
      $('.d-flex.justify-content-between.align-items-center.card-header').append(excelExportButton);
      excelExportButton
        .removeClass('btn-secondary')
        .addClass('btn-link')
        .find('span').addClass('d-flex justify-content-center').html(
          '<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>'
        );;
      var selectElement = $('.dataTables_length select');
      var targetCell = $('.advance-search th');
      targetCell.append(selectElement);
      $('.dataTables_length label').remove();
      selectElement.css({
        'width': '65px',
        'background-color': '#fff'
      })
      selectElement.addClass('ms-3');
      targetCell.find('.d-flex').append(selectElement);
    }
  $('.datatables-basic tbody').on('click', '.edit-record', function() {
  var id = $(this).data('id');
  var form_name = $(this).data('form-name');
  var state = $(this).data('statename'); // <- This is undefined
  var status = $(this).data('status');
//console.log(id, form_name, status); // 'state' is not included here
  editForm(form_name,state,status, id);
});

    $('.datatables-basic tbody').on('click', '.delete-record', function(event) {
      var id = $(this).data('id');
      deleteForm(event, id);
    });

   function editForm(form_name, statename, status, id) {
  console.log(id, form_name, statename, status);

  // Prefill the form name
  document.getElementById('form_name_edit').value = form_name;
$('#statename_edit').data('selected-state', statename);
  // Prefill the state
  const stateSelect = document.getElementById('statename_edit');
  // Assuming 'statename' is a string representing the selected state
  const options = stateSelect.options;
  
  // Iterate through options to select the correct state
  for (let i = 0; i < options.length; i++) {
    if (options[i].value === statename) {
      options[i].selected = true;
      break;
    }
  }

  // Handle the status toggle
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

  // Event listener for status toggle
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

  // Set form data-id
  document.getElementById('edit-forms').setAttribute('data-form-id', id);

  // Initialize and show the offcanvas
  const offcanvasElement = document.getElementById('offcanvaEditUser');
  const bootstrapOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
  bootstrapOffcanvas.show();

  // Reinitialize the Save button with the correct event listener
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
      onSuccess: (response) => {
  showToast(response.result, response.message);

  let row = dt_basic.row(function (idx, data, node) {
     console.log('Checking row:', data);
    return data.corporate_form_id  == formId;
  });

  if (row.any()) {
    row.data({
      ...row.data(),
      form_name: updatedFormName,
      status: updatedStatus,
      state: updatedState
    }).draw();

    bootstrapOffcanvas.hide();  // Close only after updating
  } else {
    console.warn('No matching row found for ID:', formId);
  }
}
,
      onError: (error) => {
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
      }).then((result) => {
        if (result.isConfirmed) {
          apiRequest({
            url: `/forms/form-delete/${id}`,
            method: 'DELETE',
            onSuccess: (responseData) => {
              if (responseData.result === 'success') {
                dt_basic.row($(event.target).closest('tr')).remove().draw();
                showToast('success', responseData.message);
              } else {
                showToast('error', responseData.message ||
                  'Failed to delete form.');
              }
            },
            onError: (error) => {
              showToast('error', error ||
                'Something went wrong. Please try again later.');
            }
          });
        }
      });
    }
  });
 $(document).ready(function () {
    $('#offcanvasAddUser').on('shown.bs.offcanvas', function () {
       // console.log('Offcanvas opened'); // Debug
        const stateSelect = $('#statename');
        stateSelect.empty();
        stateSelect.append(new Option('-Select State-', ''));
        $.ajax({
            url: '/forms/get-states',
            method: 'GET',
           success: function(response) {
    //console.log('States response:', response); // should show the array
    if (Array.isArray(response)) {
        const stateSelect = $('#statename');
        stateSelect.empty();
        stateSelect.append(new Option('-Select State-', ''));

        response.forEach(function(state) {
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
        success: function(response) {
            if (Array.isArray(response)) {
                stateSelect.empty();
                stateSelect.append(new Option('-Select State-', ''));

                response.forEach(function(state) {
                    stateSelect.append(new Option(state.statename, state.id));
                });

                // 🔁 Prefill the selected state here after options are loaded
                const selectedState = stateSelect.data('selected-state');
                if (selectedState) {
                    stateSelect.val(selectedState); // sets the value
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

</script>
<!-- Basic Bootstrap Table -->
<div class="card">
  <div class="d-flex justify-content-between align-items-center card-header">
    <button class="btn btn-secondary add-new btn-primary waves-effect waves-light" tabindex="0"
      aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"
      fdprocessedid="uzpu56"><span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
          class="d-none d-sm-inline-block">Add New Forms</span></span></button>
    <!-- Add Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
      aria-labelledby="offcanvasAddUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title"> New Forms</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <div class="row">
          <div class="col mb-4">
            <label for="drug_ingredients" class="form-label">Form Name </label>
            <input type="text" id="form_name" class="form-control" placeholder="Form Name">
          </div>
        </div>
         <div class="row">
          <div class="col mb-4">
            <label for="form_name" class="form-label">State </label>
           <select class="form-select" id="statename">
             <option value="">-Select State-</option>
           </select>
          </div>
        </div>
        <div class="row g-4">
          <div class="col mb-0">
            <label for="emailBasic" class="form-label">Status</label>
            <div class="demo-vertical-spacing">
              <label class="switch">
                <input type="checkbox" class="switch-input is-valid" id="status-switch" checked="true">
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
                <span class="switch-label" id="status-label">Active</span>
              </label>
            </div>
          </div>
        </div>
        <br /><br />
        <button type="button" class="btn btn-primary" id="add-forms">Save
          Changes</button>
        <button type="reset" class="btn btn-label-danger waves-effect"
          data-bs-dismiss="offcanvas">Cancel</button>
        <input type="hidden">
      </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvaEditUser"
      aria-labelledby="offcanvaEditUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvaEditUserLabel" class="offcanvas-title">Edit Form</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <div class="row">
          <div class="col mb-4">
            <label for="in-name" class="form-label">Form Name </label>
            <input type="text" id="form_name_edit" class="form-control" placeholder="Edit Name">
          </div>
        </div>
         <div class="row">
          <div class="col mb-4">
            <label for="drug_ingredients" class="form-label">State </label>
           <select class="form-select" id="statename_edit">
            <option>-Select State-</option>
           </select>
          </div>
        </div>
        <div class="row g-4">
          <div class="col mb-0">
            <label for="emailBasic" class="form-label">Status</label>
            <div class="demo-vertical-spacing">
              <label class="switch">
                <input type="checkbox" class="switch-input" id="status_switch_edit" checked="true">
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
                <span class="switch-label" id="status-label-edit">Active</span>
              </label>
            </div>
          </div>
        </div>
        <br /><br />
        <button type="button" class="btn btn-primary" id="edit-forms">Save
          Changes</button>
        <button type="reset" class="btn btn-label-danger waves-effect"
          data-bs-dismiss="offcanvas">Cancel</button>
        <input type="hidden">
      </div>
    </div>
  </div>
  <div class="card-datatable table-responsive pt-0">
    <table class="datatables-basic table">
      <thead>
        <tr class="advance-search mt-3">
          <th colspan="9" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span class="text-muted" id="employeeTypeLabel">List of Forms</span>
            </div>
          </th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
    </table>
  </div>
</div>
</div>
<hr class="my-12">
@endsection
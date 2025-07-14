@extends('layouts/layoutMaster')
@section('title', 'Drug - Types')
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
      const table = document.getElementById('drugtype-table');
      const tbody = document.getElementById('drugtype-body');
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
      const addDrugtypeButton = document.getElementById('add-drugtype');
      const drugTypeNameInput = document.getElementById('drug_type_name');
      addDrugtypeButton.addEventListener('click', () => {
        const drug_type_name = drugTypeNameInput.value.trim();
        let errorContainer = drugTypeNameInput.parentElement.querySelector(
          '.fv-plugins-message-container');
        if (errorContainer) {
          errorContainer.remove();
        }
        addDrugtypeButton.disabled = true;
        addDrugtypeButton.innerHTML =
          `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        &nbsp;Saving...`;
        addNewDrugType(drug_type_name, activeStatus);
      });

      function addNewDrugType(drug_type_name, status) {
        activeStatus = activeStatus === 'true';
        if (!drug_type_name) {
          showToast('error', 'Please provide a valid Drug type.');
          return;
        }
        apiRequest({
          url: '/drugs/add-drugtype',
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          data: {
            drug_type_name: drug_type_name,
            status: activeStatus
          },
          onSuccess: (responseData) => {
            if (responseData.result === 'success') {
              showToast(responseData.result, responseData.message);
            } else {
              showToast(responseData.result, responseData.message ||
                'Error occurred while adding drug type');
              addDrugtypeButton.disabled = false;
              addDrugtypeButton.innerHTML = 'Save Changes';
            }
          },
          onError: (error) => {
            showToast('error', error);
            addDrugtypeButton.disabled = false;
            addDrugtypeButton.innerHTML = 'Save Changes';
          },
          onComplete: () => {
            addDrugtypeButton.disabled = false;
            addDrugtypeButton.innerHTML = 'Save Changes';
          }
        });
      }
    })();
  });
  $(function() {
    var dt_basic_table = $('.datatables-basic');
    if (dt_basic_table.length) {
      dt_basic = dt_basic_table.DataTable({
        ajax: {
          url: '/drugs/fetch-drugtype',
          dataSrc: function(json) {
            console.log(json);
            return json;
          }
        },
        columns: [{
            data: 'drug_type_name',
            title: 'Drug type'
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
              <button class="btn btn-sm btn-warning edit-record" data-id="${row.id}" data-drug_type_name="${row.drug_type_name}" data-status="${row.status}">Edit</button>
              <button class="btn btn-sm btn-danger delete-record" data-id="${row.id}">Delete</button>
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
          $('#employeeTypeLabel').text(`List of Drug Types (${count})`);
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
      var drug_type_name = $(this).data('drug_type_name');
      var status = $(this).data('status');
      console.log(id, drug_type_name, status);
      editDrugType(drug_type_name, status, id);
    });
    $('.datatables-basic tbody').on('click', '.delete-record', function(event) {
      var id = $(this).data('id');
      deleteDrugtype(event, id);
    });

    function editDrugType(drug_type_name, status, id) {
      console.log(drug_type_name);
      document.getElementById('drug_type_name_edit').value = drug_type_name;
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
      statusSwitch.addEventListener('change', function() {
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
      document.getElementById('edit-drugtype').setAttribute('data-drugtype-id', id);
      const offcanvasElement = document.getElementById('offcanvaEditUser');
      const bootstrapOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
      bootstrapOffcanvas.show();
      const editDrugtypeButton = document.getElementById('edit-drugtype');
      editDrugtypeButton.replaceWith(editDrugtypeButton.cloneNode(true));
      const newEdiDrugTypeButton = document.getElementById('edit-drugtype');
      newEdiDrugTypeButton.addEventListener('click', function() {
        const drugTypeId = this.getAttribute('data-drugtype-id');
        const drug_type_name = document.getElementById('drug_type_name_edit').value;
        const statusSwitch = document.getElementById('status_switch_edit');
        const status = statusSwitch.checked ? 1 : 0;
        const updatedDrugType = document.getElementById('drug_type_name_edit').value;
        const updatedStatus = statusSwitch.checked ? 1 : 0;
        apiRequest({
          url: `/drugs/edit-drugtype/${drugTypeId}`,
          method: 'PUT',
          data: {
            drug_type_name: updatedDrugType,
            status: updatedStatus,
            drugTypeId: drugTypeId
          },
          onSuccess: (response) => {
            showToast(response.result, response.message);
            let row = dt_basic.row(function(idx, data, node) {
              return data.id ==
                drugTypeId;
            });
            if (row) {
              row.data({
                ...row.data(),
                drug_type_name: updatedDrugType,
                status: updatedStatus,
              }).draw();
            }
            bootstrapOffcanvas.hide();
          },
          onError: (error) => {
            showToast('error', error);
          }
        });
      });
    }

    function deleteDrugtype(event, id) {
      event.preventDefault();
      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this drug type? This action cannot be undone.',
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
            url: `/drugs/delete-drugtype/${id}`,
            method: 'DELETE',
            onSuccess: (responseData) => {
              if (responseData.result === 'success') {
                dt_basic.row($(event.target).closest('tr')).remove().draw();
                showToast('success', responseData.message);
              } else {
                showToast('error', responseData.message ||
                  'Failed to delete drug type.');
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
</script>
<!-- Basic Bootstrap Table -->
<div class="card">
  <div class="d-flex justify-content-between align-items-center card-header">
    <button class="btn btn-secondary add-new btn-primary waves-effect waves-light" tabindex="0"
      aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"
      fdprocessedid="uzpu56"><span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
          class="d-none d-sm-inline-block">Add New Drug Type</span></span></button>
    <!-- Add Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
      aria-labelledby="offcanvasAddUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Drug Type</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <div class="row">
          <div class="col mb-4">
            <label for="drug_ingredients" class="form-label">Drug Type </label>
            <input type="text" id="drug_type_name" class="form-control" placeholder="Enter Drug types">
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
        <button type="button" class="btn btn-primary" id="add-drugtype">Save
          Changes</button>
        <button type="reset" class="btn btn-label-danger waves-effect"
          data-bs-dismiss="offcanvas">Cancel</button>
        <input type="hidden">
      </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvaEditUser"
      aria-labelledby="offcanvaEditUserLabel">
      <div class="offcanvas-header border-bottom">
        <h5 id="offcanvaEditUserLabel" class="offcanvas-title">Add New Drug Type</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
          aria-label="Close"></button>
      </div>
      <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <div class="row">
          <div class="col mb-4">
            <label for="in-name" class="form-label">Drug Type </label>
            <input type="text" id="drug_type_name_edit" class="form-control" placeholder="Edit Name">
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
        <button type="button" class="btn btn-primary" id="edit-drugtype">Save
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
              <span class="text-muted" id="employeeTypeLabel">List of Drug types</span>
            </div>
          </th>
        </tr>
        <tr>
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
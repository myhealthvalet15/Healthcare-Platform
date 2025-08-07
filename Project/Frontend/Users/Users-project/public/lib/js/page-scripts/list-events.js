'use strict';
let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
    (function () {
        const preloader = document.getElementById('preloader');
        const table = document.getElementById('drugtemplate-table');
        const tbody = document.getElementById('drugtemplate-body');
    })();
    $(function () {
        var dt_basic_table = $('.datatables-basic');
        if (dt_basic_table.length) {
            var dt_basic = dt_basic_table.DataTable({
                ajax: function (data, callback, settings) {
                    apiRequest({
                        url: '/mhc/events/get-events',
                        method: 'GET',
                        onSuccess: function (response) {
                            console.log(response);
                            if (response.result && response.data.length > 0) {
                                callback({ data: response.data });
                            } else {
                                callback({ data: [] });
                            }
                        },
                        onError: function (errorMessage) {
                            console.error(errorMessage);
                            callback({ data: [] });
                        }
                    });
                },
                columns: [
                    {
                        data: 'event_id',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: null,
                        title: 'From Date / To Date',
                        width: '15%',
                        render: function (data, type, row) {
                            const fromDate = new Date(row.from_datetime).toLocaleDateString();
                            const toDate = new Date(row.to_datetime).toLocaleDateString();
                            return `${fromDate}<br>${toDate}`;
                        }
                    },
                    {
                        data: 'event_name',
                        title: 'Event Name',
                        width: '20%',
                        render: function (data, type, row) {
                            const hasTests = row.details?.test_names && Object.keys(row.details.test_names).length > 0;
                            const icon = hasTests ? `<i class="ti ti-microscope icon-base"></i>` : '';
                            return `
      <span class="view-details-link text-primary" style="cursor: pointer;" data-id="${row.event_id}">
        <b>${row.event_name}</b>
      </span>
      ${icon}
    `;
                        }
                    },
                    {
                        data: 'guest_name',
                        title: 'Guest Name',
                        width: '15%'
                    },
                    {
                        data: 'details',
                        title: 'Employee Type / Department',
                        width: '20%',
                        render: function (data) {
                            const empTypes = data?.employee_type_names ? Object.values(data.employee_type_names).join(', ') : 'N/A';
                            const depts = data?.department_names
                                ? Object.values(data.department_names)
                                    .map(name => name.charAt(0).toUpperCase() + name.slice(1).toLowerCase())
                                    .join(', ')
                                : 'N/A';
                            return ` ${empTypes}<br> ${depts}`;
                        }
                    },
                    {
                        data: null,
                        title: 'Action',
                        width: '10%',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-warning edit-record" data-id="${row.event_id}">Edit</button>
          <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.event_id}">
            <i class="ti ti-trash"></i>
          </button>
        </div>
      `;
                        }
                    }
                ],
                order: [[0, 'desc']],
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
                        filename: 'OTC Export',
                        className: 'btn-link ms-3',
                        exportOptions: { modifier: { page: 'all' } },
                        columns: null
                    }
                ],
                responsive: true,
                initComplete: function () {
                    var api = this.api();
                    api.rows().every(function () {
                        var rowData = this.data();
                        var expiryDate = new Date(rowData.expiry_date);
                        var today = new Date();
                        var diffTime = expiryDate - today;
                        var diffDays = Math.ceil(diffTime / (1000 * 3600 * 24));
                        if (diffDays >= 45 && diffDays <= 60) {
                            $(this.node()).addClass('highlight-row');
                        }
                    });
                    var count = api.data().count();
                    $('#employeeTypeLabel').text(`List of Events (${count})`);
                    api.buttons().container().appendTo('#export-buttons');
                    $('.datatables-basic tbody').on('click', '.view-details-link', function () {
                        const eventId = $(this).data('id');
                        const rowData = dt_basic.row($(this).closest('tr')).data();
                        const details = rowData.details || {};
                        const empTypes = details?.employee_type_names
                            ? Object.values(details.employee_type_names).join(', ')
                            : 'N/A';
                        const depts = details?.department_names
                            ? Object.values(details.department_names)
                                .map(name => name.charAt(0).toUpperCase() + name.slice(1).toLowerCase())
                                .join(', ')
                            : 'N/A';
                        const tests = details?.test_names
                            ? Object.values(details.test_names)
                                .map(test => `<li>${test}</li>`)
                                .join('')
                            : '<li>N/A</li>';
                        $('#testModalLabel').text(`Event  - ${rowData.event_name}`);
                        $('#testModalBody').html(`
  <div class="row mb-2">
    <div class="col-md-6">
      <strong>Guest Name:</strong> ${rowData.guest_name || 'N/A'}
    </div>
    <div class="col-md-6">
      <strong>From:</strong>
      ${new Date(rowData.from_datetime).toLocaleString()} <br> <strong>To:</strong>
      ${new Date(rowData.to_datetime).toLocaleString()}
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-md-6">
      <strong>Employee Types:</strong> ${empTypes}
    </div>
    <div class="col-md-6">
      <strong>Departments:</strong> ${depts}
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <strong>Tests Taken:</strong>
      <ul>${tests}</ul>
    </div>
  </div>
  <div class="row" >
    <div class="col-12">
      <strong>Description:</strong> ${rowData.event_description || 'N/A'}
    </div>
  </div>
`);
                        $('#testModal').modal('show');
                    });
                }
            });
            $('#DataTables_Table_0_filter label')
                .contents()
                .filter(function () {
                    return this.nodeType === 3;
                })
                .remove();
            $('#DataTables_Table_0_filter input').attr('placeholder', 'Search by Event / Guest Name');
            $('input[type="search"]').css('width', '300px');
            $('#DataTables_Table_0_filter input').css('height', '37px');
            $('#DataTables_Table_0_filter input').css('font-size', '15px');
            $('.dataTables_filter')
                .addClass('search-container')
                .prependTo('.d-flex.justify-content-end.align-items-center.card-header');
            var existingAddButton = $('.d-flex.justify-content-end.align-items-center.card-header .add-new');
            $('.d-flex.justify-content-end.align-items-center.card-header').append(existingAddButton);
            var excelExportButtonContainer = $('.dt-buttons.btn-group.flex-wrap');
            existingAddButton.removeClass('ms-auto');
            $('.d-flex.justify-content-end.align-items-center.card-header').append(excelExportButtonContainer);
            excelExportButtonContainer.find('button').addClass('ms-3').removeClass('ms-3');
            var excelExportButton = excelExportButtonContainer.find('.buttons-excel');
            excelExportButton
                .removeClass('btn-secondary')
                .addClass('btn-link')
                .find('span')
                .addClass('d-flex justify-content-center')
                .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');
            existingAddButton.addClass('ms-auto');
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
            console.log(id);
        });
    });
    flatpickr('#modalmanufacter_date', {
        dateFormat: 'd/m/Y'
    });
    flatpickr('#modalexpiry_date', {
        dateFormat: 'd/m/Y'
    });
});
$(document).on('click', '.deleteBtn', function () {
    var eventId = $(this).data('id');
    console.log(eventId);
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger me-3',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.isConfirmed) {
            apiRequest({
                url: `/mhc/events/delete/${eventId}`,
                method: 'DELETE',
                onSuccess: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Event has been deleted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                    $('.datatables-basic').DataTable().ajax.reload();
                },
                onError: function (errorMessage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage || 'An error occurred while deleting.',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }
            });

        }
    });
});
$(document).on('click', '.edit-record', function () {
    var id = $(this).data('id');
    console.log('Clicked Edit ID:', id);
    if (id) {
        window.location.href = `/mhc/events/edit-events/${id}`;
    } else {
        console.error('No event ID found for editing.');
    }
});

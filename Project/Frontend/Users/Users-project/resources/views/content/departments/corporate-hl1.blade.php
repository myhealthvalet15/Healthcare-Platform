@extends('layouts.layoutMaster')
@section('title', 'DataTables - Advanced Tables')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection
@section('content')
<style>
    .form-check-input:checked {
        background-color: green !important;
    }

    .form-check-input:checked+.form-check-label .status-label {
        color: green;
    }

    .form-check-input:not(:checked) {
        background-color: lightcoral !important;
        border: 2px solid lightcoral;
    }

    .form-check-input:not(:checked)+.form-check-label .status-label {
        color: lightcoral;
        /* Light red text */
    }

    .form-check-input {
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .side-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100%;
        background-color: #fff;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
        transition: right 0.3s ease-in-out;
        z-index: 1050;
        overflow-y: auto;
    }

    .side-panel.open {
        right: 0;
    }

    .side-panel-header {
        padding: 15px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .side-panel-title {
        margin: 0;
    }

    .side-panel-body {
        padding: 15px;
    }

    .dataTables_length {
        position: absolute;
        top: 1px;
        right: 20px;
        color: white;
    }

    div.dataTables_wrapper div.dataTables_length select {
        margin-left: .5rem;
        margin-right: .5rem;
        background-color: white;
    }

    .card-body {
        position: absolute;
        top: 40px;
        right: 200px;
        color: white;
    }

    .card-datatable {
        padding-top: 5px;
    }

    .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-container {
        width: 100%;
    }

    .position-relative {
        width: 100%;
    }

    /* Hide only the "entries" text */
    .dataTables_length label {
        display: flex;
        align-items: center;
    }

    .dataTables_length label::after {
        content: '';
        display: none;
    }

    .dataTables_length select {
        color: black;
    }
</style>
<div class="row">
    <div class="col-3">
        <div class="search-container">
            <label for="customSearchInput" class="visually-hidden">Search:</label>
            <input type="search" id="customSearchInput" placeholder="Search Departments" class="form-control" />
        </div>
    </div>
    <div class="col-6 d-flex justify-content-end">
        <div class="position-relative" style="height: 50px;">
            <button class="btn btn-primary btn-sm position-absolute top-0 end-0 m-3 text-white" id="openPanelButton"
                style="margin-right:27px;">Add New Department</button>
        </div>
        <button id="exportExcel" style="border: none;">
            <i class="fa-solid fa-file-excel" style="font-size: 30px;"></i>
        </button>
    </div>
</div>
<div class="card">
    <h5 class="card-header" style="background-color:rgb(107, 27, 199); color: white; font-size:15px;">
       
    </h5>
    <div class="card-body">
    </div>
    <div class="card-datatable table-responsive">
        <table class="dt-responsive table">
            <thead>
                <tr>
                    <th>Department name</th>
                    <th>Departmnt code</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddEmployeeType"
    aria-labelledby="offcanvasAddEmployeeTypeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasAddEmployeeTypeLabel">Add New
            Department</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"
            id="closePanelButton"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="add-new-record pt-0 row g-2" id="form-add-new-record" method="post" action="/hl1create">
            @csrf
            <div class="col-sm-12">
                <label class="form-label" for="hl1_name">Department Name</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                    <input type="text" id="hl1_name" class="form-control" name="hl1_name"
                        placeholder="Enter department name" required>
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="hl1_code">Department Code</label>
                <input type="text" class="form-control" id="hl1_code" name="hl1_code"
                    placeholder="Enter department code" required>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="active_status">Status</label>
                <div class="form-switch mt-1">
                    <input class="form-check-input toggle-active-status" type="checkbox" name="active_status"
                        id="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
            </div>
            <div class="col-sm-12">
                <button type="reset" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-primary me-sm-4 me-1">Submit</button>
            </div>
            <input type="hidden" name="corporate_id" value="{{$corporate_id}}">
            <input type="hidden" name="location_id" value="{{$location_id}}">
            <input type="hidden" name="corporate_admin_user_id" value="{{$corporate_admin_user_id}}">
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditEmployeeType"
    aria-labelledby="offcanvasEditEmployeeTypeLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEditEmployeeTypeLabel">Edit
            Department</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form id="editForm" method="POST">
            @csrf
            <div class="mb-3">
                <label for="edit_hl1_name" class="form-label">Department
                    Name</label>
                <input type="text" class="form-control" id="edit_hl1_name" name="hl1_name" required>
            </div>
            <div class="mb-3">
                <label for="edit_hl1_code" class="form-label">Department
                    Code</label>
                <input type="text" class="form-control" id="edit_hl1_code" name="hl1_code" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label">Status</label>
                <div class="form-switch mt-1">
                    <!-- Hidden input to capture unchecked status -->
                    <input type="hidden" name="active_status" value="0">
                    <input class="form-check-input toggle-active-status" type="checkbox" id="edit_active_status"
                        name="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="edit_active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
            </div>
            <button type="reset" class="btn btn-secondary secondary" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this entry?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="deleteButton" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openPanelButton = document.getElementById('openPanelButton');
        const sidePanel = document.getElementById('offcanvasAddEmployeeType');
        openPanelButton.addEventListener('click', function () {
            const offcanvas = new bootstrap.Offcanvas(sidePanel);
            offcanvas.show();
        });
        const statusCheckbox = document.getElementById('active_status');
        const statusLabel = document.querySelector('.status-label');
        statusCheckbox.addEventListener('change', function () {
            statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
        });
        const form = document.getElementById('form-add-new-record');
        const formValidation = FormValidation.formValidation(form, {
            fields: {
                hl1_name: {
                    validators: {
                        notEmpty: {
                            message: 'The department name is required'
                        },
                        stringLength: {
                            min: 3,
                            max: 50,
                            message: 'The department name must be between 3 and 50 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: 'The department name can only consist of alphabetical characters, numbers, and spaces'
                        }
                    }
                },
                hl1_code: {
                    validators: {
                        notEmpty: {
                            message: 'The department code is required'
                        },
                        stringLength: {
                            min: 3,
                            max: 10,
                            message: 'The department code must be between 3 and 10 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9]+$/,
                            message: 'The department code can only consist of alphabetical and numeric characters'
                        }
                    }
                },
                active_status: {
                    validators: {
                        notEmpty: {
                            message: 'The status field is required'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.col-sm-12'
                }),
                autoFocus: new FormValidation.plugins.AutoFocus(),
                submitButton: new FormValidation.plugins.SubmitButton()
            },
            init: instance => {
                instance.on('plugins.message.placed', function (e) {
                    if (e.element.parentElement.classList.contains('input-group')) {
                        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                    }
                });
            }
        });
        form.addEventListener('submit', function (event) {
            formValidation.validate().then(function (status) {
                if (status === 'Valid') {
                    form.submit();
                } else {
                    event.preventDefault();
                }
            });
        });
    });
    $(function () {
        var dt_responsive_table = $('.dt-responsive');
        var currentRow = null;
        var currentId = null;
       // var dataSet = @json($hl1);
        var dataSet = @json($hl1 ?? []);
       // console.log(dataSet);
        var dt_responsive = dt_responsive_table.DataTable({
            data:dataSet,
            columns: [
                { data: 'hl1_name' },
                { data: 'hl1_code' },
                {
                    data: 'active_status',
                    render: function (data) {
                        var status = {
                            1: { title: 'Active', class: 'bg-label-success' },
                            0: { title: 'Inactive', class: 'bg-label-danger' }
                        };
                        return status[data]
                            ? `<span class="badge ${status[data].class}">${status[data].title}</span>`
                            : data;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${data.id}"><i class="ti ti-pencil"></i>edit</button>
                    `;
                    }
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                },
                zeroRecords: "No records found",
                emptyTable: "No data available in table",
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return `Details of ${data['hl1_name']}`;
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ''
                                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                    <td>${col.title}:</td>
                                    <td>${col.data}</td>
                                </tr>`
                                : '';
                        }).join('');
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    }
                }
            }, buttons: [
                {
                    extend: 'excelHtml5',
                    text: '',
                    className: 'btn btn-secondary btn-sm d-none',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        }
                    }
                }
            ]
        });
        
        $('#exportExcel').on('click', function () {
            dt_responsive.button(0).trigger();
        });
        $(document).ready(function () {
            $(document).on('click', '.editBtn', function () {
    currentRow = dt_responsive.row($(this).parents('tr'));
    var data = currentRow.data();
    currentId = data.hl1_id;

    $('#editForm').attr('action', '/hl1update/' + currentId);
    $('#edit_hl1_name').val(data.hl1_name);
    $('#edit_hl1_code').val(data.hl1_code);
    var isActive = data.active_status == 1;
    console.log(isActive);
    $('#edit_active_status').prop('checked', isActive);
    updateStatusLabel('#edit_active_status', isActive ? 1 : 0);

    const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEditEmployeeType'));
    offcanvas.show();
});

        });

        function updateStatusLabel(selector, status) {
            $(selector).siblings('label').find('.status-label').text(status === 1 ? 'Active' : 'Inactive');
        }
        $('#editForm').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: $(this).attr('action'),
        method: 'post',
        data: formData,
        success: function (response) {
            console.log(response);
            showToast("success", "Department details updated successfully!");

            // Find the updated row in DataTable
            var rowData = {
                hl1_name: $('#edit_hl1_name').val(),
                hl1_code: $('#edit_hl1_code').val(),
                active_status: $('#edit_active_status').prop('checked') ? 1 : 0,
                id: currentId // Make sure this is the correct ID from the row you are editing
            };

            // Update the row in the DataTable
            currentRow.data(rowData).draw();

            // Close offcanvas after successful update
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEditEmployeeType'));
            offcanvas.hide();
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            alert('Error updating entry: ' + (xhr.responseJSON?.message || error));
        }
    });
});

        $('#addNewModal').on('show.bs.modal', function () {
            $('#add_active_status').prop('checked', false);
            updateStatusLabel('#add_active_status', 0);
        });
        $(document).on('change', '.toggle-active-status', function () {
            var status = $(this).prop('checked') ? 1 : 0;
            updateStatusLabel(this, status);
        });
       /*  $('#editForm').submit(function (e) {
            e.preventDefault();
            var updatedData = {
                hl1_name: $('#edit_hl1_name').val(),
                hl1_code: $('#edit_hl1_code').val(),
                active_status: $('#edit_active_status').prop('checked') ? 1 : 0
            };
            currentRow.data(updatedData).draw();
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEditEmployeeType'));
            offcanvas.hide();
        }); */
        $(document).on('click', '.deleteBtn', function () {
            currentRow = dt_responsive.row($(this).parents('tr'));
            currentId = currentRow.data().hl1_id;
            if (currentId) {
                $('#deleteModal').modal('show');
            }
        });
        $('#deleteButton').click(function () {
            if (currentId) {
                $.ajax({
                    url: `/hl1delete/${currentId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        currentRow.remove().draw();
                        $('#deleteModal').modal('hide');
                    },
                    error: function (error) {
                        console.error('Error deleting record:', error);
                    }
                });
            } else {
                console.warn('No ID found for deletion');
            }
        });
        const searchInput = $('#customSearchInput');
        const dt = dt_responsive;
        let clearButton = $('#clearSearch');
        if (clearButton.length === 0) {
            clearButton = $('<button>')
                .attr('id', 'clearSearch')
                .html('&times;')
                .css({
                    position: 'absolute',
                    right: '10px',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    display: 'none',
                    background: 'transparent',
                    border: 'none',
                    fontSize: '16px',
                    cursor: 'pointer',
                    zIndex: 10
                });
            $('.search-container').css('position', 'relative').append(clearButton);
        }
        clearButton.hide();
        searchInput.on('keyup', function () {
            const value = $(this).val();
            dt.search(value).draw();
            if (value) {
                clearButton.show();
            } else {
                clearButton.hide();
            }
        });
        clearButton.on('click', function () {
            searchInput.val('');
            dt.search('').draw();
            clearButton.hide();
        });
        $('.dataTables_length label').contents().filter(function () {
            return this.nodeType === 3 && this.nodeValue.trim() === "Show";
        }).remove();
        $('.dataTables_length label').contents().filter(function () {
            return this.nodeType === 3 && this.nodeValue.trim() === "entries";
        }).remove();
    });
</script>
@endsection
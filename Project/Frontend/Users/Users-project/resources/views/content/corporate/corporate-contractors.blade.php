@extends('layouts.layoutMaster')

@section('title', 'DataTables - Advanced Tables')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/contractor-form-validation.js'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'

])
@endsection

@section('content')
<!-- Responsive Datatable -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<head>
    
    <style>
        /* Custom toggle switch colors */
        .form-check-input:checked {
            background-color: green !important;
        }

        .form-check-input:checked+.form-check-label .status-label {
            color: green;
        }

        .form-check-input:not(:checked) {
            background-color: lightcoral !important;
            /* Light red background */
        }

        .form-check-input:not(:checked)+.form-check-label .status-label {
            color: lightcoral;
            /* Light red text */
        }

        .deleteBtn {

            color: red;
            /* Custom red text */
        }

        /* Optional: Adding smooth transition for switch */
        .form-check-input {
            transition: background-color 0.3s ease;
        }

        .dataTables_length {
            position: absolute;
            top: 1px;
            right: 20px;
            color: white;
        }


        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Make sure the search input is aligned to the left */
        .search-container {
            width: 100%;
        }

        /* Optional: Make sure the Add New button is aligned to the right */
        .position-relative {
            width: 100%;
        }

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

        .card-body {
            position: absolute;
            top: 40px;
            right: 200px;
            color: white;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin-left: .5rem;
            margin-right: .5rem;
            background-color: white;
        }
    </style>

</head>
<div class="row">
    <div class="col-3">
        <div class="search-container">
            <label for="customSearchInput" class="visually-hidden">Search:</label>
            <input type="search" id="customSearchInput" placeholder="Search contractors." class="form-control" />
        </div>
    </div>
    <div class="col-6 d-flex justify-content-end">
        <div class="position-relative" style="height: 50px;">
            <button class="btn btn-primary btn-sm position-absolute top-0 end-0 m-3 text-white" data-bs-toggle="offcanvas" data-bs-target="#addNewModal" aria-controls="addNewModal">
                Add New Contractor
            </button>

        </div>
        <button id="exportExcel" style="border: none;">

            <i class="fa-solid fa-file-excel" style="font-size: 30px;"></i>
        </button>
    </div>
</div>
<br/>
<div class="card">
   
      <div class="card-datatable table-responsive">
        <table class="dt-responsive table">
            <thead>
            <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Contractors</span>
                        </div>
                    </th>
                </tr>
                <tr>
                <th style="display:none;">ID</th>
                    <th>Contractor name</th>
                    <th>Email</th>
                    <th>Address</th>
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

<!-- Add New Modal -->
<!-- Offcanvas Structure -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addNewModal" aria-labelledby="addNewModalLabel"
    data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addNewModalLabel">Add New Contractor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Form inside Offcanvas -->

        <form action="{{ route('addContractors') }}" method="POST" id="addNewForm">
           
        @csrf
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="contractor_name">Contractor Name</label>
                <input type="text" id="contractor_name" name="contractor_name" class="form-control" placeholder="johndoe" />
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="contractor_email">Email</label>
                <input type="email" id="contractor_email" name="contractor_email" class="form-control" placeholder="john.doe@email.com" />
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="contractor_address">Address</label>
                <input type="text" id="contractor_address" name="contractor_address" class="form-control" placeholder="Address" />
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label" for="add_active_status">Status</label>
                <div class="form-switch mt-1">
                    <input type="hidden" name="active_status" value="0">
                    <input class="form-check-input toggle-active-status" type="checkbox" id="add_active_status" name="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="add_active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
               
            </div>
           
            <div class="col-sm-12">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                <button type="button" class="btn btn-primary" id="saveChangesButton">Save changes</button>
            </div>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-end editModal" tabindex="-1" id="editModal" aria-labelledby="offcanvasEditEmployeeTypeLabel"
    data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEditEmployeeTypeLabel">Edit Contractor Details</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="col-sm-12 mb-3">
                <label for="edit_contractor_name" class="form-label">Contractor Name</label>
                <input type="text" class="form-control" id="edit_contractor_name" name="contractor_name" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="edit_contractor_email" class="form-label">Email</label>
                <input type="text" class="form-control" id="edit_contractor_email" name="email" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="edit_contractor_address" class="form-label">Address</label>
                <input type="text" class="form-control" id="edit_contractor_address" name="address" required>
            </div>
            <div class="col-sm-12 mb-3">
                <label class="form-label">Status</label>
                <div class="form-switch mt-1">
                    <!-- Hidden input to capture unchecked status -->
                    <input type="hidden" name="active_status" value="0">
                    <input class="form-check-input toggle-active-status" type="checkbox" id="edit_active_status" name="active_status" value="1">
                    <label class="form-check-label ms-2 small" for="edit_active_status">
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
            </div>
            <div class="col-sm-12">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Close</button>
                <button type="submit" class="btn btn-primary" id="updateChangesButton">Update</button>
            </div>
        </form>
    </div>
</div>



<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Contractor details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this contractor details?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="deleteButton" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function() {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        var dt_responsive_table = $('.dt-responsive');
        var currentRow = null;
        var currentId = null;

        var dt_responsive = dt_responsive_table.DataTable({
            ajax: {
                url: 'https://login-users.hygeiaes.com/corporate/contractors-list',
                dataSrc: function(json) {
                  console.log(json); 
                    return json.result && json.data.length > 0 ? json.data : []; // Ensure an empty array if no data
                }
            },
            columns: [
                {
            data: "corporate_contractors_id",
            visible: false,       
            searchable: false    
        },
                
                {
                    data: 'contractor_name'
                },
                {
                    data: 'contractor_email'
                },
                {
                    data: 'contractor_address'
                },
                {
                    data: 'active_status',
                    render: function(data) {
                        var status = {
                            1: {
                                title: 'Active',
                                class: 'bg-label-success'
                            },
                            0: {
                                title: 'Inactive',
                                class: 'bg-label-danger'
                            }
                        };
                        return status[data] ?
                            `<span class="badge ${status[data].class}">${status[data].title}</span>` :
                            data;
                    }
                },

                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${data.corporate_contractors_id}"><i class="ti ti-pencil"></i>edit</button>
                        <button class="deleteBtn" style="border:none;"data-id="${data.corporate_contractors_id}"><i class="ti ti-trash"></i></button>
                    `;
                    }
                }
            ],
            pageLength: 10,
            order: [[0, 'desc']], // Set the default number of rows to display
            dom: '<"row"<"col-sm-12 col-md-6"B>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                }
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function(row) {
                            var data = row.data();
                            return `Details of ${data['contractor_name']}`;
                        }
                    }),
                    type: 'column',
                    renderer: function(api, rowIdx, columns) {
                        var data = $.map(columns, function(col) {
                            return col.title !== '' ?
                                `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                 x                   <td>${col.title}:</td>
                                    <td>${col.data}</td>
                                </tr>` :
                                '';
                        }).join('');
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    }
                }
            },
            
            buttons: [{
                extend: 'excelHtml5',
                text: '', // Do not display button inside table
                className: 'btn btn-secondary btn-sm d-none', // Hide button inside DataTable (not required)
                exportOptions: {
                    modifier: {
                        page: 'all' // Export all data (not just the current page)
                    }
                }
            }],
            drawCallback: function(settings) {
        var api = this.api();
        var count = api.data().count();

        $('#employeeTypeLabel').text(`List of Contractors (${count})`);
        api.buttons().container().appendTo('#export-buttons');
    }

        });
        $('#exportExcel').on('click', function() {
            dt_responsive.button(0).trigger(); // Trigger the first button (Excel export)
        });
        $(document).on('click', '.editBtn', function() {
            currentRow = dt_responsive.row($(this).parents('tr'));
            var data = currentRow.data();
            currentId = data.corporate_contractors_id;

            $('#editForm').attr('action', "updateContractor/" + currentId);

            $('#edit_contractor_name').val(data.contractor_name);
            $('#edit_contractor_email').val(data.contractor_email);
            $('#edit_contractor_address').val(data.contractor_address);

            var isActive = data.active_status == 1;
            $('#edit_active_status').prop('checked', isActive);

            updateStatusLabel('#edit_active_status', isActive ? 1 : 0);
            var offcanvasElement = document.getElementById('editModal');
            var offcanvas = new bootstrap.Offcanvas(offcanvasElement);
            offcanvas.show();

        });
       
        $(document).on('click', '#saveChangesButton', function (e) {
    e.preventDefault();
    console.log('Am here');

    const form = document.getElementById('addNewForm'); // Get the form element
    const formData = new FormData(form); // Use the form to get data

    // Append CSRF token only if not already in form
    if (!formData.has('_token')) {
        const token = $('meta[name="csrf-token"]').attr('content');
        formData.append('_token', token);
    }

    console.log([...formData]); // Log form data key-value pairs

    $.ajax({
        url: "/corporate/contractor",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {
            if (response.success) {
                bootstrap.Offcanvas.getOrCreateInstance('#addNewModal').hide();
                location.reload();
                // showToast("success", "Contractor details added successfully!");
            } else {
                // showToast("error", "Failed to add contractor.");
            }
        },
        error: function (xhr) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    showToast("error", response.message);
                } else {
                    showToast("error", "An error occurred while adding the contractor.");
                }
            } catch (e) {
                showToast("error", "An unexpected error occurred.");
            }
        }
    });
});





        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            console.log($(this).attr('action'));

            $.ajax({
                url: $(this).attr('action'),
                method: 'PUT',
                data: formData,
                success: function(response) {
                    $('#editModal').offcanvas('hide');

                    showToast("success", "Contractor details updated successfully!");
                    location.reload();



                },
                error: function(xhr, status, error) {
                    $('#editModal').modal('hide');

                    // alert('Error updating entry: ' + error);
                    showToast("error", error);

                }
            });
        });

        $('#addNewModal').on('show.bs.modal', function() {
            $('#add_active_status').prop('checked', false);
            updateStatusLabel('#add_active_status', 0);
        });

        function updateStatusLabel(selector, status) {
            $(selector).siblings('label').find('.status-label').text(status === 1 ? 'Active' : 'Inactive');
        }


        // Initialize the status label based on checkbox value
        $('#edit_active_status').on('change', function() {
            updateStatusLabel(this, $(this).prop('checked') ? 1 : 0);
        });


        $(document).on('change', '.toggle-active-status', function() {
            var status = $(this).prop('checked') ? 1 : 0;
            updateStatusLabel(this, status); 
        });

       /*  $('#editForm').submit(function(e) {
            e.preventDefault();
            var updatedData = {
                contractor_name: $('#edit_contractor_name').val(),
                contractor_email: $('#edit_contractor_email').val(),
                contractor_address: $('#edit_contractor_address').val(),
                active_status: $('#edit_active_status').prop('checked') ? 1 : 0
            };
            currentRow.data(updatedData).draw();
            $('#editModal').modal('hide');
        }); */


        $(document).on('click', '.deleteBtn', function() {

            currentRow = dt_responsive.row($(this).parents('tr'));
            currentId = currentRow.data().corporate_contractors_id;


            if (currentId) {

                $('#deleteModal').modal('show');
            }
        });
      
        $('#deleteButton').on('click', function () {
    if (!currentId) return;

    $.ajax({
        url: `/corporate/deleteContractor/${currentId}`,
        method: 'DELETE',
        success: function (response) {
            if (response.result) {
                // Remove row from DataTable
                currentRow.remove().draw();

                $('#deleteModal').modal('hide');
                showToast("success", "Contractor deleted successfully!");
            } else {
                showToast("error", "Deletion failed. Please try again.");
            }
        },
        error: function (xhr) {
            console.error("Delete error:", xhr);
            showToast("error", "An error occurred while deleting.");
        }
    });
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

        searchInput.on('keyup', function() {
            const value = $(this).val();
            dt.search(value).draw();

            if (value) {
                clearButton.show();
            } else {
                clearButton.hide();
            }
        });

        clearButton.on('click', function() {
            searchInput.val(''); // Clear the input field
            dt.search('').draw(); // Reset DataTables search
            clearButton.hide(); // Hide the clear button
        });

       

        $('.dataTables_length label').contents().filter(function() {
            return this.nodeType === 3 && this.nodeValue.trim() === "Show";
        }).remove();


        $('.dataTables_length label').contents().filter(function() {
            return this.nodeType === 3 && this.nodeValue.trim() === "entries";
        }).remove();
        
    });
</script>
@endsection


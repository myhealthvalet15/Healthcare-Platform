@extends('layouts/layoutMaster')

@section('title', 'Employee List')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss'



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
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'

])
@endsection

@section('page-script')
@vite([
'resources/assets/js/forms-selects.js'


])
@endsection

@section('content')

<style>
    .showDetailsBtn {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        font-size: 14px;

        transition: all 0.3s ease;
    }

    .showDetailsBtn i {
        margin-right: 5px;
    }

    .showDetailsBtn:hover {
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }

    .showDetailsBtn:focus {
        outline: none;
    }

    .custom-header {
        padding: 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .search-section input,
    .filters-section select {
        min-width: 200px;
    }

    .search-section button,
    .filters-section button {
        white-space: nowrap;
    }

    .advance-search {
        font-size: 14px;
    }

    .advance-search span.text-primary {
        font-weight: bold;
    }

    .dataTables_length select {
        background-color: white !important;
        /* Set the background color to white */
        color: black;
        /* Optionally, you can set the text color to black */
    }
</style>
<!-- DataTable with Buttons -->
<div class="card">
<!--     <div class="col-md-6 mb-6">
        <label for="select2Basic" class="form-label">Basic</label>
        <select id="select2Basic" class="select2 form-select form-select-lg" data-allow-clear="true">
            <option value="AK">Alaska</option>
            <option value="HI">Hawaii</option>
            <option value="CA">California</option>
            <option value="NV">Nevada</option>
            <option value="OR">Oregon</option>
            <option value="WA">Washington</option>
           
           
        </select>
    </div> -->
    <div class="card-datatable table-responsive pt-0">

        <table class="datatables-basic table">

            <thead>

                <tr>
                    <th>Employee Id</th>
                    <th>Employee name</th>
                    <th>phone number</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>View More</th>

                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Employee Details</h5>
                <span>Employee Id : #<span id="modalemployeeId"></span></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Container for image and text -->
                <div class="d-flex">
                    <!-- Left side (Text content) -->
                    <div>
                        <p><strong>Name :</strong> <span id="modalName"></span></p>
                        <p><strong>Email :</strong> <span id="modalEmail"></span></p>
                        <p><strong>Phone Number :</strong> <span id="modalPhone"></span></p>
                         <p><strong>Age / Gender :</strong> <span id="modalage"></span> / <span id="modalgender"></span></p>
                         <p><strong>DOB :</strong> <span id="modaldob"></span> </p>
                         <hr>
                        <p><strong>Employee Type :</strong> <span id="modalemployee_type_name"></span></p>
                        <p><strong>Department :</strong> <span id="modalDepartment"></span></p>
                        <p><strong>Designation :</strong> <span id="modalDesignation"></span></p>
                        <p><strong>DOJ :</strong> <span id="modaldoj"></span></p>
                    </div>

                    <!-- Right side (Image with padding) -->
                    <div class="ms-auto" style="padding: 20px; background-color:#7367f0; width: 125px; height: 125px; display: flex; justify-content: center; align-items: center;">
                        <img id="modalImage" src="https://login-users.hygeiaes.com/assets/img/avatars/3.png" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px;">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal to add new record -->
<div class="offcanvas offcanvas-end" id="add-new-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
            <div class="col-sm-12">
                <label class="form-label" for="basicFullname">Full Name</label>
                <div class="input-group input-group-merge">
                    <span id="basicFullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                    <input type="text" id="basicFullname" class="form-control dt-full-name" name="basicFullname"
                        placeholder="John Doe" aria-label="John Doe" aria-describedby="basicFullname2" />
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="basicPost">Post</label>
                <div class="input-group input-group-merge">
                    <span id="basicPost2" class="input-group-text"><i class='ti ti-briefcase'></i></span>
                    <input type="text" id="basicPost" name="basicPost" class="form-control dt-post"
                        placeholder="Web Developer" aria-label="Web Developer" aria-describedby="basicPost2" />
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="basicEmail">Email</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                    <input type="text" id="basicEmail" name="basicEmail" class="form-control dt-email"
                        placeholder="john.doe@example.com" aria-label="john.doe@example.com" />
                </div>
                <div class="form-text">
                    You can use letters, numbers & periods
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="basicDate">Joining Date</label>
                <div class="input-group input-group-merge">
                    <span id="basicDate2" class="input-group-text"><i class='ti ti-calendar'></i></span>
                    <input type="text" class="form-control dt-date" id="basicDate" name="basicDate"
                        aria-describedby="basicDate2" placeholder="MM/DD/YYYY" aria-label="MM/DD/YYYY" />
                </div>
            </div>
            <div class="col-sm-12">
                <label class="form-label" for="basicSalary">Salary</label>
                <div class="input-group input-group-merge">
                    <span id="basicSalary2" class="input-group-text"><i class='ti ti-currency-dollar'></i></span>
                    <input type="number" id="basicSalary" name="basicSalary" class="form-control dt-salary"
                        placeholder="12000" aria-label="12000" aria-describedby="basicSalary2" />
                </div>
            </div>
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>

    </div>
</div>
<!--/ DataTable with Buttons -->

<hr class="my-12">

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    /**
     * DataTables Basic
     */

    'use strict';

    let fv, offCanvasEl;
    //document.addEventListener('DOMContentLoaded', function(e) {
    $(document).ready(function() {
        (function() {
            const formAddNewRecord = document.getElementById('form-add-new-record');

            setTimeout(() => {
                const newRecord = document.querySelector('.create-new'),
                    offCanvasElement = document.querySelector('#add-new-record');

                // To open offCanvas, to add new record
                if (newRecord) {
                    newRecord.addEventListener('click', function() {
                        offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                        // Empty fields on offCanvas open
                        (offCanvasElement.querySelector('.dt-full-name').value = ''),
                        (offCanvasElement.querySelector('.dt-post').value = ''),
                        (offCanvasElement.querySelector('.dt-email').value = ''),
                        (offCanvasElement.querySelector('.dt-date').value = ''),
                        (offCanvasElement.querySelector('.dt-salary').value = '');
                        // Open offCanvas with form
                        offCanvasEl.show();
                    });
                }
            }, 200);

            // Form validation for Add new record
            fv = FormValidation.formValidation(formAddNewRecord, {
                fields: {
                    basicFullname: {
                        validators: {
                            notEmpty: {
                                message: 'The name is required'
                            }
                        }
                    },
                    basicPost: {
                        validators: {
                            notEmpty: {
                                message: 'Post field is required'
                            }
                        }
                    },
                    basicEmail: {
                        validators: {
                            notEmpty: {
                                message: 'The Email is required'
                            },
                            emailAddress: {
                                message: 'The value is not a valid email address'
                            }
                        }
                    },
                    basicDate: {
                        validators: {
                            notEmpty: {
                                message: 'Joining Date is required'
                            },
                            date: {
                                format: 'MM/DD/YYYY',
                                message: 'The value is not a valid date'
                            }
                        }
                    },
                    basicSalary: {
                        validators: {
                            notEmpty: {
                                message: 'Basic Salary is required'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        // Use this for enabling/changing valid/invalid class
                        // eleInvalidClass: '',
                        eleValidClass: '',
                        rowSelector: '.col-sm-12'
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    autoFocus: new FormValidation.plugins.AutoFocus()
                },
                init: instance => {
                    instance.on('plugins.message.placed', function(e) {
                        if (e.element.parentElement.classList.contains('input-group')) {
                            e.element.parentElement.insertAdjacentElement('afterend', e
                                .messageElement);
                        }
                    });
                }
            });

            // FlatPickr Initialization & Validation
            const flatpickrDate = document.querySelector('[name="basicDate"]');

            if (flatpickrDate) {
                flatpickrDate.flatpickr({
                    enableTime: false,
                    // See https://flatpickr.js.org/formatting/
                    dateFormat: 'm/d/Y',
                    // After selecting a date, we need to revalidate the field
                    onChange: function() {
                        fv.revalidateField('basicDate');
                    }
                });
            }
        })();
    });

    // datatable (jquery)

    $(document).ready(function() {  
        var dt_basic_table = $('.datatables-basic'),
        dt_basic;
        var corporate_id = "{{ session('corporate_id') }}";
        $('<style>')
            .prop('type', 'text/css')
            .html(`
            .dataTables_wrapper div.dataTables_length,
            .dataTables_wrapper div.dataTables_filter {
                margin-top: 0 !important;  /* Remove top margin */
                margin-bottom: 0 !important;  /* Remove bottom margin */
            }
        `)
            .appendTo('head');

        $.ajax({
            url: '/employees/getAllEmployees',
            method: 'GET',
            success: function(response) {
                console.log(response);
                if (response.success) {
                    var employeeData = response.employeeData;
                    initializeDataTable(employeeData);
                    // Apply search filter on key press
                    $('#globalSearchInput').on('keypress', function(e) {
                        if (e.which === 13) { // Enter key
                            applySearchFilter();
                        }
                    });

                    function applySearchFilter() {
                        if (dt_basic && dt_basic.api) { // Ensure dt_basic is a DataTable instance and has the API method
                            var searchValue = $('#globalSearchInput').val(); // Get the value from the search input
                            dt_basic.search(searchValue).draw(); // Apply the search filter and redraw the table
                        }
                    }
                    // Apply search filter on button click
                    $('.btn-primary').on('click', function() {
                        if (!isDataTableInitialized) {
                            console.error('DataTable is not initialized yet!');
                            return;
                        }
                        applySearchFilter();
                    });
                }
            },
            error: function(error) {
                console.error('Failed to fetch data:', error);
            }
        });

        var isDataTableInitialized = false;
        function initializeDataTable(employeeData) {
            if (dt_basic_table.length) {
                var advancedSearchRow = `
    <tr class="advance-search mt-3">
        <th colspan="7" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Text on the left side -->
                <span class="text-muted" id="employeeTypeLabel">List by: Employee Type</span>
            </div>
        </th>
   </tr>`;
               dt_basic_table.find('thead').prepend(advancedSearchRow);
                dt_basic = dt_basic_table.DataTable({
                    data: employeeData,
                    columns: [{
                            data: 'id',
                            render: function(data, type, row) {
                                return `<strong>#${data}</strong>`;
                            }
                        },
                        {
                            data: 'first_name',
                            render: function(data, type, row) {
                                return `
                    <div class="d-flex align-items-center">
                        <img src="${assetsPath}img/avatars/3.png" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                        <div>
                            <!-- Employee Name -->
                            <strong>${data}</strong>
                            <br>
                            <!-- Employee Email with smaller font -->
                            <span style="font-size: 12px; color: #555;">${row.email}</span>
                        </div>
                    </div> `;
                            }
                        },
                        {
                            data: 'mob_num'
                        },
                        {

                            data: 'hl1_name',
                            render: function(data) {
                                return data.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
                            }

                        },
                        {
                            data: 'designation',
                            render: function(data) {
                                return data.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
                                <button class="btn btn-outline-primary btn-sm showDetailsBtn" data-id="${data.id}" style="border:none;">
                                 <i class="fa fa-eye"></i><div class="dropdown">
                                    <button
                                        type="button"
                                        class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"
                                        fdprocessedid="wwnq4q"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>                                        
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        <a class="dropdown-item waves-effect" href="javascript:void(0);">
                                        <i class="fa-solid fa-user"></i>&nbsp;Profile Details
                                        </a>
                                        <a class="dropdown-item waves-effect" href="javascript:void(0);">
                                        <i class="fa-sharp-duotone fa-regular fa-id-badge"></i>&nbsp;Corporate Details
                                        </a>
                                        <a class="dropdown-item waves-effect" href="javascript:void(0);">
                                        <i class="fa-solid fa-file"></i>&nbsp;Medical Records
                                        </a>
                                    </div>
                                    </div>
                                    `;
                            }
                        },
                        {
                            data: 'employee_type_id',
                            visible: false // Hides the column from the table view
                        },
                        {
                            data: 'hl1_id',
                            visible: false // Hides the column from the table view
                        }
                    ],
                    order: [
                        [2, 'desc']
                    ],

                    dom: '<"row d-flex justify-content-between align-items-center"' +
                        '<"col-sm-12 col-md-6 order-1"f>' +
                        '<"col-sm-12 col-md-6 order-2 text-end"l>>' +
                        'B' + // This ensures buttons are included
                        't' +
                        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 10,
                    lengthMenu: [10, 25, 50, 75, 100],
                    language: {
                        lengthMenu: "_MENU_",
                        paginate: {
                            next: '<i class="ti ti-chevron-right ti-sm"></i>',
                            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                        }
                    },
                    buttons: [{
                            extend: 'excelHtml5',
                            text: '<i class="fa-solid fa-file-excel" style="font-size:30px;"></i> ',
                            className: 'btn btn-outline-secondary me-2'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fa-solid fa-file-pdf" style="font-size:30px;"></i> ',
                            className: 'btn btn-outline-secondary me-2',
                            orientation: 'landscape', // Use landscape for more width
                            pageSize: 'A4',
                            title: 'Employee List | My HealthValet',
                            customize: function(doc) {
                                doc.content[1].table.widths = ['*', '*', '*', '*', '*', '*']; // Match all columns
                                doc.styles.tableHeader = {
                                    bold: true,
                                    fontSize: 11,
                                    color: 'black',
                                    fillColor: '#f2f2f2'
                                };
                            }
                        }
                    ],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Details of ' + data['first_name'];
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !== '' ?
                                        '<tr data-dt-row="' +
                                        col.rowIndex +
                                        '" data-dt-column="' +
                                        col.columnIndex +
                                        '">' +
                                        '<td>' +
                                        col.title +
                                        ':' +
                                        '</td> ' +
                                        '<td>' +
                                        col.data +
                                        '</td>' +
                                        '</tr>' :
                                        '';
                                }).join('');
                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    },
                    initComplete: function(settings, json) {
                        const pdfContainer = $('<div class="d-flex align-items-center ms-auto"></div>'); // Right aligned container for export buttons
                        // Append the pdfContainer to the DataTable buttons container
                        $('.dataTables_filter').append(pdfContainer);
                        const parent = $('.dataTables_wrapper .row').first();
                        const searchBox = parent.find('.dataTables_filter');
                        const lengthMenu = parent.find('.dataTables_length');

                        // Adjust layout
                        searchBox.addClass('d-flex align-items-center order-1 me-3 w-100'); // Ensure full width
                        lengthMenu.addClass('order-2 text-end ml-auto'); // Align dropdown to the end
                        parent.addClass('d-flex justify-content-between align-items-center');

                        // Modify search input
                        const searchInput = searchBox.find('input');
                        searchInput.attr('placeholder', 'EmployeeId / Name / Email / Phone / Designation'); // Add placeholder
                        searchInput.css('width', '410px');
                        searchBox.find('label').contents().filter(function() {
                            return this.nodeType === 3; // Remove "Search:" text
                        }).remove();
                        const buttonContainer = $('<div class="d-flex justify-content-between w-100 align-items-center"></div>');
                        // Add "Advance Search" and "Add New User" buttons on the left
                        const leftButtons = $('<div class="d-flex align-items-center"></div>');
                        leftButtons.append(`
                            <button id="advanceSearchBtn" class="btn btn-primary me-2" style="white-space: nowrap; margin-left: 10px;">
                                Advance Search &nbsp;<i class="ti ti-search"></i>
                            </button>
                        `);
                        leftButtons.append(`
                <a href="https://login-users.hygeiaes.com/corporate/add-corporate-user" style="white-space: nowrap;" class="btn btn-primary me-2" target="_blank">
                    <i class="ti ti-plus"></i> Add New User
                </a>
            `);
                        // Add export buttons (Excel and PDF) on the right
                        const rightButtons = $('<div class="d-flex align-items-center ms-auto"></div>'); // Use `ms-auto` to push to the right
                        $('.dt-buttons')
                            .removeClass('flex-wrap') // Remove flex-wrap to ensure buttons are inline
                            .addClass('d-flex align-items-center'); // Ensure proper alignment
                        $('.dt-buttons').appendTo(rightButtons);
                        buttonContainer.append(leftButtons);
                        buttonContainer.append(rightButtons);
                        // Append the button container to the search box
                        searchBox.append(buttonContainer);
                        const row = $('.dataTables_wrapper .row');
                        const lengthMenuElement = row.find('.dataTables_length');
                        // Find the target <tr> and add the lengthMenu to the right side
                        const advanceSearchRow = $('tr.advance-search .d-flex.justify-content-between.align-items-center');
                        advanceSearchRow.append(lengthMenuElement.addClass('ms-auto'));
                        if ($('#filtersSection').length === 0) {
                            $('.dataTables_wrapper .row.d-flex.justify-content-between.align-items-center').after(`
            <div id="filtersSection" class="filters-section d-flex align-items-center mt-3 d-none" style="margin-left:35px;">
                <select id="select2employeeType" class="select2 form-select" data-allow-clear="true" data-select2-id="select2Basic" tabindex="-1" aria-hidden="true" style="margin-left:35px;">
                    <option value="">Employee Type</option>
                </select>&nbsp;&nbsp;&nbsp;&nbsp;
                <select id="select2contractor" class="select2 form-select" data-allow-clear="true" data-select2-id="select2Basic" tabindex="-1" aria-hidden="true" disabled style="margin-left:35px !important;">
                     <option value="">Select Contractor / Vendor</option>
                </select>  &nbsp;&nbsp;&nbsp;&nbsp;        
                <select id="select2department" class="select2 form-select" data-allow-clear="true" data-select2-id="select2Basic" tabindex="-1" aria-hidden="true">
                    <option value="">Select Department</option>
                </select>
                    
                
                <button type="submit" class="btn btn-primary me-2" style="margin-left:10px;height:46px;">
                    <i class="ti ti-search"></i>
                </button>
            </div>
            <br><br>
        `);
        $('#select2employeeType').select2();
    $('#select2contractor').select2();
    $('#select2department').select2();

                        }
                        $(document).on('click', '#advanceSearchBtn', function() {
                            $('#filtersSection').toggleClass('d-none');
                            $('#advanceSearchBtn').addClass('disabled');
                            $.ajax({
                                url: '/proxy/fetch-employee-type/' + corporate_id, // Using the Laravel proxy route
                                method: 'GET',
                                cache: false,
                                success: function(response) {
                                    console.log('Employee Types:', response);
                                    // Ensure the #filtersSection is available in the DOM
                                    if ($('#filtersSection').length > 0) {
                                        var employeeTypes = response.data;
                                        var employeeTypeSelect = $('#filtersSection #select2employeeType'); // Get the correct select element
                                        // Clear existing options
                                        employeeTypeSelect.empty();
                                        // Add the default option
                                        employeeTypeSelect.append('<option value=" ">Employee Type</option>');
                                        // Append employee types from the AJAX response
                                        employeeTypes.forEach(function(employeeType) {
                                            employeeTypeSelect.append(`<option value="${employeeType.employee_type_id}" data-checked="${employeeType.checked}">${employeeType.employee_type_name}</option>`);
                                        });
                                        employeeTypeSelect.select2();  // Re-initialize select2                                     
                                        $('#filtersSection').removeClass('d-none');
                                    } else {
                                        console.error('#filtersSection or dropdown not found in the DOM');
                                    }
                                },
                                error: function(error) {
                                    console.error('Failed to fetch employee types:', error);
                                }
                            });
                            $.ajax({
                                url: '/proxy/fetch-contractor-type/' + corporate_id, // Using the Laravel proxy route for contractor types
                                method: 'GET',
                                success: function(response) {
                                    console.log('Contractor Types:', response);
                                    var contractorTypes = response.data;
                                    var contractorDropdown = $('#select2contractor');
                                    contractorDropdown.empty();
                                    contractorDropdown.append('<option value="">Select Contractor</option>');
                                    contractorTypes.forEach(function(contractorType) {

                                        if (contractorType.active_status == 1) {
                                            contractorDropdown.append(`<option value="${contractorType.corporate_contractors_id}">${contractorType.contractor_name}</option>`);
                                         }
                                   
                                    });
                                    contractorDropdown.select2();  // Re-initialize select2
                                },
                                error: function(error) {
                                    console.error('Failed to fetch contractor types:', error);
                                }
                            });
                              
                            $.ajax({
                        url: '/proxy/fetch-department/' + corporate_id, // Using the Laravel proxy route for departments
                        method: 'GET',
                        success: function(response) {
                            console.log('Department Types:', response);
                            console.log('Department Types:', response.data);
                            var department = response.data;
                            var select2department = $('#select2department');
                            select2department.empty();
                            select2department.append('<option value="">Select Department</option>');

                            if (department && department.length > 0) {
                                department.forEach(function(department) {
                                    select2department.append(`<option value="${department.hl1_id}">${department.hl1_name}</option>`);
                                });

                                // Reinitialize Select2 and trigger 'change'
                                select2department.select2();  // Re-initialize select2
                                //select2department.trigger('change');  // Update select2 dropdown
                            } else {
                                console.log('No departments available');
                            }
                        },
                        error: function(error) {
                            console.error('Failed to fetch Department:', error);
                        }
                    });
                });
            }
        });
isDataTableInitialized = true;
let allDataBefore = [];
// Capture the original dataset when the document is ready or the table is initialized
$(document).ready(function() {
    allDataBefore = dt_basic.rows().data().toArray(); // Store the original data once
    console.log('Original data before any filtering:', allDataBefore); // Log the original data
});

$(document).on('click', '#filtersSection button[type="submit"]', function(e) {
    e.preventDefault();
    const employeeType = $('#select2employeeType').val();
    const department = $('#select2department').val();
    const contractor = $('#select2contractor').val();
    const searchValue = $('input[type="search"]').val().trim(); 
    $('#employeeTypeLabel').css('color', 'white'); // Set the label text to white
    // Clear the table data before applying any new filters
    dt_basic.clear();
    // Start with all data (deep copy to avoid mutating original array)
    let filteredData = [...allDataBefore];
    let filterLabel = 'List by:';  // Initialize the label
    // If there's a search value, apply it first
    if (searchValue) {
        console.log(`Filtering by search value: ${searchValue}`);
        filterLabel += ` ${searchValue} - `;  // Add the search value at the start of the label
        // Apply the search filter (check if the search term is in employee_id, name, or phone)
        filteredData = filteredData.filter(row => {
            return row.employee_id.includes(searchValue) ||
                   row.name.includes(searchValue) ||
                   row.phone.includes(searchValue);
        });
        console.log('Data after search filter:', filteredData); // Log filtered data after search filter
    }
    // Apply the employee type filter if it's selected
    if (employeeType !== 'Employee Type' && employeeType !== '') {
        console.log(`Filtering by Employee Type ID: ${employeeType}`);
        const employeeTypeNumber = Number(employeeType);
        filteredData = filteredData.filter(row => {
            return Number(row.employee_type_id) === employeeTypeNumber;
        });
        const employeeName = $('#select2employeeType option:selected').text();
        filterLabel += ` ${employeeName} - `;  // Add employee type to the label
        console.log('Data after employee type filter:', filteredData); // Log filtered data after employee type filter
    }
    // Apply the contractor filter if it's selected and not the default "Select Contractor"
    if (contractor && contractor !== '' && contractor !== 'Select Contractor') {
        console.log(`Filtering by Contractor ID: ${contractor}`);
        const contractorNumber = Number(contractor);
        filteredData = filteredData.filter(row => {
            return Number(row.corporate_contractors_id) === contractorNumber;
        });
        const contractorName = $('#select2contractor option:selected').text();
        filterLabel += ` ${contractorName} - `;  // Add contractor to the label
        console.log('Data after contractor filter:', filteredData); // Log filtered data after contractor filter
    }

    // Apply the department filter if it's selected
    if (department && department !== 'Select Department' && department !== '') {
        console.log(`Applying department filter: ${department}`);
        const departmentNumber = Number(department);
        filteredData = filteredData.filter(row => {
            return Number(row.hl1_id) === departmentNumber;
        });
        const departmentName = $('#select2department option:selected').text();
        filterLabel += ` ${departmentName} - `;  // Add department to the label
        console.log('Data after department filter:', filteredData); // Log filtered data after department filter
    }

    // Add filtered rows to the DataTable 
    if (filteredData.length > 0) {
        console.log('Filtered Data to be added to the table:', filteredData);
        dt_basic.rows.add(filteredData);  // Add filtered rows to DataTable
    } else {
        console.log('No matching records found for the selected filters');
        $('#employeeTypeLabel').text(`${filterLabel}: No matching records found`);
        dt_basic.draw();  // Redraw the table (empty table in this case)
        return;
    }

    // Update the label with the filtered data count (not the original count)
    const filteredCount = filteredData.length;
    if (filteredCount > 0) {
        console.log(`Filtered count: ${filteredCount}`);
        $('#employeeTypeLabel').text(`${filterLabel}: ${filteredCount} records`);  // Display the filtered count
    }

    // Finally, redraw the table
    dt_basic.draw();
    console.log('Table redrawn');
});

            }
        } //table code ends here
        $(document).on('click', '.showDetailsBtn', function() {
            var rowId = $(this).data('id');
            var rowData = dt_basic.row($(this).closest('tr')).data();
            var dob = rowData.dob;
            var doj = rowData.doj;
            var formattedDoj = formatDate(doj);
            var formattedDob = formatDate(dob);
            
            // Calculate age
            var age = calculateAge(dob);
            $('#modalemployeeId').text(rowId);
            $('#modalName').text(rowData.first_name);
            $('#modalEmail').text(rowData.email);
            $('#modalPhone').text(rowData.mob_num);
            $('#modaldob').text(formattedDob);
            $('#modalage').text(age);
            $('#modalgender').text(rowData.gender);
            $('#modalDesignation').text(rowData.designation);
            $('#modalDepartment').text(rowData.hl1_name);
            $('#modaldoj').text(formattedDoj);
            $('#modalemployee_type_name  ').text(rowData.employee_type_name);
            $('#employeeModal').modal('show');
        });

        function calculateAge(dob) {
            var birthDate = new Date(dob);
            var today = new Date();

            var age = today.getFullYear() - birthDate.getFullYear();
            var monthDifference = today.getMonth() - birthDate.getMonth();

            // If the current month is before the birth month, or if it's the birth month but the day hasn't passed yet, subtract 1 from the age
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            return age;
        }

        function formatDate(dateString) {
            var date = new Date(dateString); // Parse the date string to a Date object
            var day = date.getDate(); // Get the day
            var month = date.getMonth() + 1; // Get the month (0-indexed, so we add 1)
            var year = date.getFullYear(); // Get the year

            // Add leading zeros if necessary (e.g., 01 for January, 03 for the 3rd day)
            day = day < 10 ? '0' + day : day;
            month = month < 10 ? '0' + month : month;

            return day + '-' + month + '-' + year; // Return the formatted date
        }




        // Show/Hide Contractor dropdown based on Employee Type selection
        $(document).on('change', '#select2employeeType', function() {
            var selectedValue = $(this).val(); // Get the selected Employee Type value
            var selectedOption = $(this).find(':selected');
            var contractorDropdown = $('#select2contractor');
            var employeeTypeChecked = selectedOption.data('checked'); // Get the "checked" data attribute

            if (employeeTypeChecked == 1) {
                contractorDropdown.removeClass('d-none'); // Show the contractor dropdown
                contractorDropdown.prop('disabled', false); // Enable the contractor dropdown
            } else {
                //contractorDropdown.addClass('d-none');  // Hide the contractor dropdown
                contractorDropdown.prop('disabled', true); // Disable the contractor dropdown
            }
        });

        $('#select2contractor').addClass('d-none');
        setTimeout(() => {
            $('.dataTables_filter .form-control').removeClass('form-control-sm');
            $('.dataTables_length .form-select').removeClass('form-select-sm');
        }, 300);
    });
</script>

@endsection
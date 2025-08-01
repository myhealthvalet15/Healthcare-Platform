@extends('layouts/layoutMaster')
@section('title', 'Inventory')
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


<!-- Basic Bootstrap Table -->

<div class="card">

    <div class="d-flex justify-content-end align-items-center card-header">
    <div id="addInventoryContainer"></div>
        <div class="col-md-3" style="display: flex; gap: 10px; padding-right: 0; margin-left: 17px;">
        <!-- Status Dropdown -->
        <select title="status" id="status" class="form-select" tabindex="-1" style="width: 300px; height: 37px; margin-top: 3px;"> 
            <option value="">Select Status</option>
            <option value="1">In Use</option>
            <option value="0">Not In Use</option>
            
        </select>

        <!-- Search Button -->
        <button id="searchBtn" class="btn btn-primary" style="width: auto; height: 37px;margin-left:43px; margin-top: 3px;margin-left: 5px;">
            <i class="ti ti-search"></i> 
        </button>
    </div>

        <!-- Add Modal -->

    </div>
    <div class="card-datatable table-responsive pt-0" style="margin-top:-30px;">
        <table class="datatables-basic table">
            <thead>
                <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Inventory</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                   
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Drug Details</h5>
                <span>Drug template Id: #<span id="modaldrug_template_id"></span></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Container for image and text -->
                <div class="d-flex">
                    <!-- Left side (Text content) -->

                    <div>
                        <p><strong>Drug name :</strong> <span id="modalName"></span></p>
                        <p><strong>Drug Strength :</strong> <span id="modaldrug_strength"></span></p>
                        <p><strong>Drug Type :</strong> <span id="modalNdrug_type"></span></p>
                        <p><strong>Drug manufacturer :</strong> <span id="modaldrug_manufacturer"></span> <span id="modalgender"></span></p>
                        <p><strong>Drug Ingredients :</strong> <span id="modaldrug_ingredient"></span></p>
                        <p><strong>Restock Count :</strong> <span id="modalrestock_alert_count"></span></p>
                        <p><strong>CRD :</strong> <span id="modalcrd"></span></p>
                        <p><strong>Schedule :</strong> <span id="modalschedule"></span></p>
                        <p><strong>HSN Code :</strong> <span id="modalhsn_code"></span></p>
                        <p><strong>Amount Per Strip :</strong> <span id="modalamount_per_strip"></span></p>
                        <p><strong>Unit to Issue :</strong> <span id="modalunit_issue"></span></p>
                        <p><strong>Amount Per Tab :</strong> <span id="modalamount_per_tab"></span></p>
                        <p><strong>Discount :</strong> <span id="modaldiscount"></span></p>
                        <p><strong>SGST :</strong> <span id="modalsgst"></span></p>
                        <p><strong>CGST :</strong> <span id="modalcgst"></span></p>
                        <p><strong>IGST :</strong> <span id="modaligst"></span></p>
                        <p><strong>Bill Status :</strong> <span id="modalbill_status"></span></p>
                    </div>

                    <!-- Right side (Image with padding) -->

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>
<hr class="my-12">
<script>
    var ohcRights = {!! json_encode($ohcRights) !!};
</script>

<script>
    // Add button only if permission is 2
    if (typeof ohcRights !== 'undefined' && ohcRights.inventory == 2) {
        $('#addInventoryContainer').html(`
            <a href="/others/inventory-add" class="btn btn-secondary add-new btn-primary waves-effect waves-light" style="margin-right: 10px;">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i>
                <span style="color:#fff;">Add New Inventory</span></span>
            </a>
        `);
    }
</script>

<script>
    'use strict';
    let fv, offCanvasEl;
    let dt_basic;
    document.addEventListener('DOMContentLoaded', function(e) {
        (function() {
            const preloader = document.getElementById('preloader');
            const table = document.getElementById('drugtemplate-table');
            const tbody = document.getElementById('drugtemplate-body');

        })();

        $(function() {
            var dt_basic_table = $('.datatables-basic');
            if (dt_basic_table.length) {
                var dt_basic = dt_basic_table.DataTable({
                    ajax: {
          url: '/others/inventoryList',
          
          dataSrc: function (json) {
            if (!json.result) {
              toastr.error("Failed to fetch data: " + json.data);
              return [];
            }
            return json.data;
          },
          error: function (xhr, status, error) {
            toastr.error(error);
          }
        },
                    columns: [{
                            data: 'date',
                            title: 'Purchase Date',
                            render: function(data, type, row) {
        let date = new Date(row.date); // Keep the raw date for sorting
        
        // For sorting, return the raw date (ISO format)
        if (type === 'sort' || type === 'type') {
            return date;
        }
        
        // For display, return the formatted date in DD/MM/YYYY format
        if (type === 'display') {
            return date.toLocaleDateString('en-GB'); // Format for display only
        }
        
        return date; // Return raw date for sorting
    }
                         
                        },
                        {
                            data: 'equipment_name',
                            title: 'Equipment  Name',
                            
                        },

                    
                        {
                            data: 'manufacturers',
                            title: 'Manufacturer Name'
                        },
                        {
                            data: 'purchase_order',
                            title: 'Purchase Order',
                           
                        },

                        {
                            data: 'vendors',
                            title: 'Vendors'
                        },
                       
                       


                        {
    data: null,
    title: 'Actions',
    render: function(data, type, row) {
        if (typeof ohcRights !== 'undefined' && ohcRights.inventory == 2) {
            return `
                <a class="btn btn-sm btn-warning edit-record" 
                   data-id="${row.corporate_inventory_id}" 
                   href="/others/inventory-edit/${row.corporate_inventory_id}" 
                   style="color:#fff;">Edit</a>
            `;
        } else {
            return ''; // No permission, no button
        }
    }
}


                    ],
                    order: [
                        [0, 'desc']
                    ],
                    
                    searching: true,
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
                        filename: function () {
      // Get today's date in 'dd-mm-yyyy' format
      const today = new Date();
      const dd = String(today.getDate()).padStart(2, '0');
      const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
      const yyyy = today.getFullYear();
      const formattedDate = `${dd}-${mm}-${yyyy}`;
      
      // Construct filename as "Bio-Medical Waste - dd-mm-yyyy"
      return `Inventory-${formattedDate}`;
      
    },
                      
                        className: 'btn-link ms-3',
                        exportOptions: {
                            columns: [0,1,2,3,4]
                        },
                       
                    }],
                    responsive: true,
                    initComplete: function() {
                        var api = this.api();
                        $('.datatables-basic tbody').on('click', '.showDetailsBtn', function() {
                            var rowId = $(this).data('drug_template_id');
                            var row = api.row($(this).closest('tr'));
                            var rowData = row.data();
                            if (!rowData) {
                                console.error("Row data not found!");
                                return;
                            }
    
                            $('#modalName').text(rowData.drug_name);
                            $('#modaldrug_strength').text(rowData.drug_strength);
                            $('#modaldrug_manufacturer').text(rowData.drug_manufacturer);
                            $('#modalrestock_alert_count').text(rowData.restock_alert_count);
                            $('#modalcrd').text(rowData.crd);
                            $('#modalschedule').text(rowData.schedule);
                            $('#modalhsn_code').text(rowData.hsn_code);
                            $('#modalamount_per_strip').text(rowData.amount_per_strip);
                            $('#modalunit_issue').text(rowData.unit_issue);
                            $('#modaltablet_in_strip').text(rowData.tablet_in_strip);
                            $('#modalamount_per_tab').text(rowData.amount_per_tab);
                            $('#modaldiscount').text(rowData.discount);
                            $('#modalsgst').text(rowData.sgst);
                            $('#modalcgst').text(rowData.cgst);
                            $('#modaligst').text(rowData.igst);
                            $('#modaldrug_template_id').text(rowData.drug_template_id);
                            $('#modalbill_status').text(rowData.bill_status == 1 ? 'Active' : 'Inactive');
                            $('#employeeModal').modal('show');
                        });
                        var count = api.data().count();
                        $('#employeeTypeLabel').text(`List of Inventory (${count})`);
                        api.buttons().container().appendTo('#export-buttons');
                    }
                });
                $('#DataTables_Table_0_filter label').contents().filter(function() {
                    return this.nodeType === 3; // This filters out the text nodes (like "Search:")
                }).remove();

                // Adjust the search input width
                $('input[type="search"]').css('width', '400px');
                $('input[type="search"]').css('height', '37px');
                $('input[type="search"]').css('font-size', '15px'); 
                $('input[type="search"]').css('margin-top', '4px'); 
              

                // Move the search filter to the left of the header (if needed)
                $('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');
                $('#DataTables_Table_0_filter input').attr('placeholder', 'Search by Equipment Name / Manufacturer Name');
               
                // Find the "Add New Drug Template" button
                var existingAddButton = $('.d-flex.justify-content-end.align-items-center.card-header .add-new');

                // Append the "Add New Drug Template" button to the right end of the header
                $('.d-flex.justify-content-end.align-items-center.card-header').append(existingAddButton);

                // Move the Excel export button next to the "Add New Drug Template" button
                var excelExportButtonContainer = $('.dt-buttons.btn-group.flex-wrap');

                // Remove the existing "ms-auto" class from the add-new button (if necessary)
                existingAddButton.removeClass('ms-auto');

                // Add the Excel export button next to the "Add New Drug Template" button
                $('.d-flex.justify-content-end.align-items-center.card-header').append(excelExportButtonContainer);

                // Optionally, you can add a specific spacing or styling between the buttons if needed.
                excelExportButtonContainer.find('button')
                    .addClass('ms-3') // Add margin-left if needed
                    .removeClass('ms-3'); // Remove any previous margin that might not fit the layout

                // Modify the Excel export button appearance
                var excelExportButton = excelExportButtonContainer.find('.buttons-excel');
                excelExportButton
                    .removeClass('btn-secondary')
                    .addClass('btn-link')
                    .find('span').addClass('d-flex justify-content-center')
                    .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');

                // Optionally, adjust the layout of the "Add New Drug Template" button if needed
                existingAddButton.addClass('ms-auto');

                // Move the select dropdown to the appropriate cell
                var selectElement = $('.dataTables_length select');
                var targetCell = $('.advance-search th');
                targetCell.append(selectElement);
                $('.dataTables_length label').remove();
                selectElement.css({
                    'width': '65px',
                    'background-color': '#fff'
                });
                selectElement.addClass('ms-3');
                targetCell.find('.d-flex').append(selectElement);
                
$('#searchBtn').on('click', function() {
  
  var status = $('#status').val(); // Get the selected status

  // Initialize an empty object to store query parameters
  var queryParams = {};
  // Only add status if it's selected
  if (status) {
    queryParams.status = status;
  }
  // Log the query parameters for debugging
  console.log('Query Params:', queryParams);

  // Get the current DataTable URL and clear previous query parameters
  var newUrl = dt_basic.ajax.url().split('?')[0]; // Clear existing query parameters

  // Serialize the new queryParams and append them to the URL
  var urlWithParams = newUrl + "?" + $.param(queryParams);

  // Update the DataTable URL with the new URL containing filters
  dt_basic.ajax.url(urlWithParams).load(function() {
    // After reloading, update the count of displayed records
    var count = dt_basic.data().count();
    $('#employeeTypeLabel').text(`List of Inventory (${count})`);
  });
});


            }

            $('.datatables-basic tbody').on('click', '.edit-record', function() {
                var id = $(this).data('id');
                console.log(id);
            });
        });

    });
</script>
@endsection
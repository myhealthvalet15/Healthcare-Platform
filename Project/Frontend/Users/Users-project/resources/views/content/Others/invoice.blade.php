@extends('layouts/layoutMaster')

@section('title', 'Invoice List')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'

])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([

'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js'
])
@endsection

@section('content')
<!-- Default -->

<div class="card">
  
  <div class="card-datatable table-responsive pt-0" style="margin-top:10px;">
    <table class="datatables-basic table">    
    <thead>     
        <tr class="advance-search mt-3">
          <th colspan="9" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Invoice</span>
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
          <th></th>
          <th></th>

        </tr>
      </thead>
    </table>
  </div>
</div>
<script>
    var ohcRights = {!! json_encode($ohcRights) !!};
</script>
<script>
    // Default to no permission
    var invoicePermission = 1;
    // Get permission directly from ohcRights
    if (typeof ohcRights !== 'undefined' && ohcRights.invoice !== undefined) {
        invoicePermission = parseInt(ohcRights.invoice);
    }
</script>

<script>
  /**
   * DataTables Basic
   */

  'use strict';

  let fv, offCanvasEl;
  let dt_basic;
  document.addEventListener('DOMContentLoaded', function (e) {
    flatpickr("#date", {
      enableTime: false,
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "Y/m/d",
      allowInput: true
    });
    (function () {
      const formAddNewRecord = document.getElementById('form-add-new-record');

      setTimeout(() => {
        const newRecord = document.querySelector('.create-new'),
          offCanvasElement = document.querySelector('#add-new-record');


        // To open offCanvas, to add new record
        if (newRecord) {
          newRecord.addEventListener('click', function () {
            offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
            // Empty fields on offCanvas open
            (offCanvasElement.querySelector('.dt-full-name').value = '')
            // Open offCanvas with form
            offCanvasEl.show();
          });
        }
      }, 200);


    })();
  });

  // datatable (jquery)
  $(function () {
    var dt_basic_table = $('.datatables-basic'),
      dt_basic;

    if (dt_basic_table.length) {  
      dt_basic = dt_basic_table.DataTable({
        ajax: {
          url: 'https://login-users.hygeiaes.com/others/getInvoiceDetails',
          data: function(d) {
                    // Add date range filters to the request
                    var fromDate = $('#fromDate').val();
                    var toDate = $('#toDate').val();

                    if (fromDate) {
                        d.from_date = fromDate;
                    }
                    if (toDate) {
                        d.to_date = toDate;
                    }

                    console.log('Sending From Date:', fromDate);  // Debug log
                    console.log('Sending To Date:', toDate); 
                },
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
        columns: [
          {
            data: 'invoice_date',
            title: 'Invoice Date',
            render: function(data, type, row) {
        let date = new Date(row.invoice_date);
        
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
    data: 'vendor_name',
    title: 'Vendor Name',
    render: function(data, type, row) {
        // Check if vendor_name is empty
        if (!data || data.trim() === '') {
            return row.cash_vendor;  // Return the value from cash_vendor if vendor_name is empty
        }
        return data;  // Otherwise, display the vendor_name
    }
},

          {
            data: 'po_number',
            title: 'PO Number'
          },
          {
            data: 'invoice_number',
            title: 'Invoice Number'
          },
          {
            data: 'invoice_amount',
            title: 'Invoice Value'
          },
          {
    data: 'invoice_status',
    title: 'Status',
    render: function(data, type, row) {
        switch (data) {
            case 1:
                return 'Invoice Received';
            case 2:
                return 'OHC Verified';
            case 3:
                return 'HR Verified';
            case 4:
                return 'SES Entered';
            case 5:
                return 'Dept Verified';
            case 6:
                return 'SES Released';
            case 7:
                return 'Bill Submitted';
            case 8:
                return 'Payment Done';
            case 9:
                return 'Cash Invoice';
            case 11:
                return 'Cash Invoice';  // Custom case for 11
            default:
                return data;  // If no match, return the original data
        }
    }
}
,
{
    data: null,
    title: 'Actions',
    render: function(data, type, row) {
        if (typeof ohcRights !== 'undefined' && ohcRights.invoice == 2) {
            return `
                <a class="btn btn-sm btn-warning edit-record" 
                   data-id="${row.corporate_invoice_id}" 
                   href="/others/edit-invoice/${row.corporate_invoice_id}" 
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
        searching: true,    // Disable search
        paging: false,       // Disable pagination
        lengthChange: false,
        dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>>' +
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t' +
        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

      buttons: [
       
    
       
        {
          extend: 'excelHtml5',
          text: 'Export to Excel',
          filename: function () {
      // Get today's date in 'dd-mm-yyyy' format
      const today = new Date();
      const dd = String(today.getDate()).padStart(2, '0');
      const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
      const yyyy = today.getFullYear();
      const formattedDate = `${dd}-${mm}-${yyyy}`;
      
      // Construct filename as "Bio-Medical Waste - dd-mm-yyyy"
      return `Invoice - ${formattedDate}`;
    },
          className: 'btn btn-success d-none', // Add 'd-none' to hide the default export button
          exportOptions: {
            columns: [0,1,2,3,4,5]
          }
        }

        ],
        initComplete: function () {
          var count = dt_basic.data().count();
          $('#employeeTypeLabel').text(`List of Invoice (${count})`);
          this.api().buttons().container()
            .appendTo('#export-buttons');

        }

      });
  
      
var searchInput = $('#DataTables_Table_0_filter');
$('.card-header').prepend(searchInput);
$('#DataTables_Table_0_filter label').contents().filter(function () {
        return this.nodeType === 3; // Only target the text node (the label's text)
      }).remove();
$('#DataTables_Table_0_filter input').css('width', '377px');
$('#DataTables_Table_0_filter input').css('height', '37px');
$('#DataTables_Table_0_filter input').attr('placeholder', 'Search By Vendor Name / PO / Invoice Number');

// Create the filter row with the desired layout
var filterRow = `
<div class="row mb-2 align-items-center" style="display: flex; gap: 10px; width: 100%; flex-wrap: nowrap;">
    <!-- From Date and To Date -->
    <div class="col-md-4" style="display: flex; gap: 10px; flex-grow: 1; margin-left: 10px;">
        <input type="text" id="fromDate" class="form-control flatpickr-input" placeholder="From Date" style="width: 100%; height: 37px; margin-top: 9px;" readonly="readonly">
        <input type="text" id="toDate" class="form-control flatpickr-input" placeholder="To Date" style="width: 100%; height: 37px; margin-top: 9px;margin-left: 8px;" readonly="readonly">
    </div>
    
    <!-- Status Dropdown and Search Button -->
    <div class="col-md-3" style="display: flex; gap: 10px; padding-right: 0; margin-left: -34px;">
        <!-- Status Dropdown -->
        <select title="status" id="status" onchange="search()" class="form-select" tabindex="-1" style="width: 300px; height: 37px; margin-top: 9px;appearance: none; -webkit-appearance: none; -moz-appearance: none; "> 
            <option value="">Select Status</option>
            <option value="1">Invoice Received</option>
            <option value="2">OHC Verified</option>
            <option value="3">HR Verified</option>
            <option value="4">SES Entered</option>
            <option value="5">Dept Verified</option>
            <option value="6">SES Released</option>
            <option value="7">Bill Submitted</option>
            <option value="8">Payment Done</option>
            <option value="11">Cash Invoice</option>
        </select>

        <!-- Search Button -->
        <button id="searchBtn" class="btn btn-primary" style="width: 33px; height: 37px; margin-top: 9px;margin-left: 5px;">
            <i class="ti ti-search"></i> 
        </button>
    </div>

    <!-- Export Button - This is next to the search button -->
    <div class="col-md-2" style="flex-grow: 1; margin-left:60px;margin-top: 9px;">
        <button class="btn buttons-excel buttons-html5 btn-link" id="exportExcelBtn" tabindex="0" aria-controls="DataTables_Table_0" type="button" title="Export to Excel" style="width:30px; height: 37px;">
            <span class="d-flex justify-content-center">
                <i class="fa-sharp fa-solid fa-file-excel" style="font-size: 30px;"></i>
            </span>
        </button>
    </div>
</div>
`;

// Insert the filter row right after the search input
$('#DataTables_Table_0_filter').after(filterRow);

// Create the button row with Add New Inventory and Add New Invoice buttons
var buttonRow = `
<div class="row mb-2" style="margin-top:-36px;margin-bottom:5px;margin-right:82px;">
    <div class="col-md-12" style="display: flex; justify-content: flex-end; margin-left: 18px;">
        <a href="/others/listVendor" class="btn btn-secondary add-new btn-primary waves-effect waves-light" style="margin-right: 10px;">
            <span><span style="color:#fff;">Vendor List</span></span>
        </a>
       ${(typeof ohcRights !== 'undefined' && ohcRights.invoice == 2) ? `
    <a href="/others/add-invoice" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
        <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New Invoice</span></span>
    </a>` : ''}

    </div>
</div>
`;


// Insert the button row below the filters (search, export)
$('.card-header').after(buttonRow);




$('#searchBtn').on('click', function() {
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
  var status = $('#status').val(); // Get the selected status

  // Initialize an empty object to store query parameters
  var queryParams = {};

  // Only add fromDate if it's entered
  if (fromDate) {
    fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
    queryParams.from_date = fromDate;
  }

  // Only add toDate if it's entered
  if (toDate) {
    toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
    queryParams.to_date = toDate;
  }

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
    $('#employeeTypeLabel').text(`List of Invoice (${count})`);
  });
});


$('#exportExcelBtn').on('click', function () {
      dt_basic.button('.buttons-excel').trigger();
    });
    }
  
    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
    flatpickr("#fromDate", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    
    flatpickr("#toDate", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
  });
  
</script>
@endsection
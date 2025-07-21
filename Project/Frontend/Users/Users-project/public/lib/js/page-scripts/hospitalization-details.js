 
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
          url: 'https://login-users.hygeiaes.com/UserEmployee/getHospitalizationDetails',
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
            title: 'Hospitalization Date',
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
    title: 'Condition',
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
            title: 'Doctor Name',
          },
          {
            data: 'invoice_number',
            title: 'Hospital Name',
          },
          {
            data: 'invoice_amount',
            title: 'Attachments'
          }, 
{
    data: null,
    title: 'Actions',
    render: function(data, type, row) {
      
            return `
                <a class="btn btn-sm btn-warning edit-record" 
                   data-id="${row.corporate_invoice_id}" 
                   href="/others/edit-invoice/${row.corporate_invoice_id}" 
                   style="color:#fff;">Edit</a>
            `;
        
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
          $('#employeeTypeLabel').text(`List of Hospitalization (${count})`);
          this.api().buttons().container()
            .appendTo('#export-buttons');

        }

      });
  
      
var searchInput = $('#DataTables_Table_0_filter');
$('.card-header').prepend(searchInput);
$('#DataTables_Table_0_filter label').contents().filter(function () {
        return this.nodeType === 3; // Only target the text node (the label's text)
      }).remove();
$('#DataTables_Table_0_filter input').css('width', '325px');
$('#DataTables_Table_0_filter input').css('height', '37px');
$('#DataTables_Table_0_filter input').attr('placeholder', 'Search By Hospital Name / Doctor Name');

// Create the filter row with the desired layout
var filterRow = `
<div class="row mb-2 align-items-center" style="display: flex; gap: 10px; width: 100%; flex-wrap: nowrap;">
    <!-- From Date and To Date -->
    <div class="col-md-4" style="display: flex; gap: 10px; flex-grow: 1; margin-left: 10px;">
        <input type="text" id="fromDate" class="form-control flatpickr-input" placeholder="From Date" style="width: 60%; height: 37px; margin-top: 9px;" readonly="readonly">
        <input type="text" id="toDate" class="form-control flatpickr-input" placeholder="To Date" style="width: 60%; height: 37px; margin-top: 9px;margin-left: 8px;" readonly="readonly">
    </div>
    
    

        <!-- Search Button -->
        <button id="searchBtn" class="btn btn-primary" style="width: 33px; height: 37px; margin-top: 9px;margin-left: 5px;">
            <i class="ti ti-search"></i> 
        </button>
   

    <!-- Export Button - This is next to the search button -->
    <div class="col-md-2" style="flex-grow: 1; margin-top: 9px;">
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
            
    <a href="/UserEmployee/add" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
        <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add Hospitalization Details</span></span>
    </a>

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
    $('#employeeTypeLabel').text(`List of Hospitalization (${count})`);
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

  $('#hospital_id').on('change', function () {
    if ($(this).val() === 'other') {
        $('#hospital_name_div').show();
    } else {
        $('#hospital_name_div').hide();
    }
});
$(document).ready(function () {
    // Show/hide hospital name
    $('#hospital_id').on('change', function () {
        $('#hospital_name_div').toggle($(this).val() === 'other');
    });

    // Show/hide doctor name
    $('#doctor_id').on('change', function () {
        $('#doctor_name_div').toggle($(this).val() === 'other');
    });

    // Submit form
loadMedicalCondition();

});
function loadMedicalCondition() {
    const $conditionSelect = $('#conditionSelect');

    // Destroy Select2 if already initialized (optional but safe)
    if ($conditionSelect.hasClass("select2-hidden-accessible")) {
        $conditionSelect.select2('destroy');
    }

    // Show loading placeholder
    $conditionSelect.html('<option disabled selected>Loading...</option>');

    apiRequest({
        url: '/UserEmployee/getMedicalCondition',
        method: 'GET',
        dataType: 'json',
        onSuccess: function (response) {
            if (response.result && Array.isArray(response.data)) {
                // Add a placeholder as the first option
                let options = '<option disabled selected value="">Select condition</option>';
                
                response.data.forEach(function (condition) {
                    options += `<option value="${condition.condition_id}">${condition.condition_name}</option>`;
                });

                $conditionSelect.html(options);

                // Initialize Select2 with placeholder
                $conditionSelect.select2({
                    placeholder: 'Select condition',
                    width: '100%'
                });
            } else {
                showToast('info', 'Notice', response.message || 'No conditions found.');
                $conditionSelect.html('<option disabled>No conditions found</option>');
            }
        },
        onError: function (error) {
            showToast('error', 'Error', 'Failed to load medical conditions');
            $conditionSelect.html('<option disabled>Error loading conditions</option>');
        }
    });
}


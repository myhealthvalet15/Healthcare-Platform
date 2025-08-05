    var dtButtons = [];
   
    if (typeof ohcRights !== 'undefined' && ohcRights.invoice == 2) {
        dtButtons.push({
            text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Vendor</span>',
            className: 'create-new btn btn-primary waves-effect waves-light me-3'
        });
    }
  

  /**
   * DataTables Basic
   */

  'use strict';

  let fv, offCanvasEl;
  let dt_basic;
  document.addEventListener('DOMContentLoaded', function (e) {
    flatpickr("#po_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    (function () {
      const formAddNewRecord = document.getElementById('form-add-new-record');

      setTimeout(() => {
        const newRecord = document.querySelector('.create-new'),
          offCanvasElement = document.querySelector('#add-new-record');


        // To open offCanvas, to add new record
        
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
          url: 'https://login-users.hygeiaes.com/others/getVendorDetails',
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
            data: 'po_date',
            title: 'Date',
            render: function(data, type, row) {
        let date = new Date(row.po_date);
        
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
            title: 'Vendor '
          },
          {
            data: 'po_number',
            title: 'Purchase Order Number'
          },
          {
            data: 'po_value',
            title: 'PO Value'
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

      buttons: dtButtons,
        initComplete: function () {
          var count = dt_basic.data().count();
          $('#employeeTypeLabel').text(`List of Vendor (${count})`);
          this.api().buttons().container()
            .appendTo('#export-buttons');

        }

      });
      $('.dt-buttons').append(
        '<a href="https://login-users.hygeiaes.com/others/invoice" class="create-new btn btn-primary waves-effect waves-light me-3">' +
        
        '<span style="color:#fff;">Back to List </span>' +
        '</a>'
    );
      $('.dataTables_filter').addClass('search-container').prependTo(
        '.card-header');
      $('#DataTables_Table_0_filter label').contents().filter(function () {
        return this.nodeType === 3; // Only target the text node (the label's text)
      }).remove();
     
      $('#DataTables_Table_0_filter input').css('height', '37px');
      $('#DataTables_Table_0_filter input').attr('placeholder', 'Search by Vendor / Po Number'); 

    }
    var filterRow = `
<div class="row mb-2 align-items-center" style="display: flex;  width: 50%; flex-wrap: nowrap;">
    <!-- From Date and To Date -->
    <div class="col-md-4" style="display: flex; gap: 10px; flex-grow: 1;">
        <input type="text" id="fromDate" class="form-control flatpickr-input" placeholder="From Date" style="width: 115px; height: 37px; margin-top: 9px;" readonly="readonly">
        <input type="text" id="toDate" class="form-control flatpickr-input" placeholder="To Date" style="width:115px; height: 37px; margin-top: 9px;" readonly="readonly">
    </div>
   

        <!-- Search Button -->
        <button id="searchBtn" class="btn btn-primary" style="width: 30px; height: 37px; margin-top: 9px;margin-left:57px;">
            <i class="ti ti-search"></i> 
        </button>
        <!-- Export Button - This is next to the search button -->
    <div class="col-md-2" style="flex-grow: 1; margin-top: 9px;">
        <button class="btn buttons-excel buttons-html5 btn-link" id="exportExcelBtn" tabindex="0" aria-controls="DataTables_Table_0" type="button" title="Export to Excel" style="width:30px; height: 37px;margin-left:-17px;">
            <span class="d-flex justify-content-center">
                <i class="fa-sharp fa-solid fa-file-excel" style="font-size: 30px;"></i>
            </span>
        </button>
    </div>
    </div>

    
</div>
`;

// Insert the filter row right after the search input
$('#DataTables_Table_0_filter').after(filterRow);


    $('.create-new').on('click', function () {
      // Open the offcanvas for adding a new record
      const offCanvasElement = document.querySelector('#add-new-record');
      const bootstrapOffcanvas = new bootstrap.Offcanvas(offCanvasElement);
      bootstrapOffcanvas.show();
      
    });
    document.querySelectorAll('.dt-action-buttons.text-end.pt-6.pt-md-0').forEach(function(element) {
    element.style.width = '380px';
});
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
    $('#employeeTypeLabel').text(`List of Vendor (${count})`);
  });
});

    $('#add-vendor').on('click', function () {
      const vendor_name = document.getElementById('vendor_name').value;
      const po_number  = document.getElementById('po_number').value;
      const po_value = document.getElementById('po_value').value; 
      const po_date = document.getElementById('po_date').value;
      let formIsValid = true;

      // Remove any previous error messages and classes
      $('input').removeClass('is-invalid');
      $('div.invalid-feedback').remove();


      if (!isAlphanumeric(vendor_name)) {
        formIsValid = false;
        $('#vendor_name').addClass('is-invalid');
        $('#vendor_name').after('<div class="invalid-feedback">Vendor Name must be alphanumeric (letters and numbers only).</div>');
      }

      if (!isAlphanumeric(po_number)) {
        formIsValid = false;
        $('#po_number').addClass('is-invalid');
        $('#po_number').after('<div class="invalid-feedback">PO Number must be alphanumeric (letters and numbers only).</div>');
      }
      if (!isNumeric(po_value)) { // Check if PO Value is numeric
        formIsValid = false;
        $('#po_value').addClass('is-invalid');
        $('#po_value').after('<div class="invalid-feedback">PO Value must be numeric.</div>');
      }
      
      // If form is not valid, return early
      if (!formIsValid) {
        return;
      }

      // If form is valid, make API request
      apiRequest({
        url: '/others/addVendor',
        method: 'POST',
        data: {
            vendor_name: vendor_name,
            po_number: po_number,
            po_value: po_value, 
            po_date: po_date
        },
        onSuccess: (response) => {
          console.log("API Response:", response);

          if (response.result) {
            showToast("success", "Vendor added successfully!");
          } else {
            showToast("error", "Failed to add Vendor!");
          }

          // Refresh DataTable
          dt_basic.ajax.reload(null, false);

          // Hide the off-canvas
          const offcanvasElement = document.getElementById('add-new-record');

          if (offcanvasElement) {
            const bootstrapOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);

            if (bootstrapOffcanvas) {
              bootstrapOffcanvas.hide();
            } else {
              // If the instance wasn't found, create a new one and hide it
              const newOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
              newOffcanvas.hide();
            }
          }

          // Ensure the backdrop is removed after the offcanvas is closed
          setTimeout(() => {
            document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
          }, 300);

          // Manually remove the `show` class (in case Bootstrap instance isn't working correctly)
          offcanvasElement.classList.remove('show');
        },

        onError: (error) => {
          console.error("API Error:", error);
          showToast("error", "An error occurred while adding Bio Waste.");
        }
      });
    });

    // Helper function to check if a value is numeric
    function isNumeric(value) {
      const numericPattern = /^[0-9]+$/; // Only digits allowed
      return numericPattern.test(value);
    }

    // Helper function to check if a value is alphanumeric
    function isAlphanumeric(value) {
      const alphanumericPattern = /^[a-zA-Z0-9]+$/; // Only letters and numbers allowed
      return alphanumericPattern.test(value);
    }
    $('#red, #yellow, #blue, #white, #issued_by, #received_by').on('focus', function () {
      $(this).removeClass('is-invalid');
      $(this).next('.invalid-feedback').remove(); // Remove error message
    });


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
  

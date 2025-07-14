@extends('layouts/layoutMaster')
@section('title', 'OTC LIST')
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
<style>
    .highlight-row {
    background-color: #ffeb3b !important; /* Yellow background color */
    color: #000; /* Black text color */
}
</style>


<!-- Basic Bootstrap Table -->

<div class="card">

  <div class="d-flex justify-content-end align-items-center card-header">
  
  <a href="{{ route('otc-add-otc') }}" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
    <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New OTC</span></span>
</a>

              <!-- Add Modal -->
     
  </div>
  <div class="card-datatable table-responsive pt-0" style="margin-top:-30px;">
    <table class="datatables-basic table">
      <thead>
        <tr class="advance-search mt-3">
          <th colspan="9" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of OTC</span>
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
          
          
        </tr>
      </thead>
    </table>
  </div>
</div>
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
    var storeSelect = $('#stores');
    var drugTypeMapping = {
    1: "Capsule",
    2: "Cream",
    3: "Drops",
    4: "Foam",
    5: "Gel",
    6: "Inhaler",
    7: "Injection",
    8: "Lotion",
    9: "Ointment",
    10: "Powder",
    11: "Shampoo",
    12: "Syringe",
    13: "Syrup",
    14: "Tablet",
    15: "Toothpaste",
    16: "Suspension",
    17: "Spray",
    18: "Test"
};

    if (dt_basic_table.length) {
        var dt_basic = dt_basic_table.DataTable({
          ajax: {
                url: 'https://login-users.hygeiaes.com/otc/listotcdetails',
                dataSrc: function(json) {
                  console.log(json); 
                    return json.result && json.data.length > 0 ? json.data : []; // Ensure an empty array if no data
                }
            },
           
            columns: [
              { 
    data: 'registry_created_at', 
    title: 'Date',
    render: function(data) {
      const date = new Date(data);
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      return `${day}-${month}-${year} ${hours}:${minutes}`;
    }
  },
  {
  data: null,
  title: 'Name (Age)',
  render: function(data, type, row) {
    const firstName = row.first_name || '';
    const lastName = row.last_name || '';
    const age = row.age ? ` (${row.age})` : '';
    return `${firstName} ${lastName}${age}`;
  }
},
            
  { 
    data: 'employee_id', 
    title: 'Employee Id'
  },
  { 
    data: 'department', 
    title: 'Department',
     render: function(data) {
      return data ? data.charAt(0).toUpperCase() + data.slice(1).toLowerCase() : '';
    }
  },
  { 
    data: 'medical_system', 
    title: 'Medical System'
  },
  { 
    data: 'symptoms', 
    title: 'Symptoms'
  },
 {
  data: 'drugs',
  title: 'Tablets',
  render: function(data) {
    if (Array.isArray(data) && data.length > 0) {
      return data.map(drug => {
        const typeName = drugTypeMapping[drug.drug_type] || "Unknown";
        const drugName = drug.drug_name.charAt(0).toUpperCase() + drug.drug_name.slice(1).toLowerCase();
        return `${drugName} ${typeName}-${drug.drug_strength}`;
      }).join(', ');
    }
    return 'N/A';
  }
}, 
  { 
    data: 'first_aid_by', 
    title: 'First Aid by'
  },
  { 
    data: 'doctor_notes', 
    title: 'Doctor Notes'
  }
],

            order: [[0, 'desc']],
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
                filename: 'OTC Export',
                className: 'btn-link ms-3',
                exportOptions: { modifier: { page: 'all' } },
                columns: null 
            }],
            responsive: true,
            initComplete: function() {
                var api = this.api();
                
                api.rows().every(function() {
                    var rowData = this.data();
                    var expiryDate = new Date(rowData.expiry_date);
                    var today = new Date();
                    var diffTime = expiryDate - today;
                    var diffDays = Math.ceil(diffTime / (1000 * 3600 * 24)); // Convert milliseconds to days

                    // Check if expiry date is between 45 and 60 days
                    if (diffDays >= 45 && diffDays <= 60) {
                        $(this.node()).addClass('highlight-row'); // Highlight the row
                    }
                });
                var count = api.data().count();
                $('#employeeTypeLabel').text(`List of OTC (${count})`);
                api.buttons().container().appendTo('#export-buttons');
            }
        });
      
        $('#DataTables_Table_0_filter label').contents().filter(function() {
    return this.nodeType === 3; // This filters out the text nodes (like "Search:")
}).remove();

$('#DataTables_Table_0_filter input').attr('placeholder', 'Name/ID/First-aid Name /Medical System/Symptoms');
// Adjust the search input width
$('input[type="search"]').css('width', '300px');  // Set width to 300px, adjust as needed
$('#DataTables_Table_0_filter input').css('height', '37px');
$('#DataTables_Table_0_filter input').css('font-size', '15px');

// Move the search filter to the left of the header (if needed)
$('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');

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
  .addClass('ms-3')  // Add margin-left if needed
  .removeClass('ms-3');  // Remove any previous margin that might not fit the layout

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

   
    }

    $('.datatables-basic tbody').on('click', '.edit-record', function() {
        var id = $(this).data('id');
        console.log(id);
    });
});



flatpickr("#modalmanufacter_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    
    flatpickr("#modalexpiry_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });

});
</script>
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts/layoutMaster')
@section('title', 'List Events')
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
  
  <a href="{{ route('add-new-events') }}" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
    <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New Events</span></span>
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
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Events</span>
            </div>
          </th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>  <th></th>


        </tr>
      </thead>
    </table>
  </div>
<div class="modal fade" id="testModal" tabindex="-1" aria-labelledby="testModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header" style="background-color:rgb(107, 27, 199);color:#fff;"> 
        <h5 class="modal-title" id="testModalLabel" style="color:#fff;margin-bottom:15px;">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="testModalBody">
        <!-- Dynamic content -->
      </div>
    </div>
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
   
    if (dt_basic_table.length) {
        var dt_basic = dt_basic_table.DataTable({
          ajax: {
                url: '/mhc/events/get-events',
                dataSrc: function(json) {
                  console.log(json); 
                    return json.result && json.data.length > 0 ? json.data : []; // Ensure an empty array if no data
                }
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
  render: function(data, type, row) {
    const fromDate = new Date(row.from_datetime).toLocaleDateString();
    const toDate = new Date(row.to_datetime).toLocaleDateString();
    return `${fromDate}<br>${toDate}`;
  }
}
,
  {
  data: 'event_name',
  title: 'Event Name',
  width: '20%',
  render: function(data, type, row) {
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
    render: function(data) {
      const empTypes = data?.employee_type_names
        ? Object.values(data.employee_type_names).join(', ')
        : 'N/A';
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
    render: function(data, type, row) {
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
    ? Object.values(details.test_names).map(test => `<li>${test}</li>`).join('')
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
      
        $('#DataTables_Table_0_filter label').contents().filter(function() {
    return this.nodeType === 3; // This filters out the text nodes (like "Search:")
}).remove();

$('#DataTables_Table_0_filter input').attr('placeholder', 'Search by Event / Guest Name');
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
$(document).on('click', '.deleteBtn', function () {
 // var eventId = $(this).data('event_id');
  var eventId = $(this).data('id'); // ✅ Correct


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
      $.ajax({
        url: `/mhc/events/delete/${eventId}`,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
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
        error: function (xhr) {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: xhr.responseJSON?.message || 'An error occurred while deleting.',
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
    console.log("Clicked Edit ID:", id);
    if (id) {
        window.location.href = `/mhc/events/edit-events/${id}`;
    } else {
        console.error("No event ID found for editing.");
    }
});

</script>
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">

@extends('layouts/layoutMaster')

@section('title', 'Corporate Users List')

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
 <style>
.isDisabled {
  color: currentColor;
  cursor: not-allowed;
  opacity: 0.5;
  text-decoration: none;
}
</style>
<div class="card">

  <div class="card-datatable table-responsive pt-0" style="margin-top:10px;">
    <table class="datatables-basic table">
      <thead>
        <tr class="advance-search mt-3">
          <th colspan="9" style="background-color:rgb(107, 27, 199);">
            <div class="d-flex justify-content-between align-items-center">
              <!-- Text on the left side -->
              <span style="color: #fff;font-weight:bold;" id="employeeTypeLabel">List of Corporate Users</span>
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

        </tr>
      </thead>
    </table>
  </div>
</div>
<script>
  /**
   * DataTables Basic
   */

  'use strict';

  let fv, offCanvasEl;
  let dt_basic;
  document.addEventListener('DOMContentLoaded', function (e) {
   
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
          url: 'https://login-users.hygeiaes.com/corporate-users/getUserDetails',

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
            "targets": 0,//column index
            "data": "first_name",
            "title": "NAME",

            "render": function (data, type, row) {
              return data + ' ' + row['last_name'];
            }
          },

          {
            data: 'email',
            title: 'EMAIL'
          },
          {
            "targets": 2,//column index
            "data": "mobile_country_code",
            "title": "MOBILE",

            "render": function (data, type, row) {
              return data + ' ' + row['mobile_num'];
            }
          },

          {
            data: 'active_status',
            title: 'STATUS',
            render: function (data, type, row) {
              // console.log(data); 
              switch (data) {
                case 1:
                  return 'Active';
                case 2:
                  return 'Inactive';
              }
            }

          }
          ,
          {
            "targets": 4,//column index
            "data": "setting",
            "title": "SETTING",

            "render": function (data, type, row) {
             // console.log(row['setting']);
              if(row['setting']=='1'){
              var $mhclink="class=isDisabled";
              var $ohclink="";
              }else if(row['setting']=='2'){
                var $ohclink="class=isDisabled";
                var $mhclink="";
              }
                return `<a `+$mhclink+` data-id="${row.id}" href="/corporate-users/mhc-rights/${row.id}" 
                               ><i class="fa-solid fa-hospital-user"></i></a>

                               <a `+$ohclink+` data-id="${row.id}" href="/corporate-users/ohc-rights/${row.id}" 
                               ><i class="fa-solid fa-suitcase-medical"></i></a>`;
              
            }
          }
          ,
          {
            data: null,
            title: 'Action',
            render: function (data, type, row) {
              return `<a class="btn btn-sm btn-warning edit-record" 
                               data-id="${row.id}" href="/corporate-users/edit-corporate-user/${row.id}" 
                               style="color:#fff;">Edit</a>&nbsp;                        
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

              return `Corporate Users`;
            },
            className: 'btn btn-success d-none', // Add 'd-none' to hide the default export button
            exportOptions: {
              columns: [0, 1, 2, 3]
            }
          }

        ],
        initComplete: function () {
          var count = dt_basic.data().count();
          $('#employeeTypeLabel').text(`List of Users (${count})`);
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
      $('#DataTables_Table_0_filter input').attr('placeholder', 'Search Admin Users');

      // Create the filter row with the desired layout
      var filterRow = `
<div class="row mb-2 align-items-center" style="display: flex; gap: 10px; width: 100%; flex-wrap: nowrap;">
   
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

       <div class="col-md-4" style="flex-grow: 1; margin-left:60px;margin-top: 9px;">
           <a href="/corporate-users/add-corporate-user" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New User</span></span>
          </a>
        </div>

</div>
`;

      // Insert the filter row right after the search input
      $('#DataTables_Table_0_filter').after(filterRow);

      // Create the button row with Add New User
      var buttonRow = `
<div class="row mb-2" style="margin-top:-36px;margin-bottom:5px;margin-right:82px;">
    <div class="col-md-12" style="display: flex; justify-content: flex-end; margin-left: 18px;">
        <a href="/others/listVendor" class="btn btn-secondary add-new btn-primary waves-effect waves-light" style="margin-right: 10px;">
            <span><span style="color:#fff;">Add / View Vendor</span></span>
        </a>
        <a href="/others/add-invoice" class="btn btn-secondary add-new btn-primary waves-effect waves-light">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i><span style="color:#fff;">Add New Invoice</span></span>
        </a>
    </div>
</div>
`;

      // Insert the button row below the filters (search, export)
      //$('.card-header').after(buttonRow);




      

      $('#exportExcelBtn').on('click', function () {
        dt_basic.button('.buttons-excel').trigger();
      });
    }

    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
   
  });

</script>
@endsection
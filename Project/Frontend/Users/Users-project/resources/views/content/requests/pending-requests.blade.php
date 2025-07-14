@extends('layouts/layoutMaster')
@section('title', 'Pending Request List')
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

<link rel="stylesheet" href="{{ asset('css/common.css') }}">



<style>
    .highlight-row {
    background-color: #ffeb3b !important; /* Yellow background color */
    color: #000; /* Black text color */
}
.group-header-row {
  background-color: #d3d3d3 !important;
  color: #31708f;
  font-weight: bold;
}

.group-header-icons {
  float: right;
}

.icon-black {
  color: #000;
}

.icon-red {
  color: red;
}

.additional-info-row {
  background-color: rgb(107, 27, 199);
  color: #fff;
}

.additional-info-cell {
  color: #fff;
  text-align: center;
}

.additional-info-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.new-header-row {
  background-color: rgb(240, 232, 232);
  color: #333;
}

.search-input-custom {
  text-align: left;
  width: 305px;
  height: 37px;
  font-size: 15px;
  margin-right: 15px;
}

.date-filter-input {
  width: 120px;
  margin-right: 17px;
}

.search-button-custom {
  width: 30px;
  height: 37px;
}

.add-prescription-btn {
  color: #fff;
}

#prescription-legend {
  width: 100%;
  height: auto;
  color: #333;
  padding: 1%;
  padding-bottom: 10px;
  font-size: 12px;
}

.legend-row {
  text-align: right;
}

.legend-icons-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

.legend-icons {
  text-align: right;
}
#searchBtn{width: 30px; height: 37px;}
.issued-input {
  max-width: 60px;
  padding: 2px 4px;
  
}

.dt-row-grouping td, .dt-row-grouping th {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 4px 6px;
  
}
/* Remove table borders and header background */
#editModalBody table {
  border-collapse: collapse;
  width: 100%;
  font-family: Arial, sans-serif;
  border: none;
}

#editModalBody th, #editModalBody td {
  padding: 8px 12px;
  text-align: left;
  border: none;
}





/* Modal styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  padding-top: 120px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4); /* Fade background */
}

.modal-content {
  background-color: #fff;
  margin: auto;
  padding: 20px;
  border-radius: 5px;
  position: relative;
  width: 80%;
  max-width: 600px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}



/* Align buttons to right */
.modal-content > div:last-child {
  text-align: right;
}

</style>





<!-- Modal -->
<div id="issueModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div id="modalBody"></div>
    <div style="margin-top: 20px;">
      <button id="confirmIssue" class="btn btn-primary">Issue</button>
      <button id="cancelIssue" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>

<div id="editIssueModal" class="modal">
  <div class="modal-content">
   
    <div id="editModalBody"></div>
    <div style="margin-top: 20px;">
      <button id="confirmEditIssue" class="btn btn-primary">Confirm</button>
      <button id="cancelEditIssue" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>




<div class="card">

  <div class="card-datatable table-responsive">
    
    <table class="dt-row-grouping table">
      
      
    </table>
  </div>
</div>
<!--/ Row grouping -->


<hr class="my-12"><script>
/**
 * DataTables Basic
 */

 'use strict';

let fv, offCanvasEl;
// datatable (jquery)
$(function () {
  
  var dt_basic_table = $('.datatables-basic'),   
    dt_row_grouping_table = $('.dt-row-grouping');   

  var groupColumn = 0; // Use `prescription_id` to group
  var sortColumn = 1;
  if (dt_row_grouping_table.length) {
    var groupingTable = dt_row_grouping_table.DataTable({
      ajax: {
        url: 'https://login-users.hygeiaes.com/prescription/getAllEmployeePrescription',
        data: function (d) {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            if (fromDate) {
              d.from_date = fromDate;
            }
            if (toDate) {
              d.to_date = toDate;
            }
            console.log('Sending From Date:', fromDate);
            console.log('Sending To Date:', toDate);
          },
        dataSrc: function(json) {
  console.log(json);
  if (json.result && json.data.length > 0) {
    let formattedData = [];

    // 1️⃣ Filter prescriptions that don't have order_status = 1
    let filtered = json.data.filter(prescription => prescription.order_status !== 1);

    // 2️⃣ Flatten the data into formattedData
    filtered.forEach(prescription => {
      prescription.prescription_details.forEach(detail => {
        formattedData.push({
          prescription_id: prescription.prescription_id,
          op_registry_id: prescription.op_registry_id,
          employee_name: `${prescription.employee.employee_firstname} ${prescription.employee.employee_lastname}`,
          employee_age: prescription.employee.employee_age,
          employee_gender: prescription.employee.employee_gender,
          employee_id: prescription.employee.employee_id,
          drug_name: detail.drug_name,
          drug_strength: detail.drug_strength,
          drug_type: detail.drug_type,
          prescribed_days: detail.prescribed_days,
          morning: detail.morning,
          afternoon: detail.afternoon,
          evening: detail.evening,
          night: detail.night,
          to_issue: detail.to_issue,
          intake_condition: detail.intake_condition,
          remarks: detail.remarks,
          UserId: prescription.prescription_id,
          master_doctor_id: prescription.master_doctor_id,
          doctor_firstname:prescription.doctor_firstname,
          doctor_lastname:prescription.doctor_lastname,
          prescription_type: detail.prescription_type,
          prescription_details_id: detail.prescription_details_id,
          current_availability: detail.current_availability,
          fav_pharmacy: prescription.fav_pharmacy,
          drug_template_id: detail.drug_template_id
        });
      });
    });

    // 3️⃣ Sort the flattened data DESC by prescription_id
    //formattedData.sort((a, b) => b.prescription_id.localeCompare(a.prescription_id));

    return formattedData;
  }
  return [];
}


      },
      columns: [
    
        { data: 'prescription_id' },
        { data: 'prescription_details_id' }, // Display `prescription_id` in the row header
        {
    data: 'drug_name',
    render: function(data, type, row) {
    let drugName = row.drug_name || row.drugNameById || 'No drug information available';
    drugName = drugName.charAt(0).toUpperCase() + drugName.slice(1).toLowerCase();

    const drugTypeMapping = {
        1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel",
        6: "Inhaler", 7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder",
        11: "Shampoo", 12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste",
        16: "Suspension", 17: "Spray", 18: "Test"
    };

    const drugType = drugTypeMapping[row.drug_type] || '';
    let drugDetails = `${drugName}${row.drug_strength ? ' - ' + row.drug_strength : ''}`;
    if (drugType) drugDetails += ` (${drugType})`;

    if (row.prescription_type === 'Type2') {
        drugDetails += ` <i class="fa fa-external-link" title="Print Outside Prescription" alt="Print Prescription" style="color:#000;"></i>`;
    }

    // ✅ Keep this on one line
    return `<span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">${drugDetails}</span>`;

  }

},



        {data: 'prescribed_days'},
        { data: 'morning'},
        { data: 'afternoon'},
        { data: 'evening'},
        { data: 'night'},
        {
  data: 'intake_condition',
 
  render: function(data, type, row) {
      const conditionMap = {
          '1': 'Before Food',
          '2': 'After Food',
          '3': 'With Food',
          '4': 'SOS',
          '5': 'Stat'
      };
      // Return the mapped condition based on the 'data' ID, or a default value if not found
      return conditionMap[data] || '-Select-';
  }
},

        { data: 'remarks' },
        {
  data: 'to_issue',
  render: function (data, type, row) {
    if (row.prescription_type == 2 || row.prescription_type === "2" || row.prescription_type === "Type2") {
      return '-';
    }

    return parseInt(data) || 0;
  }
},

{
  data: null,
  render: function (data, type, row) {
    // Handle outside prescriptions
    if (row.prescription_type == 2 || row.prescription_type === "2" || row.prescription_type === "Type2") {
      return '-';
    }

    // Otherwise, show current availability
    return row.current_availability ?? 0;
  }
},
{
  data: null,
  render: function (data, type, row) {
    // Normalize and sanitize values
    const toIssue = (row.to_issue || '0').toString().trim();
    const availability = (row.current_availability || '0').toString().trim();

    console.log('Debug toIssue:', toIssue, 'availability:', availability);

    // If it's an outside prescription, show "-"
    if (row.prescription_type == 2 || row.prescription_type === "2" || row.prescription_type === "Type2") {
      return '-';
    }

   
    if (toIssue === '0' || availability === '0') {
      return '';
    }

    // ✅ Otherwise, show the input box
    return `<input type="number" min="0" class="form-control form-control-sm issued-input" 
              data-id="${row.prescription_details_id}" 
              style="width: 60px; padding: 2px 4px; font-size: 12px;" 
              value="${row.issued || ''}" />`;
  }
},



        { data: "employee_id", visible: false, searchable: true },
        { data: "employee_name", visible: false, searchable: true  }
      ],
      columnDefs: [
        {
          // For Responsive
           targets: [3, 4, 5, 6, 7], // Indices for Days, Morning, Afternoon, Evening, Night
           width: '50px',
          className: 'text-center',
        
        },
        {
    targets: 2, // Drug name column index
    width: '180px',
    className: 'text-truncate' // Bootstrap class to handle overflow gracefully
  },
  {
    targets: 9, // Drug name column index
    width: '80px',
    className: 'text-truncate' // Bootstrap class to handle overflow gracefully
  },
  {
  targets: 10, // Adjust to actual index of 'Available'
  className: 'text-center',
},
  {
  targets: 11, // Adjust to actual index of 'Available'
  className: 'text-center',
},
  {
  targets: 12, // Adjust to actual index of 'Available'
  width: '60px',
  className: 'text-center text-truncate'
},
{
  targets: 13, // Issued Input
  width: '60px',
  className: 'text-center'
},

 
       
  
  
        { visible: false, targets: groupColumn },
        { visible: false, targets: sortColumn }
      ],
     
      dom: '<"row"<"col-sm-12 col-md-9 d-flex justify-content-start"f><"col-sm-12 col-md-3 d-flex justify-content-end"l>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>', 
      displayLength: 10,
      // 0 is the index of the 'prescription_id' column

  lengthMenu: [10, 25, 50, 75, 100],
  language: {
    paginate: {
      next: '<i class="ti ti-chevron-right ti-sm"></i>',
      previous: '<i class="ti ti-chevron-left ti-sm"></i>'
    },
    lengthMenu: "_MENU_" // Keep the dropdown but remove the label "Show entries"
  },
  order: [],
  drawCallback: function (settings) {
  var api = this.api();
  var rows = api.rows({ page: 'current' }).nodes();
  var data = api.rows({ page: 'current' }).data().toArray();
  var last = null;

  let groupRowEndIndices = {}; // Track the last row index for each group

  api.column(groupColumn, { page: 'current' }).data().each(function (group, i) {
    var rowData = data[i];
    if (!rowData) return;

    if (last !== group) {
      // Insert custom group header
      
      var additionalRow = `<tr class="additional-info" style="line-height: 35px;background-color: rgb(107, 27, 199); color:#fff;">
  <td colspan="12" style="color:#fff; text-align:center;">
    <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
      <span style="flex: 1; text-align: left;">${rowData.employee_name} - ${rowData.employee_age} / ${rowData.employee_gender} (${rowData.employee_id})</span>
      <span style="position: absolute; left: 50%; transform: translateX(-50%); white-space: nowrap;">
        <span style="color: white;">${rowData.master_doctor_id}</span> 
        <span style="color: yellow;">${group}</span>
      </span>
      <span style="flex: 1; text-align: right; color: white;">
        Dr. ${rowData.doctor_firstname} ${rowData.doctor_lastname}
      </span>
    </div>
  </td>
</tr>`;

      var newHeaderRow = `<tr class="new-header-row" style="line-height: 35px;background-color:rgb(240, 232, 232); color: #333;">
        <th>Drug Name - Strength (Type)</th>
        <th>Days</th>
        <th><img src="https://www.hygeiaes.co/img/Morning.png"></th>
        <th><img src="https://www.hygeiaes.co/img/Noon.png"></th>
        <th><img src="https://www.hygeiaes.co/img/Evening.png"></th>
        <th><img src="https://www.hygeiaes.co/img/Night.png"></th>
        <th>AF/BF</th>
        <th>Remarks</th>
        <th>To Issue</th>
        <th>Available</th>
        <th>Issued</th>
      </tr>`;

      $(rows[i]).before(additionalRow);
      $(rows[i]).before(newHeaderRow);
      last = group;
    }

    // Remember last row index per group
    groupRowEndIndices[group] = i;
  });

  // Add bottom padding and action buttons after the last row of each group
  Object.values(groupRowEndIndices).forEach(idx => {
    const $lastRow = $(rows[idx]);

    // Add bottom spacing visually
    $lastRow.css('line-height', '30px');

    // Insert the button row
    const buttonRow = `
      <tr class="action-buttons-row" style="background-color: #f1f1f1;">
        <td colspan="12" style="text-align: right; padding: 10px;">
          <button class="btn btn-sm btn-success me-2 btn-group-edit" style="background:#4444e5;">Issue Partly</button>
          <button class="btn btn-sm btn-success me-2 btn-group-delete" style="background:#4444e5;">Close Prescription</button>
        </td>
      </tr>`;

    $lastRow.after(buttonRow);
  });
},
responsive: {
    details: {
        display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
                var data = row.data();
                // Check if employee_name is available, if not, fallback to employee_id
                var employeeInfo = data['employee_name'] ? data['employee_name'] : data['employee_id'];
                return 'Details of ' + employeeInfo; // Show either employee_name or employee_id
            }
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
                return col.title !== '' // Do not show row in modal popup if title is blank
                    ? '<tr data-dt-row="' +
                        col.rowIndex +
                        '" data-dt-column="' +
                        col.columnIndex +
                        '">' +
                        '<td>' +
                        col.title +
                        ':</td> ' +
                        '<td>' +
                        col.data +
                        '</td>' +
                        '</tr>'
                    : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
        }
    }
}

    });
    
    $('#DataTables_Table_0_filter label').contents().filter(function () {
        return this.nodeType === 3;
    }).remove();

    // Style the search input
    const $searchInput = $('#DataTables_Table_0_filter input');
    $searchInput
        .attr('placeholder', 'Employeed Id / Name / Prescription Id')
        .css({
            'text-align': 'left',
            'width': '305px',
            'height': '37px',
            'font-size': '15px',
            'margin-right': '15px'
        });

    // Create the additional filter elements inline
    const filterInline = `
        <input type="text" id="fromDate" class="form-control flatpickr-input" placeholder="From Date" style="width: 120px; margin-right: 17px;">
        <input type="text" id="toDate" class="form-control flatpickr-input" placeholder="To Date" style="width: 120px; margin-right: 17px;">
        <select id="doctorFilterDropdown" class="select-control" style="width: 160px;height: 37px; color: var(--bs-body-color);   margin-left: 12px;    border-radius: var(--bs-border-radius);border: var(--bs-border-width) solid #d1d0d4;">
        <option value="">All Doctors</option>
        <option value="DOC001">Dr. John Doe</option>
        <option value="DOC002">Dr. Jane Smith</option>
        <!-- Add more doctors dynamically if needed -->
        </select>
        <button id="searchBtn" class="btn btn-primary" style="margin-left:15px;">
            <i class="ti ti-search"></i>
        </button>
    `;
      $searchInput.after(filterInline);
      
    $('#DataTables_Table_0 thead tr').remove();
    // Handle right side: select + Add button
    const $select = $('#DataTables_Table_0_length select').detach(); // 👈 removes dropdown only, keeps label and children


 

const iconLegend = `
  <div id="prescription-legend">
    <div class="legend-row">
      <img src="https://www.hygeiaes.co/img/Morning.png" align="absmiddle"> Morning &nbsp;&nbsp;
      <img src="https://www.hygeiaes.co/img/Noon.png" align="absmiddle"> Noon &nbsp;&nbsp;
      <img src="https://www.hygeiaes.co/img/Evening.png" align="absmiddle"> Evening &nbsp;&nbsp;
      <img src="https://www.hygeiaes.co/img/Night.png" align="absmiddle"> Night
    </div>
    
  </div>`;


$('#DataTables_Table_0_wrapper .row').first().append(iconLegend);

    $('.dt-row-grouping tbody').on('click', 'tr.group', function () {
      var currentOrder = groupingTable.order()[0];
      if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
        groupingTable.order([groupColumn, 'desc']).draw();
      } else {
        groupingTable.order([groupColumn, 'asc']).draw();
      }
    });
  }
  $('#searchBtn').on('click', function() {
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  // Convert to backend-friendly format
  fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
  toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');

  // Update DataTable's ajax with new params and reload
  groupingTable.ajax.reload(); // ✅ This is all you need — DataTable will call `data:` function again!
});
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
 


  flatpickr("#fromDate", {
        dateFormat: "d/m/Y",
        maxDate: "today" // Set format to DD/MM/YYYY
    });
    
    flatpickr("#toDate", {
        dateFormat: "d/m/Y", 
        maxDate: "today"// Set format to DD/MM/YYYY
    });
    let pendingEditPayload = [];
    let currentEditGroup = null;
    $(document).on('click', '.btn-group-edit', function () {
  console.log('Edit Group button clicked');

  //const $group = $(this).closest('tbody');
  currentEditGroup = $(this).closest('tbody');
  const issuedInputs = currentEditGroup.find('.issued-input');
  const payload = [];

  // Start table HTML
  let modalHTML = `
  <table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
    <thead>
      <tr style="background-color: rgb(107, 27, 199);
    color: #fff; font-weight: bold; line-height: 35px;">
        <th colspan="3" style="padding: 8px 12px; font-size: 16px; text-align: left;">Issue Drugs</th>
      </tr>
      <tr style="background-color: rgb(240, 232, 232); color: #000;">
        <th style="padding: 8px 12px; text-align: left;">Drug Name</th>
        <th style="padding: 8px 12px; text-align: left;">To Issue</th>
        <th style="padding: 8px 12px; text-align: left;">Available</th>
      </tr>
    </thead>
    <tbody>
`;



  const drugTypeMapping = {
    1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel",
    6: "Inhaler", 7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder",
    11: "Shampoo", 12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste",
    16: "Suspension", 17: "Spray", 18: "Test"
  };

  issuedInputs.each(function () {
    const $input = $(this);
    const value = $input.val();

    if (value && parseInt(value) > 0) {
      const prescriptionDetailsId = $input.data('id');
      const rowData = groupingTable.row($input.closest('tr')).data();
      if (!rowData) return;

      // Build payload
      payload.push({
        prescription_id: rowData.prescription_id,
        ohc_pharmacy_id: rowData.fav_pharmacy,
        prescription_details_id: prescriptionDetailsId,
        issued_quantity: parseInt(value),
        drug_template_id: rowData.drug_template_id,
        drug_name: rowData.drug_name,
        available_quantity: rowData.current_availability ?? 0,
        order_status: 2 // Example: different order status for edit
      });

      // Format drug display
      let drugName = rowData.drug_name || rowData.drugNameById || 'No drug information available';
      drugName = drugName.charAt(0).toUpperCase() + drugName.slice(1).toLowerCase();
      const drugType = drugTypeMapping[rowData.drug_type] || '';
      let drugDetails = `${drugName}${rowData.drug_strength ? ' - ' + rowData.drug_strength : ''}`;
      if (drugType) drugDetails += ` (${drugType})`;
      if (rowData.prescription_type === 'Type2') {
        drugDetails += ` <i class="fa fa-external-link" title="Print Outside Prescription" style="color:#000;"></i>`;
      }

      // Append table row
      modalHTML += `
  <tr>
    <td>${drugDetails}</td>
    <td>${value}</td>
    <td>${rowData.current_availability ?? 'N/A'}</td>
  </tr>
`;
    }
  });

  modalHTML += '</tbody></table>';

  if (payload.length === 0) {
    alert("Please enter issued quantity for at least one drug.");
    return;
  }

  pendingEditPayload = payload;
  $('#editModalBody').html(modalHTML);
  $('#editIssueModal').fadeIn();
});
$('#cancelEditIssue').on('click', function () {
  $('#editIssueModal').fadeOut();
});

// Close modal on clicking outside
$(window).on('click', function (e) {
  const modal = document.getElementById('editIssueModal');
  if (e.target === modal) {
    // Reset the .issued-input values
    currentEditGroup.find('.issued-input').val('');
    // Optionally, reset any other elements like validation messages or custom states
    currentEditGroup.find('.error').removeClass('error');
    // Close the modal
    $(modal).fadeOut();
  }
});

$('#editIssueModal').on('hidden.bs.modal', function () {
  // Clear all input fields within the modal
  $(this).find('.issued-input').val('');
  // Optionally, reset any other elements like validation messages or custom states
  $(this).find('.error').removeClass('error');
});



$('#confirmEditIssue').on('click', function () {
  const payloadToSend = pendingEditPayload.map(item => ({
    ...item,
    order_status: 0 // Confirm edit with specific status
  }));

  $.ajax({
    type: 'POST',
    url: 'https://login-users.hygeiaes.com/requests/issue-partly-prescription', // You can change this endpoint if needed
    data: JSON.stringify(payloadToSend),
    contentType: 'application/json',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {
      toastr.success('Prescription submitted successfully!', 'Success');
      groupingTable.ajax.reload(null, false);
    },
    error: function (xhr, status, error) {
      console.error(error);
      alert("Error editing prescription.");
    }
  });

  $('#editIssueModal').fadeOut();
});

$('#cancelEditIssue').on('click', function () {
  $('#editIssueModal').fadeOut(function () {
    if (currentEditGroup) {
      currentEditGroup.find('.issued-input').val('');
      currentEditGroup = null; // clear the reference
    }
  });
});



let pendingPayload = [];
let currentCloseGroup = null;
$(document).on('click', '.btn-group-delete', function () {
  currentCloseGroup = $(this).closest('tbody');
  const issuedInputs = currentCloseGroup.find('.issued-input');
  const payload = [];
  let modalHTML = `
  <table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
    <thead>
      <tr style="background-color: rgb(107, 27, 199);
    color: #fff; font-weight: bold; line-height: 35px;">
        <th colspan="3" style="padding: 8px 12px; font-size: 16px; text-align: left;">Issue Drugs</th>
      </tr>
      <tr style="background-color: rgb(240, 232, 232); color: #000;">
        <th style="padding: 8px 12px; text-align: left;">Drug Name</th>
        <th style="padding: 8px 12px; text-align: left;">To Issue</th>
        <th style="padding: 8px 12px; text-align: left;">Available</th>
      </tr>
    </thead>
    <tbody>
`;



  const drugTypeMapping = {
    1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel",
    6: "Inhaler", 7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder",
    11: "Shampoo", 12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste",
    16: "Suspension", 17: "Spray", 18: "Test"
  };

  issuedInputs.each(function () {
    const $input = $(this);
    const value = $input.val();
    if (value && parseInt(value) > 0) {
      const prescriptionDetailsId = $input.data('id');
      const rowData = groupingTable.row($input.closest('tr')).data();

      if (!rowData) return;

      payload.push({
        prescription_id: rowData.prescription_id,
        ohc_pharmacy_id: rowData.fav_pharmacy,
        prescription_details_id: prescriptionDetailsId,
        issued_quantity: parseInt(value),
        drug_template_id: rowData.drug_template_id,
        drug_name: rowData.drug_name,
       // available_quantity: rowData.available_quantity
        available_quantity: rowData.current_availability ?? 0,
        order_status: 1

      });
      let drugName = rowData.drug_name || rowData.drugNameById || 'No drug information available';
      drugName = drugName.charAt(0).toUpperCase() + drugName.slice(1).toLowerCase();
      const drugType = drugTypeMapping[rowData.drug_type] || '';
      let drugDetails = `${drugName}${rowData.drug_strength ? ' - ' + rowData.drug_strength : ''}`;
      if (drugType) drugDetails += ` (${drugType})`;
      if (rowData.prescription_type === 'Type2') {
        drugDetails += ` <i class="fa fa-external-link" title="Print Outside Prescription" style="color:#000;"></i>`;
      }
      modalHTML += `
  <tr>
    <td style="padding: 8px 12px;">${drugDetails}</td>
    <td style="padding: 8px 12px;">${value}</td>
    <td style="padding: 8px 12px;">${rowData.current_availability ?? 'N/A'}</td>
  </tr>
`;

    }
  });

  modalHTML += '</tbody></table>';

  if (payload.length === 0) {
    alert("Please enter issued quantity for at least one drug.");
    return;
  }

  pendingPayload = payload; // Store to use on confirm
  $('#modalBody').html(modalHTML);
  $('#issueModal').fadeIn();
});
$('#cancelIssue').on('click', function () {
  $('#issueModal').fadeOut();
});


$(window).on('click', function (e) {
  const modal = document.getElementById('issueModal');
  if (e.target === modal) {
    // Reset the .issued-input values
    currentEditGroup.find('.issued-input').val('');
    // Optionally, reset any other elements like validation messages or custom states
    currentEditGroup.find('.error').removeClass('error');
    // Close the modal
    $(modal).fadeOut();
  }
});

$('#confirmIssue').on('click', function () {
  const payloadToSend = pendingPayload.map(item => ({
    ...item,
    order_status: 1
  }));
  $.ajax({
    type: 'POST',
    url: 'https://login-users.hygeiaes.com/requests/close-prescription',
    data: JSON.stringify(pendingPayload),
    contentType: 'application/json',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {
      toastr.success('Prescription Closed successfully!', 'Success');
      groupingTable.ajax.reload(null, false);
    },
    error: function (xhr, status, error) {
      console.error(error);
      alert("Error submitting prescription issue.");
    }
  });
  $('#issueModal').fadeOut();
});
$('#cancelIssue').on('click', function () {
  $('#issueModal').fadeOut();
});
$('#cancelIssue').on('click', function () {
  $('#issueModal').fadeOut(function () {
    if (currentCloseGroup) {
      currentCloseGroup.find('.issued-input').val('');
      currentCloseGroup = null; // clear the reference
    }
  });
});



const table = $('.dt-row-grouping').DataTable(); // Ensure table is initialized

$('.dt-row-grouping').on('blur', '.issued-input', function () {
  const $input = $(this);
  const id = $input.data('id');
  const val = parseInt($input.val()) || 0;

  const row = table.row($input.closest('tr')[0]);
  const rowData = row.data();

  if (!rowData) {
    console.warn('No row data found for input:', $input);
    return;
  }

  const toIssue = parseInt(rowData.to_issue);

  const available = parseInt(rowData.current_availability) || 0;
  const maxAllowed = Math.min(toIssue, available);

  // Log the key values
  console.log(`To Issue: ${toIssue}`);
  console.log(`Available: ${available}`);
  console.log(`Max Allowed: ${maxAllowed}`);

  if (val > maxAllowed) {
    alert(`You cannot issue more than (${toIssue})`);
    $input.val('');
    $input.focus();
    return;
  }

  console.log(`Issued updated for ID ${id}: ${val}`);
});


});
</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">

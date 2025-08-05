  /**
   * DataTables Basic
   */
  'use strict';

  let fv, offCanvasEl;
  $(function () {
    var dt_basic_table = $('.datatables-basic'),
      dt_row_grouping_table = $('.dt-row-grouping');
    var groupColumn = 0;
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
          dataSrc: function (json) {
            console.log(json);
            if (json.result && json.data.length > 0) {
              let formattedData = [];
              json.data.forEach(prescription => {
                prescription.prescription_details.forEach(detail => {
                  formattedData.push({
                    prescription_id: prescription.prescription_id,
                    body_part_names: prescription.body_part_names.join(', '),
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
                    intake_condition: detail.intake_condition,
                    remarks: detail.remarks,
                    UserId: prescription.prescription_id,
                    master_doctor_id: prescription.master_doctor_id,
                    doctor_firstname:prescription.doctor_firstname,
                    doctor_lastname:prescription.doctor_lastname,
                    prescription_type: detail.prescription_type,
                    prescription_details_id: detail.prescription_details_id,
                    body_part_names: prescription.body_part_names.join(', '),
                    type_of_incident:prescription.type_of_incident,
                    past_medical_history:prescription.past_medical_history,
                    registry_doctor_notes:prescription.registry_doctor_notes,
                    registry_doctor_id:prescription.registry_doctor_id


                  });
                });
              });
              return formattedData;
            }
            return [];
          }
        },
        columns: [
          { data: 'prescription_id' },
          { data: 'prescription_details_id' },
          {
            data: 'drug_name',
            render: function (data, type, row) {
              let drugName = row.drug_name || row.drugNameById;
              if (!drugName) {
                return 'No drug information available';
              }
              drugName = drugName.charAt(0).toUpperCase() + drugName.slice(1).toLowerCase();
              var drugTypeMapping = {
                1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel",
                6: "Inhaler", 7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder",
                11: "Shampoo", 12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste",
                16: "Suspension", 17: "Spray", 18: "Test"
              };
              let drugType = drugTypeMapping[row.drug_type] || '';
              let prescriptionType = row.prescription_type ? ` (${row.prescription_type})` : '';
              let drugDetails = `${drugName}${row.drug_strength ? ' - ' + row.drug_strength : ''}`;
              if (drugType) {
                drugDetails += ` (${drugType})`;
              }
              if (row.prescription_type === 'Type2') {
                drugDetails += ` <i class="fa fa-external-link" title="Print Outside Prescription" alt="Print Prescription" style="color:#000;"></i>`;
              }
              return drugDetails;
            }
          },
          { data: 'prescribed_days' },
          { data: 'morning' },
          { data: 'afternoon' },
          { data: 'evening' },
          { data: 'night' },
          {
            data: 'intake_condition',
            render: function (data, type, row) {
              const conditionMap = {
                '1': 'Before Food',
                '2': 'After Food',
                '3': 'With Food',
                '4': 'SOS',
                '5': 'Stat'
              };
              return conditionMap[data] || '-Select-';
            }
          },
          { data: 'remarks' },
          { data: "employee_id", visible: false, searchable: true },
          { data: "employee_name", visible: false, searchable: true }
        ],
        columnDefs: [
          {
            className: 'control',
            orderable: false,
            targets: 0,
            searchable: true,
            render: function (data, type, full, meta) {
              return '';
            }
          },
          { visible: false, targets: groupColumn },
          { visible: false, targets: sortColumn }
        ],
        dom: '<"row"<"col-sm-12 col-md-9 d-flex justify-content-start"f><"col-sm-12 col-md-3 d-flex justify-content-end"l>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        language: {
          paginate: {
            next: '<i class="ti ti-chevron-right ti-sm"></i>',
            previous: '<i class="ti ti-chevron-left ti-sm"></i>'
          },
          lengthMenu: "_MENU_"
        },
        drawCallback: function (settings) {
          var api = this.api();
          var rows = api.rows({ page: 'current' }).nodes();
          var last = null;
          api.column(groupColumn, { page: 'current' }).data().each(function (group, i) {
            var rowData = api.row(rows[i]).data();
            console.log(rowData.prescription_type);
            if (!rowData) return;
            var groupRows = api.rows({ page: 'current' }).data().toArray().filter(r => r.prescription_id === group);
            var hasType1 = groupRows.some(r => r.prescription_type === 'Type1');
            var hasType2 = groupRows.some(r => r.prescription_type === 'Type2');
            var type1IconColor = hasType1 ? '#000' : 'gray';
            var type2IconColor = hasType2 ? '#000' : 'gray';
             var type1Link = hasType1
  ? `<a href="javascript:void(0);" onclick="openPrintModal('${group}', 'Type1')">
      <i class="fa fa-print" title="Print Prescription" style="color:${type1IconColor}; cursor:pointer;"></i>
    </a>`
  : `<i class="fa fa-print" title="Type1 Prescription not available" style="color:${type1IconColor};"></i>`;

            var type2Link = hasType2
              ? `<a href="javascript:void(0);" onclick="openPrintModal('${group}', 'Type2')">
      <i class="fa fa-external-link" title="Print Outside Prescription" alt="Print Prescription" style="color:${type2IconColor};"></i>
    </a>`
              : `<i class="fa fa-external-link" title="Type2 Prescription not available" alt="Print Prescription" style="color:${type2IconColor};"></i>`;
            var hospitalIconColor = rowData.op_registry_id > 0 ? '#000' : 'gray';
            var employeeInfo = `<tr class="group" style="background-color: #d3d3d3 !important; color: #31708f; font-weight: bold;">
                            <td colspan="8">
                            <span style="float: right;">
                          <a style="color:${hospitalIconColor};cursor: pointer;" onclick="openHospitalPopup(
  '${rowData.op_registry_id}',
  '${rowData.body_part_names}',
  '${rowData.type_of_incident}',
  '${rowData.registry_doctor_notes}',
  '${rowData.past_medical_history}',
  '${rowData.employee_name}',
  '${rowData.employee_gender}',
  'Dr. ${rowData.doctor_firstname} ${rowData.doctor_lastname}'
)">
 <i class="fa-solid fa-hospital-user"></i></a>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <a style="color:#000;cursor: pointer;" onclick="openTestPopup('${rowData.op_registry_id}', '${rowData.employee_id}')">
    <i class="fa-solid fa-flask-vial"></i>
</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                               <a onclick="sendMailPrecription('873')"><i class="fa-solid fa-envelope" style="color:#000;"></i></a>&nbsp;&nbsp;&nbsp;
                               ${type1Link}&nbsp;&nbsp;&nbsp;
                               ${type2Link}&nbsp;&nbsp;&nbsp;
                                <a onclick="CancelPrescription('873', 'cancel')"><i class="fa fa-minus-circle" title="Delete Prescription" alt="Delete Prescription" style="color:red;"></i></a>&nbsp;&nbsp;&nbsp;
                                </span>
                            </td>              
                        </tr>`;
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

            var newHeaderRow = `<tr class="new-header-row" style="background-color:rgb(240, 232, 232); color: #333;">
                                <th>Drug Name - Strength (Type)</th>
          <th>Days</th>
          <th><img src="src="/assets/img/prescription-icons/morning.png""></th>
          <th><img src="https://www.hygeiaes.co/img/Noon.png"></th>
          <th><img src="https://www.hygeiaes.co/img/Evening.png"></th>
          <th><img src="https://www.hygeiaes.co/img/Night.png"></th>
          <th>AF/BF</th>
          <th>Remarks</th>
                            </tr>`;
            if (last !== group) {
              $(rows[i]).before(additionalRow);
              $(rows[i]).before(employeeInfo);
              $(rows[i]).before(newHeaderRow);
              last = group;
            }
          });
        },
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                var employeeInfo = data['employee_name'] ? data['employee_name'] : data['employee_id'];
                return 'Details of ' + employeeInfo;
              }
            }),
            type: 'column',
            renderer: function (api, rowIdx, columns) {
              var data = $.map(columns, function (col, i) {
                return col.title !== ''
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
      const $searchInput = $('#DataTables_Table_0_filter input');
      $searchInput
        .attr('placeholder', 'Employeed Id / Name / Prescription Id')
        .css({
          'text-align': 'left',
          'width': '320px',
          'height': '37px',
          'font-size': '15px'
          
        });
      const filterInline = `
        <input type="text" id="fromDate" class="form-control flatpickr-input" placeholder="From Date" style="width: 119px;">
        <input type="text" id="toDate" class="form-control flatpickr-input" placeholder="To Date" style="width:119px;">
        <select id="doctorFilterDropdown" class="select-control" style="width: 312px;height: 37px; color: var(--bs-body-color);   margin-left: 12px;    border-radius: var(--bs-border-radius);border: var(--bs-border-width) solid #d1d0d4;">
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
      const $select = $('#DataTables_Table_0_length select').detach();
    const prescriptionPermission = (typeof ohcRights !== 'undefined' && ohcRights.prescription !== undefined)
        ? parseInt(ohcRights.prescription)
        : 1;

    // Conditionally render the "Add New Prescription" button
    const addButton = (prescriptionPermission === 2) ? `
        <br/><br/><br/>
        <a href="https://login-users.hygeiaes.com/prescription/prescription-add"
           class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-3">
            <span>
                <i class="ti ti-plus me-0 me-sm-1 ti-xs add-prescription-btn"></i>
                <span class="add-prescription-btn">Add New Prescription</span>
            </span>
        </a>` : '';
      const $label = $('#DataTables_Table_0_length label');
      if ($label.length) {
        $label.append(addButton);
      } else {
        $('#DataTables_Table_0_length').append(addButton);
      }
      const iconLegend = `
  <div id="prescription-legend" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
  <div class="legend-row" style="display: flex; gap: 10px; align-items: center;">
    <img src="/assets/img/prescription-icons/morning.png" align="absmiddle"> Morning
    <img src="/assets/img/prescription-icons/noon.png" align="absmiddle"> Noon
    <img src="/assets/img/prescription-icons/evening.png" align="absmiddle"> Evening
    <img src="/assets/img/prescription-icons/night.png" align="absmiddle"> Night
  </div>

  <div class="legend-icons-container" style="display: flex; align-items: center; gap: 15px;">
    <div id="datatable-length-dropdown"></div>
    <div class="legend-icons">
      <i class="fa-solid fa-envelope icon-black"></i> Send Mail &nbsp;&nbsp;
      <i class="fa fa-print icon-black" title="Print Prescription"></i> Print Prescription &nbsp;&nbsp;
      <i class="fa fa-external-link icon-black" title="Print Outside Prescription"></i> Print Outside Prescription &nbsp;&nbsp;
      <i class="fa fa-minus-circle icon-red" title="Delete Prescription"></i> Delete
    </div>
  </div>
</div>
`;
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
    $('#searchBtn').on('click', function () {
      var fromDate = $('#fromDate').val();
      var toDate = $('#toDate').val();
      fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
      toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
      groupingTable.ajax.reload();
    });
    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
    flatpickr("#fromDate", {
      dateFormat: "d/m/Y",
      maxDate: "today"
    });
    flatpickr("#toDate", {
      dateFormat: "d/m/Y",
      maxDate: "today"
    });
  });
  function openHospitalPopup(id, bodyParts, incidentType, doctorNotes, pastHistory, employeeName, employeeGender, doctorName) {
  document.getElementById('hospitalModal').style.display = 'block';

  const modalContent = `
    <div class="modal-section">
      <div class="row">
        <label>Name:</label>
        <div class="value">${employeeName}</div>
      </div>
      <div class="row">
        <label>Gender:</label>
        <div class="value">${employeeGender}</div>
      </div>
      <div class="row">
        <label>Prescribed by:</label>
        <div class="value">${doctorName}</div>
      </div>
    </div>
    <div class="modal-section">
      <div class="row">
        <label>Incident Type:</label>
        <div class="value">${incidentType}</div>
      </div>
      <div class="row">
        <label>Body Parts:</label>
        <div class="value">${bodyParts}</div>
      </div>
      <div class="row">
        <label>Doctor Notes:</label>
        <div class="value">${doctorNotes}</div>
      </div>
      <div class="row">
        <label>Past History:</label>
        <div class="value">${pastHistory || '-'}</div>
      </div>
    </div>
  `;

  document.getElementById('hospitalModalBody').innerHTML = modalContent;
}

// Close function for the modal
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}


function openTestPopup(registryId, employeeId) {
    if (!registryId || !employeeId) {
        showToast('error', "Missing employee ID or registry ID");
        return;
    }

    // Optional: Show a loader or basic modal while loading data
    document.getElementById('testListModal').style.display = 'block';
    document.getElementById('labModalBody').innerText = "Fetching lab data...";

    // Ensure modal HTML is injected
    createTestModal();

    // Fetch the test data using the provided function
    fetchTestDataForEmployee(employeeId, registryId);
}


function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Optional: close when clicking outside the modal
window.onclick = function(event) {
    if (event.target.classList.contains('custom-modal')) {
        event.target.style.display = "none";
    }
}
function createTestModal() {
    if (document.getElementById('testListModal')) return;

    const modalHtml = `
    <div class="modal fade" id="testListModal" tabindex="-1" aria-labelledby="testListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testListModalLabel">Test Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="labModalBody">Loading...</p>
                    <div class="modal-header-info mt-3">
                        <div class="employee-info" id="modalEmployeeName"></div>
                        <div class="test-info">
                            <div class="date-info" id="modalTestDate"></div>&nbsp;&nbsp;
                            <div class="health-plan-status" id="modalHealthPlanStatus"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div id="modalTestList" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const styleElement = document.createElement('style');
    styleElement.textContent = `
        .test-result-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .test-result-table th, .test-result-table td {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }
        .test-result-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .test-group-title {
            font-weight: bold;
            font-size: 16px;
            margin-top: 15px;
            padding: 5px 0;
            border-bottom: 2px solid #dee2e6;
            color: #566a7f;
        }
        .test-subgroup-title {
            font-weight: 600;
            font-size: 15px;
            margin-top: 10px;
            padding: 3px 0;
            color: #697a8d;
            margin-left: 10px;
        }
        .test-subsubgroup-title {
            font-weight: 500;
            font-size: 14px;
            margin-top: 8px;
            padding: 2px 0;
            color: #697a8d;
            margin-left: 20px;
        }
        .test-item {
            margin-left: 10px;
            margin-top: 5px;
        }
        .subgroup-test-item {
            margin-left: 20px;
            margin-top: 5px;
        }
        .subsubgroup-test-item {
            margin-left: 30px;
            margin-top: 5px;
        }
        .normal-range {
            font-size: 12px;
            color: #697a8d;
        }
        .test-result-value {
            font-weight: 600;
            color: #566a7f;
        }
        .test-result-normal {
            color: #28a745;
        }
        .test-result-abnormal {
            color: #dc3545;
        }
        .test-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5px;
        }
        .health-plan-status {
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
        }
        .status-pending {
            background-color: #fff4e5;
            color: #ff9800;
        }
        .status-completed {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        .status-processing {
            background-color: #e3f2fd;
            color: #2196f3;
        }
    `;
    document.head.appendChild(styleElement);
}

function fetchTestDataForEmployee(employeeId, registryId) {
        if (!employeeId || !registryId) {
            showToast('error', "Missing employee ID or registry ID");
            return;
        }
        apiRequest({
            url: 'https://login-users.hygeiaes.com/ohc/getAllTests',
            method: 'GET',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    const employeeTests = response.data.filter(test =>
                        test.employee_id && test.employee_id.toLowerCase() === employeeId.toLowerCase()
                    );
                    if (employeeTests.length > 0) {
                        showTestListModal({
                            testStructure: JSON.stringify(employeeTests[0].tests || {}),
                            employeeName: employeeTests[0].name || 'N/A',
                            employeeId: employeeTests[0].employee_id || 'N/A',
                            employeeAge: employeeTests[0].age || 'N/A',
                            testDate: employeeTests[0].reporting_date_time || 'N/A',
                            gender: employeeTests[0].gender || 'female',
                            healthPlanStatus: employeeTests[0].healthplan_status || 'N/A'
                        });
                    } else {
                        showToast('info', "No test data found for this employee");
                    }
                } else {
                    console.warn('Error: ', response);
                    showToast('error', "Failed to load test data");
                }
            },
            onError: function (error) {
                console.error('Error loading test data:', error);
                showToast('error', "Failed to load tests: " + error);
            }
        });
    }
    function showTestListModal(dataset) {
        const modal = document.getElementById('testListModal');
        const employeeNameElement = document.getElementById('modalEmployeeName');
        const testDateElement = document.getElementById('modalTestDate');
        const healthPlanStatusElement = document.getElementById('modalHealthPlanStatus');
        const testListElement = document.getElementById('modalTestList');
        if (!modal || !employeeNameElement || !testDateElement || !healthPlanStatusElement || !testListElement) {
            console.error('Modal elements not found');
            return;
        }
        testListElement.innerHTML = '';
        employeeNameElement.textContent = `${dataset.employeeName} (${dataset.employeeAge}) - ${dataset.employeeId}`;
        if (dataset.testDate && dataset.testDate !== 'N/A') {
            const testDate = new Date(dataset.testDate);
            const formattedDate = `${testDate.getDate().toString().padStart(2, '0')}-${(testDate.getMonth() + 1).toString().padStart(2, '0')}-${testDate.getFullYear()}`;
            testDateElement.textContent = formattedDate;
        } else {
            testDateElement.textContent = 'N/A';
        }
        if (dataset.healthPlanStatus) {
            healthPlanStatusElement.textContent = dataset.healthPlanStatus;
            healthPlanStatusElement.className = 'health-plan-status';
            if (dataset.healthPlanStatus.toLowerCase() === 'pending') {
                healthPlanStatusElement.classList.add('status-pending');
            } else if (dataset.healthPlanStatus.toLowerCase() === 'completed') {
                healthPlanStatusElement.classList.add('status-completed');
            } else if (dataset.healthPlanStatus.toLowerCase() === 'processing') {
                healthPlanStatusElement.classList.add('status-processing');
            }
        } else {
            healthPlanStatusElement.textContent = 'N/A';
        }
        let testStructure = {};
        try {
            testStructure = JSON.parse(dataset.testStructure);
        } catch (e) {
            console.error('Error parsing test structure:', e);
        }
        if (Object.keys(testStructure).length > 0) {
            renderHierarchicalTests(testStructure, testListElement, dataset.gender || 'female');
        } else {
            const noTestsElement = document.createElement('div');
            noTestsElement.textContent = 'No tests available';
            testListElement.appendChild(noTestsElement);
        }
        if (typeof bootstrap !== 'undefined') {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            console.error('Bootstrap is not available');
        }
    }
    function openPrintModal(prescriptionId, groupType) {
  // Set modal info text
  $('#printModalInfo').html(`<strong>Prescription ID:</strong> ${prescriptionId} <br><strong>Type:</strong> ${groupType}`);

  // Store values in hidden inputs or data attributes
  $('#printModal').data('prescription-id', prescriptionId);
  $('#printModal').data('group-type', groupType);

  // Show the modal
  const modal = new bootstrap.Modal(document.getElementById('printModal'));
  modal.show();
}
 function printType() {
    const selectedType = document.getElementById('printType').value;

    // Get prescriptionId and groupType from modal's data attributes
    const modalElement = $('#printModal');
    const prescriptionId = modalElement.data('prescription-id');
    const groupType = modalElement.data('group-type');

    if (!prescriptionId || !selectedType) {
        console.error("Missing prescription ID or print type.");
        return;
    }

    // Construct URL
    let printUrl = `https://login-users.hygeiaes.com/prescription/prescription-print-option/${prescriptionId}?group=${groupType}&print_type=${selectedType}`;

    console.log("Redirecting to:", printUrl);

    // Redirect or open in new tab
    window.open(printUrl, '_blank');
}

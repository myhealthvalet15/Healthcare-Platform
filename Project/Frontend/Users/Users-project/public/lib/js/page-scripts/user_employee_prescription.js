function openPrintModal(prescriptionId, groupType) {
    $('#printModalInfo').html(`<strong>Prescription ID:</strong> ${prescriptionId} <br><strong>Type:</strong> ${groupType}`);
    $('#printModal').data('prescription-id', prescriptionId);
    $('#printModal').data('group-type', groupType);
    const modal = new bootstrap.Modal(document.getElementById('printModal'));
    modal.show();
}
function printType() {
    const selectedType = document.getElementById('printType').value;
    const modalElement = $('#printModal');
    const prescriptionId = modalElement.data('prescription-id');
    const groupType = modalElement.data('group-type');
    if (!prescriptionId || !selectedType) {
        console.error("Missing prescription ID or print type.");
        return;
    }
    let printUrl = `https://login-users.hygeiaes.com/prescription/prescription-print-option/${prescriptionId}?group=${groupType}&print_type=${selectedType}`;
    console.log("Redirecting to:", printUrl);
    window.open(printUrl, '_blank');
}
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
            ajax: function (data, callback, settings) {
                apiRequest({
                    url: `https://login-users.hygeiaes.com/UserEmployee/getuserPrescription?employee_id=${employeeId}`,
                    method: 'GET',
                    onSuccess: function (json) {
                        let formattedData = [];
                        if (json.result && Array.isArray(json.data)) {
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
                                        doctor_firstname: prescription.doctor_firstname,
                                        doctor_lastname: prescription.doctor_lastname,
                                        prescription_type: detail.prescription_type,
                                        prescription_details_id: detail.prescription_details_id,
                                        type_of_incident: prescription.type_of_incident,
                                        past_medical_history: prescription.past_medical_history,
                                        prescription_attachments: prescription.prescription_attachments,
                                        registry_doctor_notes: prescription.doctor_notes,
                                        registry_user_notes: prescription.user_notes,
                                        registry_doctor_id: prescription.registry_doctor_id
                                    });
                                });
                            });
                        }
                        callback({ data: formattedData });
                    },
                    onError: function () {
                        callback({ data: [] });
                        alert('Failed to load prescriptions.');
                    }
                });
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
            ],
            columnDefs: [
                {
                    className: 'control',
                    orderable: false,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return '';
                    }
                },
                { visible: false, targets: groupColumn },
                { visible: false, targets: sortColumn }
            ],
            dom: '<"row"<"col-sm-12 col-md-12 d-flex justify-content-end">>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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
                var last = null; let firstValidRow = api.rows({ page: 'current' }).data().toArray().find(r => r);
                if (firstValidRow) {
                    const employeeName = firstValidRow.employee_name || 'N/A';
                    const employeeGender = firstValidRow.employee_gender || 'N/A';
                    const employeeAge = firstValidRow.employee_age || 'N/A';
                    const addButton = `
      <a href="https://login-users.hygeiaes.com/prescription/add-employee-prescription/${employeeId}"
   class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-3">
    <span>
        <i class="ti ti-plus me-0 me-sm-1 ti-xs add-prescription-btn"></i>
        <span class="add-prescription-btn">Add New Prescription</span>
    </span>
</a>
`;
                    const employeeInfo = `
      <span class="text-dark">
        <i class="fas fa-user"></i> <strong>${employeeName}</strong> | 
        <i class="fas fa-venus-mars"></i> ${employeeGender} | 
        <i class="fas fa-birthday-cake"></i> ${employeeAge} yrs
      </span>`;
                    $('#add-prescription-button').html(addButton);
                    $('#employee-info-display').html(employeeInfo);
                }
                api.column(groupColumn, { page: 'current' }).data().each(function (group, i) {
                    var rowData = api.row(rows[i]).data();
                    if (!rowData) return;
                    var groupRows = api.rows({ page: 'current' }).data().toArray().filter(r => r.prescription_id === group);
                    var hasType1 = groupRows.some(r => r.prescription_type === 'Type1');
                    var hasType2 = groupRows.some(r => r.prescription_type === 'Type2');
                    var type1IconColor = hasType1 ? '#fff' : 'gray';
                    var type2IconColor = hasType2 ? '#fff' : 'gray';
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
                    let attachments = [];
                    try {
                        attachments = JSON.parse(rowData.prescription_attachments);
                    } catch (e) {
                        console.error("Error parsing prescription_attachments:", e);
                    }
                    let firstAttachment = attachments.length > 0 ? attachments[0] : null;
                    console.log("Raw prescription_attachments:", rowData.prescription_attachments);
                    let attachmentHTML = '';
                    if (firstAttachment) {
                        attachmentHTML = `<i class="fa-solid fa-paperclip"onclick="showAttachmentPopup('${firstAttachment}')" style="cursor:pointer;"></i>`;
                    }
                    let doctorName = (rowData.doctor_firstname && rowData.doctor_lastname)
                        ? `Dr. ${rowData.doctor_firstname} ${rowData.doctor_lastname}`
                        : 'Self';
                    let hasDoctor = doctorName !== 'Self';
                    let rightIconsHTML = '';
                    if (hasDoctor) {
                        rightIconsHTML = `
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
      <i class="fa-solid fa-hospital-user" style="color:#fff;"></i>
    </a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a style="color:#000;cursor: pointer;" onclick="openTestPopup('${rowData.op_registry_id}', '${rowData.employee_id}')">
      <i class="fa-solid fa-flask-vial" style="color:#fff;"></i>
    </a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  `;
                    }
                    var additionalRow = `<tr class="additional-info" style="line-height: 35px;background-color: rgb(107, 27, 199); color:#fff;">
  <td colspan="12" style="color:#fff; text-align:center;">
    <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
      <span style="flex: 1; text-align: left;"> 
        ${doctorName} ${attachmentHTML}
      </span>
      <span style="position: absolute; left: 50%; transform: translateX(-50%); white-space: nowrap;">
        <span style="color: white;">${rowData.master_doctor_id}</span> 
        <span style="color: yellow;">${group}</span>
      </span>
      <span style="flex: 1; text-align: right; color: white;">
        ${rightIconsHTML}
        <a onclick="sendMailPrecription('873')"><i class="fa-solid fa-envelope" style="color:#fff;"></i></a>&nbsp;&nbsp;&nbsp;
        ${type1Link}&nbsp;&nbsp;&nbsp;
        ${type2Link}&nbsp;&nbsp;&nbsp;
      </span>
    </div>
  </td>
</tr>`;
                    var newHeaderRow = `<tr class="new-header-row" style="background-color:rgb(240, 232, 232); color: #333;">
                                <th>Drug Name - Strength (Type)</th>
          <th>Days</th>
          <th><img src="https://www.hygeiaes.co/img/Morning.png"></th>
          <th><img src="https://www.hygeiaes.co/img/Noon.png"></th>
          <th><img src="https://www.hygeiaes.co/img/Evening.png"></th>
          <th><img src="https://www.hygeiaes.co/img/Night.png"></th>
          <th>AF/BF</th>
          <th>Remarks</th>
                            </tr>`;
                    let prescriptionRow = $(rows[i]);
                    let doctorNotes = rowData.registry_doctor_notes || 'No notes';
                    let userNotes = rowData.registry_user_notes || 'No notes';
                    let notesRow = `
<tr class="notes-row" style="background-color: #f9f9f9;">
  <td colspan="12" style="padding: 10px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div style="flex-shrink: 0;">
        <strong>Doctor Notes:</strong> ${doctorNotes}
      </div>
      <div style="margin-left: auto; text-align: right;">
        <strong>User Notes:</strong> ${userNotes}
      </div>
    </div>
  </td>
</tr>`;
                    if (last !== group) {
                        $(rows[i]).before(additionalRow);
                        $(rows[i]).before(newHeaderRow);
                        let currentGroupRows = api.rows({ page: 'current' }).nodes().toArray()
                            .filter((r, idx) => api.row(r).data().prescription_id === group);
                        let lastDrugRow = $(currentGroupRows[currentGroupRows.length - 1]);
                        lastDrugRow.after(notesRow);
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
        const employeeName = "{{ session('employee_name') }}";
        const employeeGender = "{{ session('employee_gender') }}";
        const employeeAge = "{{ session('employee_age') }}";
        const addButton = `
  <a href="https://login-users.hygeiaes.com/prescription/add-employee-prescription/${employeeId}"
     class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-3">
      <span>
          <i class="ti ti-plus me-0 me-sm-1 ti-xs add-prescription-btn"></i>
          <span class="add-prescription-btn">Add New Prescription</span>
      </span>
  </a>`;
        const employeeInfo = `
  <span class="text-dark">
    <i class="fas fa-user"></i> <strong>${employeeName}</strong> | 
    <i class="fas fa-venus-mars"></i> ${employeeGender} | 
    <i class="fas fa-birthday-cake"></i> ${employeeAge} yrs
  </span>`;
        $('#add-prescription-button').html(addButton);
        $('#employee-info-display').html(employeeInfo);
        const iconLegend = `
  <div id="prescription-legend" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
  <div class="legend-row" style="display: flex; gap: 10px; align-items: center;">
    <img src="https://www.hygeiaes.co/img/Morning.png" align="absmiddle"> Morning
    <img src="https://www.hygeiaes.co/img/Noon.png" align="absmiddle"> Noon
    <img src="https://www.hygeiaes.co/img/Evening.png" align="absmiddle"> Evening
    <img src="https://www.hygeiaes.co/img/Night.png" align="absmiddle"> Night
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
});
function showAttachmentPopup(imageBase64) {
    $('#prescriptionModalImage').attr('src', imageBase64);
    $('#prescriptionModal').modal('show');
}
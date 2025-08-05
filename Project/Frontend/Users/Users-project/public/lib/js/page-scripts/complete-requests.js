 /**
         * DataTables Basic
         */
        'use strict';
        let fv, offCanvasEl;
        $(function() {
            var dt_basic_table = $('.datatables-basic'),
                dt_row_grouping_table = $('.dt-row-grouping');
            var groupColumn = 0;
            var sortColumn = 1;
            if (dt_row_grouping_table.length) {
                var groupingTable = dt_row_grouping_table.DataTable({
                    ajax: {
                        url: 'https://login-users.hygeiaes.com/requests/complete-prescription',
                        data: function(d) {
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
                                let filtered = json.data.filter(prescription => prescription
                                    .order_status === 1);
                                filtered.forEach(prescription => {
                                    prescription.prescription_details.forEach(detail => {
                                        formattedData.push({
                                            prescription_id: prescription
                                                .prescription_id,
                                            op_registry_id: prescription
                                                .op_registry_id,
                                            employee_name: `${prescription.employee.employee_firstname} ${prescription.employee.employee_lastname}`,
                                            employee_age: prescription.employee
                                                .employee_age,
                                            employee_gender: prescription
                                                .employee.employee_gender,
                                            employee_id: prescription.employee
                                                .employee_id,
                                            drug_name: detail.drug_name,
                                            drug_strength: detail.drug_strength,
                                            drug_type: detail.drug_type,
                                            prescribed_days: detail
                                                .prescribed_days,
                                            morning: detail.morning,
                                            afternoon: detail.afternoon,
                                            evening: detail.evening,
                                            night: detail.night,
                                            to_issue: detail.to_issue,
                                            intake_condition: detail
                                                .intake_condition,
                                            remarks: detail.remarks,
                                            UserId: prescription
                                                .prescription_id,
                                            master_doctor_id: prescription
                                                .master_doctor_id,
                                            doctor_firstname: prescription
                                                .doctor_firstname,
                                            doctor_lastname: prescription
                                                .doctor_lastname,
                                            prescription_type: detail
                                                .prescription_type,
                                            prescription_details_id: detail
                                                .prescription_details_id,
                                            current_availability: detail
                                                .current_availability,
                                            issued_quantity: detail
                                                .issued_quantity,
                                            fav_pharmacy: prescription
                                                .fav_pharmacy,
                                            drug_template_id: detail
                                                .drug_template_id
                                        });
                                    });
                                });
                                return formattedData;
                            }
                            return [];
                        }
                    },
                    columns: [{
                            data: 'prescription_id'
                        },
                        {
                            data: 'prescription_details_id'
                        },
                        {
                            data: 'drug_name',
                            render: function(data, type, row) {
                                let drugName = row.drug_name || row.drugNameById ||
                                    'No drug information available';
                                drugName = drugName.charAt(0).toUpperCase() + drugName.slice(1)
                                    .toLowerCase();
                                const drugTypeMapping = {
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
                                const drugType = drugTypeMapping[row.drug_type] || '';
                                let drugDetails =
                                    `${drugName}${row.drug_strength ? ' - ' + row.drug_strength : ''}`;
                                if (drugType) drugDetails += ` (${drugType})`;
                                if (row.prescription_type === 'Type2') {
                                    drugDetails +=
                                        ` <i class="fa fa-external-link" title="Print Outside Prescription" alt="Print Prescription" style="color:#000;"></i>`;
                                }
                                return `<span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">${drugDetails}</span>`;
                            }
                        },
                        {
                            data: 'prescribed_days'
                        },
                        {
                            data: 'morning'
                        },
                        {
                            data: 'afternoon'
                        },
                        {
                            data: 'evening'
                        },
                        {
                            data: 'night'
                        },
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
                                return conditionMap[data] || '-Select-';
                            }
                        },
                        {
                            data: 'remarks'
                        },
                        {
                            data: 'to_issue',
                            render: function(data, type, row) {
                                if (row.prescription_type == 2 || row.prescription_type === "2" ||
                                    row.prescription_type === "Type2") {
                                    return '-';
                                }
                                const prescribedDays = parseInt(row.prescribed_days) || 0;
                                const morning = parseInt(row.morning) || 0;
                                const afternoon = parseInt(row.afternoon) || 0;
                                const evening = parseInt(row.evening) || 0;
                                const night = parseInt(row.night) || 0;
                                const totalPerDay = morning + afternoon + evening + night;
                                const toIssue = prescribedDays * totalPerDay;
                                return toIssue;
                            }
                        },
                        {
                            data: 'issued_quantity',
                            render: function(data, type, row) {
                                if (row.prescription_type == 2 || row.prescription_type === "2" ||
                                    row.prescription_type === "Type2") {
                                    return '-';
                                }
                                return data;
                            }
                        },
                        {
                            data: "employee_id",
                            visible: false,
                            searchable: true
                        },
                        {
                            data: "employee_name",
                            visible: false,
                            searchable: true
                        }
                    ],
                    columnDefs: [{
                            targets: [3, 4, 5, 6,
                                7
                            ],
                            width: '50px',
                            className: 'text-center',
                        },
                        {
                            targets: 2,
                            width: '180px',
                            className: 'text-truncate'
                        },
                        {
                            targets: 9,
                            width: '80px',
                            className: 'text-truncate'
                        },
                        {
                            targets: 10,
                            className: 'text-center',
                        },
                        {
                            targets: 11,
                            className: 'text-center',
                        },
                        {
                            targets: 12,
                            width: '60px',
                            className: 'text-center text-truncate'
                        },
                        {
                            visible: false,
                            targets: groupColumn
                        },
                        {
                            visible: false,
                            targets: sortColumn
                        }
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
                    order: [],
                    drawCallback: function(settings) {
                        var api = this.api();
                        var rows = api.rows({
                            page: 'current'
                        }).nodes();
                        var data = api.rows({
                            page: 'current'
                        }).data().toArray();
                        var last = null;
                        let groupRowEndIndices = {};
                        api.column(groupColumn, {
                            page: 'current'
                        }).data().each(function(group, i) {
                            var rowData = data[i];
                            if (!rowData) return;
                            $(rows[i]).css('line-height', '30px');
                            if (last !== group) {
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
        <th>Issued</th>
      </tr>`;
                                $(rows[i]).before(additionalRow);
                                $(rows[i]).before(newHeaderRow);
                                last = group;
                            }
                            groupRowEndIndices[group] = i;
                        });
                        Object.values(groupRowEndIndices).forEach(function(lastIndex) {
                            $(rows[lastIndex]).css('padding-bottom', '20px');
                        });
                    },
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    var employeeInfo = data['employee_name'] ? data[
                                        'employee_name'] : data['employee_id'];
                                    return 'Details of ' +
                                        employeeInfo;
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !==
                                        '' ?
                                        '<tr data-dt-row="' +
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
                                        '</tr>' :
                                        '';
                                }).join('');
                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }
                });
                $('#DataTables_Table_0_filter label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();
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
                $('#DataTables_Table_0 thead tr').remove();
                const $select = $('#DataTables_Table_0_length select')
                    .detach();
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
                $('.dt-row-grouping tbody').on('click', 'tr.group', function() {
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
                fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
                toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
                groupingTable.ajax
                    .reload();
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
            let pendingEditPayload = [];
            $(document).on('click', '.btn-group-edit', function() {
                console.log('Edit Group button clicked');
                const $group = $(this).closest('tbody');
                const issuedInputs = $group.find('.issued-input');
                const payload = [];
                let modalHTML = '<ul>';
                issuedInputs.each(function() {
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
                            available_quantity: rowData.current_availability ?? 0,
                            order_status: 2
                        });
                        modalHTML += `<li>
        <strong>${rowData.drug_name}</strong>: 
        Issue ${value} 
        (Available: ${rowData.current_availability ?? 'N/A'})
      </li>`;
                    }
                });
                modalHTML += '</ul>';
                if (payload.length === 0) {
                    alert("Please enter issued quantity for at least one drug.");
                    return;
                }
                pendingEditPayload = payload;
                $('#editModalBody').html(modalHTML);
                $('#editIssueModal').fadeIn();
            });
            $('#confirmEditIssue').on('click', function() {
                const payloadToSend = pendingEditPayload.map(item => ({
                    ...item,
                    order_status: 0
                }));
                $.ajax({
                    type: 'POST',
                    url: 'https://login-users.hygeiaes.com/requests/issue-partly-prescription',
                    data: JSON.stringify(payloadToSend),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert("Prescription edited successfully!");
                        groupingTable.ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert("Error editing prescription.");
                    }
                });
                $('#editIssueModal').fadeOut();
            });
            $('#cancelEditIssue').on('click', function() {
                $('#editIssueModal').fadeOut();
                location.reload();
            });
            let pendingPayload = [];
            $(document).on('click', '.btn-group-delete', function() {
                const $group = $(this).closest('tbody');
                const issuedInputs = $group.find('.issued-input');
                const payload = [];
                let modalHTML = '<ul>';
                issuedInputs.each(function() {
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
                            available_quantity: rowData.current_availability ?? 0,
                            order_status: 1
                        });
                        modalHTML += `<li>
  <strong>${rowData.drug_name}</strong>: 
  Issue ${value} 
  (Available: ${rowData.current_availability ?? 'N/A'})
</li>`;
                    }
                });
                modalHTML += '</ul>';
                if (payload.length === 0) {
                    alert("Please enter issued quantity for at least one drug.");
                    return;
                }
                pendingPayload = payload;
                $('#modalBody').html(modalHTML);
                $('#issueModal').fadeIn();
            });
            $('#confirmIssue').on('click', function() {
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
                    success: function(response) {
                        alert("Prescription closed successfully!");
                        groupingTable.ajax.reload(null, false);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert("Error submitting prescription issue.");
                    }
                });
                $('#issueModal').fadeOut();
            });
            $('#cancelIssue').on('click', function() {
                $('#issueModal').fadeOut();
                pendingPayload = [];
                $('#modalBody').html('');
                location.reload();
            });
            const table = $('.dt-row-grouping').DataTable();
            $('.dt-row-grouping').on('blur', '.issued-input', function() {
                const $input = $(this);
                const id = $input.data('id');
                const val = parseInt($input.val()) || 0;
                const row = table.row($input.closest('tr'));
                const rowData = row.data();
                if (!rowData) return;
                const toIssue = parseInt(rowData.prescribed_days) * (
                    parseInt(rowData.morning) +
                    parseInt(rowData.afternoon) +
                    parseInt(rowData.evening) +
                    parseInt(rowData.night)
                );
                const available = parseInt(rowData.current_availability) || 0;
                const maxAllowed = Math.min(toIssue, available);
                if (val > maxAllowed) {
                    alert(`You cannot issue more than Available (${available}) or To Issue (${toIssue}).`);
                    $input.val('');
                    $input.focus();
                    return;
                }
                console.log(`Issued updated for ID ${id}: ${val}`);
            });
        });
   
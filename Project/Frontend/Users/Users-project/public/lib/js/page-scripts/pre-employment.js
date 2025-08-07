'use strict';
let dt_basic;
document.addEventListener('DOMContentLoaded', function () {
  flatpickr("#date", {
    enableTime: false,
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "Y/m/d",
    allowInput: true
  });
  setTimeout(() => {
    const newRecord = document.querySelector('.create-new');
    const offCanvasElement = document.querySelector('#add-new-record');
    if (newRecord && offCanvasElement) {
      newRecord.addEventListener('click', function () {
        const offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
        offCanvasElement.querySelector('.dt-full-name').value = '';
        offCanvasEl.show();
      });
    }
  }, 200);
});
$(function () {
  const dt_basic_table = $('.datatables-basic');
  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: 'https://login-users.hygeiaes.com/pre-employment/getUserDetails',
        data: function (d) {
          const fromDate = $('#fromDate').val();
          const toDate = $('#toDate').val();
          if (fromDate) d.from_date = fromDate;
          if (toDate) d.to_date = toDate;
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
          data: null,
          title: 'First Name',
          render: function (data, type, row) {
            const from = new Date(row.from_datetime).toLocaleDateString('en-GB');
            const to = new Date(row.to_datetime).toLocaleDateString('en-GB');
            return type === 'display' ? `${from} <br>${to}` : row.from_datetime;
          }
        },
        {
          data: 'name',
          title: 'Last Name',
          render: function (data) {
            return Array.isArray(data) && data.length
              ? data.map(name => `<div>${name}</div>`).join('')
              : '-';
          }
        },
        {
          data: null,
          title: 'Email',
          render: function (row) {
            return (!row.doctor_id || row.doctor_id === 0)
              ? (row.doctor_name || 'Unknown')
              : (doctorMap[row.doctor_id] || 'Unknown');
          }
        },
        {
          data: null,
          title: 'Mobile',
          render: function (row) {
            return (!row.hospital_id || row.hospital_id === 0)
              ? (row.hospital_name || 'Unknown')
              : (hospitalMap[row.hospital_id] || 'Unknown');
          }
        },
        {
          data: null,
          title: 'DOB',
          render: function (data, type, row) {
            const dischargeBtn = row.attachment_discharge
              ? `<button class="btn btn-sm btn-primary view-discharge" data-url="${row.attachment_discharge}">Summary Report</button>`
              : '';
            let reportsBtn = '';
            try {
              const reports = JSON.parse(row.attachment_test_reports || '[]');
              if (reports.length) {
                reportsBtn = `<button class="btn btn-sm btn-info view-reports" data-reports='${JSON.stringify(reports)}'>Test Reports</button>`;
              }
            } catch (e) { }
            return [dischargeBtn, reportsBtn].filter(Boolean).join(' ');
          }
        },
        {
          data: null,
          title: 'Adhar Number',
          render: function (row) {
            return (!row.hospital_id || row.hospital_id === 0)
              ? (row.hospital_name || 'Unknown')
              : (hospitalMap[row.hospital_id] || 'Unknown');
          }
        }
      ],
      order: [[0, 'desc']],
      searching: true,
      paging: false,
      lengthChange: false,
      dom: `
        <"card-header flex-column flex-md-row"
          <"head-label text-center">
          <"dt-action-buttons text-end pt-2 pt-md-0"B>
        >
        <"row"
          <"col-sm-12 col-md-6"l>
          <"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n2 mt-md-0"f>
        >
        t
        <"row"
          <"col-sm-12 col-md-6"i>
          <"col-sm-12 col-md-6"p>
        >
      `,
      buttons: [
        {
          extend: 'excelHtml5',
          text: 'Export to Excel',
          filename: () => {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            return `Invoice - ${dd}-${mm}-${yyyy}`;
          },
          className: 'btn btn-success d-none',
          exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
        }
      ],
      initComplete: function () {
        const count = this.api().data().count();
        $('#employeeTypeLabel').text(`List of users (${count})`);
        this.api().buttons().container().appendTo('#export-buttons');
      }
    });
    $('#DataTables_Table_0_filter label').contents().filter(function () {
      return this.nodeType === 3;
    }).remove();
    $('#DataTables_Table_0_filter input')
      .css({ width: '325px', height: '37px', display: 'inline-block', margin: '0' })
      .attr('placeholder', 'Search By Name / Email/ Adhar');
    const searchInput = $('#DataTables_Table_0_filter').detach();
    const customHeaderRow = `
      <div class="row align-items-center mb-3" style="margin-top: -50px; margin-bottom: 0;">
        <div class="col-md-6 d-flex align-items-center" id="customSearchContainer"></div>
        <div class="col-md-6 d-flex justify-content-end">
          <a href="/pre-employment-add" class="btn btn-primary">
            <i class="ti ti-plus me-1 ti-xs"></i> Add Pre-employment user Details
          </a>
        </div>
      </div>`;
    $('.card-header').after(customHeaderRow);
    $('#customSearchContainer').append(searchInput);
    $('#searchBtn').on('click', function () {
      let fromDate = $('#fromDate').val();
      let toDate = $('#toDate').val();
      const status = $('#status').val();
      const queryParams = {};
      if (fromDate) {
        fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
        queryParams.from_date = fromDate;
      }
      if (toDate) {
        toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
        queryParams.to_date = toDate;
      }
      if (status) {
        queryParams.status = status;
      }
      const newUrl = dt_basic.ajax.url().split('?')[0];
      const urlWithParams = `${newUrl}?${$.param(queryParams)}`;
      dt_basic.ajax.url(urlWithParams).load(() => {
        const count = dt_basic.data().count();
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
  flatpickr("#fromDate", { dateFormat: "d/m/Y" });
  flatpickr("#toDate", { dateFormat: "d/m/Y" });
});

var dtButtons = [];
if (typeof ohcRights !== 'undefined' && ohcRights.bio_medical == 2) {
  dtButtons.push(
    {
      text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
      className: 'create-new btn btn-primary waves-effect waves-light',
      action: function () {
        console.log("Add button clicked");
      }
    },
    {
      extend: 'excelHtml5',
      text: '<button class="btn buttons-excel buttons-html5 btn-link" type="button" title="Export to Excel" style="display: inline-block;width:20px;margin-left:8px;">' +
        '<i class="fa-sharp fa-solid fa-file-excel" style="font-size: 33px;"></i>' +
        '</button>',
      filename: function () {
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const yyyy = today.getFullYear();
        return `Bio-Medical Waste - ${dd}-${mm}-${yyyy}`;
      },
      className: '',
      exportOptions: {
        columns: [0, 1, 2, 3, 4, 5, 6]
      }
    }
  );
}
/**
 * DataTables Basic
 */
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
    }, 200);
  })();
});
$(function () {
  var dt_basic_table = $('.datatables-basic'),
    dt_basic;
  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: 'https://login-users.hygeiaes.com/others/getAllBiowasteDetails',
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
          data: 'date',
          title: 'Date',
          render: function (data, type, row) {
            let date = new Date(row.date);
            if (type === 'sort' || type === 'type') {
              return date;
            }
            if (type === 'display') {
              return date.toLocaleDateString('en-GB');
            }
            return date;
          }
        },
        {
          data: 'issued_by',
          title: 'Issued by'
        },
        {
          data: 'received_by',
          title: 'Recieved By'
        },
        {
          data: 'red',
          title: 'Red'
        },
        {
          data: 'yellow',
          title: 'Yellow'
        },
        {
          data: 'blue',
          title: 'Blue'
        },
        {
          data: 'white',
          title: 'White'
        },
        {
          data: null,
          title: 'Actions',
          render: function (data, type, row) {
            let buttons = '';
            if (typeof ohcRights !== 'undefined' && ohcRights.bio_medical == 2) {
              buttons += `
                <button class="btn btn-sm btn-warning edit-record" 
                    data-id="${row.industry_id}" 
                    data-date="${row.date}" 
                    data-red="${row.red}" 
                    data-yellow="${row.yellow}"
                    data-blue="${row.blue}"
                    data-white="${row.white}"
                    data-issued_by="${row.issued_by}"
                    data-received_by="${row.received_by}">
                    Edit
                </button>&nbsp;&nbsp;
            `;
            }
            buttons += `
            <a href="javascript:void(0);" onclick="printChallan(${row.industry_id})">
                <i class="fa-solid fa-print"></i>
            </a>
        `;
            return buttons;
          }
        }
      ],
      order: [
        [0, 'desc']
      ],
      searching: true,
      paging: false,
      lengthChange: false,
      dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>>' +
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t' +
        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: dtButtons,
      initComplete: function () {
        var count = dt_basic.data().count();
        $('#employeeTypeLabel').text(`List of Bio-Medical Waste (${count})`);
        this.api().buttons().container()
          .appendTo('#export-buttons');
      }
    });
    $('.dataTables_filter').addClass('search-container').prependTo('.card-header');
    $('#DataTables_Table_0_filter label').contents().filter(function () {
      return this.nodeType === 3;
    }).remove();
    $('#DataTables_Table_0_filter input')
      .css({ 'width': '300px', 'margin-right': '10px' })
      .attr('placeholder', 'Search by ISSUED / RECEIVED BY');
    $('.card-header').css({
      'display': 'flex',
      'flex-wrap': 'nowrap',
      'align-items': 'center',
      'justify-content': 'space-between',
      'margin-right': '10px',
    });
    var legendHTML = `
        <div class="legend" style="margin-left:30px;">
          <div class="legend-column">
              <div class="legend-item"><span class="color-box red"></span> Syringes, IV Tubes, Bottle</div>
              <div class="legend-item"><span class="color-box blue"></span> Broken/Contaminated Glass-Ampoules</div>
          </div>
          <div class="legend-column" style="margin-left: auto; margin-right:50px;">
              <div class="legend-item"><span class="color-box yellow"></span> Boiled Cotton Dress, Expired Medicines/Drugs, Plastic Casts</div>
              <div class="legend-item"><span class="color-box white"></span> Scalpels, Blades, Needles & Sharp Metal</div>
          </div>
        </div><br/>
      `;
    $('.card-header.flex-column.flex-md-row').after(legendHTML);
    var filterHTML = `
        <div class="row mb-2" style="display: flex; align-items: center;margin-right: 95px;
          margin-top: 10px;">
              <input type="text" id="fromDate" class="form-control flatpickr-input" placeholder="From Date" style="width: 120px;" readonly="readonly">
              <input type="text" id="toDate" class="form-control flatpickr-input" placeholder="To Date" style="width: 120px;margin-left: 17px;" readonly="readonly">
              <button id="searchBtn" class="btn btn-primary" style="width: 30px; height: 37px;margin-left:17px;">
                  <i class="ti ti-search"></i>
              </button>
        </div>
      `;
    $('.dt-action-buttons').before(filterHTML);
    $('.row.mb-2').css({
      'display': 'flex',
      'flex-wrap': 'nowrap',
      'align-items': 'center',
      'margin-left': '10px'
    });
    $('#searchBtn').on('click', function () {
      var fromDate = $('#fromDate').val();
      var toDate = $('#toDate').val();
      fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
      toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
      console.log('From Date:', fromDate);
      console.log('To Date:', toDate);
      var newUrl = dt_basic.ajax.url() + "?from_date=" + fromDate + "&to_date=" + toDate;
      dt_basic.ajax.url(newUrl).load(function () {
        var count = dt_basic.data().count();
        $('#employeeTypeLabel').text(`List of Bio-Medical Waste (${count})`);
      });
    });
    $('#exportExcelBtn').on('click', function () {
      dt_basic.button('.buttons-excel').trigger();
    });
  }
  $('.create-new').on('click', function () {
    const offCanvasElement = document.querySelector('#add-new-record');
    const bootstrapOffcanvas = new bootstrap.Offcanvas(offCanvasElement);
    bootstrapOffcanvas.show();
    document.getElementById('date').value = '';
  });
  $('#add-bio-waste').on('click', function () {
    const date = document.getElementById('date').value;
    const red = document.getElementById('red').value;
    const yellow = document.getElementById('yellow').value;
    const blue = document.getElementById('blue').value;
    const white = document.getElementById('white').value;
    const issued_by = document.getElementById('issued_by').value;
    const received_by = document.getElementById('received_by').value;
    let formIsValid = true;
    $('input').removeClass('is-invalid');
    $('div.invalid-feedback').remove();
    if (!isNumeric(red)) {
      formIsValid = false;
      $('#red').addClass('is-invalid');
      $('#red').after('<div class="invalid-feedback">Red must be a numeric value.</div>');
    }
    if (!isNumeric(yellow)) {
      formIsValid = false;
      $('#yellow').addClass('is-invalid');
      $('#yellow').after('<div class="invalid-feedback">Yellow must be a numeric value.</div>');
    }
    if (!isNumeric(blue)) {
      formIsValid = false;
      $('#blue').addClass('is-invalid');
      $('#blue').after('<div class="invalid-feedback">Blue must be a numeric value.</div>');
    }
    if (!isNumeric(white)) {
      formIsValid = false;
      $('#white').addClass('is-invalid');
      $('#white').after('<div class="invalid-feedback">White must be a numeric value.</div>');
    }
    if (!isAlphanumeric(issued_by)) {
      formIsValid = false;
      $('#issued_by').addClass('is-invalid');
      $('#issued_by').after('<div class="invalid-feedback">Issued By must be alphanumeric (letters and numbers only).</div>');
    }
    if (!isAlphanumeric(received_by)) {
      formIsValid = false;
      $('#received_by').addClass('is-invalid');
      $('#received_by').after('<div class="invalid-feedback">Received By must be alphanumeric (letters and numbers only).</div>');
    }
    if (!formIsValid) {
      return;
    }
    apiRequest({
      url: '/others/addBioMedicalWaste',
      method: 'POST',
      data: {
        date: date,
        red: red,
        yellow: yellow,
        blue: blue,
        white: white,
        issued_by: issued_by,
        received_by: received_by,
      },
      onSuccess: (response) => {
        console.log("API Response:", response);
        if (response.result) {
          showToast("success", "Bio Waste added successfully!");
        } else {
          showToast("error", "Failed to add Bio Waste!");
        }
        dt_basic.ajax.reload(null, false);
        const offcanvasElement = document.getElementById('add-new-record');
        const bootstrapOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
        if (bootstrapOffcanvas) {
          bootstrapOffcanvas.hide();
        } else {
          const newOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
          newOffcanvas.hide();
        }
        setTimeout(() => {
          document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
        }, 300);
        offcanvasElement.classList.remove('show');
      },
      onError: (error) => {
        console.error("API Error:", error);
        showToast("error", "An error occurred while adding Bio Waste.");
      }
    });
  });
  function isNumeric(value) {
    const numericPattern = /^[0-9]+$/;
    return numericPattern.test(value);
  }
  function isAlphanumeric(value) {
    const alphanumericPattern = /^[a-zA-Z0-9]+$/;
    return alphanumericPattern.test(value);
  }
  $('#red, #yellow, #blue, #white, #issued_by, #received_by').on('focus', function () {
    $(this).removeClass('is-invalid');
    $(this).next('.invalid-feedback').remove();
  });
  $('.datatables-basic tbody').on('click', '.edit-record', function () {
    var id = $(this).data('id');
    var date = $(this).data('date');
    var red = $(this).data('red');
    var yellow = $(this).data('yellow');
    var blue = $(this).data('blue');
    var white = $(this).data('white');
    var issued_by = $(this).data('issued_by');
    var received_by = $(this).data('received_by');
    editBioMedicalWaste(id, date, red, yellow, blue, white, issued_by, received_by);
  });
  function editBioMedicalWaste(id, date, red, yellow, blue, white, issued_by, received_by) {
    document.getElementById('date_edit').value = date;
    document.getElementById('red_edit').value = red;
    document.getElementById('yellow_edit').value = yellow;
    document.getElementById('blue_edit').value = blue;
    document.getElementById('white_edit').value = white;
    document.getElementById('issued_by_edit').value = issued_by;
    document.getElementById('received_by_edit').value = received_by;
    document.getElementById('edit-BioMedicalWaste').setAttribute('data-corporateOHC-id', id);
    const offcanvasElement = document.getElementById('edit-new-record');
    const bootstrapOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
    bootstrapOffcanvas.show();
    const editDrugtypeButton = document.getElementById('edit-BioMedicalWaste');
    editDrugtypeButton.replaceWith(editDrugtypeButton.cloneNode(true));
    const newEdiDrugTypeButton = document.getElementById('edit-BioMedicalWaste');
    newEdiDrugTypeButton.addEventListener('click', function () {
      const BioId = this.getAttribute('data-corporateOHC-id');
      const date = document.getElementById('date_edit').value;
      const red = document.getElementById('red_edit').value;
      const yellow = document.getElementById('yellow_edit').value;
      const blue = document.getElementById('blue_edit').value;
      const white = document.getElementById('white_edit').value;
      const issued_by = document.getElementById('issued_by_edit').value;
      const received_by = document.getElementById('received_by_edit').value;
      apiRequest({
        url: `/others/updateBioMedicalWaste/${BioId}`,
        method: 'POST',
        data: {
          BioId: BioId,
          date: date,
          red: red,
          yellow: yellow,
          blue: blue,
          white: white,
          issued_by: issued_by,
          received_by: received_by,
        },
        onSuccess: (response) => {
          showToast(response.result, response.message);
          dt_basic.ajax.reload(null, false);
          bootstrapOffcanvas.hide();
        },
        onError: (error) => {
          showToast('error', error);
        }
      });
    });
  }
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
  flatpickr("#fromDate", {
    dateFormat: "d/m/Y",
  });
  flatpickr("#toDate", {
    dateFormat: "d/m/Y",
  });
});
function printChallan(industryId) {
  const button = document.querySelector(`.edit-record[data-id='${industryId}']`);
  const date = button.getAttribute('data-date');
  const red = button.getAttribute('data-red');
  const yellow = button.getAttribute('data-yellow');
  const blue = button.getAttribute('data-blue');
  const white = button.getAttribute('data-white');
  const issuedBy = button.getAttribute('data-issued_by');
  const receivedBy = button.getAttribute('data-received_by');
  const dateObj = new Date(date);
  const day = String(dateObj.getDate()).padStart(2, '0');
  const month = String(dateObj.getMonth() + 1).padStart(2, '0');
  const year = dateObj.getFullYear();
  const formattedDate = `${day}-${month}-${year}`;
  const printWindow = window.open('', '_blank', 'width=800,height=600');
  printWindow.document.write(`
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Challan for Disposal of Hazardous Waste</title>
        </head>
        <body>
            <div style="width:100%; float:left;"><h4 style="text-align:center; text-decoration:underline;">CHALLAN FOR DISPOSAL OF HAZARDOUS WASTE</h4></div>
            <div style="width:100%; float:left;"><p style="text-align:center;">(To be received by the contractor and handed over to the final disposing agency at Scrap Yard)</p></div>
            <div style="width:100%; float:left;">
                <table cellpadding="10" cellspacing="0" border="1" width="98%" align="center">
                    <tr>
                        <td width="50%" valign="top"><br />From: <b>L&Ts , Oragadam</b></td>
                        <td width="50%" valign="top">
                            <p align="right">DATE: ${formattedDate}</p>
                            To: <b>Scrap Yard</b>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:100%; float:left; text-align:center; padding:10px 0;">The following quantity of hazardous waste as per details given below are being handed over</div>
            <div style="width:100%; float:left;">
                <table cellpadding="10" cellspacing="0" border="1" width="98%" align="center">
                    <tr><td width="50%" valign="top">Type of Waste</td><td valign="top"><b>Bio-Medical Waste</b></td></tr>
                    <tr><td valign="top">Quantity of Waste</td>
                    <td><b>Red</b>: ${red} gms<br />
                        <b>Yellow</b>: ${yellow} gms<br />
                        <b>Blue</b>: ${blue} gms<br />
                        <b>White</b>: ${white} gms</td></tr>
                    <tr><td width="49%" valign="top">Handed over by: <b>${issuedBy}</b></td><td valign="top">Received by: <b>${receivedBy}</b></td></tr>
                </table>
            </div>
        </body>
        </html>
    `);
  printWindow.document.close();
  printWindow.print();
}

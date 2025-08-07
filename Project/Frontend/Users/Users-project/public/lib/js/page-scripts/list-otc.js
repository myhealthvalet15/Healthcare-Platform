'use strict';
let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const preloader = document.getElementById('preloader');
    const table = document.getElementById('drugtemplate-table');
    const tbody = document.getElementById('drugtemplate-body');
  })();
  $(function () {
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
        ajax: function (data, callback, settings) {
          apiRequest({
            url: 'https://login-users.hygeiaes.com/otc/listotcdetails',
            method: 'GET',
            onSuccess: function (response) {
              console.log(response);
              if (response.result && response.data.length > 0) {
                callback({ data: response.data });
              } else {
                callback({ data: [] });
              }
            },
            onError: function (errorMessage) {
              console.error('Failed to fetch OTC data:', errorMessage);
              callback({ data: [] });
            }
          });
        },
        columns: [
          {
            data: 'registry_created_at',
            title: 'Date',
            render: function (data) {
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
            render: function (data, type, row) {
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
            render: function (data) {
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
            render: function (data) {
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
        initComplete: function () {
          var api = this.api();
          api.rows().every(function () {
            var rowData = this.data();
            var expiryDate = new Date(rowData.expiry_date);
            var today = new Date();
            var diffTime = expiryDate - today;
            var diffDays = Math.ceil(diffTime / (1000 * 3600 * 24));
            if (diffDays >= 45 && diffDays <= 60) {
              $(this.node()).addClass('highlight-row');
            }
          });
          var count = api.data().count();
          $('#employeeTypeLabel').text(`List of OTC (${count})`);
          api.buttons().container().appendTo('#export-buttons');
        }
      });
      $('#DataTables_Table_0_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
      $('#DataTables_Table_0_filter input').attr('placeholder', 'Name/ID/First-aid Name /Medical System/Symptoms');
      $('input[type="search"]').css('width', '300px');
      $('#DataTables_Table_0_filter input').css('height', '37px');
      $('#DataTables_Table_0_filter input').css('font-size', '15px');
      $('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');
      var existingAddButton = $('.d-flex.justify-content-end.align-items-center.card-header .add-new');
      $('.d-flex.justify-content-end.align-items-center.card-header').append(existingAddButton);
      var excelExportButtonContainer = $('.dt-buttons.btn-group.flex-wrap');
      existingAddButton.removeClass('ms-auto');
      $('.d-flex.justify-content-end.align-items-center.card-header').append(excelExportButtonContainer);
      excelExportButtonContainer.find('button')
        .addClass('ms-3')
        .removeClass('ms-3');
      var excelExportButton = excelExportButtonContainer.find('.buttons-excel');
      excelExportButton
        .removeClass('btn-secondary')
        .addClass('btn-link')
        .find('span').addClass('d-flex justify-content-center')
        .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');
      existingAddButton.addClass('ms-auto');
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
    $('.datatables-basic tbody').on('click', '.edit-record', function () {
      var id = $(this).data('id');
      console.log(id);
    });
  });
  flatpickr("#modalmanufacter_date", {
    dateFormat: "d/m/Y",
  });
  flatpickr("#modalexpiry_date", {
    dateFormat: "d/m/Y",
  });
});

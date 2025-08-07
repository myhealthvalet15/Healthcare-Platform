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
      apiRequest({
        url: 'https://login-users.hygeiaes.com/UserEmployee/listotcdetailsForEmployee',
        method: 'GET',
        onSuccess: function (json) {
          const data = json.result && Array.isArray(json.data) && json.data.length > 0 ? json.data : [];
          var dt_basic = dt_basic_table.DataTable({
            data: data,
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
                data: 'symptoms',
                title: 'Symptoms'
              },
              {
                data: 'medical_system',
                title: 'Medical System'
              },
              {
                data: 'drugs',
                title: 'Issued Tablets (Quantity)',
                render: function (data) {
                  if (Array.isArray(data) && data.length > 0) {
                    return data.map(drug => {
                      const typeName = drugTypeMapping[drug.drug_type] || "Unknown";
                      const drugName = drug.drug_name
                        ? drug.drug_name.charAt(0).toUpperCase() + drug.drug_name.slice(1).toLowerCase()
                        : "Unnamed";
                      const strength = drug.drug_strength || '';
                      const quantity = drug.issued_quantity !== undefined ? drug.issued_quantity : 'N/A';
                      return `${drugName} ${typeName} - ${strength} (${quantity})`;
                    }).join('<br>');
                  }
                  return 'N/A';
                }
              },
              {
                data: 'doctor_notes',
                title: 'Remarks'
              }
            ],
            order: [[0, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"><"col-sm-12 col-md-6">>' +
              '<"col-sm-12"f>' +
              't' +
              '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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
              $('#DataTables_Table_0_filter label').contents().filter(function () {
                return this.nodeType === 3;
              }).remove();
              $('#DataTables_Table_0_filter input')
                .attr('placeholder', 'Search By : Medical System/Symptoms')
                .css({
                  'width': '325px',
                  'height': '37px',
                  'font-size': '15px',
                  'margin-top': '17px',
                  'margin-right': '28px'
                });
              $('.dataTables_filter')
                .addClass('search-container')
                .prependTo('.d-flex.justify-content-end.align-items-center.card-header');
            }
          });
        },
        onError: function (error) {
          console.error('Error fetching OTC details:', error);
        }
      });
      $('#DataTables_Table_0_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
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
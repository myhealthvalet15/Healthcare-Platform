'use strict';
let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
    (function () {
        const formAddNewRecord = document.getElementById('form-add-new-record');
        setTimeout(() => {
            const newRecord = document.querySelector('.create-new'),
                offCanvasElement = document.querySelector('#add-new-record');
            if (newRecord) {
                newRecord.addEventListener('click', function () {
                    offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    (offCanvasElement.querySelector('.dt-full-name').value = '')
                    offCanvasEl.show();
                });
            }
        }, 200);
    })();
});
$(function () {
    var dt_basic_table = $('.datatables-basic'),
        dt_basic;
    if (dt_basic_table.length) {
        dt_basic = dt_basic_table.DataTable({
            ajax: function (data, callback, settings) {
                apiRequest({
                    url: 'https://login-users.hygeiaes.com/corporate-users/getUserDetails',
                    method: 'GET',
                    onSuccess: function (json) {
                        if (!json.result) {
                            toastr.error("Failed to fetch data: " + json.data);
                            callback({ data: [] });
                            return;
                        }
                        callback({ data: json.data });
                    },
                    onError: function (err) {
                        toastr.error("Error fetching user data");
                        callback({ data: [] });
                    }
                });
            },
            columns: [
                {
                    "targets": 0,
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
                    "targets": 2,
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
                    "targets": 4,
                    "data": "setting",
                    "title": "SETTING",
                    "render": function (data, type, row) {
                        if (row['setting'] == '1') {
                            var $mhclink = "class=isDisabled";
                            var $ohclink = "";
                        } else if (row['setting'] == '2') {
                            var $ohclink = "class=isDisabled";
                            var $mhclink = "";
                        }
                        return `<a ` + $mhclink + ` data-id="${row.id}" href="/corporate-users/mhc-rights/${row.id}" 
                               ><i class="fa-solid fa-hospital-user"></i></a>
                               <a `+ $ohclink + ` data-id="${row.id}" href="/corporate-users/ohc-rights/${row.id}" 
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
            searching: true,
            paging: false,
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
                    className: 'btn btn-success d-none',
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
            return this.nodeType === 3;
        }).remove();
        $('#DataTables_Table_0_filter input').css('width', '377px');
        $('#DataTables_Table_0_filter input').css('height', '37px');
        $('#DataTables_Table_0_filter input').attr('placeholder', 'Search Admin Users');
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
        $('#DataTables_Table_0_filter').after(filterRow);
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
        $('#exportExcelBtn').on('click', function () {
            dt_basic.button('.buttons-excel').trigger();
        });
    }
    setTimeout(() => {
        $('.dataTables_filter .form-control').removeClass('form-control-sm');
        $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
});

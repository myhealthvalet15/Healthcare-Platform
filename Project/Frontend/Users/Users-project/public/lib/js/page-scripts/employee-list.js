
'use strict';
let fv, offCanvasEl;
document.addEventListener('DOMContentLoaded', function (e) {
    (function () {
        const formAddNewRecord = document.getElementById('form-add-new-record');
        setTimeout(() => {
            const newRecord = document.querySelector('.create-new'),
                offCanvasElement = document.querySelector('#add-new-record');
            if (newRecord) {
                newRecord.addEventListener('click', function () {
                    offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    (offCanvasElement.querySelector('.dt-full-name').value = ''),
                        (offCanvasElement.querySelector('.dt-post').value = ''),
                        (offCanvasElement.querySelector('.dt-email').value = ''),
                        (offCanvasElement.querySelector('.dt-date').value = ''),
                        (offCanvasElement.querySelector('.dt-salary').value = '');
                    offCanvasEl.show();
                });
            }
        }, 200);
        fv = FormValidation.formValidation(formAddNewRecord, {
            fields: {
                basicFullname: {
                    validators: {
                        notEmpty: {
                            message: 'The name is required'
                        }
                    }
                },
                basicPost: {
                    validators: {
                        notEmpty: {
                            message: 'Post field is required'
                        }
                    }
                },
                basicEmail: {
                    validators: {
                        notEmpty: {
                            message: 'The Email is required'
                        },
                        emailAddress: {
                            message: 'The value is not a valid email address'
                        }
                    }
                },
                basicDate: {
                    validators: {
                        notEmpty: {
                            message: 'Joining Date is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: 'The value is not a valid date'
                        }
                    }
                },
                basicSalary: {
                    validators: {
                        notEmpty: {
                            message: 'Basic Salary is required'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.col-sm-12'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            },
            init: instance => {
                instance.on('plugins.message.placed', function (e) {
                    if (e.element.parentElement.classList.contains('input-group')) {
                        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                    }
                });
            }
        });
        const flatpickrDate = document.querySelector('[name="basicDate"]');
        if (flatpickrDate) {
            flatpickrDate.flatpickr({
                enableTime: false,
                dateFormat: 'm/d/Y',
                onChange: function () {
                    fv.revalidateField('basicDate');
                }
            });
        }
    })();
});
$(function () {
    var dt_basic_table = $('.datatables-basic'),
        dt_complex_header_table = $('.dt-complex-header'),
        dt_row_grouping_table = $('.dt-row-grouping'),
        dt_multilingual_table = $('.dt-multilingual'),
        dt_basic;
    $.ajax({
        url: '/employees/getAllEmployees',
        method: 'GET',
        success: function (response) {
            console.log(response);
            if (response.result) {
                var employeeData = response.message;
                initializeDataTable(employeeData);
            }
        },
        error: function (error) {
            console.error('Failed to fetch data:', error);
        }
    });
    $(document).on('click', '.showDetailsBtn', function () {
        var rowId = $(this).data('id');
        var rowData = dt_basic.row($(this).closest('tr')).data();
        console.log(rowId);
        $('#modalName').text(rowData.first_name);
        $('#modalEmail').text(rowData.email);
        $('#modalDesignation').text(rowData.designation);
        $('#modalDepartment').text(rowData.hl1_name);
        $('#employeeModal').modal('show');
    });

    function initializeDataTable(employeeData) {
        console.log(employeeData);
        if (dt_basic_table.length) {
            dt_basic = dt_basic_table.DataTable({
                data: employeeData,
                columns: [{
                    data: 'employee_user_mapping_id',
                    visible: false
                },
                {
                    data: 'first_name',
                    render: function (data, type, row) {
                        return `
                      <div class="d-flex align-items-center">
                      <img src="${assetsPath}img/avatars/3.png" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                      <span>${data}</span>
                      </div>
                      `;
                    }
                },
                {
                    data: 'email'
                },
                {
                    data: 'mob_num'
                },
                {
                    data: 'hl1_name'
                },
                {
                    data: 'designation'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `
                <button class="btn btn-outline-primary btn-sm showDetailsBtn" data-id="${data.employee_user_mapping_id}">
  <i class="fa fa-eye"></i>`;
                    }
                },
                ],
                order: [
                    [2, 'desc']
                ],
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 7,
                lengthMenu: [7, 10, 25, 50, 75, 100],
                language: {
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                buttons: [{
                    extend: 'collection',
                    className: 'btn btn-label-primary dropdown-toggle me-4 waves-effect waves-light border-none',
                    text: '<i class="ti ti-file-export ti-xs me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [{
                        extend: 'print',
                        text: '<i class="ti ti-printer me-1" ></i>Print',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [3, 4, 5, 6, 7],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        },
                        customize: function (win) {
                            $(win.document.body)
                                .css('color', config.colors.headingColor)
                                .css('border-color', config.colors.borderColor)
                                .css('background-color', config.colors.bodyBg);
                            $(win.document.body)
                                .find('table')
                                .addClass('compact')
                                .css('color', 'inherit')
                                .css('border-color', 'inherit')
                                .css('background-color', 'inherit');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="ti ti-file-text me-1" ></i>Csv',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [3, 4, 5, 6, 7],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [3, 4, 5, 6, 7],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="ti ti-file-description me-1"></i>Pdf',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [3, 4, 5, 6, 7],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="ti ti-copy me-1" ></i>Copy',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [3, 4, 5, 6, 7],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = '';
                                    $.each(el, function (index, item) {
                                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                                            result = result + item.lastChild.firstChild.textContent;
                                        } else if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                }
                            }
                        }
                    }
                    ]
                },
                {
                    text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
                    className: 'create-new btn btn-primary waves-effect waves-light'
                }
                ],
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Details of ' + data['first_name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                                return col.title !== '' ?
                                    '<tr data-dt-row="' +
                                    col.rowIndex +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');
                            return data ? $('<table class="table"/><tbody />').append(data) : false;
                        }
                    }
                },
                initComplete: function (settings, json) {
                    $('.card-header').after('<hr class="my-0">');
                }
            });
            $('div.head-label').html(`
  <!-- Search and Filter UI -->
<div class="row align-items-center mb-3">
  <div class="col-6">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by Name/Employee ID/Phone #" />
  </div>
</div>
<div class="row align-items-center">
  <div class="col-md-5">
    <select id="employeeType" class="form-select">
      <option value="">Employee Type</option>
    </select>
  </div>
  <div class="col-md-4">
    <select id="departments" class="form-select">
      <option value="">Department</option>
    </select>
  </div>
  <div class="col-md-3">
    <select id="status" class="form-select">
     <option value="">Status</option>
    </select>
  </div>
</div>
`);
            $('#select2employeeType').select2();
            $('#select2contractor').select2();
            $('#select2department').select2();
        }
    }
    setTimeout(() => {
        $('.dataTables_filter .form-control').removeClass('form-control-sm');
        $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
});

document.addEventListener('DOMContentLoaded', function (e) {
    'use strict';
    let fv, offCanvasEl;
    $(function () {
        var dt_basic_table = $('.datatables-basic'),
            dt_complex_header_table = $('.dt-complex-header'),
            dt_row_grouping_table = $('.dt-row-grouping'),
            dt_multilingual_table = $('.dt-multilingual'),
            dt_basic;
        if (dt_basic_table.length) {
            dt_basic = dt_basic_table.DataTable({
                ajax: {
                    url: 'https://mhv-admin.hygeiaes.com/hra/get-all-master-tests',
                    dataSrc: ''
                },
                columns: [{
                    data: ''
                },
                {
                    data: 'master_test_id'
                },
                {
                    data: 'test_name'
                },
                {
                    data: 'testgroup.name',
                    defaultContent: ''
                },
                {
                    data: 'subgroup.name',
                    defaultContent: ''
                },
                {
                    data: 'subsubgroup.name',
                    defaultContent: ''
                },
                {
                    data: ''
                }
                ],
                columnDefs: [{
                    className: 'control',
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return '';
                    }
                },
                {
                    targets: 1,
                    searchable: false,
                    visible: false
                },
                {
                    targets: 2,
                    responsivePriority: 4,
                    render: function (data, type, full, meta) {
                        var $name = full['test_name'],
                            $desc = full['unit'];
                        var stateNum = Math.floor(Math.random() * 6);
                        var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                        var $state = states[stateNum],
                            $initials = $name.match(/\b\w/g) || [];
                        $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                        var $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                        var $row_output =
                            '<div class="d-flex justify-content-start align-items-center user-name">' +
                            '<div class="avatar-wrapper">' +
                            '<div class="avatar me-2">' +
                            $output +
                            '</div>' +
                            '</div>' +
                            '<div class="d-flex flex-column">' +
                            '<span class="emp_name text-truncate">' +
                            $name +
                            '</span>' +
                            '<small class="emp_post text-truncate text-muted">' +
                            $desc +
                            '</small>' +
                            '</div>' +
                            '</div>';
                        return $row_output;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, full, meta) {
                        return full.testgroup && full.testgroup.name ? full.testgroup.name : '';
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, full, meta) {
                        return full.subgroup && full.subgroup.name ? full.subgroup.name : 'N/A';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, full, meta) {
                        return full.subsubgroup && full.subsubgroup.name ? full.subsubgroup.name : 'N/A';
                    }
                },
                {
                    targets: -1,
                    title: 'Actions',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="d-inline-block">' +
                            '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                            '<ul class="dropdown-menu dropdown-menu-end m-0">' +
                            '<li><a target="_blank" href="https://mhv-admin.hygeiaes.com/test-group/edit-tests/' + full.master_test_id + '" class="dropdown-item">Edit</a></li>' +
                            '<div class="dropdown-divider"></div>' +
                            '<li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></li>' +
                            '</ul>' +
                            '</div>' +
                            '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-view" data-test-id="' + full.master_test_id + '"><i class="ti ti-eye ti-md"></i></a>'
                        );
                    }
                }
                ],
                order: [
                    [1, 'desc']
                ],
                dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>><"row bg-violet text-white py-2 px-3"<"col-12 d-flex justify-content-between align-items-center"<"ml-auto"f><"ms-3"l>>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    },
                    search: ''
                },
                buttons: [{
                    text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Test</span>',
                    className: 'create-new btn btn-primary waves-effect waves-light',
                    action: function () {
                        window.location.href = '/test-group/add-tests';
                    }
                }],
                initComplete: function (settings, json) {
                    $('.card-header').after('<hr class="my-0">');
                    $('.row.bg-violet').css({
                        'background-color': '#6B1BC7',
                        'position': 'relative',
                        'color': 'white',
                        'padding': '20px 15px',
                        'display': 'flex',
                        'justify-content': 'space-between',
                        'align-items': 'center',
                        'min-height': '70px'
                    });
                    $('.row.bg-violet .col-12').prepend('<span class="sample-text" style="color: #FFFFFF; font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;List of Master Test</span>');
                    $('.dataTables_filter').css({
                        'margin': '0',
                        'text-align': 'left',
                        'float': 'left'
                    });
                    $('.dataTables_length').css({
                        'margin': '0',
                        'text-align': 'right',
                        'float': 'right',
                        'width': 'auto',
                        'display': 'inline-flex',
                        'justify-content': 'flex-end',
                        'align-items': 'center',
                        'position': 'relative',
                        'right': '0'
                    });
                    $('.sample-text').css({
                        'margin-right': '20px',
                        'font-weight': 'bold'
                    });
                    $('.dataTables_filter input').css({
                        'background-color': 'rgba(255, 255, 255, 0.9)',
                        'border-color': '#6B1BC7',
                        'border-width': '1px',
                        'border-radius': '4px',
                        'height': '38px',
                        'padding': '5px 10px',
                        'margin-right': '5px'
                    }).on('focus', function () {
                        $(this).css({
                            'border-color': '#6B1BC7',
                            'box-shadow': '0 0 5px rgba(107, 27, 199, 0.5)'
                        });
                    }).on('blur', function () {
                        $(this).css({
                            'border-color': '#6B1BC7',
                            'box-shadow': 'none'
                        });
                    }).attr('placeholder', 'Search...');
                    $('.dataTables_filter input')
                        .off()
                        .on('keyup', function (e) {
                            if (e.key === 'Enter') {
                                dt_basic.search(this.value).draw();
                            }
                        });
                    $('.dataTables_filter').append(
                        '<button id="dt-search-btn" class="btn btn-primary" style="height: 38px; width: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">' +
                        '<i class="ti ti-search"></i></button>'
                    );
                    $('#dt-search-btn').on('click', function () {
                        dt_basic.search($('.dataTables_filter input').val()).draw();
                    });
                    $('.dataTables_length select').css({
                        'background-color': 'rgba(255, 255, 255, 0.9)',
                        'border-color': 'transparent',
                        'border-radius': '4px',
                        'color': '#333',
                        'height': '38px'
                    });
                    $('.dataTables_length label, .dataTables_filter label').css('color', 'white');
                    $('.dataTables_filter label').html($('.dataTables_filter label input'));
                    setTimeout(function () {
                        $('.dataTables_filter').detach().appendTo('div.head-label');
                        $('.dataTables_filter label').css('color', '#333');
                    }, 0);
                    window.testData = json;
                }
            });
            $('div.head-label').html('');
            $('.dataTables_filter').removeClass('d-none').detach().appendTo('div.head-label');
            $(document).on('click', '.item-view', function () {
                var testId = $(this).data('test-id');
                showTestDetails(testId);
            });
        }
    });
});
function showTestDetails(testId) {
    var testData = window.testData || [];
    var test = testData.find(function (item) {
        return item.master_test_id == testId;
    });
    if (!test) {
        console.error('Test data not found for ID:', testId);
        return;
    }
    $('#exampleModalLabel1').text('Test Details: ' + test.test_name);
    var modalBody = $('#viewModal .modal-body');
    modalBody.empty();
    var content = `
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card p-3">
                            <h5 class="fw-bold">Basic Information</h5>
                            <p><strong>Test Name:</strong> ${test.test_name}</p>
                            <p><strong>Description:</strong> ${test.test_desc || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="fw-bold">Classification</h5>
                            <p><strong>Test Group:</strong> ${test.testgroup?.name || 'N/A'}</p>
                            <p><strong>Subgroup:</strong> ${test.subgroup?.name || 'N/A'}</p>
                            <p><strong>Sub-subgroup:</strong> ${test.subsubgroup?.name || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="fw-bold">Test Parameters</h5>
                            <p><strong>Unit:</strong> ${test.unit.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) || 'N/A'}</p>
                            <p><strong>Type:</strong> ${test.type.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) || 'N/A'}</p>
                            <p><strong>Numeric Type:</strong> ${test.numeric_type.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="fw-bold">Reference Ranges</h5>
                            <p><strong>Male Range:</strong> ${formatMinMax(test.m_min_max) || 'N/A'}</p>
                            <p><strong>Female Range:</strong> ${formatMinMax(test.f_min_max) || 'N/A'}</p>
                            <p><strong>Normal Values:</strong> ${test.normal_values || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5 class="fw-bold">Additional Information</h5>
                            <p><strong>Condition:</strong> ${formatCondition(test.condition) || 'N/A'}</p>
                            <p><strong>Numeric Condition:</strong> ${test.numeric_condition || 'N/A'}</p>
                            <p><strong>Remarks:</strong> ${test.remarks || 'N/A'}</p>
                        </div>
                    </div>
                </div>
            </div>
            `;
    modalBody.html(content);
    var viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
    viewModal.show();
}
function formatMinMax(minMaxStr) {
    if (!minMaxStr) return null;
    try {
        const data = JSON.parse(minMaxStr);
        if (typeof data.min === 'number' || typeof data.min === 'string') {
            return `Min: ${data.min}, Max: ${data.max}`;
        }
        if (Array.isArray(data.min)) {
            let result = '';
            for (let i = 0; i < data.min.length; i++) {
                result += `Range ${i + 1}: Min: ${data.min[i]}, Max: ${data.max[i]}<br>`;
            }
            return result;
        }
        return JSON.stringify(data);
    } catch (e) {
        console.error('Error parsing min/max data:', e);
        return minMaxStr;
    }
}
function formatCondition(conditionStr) {
    if (!conditionStr) return null;
    try {
        if (conditionStr.startsWith('[') && conditionStr.endsWith(']')) {
            const conditions = JSON.parse(conditionStr);
            return conditions.join('<br>');
        }
        if (conditionStr.startsWith('{') && conditionStr.endsWith('}')) {
            const condition = JSON.parse(conditionStr);
            let result = '';
            for (const key in condition) {
                result += `${key}: ${condition[key]}<br>`;
            }
            return result;
        }
        return conditionStr;
    } catch (e) {
        console.error('Error parsing condition data:', e);
        return conditionStr;
    }
}
function formatDate(dateStr) {
    if (!dateStr) return null;
    try {
        const date = new Date(dateStr);
        return date.toLocaleString();
    } catch (e) {
        return dateStr;
    }
}

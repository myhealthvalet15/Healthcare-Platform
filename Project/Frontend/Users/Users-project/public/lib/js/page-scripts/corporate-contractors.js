$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var dt_responsive_table = $('.dt-responsive');
    var currentRow = null;
    var currentId = null;
    var dt_responsive = dt_responsive_table.DataTable({
        ajax: {
            url: 'https://login-users.hygeiaes.com/corporate/contractors-list',
            dataSrc: function (json) {
                console.log(json);
                return json.result && json.data.length > 0 ? json.data : [];
            }
        },
        columns: [
            {
                data: 'corporate_contractors_id',
                visible: false,
                searchable: false
            },
            {
                data: 'contractor_name'
            },
            {
                data: 'contractor_email'
            },
            {
                data: 'contractor_address'
            },
            {
                data: 'active_status',
                render: function (data) {
                    var status = {
                        1: {
                            title: 'Active',
                            class: 'bg-label-success'
                        },
                        0: {
                            title: 'Inactive',
                            class: 'bg-label-danger'
                        }
                    };
                    return status[data] ? `<span class="badge ${status[data].class}">${status[data].title}</span>` : data;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${data.corporate_contractors_id}"><i class="ti ti-pencil"></i>edit</button>
                        <button class="deleteBtn" style="border:none;"data-id="${data.corporate_contractors_id}"><i class="ti ti-trash"></i></button>
                    `;
                }
            }
        ],
        pageLength: 10,
        order: [[0, 'desc']],
        dom: '<"row"<"col-sm-12 col-md-6"B>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            paginate: {
                next: '<i class="ti ti-chevron-right ti-sm"></i>',
                previous: '<i class="ti ti-chevron-left ti-sm"></i>'
            }
        },
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return `Details of ${data['contractor_name']}`;
                    }
                }),
                type: 'column',
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col) {
                        return col.title !== ''
                            ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                 x                   <td>${col.title}:</td>
                                    <td>${col.data}</td>
                                </tr>`
                            : '';
                    }).join('');
                    return data ? $('<table class="table"/><tbody />').append(data) : false;
                }
            }
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '',
                className: 'btn btn-secondary btn-sm d-none',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    }
                }
            }
        ],
        drawCallback: function (settings) {
            var api = this.api();
            var count = api.data().count();
            $('#employeeTypeLabel').text(`List of Contractors (${count})`);
            api.buttons().container().appendTo('#export-buttons');
        }
    });
    $('#exportExcel').on('click', function () {
        dt_responsive.button(0).trigger();
    });
    $(document).on('click', '.editBtn', function () {
        currentRow = dt_responsive.row($(this).parents('tr'));
        var data = currentRow.data();
        currentId = data.corporate_contractors_id;
        $('#editForm').attr('action', 'updateContractor/' + currentId);
        $('#edit_contractor_name').val(data.contractor_name);
        $('#edit_contractor_email').val(data.contractor_email);
        $('#edit_contractor_address').val(data.contractor_address);
        var isActive = data.active_status == 1;
        $('#edit_active_status').prop('checked', isActive);
        updateStatusLabel('#edit_active_status', isActive ? 1 : 0);
        var offcanvasElement = document.getElementById('editModal');
        var offcanvas = new bootstrap.Offcanvas(offcanvasElement);
        offcanvas.show();
    });
    $(document).on('click', '#saveChangesButton', function (e) {
        e.preventDefault();
        console.log('Am here');
        const form = document.getElementById('addNewForm');
        const formData = new FormData(form);
        if (!formData.has('_token')) {
            const token = $('meta[name="csrf-token"]').attr('content');
            formData.append('_token', token);
        }
        console.log([...formData]);
        apiRequest({
            url: '/corporate/contractor',
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                if (response.success) {
                    bootstrap.Offcanvas.getOrCreateInstance('#addNewModal').hide();
                    location.reload();
                }
            },
            onError: function (errorMessage) {
                showToast('error', errorMessage || 'An error occurred while adding the contractor.');
            }
        });
    });
    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        console.log($(this).attr('action'));
        apiRequest({
            url: $(form).attr('action'),
            method: 'PUT',
            data: formData,
            onSuccess: function (response) {
                $('#editModal').offcanvas('hide');
                showToast('success', 'Contractor details updated successfully!');
                location.reload();
            },
            onError: function (errorMessage) {
                $('#editModal').modal('hide');
                showToast('error', errorMessage || 'An error occurred while updating the contractor.');
            }
        });
    });
    $('#addNewModal').on('show.bs.modal', function () {
        $('#add_active_status').prop('checked', false);
        updateStatusLabel('#add_active_status', 0);
    });
    function updateStatusLabel(selector, status) {
        $(selector)
            .siblings('label')
            .find('.status-label')
            .text(status === 1 ? 'Active' : 'Inactive');
    }
    $('#edit_active_status').on('change', function () {
        updateStatusLabel(this, $(this).prop('checked') ? 1 : 0);
    });
    $(document).on('change', '.toggle-active-status', function () {
        var status = $(this).prop('checked') ? 1 : 0;
        updateStatusLabel(this, status);
    });
    $(document).on('click', '.deleteBtn', function () {
        currentRow = dt_responsive.row($(this).parents('tr'));
        currentId = currentRow.data().corporate_contractors_id;
        if (currentId) {
            $('#deleteModal').modal('show');
        }
    });
    $('#deleteButton').on('click', function () {
        if (!currentId) return;
        apiRequest({
            url: `/corporate/deleteContractor/${currentId}`,
            method: 'DELETE',
            onSuccess: function (response) {
                if (response.result) {
                    currentRow.remove().draw();
                    $('#deleteModal').modal('hide');
                    showToast('success', 'Contractor deleted successfully!');
                } else {
                    showToast('error', 'Deletion failed. Please try again.');
                }
            },
            onError: function () {
                console.error('Delete error');
                showToast('error', 'An error occurred while deleting.');
            }
        });
    });
    const searchInput = $('#customSearchInput');
    const dt = dt_responsive;
    let clearButton = $('#clearSearch');
    if (clearButton.length === 0) {
        clearButton = $('<button>').attr('id', 'clearSearch').html('&times;').css({
            position: 'absolute',
            right: '10px',
            top: '50%',
            transform: 'translateY(-50%)',
            display: 'none',
            background: 'transparent',
            border: 'none',
            fontSize: '16px',
            cursor: 'pointer',
            zIndex: 10
        });
        $('.search-container').css('position', 'relative').append(clearButton);
    }
    clearButton.hide();
    searchInput.on('keyup', function () {
        const value = $(this).val();
        dt.search(value).draw();
        if (value) {
            clearButton.show();
        } else {
            clearButton.hide();
        }
    });
    clearButton.on('click', function () {
        searchInput.val('');
        dt.search('').draw();
        clearButton.hide();
    });
    $('.dataTables_length label')
        .contents()
        .filter(function () {
            return this.nodeType === 3 && this.nodeValue.trim() === 'Show';
        })
        .remove();
    $('.dataTables_length label')
        .contents()
        .filter(function () {
            return this.nodeType === 3 && this.nodeValue.trim() === 'entries';
        })
        .remove();
});

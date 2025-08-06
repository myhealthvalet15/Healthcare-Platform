    document.addEventListener('DOMContentLoaded', function () {
        const openPanelButton = document.getElementById('openPanelButton');
        const sidePanel = document.getElementById('offcanvasAddEmployeeType');
        openPanelButton.addEventListener('click', function () {
            const offcanvas = new bootstrap.Offcanvas(sidePanel);
            offcanvas.show();
        });
        const statusCheckbox = document.getElementById('active_status');
        const statusLabel = document.querySelector('.status-label');
        statusCheckbox.addEventListener('change', function () {
            statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
        });
        const form = document.getElementById('form-add-new-record');
        const formValidation = FormValidation.formValidation(form, {
            fields: {
                hl1_name: {
                    validators: {
                        notEmpty: {
                            message: 'The department name is required'
                        },
                        stringLength: {
                            min: 3,
                            max: 50,
                            message: 'The department name must be between 3 and 50 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: 'The department name can only consist of alphabetical characters, numbers, and spaces'
                        }
                    }
                },
                hl1_code: {
                    validators: {
                        notEmpty: {
                            message: 'The department code is required'
                        },
                        stringLength: {
                            min: 3,
                            max: 10,
                            message: 'The department code must be between 3 and 10 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9]+$/,
                            message: 'The department code can only consist of alphabetical and numeric characters'
                        }
                    }
                },
                active_status: {
                    validators: {
                        notEmpty: {
                            message: 'The status field is required'
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
                autoFocus: new FormValidation.plugins.AutoFocus(),
                submitButton: new FormValidation.plugins.SubmitButton()
            },
            init: instance => {
                instance.on('plugins.message.placed', function (e) {
                    if (e.element.parentElement.classList.contains('input-group')) {
                        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                    }
                });
            }
        });
        form.addEventListener('submit', function (event) {
            formValidation.validate().then(function (status) {
                if (status === 'Valid') {
                    form.submit();
                } else {
                    event.preventDefault();
                }
            });
        });
    });
    $(function () {
        var dt_responsive_table = $('.dt-responsive');
        var currentRow = null;
        var currentId = null;
      
        var dt_responsive = dt_responsive_table.DataTable({
            data:dataSet,
            columns: [
                { data: 'hl1_name' },
                { data: 'hl1_code' },
                {
                    data: 'active_status',
                    render: function (data) {
                        var status = {
                            1: { title: 'Active', class: 'bg-label-success' },
                            0: { title: 'Inactive', class: 'bg-label-danger' }
                        };
                        return status[data]
                            ? `<span class="badge ${status[data].class}">${status[data].title}</span>`
                            : data;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `
                        <button class="btn btn-warning btn-sm editBtn" data-id="${data.id}"><i class="ti ti-pencil"></i>edit</button>
                    `;
                    }
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                },
                zeroRecords: "No records found",
                emptyTable: "No data available in table",
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return `Details of ${data['hl1_name']}`;
                        }
                    }),
                    type: 'column',
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ''
                                ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                    <td>${col.title}:</td>
                                    <td>${col.data}</td>
                                </tr>`
                                : '';
                        }).join('');
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    }
                }
            }, buttons: [
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
            ]
        });
        
        $('#exportExcel').on('click', function () {
            dt_responsive.button(0).trigger();
        });
        $(document).ready(function () {
            $(document).on('click', '.editBtn', function () {
    currentRow = dt_responsive.row($(this).parents('tr'));
    var data = currentRow.data();
    currentId = data.hl1_id;

    $('#editForm').attr('action', '/hl1update/' + currentId);
    $('#edit_hl1_name').val(data.hl1_name);
    $('#edit_hl1_code').val(data.hl1_code);
    var isActive = data.active_status == 1;
    console.log(isActive);
    $('#edit_active_status').prop('checked', isActive);
    updateStatusLabel('#edit_active_status', isActive ? 1 : 0);

    const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasEditEmployeeType'));
    offcanvas.show();
});

        });

        function updateStatusLabel(selector, status) {
            $(selector).siblings('label').find('.status-label').text(status === 1 ? 'Active' : 'Inactive');
        }
        $('#editForm').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: $(this).attr('action'),
        method: 'post',
        data: formData,
        success: function (response) {
            console.log(response);
            showToast("success", "Department details updated successfully!");

            // Find the updated row in DataTable
            var rowData = {
                hl1_name: $('#edit_hl1_name').val(),
                hl1_code: $('#edit_hl1_code').val(),
                active_status: $('#edit_active_status').prop('checked') ? 1 : 0,
                id: currentId // Make sure this is the correct ID from the row you are editing
            };

            // Update the row in the DataTable
            currentRow.data(rowData).draw();

            // Close offcanvas after successful update
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEditEmployeeType'));
            offcanvas.hide();
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            alert('Error updating entry: ' + (xhr.responseJSON?.message || error));
        }
    });
});

        $('#addNewModal').on('show.bs.modal', function () {
            $('#add_active_status').prop('checked', false);
            updateStatusLabel('#add_active_status', 0);
        });
        $(document).on('change', '.toggle-active-status', function () {
            var status = $(this).prop('checked') ? 1 : 0;
            updateStatusLabel(this, status);
        });
       /*  $('#editForm').submit(function (e) {
            e.preventDefault();
            var updatedData = {
                hl1_name: $('#edit_hl1_name').val(),
                hl1_code: $('#edit_hl1_code').val(),
                active_status: $('#edit_active_status').prop('checked') ? 1 : 0
            };
            currentRow.data(updatedData).draw();
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEditEmployeeType'));
            offcanvas.hide();
        }); */
        $(document).on('click', '.deleteBtn', function () {
            currentRow = dt_responsive.row($(this).parents('tr'));
            currentId = currentRow.data().hl1_id;
            if (currentId) {
                $('#deleteModal').modal('show');
            }
        });
        $('#deleteButton').click(function () {
            if (currentId) {
                $.ajax({
                    url: `/hl1delete/${currentId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        currentRow.remove().draw();
                        $('#deleteModal').modal('hide');
                    },
                    error: function (error) {
                        console.error('Error deleting record:', error);
                    }
                });
            } else {
                console.warn('No ID found for deletion');
            }
        });
        const searchInput = $('#customSearchInput');
        const dt = dt_responsive;
        let clearButton = $('#clearSearch');
        if (clearButton.length === 0) {
            clearButton = $('<button>')
                .attr('id', 'clearSearch')
                .html('&times;')
                .css({
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
        $('.dataTables_length label').contents().filter(function () {
            return this.nodeType === 3 && this.nodeValue.trim() === "Show";
        }).remove();
        $('.dataTables_length label').contents().filter(function () {
            return this.nodeType === 3 && this.nodeValue.trim() === "entries";
        }).remove();
    });

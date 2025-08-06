/**
 * DataTables Basic
 */

'use strict';

let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
    (function () {
        const formAddNewRecord = document.getElementById('form-add-new-record');

        setTimeout(() => {
            const newRecord = document.querySelector('.create-new'),
                offCanvasElement = document.querySelector('#add-new-record');


            // To open offCanvas, to add new record
            if (newRecord) {
                newRecord.addEventListener('click', function () {
                    offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
                    // Empty fields on offCanvas open
                    (offCanvasElement.querySelector('.dt-full-name').value = '')
                    // Open offCanvas with form
                    offCanvasEl.show();
                });
            }
        }, 200);

        // Form validation for Add new record
        fv = FormValidation.formValidation(formAddNewRecord, {
            fields: {
                basicFullname: {
                    validators: {
                        notEmpty: {
                            message: 'The name is required'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    // Use this for enabling/changing valid/invalid class
                    // eleInvalidClass: '',
                    eleValidClass: '',
                    rowSelector: '.col-sm-12'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
                // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
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
    })();
});

// datatable (jquery)
$(function () {
    var dt_basic_table = $('.datatables-basic'),
        dt_basic;

    if (dt_basic_table.length) {
        dt_basic = dt_basic_table.DataTable({
            ajax: {
                url: 'https://login-users.hygeiaes.com/corporate/getAllOHCDetails',
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
            columns: [{
                data: 'ohc_name',
                title: 'OHC Name'
            },
            {
                data: 'active_status',
                title: 'Status',
                render: function (data) {
                    var statusText = data === 1 ? 'Active' : 'Inactive';
                    var statusClass = data === 1 ? 'bg-success' : 'bg-danger';
                    return `<span class="badge ${statusClass}">${statusText}</span>`;
                }
            },
            {
                data: null,
                title: 'Actions',
                render: function (data, type, row) {
                    return `
          <button class="btn btn-sm btn-warning edit-record" 
              data-id="${row.corporate_ohc_id}" 
              data-ohc_name="${row.ohc_name}" 
              data-status="${row.active_status}">
              Edit
          </button>`;
                }
            }
            ],
            order: [
                [2, 'desc']
            ],
            searching: false,    // Disable search
            paging: false,       // Disable pagination
            lengthChange: false,
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>>' +
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',



            buttons: [{
                text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New OHC</span>',
                className: 'create-new btn btn-primary waves-effect waves-light'
            }

            ],
            initComplete: function () {



            }
        });
    }
    $('.create-new').on('click', function () {
        // Open the offcanvas for adding a new record
        const offCanvasElement = document.querySelector('#add-new-record');
        const bootstrapOffcanvas = new bootstrap.Offcanvas(offCanvasElement);
        bootstrapOffcanvas.show();

        // Reset fields
        document.getElementById('ohcname').value = ''; // Clear OHC Name
        const statusSwitch = document.getElementById('status-switch');
        statusSwitch.checked = true; // Default to Active
        const statusLabel = document.getElementById('status-label');
        statusLabel.textContent = 'Active'; // Default Status label
        statusSwitch.classList.add('is-valid');
        statusSwitch.classList.remove('is-invalid');
    });
    $('#add-corporate-ohc').on('click', function () {
        const ohcName = document.getElementById('ohcname').value;
        const statusSwitch = document.getElementById('status-switch');
        const status = statusSwitch.checked ? 1 : 0;
        if (!ohcName) {
            toastr.error('OHC Name is required');
            return;
        }

        apiRequest({
            url: '/corporate/addCorporateOHC',
            method: 'POST',
            data: {
                ohc_name: ohcName,
                status: status

            },
            onSuccess: (response) => {
                console.log("API Response:", response);

                if (response.result) {
                    showToast("success", "OHC added successfully!");
                } else {
                    showToast("error", "Failed to add OHC!");
                }

                // Refresh DataTable
                dt_basic.ajax.reload(null, false);

                // Hide the off-canvas
                const offcanvasElement = document.getElementById('add-new-record');
                const bootstrapOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (bootstrapOffcanvas) {
                    bootstrapOffcanvas.hide();
                }
                setTimeout(() => {
                    document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
                }, 300);
            },
            onError: (error) => {
                console.error("API Error:", error);
                showToast("error", "An error occurred while adding OHC.");
            }
        });
    });

    $('.datatables-basic tbody').on('click', '.edit-record', function () {
        // console.log('MA here');
        var id = $(this).data('id'); // Corrected
        var ohc_name = $(this).data('ohc_name'); // This is fine
        var status = $(this).data('status'); // Corrected

        editCorporateOHC(ohc_name, status, id);
    });

    function editCorporateOHC(ohc_name, status, id) {
        // Set the values in the edit form
        document.getElementById('ohc_name_edit').value = ohc_name;
        const statusSwitch = document.getElementById('status_switch_edit');
        const statusLabel = document.getElementById('status-label-edit');

        // Set the switch state and label based on the current status
        if (status === 'Active' || status === 1) {
            statusSwitch.checked = true;
            statusLabel.textContent = 'Active';
            statusSwitch.classList.add('is-valid');
            statusSwitch.classList.remove('is-invalid');
        } else {
            statusSwitch.checked = false;
            statusLabel.textContent = 'Inactive';
            statusSwitch.classList.add('is-invalid');
            statusSwitch.classList.remove('is-valid');
        }

        // Set up the event listener for status switch change
        statusSwitch.addEventListener('change', function () {
            if (statusSwitch.checked) {
                statusLabel.textContent = 'Active';
                statusSwitch.classList.add('is-valid');
                statusSwitch.classList.remove('is-invalid');
            } else {
                statusLabel.textContent = 'Inactive';
                statusSwitch.classList.add('is-invalid');
                statusSwitch.classList.remove('is-valid');
            }
        });

        // Set the corporateOHC ID for the update button
        document.getElementById('edit-corporateOHC').setAttribute('data-corporateOHC-id', id);

        // Show the off-canvas
        const offcanvasElement = document.getElementById('edit-new-record');
        const bootstrapOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bootstrapOffcanvas.show();

        // Replace the button and re-attach the event listener
        const editDrugtypeButton = document.getElementById('edit-corporateOHC');
        editDrugtypeButton.replaceWith(editDrugtypeButton.cloneNode(true));
        const newEdiDrugTypeButton = document.getElementById('edit-corporateOHC');

        // Handle the button click to submit the update
        newEdiDrugTypeButton.addEventListener('click', function () {
            const CorporateOHCId = this.getAttribute('data-corporateOHC-id');
            const updatedOHCNAme = document.getElementById('ohc_name_edit').value;
            const statusSwitch = document.getElementById('status_switch_edit');
            const updatedStatus = statusSwitch.checked ? 1 : 0;

            apiRequest({
                url: `/corporate/updateCorporateOHC/${CorporateOHCId}`,
                method: 'POST',
                data: {
                    ohc_name: updatedOHCNAme,
                    status: updatedStatus,
                    CorporateOHCId: CorporateOHCId
                },
                onSuccess: (response) => {
                    showToast(response.result, response.message);

                    // Update the table row
                    dt_basic.ajax.reload(null, false);
                    // Hide the off-canvas
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
});

//Personal
$(function () {
    // Check if the DataTable is already initialized for the second tab (personal-info)
    var dt_personal_info_table = $('.datatables-basic-pi');
    if (dt_personal_info_table.length && !$.fn.dataTable.isDataTable(dt_personal_info_table)) {
        var dt_personal_info = dt_personal_info_table.DataTable({
            ajax: {
                url: 'https://login-users.hygeiaes.com/corporate/getAllPharmacyDetails', // Change the endpoint for pharmacy
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
            columns: [{
                data: 'pharmacy_name',
                title: 'Pharmacy Name'
            },
            {
                data: 'active_status',
                title: 'Status',
                render: function (data) {
                    var statusText = data === 1 ? 'Active' : 'Inactive';
                    var statusClass = data === 1 ? 'bg-success' : 'bg-danger';
                    return `<span class="badge ${statusClass}">${statusText}</span>`;
                }
            },
            {
                data: null,
                title: 'Actions',
                render: function (data, type, row) {
                    return `
              <button class="btn btn-sm btn-warning edit-record1" 
                  data-id="${row.ohc_pharmacy_id}" 
                  data-pharmacy_name="${row.pharmacy_name}" 
                  data-status="${row.active_status}"
                   data-main_pharmacy="${row.main_pharmacy}">
                  Edit
              </button>`;
                }
            }
            ],
            order: [
                [2, 'desc']
            ],
            searching: false,    // Disable search
            paging: false,       // Disable pagination
            lengthChange: false,
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>>' +
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

            buttons: [{
                text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Pharmacy</span>',
                className: 'create-new btn btn-primary waves-effect waves-light'
            },

            ],
            initComplete: function () {


            }
        });
    }

    // Add new record for second tab
    $('.create-new').on('click', function () {
        // Open the offcanvas for adding a new record in the second tab
        const offCanvasElement = document.querySelector('#add-new-record1');
        const bootstrapOffcanvas = new bootstrap.Offcanvas(offCanvasElement);
        bootstrapOffcanvas.show();

        // Reset fields
        document.getElementById('pharmacy_name').value = ''; // Clear Full Name
        const statusSwitch = document.getElementById('status-switch');
        statusSwitch.checked = true; // Default to Active
        const statusLabel = document.getElementById('status-label');
        statusLabel.textContent = 'Active'; // Default Status label
        statusSwitch.classList.add('is-valid');
        statusSwitch.classList.remove('is-invalid');
    });

    // Handle add button click for the second tab
    $('#add-corporate-pharmacy').on('click', function () {
        // alert("Hii")
        //console.log('Bhava');
        const pharmacyName = document.getElementById('pharmacy_name').value;
        const statusSwitch = document.getElementById('status-switch');
        const status = statusSwitch.checked ? 1 : 0;
        const main_pharmacy = document.getElementById('main_pharmacy').checked ? 1 : 0;


        if (!pharmacyName) {
            toastr.error('Pharmacy Name is required');
            return;
        }

        // Submit data for the new record
        // After successful insert
        apiRequest({
            url: '/corporate/addPharmacy', // Endpoint for adding new pharmacy
            method: 'POST',
            data: {
                pharmacy_name: pharmacyName,
                status: status,
                main_pharmacy: main_pharmacy
            },
            onSuccess: (response) => {
                console.log("API Response:", response);

                if (response.result) {
                    showToast("success", "Pharmacy added successfully!");
                } else {
                    // 🛠️ **Check for a specific error message from the API**
                    let errorMessage = "Failed to add Pharamcy!";

                    if (response.errors) {
                        if (response.errors.pharmacy_name) {
                            errorMessage = response.errors.pharmacy_name[0]; // Get first error message
                        }
                    } else if (response.message) {
                        errorMessage = response.message;
                    }

                    showToast("error", errorMessage);

                }

                // Refresh DataTable
                dt_personal_info.ajax.reload(null, false); // Ensure correct DataTable instance

                // Hide the off-canvas
                const offcanvasElement = document.getElementById('add-new-record1');
                const bootstrapOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                if (bootstrapOffcanvas) {
                    bootstrapOffcanvas.hide();
                }

                // Remove the backdrop (to clean up)
                setTimeout(() => {
                    document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
                }, 300);
            }
        });
    });
    $('.datatables-basic-pi tbody').on('click', '.edit-record1', function () {
        //console.log('MA here');
        var id = $(this).data('id'); // Corrected
        var pharmacy_name = $(this).data('pharmacy_name'); // This is fine
        var status = $(this).data('status'); // Corrected
        var mainPharmacy = $(this).data('main_pharmacy');

        editPharmacyOHC(pharmacy_name, status, id, mainPharmacy);
    });


    function editPharmacyOHC(pharmacy_name, status, id, mainPharmacy) {
        // Set the values in the edit form
        document.getElementById('pharmacy_name_edit').value = pharmacy_name;
        const statusSwitch = document.getElementById('status_switch_edit1');
        const statusLabel = document.getElementById('status-label-edit1');
        const mainPharmacyCheckbox = document.getElementById('main_pharmacy_edit');

        // Set the main store checkbox
        mainPharmacyCheckbox.checked = parseInt(mainPharmacy) === 1;

        // Set the switch state and label based on the current status
        if (status === 'Active' || status === 1) {
            statusSwitch.checked = true;
            statusLabel.textContent = 'Active';
            statusSwitch.classList.add('is-valid');
            statusSwitch.classList.remove('is-invalid');
        } else {
            statusSwitch.checked = false;
            statusLabel.textContent = 'Inactive';
            statusSwitch.classList.add('is-invalid');
            statusSwitch.classList.remove('is-valid');
        }

        statusSwitch.addEventListener('change', function () {
            if (statusSwitch.checked) {
                statusLabel.textContent = 'Active';
                statusSwitch.classList.add('is-valid');
                statusSwitch.classList.remove('is-invalid');
            } else {
                statusLabel.textContent = 'Inactive';
                statusSwitch.classList.add('is-invalid');
                statusSwitch.classList.remove('is-valid');
            }
        });

        // Set the corporateOHC ID for the update button
        document.getElementById('edit-pharmacyOHC').setAttribute('data-corporatePharmacy-id', id);

        // Show the off-canvas
        const offcanvasElement = document.getElementById('edit-new-record1');
        const bootstrapOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bootstrapOffcanvas.show();

        // Replace the button and re-attach the event listener
        const editPharmacyButton = document.getElementById('edit-pharmacyOHC');
        editPharmacyButton.replaceWith(editPharmacyButton.cloneNode(true)); // Re-clone to remove old listeners
        const newEditPharmacyButton = document.getElementById('edit-pharmacyOHC');

        // Handle the button click to submit the update
        newEditPharmacyButton.addEventListener('click', function () {
            const PharmacyOHCId = this.getAttribute('data-corporatePharmacy-id');
            const updatedPharmacyName = document.getElementById('pharmacy_name_edit').value;
            const updatedStatus = statusSwitch.checked ? 1 : 0;
            const updatedMainPharmacy = mainPharmacyCheckbox.checked ? 1 : 0;

            apiRequest({
                url: `/corporate/updatePharmacy/${PharmacyOHCId}`,
                method: 'POST',
                data: {
                    pharmacy_name: updatedPharmacyName,
                    status: updatedStatus,
                    main_pharmacy: updatedMainPharmacy,
                    PharmacyOHCId: PharmacyOHCId
                },
                onSuccess: (response) => {
                    console.log('API Response:', response);

                    if (response && response.result === false) {
                        // If the API response contains an error (e.g., duplicate main pharmacy), show error toast
                        showToast('error', response.message); // Red error toast
                    } else {
                        showToast('success', response.message || 'Pharmacy updated successfully'); // Green success toast
                    }

                    // Reload DataTable after update
                    if ($.fn.DataTable.isDataTable("#personal-info-table")) {
                        $('#personal-info-table').DataTable().ajax.reload(null, false);
                    }


                    // Close the offcanvas properly
                    const offcanvasElement = document.getElementById('edit-new-record1');
                    const bootstrapOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (bootstrapOffcanvas) {
                        bootstrapOffcanvas.hide();
                    }
                },
                onError: (error) => {
                    console.log('API Error:', error);

                    let errorMessage = 'Error updating pharmacy!';
                    if (error && error.responseJSON && error.responseJSON.message) {
                        errorMessage = error.responseJSON.message;
                    } else if (error && error.message) {
                        errorMessage = error.message;
                    }

                    showToast('error', errorMessage); // Display error in red
                }
            });
        });
    }


});

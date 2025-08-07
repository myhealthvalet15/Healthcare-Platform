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
        if (dt_basic_table.length) {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/PharmacyStock/getPharmacyStockDetails',
                method: 'GET',
                onSuccess: function (json) {
                    const data = json.result && Array.isArray(json.data) && json.data.length > 0 ? json.data : [];
                    var dt_basic = dt_basic_table.DataTable({
                        data: data,
                        autoWidth: false,
                        columnDefs: [
                            {
                                targets: 0,
                                width: '300px'
                            }
                        ],
                        columns: [
                            {
                                data: 'drug_id',
                                visible: false
                            },
                            {
                                data: 'drug_name',
                                title: 'Drug Name - Strength (Type)',
                                render: function (data, type, row) {
                                    var capitalizedDrugName = row.drug_name.charAt(0).toUpperCase() + row.drug_name.slice(1).toLowerCase();
                                    let drugTypes = {
                                        1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel", 6: "Inhaler", 7: "Injection",
                                        8: "Lotion", 9: "Ointment", 10: "Powder", 11: "Shampoo", 12: "Syringe", 13: "Syrup",
                                        14: "Tablet", 15: "Toothpaste", 16: "Suspension", 17: "Spray", 18: "Test"
                                    };
                                    let drugType = drugTypes[row.drug_type] || 'Unknown';
                                    return `${capitalizedDrugName} - ${row.drug_strength} (${drugType})`;
                                }
                            },
                            {
                                data: null,
                                title: 'Manufacturer / Batch / Quantity',
                                render: function (data, type, row) {
                                    return row.drug_manufacturer + ' - ' + row.drug_batch + ' / ' + row.current_availability;
                                }
                            },
                            {
                                data: null,
                                title: 'Manufacturer / Expiry Date',
                                render: function (data, type, row) {
                                    let manufacterDate = new Date(row.manufacter_date).toLocaleDateString('en-GB');
                                    let expiryDate = new Date(row.expiry_date).toLocaleDateString('en-GB');
                                    return manufacterDate + ' <br> ' + expiryDate;
                                }
                            },
                            {
                                data: null,
                                title: 'No of Tablets',
                                render: function (data, type, row) {
                                    return `
                            <input type="number" name="mqty" data-id="${row.drug_template_id}" style="width:50px;" max="20" required min="1">
                        `;
                                }
                            },
                            {
                                data: null,
                                title: 'Sub Pharmacy List',
                                render: function (data, type, row) {
                                    return `
                            <select id="stores_${row.drug_template_id}" name="stores" style=" margin-left: 23px; height: 27px; width: 150px; border-color:#e4d7d7; color:#6a5d5d">
                                <option>-Select Store</option>
                            </select>
                        `;
                                }
                            },
                            {
                                data: null,
                                title: 'Action',
                                render: function (data, type, row) {
                                    return `<button class="btn btn-sm float-right m-1 btn-primary" main-store-id="${row.ohc_pharmacy_id}" data-id="${row.drug_template_id}" id ="showDetailsBtn"style="border:none;">
                                    Move
                                </button>`;
                                }
                            }
                        ],
                        order: [[0, 'desc']],
                        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>' +
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
                        responsive: true,
                        initComplete: function () {
                            var api = this.api();
                            var count = api.data().count();
                            $('#employeeTypeLabel')
                                .text(`List of Stocks (${count})`)
                                .css({
                                    'color': 'white',
                                    'font-weight': 'bold'
                                });
                            api.buttons().container().appendTo('#export-buttons');
                            apiRequest({
                                url: 'https://login-users.hygeiaes.com/corporate/getAllPharmacyDetails',
                                method: 'GET',
                                onSuccess: function (response) {
                                    if (response.result === true && Array.isArray(response.data) && response.data.length > 0) {
                                        const pharmacies = response.data;
                                        dt_basic.rows().every(function () {
                                            var row = this.data();
                                            var selectElement = $(`#stores_${row.drug_template_id}`);
                                            selectElement.empty();
                                            selectElement.append('<option>-Select Store</option>');
                                            pharmacies.forEach(function (pharmacy) {
                                                if (pharmacy.main_pharmacy !== 1) {
                                                    const option = $('<option></option>')
                                                        .val(pharmacy.ohc_pharmacy_id)
                                                        .text(pharmacy.pharmacy_name);
                                                    selectElement.append(option);
                                                }
                                            });
                                        });
                                    }
                                },
                                onError: function (error) {
                                    console.log('Error fetching pharmacy data:', error);
                                }
                            });
                        }
                    });
                },
                onError: function (error) {
                    console.error('Error loading stock data:', error);
                }
            });
            $(document).on('click', '#showDetailsBtn', function () {
                var drugTemplateId = $(this).data('id');
                var mainStoreId = $(this).attr('main-store-id');
                var quantity = $(`input[name="mqty"][data-id="${drugTemplateId}"]`).val();
                var storeId = $(`#stores_${drugTemplateId}`).val();
                apiRequest({
                    url: 'https://login-users.hygeiaes.com/pharmacy/moveStock',
                    method: 'POST',
                    data: {
                        drug_template_id: drugTemplateId,
                        mainStoreId: mainStoreId,
                        quantity: quantity,
                        store_id: storeId,
                    },
                    onSuccess: function (response) {
                        if (response.result === true) {
                            showToast("success", "Pharmacy Stock Moved successfully!");
                            window.location.href = 'https://login-users.hygeiaes.com/pharmacy/pharmacy-stock-list';
                        } else {
                            alert('Failed to move stock.');
                        }
                    },
                    onError: function (error) {
                        console.log('Error moving stock:', error);
                    }
                });
            });
            $('#DataTables_Table_0_filter label').contents().filter(function () {
                return this.nodeType === 3;
            }).remove();
            var searchContainer = document.getElementById('DataTables_Table_0_filter');
            searchContainer.style.textAlign = 'left';
            searchContainer.style.marginLeft = '20px';
            var searchInput = searchContainer.querySelector('input');
            searchInput.style.marginLeft = '0';
            $('#DataTables_Table_0_filter input').attr('placeholder', 'Drug Name/Manufacturer/Drug Type/Batch');
            $('input[type="search"]').css('width', '340px');
            $('input[type="search"]').css('height', '37px');
            $('input[type="search"]').css('font-size', '15px');
            $('input[type="search"]').css('margin-top', '-19px');
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
    });
});

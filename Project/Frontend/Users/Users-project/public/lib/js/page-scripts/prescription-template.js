  'use strict';
  let fv, offCanvasEl;
  let dt_basic;
  document.addEventListener('DOMContentLoaded', function(e) {
    (function() {
      const preloader = document.getElementById('preloader');
      const table = document.getElementById('drugtemplate-table');
      const tbody = document.getElementById('drugtemplate-body');     
    
    })();
  
    $(function() {
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

    var dt_basic_table = $('.datatables-basic');
    var storeSelect = $('#stores');
    if (dt_basic_table.length) {
        var dt_basic = dt_basic_table.DataTable({
          ajax: {
                url: 'https://login-users.hygeiaes.com/prescription/getPrescriptionDetails',
                
                dataSrc: function(json) {
                    if (json.data && json.data.length > 0) {
                    
                    console.log(json); 
                    let groupedData = [];

                    // Group data by template_name
                    let grouped = {};

                    json.data.forEach(item => {
                        if (!grouped[item.template_name]) {
                            grouped[item.template_name] = {
                                template_name: item.template_name,
                                prescription_template_id: item.prescription_template_id,
                                drugs: [],
                                drug_strength: [],
                                drug_type: [],
                                remarks: [],
                                intake_days: [],
                                morning: [],
                                afternoon: [],
                                evening: [],
                                night: [],
                                intake_condition: []
                            };
                        }
                        grouped[item.template_name].drugs.push(item.drug_name);
                        //grouped[item.template_name].prescription_template_id.push(item.prescription_template_id);
                        grouped[item.template_name].drug_strength.push(item.drug_strength);
                        grouped[item.template_name].drug_type.push(item.drug_type);
                        grouped[item.template_name].remarks.push(item.remarks);
                        grouped[item.template_name].intake_days.push(item.intake_days);
                        grouped[item.template_name].morning.push(item.morning);
                        grouped[item.template_name].afternoon.push(item.afternoon);
                        grouped[item.template_name].evening.push(item.evening);
                        grouped[item.template_name].night.push(item.night);
                        grouped[item.template_name].intake_condition.push(item.intake_condition);
                       });

                    // Convert the grouped object back to an array
                    for (let key in grouped) {
                        groupedData.push(grouped[key]);
                    }

                    return groupedData;
                    
                
                     
                    } else {
        return []; // Return empty array if no records are found
    }
                }
            },
            columns: [
                {
                    data: 'template_name',
                    title: 'Template Name',
                    render: function(data, type, row) {
                        // Display the template name
                        return `${row.template_name}`;
                    }
                },
                {
            data: null,
            title: 'Drug Name - Strength - Type',  // New column heading
            render: function(data, type, row) {
                // Initialize the array to hold the combined drug info
                var drugNamesAndStrengths = [];
                
                // Get the drugs, strengths, and drug types from the row
                var drugs = row.drugs;  // Array of drug names
                var strengths = row.drug_strength;  // Array of drug strengths
                var drugTypes = row.drug_type;  // Array of drug type IDs

                // Combine each drug's name, strength, and type in the format "drug_name (strength) - drug_type"
                for (var i = 0; i < drugs.length; i++) {
                    var drug = drugs[i].trim();
                    var strength = strengths[i] ? strengths[i].trim() : '';
                    var drugType = drugTypes[i] ? drugTypeMapping[drugTypes[i]] : 'Unknown';

                    // Capitalize and format the drug name, strength, and type
                    if (drug && strength) {
                        drugNamesAndStrengths.push(`${capitalizeFirstLetter(drug)} (${capitalizeFirstLetter(strength)}) - ${capitalizeFirstLetter(drugType)}`);
                    }
                }

                // Return the formatted string for this cell, joined by commas
                return drugNamesAndStrengths.join(', ');
            }
        },


                {
                    data: null,
                    title: 'Action',
                    render: function(data, type, row) {
    const templateId = row.prescription_template_id; // Access the prescription_template_id
    if (!templateId) {
        console.warn("Missing prescription_template_id for row", row); // Log missing ID warning
        return ''; // Handle missing ID gracefully
    }

    return `
        <a class="btn btn-sm btn-warning edit-record" 
           data-id="${templateId}" 
           href="/prescription/prescription-edit/${templateId}" 
           style="color:#fff;">
           Edit
        </a>&nbsp;&nbsp;
        <button class="btn btn-outline-primary btn-sm showDetailsBtn" 
                data-id="${templateId}" 
                style="border:none;">
            <i class="fa fa-eye"></i>
        </button>
    `;
}



                }
            ],
            order: [[2, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"B>>' +
                 '<"col-sm-12"f>' +
                 't' +
                 '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            language: {
                emptyTable: "No records found",
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                }
            },
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-sharp fa-solid fa-file-excel"></i>',
                titleAttr: 'Export to Excel',
                filename: 'Drug_template_Export',
                className: 'btn-link ms-3',
                exportOptions: { modifier: { page: 'all' } },
                columns: null 
            }],
            responsive: true,
            initComplete: function() {
                var api = this.api();
                $('.datatables-basic tbody').on('click', '.showDetailsBtn', function() {
    var rowId = $(this).data('id'); // Get the template_id or prescription_template_id from the clicked row
    var row = dt_basic.row($(this).closest('tr')); // Get the DataTable row
    var rowData = row.data(); // Get the row data
    
    if (!rowData) {
        console.error("Row data not found!");
        return;
    }

    var templateName = rowData.template_name || 'Unknown Template'; // Get the template name or use a fallback name
    $('#employeeModalLabel').html('Template Name : <span style="color:#4444e5;font-weight: bold;">' + templateName + '</span>');
 // Update modal title
    
    // Set the prescription_template_id in the modal
    var templateId = rowData.prescription_template_id || 'N/A';  // Extract the prescription template ID
    $('#modalprescription_template_id').text(templateId);
    $('#prescriptionTemplateTableBody').empty();

    // Loop through each drug (for a specific template) and create rows in the modal table
    for (var i = 0; i < rowData.drugs.length; i++) {
        let drugTypeName = drugTypeMapping[rowData.drug_type[i]] || 'Unknown'; // Default to 'Unknown' if no match

        let intakeConditionName = '';

    if (rowData.intake_condition[i] == '1') {
        intakeConditionName = 'Before Food';
    } else if (rowData.intake_condition[i] == '2') {
        intakeConditionName = 'After Food';
    } else if (rowData.intake_condition[i] == '3') {
        intakeConditionName = 'With Food';
    } else if (rowData.intake_condition[i] == '4') {
        intakeConditionName = 'SOS';
    } else if (rowData.intake_condition[i] == '5') {
        intakeConditionName = 'Stat';
    }
        var rowHtml = `
            <tr>
                <td>${capitalizeFirstLetter(rowData.drugs[i])} - ${rowData.drug_strength[i]}(${drugTypeName})</td>
               
                <td style="text-align:center;">${rowData.intake_days[i]}</td>
                <td style="text-align:center;">${rowData.morning[i]}</td>
                <td style="text-align:center;">${rowData.afternoon[i]}</td>
                <td style="text-align:center;">${rowData.evening[i]}</td>
                <td style="text-align:center;">${rowData.night[i]}</td>
                 <td style="text-align:center;">${rowData.remarks[i]}</td>
                  <td style="text-align:center;">${intakeConditionName}</td>
            </tr>
        `;
        $('#prescriptionTemplateTableBody').append(rowHtml); // Append each drug's row
    }

    // Show the modal
    $('#employeeModal').modal('show');
});


// Assuming api is initialized somewhere else and has data
var count = api.data().count();
$('#employeeTypeLabel').text(`List of Prescription Templates (${count})`);
api.buttons().container().appendTo('#export-buttons');


               
                var count = api.data().count();
                $('#employeeTypeLabel').text(`List of Prescription Template (${count})`);
                api.buttons().container().appendTo('#export-buttons');
            }
        });
   


        $('#DataTables_Table_0_filter label').contents().filter(function() {
    return this.nodeType === 3; // This filters out the text nodes (like "Search:")
}).remove();

$('#DataTables_Table_0_filter input').attr('placeholder', 'Template Name / Drug Name');
// Adjust the search input width
$('input[type="search"]').css('width', '300px');  // Set width to 300px, adjust as needed
$('#DataTables_Table_0_filter input').css('height', '37px');
$('#DataTables_Table_0_filter input').css('font-size', '15px');

// Move the search filter to the left of the header (if needed)
$('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');

// Find the "Add New Drug Template" button
var existingAddButton = $('.d-flex.justify-content-end.align-items-center.card-header .add-new');

// Append the "Add New Drug Template" button to the right end of the header
$('.d-flex.justify-content-end.align-items-center.card-header').append(existingAddButton);

// Move the Excel export button next to the "Add New Drug Template" button
var excelExportButtonContainer = $('.dt-buttons.btn-group.flex-wrap');

// Remove the existing "ms-auto" class from the add-new button (if necessary)
existingAddButton.removeClass('ms-auto');

// Add the Excel export button next to the "Add New Drug Template" button
$('.d-flex.justify-content-end.align-items-center.card-header').append(excelExportButtonContainer);

// Optionally, you can add a specific spacing or styling between the buttons if needed.
excelExportButtonContainer.find('button')
  .addClass('ms-3')  // Add margin-left if needed
  .removeClass('ms-3');  // Remove any previous margin that might not fit the layout

// Modify the Excel export button appearance
var excelExportButton = excelExportButtonContainer.find('.buttons-excel');
excelExportButton
  .removeClass('btn-secondary')
  .addClass('btn-link')
  .find('span').addClass('d-flex justify-content-center')
  .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');

// Optionally, adjust the layout of the "Add New Drug Template" button if needed
existingAddButton.addClass('ms-auto');

// Move the select dropdown to the appropriate cell
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

    $('.datatables-basic tbody').on('click', '.edit-record', function() {
        var id = $(this).data('id');
        console.log(id);
    });
});

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
});


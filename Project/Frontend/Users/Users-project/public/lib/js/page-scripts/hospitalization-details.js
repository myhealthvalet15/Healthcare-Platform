  //Corporate Code
  $('#hospital_id').on('change', function () {
    if ($(this).val() === '0') {
        $('#hospital_name_div').show();
    } else {
        $('#hospital_name_div').hide();
    }
});
$(document).ready(function () {
    // Show/hide hospital name
    $('#hospital_id').on('change', function () {
        $('#hospital_name_div').toggle($(this).val() === '0');
    });

    // Show/hide doctor name
    $('#doctor_id').on('change', function () {
        $('#doctor_name_div').toggle($(this).val() === '0');
    });

 console.log("employeeUserId:", employeeUserId);
    console.log("opRegistryId:", opRegistryId);

    loadMedicalCondition();

    // Delay load until conditions dropdown is ready
    setTimeout(() => {
        loadHospitalizationDetails();
    }, 300);

function showError(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        if ($input.next('.invalid-feedback').length === 0) {
            $input.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
            $input.next('.invalid-feedback').text(message);
        }
    }

    // Clear specific input error
    function clearError(input) {
        const $input = $(input);
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').remove();
    }

    // Clear all errors on form
    function clearAllErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }


$('#hospitalizationForm').on('submit', function (e) {
    e.preventDefault();
    clearAllErrors();
    const form = this;
    const formData = new FormData(form);
    formData.append('employee_id', bladeEmployeeId);
    formData.append('employee_user_id', bladeEmployeeUserId);
let isValid = true;

        const hospitalId = $('#hospital_id').val();
        const hospitalName = $('input[name="hospital_name"]').val().trim();
        const fromDate = $('input[name="from_date"]').val();
        const toDate = $('input[name="to_date"]').val();

        // Validation 1: Hospital select and name
        if (!hospitalId) {
            showError('#hospital_id', 'Please select a hospital.');
            isValid = false;
        } else if (hospitalId === '0' && hospitalName === '') {
            showError('input[name="hospital_name"]', 'Please enter the hospital name.');
            isValid = false;
        }

        // Validation 2: From/To dates
        if (!fromDate) {
            showError('input[name="from_date"]', 'Please enter the start date.');
            isValid = false;
        }

        if (!toDate) {
            showError('input[name="to_date"]', 'Please enter the end date.');
            isValid = false;
        }

        if (fromDate && toDate && new Date(toDate) < new Date(fromDate)) {
            showError('input[name="to_date"]', 'End date must be after or equal to start date.');
            isValid = false;
        }

        if (!isValid) return;
    fetch('/ohc/health-registry/update-hospitalization-by-id', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.result) {
            showToast('success', data.message || 'Hospitalization record updated successfully');
           
        } else {
            showToast('warning', data.message || 'Something went wrong while updating');
        }
    })
    .catch(error => {
        console.error('Submission failed:', error);
        showToast('error', 'Error submitting hospitalization data: ' + (error.message || 'Unknown error'));
    });
});


});
function loadHospitalizationDetails() {
 // console.log('🏁 loadHospitalizationDetails called');
    if (!employeeUserId || !opRegistryId) return;

    apiRequest({
        url: `/ohc/health-registry/get-hospitalization-by-id/${employeeUserId}/${opRegistryId}`,
        method: 'GET',
        dataType: 'json',
        onSuccess: function (response) {
        //  console.log('API response:', response); 
          const hospitalizationList = response?.data?.data;

          if (Array.isArray(hospitalizationList) && hospitalizationList.length > 0) {
     const hospitalization = hospitalizationList[0];
    console.log('🩺 Prefilling with:', hospitalization);
 setTimeout(() => {
      prefillHospitalizationForm(hospitalization);
    }, 300);
}
        },
        error: function (err) {
            console.error('Failed to load hospitalization details:', err);
        }
    });
}

function formatDateTime(datetimeStr) {
    if (!datetimeStr) return '';
    return datetimeStr.replace(' ', 'T').slice(0, 16); // "2025-07-01 19:22:00" → "2025-07-01T19:22"
}





function prefillHospitalizationForm(data) {
    console.log('Prefilling with:', data);

    // Hospital
    if (data.hospital_id != null) {
        $('#hospital_id').val(String(data.hospital_id)).trigger('change');
        if (parseInt(data.hospital_id) === 0 && data.hospital_name) {
            $('#hospital_name_div').show();
            $('input[name="hospital_name"]').val(data.hospital_name);
        }
    }

    // Doctor
    if (data.doctor_id != null) {
        $('#doctor_id').val(String(data.doctor_id)).trigger('change');
        if (parseInt(data.doctor_id) === 0 && data.doctor_name) {
            $('#doctor_name_div').show();
            $('input[name="doctor_name"]').val(data.doctor_name);
        }
    }

    // Dates (use formatter!)
    $('input[name="from_date"]').val(formatDateTime(data.from_datetime));
    $('input[name="to_date"]').val(formatDateTime(data.to_datetime));

    // Condition
    if (data.condition_id) {
        let conditionArray;
        try {
            conditionArray = JSON.parse(data.condition_id);
        } catch (e) {
            conditionArray = [];
        }
        $('#conditionSelect').val(conditionArray).trigger('change');
    }

    // Description
    $('textarea[name="description"]').val(data.description);
}

function loadMedicalCondition() {
    const $conditionSelect = $('#conditionSelect');

    // Destroy Select2 if already initialized (optional but safe)
    if ($conditionSelect.hasClass("select2-hidden-accessible")) {
        $conditionSelect.select2('destroy');
    }

    // Show loading placeholder
    $conditionSelect.html('<option disabled selected>Loading...</option>');

    apiRequest({
        url: '/UserEmployee/getMedicalCondition',
        method: 'GET',
        dataType: 'json',
        onSuccess: function (response) {
            if (response.result && Array.isArray(response.data)) {
                // Add a placeholder as the first option
                let options = '<option disabled selected value="">Select condition</option>';
                
                response.data.forEach(function (condition) {
                    options += `<option value="${condition.condition_id}">${condition.condition_name}</option>`;
                });

                $conditionSelect.html(options);

                // Initialize Select2 with placeholder
                $conditionSelect.select2({
                    placeholder: 'Select condition',
                    width: '100%'
                });
            } else {
                showToast('info', 'Notice', response.message || 'No conditions found.');
                $conditionSelect.html('<option disabled>No conditions found</option>');
            }
        },
        onError: function (error) {
            showToast('error', 'Error', 'Failed to load medical conditions');
            $conditionSelect.html('<option disabled>Error loading conditions</option>');
        }
    });
}
//Employee Code
const doctorMap = {
    101: "Dr. Aditi Verma",
    102: "Dr. Rajeev Kumar",
    103: "Dr. Meera Singh",
    other: "Other"
};

const hospitalMap = {
    1: "City Hospital",
    2: "State Medical",
    other: "Other"
};

'use strict';
  let fv, offCanvasEl;
  let dt_basic;
  document.addEventListener('DOMContentLoaded', function (e) {
    flatpickr("#date", {
      enableTime: false,
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "Y/m/d",
      allowInput: true
    });
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
    })();
  });

  // datatable (jquery)
  $(function () {
    var dt_basic_table = $('.datatables-basic'),
    dt_basic;
    if (dt_basic_table.length) {  
      dt_basic = dt_basic_table.DataTable({
        ajax: {
          url: 'https://login-users.hygeiaes.com/UserEmployee/getHospitalizationDetails',
          data: function(d) {
                    // Add date range filters to the request
                    var fromDate = $('#fromDate').val();
                    var toDate = $('#toDate').val();

                    if (fromDate) {
                        d.from_date = fromDate;
                    }
                    if (toDate) {
                        d.to_date = toDate;
                    }

                    console.log('Sending From Date:', fromDate);  // Debug log
                    console.log('Sending To Date:', toDate); 
                },
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
      columns: [
  {
    data: null,
    title: 'Hospitalization Date',
    render: function (data, type, row) {
      const from = new Date(row.from_datetime).toLocaleDateString('en-GB');
      const to = new Date(row.to_datetime).toLocaleDateString('en-GB');
      return type === 'display' ? `${from} <br>${to}` : row.from_datetime;
    }
  },
  {
    data: 'condition_names',
    title: 'Condition',
    render: function (data) {
      return Array.isArray(data) && data.length ? data.map(name => `<div>${name}</div>`).join('') : '-';
    }
  },
  {
    data: null,
    title: 'Doctor Name',
    render: function (row) {
      return (!row.doctor_id || row.doctor_id === 0) ? (row.doctor_name || 'Unknown') : (doctorMap[row.doctor_id] || 'Unknown');
    }
  },
  {
    data: null,
    title: 'Hospital Name',
    render: function (row) {
      return (!row.hospital_id || row.hospital_id === 0) ? (row.hospital_name || 'Unknown') : (hospitalMap[row.hospital_id] || 'Unknown');
    }
  },
 {
  data: null,
  title: 'Discharge Summary / Test Reports',
  render: function (data, type, row) {
    const dischargeBtn = row.attachment_discharge
      ? `<button class="btn btn-sm btn-primary view-discharge" data-url="${row.attachment_discharge}">Summary Report</button>`
      : '';

    let reportsBtn = '';
    try {
      const reports = JSON.parse(row.attachment_test_reports || '[]');
      if (reports.length) {
        reportsBtn = `<button class="btn btn-sm btn-info view-reports" data-reports='${JSON.stringify(reports)}'>Test Reports</button>`;
      }
    } catch (e) {}

    return [dischargeBtn, reportsBtn].filter(Boolean).join(' ');
  }
}
],

        order: [
          [0, 'desc']
        ],
        searching: true,    // Disable search
        paging: false,       // Disable pagination
        lengthChange: false,
       dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-2 pt-md-0"B>>' +
     '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n2 mt-md-0"f>>t' +
     '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',


      buttons: [
         
       
        {
          extend: 'excelHtml5',
          text: 'Export to Excel',
          filename: function () {
      // Get today's date in 'dd-mm-yyyy' format
      const today = new Date();
      const dd = String(today.getDate()).padStart(2, '0');
      const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
      const yyyy = today.getFullYear();
      const formattedDate = `${dd}-${mm}-${yyyy}`;
      
      // Construct filename as "Bio-Medical Waste - dd-mm-yyyy"
      return `Invoice - ${formattedDate}`;
    },
          className: 'btn btn-success d-none', // Add 'd-none' to hide the default export button
          exportOptions: {
            columns: [0,1,2,3,4,5]
          }
        }

        ],
        initComplete: function () {
          var count = dt_basic.data().count();
          $('#employeeTypeLabel').text(`List of Hospitalization (${count})`);
          this.api().buttons().container()
            .appendTo('#export-buttons');

        }

      });
  
 // Remove label text from search box
$('#DataTables_Table_0_filter label').contents().filter(function () {
    return this.nodeType === 3;
}).remove();

// Style the search input
$('#DataTables_Table_0_filter input')
    .css({
        width: '325px',
        height: '37px',
        display: 'inline-block',
        margin: '0'
    })
    .attr('placeholder', 'Search By Hospital Name / Doctor Name');

// Detach the search input container to reposition it
const searchInput = $('#DataTables_Table_0_filter').detach();

// Create the new header row layout
const customHeaderRow = `
<div class="row align-items-center mb-3" style="margin-top: -50px; margin-bottom: 0;">
    <div class="col-md-6 d-flex align-items-center" id="customSearchContainer"></div>
    <div class="col-md-6 d-flex justify-content-end">
        <a href="/UserEmployee/add" class="btn btn-primary">
            <i class="ti ti-plus me-1 ti-xs"></i> Add Hospitalization Details
        </a>
    </div>
</div>
`;

// Add it below DataTable header
$('.card-header').after(customHeaderRow);

// Place search input in the left section
$('#customSearchContainer').append(searchInput);



$('#searchBtn').on('click', function() {
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
  var status = $('#status').val(); // Get the selected status

  // Initialize an empty object to store query parameters
  var queryParams = {};

  // Only add fromDate if it's entered
  if (fromDate) {
    fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
    queryParams.from_date = fromDate;
  }

  // Only add toDate if it's entered
  if (toDate) {
    toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
    queryParams.to_date = toDate;
  }

  // Only add status if it's selected
  if (status) {
    queryParams.status = status;
  }

  // Log the query parameters for debugging
  console.log('Query Params:', queryParams);

  // Get the current DataTable URL and clear previous query parameters
  var newUrl = dt_basic.ajax.url().split('?')[0]; // Clear existing query parameters

  // Serialize the new queryParams and append them to the URL
  var urlWithParams = newUrl + "?" + $.param(queryParams);

  // Update the DataTable URL with the new URL containing filters
  dt_basic.ajax.url(urlWithParams).load(function() {
    // After reloading, update the count of displayed records
    var count = dt_basic.data().count();
    $('#employeeTypeLabel').text(`List of Hospitalization (${count})`);
  });
});


$('#exportExcelBtn').on('click', function () {
      dt_basic.button('.buttons-excel').trigger();
    });
    }
  
    setTimeout(() => {
      $('.dataTables_filter .form-control').removeClass('form-control-sm');
      $('.dataTables_length .form-select').removeClass('form-select-sm');
    }, 300);
    flatpickr("#fromDate", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    
    flatpickr("#toDate", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
  });

 
// Handle test reports (already exists)
$(document).on('click', '.view-reports', function () {
  const reports = JSON.parse($(this).attr('data-reports') || '[]');
  const $list = $('#reportList');
  $list.empty();
  $('#previewImage').addClass('d-none').attr('src', '');
  $('#downloadBtnWrapper').addClass('d-none');

  if (reports.length) {
    reports.forEach((url, index) => {
      const filename = `Report ${index + 1}`;
      const listItem = `
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <a href="#" class="preview-report" data-url="${url}">${filename}</a>
          <a href="${url}" target="_blank" download class="btn btn-sm btn-outline-secondary">Download</a>
        </li>`;
      $list.append(listItem);
    });
  }

  $('#reportModal').modal('show');
});

// Show report image on preview
$(document).on('click', '.preview-report', function (e) {
  e.preventDefault();
  const imgUrl = $(this).data('url');
  $('#previewImage').attr('src', imgUrl).removeClass('d-none');
  $('#downloadAttachment').attr('href', imgUrl);
  $('#downloadBtnWrapper').removeClass('d-none');
});

// Show discharge summary image in modal
$(document).on('click', '.view-discharge', function () {
  const dischargeUrl = $(this).data('url');  
  $('#reportList').empty();  
  $('#previewImage').attr('src', dischargeUrl).removeClass('d-none'); 
  $('#downloadAttachment').attr('href', dischargeUrl).removeClass('d-none');
  $('#downloadBtnWrapper').removeClass('d-none'); 
  $('#reportModal').modal('show');
});

//Add
$(function () {
    // Show an error message below an input
    function showError(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        if ($input.next('.invalid-feedback').length === 0) {
            $input.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
            $input.next('.invalid-feedback').text(message);
        }
    }

    // Clear specific input error
    function clearError(input) {
        const $input = $(input);
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').remove();
    }

    // Clear all errors on form
    function clearAllErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

    // Toggle hospital name input visibility
    $('#hospital_id').on('change', function () {
        const selected = $(this).val();
        $('#hospital_name_div').toggle(selected === '0');

        clearError(this); // Clear hospital select error

        // Also validate hospital name if 'Other'
        if (selected !== '0') {
            $('input[name="hospital_name"]').val('');
            clearError('input[name="hospital_name"]');
        }
    });

    // Toggle doctor name input visibility
    $('#doctor_id').on('change', function () {
        const selected = $(this).val();
        $('#doctor_name_div').toggle(selected === '0');
    });

    // Clear errors on user input (live feedback)
    $('input[name="hospital_name"], input[name="from_date"], input[name="to_date"]').on('input', function () {
        clearError(this);
    });

    $('#employeeHospitalizationForm').on('submit', function (e) {
        e.preventDefault();
        clearAllErrors();

        const form = this;
        const formData = new FormData(form);
        let isValid = true;

        const hospitalId = $('#hospital_id').val();
        const hospitalName = $('input[name="hospital_name"]').val().trim();
        const fromDate = $('input[name="from_date"]').val();
        const toDate = $('input[name="to_date"]').val();

        // Validation 1: Hospital select and name
        if (!hospitalId) {
            showError('#hospital_id', 'Please select a hospital.');
            isValid = false;
        } else if (hospitalId === '0' && hospitalName === '') {
            showError('input[name="hospital_name"]', 'Please enter the hospital name.');
            isValid = false;
        }

        // Validation 2: From/To dates
        if (!fromDate) {
            showError('input[name="from_date"]', 'Please enter the start date.');
            isValid = false;
        }

        if (!toDate) {
            showError('input[name="to_date"]', 'Please enter the end date.');
            isValid = false;
        }

        if (fromDate && toDate && new Date(toDate) < new Date(fromDate)) {
            showError('input[name="to_date"]', 'End date must be after or equal to start date.');
            isValid = false;
        }

        if (!isValid) return;

        // Proceed with AJAX submission
        fetch('/UserEmployee/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.result) {
                showToast('success', data.message || 'Hospitalization record added successfully');
                setTimeout(() => {
                    window.location.href = 'https://login-users.hygeiaes.com/UserEmployee/hospitalization';
                }, 1500);
            } else {
                showToast('warning', data.message || 'Something went wrong while updating');
            }
        })
        .catch(error => {
            console.error('Submission failed:', error);
            showToast('error', 'Error submitting hospitalization data: ' + (error.message || 'Unknown error'));
        });
    });
});

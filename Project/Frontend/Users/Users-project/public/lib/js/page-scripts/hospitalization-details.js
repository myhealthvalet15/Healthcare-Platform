$('#hospital_id').on('change', function () {
  if ($(this).val() === '0') {
    $('#hospital_name_div').show();
  } else {
    $('#hospital_name_div').hide();
  }
});
$(document).ready(function () {
  $('#hospital_id').on('change', function () {
    $('#hospital_name_div').toggle($(this).val() === '0');
  });
  $('#doctor_id').on('change', function () {
    $('#doctor_name_div').toggle($(this).val() === '0');
  });
  loadMedicalCondition();
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
  function clearError(input) {
    const $input = $(input);
    $input.removeClass('is-invalid');
    $input.next('.invalid-feedback').remove();
  }
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
    if (!hospitalId) {
      showError('#hospital_id', 'Please select a hospital.');
      isValid = false;
    } else if (hospitalId === '0' && hospitalName === '') {
      showError('input[name="hospital_name"]', 'Please enter the hospital name.');
      isValid = false;
    }
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
    apiRequest({
      url: '/ohc/health-registry/update-hospitalization-by-id',
      method: 'POST',
      data: formData,
      onSuccess: data => {
        if (data.result) {
          showToast('success', data.message || 'Hospitalization record updated successfully');
        } else {
          showToast('warning', data.message || 'Something went wrong while updating');
        }
      },
      onError: errorMessage => {
        console.error('Submission failed:', errorMessage);
        showToast('error', 'Error submitting hospitalization data: ' + errorMessage);
      }
    });
  });
});
function loadHospitalizationDetails() {
  if (!employeeUserId || !opRegistryId) return;
  apiRequest({
    url: `/ohc/health-registry/get-hospitalization-by-id/${employeeUserId}/${opRegistryId}`,
    method: 'GET',
    dataType: 'json',
    onSuccess: function (response) {
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
  return datetimeStr.replace(' ', 'T').slice(0, 16);
}
function prefillHospitalizationForm(data) {
  console.log('Prefilling with:', data);
  if (data.hospital_id != null) {
    $('#hospital_id').val(String(data.hospital_id)).trigger('change');
    if (parseInt(data.hospital_id) === 0 && data.hospital_name) {
      $('#hospital_name_div').show();
      $('input[name="hospital_name"]').val(data.hospital_name);
    }
  }
  if (data.doctor_id != null) {
    $('#doctor_id').val(String(data.doctor_id)).trigger('change');
    if (parseInt(data.doctor_id) === 0 && data.doctor_name) {
      $('#doctor_name_div').show();
      $('input[name="doctor_name"]').val(data.doctor_name);
    }
  }
  $('input[name="from_date"]').val(formatDateTime(data.from_datetime));
  $('input[name="to_date"]').val(formatDateTime(data.to_datetime));
  if (data.condition_id) {
    let conditionArray;
    try {
      conditionArray = JSON.parse(data.condition_id);
    } catch (e) {
      conditionArray = [];
    }
    $('#conditionSelect').val(conditionArray).trigger('change');
  }
  $('textarea[name="description"]').val(data.description);
  try {
    if (data.attachment_discharge) {
      renderAttachmentPreview('discharge_summary_preview', 'Discharge Summary', data.attachment_discharge);
    }
    const testReports = JSON.parse(data.attachment_test_reports || '[]');
    if (Array.isArray(testReports)) {
      $('#summary_reports_count').text(testReports.length);
      const container = $('#summary_reports_preview');
      container.empty();
      testReports.forEach((img, index) => {
        renderAttachmentPreview('summary_reports_preview', `Test Report ${index + 1}`, img);
        container.append('<br>');
      });
    }
  } catch (e) {
    console.warn('Attachment parsing failed:', e);
  }
}
function renderAttachmentPreview(containerId, label, base64Data) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const id = `att_${Math.random().toString(36).substring(2, 10)}`;
  const wrapper = document.createElement('div');
  wrapper.className = 'd-inline-block me-2 mb-2';
  const viewBtn = document.createElement('button');
  viewBtn.type = 'button';
  viewBtn.className = 'btn btn-outline-primary btn-sm';
  viewBtn.id = `${id}_view`;
  viewBtn.textContent = label;
  const deleteBtn = document.createElement('button');
  deleteBtn.type = 'button';
  deleteBtn.className = 'btn btn-outline-danger btn-sm ms-1';
  deleteBtn.id = `${id}_delete`;
  deleteBtn.textContent = '🗑';
  viewBtn.addEventListener('click', () => {
    Swal.fire({
      title: label,
      imageUrl: base64Data,
      imageAlt: label,
      confirmButtonText: 'Close'
    });
  });
  deleteBtn.addEventListener('click', () => {
    Swal.fire({
      title: `Delete "${label}"?`,
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel'
    }).then(result => {
      if (result.isConfirmed) {
        wrapper.remove();
      }
    });
  });
  wrapper.appendChild(viewBtn);
  wrapper.appendChild(deleteBtn);
  container.appendChild(wrapper);
}
function loadMedicalCondition() {
  const $conditionSelect = $('#conditionSelect');
  if ($conditionSelect.hasClass('select2-hidden-accessible')) {
    $conditionSelect.select2('destroy');
  }
  $conditionSelect.html('<option disabled selected>Loading...</option>');
  apiRequest({
    url: '/UserEmployee/getMedicalCondition',
    method: 'GET',
    dataType: 'json',
    onSuccess: function (response) {
      if (response.result && Array.isArray(response.data)) {
        let options = '<option disabled selected value="">Select condition</option>';
        response.data.forEach(function (condition) {
          options += `<option value="${condition.condition_id}">${condition.condition_name}</option>`;
        });
        $conditionSelect.html(options);
        $conditionSelect.select2({
          placeholder: 'Select condition',
          width: '100%'
        });
      } else {
        showToast('info', 'Notice', response.message || 'No conditions found.');
        $conditionSelect.html('<option disabled>No conditions found</option>');
      }
    }
  });
}
const doctorMap = {
  101: 'Dr. Aditi Verma',
  102: 'Dr. Rajeev Kumar',
  103: 'Dr. Meera Singh',
  other: 'Other'
};
const hospitalMap = {
  1: 'City Hospital',
  2: 'State Medical',
  other: 'Other'
};
('use strict');
let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
  flatpickr('#date', {
    enableTime: false,
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'Y/m/d',
    allowInput: true
  });
  (function () {
    const formAddNewRecord = document.getElementById('form-add-new-record');
    setTimeout(() => {
      const newRecord = document.querySelector('.create-new'),
        offCanvasElement = document.querySelector('#add-new-record');
      if (newRecord) {
        newRecord.addEventListener('click', function () {
          offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
          offCanvasElement.querySelector('.dt-full-name').value = '';
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
      ajax: {
        url: 'https://login-users.hygeiaes.com/UserEmployee/getHospitalizationDetails',
        data: function (d) {
          var fromDate = $('#fromDate').val();
          var toDate = $('#toDate').val();
          if (fromDate) {
            d.from_date = fromDate;
          }
          if (toDate) {
            d.to_date = toDate;
          }
          console.log('Sending From Date:', fromDate);
          console.log('Sending To Date:', toDate);
        },
        dataSrc: function (json) {
          if (!json.result) {
            toastr.error('Failed to fetch data: ' + json.data);
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
            return !row.doctor_id || row.doctor_id === 0
              ? row.doctor_name || 'Unknown'
              : doctorMap[row.doctor_id] || 'Unknown';
          }
        },
        {
          data: null,
          title: 'Hospital Name',
          render: function (row) {
            return !row.hospital_id || row.hospital_id === 0
              ? row.hospital_name || 'Unknown'
              : hospitalMap[row.hospital_id] || 'Unknown';
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
      order: [[0, 'desc']],
      searching: true,
      paging: false,
      lengthChange: false,
      dom:
        '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-2 pt-md-0"B>>' +
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n2 mt-md-0"f>>t' +
        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
        {
          extend: 'excelHtml5',
          text: 'Export to Excel',
          filename: function () {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();
            const formattedDate = `${dd}-${mm}-${yyyy}`;
            return `Invoice - ${formattedDate}`;
          },
          className: 'btn btn-success d-none',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5]
          }
        }
      ],
      initComplete: function () {
        var count = dt_basic.data().count();
        $('#employeeTypeLabel').text(`List of Hospitalization (${count})`);
        this.api().buttons().container().appendTo('#export-buttons');
      }
    });
    $('#DataTables_Table_0_filter label')
      .contents()
      .filter(function () {
        return this.nodeType === 3;
      })
      .remove();
    $('#DataTables_Table_0_filter input')
      .css({
        width: '325px',
        height: '37px',
        display: 'inline-block',
        margin: '0'
      })
      .attr('placeholder', 'Search By Hospital Name / Doctor Name');
    const searchInput = $('#DataTables_Table_0_filter').detach();
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
    $('.card-header').after(customHeaderRow);
    $('#customSearchContainer').append(searchInput);
    $('#searchBtn').on('click', function () {
      var fromDate = $('#fromDate').val();
      var toDate = $('#toDate').val();
      var status = $('#status').val();
      var queryParams = {};
      if (fromDate) {
        fromDate = moment(fromDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
        queryParams.from_date = fromDate;
      }
      if (toDate) {
        toDate = moment(toDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
        queryParams.to_date = toDate;
      }
      if (status) {
        queryParams.status = status;
      }
      console.log('Query Params:', queryParams);
      var newUrl = dt_basic.ajax.url().split('?')[0];
      var urlWithParams = newUrl + '?' + $.param(queryParams);
      dt_basic.ajax.url(urlWithParams).load(function () {
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
  flatpickr('#fromDate', {
    dateFormat: 'd/m/Y'
  });
  flatpickr('#toDate', {
    dateFormat: 'd/m/Y'
  });
});
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
$(document).on('click', '.preview-report', function (e) {
  e.preventDefault();
  const imgUrl = $(this).data('url');
  $('#previewImage').attr('src', imgUrl).removeClass('d-none');
  $('#downloadAttachment').attr('href', imgUrl);
  $('#downloadBtnWrapper').removeClass('d-none');
});
$(document).on('click', '.view-discharge', function () {
  const dischargeUrl = $(this).data('url');
  $('#reportList').empty();
  $('#previewImage').attr('src', dischargeUrl).removeClass('d-none');
  $('#downloadAttachment').attr('href', dischargeUrl).removeClass('d-none');
  $('#downloadBtnWrapper').removeClass('d-none');
  $('#reportModal').modal('show');
});
$(function () {
  function showError(input, message) {
    const $input = $(input);
    $input.addClass('is-invalid');
    if ($input.next('.invalid-feedback').length === 0) {
      $input.after(`<div class="invalid-feedback">${message}</div>`);
    } else {
      $input.next('.invalid-feedback').text(message);
    }
  }
  function clearError(input) {
    const $input = $(input);
    $input.removeClass('is-invalid');
    $input.next('.invalid-feedback').remove();
  }
  function clearAllErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
  }
  $('#hospital_id').on('change', function () {
    const selected = $(this).val();
    $('#hospital_name_div').toggle(selected === '0');
    clearError(this);
    if (selected !== '0') {
      $('input[name="hospital_name"]').val('');
      clearError('input[name="hospital_name"]');
    }
  });
  $('#doctor_id').on('change', function () {
    const selected = $(this).val();
    $('#doctor_name_div').toggle(selected === '0');
  });
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
    if (!hospitalId) {
      showError('#hospital_id', 'Please select a hospital.');
      isValid = false;
    } else if (hospitalId === '0' && hospitalName === '') {
      showError('input[name="hospital_name"]', 'Please enter the hospital name.');
      isValid = false;
    }
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
    apiRequest({
      url: '/UserEmployee/store',
      method: 'POST',
      data: formData,
      onSuccess: data => {
        if (data.result) {
          showToast('success', data.message || 'Hospitalization record added successfully');
          setTimeout(() => {
            window.location.href = 'https://login-users.hygeiaes.com/UserEmployee/hospitalization';
          }, 1500);
        } else {
          showToast('warning', data.message || 'Something went wrong while updating');
        }
      },
      onError: errorMessage => {
        console.error('Submission failed:', errorMessage);
        showToast('error', `Error submitting hospitalization data: ${errorMessage}`);
      }
    });
  });
});
document.addEventListener('DOMContentLoaded', function () {
  const hospitalIdSelect = document.getElementById('hospital_id');
  const hospitalNameInput = document.getElementById('hospital_name');
  const hospitalNameDiv = document.getElementById('hospital_name_div');
  const hospitalValue = document.getElementById('hospital_value').value.trim();
  const knownHospitalIds = ['1', '2', '3'];
  if (knownHospitalIds.includes(hospitalValue)) {
    hospitalIdSelect.value = hospitalValue;
    hospitalNameDiv.style.display = 'none';
    hospitalNameInput.value = '';
  } else if (hospitalValue) {
    hospitalIdSelect.value = '0';
    hospitalNameDiv.style.display = 'block';
    hospitalNameInput.value = hospitalValue;
  } else {
    hospitalIdSelect.value = '';
    hospitalNameDiv.style.display = 'none';
    hospitalNameInput.value = '';
  }
  hospitalIdSelect.addEventListener('change', function () {
    if (this.value === '0') {
      hospitalNameDiv.style.display = 'block';
    } else {
      hospitalNameDiv.style.display = 'none';
      hospitalNameInput.value = '';
    }
  });
});

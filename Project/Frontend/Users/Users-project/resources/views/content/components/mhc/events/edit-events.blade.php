@extends('layouts/layoutMaster')
@section('title', 'Events - Create Event')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/form-layouts.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Create Event</h5>
    </div>
    <div class="card-body">
        <form id="eventForm" method="POST" action="javascript:void(0);">
            @csrf
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="event_name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                    <div class="invalid-feedback d-block" id="event_name_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="from_date" class="form-label">From Date & Time</label>
                    <input type="text" class="form-control flatpickr-date-time" id="from_date" name="from_date" placeholder="Select start date & time" required>
                    <div class="invalid-feedback d-block" id="from_date_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="to_date" class="form-label">To Date & Time</label>
                    <input type="text" class="form-control flatpickr-date-time" id="to_date" name="to_date" placeholder="Select end date & time" required>
                    <div class="invalid-feedback d-block" id="to_date_error"></div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="event_description" class="form-label">Event Description</label>
                    <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
                    <div class="invalid-feedback d-block" id="event_description_error"></div>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="guest_name" class="form-label">Guest Name</label>
                    <input type="text" class="form-control" id="guest_name" name="guest_name">
                    <div class="invalid-feedback d-block" id="guest_name_error"></div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select select2" id="department" name="department" required>
                        <option value="">Select Department</option>
                    </select>
                    <div class="invalid-feedback d-block" id="department_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="employee_type" class="form-label">Employee Type</label>
                    <select class="form-select select2" id="employee_type" name="employee_type" required>
                        <option value="">Select Employee Type</option>
                    </select>
                    <div class="invalid-feedback d-block" id="employee_type_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="test" class="form-label">Test</label>
                    <select class="form-select select2" id="test" name="test" required>
                        <option value="">Select Test</option>
                    </select>
                    <div class="invalid-feedback d-block" id="test_error"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
    </div>
</div>
<script>
    const EVENT_ID = @json($eventId);
document.addEventListener('DOMContentLoaded', function () {
  const loaded = {
    department: false,
    employee_type: false,
    test: false
  };

  // 1. Patch apiRequest to flag when dropdowns are loaded
  const originalApiRequest = window.apiRequest;
  window.apiRequest = function (options) {
    const key =
      options.url.includes('getDepartments') ? 'department' :
      options.url.includes('getEmployeeType') ? 'employee_type' :
      options.url.includes('getAllSubGroup') ? 'test' : null;

    if (key) {
      const originalSuccess = options.onSuccess;
      options.onSuccess = function (data) {
        if (originalSuccess) originalSuccess(data);
        loaded[key] = true;
      };
    }

    return originalApiRequest(options);
  };

  // 2. Load test dropdown options
  const loadTestsDropdown = () => {
    const testSelect = document.getElementById('test');
    testSelect.setAttribute('multiple', 'multiple');

    apiRequest({
      url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
      method: 'GET',
      dataType: 'json',
      onSuccess: function (response) {
        if (!response.result || !response.data) return;

        const selectElement = document.getElementById('test');
        selectElement.innerHTML = '';
        const subgroupsOptgroup = document.createElement('optgroup');
        subgroupsOptgroup.label = 'Test Groups';
        const testsInSubSubgroups = new Set();

        // Gather nested test IDs
        response.data.subgroups?.forEach(sg => {
          sg.subgroups?.forEach(ssg => {
            ssg.tests?.forEach(test => testsInSubSubgroups.add(test.master_test_id.toString()));
          });
        });

        response.data.subgroups?.forEach(sg => {
          const sgOption = document.createElement('option');
          sgOption.value = `sg_${sg.test_group_id}`;
          sgOption.textContent = `${sg.mother_group}: ${sg.test_group_name}`;
          sgOption.disabled = true;
          subgroupsOptgroup.appendChild(sgOption);

          sg.tests?.filter(t => !testsInSubSubgroups.has(t.master_test_id.toString()))
            .forEach(test => {
              const option = document.createElement('option');
              option.value = test.master_test_id;
              option.textContent = `  — ${test.test_name}`;
              subgroupsOptgroup.appendChild(option);
            });

          sg.subgroups?.forEach(ssg => {
            if (!ssg.tests?.length) return;
            const ssgOption = document.createElement('option');
            ssgOption.value = `ssg_${ssg.test_group_id}`;
            ssgOption.textContent = `  — ${ssg.test_group_name}`;
            ssgOption.disabled = true;
            subgroupsOptgroup.appendChild(ssgOption);

            ssg.tests.forEach(test => {
              const option = document.createElement('option');
              option.value = test.master_test_id;
              option.textContent = `    — ${test.test_name}`;
              subgroupsOptgroup.appendChild(option);
            });
          });
        });

        if (subgroupsOptgroup.children.length) selectElement.appendChild(subgroupsOptgroup);

        if (response.data.individual_tests?.length) {
          const indivOpt = document.createElement('optgroup');
          indivOpt.label = 'Individual Tests';
          response.data.individual_tests.forEach(test => {
            const option = document.createElement('option');
            option.value = test.master_test_id || '';
            option.textContent = test.test_name || '';
            indivOpt.appendChild(option);
          });
          selectElement.appendChild(indivOpt);
        }

        if (window.$ && $.fn.select2) {
          $('#test').select2('destroy').select2({
            width: '100%',
            placeholder: 'Select Test',
            allowClear: true
          });
        }
      }
    });
  };
 const departmentSelect = document.getElementById('department');
        departmentSelect.setAttribute('multiple', 'multiple');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getDepartments',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (data) {
            if (data.result && Array.isArray(data.data)) {
                departmentSelect.innerHTML = '';
                data.data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.hl1_id;
                option.textContent = dept.hl1_name;
                departmentSelect.appendChild(option);
                });
                if (window.$ && $.fn.select2) {
                $('#department').select2('destroy');
                $('#department').select2({
                    width: '100%',
                    placeholder: 'Select Department',
                    allowClear: true
                });
                $('#department').val(null).trigger('change');
                }
            }
            },
            onError: function (error) {
            // Optionally handle error
            }
        });
const prefillDepartments = (departments = []) => {
  const departmentIds = departments.map(dep => dep.hl1_id);
  $('#department').val(departmentIds).trigger('change');
}; 
 const prefillEmployeeTypes = (employeeTypes = []) => {
    const employeeTypeIds = employeeTypes.map(emp => emp.employee_type_id);
    $('#employee_type').val(employeeTypeIds).trigger('change');
  };
// Employee Type (multiselect)
        const employeeTypeSelect = document.getElementById('employee_type');
        employeeTypeSelect.setAttribute('multiple', 'multiple');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getEmployeeType',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (data) {
            if (data.result && Array.isArray(data.data)) {
                employeeTypeSelect.innerHTML = '';
                data.data.forEach(type => {
                const option = document.createElement('option');
                option.value = type.employee_type_id;
                option.textContent = type.employee_type_name;
                employeeTypeSelect.appendChild(option);
                });
                if (window.$ && $.fn.select2) {
                $('#employee_type').select2('destroy');
                $('#employee_type').select2({
                    width: '100%',
                    placeholder: 'Select Employee Type',
                    allowClear: true
                });
                $('#employee_type').val(null).trigger('change');
                }
            }
            },
            onError: function (error) {
            // Optionally handle error
            }
        });
  // 3. Wait until all dropdowns are loaded
  const waitUntilLoaded = () => {
    return new Promise(resolve => {
      const check = setInterval(() => {
        if (loaded.department && loaded.employee_type && loaded.test) {
          clearInterval(check);
          resolve();
        }
      }, 100);
    });
  };

  
  // 4. Prefill the form with event data
  const prefillForm = async (data) => {
    const event = data.event || {};
    const departments = data.departments || [];
    const employeeTypes = data.employeeTypes || [];
    const tests = data.tests || [];

    // Set basic fields
    document.getElementById('event_name').value = event.event_name || '';
    document.getElementById('from_date').value = event.from_datetime || '';
    document.getElementById('to_date').value = event.to_datetime || '';
    document.getElementById('event_description').value = event.event_description || '';
    document.getElementById('guest_name').value = event.guest_name || '';

    await waitUntilLoaded();

    // Extract IDs and set dropdown values
    const departmentIds = departments.map(dep => dep.hl1_id);
    const employeeTypeIds = employeeTypes.map(emp => emp.employee_type_id);
    const testIds = tests.map(test => test.master_test_id);

    $('#department').val(departmentIds).trigger('change');
    $('#employee_type').val(employeeTypeIds).trigger('change');
    $('#test').val(testIds).trigger('change');
  };

  // 5. Initialize dropdown loaders
  loadTestsDropdown();
  // Load your department and employee type dropdowns similarly here

  // 6. Fetch event data
  fetch(`/mhc/events/modify-events/${EVENT_ID}`)
    .then(res => res.json())
    .then(res => {
      if (res.result && res.data) {
        const data = res.data;
      // Fill basic fields
      document.getElementById('event_name').value = data.event?.event_name || '';
      document.getElementById('from_date').value = data.event?.from_datetime || '';
      document.getElementById('to_date').value = data.event?.to_datetime || '';
      document.getElementById('event_description').value = data.event?.event_description || '';
      document.getElementById('guest_name').value = data.event?.guest_name || '';

      waitUntilLoaded().then(() => {
        prefillDepartments(data.departments);
        // other dropdowns will be handled separately
      });
      } else {
        Swal.fire('Error', 'Unable to fetch event data', 'error');
      }
    })
    .catch(() => {
      Swal.fire('Error', 'An error occurred while fetching event data', 'error');
    });
});

</script>
@endsection
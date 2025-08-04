@extends('layouts/layoutMaster')
@section('title', 'Reports - Health Risk Reports')

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/charts-chartjs.js'])
@endsection
<script src="{{ asset('lib/js/page-scripts/health-risk-report.js') }}?v={{ time() }}"></script>

@section('content')

<style>
  .custom-legend {
    display: flex;
    justify-content: center;
    margin-top: 15px;
    gap: 20px;
    font-size: 14px;
    flex-wrap: wrap;
  }
  .custom-legend span {
    display: flex;
    align-items: center;
  }
  .legend-dot {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    margin-right: 6px;
    display: inline-block;
  }
  .form-label i {
    margin-right: 5px;
  }
</style>

<div class="container-fluid">

  <!-- Filters Card -->
  <div class="card mb-4">
    <div class="card-body">
      <form id="filterForm" onsubmit="event.preventDefault(); userdata();">
        <div class="row g-4">
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-user"></i>Employee Type</label>
            <select class="form-select select2" id="jobvalue" onchange="userdata()">
              <!-- Will be populated via JS -->
            </select>

          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-sitemap"></i>Department</label>
           <select class="form-select select2" id="department" onchange="userdata()">
            <!-- Options will be populated dynamically -->
          </select>

          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-calendar"></i>From Date</label>
            <input type="text" class="form-control datepick" id="frm"  onchange="userdata()">
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-calendar-check"></i>To Date</label>
            <input type="text" class="form-control datepick" id="to"   onchange="userdata()">
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-medkit"></i>Medical Condition</label>
           <select class="form-select select2" id="medtest" onchange="userdata()">
            <!-- Dynamically filled options -->
          </select>

          </div>
         
  <div class="col-md-3">
            <label class="form-label"><i class="fa fa-users"></i>Age Group</label>
            <select class="form-select select2" id="age" onchange="userdata()">
              <option value="">All Age Groups</option>
              <option value="20.0 and 30.0">20-30</option>
              <option value="30.1 and 40.0">31-40</option>
              <option value="40.1 and 50.0">41-50</option>
              <option value="50.1 and 60">51-60</option>
            </select>
          </div>
          <!-- Additional filters -->
          <div class="col-md-5">
            <label class="form-label"><i class="fa fa-bar-chart"></i>Report Type</label>
            <select class="form-select select2" name="reppag" onchange="userdata()" style="background-color: #fff;">
              <option value="1">Agewise Distribution of Health Parameters</option>
              <option value="2">Departmentwise Distribution of Health Parameters</option>
              <option value="3">Locationwise Distribution of Health Parameters</option>
              <option value="9">Locationwise Certification</option>
              <option value="10">Conditionwise Certification</option>
             
            </select>
          </div>
        
        </div>
      </form>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="card">
    <div class="card-body">
      <div style="position: relative; height: 400px;">
        <canvas id="eventBarChart"></canvas>
      </div>

      <div class="custom-legend mt-3">
        <span><span class="legend-dot" style="background-color: #FFBABA;"></span>Low</span>
        <span><span class="legend-dot" style="background-color: #B9FBC0;"></span>Normal</span>
        <span><span class="legend-dot" style="background-color: #FFF3B0;"></span>High</span>
      </div>

      <div id="range-info" class="text-center mt-3" style="font-size: 15px; font-weight: 500;"></div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
<script>
  document.addEventListener('DOMContentLoaded', function () {
  loadMedicalConditions();
  userdata(); // Draw chart
  loadEmployeeTypes();
  loadDepartments();

});
let areaChartInstance = null;

function userdata() {
  const ctx = document.getElementById('eventBarChart').getContext('2d');

  // Dummy sample data — replace with AJAX or dynamic logic
  const labels = ['20-30', '30-40', '40-50', '50+'];
  const lowData = [15, 40, 1, 0];
  const normalData = [88, 285, 13, 10];
  const highData = [1, 4, 0, 0];

  const barChartData = {
    labels: labels,
    datasets: [
      {
        label: 'Low',
        data: lowData,
        backgroundColor: '#FFBABA',
        stack: 'health'
      },
      {
        label: 'Normal',
        data: normalData,
        backgroundColor: '#B9FBC0',
        stack: 'health'
      },
      {
        label: 'High',
        data: highData,
        backgroundColor: '#FFF3B0',
        stack: 'health'
      }
    ]
  };

  const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      title: {
        display: true,
        text: 'Age-wise Distribution of Health Parameters',
        font: { size: 18, weight: '600' },
        padding: { bottom: 10 }
      },
      tooltip: {
        callbacks: {
          label: context => `${context.dataset.label}: ${context.raw}`
        }
      },
      legend: { display: false }
    },
    scales: {
      x: {
        stacked: true,
        title: {
          display: true,
          text: 'Age Group'
        }
      },
      y: {
        stacked: true,
        beginAtZero: true,
        title: {
          display: true,
          text: 'Number of Employees'
        }
      }
    }
  };

  document.getElementById('range-info').innerHTML = `
    <span style="color:#dc3545;">Low:</span> Below normal range &nbsp; |
    <span style="color:#28a745;">Normal:</span> Within normal range &nbsp; |
    <span style="color:#ffc107;">High:</span> Above normal range
  `;

  if (areaChartInstance) areaChartInstance.destroy();

  areaChartInstance = new Chart(ctx, {
    type: 'bar',
    data: barChartData,
    options: barChartOptions
  });
}

function loadMedicalConditions() {
  apiRequest({
    url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
    method: 'GET',
    onSuccess: function (response) {
      if (response.result && response.data && Array.isArray(response.data.subgroups)) {
        const medtest = document.getElementById('medtest');
        medtest.innerHTML = ''; // Clear existing options
        const allTests = new Set();

        response.data.subgroups.forEach(subgroup => {
          // Direct tests
          if (Array.isArray(subgroup.tests)) {
            subgroup.tests.forEach(test => addUniqueOption(test));
          }

          // Nested sub-subgroups
          if (Array.isArray(subgroup.subgroups)) {
            subgroup.subgroups.forEach(subSubgroup => {
              if (Array.isArray(subSubgroup.tests)) {
                subSubgroup.tests.forEach(test => addUniqueOption(test));
              }
            });
          }
        });

        function addUniqueOption(test) {
          if (!allTests.has(test.master_test_id)) {
            const option = document.createElement('option');
            option.value = test.master_test_id;
            option.text = test.test_name; // ✅ Corrected here
            medtest.appendChild(option);
            allTests.add(test.master_test_id);
          }
        }

        // Optional: Re-initialize select2
        if (window.jQuery && jQuery().select2) {
          $('#medtest').select2({
            placeholder: 'Select a medical condition',
            allowClear: true,
            width: '100%'
          });
        }

        // Initial chart update with first available test
        userdata();
      } else {
        console.warn('Unexpected API format:', response);
      }
    },
    onError: function (error) {
      console.error('API Error:', error);
    }
  });
}
function loadEmployeeTypes() {
  apiRequest({
    url: '/corporate/getEmployeeType', // your Laravel route
    method: 'GET',
    onSuccess: function (response) {
      const empSelect = document.getElementById('jobvalue');
      empSelect.innerHTML = '';

      // Add default "All" option
      const allOption = document.createElement('option');
      allOption.value = '0';
      allOption.textContent = 'All Employees';
      empSelect.appendChild(allOption);

      if (response.result && Array.isArray(response.data)) {
        response.data.forEach(type => {
          const option = document.createElement('option');
          option.value = type.employee_type_id;
          option.textContent = type.employee_type_name;
          empSelect.appendChild(option);
        });

        // Reinitialize Select2
        if (window.jQuery && jQuery().select2) {
          $('#jobvalue').select2({
            placeholder: 'Select Employee Type',
            allowClear: true,
            width: '100%'
          });
        }

        userdata(); // trigger chart refresh
      } else {
        console.warn('Unexpected employee type response:', response);
      }
    },
    onError: function (error) {
      console.error('Failed to load employee types:', error);
    }
  });
}
function loadDepartments() {
  apiRequest({
    url: '/corporate/getDepartments',
    method: 'GET',
    onSuccess: function (response) {
      const deptSelect = document.getElementById('department');
      deptSelect.innerHTML = '';

      // Default "All" option
      const defaultOption = document.createElement('option');
      defaultOption.value = '0';
      defaultOption.textContent = 'All Departments';
      deptSelect.appendChild(defaultOption);

      if (response.result && Array.isArray(response.data)) {
        response.data.forEach(dept => {
          const option = document.createElement('option');
          option.value = dept.hl1_id ?? dept.id; // adjust key names if needed
          option.textContent = dept.hl1_name ?? dept.name; // adjust key names if needed
          deptSelect.appendChild(option);
        });

        // Re-init Select2
        if (window.jQuery && jQuery().select2) {
          $('#department').select2({
            placeholder: 'Select Department',
            allowClear: true,
            width: '100%'
          });
        }

        userdata(); // refresh chart
      } else {
        console.warn('Unexpected response structure for departments:', response);
      }
    },
    onError: function (error) {
      console.error('Failed to load departments:', error);
    }
  });
}

</script>

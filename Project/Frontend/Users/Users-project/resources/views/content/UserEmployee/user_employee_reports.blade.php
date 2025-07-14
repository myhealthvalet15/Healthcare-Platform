@extends('layouts/layoutMaster')

@section('title', 'Chart.js - Charts')

@section('vendor-script')
@vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/charts-chartjs.js'])
@endsection

@section('content')
<style>
  /* Override Bootstrap striping (odd/even rows) for this table */
  #testResultTable table.table-striped tbody tr:nth-child(odd),
  #testResultTable table.table-striped tbody tr:nth-child(even) {
    background-color: #fff !important;
  }
</style>


<div class="mb-4">
  <label for="select2Primary_tests" class="form-label">Select Test</label>
  <select id="select2Primary_tests" class="form-select" style="width: 100%;">
    <option value="">-- Select a Test --</option>
  </select>
</div>


<div class="row">
  <div class="col-12 mb-6">
    <div class="card">      
      <div class="card-body pt-2">
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
          <!-- Chart container (left side) -->
          <div style="flex: 1 1 55%; min-width: 300px;">
            <canvas id="lineChart" class="chartjs" data-height="450"></canvas>
          </div>
          <!-- Table container (right side) -->
          <div id="testResultTable" style="flex: 1 1 40%; min-width: 250px; display: none;margin-top: 54px;">
            <!-- Dynamic table will be inserted here -->
          </div>
           </div><div id="chartLegend" style="margin-top: 10px;"></div>
        </div>
      </div>
    </div>
 

</div>

<script>
    const masterUserId = "{{ session('master_user_user_id') }}";
  document.addEventListener('DOMContentLoaded', function () {
    const selectElement = document.getElementById('select2Primary_tests');

    apiRequest({
        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
        method: 'GET',
        onSuccess: function (response) {
            if (response.result && response.data && Array.isArray(response.data.subgroups)) {
                const allTests = new Set();

                response.data.subgroups.forEach(subgroup => {
                    // Add direct tests from subgroup
                    if (Array.isArray(subgroup.tests)) {
                        subgroup.tests.forEach(test => {
                            if (!allTests.has(test.master_test_id)) {
                                addOption(test);
                                allTests.add(test.master_test_id);
                            }
                        });
                    }

                    // Add tests from sub-subgroups
                    if (Array.isArray(subgroup.subgroups)) {
                        subgroup.subgroups.forEach(subSubgroup => {
                            if (Array.isArray(subSubgroup.tests)) {
                                subSubgroup.tests.forEach(test => {
                                    if (!allTests.has(test.master_test_id)) {
                                        addOption(test);
                                        allTests.add(test.master_test_id);
                                    }
                                });
                            }
                        });
                    }
                });

                // Optional: Initialize Select2 (single select)
                if (window.jQuery && window.jQuery().select2) {
                    $('#select2Primary_tests').select2({
                        placeholder: 'Select a test',
                        allowClear: true,
                        width: '100%',
                    });
                }
            } else {
                console.warn('Unexpected API format:', response);
            }
        },
        onError: function (error) {
            console.error('API Error:', error);
        }
    });

    function addOption(test) {
        const option = document.createElement('option');
        option.value = test.master_test_id;
        option.textContent = test.test_name;
        selectElement.appendChild(option);
    }
});

function formatSelection(option) {
    return option.text;
}
 $('#select2Primary_tests').on('change', function () {
    const selectedTestId = $(this).val(); // this will be the selected value
    const master_user_id = "{{ session('master_user_user_id') }}";
    if (!master_user_id || !selectedTestId) {
        console.warn('Missing user ID or test ID');
        return;
    }

    apiRequest({
        url: `/UserEmployee/getEmployeeTestForGraph/${master_user_id}/${selectedTestId}`,
        method: 'GET',
        onSuccess: function (response) {
            console.log();
            console.log('Data received:', response);
            // Call your chart/table update function here
            updateChartWithData(response.data);
        },
        onError: function (error) {
            console.error('Error fetching test data:', error);
        }
    });
});





let lineChartInstance = null;

function updateChartWithData(data) {
  const canvas = document.getElementById('lineChart');

  // Destroy existing chart if any
  const existingChart = Chart.getChart(canvas);
  if (existingChart) existingChart.destroy();

  // Sort data by date
  data.sort((a, b) => new Date(a.test_date) - new Date(b.test_date));

  const testName = data[0].test_name || 'Test';
  const unit = data[0].unit || '';

  // Extract reference ranges safely
  let mMin = -Infinity, mMax = Infinity;
  try {
    const male = JSON.parse(data[0].m_min_max || '{}');
    mMin = parseFloat(male.min);
    mMax = parseFloat(male.max);
  } catch (e) {
    console.warn("Invalid m_min_max");
  }

  // Build arrays for results and labels
  const results = [], labels = [], pointColors = [];

  data.forEach(item => {
    const date = new Date(item.test_date);
    const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
    const value = parseFloat(item.test_result);

    // Assume Male by default (you can enhance with gender info later)
    let status = 'Normal';
    if (value < mMin) status = 'Low';
    else if (value > mMax) status = 'High';

    const color = status === 'Low' ? 'orange' : status === 'High' ? 'red' : 'green';

    results.push(value);
    labels.push(`${formattedDate} (${value})`);
    pointColors.push(color);
  });

  // Draw chart with colored points
  const ctx = canvas.getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: `${testName} (${unit})`,
        data: results,
        borderColor: '#42a5f5',
        backgroundColor: 'rgba(66, 165, 245, 0.2)',
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: pointColors // ✅ Color each point by status
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          ticks: { color: '#6e6b7b' },
          grid: { color: '#ebedf3', drawBorder: false }
        },
        y: {
          beginAtZero: false,
          ticks: { color: '#6e6b7b' },
          grid: { color: '#ebedf3', drawBorder: false },
          title: {
            display: true,
            text: `Result (${unit})`
          }
        }
      },
      plugins: {
        legend: {
          position: 'top',
          align: 'start',
          labels: {
            usePointStyle: true,
            color: '#5e5873'
          }
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const value = context.raw;
              const color = context.dataset.pointBackgroundColor[context.dataIndex];
              let status = '';
              if (color === 'red') status = ' (High)';
              else if (color === 'orange') status = ' (Low)';
              else if (color === 'green') status = ' (Normal)';
              return `${testName}: ${value} ${unit}${status}`;
            }
          }
        }
      }
    } 
  });

  // Optional: Add the results + reference tables (same as before)
 
  // =========================
  // ✅ Results Table HTML
  // =========================
  let tableHTML = `
    <table cellpadding="5" cellspacing="5" width="100%" border="1" class="table table-bordered">
      <thead>
        <tr style="line-height: 35px; background-color: rgb(107, 27, 199); color:#fff;">
          <th colspan="2" style="color:#fff;" class="text-center fw-bold">${testName} (${unit})</th>
        </tr>
        <tr style="background-color:#d3d3d3; text-align:center; color: #333;">
          <th style="width: 50%;">Date</th>
          <th>Result</th>
        </tr>
      </thead>
      <tbody style="background-color:#fff !important;">
  `;

  data.forEach(item => {
    const date = new Date(item.test_date);
    const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;
    tableHTML += `
      <tr style="background-color:#fff;">
        <td class="text-center">${formattedDate}</td>
        <td class="text-center">${item.test_result}</td>
      </tr>
    `;
  });

  tableHTML += '</tbody></table>';
  let mRange = '', fRange = '';
  try {
    const male = JSON.parse(data[0].m_min_max);
    const female = JSON.parse(data[0].f_min_max);
    mRange = `${male.min} - ${male.max}`;
    fRange = `${female.min} - ${female.max}`;
  } catch (e) {
    console.warn('Invalid min/max format');
  }

  const referenceHTML = `
    <table cellpadding="5" cellspacing="5" width="100%" border="1" class="table table-bordered mt-4">
      <thead>
        <tr style="line-height: 35px;background-color: rgb(107, 27, 199); color:#fff;">
          <th colspan="2" class="text-center fw-bold" style="color:#fff;">${testName} - Reference Range</th>
        </tr>
        <tr style="background-color:#d3d3d3;text-align:center; color: #333;">
          <th>Category</th>
          <th>Normal Range (${unit})</th>
        </tr>
      </thead>
      <tbody style="background-color: #fff !important;">
        <tr>
          <td class="text-left">Male</td>
          <td class="text-center">${mRange}</td>
        </tr>
        <tr>
          <td class="text-left">Female</td>
          <td class="text-center">${fRange}</td>
        </tr>
      </tbody>
    </table>
  `;

  // ✅ Render both tables
  const tableContainer = document.getElementById('testResultTable');
  tableContainer.innerHTML = tableHTML + referenceHTML;
  tableContainer.style.display = 'block';  // =========================
  // ✅ Custom Legend (Color Meaning)
  // =========================
  const legendHTML = `
    <div class="custom-legend mt-3" style="display: flex; gap: 20px; align-items: center; font-size: 14px;">
      <div style="display: flex; align-items: center;">
        <span style="width: 14px; height: 14px; background-color: green; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> Normal
      </div>
      <div style="display: flex; align-items: center;">
        <span style="width: 14px; height: 14px; background-color: orange; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> Low
      </div>
      <div style="display: flex; align-items: center;">
        <span style="width: 14px; height: 14px; background-color: red; display: inline-block; margin-right: 6px; border-radius: 50%;"></span> High
      </div>
    </div>
  `;

  const legendContainer = document.getElementById('chartLegend');
  legendContainer.innerHTML = legendHTML;
  legendContainer.style.display = 'flex';


}

</script>

@endsection

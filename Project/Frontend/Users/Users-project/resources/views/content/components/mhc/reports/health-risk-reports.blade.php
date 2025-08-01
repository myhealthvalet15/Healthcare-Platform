@extends('layouts/layoutMaster')
@section('title', 'Reports - Health Risk Reports')

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/charts-chartjs.js'])
@endsection

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
  .form-section {
    margin-bottom: 25px;
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
              <option value="0">All Employees</option>
              <option value="3">Contract</option>
              <option value="4">Executive</option>
              <option value="5">Apprentice</option>
              <option value="7">NEEM</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-sitemap"></i>Department</label>
            <select class="form-select select2" id="department" onchange="userdata()">
              <option value="0">All Departments</option>
              <option value="311087">Accounts</option>
              <option value="310908">Admin</option>
              <option value="311407">All BU</option>
              <option value="311409">All Plant</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-calendar"></i>From Date</label>
            <input type="text" class="form-control datepick" id="frm" value="31/07/2024" onchange="userdata()">
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-calendar-check"></i>To Date</label>
            <input type="text" class="form-control datepick" id="to" value="31/07/2025" onchange="userdata()">
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-medkit"></i>Medical Condition</label>
            <select class="form-select select2" id="medtest" onchange="userdata()">
              <option value="51">RBC Count</option>
              <option value="52">Haemoglobin</option>
              <option value="53">Hct (PCV)</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-map-marker"></i>Location</label>
            <select class="form-select select2" id="location" onchange="userdata()">
              <option value="0">All Locations</option>
              <option value="1">Plant 1</option>
              <option value="2">Plant 2</option>
              <option value="3">Head Office</option>
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
  <canvas id="eventLineChart"></canvas>
</div>
<div class="custom-legend mt-3">
  <span><span class="legend-dot" style="background-color: #FFBABA;"></span>Low</span>
  <span><span class="legend-dot" style="background-color: #B9FBC0;"></span>Normal</span>
  <span><span class="legend-dot" style="background-color: #FFF3B0;"></span>High</span>
</div>

    <!-- 🔽 Add this -->
   <div id="range-info" class="text-center mt-3" style="font-size: 15px; font-weight: 500;"></div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let lineChartInstance = null;

function userdata() {
  const ctx = document.getElementById('eventLineChart').getContext('2d');
  const testId = document.getElementById('medtest').value;

  const dataMap = {
    '51': {
      label: 'RBC Count',
      data: [2.5, 4.3, 3.3, 4.2, 4.5, 1.6, 4.8],
      normal: [4.0, 4.6],
      high: 4.8
    },
    '52': {
      label: 'Haemoglobin',
      data: [11.5, 13.2, 13.8, 13.6, 14.0, 13.9, 15.8],
      normal: [12.5, 15.5],
      high: 16.0
    },
    '53': {
      label: 'Hct (PCV)',
      data: [36, 43, 41, 44, 45, 46, 48],
      normal: [38, 47],
      high: 49
    }
  };

  const selected = dataMap[testId];
  const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];

  const lineData = {
    labels: labels,
    datasets: [{
      label: selected.label,
      data: selected.data,
      fill: false,
      borderColor: '#4B8BBE',
      backgroundColor: '#4B8BBE',
      tension: 0.3,
      pointRadius: 5,
      pointHoverRadius: 6,
      pointBackgroundColor: selected.data.map(val => {
        if (val < selected.normal[0]) return '#FFBABA';
        if (val > selected.normal[1]) return '#FFF3B0';
        return '#B9FBC0';
      })
    }]
  };

  const rangeInfo = document.getElementById('range-info');
  rangeInfo.innerHTML = `
    <span style="color:#dc3545;">Low:</span> Less than ${selected.normal[0]} &nbsp; | 
    <span style="color:#28a745;">Normal:</span> ${selected.normal[0]} to ${selected.normal[1]} &nbsp; | 
    <span style="color:#ffc107;">High:</span> More than ${selected.normal[1]}
  `;

  const lineOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      title: {
        display: true,
        text: selected.label + ' Trend Over Months',
        font: { size: 18, weight: '600' },
        padding: { bottom: 10 }
      },
      tooltip: {
        callbacks: {
          label: context => `${selected.label}: ${context.raw}`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        suggestedMax: selected.high + 2,
        ticks: { stepSize: 1 },
        grid: { color: 'rgba(0,0,0,0.05)' }
      },
      x: { grid: { display: false } }
    }
  };

  // Destroy previous chart
  if (lineChartInstance) lineChartInstance.destroy();

  lineChartInstance = new Chart(ctx, {
    type: 'line',
    data: lineData,
    options: lineOptions
  });
}

document.addEventListener('DOMContentLoaded', userdata);
</script>

@endsection

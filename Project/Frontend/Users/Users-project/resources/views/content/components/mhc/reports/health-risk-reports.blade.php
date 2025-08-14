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
  .chart-menu {
    position: absolute;
    right: 15px;
    top: 15px;
    z-index: 5;
  }
</style>

<div class="container-fluid">

  <!-- Chart Menu -->
  <div class="dropdown chart-menu">
    <button class="btn btn-light dropdown-toggle" type="button" id="chartMenu" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa fa-bars"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="chartMenu">
      <li><a class="dropdown-item" href="#" onclick="viewFullScreen()">View in full screen</a></li>
      <li><a class="dropdown-item" href="#" onclick="printChart()">Print chart</a></li>
      <li><a class="dropdown-item" href="#" onclick="downloadChart('png')">Download PNG image</a></li>
      <li><a class="dropdown-item" href="#" onclick="downloadChart('jpeg')">Download JPEG image</a></li>
      <li><a class="dropdown-item" href="#" onclick="downloadChart('svg')">Download SVG vector image</a></li>
    </ul>
  </div>

  <!-- Report Type Card -->
  <div class="card mb-3">
    <div class="card-body">
      <form id="reportTypeForm" onsubmit="event.preventDefault(); userdata();">
        <div class="row">
          <div class="col-md-5">
            <label class="form-label"><i class="fa fa-bar-chart"></i>Report Type</label>
            <select class="form-select select2" id="reppag" name="reppag" onchange="userdata()" style="background-color: #fff;">
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

  <!-- Filters Card -->
  <div class="card mb-4">
    <div class="card-body">
      <form id="filterForm" onsubmit="event.preventDefault(); userdata();">
        <div class="row g-4">
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-user"></i>Employee Type</label>
            <select class="form-select select2" id="jobvalue" onchange="userdata()"></select>
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-sitemap"></i>Department</label>
            <select class="form-select select2" id="department" onchange="userdata()"></select>
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
            <select class="form-select select2" id="medtest" onchange="userdata()"></select>
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
        </div>
      </form>
    </div>
  </div>

  <!-- Chart and Table Tabs -->
  <div class="card">
    <div class="card-body">
      <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chartTabContent" type="button" role="tab">Chart View</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#tableTabContent" type="button" role="tab">Table View</button>
        </li>
      </ul>

      <div class="tab-content" id="reportTabsContent">
        <!-- Chart Tab -->
        <div class="tab-pane fade show active" id="chartTabContent" role="tabpanel" aria-labelledby="chart-tab">
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

        <!-- Table Tab -->
        <div class="tab-pane fade" id="tableTabContent" role="tabpanel" aria-labelledby="table-tab">
          <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered" id="agewise" width="100%">
              <thead>
                <tr style="background-color:#4444e5; color:#fff;">
                  <th style="color:#fff;">Date</th>
                  <th style="color:#fff;" >Name---(ID)/<br>Designation</th>
                  <th style="color:#fff;" > Location</th>
                  <th style="color:#fff;" >Department</th>
                  <th style="color:#fff;" >Age Group</th>
                  <th style="color:#fff;"  >Haemoglobin (Hb)</th>
                  <th style="color:#fff;">Remarks</th>
                </tr>
              </thead>
              <tbody id="table-body">
                <tr>
                  <td colspan="7" align="center">No data available. Please filter and load results.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
function downloadChart(format) {
  const chartCanvas = document.getElementById('eventBarChart');
  const link = document.createElement('a');

  if (format === 'svg') {
    alert('SVG export is not natively supported by Chart.js.');
    return;
  }

  link.href = chartCanvas.toDataURL(`image/${format}`);
  link.download = `chart.${format}`;
  link.click();
}

function printChart() {
  const chartCanvas = document.getElementById('eventBarChart');
  const win = window.open('', '_blank');
  win.document.write(`<img src="${chartCanvas.toDataURL()}" style="width:100%">`);
  win.document.close();
  win.print();
}

function viewFullScreen() {
  const chartCanvas = document.getElementById('eventBarChart');
  if (chartCanvas.requestFullscreen) {
    chartCanvas.requestFullscreen();
  } else if (chartCanvas.webkitRequestFullscreen) {
    chartCanvas.webkitRequestFullscreen();
  } else if (chartCanvas.msRequestFullscreen) {
    chartCanvas.msRequestFullscreen();
  }
}
</script>

@endsection

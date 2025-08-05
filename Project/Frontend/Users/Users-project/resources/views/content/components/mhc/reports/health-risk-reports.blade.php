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
            <input type="text" class="form-control datepick" id="frm" value="31/07/2024" onchange="userdata()">
          </div>
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-calendar-check"></i>To Date</label>
            <input type="text" class="form-control datepick" id="to" value="31/07/2025" onchange="userdata()">
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
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

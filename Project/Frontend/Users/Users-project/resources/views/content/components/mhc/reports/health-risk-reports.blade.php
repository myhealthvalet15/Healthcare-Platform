@extends('layouts/layoutMaster')
@section('title', 'Reports - Health Risk Reports')

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/charts-chartjs.js'])
@endsection

<script src="/lib/js/page-scripts/health-risk-report.js?v={{ time() }}"></script>
@section('content')
<div class="container-fluid">
  <div class="card">
   
    <div class="card-body">
      <form id="filterForm" onsubmit="event.preventDefault(); userdata();">
        <div class="row g-3">

          {{-- Employee Type --}}
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-user me-1"></i>Employee Type</label>
            <select class="form-select select2" id="jobvalue" name="jobvalue" onchange="userdata()">
              <option value="0">All Employees</option>
              <option value="3">Contract</option>
              <option value="4">Executive</option>
              <option value="5">Apprentice</option>
              <option value="7">NEEM</option>
            </select>
          </div>

          {{-- Department --}}
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-sitemap me-1"></i>Department</label>
            <select class="form-select select2" id="department" name="department" onchange="userdata()">
              <option value="0">All Departments</option>
              <option value="311087">Accounts</option>
              <option value="310908">Admin</option>
              <option value="311407">All BU</option>
              <option value="311409">All Plant</option>
              {{-- Add the rest of your options --}}
            </select>
          </div>

          {{-- Date Range --}}
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-calendar me-1"></i>From Date</label>
            <input type="text" class="form-control datepick" id="frm" name="frm" value="31/07/2024" placeholder="From Date" onchange="userdata()">
          </div>

          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <input type="text" class="form-control datepick" id="to" name="to" value="31/07/2025" placeholder="To Date" onchange="userdata()">
          </div>

          {{-- Medical Condition --}}
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-medkit me-1"></i>Medical Condition</label>
            <select class="form-select select2" id="medtest" name="medtest" onchange="userdata()">
              <option value="51">RBC Count</option>
              <option value="52">Haemoglobin</option>
              <option value="53">Hct (PCV)</option>
              {{-- Add more test options --}}
            </select>
          </div>

          {{-- Location --}}
          <div class="col-md-3">
            <label class="form-label"><i class="fa fa-map-marker me-1"></i>Location</label>
            <select class="form-select select2" id="location" name="location" onchange="userdata()">
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

  {{-- Chart Section --}}
  <div class="card mt-4">
    
    <div class="card-body">
      <canvas id="eventChart" height="100"></canvas>
    </div>
  </div>
</div>
@endsection



@extends('layouts/layoutMaster')
@section('title', 'Chart.js - Charts')
@section('vendor-script')
@vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/charts-chartjs.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/user_employee_reports.css">
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
          <div style="flex: 1 1 55%; min-width: 300px;">
            <canvas id="lineChart" class="chartjs" data-height="450"></canvas>
          </div>
          <div id="testResultTable" style="flex: 1 1 40%; min-width: 250px; display: none;margin-top: 54px;">
          </div>
        </div>
        <div id="chartLegend" style="margin-top: 10px;"></div>
      </div>
    </div>
  </div>
</div>
<script>
  const masterUserId = "{{ session('master_user_user_id') }}";
</script>
<script src="/lib/js/page-scripts/user_employee_reports.js"></script>
@endsection
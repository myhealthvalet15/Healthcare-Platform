@extends('layouts/layoutMaster')
@section('title', 'Chart.js - Charts')
@section('vendor-script')
@vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/charts-chartjs.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/user_employee_reports.css?v=time()">
<div class="mb-4">
  <label for="select2Primary_tests" class="form-label">Select Test</label>
  <select id="select2Primary_tests" class="form-select" style="width: 100%;">
    <option value="">-- Select a Test --</option>
  </select>
</div>
<div style="display: flex; flex-direction: column; gap: 30px;">
  <div id="chartWrapper" style="display: flex; gap: 20px; width: 100%; height: 400px;">
    <div id="glucoseChartContainer" style="flex: 1 1 50%; min-width: 300px;">
      <canvas id="glucoseChart" style="height: 100%; width: 100%;"></canvas>
    </div>
    <div id="hbChartContainer" style="flex: 1 1 50%; min-width: 300px;">
      <canvas id="hbChart" style="height: 100%; width: 100%;"></canvas>
    </div>
  </div>
  <div id="resultTablesRow" style="display: flex; gap: 20px; width: 100%;">
    <div id="glucoseTables" style="flex: 1 1 50%; min-width: 300px;"></div>
    <div id="hbA1CTables" style="flex: 1 1 50%; min-width: 300px;"></div>
  </div>
  <div id="referenceTablesRow" style="display: flex; gap: 20px; width: 100%;">
    <div id="glucoseFastingRef" style="flex: 1 1 33%; min-width: 200px;"></div>
    <div id="glucosePostPrandialRef" style="flex: 1 1 33%; min-width: 200px;"></div>
    <div id="hbA1CRef" style="flex: 1 1 33%; min-width: 200px;"></div>
  </div>
  <div id="chartLegend" style="width: 100%; margin-top: 10px;"></div>
</div>
<script>
  const masterUserId = "{{ session('master_user_user_id') }}";
</script>
<script src="/lib/js/page-scripts/user_employee_reports_graph.js?v=time()"></script>
@endsection
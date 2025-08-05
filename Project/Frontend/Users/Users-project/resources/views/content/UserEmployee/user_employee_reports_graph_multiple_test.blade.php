@extends('layouts/layoutMaster')
@section('title', 'Chart.js - Charts')
@section('vendor-script')
@vite(['resources/assets/vendor/libs/chartjs/chartjs.js'])
@endsection
@section('page-script')
@vite(['resources/assets/js/charts-chartjs.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/user_employee_reports_graph_multiple_test.css?v=time()">
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
        <div id="lineChartContainer" style="width: 100%; height: 450px;">
          <canvas id="lineChart" class="chartjs" style="width: 100%; height: 100%;"></canvas>
        </div>
        <div id="testTablesContainer"
          style="margin-top: 20px; display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-between;">
          <div id="testResultTable" style="flex: 1 1 48%; min-width: 250px;"></div>
          <div id="referenceTable" style="flex: 1 1 48%; min-width: 250px;"></div>
        </div>
        <div id="chartLegend" style="margin-top: 10px;"></div>
      </div>
    </div>
  </div>
</div>
<script src="/lib/js/page-scripts/user_employee_reports_graph_multiple_test.js?v=time()"></script>
@endsection
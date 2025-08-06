@extends('layouts/layoutMaster')
@section('title', 'Pending Request List')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<style>
  .highlight-row {
    background-color: #ffeb3b !important;
    /* Yellow background color */
    color: #000;
    /* Black text color */
  }

  .group-header-row {
    background-color: #d3d3d3 !important;
    color: #31708f;
    font-weight: bold;
  }

  .group-header-icons {
    float: right;
  }

  .icon-black {
    color: #000;
  }

  .icon-red {
    color: red;
  }

  .additional-info-row {
    background-color: rgb(107, 27, 199);
    color: #fff;
  }

  .additional-info-cell {
    color: #fff;
    text-align: center;
  }

  .additional-info-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .new-header-row {
    background-color: rgb(240, 232, 232);
    color: #333;
  }

  .search-input-custom {
    text-align: left;
    width: 305px;
    height: 37px;
    font-size: 15px;
    margin-right: 15px;
  }

  .date-filter-input {
    width: 120px;
    margin-right: 17px;
  }

  .search-button-custom {
    width: 30px;
    height: 37px;
  }

  .add-prescription-btn {
    color: #fff;
  }

  #prescription-legend {
    width: 100%;
    height: auto;
    color: #333;
    padding: 1%;
    padding-bottom: 10px;
    font-size: 12px;
  }

  .legend-row {
    text-align: right;
  }

  .legend-icons-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
  }

  .legend-icons {
    text-align: right;
  }

  #searchBtn {
    width: 30px;
    height: 37px;
  }

  .issued-input {
    max-width: 60px;
    padding: 2px 4px;
  }

  .dt-row-grouping td,
  .dt-row-grouping th {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 4px 6px;
  }

  /* Remove table borders and header background */
  #editModalBody table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;
    border: none;
  }

  #editModalBody th,
  #editModalBody td {
    padding: 8px 12px;
    text-align: left;
    border: none;
  }

  /* Modal styles */
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 120px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    /* Fade background */
  }

  .modal-content {
    background-color: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 5px;
    position: relative;
    width: 80%;
    max-width: 600px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  }

  /* Align buttons to right */
  .modal-content>div:last-child {
    text-align: right;
  }
</style>
<div id="issueModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div id="modalBody"></div>
    <div style="margin-top: 20px;">
      <button id="confirmIssue" class="btn btn-primary">Issue</button>
      <button id="cancelIssue" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>
<div id="editIssueModal" class="modal">
  <div class="modal-content">
    <div id="editModalBody"></div>
    <div style="margin-top: 20px;">
      <button id="confirmEditIssue" class="btn btn-primary">Confirm</button>
      <button id="cancelEditIssue" class="btn btn-secondary">Cancel</button>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="dt-row-grouping table">
    </table>
  </div>
</div>
<hr class="my-12">
<script src="/lib/js/page-scripts/pending-requests.js"></script>
@endsection
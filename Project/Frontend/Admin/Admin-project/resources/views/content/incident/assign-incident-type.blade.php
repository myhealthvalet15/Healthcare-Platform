@extends('layouts/layoutMaster')
@section('title', 'Incident types')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
@vite([
    'resources/assets/js/ui-modals.js',
    'resources/assets/js/questions.js',
    'resources/assets/js/extended-ui-sweetalert2.js'
])
@endsection


@section('page-style')
<style>
   .card {
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    background-color: #fff;
}
.form-check-label {
    font-weight: 500;
    color: #343a40;
}
#incrementCode {
  height: 40px;
  width: 40px;
  font-size: 24px;
  line-height: 20px;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

</style>
@endsection
@section('content')
<div class="card container mt-4">
  <h5 class="mt-3">Assign Incident Types</h5>
  <div class="row mb-3" id="incidentTypesRow"></div>
 <div id="inputFields" style="display: none;">
    <div class="row align-items-center mb-2">
      <div class="col-md-3">
        <input type="text"  class="form-control injury-text" placeholder="Injury Type Text">
      </div>
      <div class="col-md-1 text-center">
        <input type="color" id="colorPicker" class="form-control form-control-color color-picker" style="width: 40px; height: 40px; padding: 0;">
      </div>
      <div class="col-md-4">
        <input type="text" id="colorCode" class="form-control color-code" placeholder="Color Code" readonly>
      </div>
       <div class="col-md-1 mt-2">
  <button id="incrementCode" class="btn btn-primary form-control" onclick="incrementIncidentTypes()">+</button>
</div>
    </div>
       <div id="addinputFields"></div>

    <div class="text-end mb-3">
      <button class="btn btn-primary" onclick="saveIncidentTypes()">Save Incident Types</button>
    </div>
  </div>
</div>

<script src="/lib/js/page-scripts/show-corporate-incidenttypes.js?v=65"></script>

@endsection


@extends('layouts/layoutMaster')

@section('title', 'Edit Drug Template - Forms')

{{-- Vendor Styles --}}
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
    'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss'
  ])
@endsection

{{-- Vendor Scripts --}}
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
    'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

{{-- Page Scripts --}}
@section('page-script')
  @vite([
    'resources/assets/js/form-wizard-numbered.js',
    'resources/assets/js/form-wizard-validation.js'
  ])

  {{-- Pass data to JS --}}
  <script>
    window.drugTemplateData = @json($drugtemplates);
  </script>

  {{-- Your external JS --}}
  <script src="/lib/js/page-scripts/drug-template-edit.js"></script>
@endsection

@section('content')
<div class="col-12 mb-6">
  <div id="wizard-validation" class="bs-stepper mt-2">
    <div class="bs-stepper-content">
      <form id="wizard-validation-form">
        <div id="account-details-validation" class="content" style="display:block;">
          <div class="row g-6">
            @php
              $fields = [
                ['drug_type_name', 'Drug Type Name'],
                ['drug_type_manufacturer', 'Drug Manufacturer'],
                ['drug_strength', 'Drug Strength'],
                ['restock_alert_count', 'Restock Alert Count'],
                ['schedule', 'Schedule'],
                ['id_no', 'ID Number'],
                ['hsn_code', 'HSN Code'],
                ['unit_issue', 'Unit to Issue'],
                ['amount_per_strip', 'Amount Per Strip'],
                ['tablet_in_strip', 'Tablet in Strip'],
                ['amount_per_tab', 'Amount per Tab'],
                ['discount', 'Discount'],
                ['sgst', 'SGST'],
                ['cgst', 'CGST'],
                ['igst', 'IGST']
              ];
            @endphp

            @foreach($fields as [$id, $label])
              <div class="col-sm-6">
                <label for="{{ $id }}" class="form-label">{{ $label }}</label>
                <input type="text" id="{{ $id }}" class="form-control" placeholder="Enter {{ $label }}">
              </div>
            @endforeach

            <div class="col-sm-6">
              <label class="form-label" for="drug_type">Drug Type</label>
              <select id="drug_type" class="form-control select2"></select>
            </div>
            <div class="col-sm-6">
              <label class="form-label" for="drug_ingredients">Drug Ingredients</label>
              <select id="drug_ingredients" class="form-control select2" multiple></select>
            </div>
            <div class="col-sm-6">
              <label for="bill_status" class="form-label">Bill Status</label>
              <label class="switch">
                <input type="checkbox" class="switch-input" id="bill_status">
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
                <span class="switch-label" id="bill-status-label">Inactive</span>
              </label>
            </div>
            <div class="col-sm-6">
              <div class="form-check">
                <input type="checkbox" id="otc" name="otc" class="form-check-input">
                <label class="form-check-label" for="otc">OTC</label>
                &nbsp;&nbsp;
                <input type="checkbox" id="crd" name="crd" class="form-check-input">
                <label class="form-check-label" for="crd">CRD</label>
              </div>
            </div>
            <div class="col-sm-6">
              <button type="button" class="btn btn-primary" id="edit-drugtype">Save Changes</button>
            </div>
          </div>
          <br><br>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

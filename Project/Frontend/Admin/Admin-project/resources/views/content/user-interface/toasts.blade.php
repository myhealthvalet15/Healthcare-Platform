@extends('layouts/layoutMaster')

@section('title', 'Toasts - UI elements')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@endsection

<!-- Vendor Script -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

<!-- Page Script -->
@section('page-script')
@vite(['resources/assets/js/ui-toasts.js'])
@endsection

@section('content')
<!-- Toastr Demo -->
<div class="card">
  <h5 class="card-header">Toastr</h5>
  <div class="card-body">
    <div class="row">
      
      <div class="col-lg-6 col-xl-3">
        <div class="mb-4" id="toastTypeGroup">
          <label class="form-label">Toast Type</label>
          <div class="form-check">
            <input type="radio" id="successRadio" name="toastsRadio" class="form-check-input" checked value="success" />
            <label class="form-check-label" for="successRadio">Success</label>
          </div>
          <div class="form-check">
            <input type="radio" id="infoRadio" name="toastsRadio" class="form-check-input" value="info" />
            <label class="form-check-label" for="infoRadio">Info</label>
          </div>
          <div class="form-check">
            <input type="radio" id="warningRadio" name="toastsRadio" class="form-check-input" value="warning" />
            <label class="form-check-label" for="warningRadio">Warning</label>
          </div>
          <div class="form-check">
            <input type="radio" id="errorRadio" name="toastsRadio" class="form-check-input" value="error" />
            <label class="form-check-label" for="errorRadio">Error</label>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-xl-3">
        <div class="mb-4">
          <label class="form-label" for="showEasing">Show Easing</label>
          <input id="showEasing" type="text" class="form-control" placeholder="swing, linear" value="swing" />
        </div>
        <div class="mb-4">
          <label class="form-label" for="hideEasing">Hide Easing</label>
          <input id="hideEasing" type="text" class="form-control" placeholder="swing, linear" value="linear" />
        </div>
        <div class="mb-4">
          <label class="form-label" for="showMethod">Show Method</label>
          <input id="showMethod" type="text" class="form-control" placeholder="show, fadeIn, slideDown" value="fadeIn" />
        </div>
        <div class="mb-4">
          <label class="form-label" for="hideMethod">Hide Method</label>
          <input id="hideMethod" type="text" class="form-control" placeholder="hide, fadeOut, slideUp" value="fadeOut" />
        </div>
      </div>
      <div class="col-lg-6 col-xl-3">
        <div class="mb-4 kt-form__grou">
          <label class="form-label" for="showDuration">Show Duration</label>
          <input id="showDuration" type="text" class="form-control" placeholder="ms" value="300" />
        </div>
        <div class="mb-4 kt-form__grou">
          <label class="form-label" for="hideDuration">Hide Duration</label>
          <input id="hideDuration" type="text" class="form-control" placeholder="ms" value="1000" />
        </div>
        <div class="mb-4 kt-form__grou">
          <label class="form-label" for="timeOut">Time out</label>
          <input id="timeOut" type="text" class="form-control" placeholder="ms" value="5000" />
        </div>
        <div class="mb-4 kt-form__grou">
          <label class="form-label" for="extendedTimeOut">Extended time out</label>
          <input id="extendedTimeOut" class="form-control" type="text" placeholder="ms" value="1000" />
        </div>
      </div>
    </div>
    <hr />
    <div class="d-flex gap-3 flex-wrap">
      <a href="javascript:;" class="btn btn-primary" id="showtoast">Show Toast</a>
      <a href="javascript:;" class="btn btn-danger" id="cleartoasts">Clear Toasts</a>
      <a href="javascript:;" class="btn btn-danger" id="clearlasttoast">Clear Last Toast</a>
    </div>
  </div>
</div>
<!--/ Toastr Demo -->

@endsection

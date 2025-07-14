@php
$customizerHidden = 'customizer-hide';
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Two Steps Verifications Basic - Pages')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/toastr/toastr.scss',
])
@endsection
@section('page-style')
@vite([
'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/toastr/toastr.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('page-script')
@vite([
'resources/assets/js/pages-auth.js',
'resources/assets/js/pages-auth-two-steps.js'
])
@endsection
@section('content')
<!-- @php
    dump(session()->all());
@endphp -->
<div class="authentication-wrapper authentication-basic px-6">
  <div class="authentication-inner py-6">
    <!--  Two Steps Verification -->
    <div class="card">
      <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-6">
          <a href="{{url('/')}}" class="app-brand-link">
            <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg'
              => "fill:
              #fff;"])</span>
            <span class="app-brand-text demo text-heading fw-bold">{{
              config('variables.templateName') }}</span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-1">Two Step Verification 💬</h4>
        <p class="text-start mb-6">
          {{ session('2fa_message',
          'Please enter the verification code sent to your email.') }}
        </p>
        <p class="mb-0">Type your 6 digit security code</p>
        <form id="twoStepsForm" action="{{url('/')}}" method="GET">
          <div class="mb-6">
            <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                maxlength="1" autofocus>
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                maxlength="1">
              <input type="tel" class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                maxlength="1">
            </div>
            <input type="hidden" name="otp" />
          </div>
          <div class="d-flex justify-content-between align-items-center mb-6">
            <button id="login-again-btn" class="btn btn-primary w-40" onclick="window.location.href='/auth/2falogin'">
              Login Again
            </button>
            <button id="verify-my-account-btn" class="btn btn-success w-60 me-2">
              Verify OTP
            </button>
          </div>
          <div class="text-center">
            Didn't get the code?
            <a href="#" id="resend-otp-link">Resend</a>
          </div>
        </form>
      </div>
    </div>
    <!-- / Two Steps Verification -->
  </div>
</div>
@endsection
<script src="/lib/js/jquery/jquery.min.js"></script>
<script src="/lib/js/page-scripts/common.js"></script>
<script src="/lib/js/page-scripts/verify-otp.js"></script>
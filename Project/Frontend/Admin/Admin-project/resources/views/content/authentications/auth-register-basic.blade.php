@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Register Basic - Pages')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
'resources/assets/js/pages-auth.js'
])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">

      <!-- Register Card -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{url('/')}}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros',['height'=>20,'withbg' => "fill: #fff;"])</span>
              <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
            </a>
          </div>
          <!-- /Logo -->
          <!-- /Logo -->
          <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
          <p class="mb-6">Please register to your account and start the adventure</p>
          <form id="formAuthentication" class="mb-6" action="{{route('auth-register')}}" method="post">
            @csrf
            <div class="mb-6">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autofocus>
            </div>
            <div class="mb-6">
              <label for="email" class="form-label">Email</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email">
            </div>
            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
            </div>
            <!-- <div class="my-8">
              <div class="form-check mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms">
                <label class="form-check-label" for="terms-conditions">
                  I agree to
                  <a href="javascript:void(0);">privacy policy & terms</a>
                </label>
              </div>
            </div> -->
            <button class="btn btn-primary d-grid w-100" onclick="encryptAndSubmit()">
              Sign up
            </button>
          </form>
          <p class="text-center">
            <span>Already have an account?</span>
            <a href="{{url('auth/login')}}">
              <span>Sign in instead</span>
            </a>
          </p>
          <!-- <div class="divider my-6">
            <div class="divider-text">or</div>
          </div> -->

          <!-- <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-facebook me-1_5">
              <i class="tf-icons ti ti-brand-facebook-filled"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-twitter me-1_5">
              <i class="tf-icons ti ti-brand-twitter-filled"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-github me-1_5">
              <i class="tf-icons ti ti-brand-github-filled"></i>
            </a>

            <a href="javascript:;" class="btn btn-sm btn-icon rounded-pill btn-text-google-plus">
              <i class="tf-icons ti ti-brand-google-filled"></i>
            </a>
          </div> -->
        </div>
      </div>
      <!-- Register Card -->
    </div>
  </div>
</div>
<script>
  function encryptAndSubmit() {


    const publicKey = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2AO7zvFQUgyeQx7Ey7e/
Hb5NV88V4nhMnPLi6Ei3gqJ0RViDWRpDTndjxP+pDT6r45cJCNCR8OJDE9RZbgu+
/P99wbP3viM1DurF27TBIisnubre/5b0xcP9TxHX6LOiIZV/DHwXEZJ4ciZOiJEV
Oa7+lh4UlmYGRTfVrw3Ahb9ifgh0hyvsnFXQpjXOuwZkPdcbS6bETdlq434U7xiO
X522WbrrWb6uV0NoEr9lcEV/3GRDO/LFcn+DFUH6byAqAgxYnW5LpElSSJvMTc92
sXTJwCLaI9y523ADO57fuwcHcoZKY3TDv38TwIg5e5VctX3WFL3GH2gvN4T+t/yD
fwIDAQAB
-----END PUBLIC KEY-----`;

    const encryptor = new JSEncrypt();
    encryptor.setPublicKey(publicKey);

    const username = document.getElementById('username').value;

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const encryptedUsername = encryptor.encrypt(username);
    const encryptedEmail = encryptor.encrypt(email);
    const encryptedPassword = encryptor.encrypt(password);
    alert(encryptedUsername);
    if (encryptedEmail && encryptedPassword) {
      document.getElementById('username').value = encryptedUsername;
      document.getElementById('email').value = encryptedEmail;
      document.getElementById('password').value = encryptedPassword;

      document.getElementById('formAuthentication').submit(); // Submit the form after encryption
    } else {
      alert("Encryption failed. Please try again.");
    }
  }
</script>
@endsection
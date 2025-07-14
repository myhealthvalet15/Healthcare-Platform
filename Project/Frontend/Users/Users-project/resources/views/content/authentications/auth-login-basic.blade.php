@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login Basic - Pages')

@section('vendor-style')
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
    <style>
        .captcha-spacing img {
            margin-bottom: 8px;
        }

        .captcha-spacing input {
            margin-top: 8px;
        }
    </style>
    <script src="/lib/js/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
        integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])</span>
                                <span
                                    class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
                        <p class="mb-6">Please sign-in to your account and start the adventure</p>

                        <form id="formAuthentication" class="mb-4" action="{{ route('auth-login') }}" method="post">
                            @csrf
                            <div class="mb-6">
                                <label for="email-username" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="email-username" name="email_username"
                                    placeholder="Enter your email or username" autofocus>
                            </div>
                            <div class="mb-6 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            @php
                                $captchaType = env('CAPTCHA_DRIVER', 'none');
                            @endphp
                            <input type="hidden" name="captcha_type" value="{{ $captchaType }}" id="captchaType">
                            @if ($captchaType === 'anCaptcha')
                                <div class="mb-6 captcha">
                                    <label for="captcha" class="form-label">Captcha</label>
                                    <div>
                                        <span>
                                            {!! captcha_img('math') !!}
                                        </span>
                                        <button type="button" class="btn btn-link" id="reload">Refresh</button>
                                    </div>
                                    <input type="text" name="an-recaptcha" class="form-control"
                                        placeholder="Enter captcha" required style="margin-top: 8px;">
                                </div>
                            @elseif($captchaType === 'google_v3')
                                <script src="https://www.google.com/recaptcha/api.js?render={{ env('G_CAPTCHA_SITE_KEY', 'none') }}"></script>
                                <input type="hidden" name="g-recaptcha" id="g-recaptcha">
                                <script src="https://www.google.com/recaptcha/api.js"></script>
                            @endif
                            <div class="mb-6">
                                <button class="btn btn-primary w-100" type="submit" id="submitButton" disabled>
                                    <span class="spinner-grow me-1" role="status" aria-hidden="true"
                                        id="letting-in-spinner"></span>
                                    Preparing your space ...
                                </button>
                            </div>
                        </form>
                        <p class="text-center">
                            <span>New on our platform?</span>
                            <a href="{{ url('auth/register') }}">
                                <span>Create an account</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0-beta.1/jsencrypt.min.js"></script>
    <script src="/lib/js/page-scripts/common.js"></script>
    <script src="/lib/js/page-scripts/index.js"></script>
    <script>
        $(document).ready(function() {
            window.CAPTCHA_SITE_KEY = "{{ env('G_CAPTCHA_SITE_KEY', 'none') }}";
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                    showToast('error', "{{ $error }}");
                @endforeach
            @else
                showToast('info', "Login Now");
            @endif
        });
    </script>
@endsection

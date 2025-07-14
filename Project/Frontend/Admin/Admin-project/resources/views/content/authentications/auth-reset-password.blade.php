@php
$customizerHidden = 'customizer-hide';
@endphp
@extends('layouts/layoutMaster')
@section('title', 'Reset Password')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/toastr/toastr.scss',
])
@endsection
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/toastr/toastr.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
            <!-- Reset Password -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-6">
                        <a href="{{ url('/') }}" class="app-brand-link">
                            <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20, 'withbg' =>
                                'fill: #fff;'])</span>
                            <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName')
                                }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-1">Reset Your Password</h4>
                    <p class="mb-6">Please enter your new password and confirm it to reset your password.</p>
                    <form id="formAuthentication" class="mb-4" action="{{route('reset-password')}}" method="post">
                        @csrf
                        <div class="mb-6 form-password-toggle">
                            <label for="new-password" class="form-label">New Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" id="new-password" name="new_password"
                                    placeholder="Enter your new password" required aria-describedby="new-password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="confirm-password">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="confirm-password" class="form-control" name="confirm_password"
                                    placeholder="Confirm your new password" required aria-describedby="confirm-password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                        <!-- Hidden token input field -->
                        <input type="hidden" name="resetToken" id="resetToken">
                        @php
                        $captchaType = env('CAPTCHA_DRIVER', 'none');
                        @endphp
                        <input type="hidden" name="captcha_type" value="{{ $captchaType }}" id="captchaType">
                        @if($captchaType === 'anCaptcha')
                        <div class="mb-6 captcha">
                            <label for="captcha" class="form-label">Captcha</label>
                            <div>
                                <span>
                                    {!! captcha_img('math') !!}
                                </span>
                                <button type="button" class="btn btn-link" id="reload">Refresh</button>
                            </div>
                            <input type="text" name="an-recaptcha" class="form-control" placeholder="Enter captcha" required style="margin-top: 8px;">
                        </div>
                        @elseif($captchaType === 'google_v3')
                        <script src="https://www.google.com/recaptcha/api.js?render={{ env('G_CAPTCHA_SITE_KEY', 'none') }}"></script>
                        <input type="hidden" name="g-recaptcha" id="g-recaptcha">
                        <script src="https://www.google.com/recaptcha/api.js"></script>
                        @endif
                        <div class="mb-6">
                            <button class="btn btn-primary d-grid w-100" type="submit" id="submitButton">Reset Password</button>
                        </div>
                    </form>
                    <p class="text-center">
                        <span>Remember your password?</span>
                        <a href="{{ url('auth/login') }}">
                            <span>Login</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.0.0-beta.1/jsencrypt.min.js"></script>
<script src="/lib/js/jquery/jquery.min.js"></script>
<script src="/lib/js/page-scripts/common.js"></script>
<script src="/lib/js/page-scripts/reset-password-index.js"></script>
<script>
    $(document).ready(function() {
        const url = window.location.href;
        const token = url.split('/').pop();
        $('#resetToken').val(token);
        window.CAPTCHA_SITE_KEY = "{{ env('G_CAPTCHA_SITE_KEY', 'none') }}";
        toastr.options = {
            "progressBar": true,
            "closeButton": true,
            "positionClass": "toast-top-right"
        };

        @if($errors -> any())
        @foreach($errors -> all() as $error)
        toastr.error("{{ $error }}");
        @endforeach
        @else
        toastr.info("Reset your password now");
        @endif
    });
</script>

@endsection
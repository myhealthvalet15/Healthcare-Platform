@extends('layouts/layoutMaster')
@section('title', 'My Dashboard')
@section('description', 'Description of my dashboard')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection
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
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js',
'resources/assets/js/form-wizard-validation.js'
])
@endsection
@section('content')
@php
$tabNames = ['Injury Type', 'Site of Injury','Nature of Injury', 'Injury Mechanism', 'Body Part', 'Symptoms', 'Medical
System', 'Diagnosis', 'Mechanism','Others'];
@endphp
<link rel="stylesheet" href="/lib/css/page-styles/injury-index.css">
<div class="container mt-4">
    <div class="row header-section">
        <div class="col-3">
            <ul class="nav flex-column nav-pills" id="tabMenu">
                @foreach (range(1, count($tabNames)) as $i)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $i === 1 ? 'active' : '' }}" id="tab-{{ $i }}-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-content-{{ $i }}" type="button" role="tab" data-injury-key="{{ $i }}">
                        {{ $tabNames[$i - 1] }}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="col-9">
            <div class="tab-content mt-4" id="myTabContent">
                @foreach (range(1, count($tabNames)) as $i)
                <div id="tab-content-{{ $i }}" class="tab-pane fade {{ $i === 1 ? 'show active' : '' }}">
                    <div class="loader-container">
                        <div class="custom-loader">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <p>Loading, please wait...</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/injury-index.js"></script>
@endsection
@extends('layouts/layoutMaster')
@section('title', 'Incident types')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('page-style')
<style>
    .card {
        border-radius: 10px;
        border: 1px solid #ddd;
        transition: box-shadow 0.3s;
    }
    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .form-check-input {
        margin-top: 0;
        margin-right: 0.5rem;
    }
    #incidentCardContainer .card h5 {
        font-size: 1.1rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row" id="incidentCardContainer"></div>
    <div id="preloader" style="display: none;">Loading...</div>
</div>
<script src="/lib/js/page-scripts/show-corporate-incidenttypes.js"></script>

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

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

<!-- Vendor Scripts -->
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

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js',
'resources/assets/js/form-wizard-validation.js'
])
@endsection
@section('content')
<script src="{{ asset('css/injury.css') }}"></script>
@php
    $tabNames = ['Injury Type', 'Site of Injury','Nature of Injury', 'Injury Mechanism', 'Body Part', 'Symptoms', 'Medical System', 'Diagnosis', 'Mechanism','Others'];
@endphp
<style>
        .loader-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
           
            padding: 20px;
            text-align: center;
            /* background: #f8f9fa; */
            /* Subtle background color */
        }

        /* Custom loader: Dots */
        .custom-loader {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            /* Space between dots */
            margin-bottom: 10px;
            /* Space between dots and message */
        }

        .custom-loader span {
            width: 12px;
            height: 12px;
            /* background-color: #007bff; */
            /* Bootstrap primary color */
            border-radius: 50%;
            /* Make it a perfect circle */
            animation: bounce 1.5s infinite ease-in-out;
        }

        /* Animation delays for the bouncing effect */
        .custom-loader span:nth-child(1) {
            animation-delay: 0s;
        }

        .custom-loader span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .custom-loader span:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Keyframes for bouncing animation */
        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
                /* Shrink dot */
            }

            40% {
                transform: scale(1);
                /* Full size */
            }
        }

        /* Loader message styles */
        .loader-container p {
            font-size: 16px;
            color: #555;
            /* Subtle gray color */
            font-weight: 500;
            animation: fadeIn 2s ease-in-out infinite;
        }

        /* Fade-in effect for the text */
        @keyframes fadeIn {

            0%,
            100% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 0;
            list-style: none;
        }

       
        body {
            font-family: 'Arial', sans-serif;
        }

        .btn-gradient {
            /* background: linear-gradient(135deg, #007bff, #00c6ff); */
            border-radius: 8px;
            /* transition: background-color 0.3s ease; */
        }

        .btn-gradient:hover {
            /* background: linear-gradient(135deg, #0056b3, #0099cc); */
        }

        .btn-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Table row hover effect */
        .table tbody tr:hover {
            /* background-color: #f5f5f5; */
        }

        /* Table design improvements */
        .table th {
            /* background-color: #f9f9f9; */
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table td {
            color: #555;
        }

        .table input.form-control {
            /* background-color: #f9f9f9; */
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            padding: 10px;
        }

        /* Form input focus effect */
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .header-section {
            /* background-color: #f8f9fa; */
            padding: 20px;
            border-radius: 8px;
        }

        .nav-pills .nav-link {
            border-radius: 5px;
            padding: 15px 20px;
            margin-bottom: 5px;
            font-size: 16px;
            color: #495057;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            /* background-color: #007bff; */
            color: #fff;
        }

        .nav-pills .nav-link:hover {
            background-color: #e9ecef;
            color: #007bff;
        }

        .nav-pills {
            border-right: 2px solid #ddd;
            padding-right: 20px;
        }

        .tab-content {
            /* background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(255, 255, 255, 0.3)); */
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: opacity 0.5s ease-in-out;
        }

        .tab-content .tab-pane {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .tab-content .tab-pane.active {
            opacity: 1;
        }

        .tab-pane h4 {
            font-size: 24px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 20px;
        }

        .tab-pane p {
            font-size: 16px;
            color: #6c757d;
            line-height: 1.6;
        }

        .tab-content .btn-primary {
            /* background-color: #007bff; */
            border-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-transform: uppercase;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab-content .btn-primary:hover {
            /* background-color: #0056b3; */
            border-color: #0056b3;
        }

        /* Add some spacing to the content */
        .tab-content .card {
            margin-top: 20px;
        }

        .icon {
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .nav-item:hover .icon {
            transform: scale(1.2);
        }
    </style>
<div class="container mt-4">
    <div class="row header-section">
        <!-- Tab Navigation -->
        <div class="col-3">
            <ul class="nav flex-column nav-pills" id="tabMenu">
                @foreach (range(1, count($tabNames)) as $i)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $i === 1 ? 'active' : '' }}" 
                            id="tab-{{ $i }}-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#tab-content-{{ $i }}" 
                            type="button" 
                            role="tab" 
                            data-injury-key="{{ $i }}">
                            {{ $tabNames[$i - 1] }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-9">
            <div class="tab-content mt-4" id="myTabContent">
                @foreach (range(1, count($tabNames)) as $i)
                    <div id="tab-content-{{ $i }}"
                        class="tab-pane fade {{ $i === 1 ? 'show active' : '' }}">
                     
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<!-- Popper.js (Bootstrap Dependency) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        loadTabContent(1);

       
        $('button[data-bs-toggle="tab"]').on('click', function () {
            var injuryKey = $(this).data('injury-key'); 
            loadTabContent(injuryKey); 
        });

        function loadTabContent(injuryKey, page = 1) {
            var tabContent = $('#tab-content-' + injuryKey);

            tabContent.html(`
                <div class="loader-container">
                    <div class="custom-loader">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <p>Loading, please wait...</p>
                </div>
            `);

            $.ajax({
                url: '/outpatient/injury', 
                type: 'GET',
                data: {
                    injury_key: injuryKey,
                    page: page
                },
                success: function (response) {
                    tabContent.html(response.data);

                    tabContent.find('.pagination a').on('click', function (e) {
                        e.preventDefault();
                        var url = $(this).attr('href');
                        var newPage = new URLSearchParams(url.split('?')[1]).get('page');
                        loadTabContent(injuryKey, newPage);
                    });
                },
                error: function (xhr) {
                    console.error("Error loading content:", xhr.responseText);
                    tabContent.html('<p>Error loading content. Please try again.</p>');
                }
            });
        }
    });
</script>



@endsection
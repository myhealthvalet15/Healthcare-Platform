@extends('layouts/layoutMaster')

@section('title', 'Wizard Numbered - Forms')




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
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="container mt-5">
    
    <h1 class="mb-4 text-center text-dark font-weight-bold">Corporate List</h1>
    <div class="row my-4">
        <div class="col-12">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert" style="background-color:black">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
    </div>
    <div class="row my-4">
        <div class="col-12">
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>

        @if(is_array($corporate) && count($corporate) > 0)
        <div class="table-responsive shadow-lg rounded">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Corporate Name</th>

                        <th>Industry</th>
                        <!-- <th>Created By</th>
                    <th>Created On</th> -->
                        
                        <th>Status</th>
                        <th>Display Name</th>
                        <th>Add location</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($corporate as $index => $item)
                    <tr class="align-middle">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['corporate_name'] }}</td>

                        <td>{{ $item['industry'] }}</td>


                        <!-- <td>{{ $item['created_by'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['created_on'])->format('d M Y') }}</td> -->
                        <td>
                            <span class="badge {{ $item['active_status'] == 1 ? 'bg-success' : 'bg-danger' }}">
                                {{ $item['active_status'] == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $item['display_name'] }}</td>
                       @if($item['corporate_id'] == $item['location_id'])
                        <td>
                            <a href="{{ route('corporate_location', ['corporate_id' => $item['id'], 'corporate_name' => $item['corporate_id']]) }}"
                                class="btn btn-dark btn-sm" data-bs-toggle="tooltip" title="Add Corporate locations">
                                <i class="fa fa-plus" style="color: #FF5733;"></i>                                </a>
                         


                        </td>
                       @else
                       <td> </td>

                       @endif
                        <td class="text-center">
                            <a href="{{ route('corporate.edit', $item['id']) }}" class="btn btn-dark btn-sm"
                                data-bs-toggle="tooltip" title="Edit Corporate Details">
                                <i class="fas fa-building"></i>
                            </a>

                            <a href="{{ route('corporate.editAddress', ['id' => $item['id'], 'corporate_id' => $item['corporate_id']]) }}"
                            class="btn btn-info btn-sm" data-bs-toggle="tooltip"
                                title="Edit corporaate address Details">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                            @if($item['corporate_id'] == $item['location_id'])
                            <a href="{{ route('corporate.editEmployeeTypes',['id' => $item['id'], 'corporate_id' => $item['corporate_id']]) }}"
                                class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit Employee Types">
                                <i class="fas fa-users"></i>
                            </a>

                            <a href="{{ route('corporate.editComponents', ['id' => $item['id'], 'corporate_id' => $item['corporate_id']]) }}"
                                class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="edit components Details">
                                <i class="fas fa-home"></i>
                            </a>
                            @endif    
                            <a href="{{ route('corporate.editAdminUsers',['id' => $item['id'], 'corporate_id' => $item['corporate_id']]) }}"
                                class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                title="edit Corporate super  Admin">
                                <i class="fas fa-user-tie"></i>
                            </a>
                            <a href="{{ route('corporate.assignForms',['corporate_id' => $item['corporate_id'],'location_id' => $item['location_id']]) }}"
                                class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                title="Assign Form for Corporate Location">
                                <i class="fa-regular fa-file-waveform"></i>
                            </a>
                              @if($item['corporate_id'] == $item['location_id'])
                            <a href="{{ route('corporate.assignIncidentTypes',['corporate_id' => $item['corporate_id']]) }}"
                                class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit incident Types">
                                <i class="fas fa-envelope"></i>
                            </a>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-circle"></i> No corporate data available.
        </div>
        @endif
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
 toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Display success or error messages if they exist
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
       
</script>
    <style>
    .thead-light th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: bold;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-striped tbody tr:hover {
        background-color: #e9ecef;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 12px 15px;
    }

    .btn-outline-dark,
    .btn-outline-info,
    .btn-outline-primary,
    .btn-outline-secondary,
    .btn-outline-warning,
    .btn-outline-success {
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .btn-outline-dark:hover,
    .btn-outline-info:hover,
    .btn-outline-primary:hover,
    .btn-outline-secondary:hover,
    .btn-outline-warning:hover,
    .btn-outline-success:hover {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
        transform: scale(1.05);
    }

    /* Badge adjustments */
    .badge {
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 20px;
        text-transform: capitalize;
    }

    /* Clean up the title */
    h1 {
        font-size: 2.5rem;
        color: #343a40;
    }

    /* Clean the alert message */
    .alert {
        font-size: 16px;
        font-weight: 500;
        padding: 12px;
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
        .table-responsive {
            margin-top: 20px;
        }
    }
    </style>

    @endsection
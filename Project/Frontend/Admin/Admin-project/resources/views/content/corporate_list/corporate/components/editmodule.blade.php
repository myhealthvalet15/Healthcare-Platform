@extends('layouts.layoutMaster')

@section('title', 'Edit Module')
@section('description', 'Update module details.')

@section('content')
<div class="container py-5">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-lg border-0 p-4 rounded-3" style="background-color: #f9f9f9;">
            <h4 class="text-primary mb-4 text-center fw-bold">Edit Module</h4>

            <!-- Success and Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('update-module', $module['module_id']) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Module Name Input -->
                <div class="mb-4">
                    <label for="module_name" class="form-label fw-semibold">Module Name</label>
                    <input 
                        type="text" 
                        class="form-control form-control-lg shadow-sm" 
                        id="new_module_name" 
                        name="new_module_name" 
                        value="{{ $module['module_name'] ?? '' }}" 
                        placeholder="Enter module name" 
                        >
                </div>
                <input 
                        type="hidden" 
                        class="form-control form-control-lg shadow-sm" 
                        id="module_name" 
                        name="module_name" 
                        value="{{ $module['module_name'] ?? '' }}" 
                        placeholder="Enter module name" 
                        >
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Update Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styling for extra effects -->
@section('styles')
    <style>
        .card {
            background-color: #f9f9f9; /* Soft background for the card */
            border-radius: 10px; /* Rounded corners for the card */
        }

        .form-control {
            border-radius: 10px; /* Rounded form controls */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow on inputs */
        }

        .form-label {
            font-weight: 600; /* Bold labels for better readability */
            color: #495057; /* Darker color for text */
        }

        .btn-primary {
            background-color: #007bff; /* Vibrant primary button color */
            border-color: #007bff; /* Matching border color */
            border-radius: 10px; /* Rounded button edges */
        }

        .alert {
            font-weight: 500; /* Slightly bold text for alerts */
        }

        .alert-dismissible .btn-close {
            position: relative;
            top: -2px;
        }

        .container {
            margin-top: 50px;
        }

        .fw-bold {
            font-weight: 700;
        }
    </style>
@endsection

@endsection

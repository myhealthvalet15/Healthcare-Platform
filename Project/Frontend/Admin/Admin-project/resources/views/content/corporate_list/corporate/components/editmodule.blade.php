@extends('layouts.layoutMaster')
@section('title', 'Edit Module')
@section('description', 'Update module details.')
@section('content')
<style>
    .card {
        background-color: #f9f9f9; 
        border-radius: 10px; 
    }
    .form-control {
        border-radius: 10px; 
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
    }
    .form-label {
        font-weight: 600; 
        color: #495057; 
    }
    .btn-primary {
        background-color: #007bff; 
        border-color: #007bff; 
        border-radius: 10px; 
    }
    .alert {
        font-weight: 500; 
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
<div class="container py-5">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-lg border-0 p-4 rounded-3" style="background-color: #f9f9f9;">
            <h4 class="text-primary mb-4 text-center fw-bold">Edit Module</h4>
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
            <form action="{{ route('update-module', $module['module_id']) }}" method="POST">
                @csrf
                @method('PUT')
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
                <div class="d-flex gap-3 justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Update Module</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts/layoutMaster')
@section('title', 'My Dashboard')
@section('description', 'Description of my dashboard')
@section('content')
<style>
    body {
        background-color: #f5f5f5; /* Subtle light gray background */
        font-family: 'Roboto', sans-serif; /* Professional font choice */
    }
    .card-header {
        font-size: 1.8rem;
        font-weight: 700;
    }
    .btn-secondary {
        font-size: 1rem;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    .btn-secondary i {
        margin-right: 0.5rem;
    }
    .list-group-item {
        transition: box-shadow 0.2s ease-in-out;
    }
    .list-group-item:hover {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
</style>
<div class="container mt-5">
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left-circle"></i> Back
        </a>
    </div>
    <div class="card shadow-sm border-0 rounded-lg" style="background-color: #fff;">
        <div class="card-header text-center py-4" style="background-color: #fff; border-bottom: 1px solid #ddd;">
            <h2 class="m-0 text-primary" style="font-weight: bold; font-size: 1.8rem;">Certification Details</h2>
        </div>
        <div class="card-body px-4 py-5">
            <div class="row mb-4">
                <div class="col-md-4 text-secondary font-weight-bold">Certification Title:</div>
                <div class="col-md-8 text-dark">{{ $certification['certification_title'] }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-secondary font-weight-bold">Short Tag:</div>
                <div class="col-md-8 text-dark">{{ $certification['short_tag'] }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-secondary font-weight-bold">Content:</div>
                <div class="col-md-8 text-dark">{{ $certification['content'] }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-secondary font-weight-bold">Conditions:</div>
                <div class="col-md-8">
                    <ul class="list-group list-group-flush">
                        @foreach ($certification['condition'] as $index => $condition)
                            <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2 border-0" style="background-color: #f8f9fa; border-radius: 5px; margin-bottom: 10px;">
                                <span class="text-dark">{{ $condition }}</span>
                                <div class="color-box" style="width: 30px; height: 30px; background-color: {{ $certification['color_condition'][$index] }}; border-radius: 50%; border: 1px solid #ccc;"></div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 text-secondary font-weight-bold">Active Status:</div>
                <div class="col-md-8">
                    <span class="badge px-3 py-2 {{ $certification['active_status'] == 1 ? 'bg-success' : 'bg-danger' }}" style="font-size: 1rem;">
                        {{ $certification['active_status'] == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

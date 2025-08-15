@extends('layouts.layoutMaster')
@section('title', 'Corporate Module Management')
@section('description', 'Efficiently manage corporate modules.')
@section('content')
<div class="container py-5">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-sm border-0 p-4" style="background-color: #f9f9f9;">
            <h4 class="text-primary mb-4 text-center">Add New Module</h4>
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('components_module.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="module_name" class="form-label fw-semibold">Module Name</label>
                    <input
                        type="text"
                        class="form-control form-control-lg shadow-sm"
                        id="module_name"
                        name="module_name"
                        placeholder="Enter module name"
                        required>
                </div>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Create Module</button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg w-100 shadow-sm">Reset</button>
                </div>
            </form>
        </div>
    </div>
    <hr class="my-5">
    <div class="card shadow-sm border-0 bg-white">
        <div class="card-body">
            <h4 class="card-title text-primary mb-4">Existing Modules</h4>
            @if(empty($modules))
            <div class="text-center text-muted">
                <p class="mb-0">No modules available. Create a new one!</p>
            </div>
            @else
            <ul class="list-group list-group-flush">
                @foreach($modules as $module)
                <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span class="fw-medium text-dark">{{ $module['module_name'] }}</span>
                    <a href="/components/edit-module/{{ $module['module_id'] }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-edit" title="Edit"></i></a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection
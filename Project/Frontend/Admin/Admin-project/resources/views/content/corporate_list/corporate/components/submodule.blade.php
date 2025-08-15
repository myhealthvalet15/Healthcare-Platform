@extends('layouts/layoutMaster')
@section('title', 'Components ')
@section('description', 'Manage Components ')
@section('content')
<div class="container py-5">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-sm border-0 p-4" style="background-color: #f9f9f9;">
            <h4 class="text-primary mb-4 text-center">Add New Sub-Module</h4>
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('components_submodule_store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="sub_module_name" class="form-label fw-semibold">Sub-Module Name</label>
                    <input
                        type="text"
                        class="form-control form-control-lg shadow-sm"
                        id="sub_module_name"
                        name="sub_module_name"
                        placeholder="Enter sub-module name"
                        required>
                    @error('sub_module_name')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="module_id" class="form-label">Select Module</label>
                    <select id="module_id" name="module_id" class="select2 form-control border rounded" data-live-search="true">
                        <option value="">Select a module name</option>
                        @foreach ($modules as $module)
                        <option value="{{ $module['module_id'] }}">{{ $module['module_name'] }}</option>
                        @endforeach
                    </select>
                    @error('module_id')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="submit" class="btn btn-light btn-lg w-100 shadow-sm">Create Sub-Module</button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg w-100 shadow-sm">Reset</button>
                </div>
            </form>
        </div>
    </div>
    <hr class="my-5">
    <div class="card shadow-sm border-0 bg-white">
        <div class="card-body">
            <h4 class="card-title text-primary mb-4">Existing Sub Modules</h4>
            @if(empty($submodules))
            <div class="text-center text-muted">
                <p class="mb-0">No modules available. Create a new one!</p>
            </div>
            @else
            <ul class="list-group list-group-flush">
                @foreach($submodules as $module)
                <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span class="fw-medium text-dark">{{ $module['sub_module_name'] }}</span>
                    <div class="d-flex gap-2">
                        <a href="/components/edit-sub-module/{{ $module['sub_module_id'] }}" class="btn btn-outline-primary btn-sm">Edit</a>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection
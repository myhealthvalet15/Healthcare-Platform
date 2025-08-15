@extends('layouts.layoutMaster')
@section('title', 'Edit Module')
@section('description', 'Update module details.')
@section('content')
<div class="col-md-8 col-lg-6 mx-auto">
    <div class="card shadow-sm border-0 p-4" style="background-color: #f9f9f9;">
        <h4 class="text-primary mb-4 text-center">Update Sub-Module</h4>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <form action="{{ route('submodule.update',$submodule['sub_module_id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="sub_module_name" class="form-label fw-semibold">Sub-Module Name</label>
                <input
                    type="text"
                    class="form-control form-control-lg shadow-sm"
                    id="new_sub_module_name"
                    name="new_sub_module_name"
                    placeholder="Enter sub-module name"
                    value="{{ old('sub_module_name', $submodule['sub_module_name'] ?? '') }}"
                    required>
                <input
                    type="hidden"
                    id="sub_module_name"
                    name="sub_module_name"
                    value="{{ $submodule['sub_module_name']}}">
                @error('sub_module_name')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="module_id" class="form-label">Select Module</label>
                <select id="module_id" name="module_id" class="select2 form-control border rounded" data-live-search="true">
                    <option value="">Select a module name</option>
                    @foreach ($modules as $module)
                    <option value="{{ $module['module_id'] }}" {{ isset($submodule['module_id']) && $submodule['module_id'] == $module['module_id'] ? 'selected' : '' }}>
                        {{ $module['module_name'] }}
                    </option>
                    @endforeach
                </select>
                @error('module_id')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex gap-3 justify-content-center">
                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Save Changes</button>
                <button type="reset" class="btn btn-outline-secondary btn-lg w-100 shadow-sm">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
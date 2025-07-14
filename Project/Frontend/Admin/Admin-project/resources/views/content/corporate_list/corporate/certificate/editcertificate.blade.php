@extends('layouts/layoutMaster')
@section('title', 'Edit Certification')
@section('description', 'Edit the certification details.')
@section('content')

<div class="container">
    <div class="row my-4">
        @if (session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="col-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <form method="post" action="{{ route('certification.update', $certificate['certificate_id']) }}" class="card shadow-sm p-4 border-0 rounded-4">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Certification Title -->
            <div class="col-md-6">
                <label for="title" class="form-label fw-bold">Certification Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="certification_title" 
                    class="form-control border-primary" 
                    placeholder="Enter certification title"
                    value="{{ old('certification_title', $certificate['certification_title']) }}">
            </div>

            <!-- Short Tag -->
            <div class="col-md-6">
                <label for="short_tag" class="form-label fw-bold">Short Tag</label>
                <input 
                    type="text" 
                    id="short_tag" 
                    name="short_tag" 
                    class="form-control border-primary" 
                    placeholder="Short tag"
                    value="{{ old('short_tag', $certificate['short_tag']) }}">
            </div>

            <!-- Content -->
            <div class="col-12">
                <label for="content" class="form-label fw-bold">Content</label>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-control border-primary" 
                    rows="4"
                    placeholder="Enter content">{{ old('content', $certificate['content']) }}</textarea>
            </div>

            <!-- Conditions and Colors -->
            <div id="conditionsContainer" class="row">
                @foreach ($certificate['condition'] as $index => $condition)
                    <div class="col-md-6 mt-3 condition-block">
                        <div class="input-group mb-3 p-3 border rounded-3">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <label class="fw-bold me-3" for="colorCondition{{ $index }}" style="min-width: 100px;">Select Color</label>
                                    <input 
                                        type="color" 
                                        id="colorCondition{{ $index }}" 
                                        name="color_condition[]" 
                                        class="form-control form-control-color" 
                                        value="{{ old('color_condition.' . $index, $certificate['color_condition'][$index]) }}" 
                                        style="width: 200px; height: 60px; padding: 0;">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger ms-2 remove-condition" style="height: 35px; width: 35px; padding: 0;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <input 
                                type="text" 
                                name="condition[]" 
                                class="form-control border-primary mt-2" 
                                placeholder="Enter condition {{ $index + 1 }}"
                                value="{{ old('condition.' . $index, $condition) }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-12 text-end mt-3">
                <button type="button" id="addConditionBtn" class="btn btn-secondary px-4 py-2">
                    <i class="fas fa-plus-circle me-2"></i> Add Another Condition
                </button>
            </div>

            <!-- Active Status -->
            <div class="col-md-6">
                <label for="active_status" class="form-label fw-bold">Active Status</label>
                <select 
                    id="active_status" 
                    name="active_status" 
                    class="form-control border-primary">
                    <option value="1" {{ $certificate['active_status'] == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $certificate['active_status'] == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-save me-2"></i> Update Certification
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let conditionCount = {{ count($certificate['condition']) }};
    document.getElementById('addConditionBtn').addEventListener('click', function() {
        conditionCount++;
        const conditionsContainer = document.getElementById('conditionsContainer');
        
        // Create a new condition input block with a remove button
        const newCondition = document.createElement('div');
        newCondition.classList.add('col-md-6', 'mt-3', 'condition-block');
        newCondition.innerHTML = `
            <div class="input-group mb-3 p-3 border rounded-3">
                <div class="d-flex justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <label class="fw-bold me-3" for="colorCondition${conditionCount}" style="min-width: 100px;">Select Color</label>
                        <input 
                            type="color" 
                            id="colorCondition${conditionCount}" 
                            name="color_condition[]" 
                            class="form-control form-control-color" 
                            value="#66cc00" 
                            style="width: 200px; height: 60px; padding: 0;">
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-2 remove-condition" style="height: 35px; width: 35px; padding: 0;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <textarea name="condition[]" class="form-control mt-2" placeholder="Enter condition" rows="2" required></textarea>
            </div>
        `;
        conditionsContainer.appendChild(newCondition);

        // Add event listener to the new remove button
        newCondition.querySelector('.remove-condition').addEventListener('click', function() {
            newCondition.remove();
        });
    });

    // Add remove functionality for existing blocks
    document.querySelectorAll('.remove-condition').forEach(button => {
        button.addEventListener('click', function() {
            button.closest('.condition-block').remove();
        });
    });
</script>

@endsection

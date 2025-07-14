@extends('layouts/layoutMaster')
@section('title', 'Certificate Management')
@section('description', 'Efficiently manage corporate modules.')
@section('content')

<div class="container py-5" style="max-width: 900px;">
    <div class="row">
        <!-- Success and Error Messages -->
        @if (session('success'))
        <div class="col-12 mb-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
        @if (session('error'))
        <div class="col-12 mb-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        <!-- Header Section -->
        <div class="col-12 mb-4">
            <h3 class="text-dark fw-bold">Certification Management</h3>
            <p class="text-muted">Efficiently manage certifications with a modern, intuitive interface.</p>
        </div>

        <!-- Add New Certification Button -->
        <div class="col-12 mb-4 text-end">
            <button id="showFormBtn" class="btn btn-primary px-4 py-2">
                <i class="fas fa-plus-circle me-2"></i> Add New Certification
            </button>
        </div>

        <!-- Form Section -->
        <form method="post" action="{{route('certification.store')}}" id="certificationForm"
            class="card p-4 border-0 rounded-3 shadow-lg" style="display: none;">
            @csrf
            <input type="hidden" name="hiddenid" value="">

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="title" class="form-label">Certification Title</label>
                    <input type="text" id="title" name="certification_title" class="form-control"
                        placeholder="Enter certification title" required>
                </div>

                <div class="col-md-6">
                    <label for="tit_tag" class="form-label">Title Short Tag</label>
                    <input type="text" id="tit_tag" name="short_tag" class="form-control" placeholder="Short tag"
                        required>
                </div>

                <div class="col-12">
                    <label for="content" class="form-label">Content</label>
                    <textarea id="content" name="content" class="form-control" rows="4" placeholder="Enter content"
                        required></textarea>
                </div>
            </div>

            <!-- Conditions Section -->
            <div class="row g-3 mt-4">
                <div class="col-12">
                    <label class="form-label">Conditions</label>
                </div>

                <!-- Default Condition Input (Visible by default) -->
                <div id="conditionsContainer" class="row">
                    <div class="col-md-6 mt-3 condition-block">
                        <div class="input-group mb-3 p-3 border rounded-3">
                            <div class="d-flex justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <label class="fw-bold me-3" for="colorCondition1" style="min-width: 100px;">Select
                                        Color 1</label>
                                    <input type="color" id="colorCondition1" name="color_condition[]"
                                        class="form-control form-control-color" value="#66cc00"
                                        style="width: 200px; height: 60px; padding: 0;">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger ms-2 remove-condition"
                                    style="height: 35px; width: 35px; padding: 0;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <textarea name="condition[]" class="form-control mt-2" placeholder="Enter condition"
                                rows="4" style="resize: none;" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="button" id="addConditionBtn" class="btn btn-secondary px-4 py-2">
                        <i class="fas fa-plus-circle me-2"></i> Add Another Condition
                    </button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-12 text-end mt-4">
                <button type="submit" name="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i> Save Certification
                </button>
            </div>
        </form>

        @if(!empty($certification['certificates']) && count($certification['certificates']) > 0)

        <div class="col-12 mt-4">
            <div class="card border-0 rounded-3 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Certifications List</h5>
                    <span class="badge bg-secondary text-white">Total: {{ $certification['total_count'] }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="text-white">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certification['certificates'] as $index => $certificate)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $certificate['certification_title'] }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $certificate['active_status'] == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{route('certificate.edit', $certificate['certificate_id'])}}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{route('certificate.show', $certificate['certificate_id'])}}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted text-end">
                    <small>Last updated: {{ now()->format('F j, Y, g:i a') }}</small>
                </div>
            </div>
        </div>
        @else
        <div class="col-12 mt-4">
            <div class="alert alert-info">
                No certifications available. Please add a new certification.
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.getElementById('showFormBtn').addEventListener('click', function() {
    const form = document.getElementById('certificationForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});

let conditionCount = 1;
document.getElementById('addConditionBtn').addEventListener('click', function() {
    conditionCount++;
    const conditionsContainer = document.getElementById('conditionsContainer');

    // Create a new condition input block with a remove button
    const newCondition = document.createElement('div');
    newCondition.classList.add('col-md-6');
    newCondition.classList.add('mt-3');
    newCondition.classList.add('condition-block');
    newCondition.innerHTML = `
            <div class="input-group mb-3 p-3 border rounded-3">
                <div class="d-flex justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <label class="fw-bold me-3" for="colorCondition${conditionCount}" style="min-width: 100px;"> Select Color ${conditionCount}</label>
                        <input type="color" id="colorCondition1" name="color_condition[]" class="form-control form-control-color" value="#66cc00" style="width: 200px; height: 60px; padding: 0;">
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-2 remove-condition" style="height: 35px; width: 35px; padding: 0;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <textarea name="condition[]" class="form-control mt-2" placeholder="Enter condition" rows="4" style="resize: none;" required></textarea>
            </div>
        `;
    conditionsContainer.appendChild(newCondition);

    // Add an event listener to the remove button
    newCondition.querySelector('.remove-condition').addEventListener('click', function() {
        newCondition.remove();
    });
});
</script>

@endsection
@extends('layouts/layoutMaster')

@section('title', 'Doctor Qualifications')
@section('description', 'Manage doctor qualifications and specializations.')

@section('content')
<div class="container mt-5">
  
@if(session('success') !== null)
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error') !== null)
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <div class="row">
        <!-- Add Button -->
        <div class="col-12 mb-4 text-end">
            <button id="showFormBtn" class="btn btn-primary px-4 py-2">
                <i class="fas fa-plus-circle me-2"></i> Add New Qualification/Specialization
            </button>
        </div>
        
        <!-- Add Qualification Form -->
        <div class="col-md-6 mx-auto" id="qualify" style="display: none;">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient text-white rounded-top py-3">
                    <h5 class="mb-0 text-center text-dark">Add Qualification</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('doctor.add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="qualification_name" class="form-label">Qualification/Specialization</label>
                            <input type="text" class="form-control" id="qualification_name" name="qualification_name" placeholder="Enter qualification" required>
                        </div>
                        <div class="mb-3">
                            <label for="qualification_type" class="form-label">Type</label>
                            <select class="form-select" id="qualification_type" name="qualification_type" required>
                                <option value="" disabled selected>Select category</option>
                                <option value="Qualification">Qualification</option>
                                <option value="Specialization">Specialization</option>
                            </select>
                        </div>
                        <input type="hidden" name="active_status" value="1">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Qualifications Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient text-dark rounded-top py-3">
                    <h5 class="mb-0 text-center">Manage Qualifications/Specialization</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Qualification Name</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @if(isset($doctors['data']))
                                @foreach ($doctors['data'] as $doctor)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>
                                            <input type="text" class="form-control qualification_name" value="{{ $doctor['qualification_name'] }}">
                                        </td>
                                        <td class="text-center">
                                            <input type="text" class="form-control" value="{{ $doctor['qualification_type'] }}" readonly>
                                        </td>
                                        <td class="text-center">
                                            <select class="form-select active_status_id">
                                                <option value="1" {{ $doctor['active_status'] == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $doctor['active_status'] == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" value="{{ $doctor['qualification_id'] }}" class="op_component_id">
                                            <button class="btn btn-success btn-sm btnsysups">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Toggle Form Visibility
    document.getElementById('showFormBtn').addEventListener('click', function() {
        const form = document.getElementById('qualify');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });

    // Update Qualification Details
    $(document).ready(function() {
        $('.btnsysups').click(function() {
            var row = $(this).closest('tr');
            var name = row.find('.qualification_name').val();
            var type = row.find('input[type="text"][readonly]').val();
            var id = row.find('.op_component_id').val();
            var active_status = row.find('.active_status_id').val();
            var token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '{{ route('doctor_update') }}',
                data: {
                    qualification_name: name,
                    qualification_type: type,
                    qualification_id: id,
                    active_status: active_status,
                    _token: token
                },
                success: function(response) {
                    var successHtml = '<div class="alert alert-success alert-dismissible fade show">' +
                                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                                      response.message + '</div>';
                    $('#messages').html(successHtml);
                },
                error: function(xhr) {
                    alert("Error: " + xhr.responseText);
                }
            });
        });
    });
</script>
@endsection

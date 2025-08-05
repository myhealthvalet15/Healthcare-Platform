@extends('layouts/layoutMaster')
@section('title', 'Add New User')
<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/assets/js/form-wizard-numbered.js'])
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('lib/js/page-scripts/pre-employment.js') }}?v=time()"></script>

@section('content')
    <!-- Default -->

    <div class="card">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Candidate</h5>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST">
                    @csrf

                    <div class="row">
                        <!-- First Name -->
                        <div class="mb-3 col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required>
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3 col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Date of Birth -->
                        <div class="mb-3 col-md-6">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="text" id="dob" name="dob" class="form-control flatpickr"
                                placeholder="YYYY-MM-DD" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Mobile -->
                        <div class="mb-3 col-md-6">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input type="text" id="mobile" name="mobile" class="form-control" required>
                        </div>

                        <!-- Aadhar Number -->
                        <div class="mb-3 col-md-6">
                            <label for="aadhar" class="form-label">Aadhar Number</label>
                            <input type="text" id="aadhar" name="aadhar" class="form-control" maxlength="12"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Register Candidate</button>
                </form>
            </div>
        </div>

    </div>
@endsection

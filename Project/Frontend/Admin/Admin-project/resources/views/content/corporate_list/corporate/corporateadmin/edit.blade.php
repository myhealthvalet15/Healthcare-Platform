@extends('layouts/layoutMaster')

@section('title', 'CorporateAdmin Type Management')
@section('description', 'Description of my dashboard')

@section('content')
    <div class="container row">
        <div class="d-flex justify-content-between align-items-center col-md-12 mb-3">
            <!-- Corporate Name and Title -->
            <div class="col-md-5">
                <p class="mb-2 text-muted">
                    Corporate &raquo; Corporate List
                </p>
                <h3 class="text-primary mb-3">
                    <strong>{{ $corporate_name }}</strong>
                    <p class="text-dark small">Corporate Super Admin Details</p>
                </h3>
            </div>

            <!-- Icons for editing options -->
            <div class="col-md-7 text-end">

                <a href="{{ route('corporate.edit', $id) }}" class="btn btn-dark btn-sm" data-bs-toggle="tooltip"
                    title="Edit Corporate Details">
                    <i class="fas fa-building"></i>
                </a>

                <a href="{{ route('corporate.editAddress', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
                    class="btn btn-info btn-sm" data-bs-toggle="tooltip" title="Edit corporaate address Details">
                    <i class="fas fa-map-marker-alt"></i>
                </a>
                @if ($emptype['corporate_id'] == $emptype['location_id'])
                    <a href="{{ route('corporate.editEmployeeTypes', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
                        class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit Employee Types">
                        <i class="fas fa-users"></i>
                    </a>

                    <a href="{{ route('corporate.editComponents', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
                        class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="edit components Details">
                        <i class="fas fa-home"></i>
                    </a>
                @endif
                <a href="{{ route('corporate.editAdminUsers', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
                    class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="edit Corporate super  Admin">
                    <i class="fas fa-user-tie"></i>
                </a>


            </div>
        </div>

    </div>
    <div class="container">




        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <form method="POST" action="{{ route('corporate.adminuser_update', $emptype['id']) }}"
            class="form-horizontal  p-4 rounded shadow-sm">
            @csrf

            <div class="row">
                <!-- First Name -->
                <div class="col-sm-6 mb-3">
                    <label for="first_name" class="form-label">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control"
                        value="{{ old('first_name', $emptype['first_name']) }}" required>
                </div>

                <!-- Last Name -->
                <div class="col-sm-6 mb-3">
                    <label for="last_name" class="form-label">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" class="form-control"
                        value="{{ old('last_name', $emptype['last_name']) }}" required>
                </div>

          

                <!-- Gender -->
                <div class="col-sm-6 mb-3">
                    <label for="gender" class="form-label">Gender:</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="male" {{ $emptype['gender'] == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $emptype['gender'] == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $emptype['gender'] == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Email -->
                <div class="col-sm-6 mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email', $emptype['email']) }}" required>
                </div>

                <!-- Mobile Country Code -->
                <div class="col-sm-6 mb-3">
                    <label for="mobile_country_code" class="form-label">Mobile Country Code:</label>
                    <input type="text" id="mobile_country_code" name="mobile_country_code" class="form-control"
                        value="{{ old('mobile_country_code', $emptype['mobile_country_code']) }}" required>
                </div>

                <!-- Mobile Number -->
                <div class="col-sm-6 mb-3">
                    <label for="mobile_num" class="form-label">Mobile Number:</label>
                    <input type="text" id="mobile_num" name="mobile_num" class="form-control"
                        value="{{ old('mobile_num', $emptype['mobile_num']) }}" required>
                </div>

                <!-- Aadhar -->
                <div class="col-sm-6 mb-3">
                    <label for="aadhar" class="form-label">Aadhar Number:</label>
                    <input type="text" id="aadhar" name="aadhar" class="form-control"
                        value="{{ old('aadhar', $emptype['aadhar']) }}" required>
                </div>

               

                <!-- Signup By -->
                <div class="col-sm-6 mb-3">
                    <label for="signup_by" class="form-label">Signup By:</label>
                    <input type="text" id="signup_by" name="signup_by" class="form-control"
                        value="{{ old('signup_by', $emptype['signup_by']) }}" required>
                </div>

                <!-- Signup On -->
                <div class="col-sm-6 mb-3">
                    <label for="signup_on" class="form-label">Signup On:</label>
                    <input type="text" id="signup_on" name="signup_on" class="form-control"
                        value="{{ old('signup_on', $emptype['signup_on']) }}" disabled>
                </div>

                <!-- Active Status -->
                <div class="col-sm-6 mb-3">
                    <label for="active_status" class="form-label">Active Status:</label>
                    <select id="active_status" name="active_status" class="form-control" required>
                        <option value="1" {{ $emptype['active_status'] == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $emptype['active_status'] == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-sm-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <!-- Back to List Button -->
                        <a href="{{ route('corporate-list') }}" class="btn btn-outline-secondary px-4 py-2">
                            <i class="fas fa-arrow-left me-2"></i> Back to List
                        </a>

                        <!-- Save Changes Button -->
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <!-- Form End -->
    </div>
@endsection

@extends('layouts/layoutMaster')
@section('title', 'User Profile - Profile')
{{-- Vendor Styles --}}
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
{{-- Page Styles --}}
@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection
{{-- Vendor Scripts --}}
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
{{-- Page Scripts --}}
@section('page-script')
@vite(['resources/assets/js/pages-profile.js'])
@endsection

@section('content')
<script>
  const employeeId = "{{ session('employee_id') }}";
  const employeeDetailsUrl = "{{ route('employee-user-details') }}?employee_id=" + employeeId;
</script>
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header-banner">
        <img id="bannerImage" src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image"
          class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 d-flex justify-content-center align-items-center mt-n2 mx-auto"
          style="border:1px solid black;">
          <img id="profileImage" src="{{ $profilePic ?? asset('assets/img/avatars/1.png') }}" alt="user image"
            class="d-block h-auto rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div
            class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6" id="empName">Employee Name</h4>
              <ul class="list-inline mb-0 d-flex flex-column align-items-start gap-2 mt-2" id="employeeInfoList"></ul>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <a href="javascript:void(0);" class="btn btn-outline-secondary mb-1" data-bs-toggle="modal"
                data-bs-target="#editProfileModal" id="editProfileBtn">
                <i class="ti ti-edit ti-xs me-1"></i> Edit
              </a>
              <a href="javascript:void(0)" id="connectedBtn" class="btn btn-primary mb-1">
                <i class='ti ti-user-check ti-xs me-2'></i>Connected
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <div class="card mb-6">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted small">About</small>
        <ul class="list-unstyled my-3 py-1">
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-id ti-lg"></i>
            <span class="fw-medium mx-2">Employee ID:</span> <span>{{ session('employee_id') }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-crown ti-lg"></i>
            <span class="fw-medium mx-2">Employee Type:</span> <span id="empType">{{ session('user_type') }}</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-building ti-lg"></i>
            <span class="fw-medium mx-2">Department:</span> <span id="empDepartment">-</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-award ti-lg"></i>
            <span class="fw-medium mx-2">Designation:</span> <span id="empDesignation">-</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-map-pin ti-lg"></i>
            <span class="fw-medium mx-2">Location:</span> <span id="empLocation">-</span>
          </li>
          <li class="d-flex align-items-center mb-4">
            <i class="ti ti-calendar ti-lg"></i>
            <span class="fw-medium mx-2">Date of Joining:</span> <span id="empDateOfJoining">-</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7">
    <div class="card card-action mb-6">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0"><i class='ti ti-chart-bar ti-lg text-body me-4'></i>Activity Timeline</h5>
      </div>
      <div class="card-body pt-3">
      </div>
    </div>
  </div>
</div>
<div class="card card-action mb-6" id="editProfileCard" style="display: none;">
  <div class="card-header align-items-center">
    <h5 class="card-action-title mb-0"><i class="ti ti-edit ti-lg text-body me-4"></i>Edit Profile</h5>
    <button type="button" class="btn-close ms-auto" aria-label="Close"
      onclick="document.getElementById('editProfileCard').style.display='none'"></button>
  </div>
  <div class="card-body">
    <form id="editProfileForm" enctype="multipart/form-data">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" id="editFirstName" name="first_name">
        </div>
        <div class="col-md-6">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" id="editLastName" name="last_name">
        </div>
        <div class="col-md-6">
          <label class="form-label">Date of Birth</label>
          <input type="date" class="form-control" id="editDob" name="date_of_birth">
        </div>
        <div class="col-md-6">
          <label class="form-label">Gender</label>
          <select class="form-control" id="editGender" name="gender">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone Number</label>
          <input type="text" class="form-control" id="editPhone" name="contact_number">
        </div>
        <div class="col-md-6">
          <label class="form-label">Alternative Email</label>
          <input type="email" class="form-control" id="editAltEmail" name="alternative_email">
        </div>
        <div class="col-md-6">
          <label class="form-label">Aadhar ID</label>
          <input type="text" class="form-control" id="editAadharId" name="aadhar_id">
        </div>
        <div class="col-md-6">
          <label class="form-label">ABHA ID</label>
          <input type="text" class="form-control" id="editAbhaId" name="abha_id">
        </div>
        <div class="col-md-6">
          <label class="form-label">Area</label>
          <input type="text" class="form-control" id="editArea" name="area">
        </div>
        <div class="col-md-6">
          <label class="form-label">Zipcode</label>
          <input type="text" class="form-control" id="editZipcode" name="zipcode">
        </div>
        <div class="col-md-6">
          <label class="form-label">Profile Picture</label>
          <input type="file" class="form-control" id="editProfilePic" name="profile_pic" accept="image/*">
        </div>
        <div class="col-md-6">
          <label class="form-label">Banner Image</label>
          <input type="file" class="form-control" id="editBanner" name="banner" accept="image/*">
        </div>
      </div>
      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="button" class="btn btn-secondary ms-2"
          onclick="document.getElementById('editProfileCard').style.display='none'">Cancel</button>
      </div>
    </form>
  </div>
</div>
<script src="/lib/js/page-scripts/user_employee_personal_information.js"></script>
@endsection
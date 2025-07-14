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

<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header-banner">
        <img id="bannerImage" src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
        <div class="flex-shrink-0 d-flex justify-content-center align-items-center mt-n2 mx-auto" style="border:1px solid black;">
  <img id="profileImage" src="{{ $profilePic ?? asset('assets/img/avatars/1.png') }}" alt="user image" class="d-block h-auto rounded user-profile-img">
</div>

        <div class="flex-grow-1 mt-3 mt-lg-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4 class="mb-2 mt-lg-6" id="empName">Employee Name</h4>
              <ul class="list-inline mb-0 d-flex flex-column align-items-start gap-2 mt-2" id="employeeInfoList"></ul>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <a href="javascript:void(0);" class="btn btn-outline-secondary mb-1" data-bs-toggle="modal" data-bs-target="#editProfileModal" id="editProfileBtn">
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

<!-- User Profile Content -->
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5">
    <!-- About User -->
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
    <!-- Activity Timeline -->
    <div class="card card-action mb-6">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0"><i class='ti ti-chart-bar ti-lg text-body me-4'></i>Activity Timeline</h5>
      </div>
      <div class="card-body pt-3">
        <!-- Add activity data here -->
      </div>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<!-- Edit Profile Inline Form (below Activity Timeline) -->
<div class="card card-action mb-6" id="editProfileCard" style="display: none;">
  <div class="card-header align-items-center">
    <h5 class="card-action-title mb-0"><i class="ti ti-edit ti-lg text-body me-4"></i>Edit Profile</h5>
    <button type="button" class="btn-close ms-auto" aria-label="Close" onclick="document.getElementById('editProfileCard').style.display='none'"></button>
  </div>
  <div class="card-body">
    <form id="editProfileForm" enctype="multipart/form-data">
      @csrf
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
        <button type="button" class="btn btn-secondary ms-2" onclick="document.getElementById('editProfileCard').style.display='none'">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Show inline form instead of modal
  document.getElementById('editProfileBtn').addEventListener('click', function () {
    document.getElementById('editProfileCard').style.display = 'block';
    fetch(employeeDetailsUrl)
      .then(res => res.json())
      .then(data => {
        document.getElementById('editFirstName').value = data.employee_firstname || '';
        document.getElementById('editLastName').value = data.employee_lastname || '';
        document.getElementById('editDob').value = data.employee_dob || '';
        document.getElementById('editGender').value = (data.employee_gender || '').toLowerCase();
        document.getElementById('editPhone').value = data.employee_contact_number || '';
        document.getElementById('editAltEmail').value = data.alternative_email || '';
        document.getElementById('editAadharId').value = data.aadhar_id || '';
        document.getElementById('editAbhaId').value = data.abha_id || '';
        document.getElementById('editArea').value = data.area || '';
        document.getElementById('editZipcode').value = data.zipcode || '';
      });
  });
</script>

<script>
  const employeeId = "{{ session('employee_id') }}";
  const employeeDetailsUrl = "{{ route('employee-user-details') }}?employee_id=" + employeeId;

  document.addEventListener('DOMContentLoaded', function () {
    fetchEmployeeDetails(employeeId);
  });

  function fetchEmployeeDetails(employeeId) {
    apiRequest({
      url: employeeDetailsUrl,
      method: "GET",
      onSuccess: (data) => {
        if (data && data.employee_id) {
          document.getElementById("empName").textContent = `${data.employee_firstname} ${data.employee_lastname}`;
          document.getElementById("empType").textContent = capitalizeFirstLetter(data.employee_type_name || '-');
          document.getElementById("empDepartment").textContent = capitalizeFirstLetter(data.employee_department || '-');
          document.getElementById("empDesignation").textContent = capitalizeFirstLetter(data.employee_designation || '-');
          document.getElementById("empLocation").textContent = data.employee_location_name || '-';
          document.getElementById("empDateOfJoining").textContent = data.dateOfJoining || '-';

          document.getElementById("profileImage").src = data.profile_pic || "{{ asset('assets/img/avatars/1.png') }}";
          document.getElementById("bannerImage").src = data.banner || "{{ asset('assets/img/pages/profile-banner.png') }}";

          const infoList = document.getElementById("employeeInfoList");
          infoList.innerHTML = '';

          const firstLine = document.createElement('li');
          firstLine.className = 'list-inline-item d-flex gap-3 align-items-center flex-wrap';

          firstLine.innerHTML = `
            <span><i class="ti ti-mail ti-lg"></i> ${data.employee_email || 'N/A'}</span>
            <span><i class="ti ti-phone ti-lg"></i> ${data.employee_contact_number || 'N/A'}</span>
            <span><i class="ti ti-user ti-lg"></i> ${data.employee_age || '?'} yrs • ${capitalizeFirstLetter(data.employee_gender || '')}</span>
          `;
          infoList.appendChild(firstLine);

          const connectedBtn = document.getElementById("connectedBtn");
          if (connectedBtn) {
            connectedBtn.innerHTML = `<i class='ti ti-user-check ti-xs me-2'></i>Connected to ${data.employee_corporate_name || 'Corporate'}`;
          }
        }
      },
      onError: (error) => console.error('Error fetching employee details:', error)
    });
  }

  function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
  }

  document.getElementById('editProfileForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = document.getElementById('editProfileForm');

    const firstName = form.first_name.value.trim();
    const lastName = form.last_name.value.trim();
    const dob = form.date_of_birth.value;
    const gender = form.gender.value;
    const phone = form.contact_number.value.trim();
    const alternativeEmail = form.alternative_email.value.trim();
    const aadharId = form.aadhar_id.value.trim();
    const abhaId = form.abha_id.value.trim();
    const area = form.area.value.trim();
    const zipcode = form.zipcode.value.trim();

    const profilePicInput = document.getElementById('editProfilePic');
    const bannerInput = document.getElementById('editBanner');

    const profilePic = profilePicInput.files[0];
    const banner = bannerInput.files[0];

    const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/tiff', 'image/webp'];

    // 1. Basic field validation
    if (!firstName) return showToast('error', 'Validation Error', 'First name is required.');
    if (!lastName) return showToast('error', 'Validation Error', 'Last name is required.');
    if (!dob) return showToast('error', 'Validation Error', 'Date of birth is required.');
    if (!gender) return showToast('error', 'Validation Error', 'Gender is required.');
    if (!phone) return showToast('error', 'Validation Error', 'Phone number is required.');

    // 2. Image validation (only if files selected)
    if (profilePic) {
      if (!validImageTypes.includes(profilePic.type)) {
        return showToast('error', 'Invalid Image', 'Profile picture must be a valid image type.');
      }
      if (profilePic.size > 200 * 1024) {
        return showToast('error', 'File Too Large', 'Profile picture must be under 200KB.');
      }
    }
    if (banner) {
      if (!validImageTypes.includes(banner.type)) {
        return showToast('error', 'Invalid Image', 'Banner image must be a valid image type.');
      }
      if (banner.size > 1024 * 1024) {
        return showToast('error', 'File Too Large', 'Banner image must be under 1MB.');
      }
    }

    // Helper function: convert File to base64 (returns Promise)
    function fileToBase64(file) {
      return new Promise((resolve, reject) => {
        if (!file) resolve(null);  // no file, return null
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
        reader.readAsDataURL(file); // this produces base64 string with prefix "data:<mime>;base64,..."
      });
    }

    // Confirmation dialog
    Swal.fire({
      title: "Are you sure?",
      text: "Do you want to update your profile?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, update it!"
    }).then(async (result) => {
      if (!result.isConfirmed) return;

      const employeeId = "{{ session('employee_id') }}";
      const updateUrl = `{{ route('update-profile-details', ['id' => ':id']) }}`.replace(':id', employeeId);

      try {
        const profilePicBase64 = await fileToBase64(profilePic);
        const bannerBase64 = await fileToBase64(banner);

        // Prepare JSON payload
        const payload = {
          first_name: firstName,
          last_name: lastName,
          date_of_birth: dob,
          gender: gender,
          contact_number: phone,
          alternative_email: alternativeEmail,
          aadhar_id: aadharId,
          abha_id: abhaId,
          area: area,
          zipcode: zipcode,
          profile_pic: profilePicBase64, // will be null if no file selected
          banner: bannerBase64 // will be null if no file selected
        };

        const response = await fetch(updateUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(payload)
        });

        if (!response.ok) throw new Error('Server error');

        const data = await response.json();

        showToast('success', 'Success', 'Profile updated successfully!');
        fetchEmployeeDetails(employeeId);

        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
          if (modal) modal.hide();
          document.getElementById('editProfileCard').style.display = 'none';
        }, 400);

      } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Error', 'Failed to update profile.');
      }
    });
  });
</script>


@endsection

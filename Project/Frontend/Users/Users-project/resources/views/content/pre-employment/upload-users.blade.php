@extends('layouts/layoutMaster')
@section('title', 'Upload Users')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',

])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([

'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',

])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js'
])
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('lib/js/page-scripts/pre-employment.js') }}?v={{ time() }}"></script>

@section('content')
<!-- Default -->

<div class="card">
<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Upload Users via Excel</h5>
  </div>

  <div class="card-body">
    <!-- Display Validation Errors -->
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Success Message -->
    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    <form  method="POST" enctype="multipart/form-data">
      @csrf

      <!-- File Upload Input -->
      <div class="mb-3">
        <label for="users_file" class="form-label">Select Excel File (.xlsx or .csv)</label>
        <input type="file" class="form-control" id="users_file" name="users_file" accept=".xlsx,.xls,.csv" required>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <!-- Download Sample File -->
    <div class="mt-3">
      <a href="{{ asset('samples/user_upload_sample.xlsx') }}" class="btn btn-outline-secondary">
        Download Sample Template
      </a>
    </div>
  </div>
</div>
  
  
</div>
@endsection
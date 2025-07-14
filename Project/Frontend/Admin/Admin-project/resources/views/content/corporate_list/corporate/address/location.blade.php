@extends('layouts.layoutMaster')

@section('title', 'Corporate Dashboard')
@section('description', 'Manage corporate details and locations.')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('content')
<div class="container mt-5">
    <h6 class="text-center mb-4">Corporate Address</h6>

    <!-- Corporate Details Section -->
    <div class="d-flex justify-content-center mb-4">
        <div class="p-3 border rounded shadow-sm bg-light text-center" style="width: auto;">
            <strong>Corporate Name:</strong> <span>{{ $corporate_name }}</span>
        </div>
    </div>

    <!-- Add Location Form -->
    <form action="{{ route('corporate_address_location', ['corporate_id' => $corporate_id]) }}" method="POST">
        @csrf
        <div id="personal-info-validation" class="content">
            <div class="content-header mb-4">
                <h6 class="mb-0">Add locations</h6>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="formValidationCountry" class="form-label font-weight-bold">Pincode</label>
                    <select id="formValidationCountry" name="address_id" class="select2 form-control border rounded"
                        data-placeholder="Select the pincode" aria-label="Select a pincode">
                        <option value="">Select the pincode</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="areaoptions" class="form-label font-weight-bold">Select Area</label>
                    <select id="areaoptions" name="area" class="select2 form-control border rounded"
                        data-placeholder="Please select an area" aria-label="Select an area">
                        <option value="">Please select an area</option>
                    </select>
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill shadow-sm hover-shadow-lg">Save
                    Address</button>
            </div>
        </div>
    </form>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for Pincode
    $('#formValidationCountry').select2({
        placeholder: "Select the pincode",
        minimumInputLength: 4,
        ajax: {
            url: '{{ route('findpincode') }}',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { pincode: params.term };
            },
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.address_id,
                        text: item.address_name
                    }))
                };
            }
        }
    });

    // On selecting a Pincode, fetch Areas
    $('#formValidationCountry').on('change', function() {
        const addressId = $(this).val();
        const token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '{{ route('area_find') }}',
            type: 'POST',
            data: { address_id: addressId, _token: token },
            success: function(response) {
                populateDropdown('#areaoptions', response);
            }
        });
    });

    // Helper function to populate dropdowns
    function populateDropdown(selector, items) {
        const dropdown = $(selector);
        dropdown.empty().append('<option value="">Please select</option>');
        items.forEach(item => {
            dropdown.append(`<option value="${item.address_id}">${item.address_name}</option>`);
        });
    }
});
</script>
@endsection

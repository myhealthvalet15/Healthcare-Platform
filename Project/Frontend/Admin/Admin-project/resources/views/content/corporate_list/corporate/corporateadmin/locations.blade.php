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
    <h6 class="text-center mb-4">Corporate Details</h6>

    <!-- Corporate Details Section -->
    <div class="d-flex justify-content-center mb-4">
        <div class="p-3 border rounded shadow-sm bg-light text-center" style="width: auto;">
            <strong>Corporate Name:</strong> <span>{{ $corporate_name }}</span>
        </div>
    </div>

    <!-- Add Location Form -->
    <form action="{{ route('auperadmin.add_location', ['corporate_id' => $corporate_id]) }}" method="POST">
    @csrf
    <div id="personal-info-validation" class="content">
        <div class="content-header mb-4">
            <h6 class="mb-0">Add locations</h6>
          
        </div>
        <div class="row g-4">
        <div class="col-md-6">
                <label for="formValidationCountry" class="form-label font-weight-bold">Pincode</label>
                <select id="formValidationCountry" name="address_id" 
                        class="select2 form-control border rounded" 
                        data-placeholder="Select the pincode" aria-label="Select a pincode">
                    <option value="">Select the pincode</option>
                </select>
            </div>
            <div class="col-sm-6" id="Area">
                <label for="areaoptions" class="form-label font-weight-bold">Select Area</label>
                <select id="areaoptions" name="area" class="select2 form-control border rounded" data-placeholder="Please select an area" aria-label="Select an area">
                    <option value="">Please select an area</option>
                </select>
            </div>

            <div class="col-sm-6" id="City" style="display: none;">
                <label for="cityoptions" class="form-label font-weight-bold">Search by City</label>
                <select id="cityoptions" name="city" class="select2 form-control border rounded" data-placeholder="Please select a city" aria-label="Select a city"></select>
            </div>

            <div class="col-sm-6" id="State" style="display: none;">
                <label for="stateoptions" class="form-label font-weight-bold">Search by State</label>
                <select id="stateoptions" name="state" class="select2 form-control border rounded" data-placeholder="Please select a state" aria-label="Select a state"></select>
            </div>

            <div class="col-sm-6" id="Country" style="display: none;">
                <label for="countryoptions" class="form-label font-weight-bold">Country</label>
                <select id="countryoptions" name="country" class="select2 form-control border rounded" data-placeholder="Please select a country" aria-label="Select a country"></select>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill shadow-sm hover-shadow-lg">Save Address</button>
            </div>

        </div>
    </div>
</form>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    
    $(function () {
    const select2 = $('.select2'),
        selectPicker = $('.selectpicker');

    // Initialize Bootstrap Select
    if (selectPicker.length) {
        selectPicker.selectpicker();
    }

    // Initialize Select2 for all elements with the 'select2' class
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            var placeholderText = $this.data('placeholder') || 'Please select'; // Default or custom placeholder
            $this.select2({
                placeholder: placeholderText,
                dropdownParent: $this.parent(),
            });
        });
    }

    $(document).ready(function() {
    $('#formValidationCountry').select2({
    placeholder: "Select the pincode",
    allowClear: true,
    minimumInputLength: 4,  
    ajax: {
        url: '{{route('findpincode')}}',  
        type: 'GET',
        dataType: 'json',
        delay: 250,  
        data: function (params) {
            return {
                pincode: params.term  
            };
        },
        processResults: function (data) {
            console.log(data);  

            return {
                results: data.map(function(item) {
                    return {
                        id: item.address_id,  
                        text: item.address_name  
                    };
                })
            };
        },
        cache: true
    }
});


   
});
$('#formValidationCountry').change(function() {
        var address_id = $('#formValidationCountry').val();
        //alert(address_id);
        var address_name = $('#formValidationCountry option:selected').text();  // This will give you the 'address_name'
        //alert(address_name);
        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type: 'POST',
            url: "{{ route('area_find') }}",
            data: { 
                address_name: address_name,
                _token: token 
            },
            success: function(response) {
                $('#areaoptions').empty();
                $('#areaoptions').append('<option label=" ">Please search area </option>');

                $.each(response, function(index, area) {
                    $('#areaoptions').append('<option value="' + area.address_id + '">' + area.address_name + '</option>');
                });

                $('#areaoptions').trigger('change');
                initializeSelect2($('#areaoptions'));
            },
            error: function(xhr, status, error) {
                console.error("There was an error:", error);
            }
        });
    });

    $('#areaoptions').change(function(){
    var area_id = $('#areaoptions').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: "{{ route('findlocation') }}",
        type: "POST",
        data: {
            address_id: area_id,
            _token: token
        },
        success: function(response) {
            if (response?.state?.length > 0) {
                populateDropdown('#stateoptions', response.state);
                initializeSelect2($('#stateoptions'));  // Initialize select2 for state options

                // Set default value for State dropdown (if any)
                if (response?.state?.length > 0) {
                    $('#stateoptions').val(response.state[0].address_id).trigger('change');
                }
            }

            if (response?.country?.length > 0) {
                populateDropdown('#countryoptions', response.country);
                initializeSelect2($('#countryoptions'));  // Initialize select2 for country options

                // Set default value for Country dropdown (if any)
                if (response?.country?.length > 0) {
                    $('#countryoptions').val(response.country[0].address_id).trigger('change');
                }
            }

            if (response?.city?.length > 0) {
                populateDropdown('#cityoptions', response.city);
                initializeSelect2($('#cityoptions'));  // Initialize select2 for city options

                // Set default value for City dropdown (if any)
                if (response?.city?.length > 0) {
                    $('#cityoptions').val(response.city[0].address_id).trigger('change');
                }

                $("#Country, #City, #State").show();  // Show the dropdowns
            }
        },
        error: function(xhr) {
            console.error('Error fetching addresses:', xhr.responseText || xhr.statusText);
            alert('Error fetching addresses. Please try again later.');
        }
    });
});


    function populateDropdown(selector, items) {
        if ($(selector).length) {
            $(selector).empty(); // Clear existing options
            $(selector).append('<option value="">Please select</option>'); // Add the default option

            $.each(items, function(key, value) {
                $(selector).append(`<option value="${value.address_id}">${value.address_name}</option>`);
            });
        }
    }

    function initializeSelect2($element) {
        if ($element.length && !$element.hasClass('select2-hidden-accessible')) {
            $element.select2({
                placeholder: $element.data('placeholder') || 'Please select',
                dropdownParent: $element.parent()
            });
        }
    }




});

   

    // Add logic to update dynamic dropdowns based on selected pincode
    


</script>
@endsection

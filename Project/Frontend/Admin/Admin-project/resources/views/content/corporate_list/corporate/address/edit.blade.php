@extends('layouts/layoutMaster')

@section('title', 'Edit Corporate Address')
@section('description', 'Description of my dashboard')
@section('content')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

<!-- Vendor Scripts -->
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

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js',
'resources/assets/js/form-wizard-validation.js'
])
@endsection

<div class="container">
<div class="container row">
    <div class="d-flex justify-content-between align-items-center col-md-12 mb-3">
        <!-- Corporate Name and Title -->
        <div class="col-md-5">
        <p class="mb-2 text-muted">
            Corporate &raquo; Corporate List
        </p>
            <h3 class="text-primary mb-3">
                <strong>{{ $corporate_name['corporate_name'] }}</strong>
                <p class="text-dark small">Corporate Address Details</p>
            </h3>
        </div>

        <!-- Icons for editing options -->
        <div class="col-md-7 text-end">

        <a href="{{ route('corporate.edit', ['id' => $id]) }}" 
           class="btn btn-dark btn-sm me-2" data-bs-toggle="tooltip" 
           title="Edit Corporate Details">
            <i class="fas fa-building"></i>
        </a>

        <a href="{{ route('corporate.editAddress', ['id' => $id, 'corporate_id' => $corporate_id]) }}" 
           class="btn btn-info btn-sm me-2" data-bs-toggle="tooltip" 
           title="Edit Corporate Address Details">
            <i class="fas fa-map-marker-alt"></i>
        </a>

        @if($corporate_address['corporate_id'] == $corporate_address['location_id'])
        <a href="{{ route('corporate.editEmployeeTypes',['id' => $id, 'corporate_id' => $corporate_id]) }}" class="btn btn-primary btn-sm"
            data-bs-toggle="tooltip" title="Edit Employee Types">
            <i class="fas fa-users"></i>
        </a>

        <a href="{{ route('corporate.editComponents', ['id' => $id, 'corporate_id' => $corporate_id]) }}" class="btn btn-success btn-sm"
            data-bs-toggle="tooltip" title="edit components Details">
            <i class="fas fa-home"></i>
        </a>
        @endif
        <a href="{{ route('corporate.editAdminUsers',['id' => $id, 'corporate_id' => $corporate_id]) }}"
            class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="edit Corporate super  Admin">
            <i class="fas fa-user-tie"></i>
        </a>
    

        </div>
    </div>
   
</div>
 
         



    <div class="d-flex justify-content-center">

        <div class="card shadow rounded-lg border-0" style="max-width: 800px; width: 100%;">
            <div class="card-body p-4">

                <div id="form-step-2" class="step-form">
                    <form id="step-2-form" method="post"
                        action="{{ route('corporate.updateaddress', ['id' => $corporate_address['id']]) }}">
                        @csrf
                        <div class="row g-3">

                          
                            <div class="col-md-6">
                                <label class="form-label" for="formValidationCountry">Pincode</label>
                                <select class="select2 form-control border rounded" id="formValidationCountry"
                                    name="pincode_id">
                                    <option value="{{ $pincode['address_id'] }}">


                                        {{ $pincode['address_name'] }}
                                    </option>
                                </select>
                            </div>


                          
                            <div class="col-md-6">
                                <label class="form-label" for="areaoptions">Area</label>
                                <select class="select2 form-control border rounded" id="areaoptions" name="area_id">
                                    <option value="{{ $area['address_id'] }}">
                                        {{ $area['address_name'] }}
                                    </option>
                                </select>
                            </div>

                          
                            <div class="col-md-6">
                                <label class="form-label" for="city">City</label>
                                <select class="select2 form-control border rounded" id="city" name="city_id">
                                    <option value="{{ $city['address_id'] }}">
                                        {{ $city['address_name'] }}
                                    </option>
                                </select>
                            </div>

                         
                            <div class="col-md-6">
                                <label class="form-label" for="state">State</label>
                                <select class="select2 form-control border rounded" id="state" name="state_id">
                                    <option value="{{ $state['address_id'] }}">
                                        {{ $state['address_name'] }}
                                    </option>
                                </select>
                            </div>

                     
                            <div class="col-md-6">
                                <label class="form-label" for="country">Country</label>
                                <select class="select2 form-control border rounded" id="country" name="country_id">
                                    <option value="{{ $country['address_id'] }}">
                                        {{ $country['address_name'] }}
                                    </option>
                                </select>
                            </div>

                           
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" id="latitude" value="{{$corporate_address['latitude']}}"
                                    name="latitude" class="form-control border rounded shadow-sm"
                                    placeholder="Latitude">
                            </div>

                        
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" id="longitude" value="{{$corporate_address['longitude']}}"
                                    name="longitude" class="form-control border rounded shadow-sm"
                                    placeholder="Longitude">
                            </div>

                       
                            <div class="col-md-6">
                                <label for="website_link" class="form-label">Website Link</label>
                                <input type="text" id="website_link" name="website_link"
                                    value="{{$corporate_address['website_link']}}"
                                    class="form-control border rounded shadow-sm" placeholder="Website Link">
                            </div>

                           
                            <div class="col-12 text-center mt-4">
                                <button type="submit" id="submit-form"
                                    class="btn btn-success rounded-pill shadow-sm px-4 py-2 w-50">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function() {
    const select2 = $('.select2'),
        selectPicker = $('.selectpicker');

   
    if (selectPicker.length) {
        selectPicker.selectpicker();
    }

   
    if (select2.length) {
        select2.each(function() {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            var placeholderText = $this.data('placeholder') ||
                'Please select'; 
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
                data: function(params) {
                    return {
                        pincode: params.term
                    };
                },
                processResults: function(data) {
                    const uniqueNames = new Set();

return {
    results: data
        .filter(function(item) {
            // Only include items with unique address names
            if (!uniqueNames.has(item.address_name)) {
                uniqueNames.add(item.address_name);
                return true;
            }
            return false;
        })
        .map(function(item) {
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




        $('#formValidationCountry').change(function() {
            var address_id = $('#formValidationCountry').val();
            //alert(address_id);
            var address_name = $('#formValidationCountry option:selected')
                .text(); // This will give you the 'address_name'
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
                    /// console.log(response);
                    $('#areaoptions').empty();
                    $('#areaoptions').append(
                        '<option label=" ">Please search area </option>');

                    $.each(response, function(index, area) {
                        $('#areaoptions').append('<option value="' + area
                            .address_id +
                            '">' + area.address_name + '</option>');
                    });

                    $('#areaoptions').trigger('change');
                    initializeSelect2($('#areaoptions'));
                },
                error: function(xhr, status, error) {
                    console.error("There was an error:", error);
                }
            });
        });

        $('#areaoptions').change(function() {
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
                    console.log(response);
                    if (response?.state?.length > 0) {
                        populateDropdown('#state', response.state);
                        initializeSelect2($(
                            '#state'
                            )); 

                        
                        if (response?.state?.length > 0) {
                            $('#state').val(response.state[0].address_id)
                                .trigger(
                                    'change');
                        }
                    }

                    if (response?.country?.length > 0) {
                        populateDropdown('#country', response.country);
                        initializeSelect2($(
                            '#country'
                            )); 
   
                        if (response?.country?.length > 0) {
                            $('#country').val(response.country[0].address_id)
                                .trigger(
                                    'change');
                        }
                    }

                    if (response?.city?.length > 0) {
                        populateDropdown('#city', response.city);
                        initializeSelect2($(
                            '#city')); 

                    
                        if (response?.city?.length > 0) {
                            $('#city').val(response.city[0].address_id)
                                .trigger(
                                    'change');
                        }

                        $("#Country, #City, #State").show(); // Show the dropdowns
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching addresses:', xhr.responseText ||
                        xhr
                        .statusText);
                    alert('Error fetching addresses. Please try again later.');
                }
            });
        });


        function populateDropdown(selector, items) {
            if ($(selector).length) {
                $(selector).empty(); // Clear existing options
                $(selector).append('<option value="">Please select</option>'); // Add the default option

                $.each(items, function(key, value) {
                    $(selector).append(
                        `<option value="${value.address_id}">${value.address_name}</option>`
                        );
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

});
</script>

@endsection
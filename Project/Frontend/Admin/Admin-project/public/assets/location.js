alert('hello');

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

    // Initialize Select2 for the specific dropdown
   // $(document).ready(function() {
    // Initialize the Select2
    $('#formValidationCountry').select2({
    placeholder: "Select the pincode",
    allowClear: true,
    minimumInputLength: 4,  
    ajax: {
        url: pincodeUrl,  // Your backend endpoint for retrieving pincode data
        type: 'GET',
        dataType: 'json',
        delay: 250,  // Delay between requests to reduce server load
        data: function (params) {
            return {
                pincode: params.term  // Send the search term to the backend
            };
        },
        processResults: function (data) {
            console.log(data);  // Check the structure of the data returned

            // Ensure the data is in the expected format
            return {
                results: data.map(function(item) {
                    return {
                        id: item.address_id,  // Using address_id as the unique identifier
                        text: item.address_name  // Displaying address_name as the option text (assuming it's the pincode)
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

   
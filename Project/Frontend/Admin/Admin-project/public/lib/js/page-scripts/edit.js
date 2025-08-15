
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
                    url: '/location/findpincode',
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
                var address_name = $('#formValidationCountry option:selected')
                    .text(); 
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
                            $("#Country, #City, #State").show(); 
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
                    $(selector).empty(); 
                    $(selector).append('<option value="">Please select</option>'); 
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

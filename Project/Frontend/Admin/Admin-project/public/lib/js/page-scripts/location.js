$(document).ready(function () {
    $('#formValidationCountry').select2({
        placeholder: "Select the pincode",
        minimumInputLength: 4,
        ajax: {
            url: '/location/findpincode',
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { pincode: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.address_id,
                        text: item.address_name
                    }))
                };
            }
        }
    });
    $('#formValidationCountry').on('change', function () {
        const addressId = $(this).val();
        const token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '/area_find',
            type: 'POST',
            data: { address_id: addressId, _token: token },
            success: function (response) {
                populateDropdown('#areaoptions', response);
            }
        });
    });
    function populateDropdown(selector, items) {
        const dropdown = $(selector);
        dropdown.empty().append('<option value="">Please select</option>');
        items.forEach(item => {
            dropdown.append(`<option value="${item.address_id}">${item.address_name}</option>`);
        });
    }
});

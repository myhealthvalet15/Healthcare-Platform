$(document).ready(function() {
 
    // Toggle the form visibility when the button is clicked
    $('#showFormBtn').click(function() {
        var form = $('#addInjuryForm');
        
        // Toggle form visibility
        form.toggle();
    });

    // Handle the 'add' button click
    $('.frombtnn').click(function(e) {
        e.preventDefault();

        // Get the closest form container and toggle the visibility of the form
        $(this).closest('.container').find('#addInjuryForm').toggle();
    });

    // Handle the 'add injury' button click for sending data
    $('.btnsysadd').click(function(e) {
        e.preventDefault();

        var $row = $(this).closest('tr');

        var name = $row.find('.typ').val().trim();
        var cat = $row.find('.cat').val().trim();
        var active_status_id = $row.find('.active_status_id').val().trim();

        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type: 'POST',
            url: injuryAddUrl,  // Make sure route is correctly defined in Blade
            data: {
                op_component_name: name,
                op_component_type: cat,
                active_status: active_status_id,
                _token: token
            },
            success: function(response) {
                // Display success message
                var successHtml = '<div class="alert alert-success">' +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></i></strong> ' + response.message +
                    '</div>';

                $('.messages').html(successHtml);

                // Redirect after a brief delay (you can customize this URL)
                setTimeout(function() {
                    window.location.href = ''; // Replace with your target URL
                }, 2000);

                toastr.success(response.message, 'Success');  // Toastr success notification
            },
            error: function(xhr) {
                // Handle error (show alert and log the error)
                alert("Error: " + xhr.responseText);
                console.error(xhr);
            }
        });
    });

    // Handle the 'update injury' button click for sending updated data
    $('.btnupsys').click(function(e) {
        e.preventDefault();

        var $row = $(this).closest('tr');

        var mdinjuryValue = $row.find('.mdinjury').val().trim();
        var injuryIdValue = $row.find('.op_component_id').val().trim();
        var op_component_type = $row.find('.op_component_type').val().trim();

        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type: 'POST',
            url: injuryUpdateUrl,  // Make sure route is correctly defined in Blade
            data: {
                op_component_name: mdinjuryValue,
                op_component_id: injuryIdValue,
                op_component_type: op_component_type,
                _token: token
            },
            success: function(response) {
                // Display success message
                var successHtml = '<div class="alert alert-success">' +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></i></strong> ' + response.message +
                    '</div>';

                $('.messages').html(successHtml);

                // Optionally, update the DOM or log the response for debugging
                console.log(response);
            },
            error: function(xhr) {
                // Handle error (show alert and log the error)
                alert("Error: " + xhr.responseText);
                console.error(xhr);
            }
        });
    });
});

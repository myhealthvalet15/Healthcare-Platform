document.getElementById('showFormBtn').addEventListener('click', function () {
    var form = document.getElementById('addInjuryForm');
});
$(document).ready(function () {
    $('.frombtnn').click(function (e) {
        e.preventDefault();
        $(this).closest('.container').find('#addInjuryForm').toggle();
    });
    $(document).ready(function () {
        $('.btnsysadd').click(function (e) {
            e.preventDefault();
            var name = $('.typ').val().trim();
            var cat = $('.cat').val().trim();
            var active_status_id = $('.active_status_id').val().trim();
            var token = $('meta[name="csrf-token"]').attr('content');
            if (!name) {
                toastr.error('Injury name is required.', 'Error');
                return;
            }
            $.ajax({
                type: 'POST',
                url: '/addinjury',
                data: {
                    op_component_name: name,
                    op_component_type: cat,
                    active_status: active_status_id,
                    _token: token
                },
                success: function (response) {
                    var successHtml = `
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong><i class="glyphicon glyphicon-ok-sign push-5-r"></i></strong> 
                        ${response.message}
                    </div>`;
                    $('.messages').html(successHtml);
                    $('#injuryForm')[0].reset();
                    setTimeout(function () {
                        $('#tab-content-3').show();
                        window.location.href = '';
                    }, 1000);
                    toastr.success(response.message, 'Success');
                    console.log(response);
                },
                error: function (xhr) {
                    var response = xhr.responseJSON || {};
                    var errorMessage = response.message ||
                        'An error occurred. Please try again.';
                    toastr.error(errorMessage, 'Error');
                    console.error(xhr);
                }
            });
        });
    });
});
$(document).ready(function () {
    $('.btnupsys').click(function (e) {
        var $row = $(this).closest('tr');
        var mdinjuryValue = $row.find('.mdinjury').val().trim();
        var injuryIdValue = $row.find('.op_component_id').val().trim();
        var op_component_type = $row.find('.op_component_type').val().trim();
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'POST',
            url: '/update_injury',
            data: {
                op_component_name: mdinjuryValue,
                op_component_id: injuryIdValue,
                op_component_type: op_component_type,
                _token: token
            },
            success: function (response) {
                var messages = $('.messages');
                var successHtml = '<div class="alert alert-success">' +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> ' +
                    response.message +
                    '</div>';
                $(messages).html(successHtml);
                console.log(response);
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseText);
                console.error(xhr);
            }
        });
    });
});
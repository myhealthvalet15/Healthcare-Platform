document.getElementById('showFormBtn').addEventListener('click', function () {
    var form = document.getElementById('addInjuryForm');
});
$(document).ready(function () {
    $('.frombtnn').click(function (e) {
        e.preventDefault();
        $(this).closest('.container').find('#addInjuryForm').toggle();
    });
    $('.btnsysadd').click(function (e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var name = $row.find('.typ').val().trim();
        var cat = $row.find('.cat').val().trim();
        var active_status_id = $row.find('.active_status_id').val().trim();
        var token = $('meta[name="csrf-token"]').attr('content');
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
                var messages = $('.messages');
                var successHtml = '<div class="alert alert-success">' +
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                    '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> ' +
                    response.message +
                    '</div>';
                $(messages).html(successHtml);
                console.log(response);
                setTimeout(function () {
                    window.location.href = '';
                }, 2000);
                toastr.success(response.message, 'Success');
            },
            error: function (response) {
                alert(response.error);
                console.error(xhr);
            }
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
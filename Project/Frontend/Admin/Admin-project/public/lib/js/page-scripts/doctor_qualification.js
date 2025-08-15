document.getElementById('showFormBtn').addEventListener('click', function () {
    const form = document.getElementById('qualify');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});
$(document).ready(function () {
    $('.btnsysups').click(function () {
        var row = $(this).closest('tr');
        var name = row.find('.qualification_name').val();
        var type = row.find('input[type="text"][readonly]').val();
        var id = row.find('.op_component_id').val();
        var active_status = row.find('.active_status_id').val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'POST',
            url: '/others/doctor-qualifications/update',
            data: {
                qualification_name: name,
                qualification_type: type,
                qualification_id: id,
                active_status: active_status,
                _token: token
            },
            success: function (response) {
                var successHtml = '<div class="alert alert-success alert-dismissible fade show">' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    response.message + '</div>';
                $('#messages').html(successHtml);
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseText);
            }
        });
    });
});
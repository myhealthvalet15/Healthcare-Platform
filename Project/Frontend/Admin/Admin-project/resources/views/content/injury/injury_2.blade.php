<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div class="container" style="display: flex; flex-direction: column; gap: 30px; align-items: center;">
    <!-- Injury List Table -->
    <div class="col-12 mb-4 text-end">
        <button id="showFormBtn" class="btn btn-primary px-4 py-2 btn-shadow frombtnn">
            <i class="fas fa-plus-circle me-2"></i> Add Nature of injury
        </button>
    </div>

    <!-- Left Table - Add Injury Form (Initially hidden) -->
    <div id="addInjuryForm" style="display: none; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: #fff; width: 100%; max-width: 500px; padding: 30px;">
        <table class="table table-striped table-nomargin table-mail shownbtnn">
            
        <tbody>
    <tr>
        <td style="display: flex; flex-direction: row; align-items: center;">
            <!-- Input Fields -->
            <input type="text" class="typ form-control" name="name" placeholder="Enter Injury name" style="margin-right: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">

            <input type="hidden" class="cat" name="cat" value="2">
            <input type="hidden" class="active_status_id" name="active_status_id" value="1">
            
            <!-- Add Button -->
            <input type="button" class="btnsysadd" value="Add" style="
                background-color: #007bff; 
                color: white; 
                border: none; 
                border-radius: 4px; 
                padding: 10px 15px; 
                cursor: pointer; 
                transition: background-color 0.3s;">
        </td>
    </tr>
</tbody>

        </table>
    </div>

    <!-- Injury List Table -->
    <table class="table table-striped table-nomargin table-mail" style="box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); border-radius: 8px; background-color: #fff; width: 100%; max-width: 900px;">
        <thead>
            <tr>
                <th class="sys text-center" style="width: 10%;">#</th>
                <th class="sys" style="width: 50%; text-align: left; font-weight: bold; color: #333;">Injury Type</th>
                <th class="sys text-center" style="width: 20%; font-weight: bold; color: #333;">Type</th>
                <th class="sys text-center" style="width: 20%; font-weight: bold; color: #333;">Edit</th>
            </tr>
        </thead>
        <tbody id="injury-list" class="inj">
            @foreach ($injuries as $index => $injury)
            <tr class="uniqueinjury" style="transition: background-color 0.3s; cursor: pointer;"
                onmouseover="this.style.backgroundColor='#f2f2f2'"
                onmouseout="this.style.backgroundColor='transparent'">
                <td class="text-center" style="padding: 15px;">{{ $index + 1 }}</td>
                <td class="hid" style="text-align: left; padding: 10px;">
                    <input type="text" class="mdinjury form-control" value="{{ $injury['op_component_name'] }}"
                        style="padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                </td>
                <td class="text-center" style="padding: 10px;">
                    <input type="text" class="sta form-control"
                        style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                        value="{{ $injury['op_component_type'] }}" readonly>
                </td>

                <td class="text-center" style="padding: 10px;">
                    <div class="btn-group"
                        style="display: flex; justify-content: center; align-items: center; gap: 10px;">

                        <!-- Hidden Inputs -->
                        <input type="hidden" value="{{ $injury['op_component_id'] }}" class="op_component_id">
                        <input type="hidden" value="{{ $injury['op_component_type'] }}" class="op_component_type">
                        <input type="hidden" value="1" class="active_status">

                        <!-- Updated 'Update' button with hover effects -->
                        <input type="button" class="btnupsys btn-success" value="Update" style="padding: 10px 20px; background-color: #28a745; color: white; border: none;
                      border-radius: 6px; font-size: 14px; cursor: pointer; transition: background-color 0.3s, 
                      transform 0.3s; width: 100%; max-width: 150px; text-align: center; display: inline-flex; 
                      justify-content: center; align-items: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

                        <!-- Button Hover Effects -->
                        <style>
                        .btnupsys:hover {
                            background-color: #218838;
                            transform: translateY(-2px);
                        }

                        .btnupsys:focus {
                            outline: none;
                            box-shadow: 0 0 5px rgba(40, 167, 69, 0.7);
                        }
                        </style>

                        <!-- Delete Form with updated icon and button style -->
                        <form action="{{ route('injurydelete', $injury['op_component_id']) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 10px 20px; border-radius: 6px; border: none; background-color: #dc3545; 
                    color: white; font-size: 16px; cursor: pointer; transition: background-color 0.3s, transform 0.3s;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

                                <i class="fas fa-trash-alt" style="font-size: 18px; margin-right: 8px;"></i>
                                <!-- Improved icon spacing -->
                                Delete
                            </button>
                        </form>
                    </div>
                </td>



            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="d-flex justify-content-center mt-3">
        {!! $injuries->links('pagination::bootstrap-5') !!}
    </div>
    
</div>


<script>
document.getElementById('showFormBtn').addEventListener('click', function() {
            var form = document.getElementById('addInjuryForm');

            // Toggle form visibility
            // form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        $(document).ready(function() {
            $('.frombtnn').click(function(e) {
                e.preventDefault();

                // Get the corresponding form container for the clicked button
                $(this).closest('.container').find('#addInjuryForm').toggle(); // Toggles visibility
            });

            $('.btnsysadd').click(function(e) {
                e.preventDefault();

                var $row = $(this).closest('tr');

                var name = $row.find('.typ').val().trim();

                var cat = $row.find('.cat').val().trim();

                var active_status_id = $row.find('.active_status_id').val().trim();



                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('injuryadd') }}',
                    data: {
                        op_component_name: name,
                        op_component_type: cat,
                        active_status: active_status_id,

                        _token: token
                    },
                    success: function(response) {
                        var messages = $('.messages');

                        var successHtml = '<div class="alert alert-success">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> ' +
                            response.message +
                            '</div>';

                        $(messages).html(successHtml);


                        console.log(response);
                        setTimeout(function() {
                            window.location.href = ''; // Replace with your target URL
                        }, 2000);


                        toastr.success(response.message, 'Success');



                    },
                    error: function(response) {
                        alert(response.error);
                        console.error(xhr);

                    }
                });
            });
        });

        $(document).ready(function() {
            $('.btnupsys').click(function(e) {

                var $row = $(this).closest('tr');

                var mdinjuryValue = $row.find('.mdinjury').val().trim();

                var injuryIdValue = $row.find('.op_component_id').val().trim();
                var op_component_type = $row.find('.op_component_type').val().trim();


                var token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('injuryupdate') }}',
                    data: {
                        op_component_name: mdinjuryValue,
                        op_component_id: injuryIdValue,
                        op_component_type: op_component_type,
                        _token: token
                    },
                    success: function(response) {

                        var messages = $('.messages');

                        var successHtml = '<div class="alert alert-success">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> ' +
                            response.message +
                            '</div>';

                        $(messages).html(successHtml);


                        console.log(response);

                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);


                        console.error(xhr);

                    }
                });
            });
        });

</script>

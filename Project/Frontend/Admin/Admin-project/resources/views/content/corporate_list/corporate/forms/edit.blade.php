@extends('layouts/layoutMaster')

@section('title', 'Assign Forms')
@section('description', 'Manage Employee Types and Active Status')
@section('content')
<div class="container">
    <h2>Assign Forms</h2>

   <form id="assignFormsForm" method="POST">
    @csrf
     <input type="hidden" name="corporate_id" value="{{ $corporate_id }}">
    <input type="hidden" name="location_id" value="{{ $location_id }}">
    <div id="form-sections">
        <!-- Checkboxes will be loaded here via AJAX -->
    </div>

    <button type="submit" class="btn btn-primary mt-3">Submit</button>
</form>
</div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const corporateId = '{{ $corporate_id }}';
    const locationId = '{{ $location_id }}';
    let assignedFormIds = [];

    // Step 1: Fetch assigned form IDs (Ajax Call 1)
    $.ajax({
        url: '{{ route("corporate.getAssignedForms", ["corporate_id" => $corporate_id, "location_id" => $location_id]) }}',
        method: 'GET',
        dataType: 'json',
        success: function (assignResponse) {
            if (assignResponse.success) {
                assignedFormIds = assignResponse.data.selectedFormIds || [];
            }

            // After fetching the assigned forms, make the second request to fetch available forms.
            fetchAvailableForms();
        },
        error: function (xhr) {
            console.error("Error loading assigned forms:", xhr.responseText);
        }
    });

    // Step 2: Fetch available forms (Ajax Call 2)
    function fetchAvailableForms() {
        $.ajax({
            url: '{{ route("corporate.module4.submodules") }}',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                let container = $('#form-sections');
                container.empty();

                let formsBySubModule = response.data.formsBySubModule || [];

                if (formsBySubModule.length === 0) {
                    container.append('<p>No forms found for module ID 4.</p>');
                    return;
                }

                let section = `<div class="mb-4 border p-3">
                    <h5>Forms</h5>
                `;

                // Render each form and check if it’s assigned
                formsBySubModule.forEach(form => {
                    const isChecked = assignedFormIds.includes(String(form.corporate_form_id)); // Type-safe match
                    section += `
                        <div class="form-check" style="margin-left:10px;">
                            <input class="form-check-input" type="checkbox" name="form_ids[]" value="${form.corporate_form_id}" id="form_${form.corporate_form_id}" ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label" for="form_${form.corporate_form_id}">${form.sub_module_name}</label>
                        </div>
                    `;
                });

                section += `</div>`;
                container.append(section);
            },
            error: function (xhr) {
                console.error("Error loading forms:", xhr.responseText);
                $('#form-sections').append('<p class="text-danger">Failed to load submodules.</p>');
            }
        });
    }

    // Form submission handling
    $('#assignFormsForm').on('submit', function(e) {
        e.preventDefault(); // Prevent form from submitting normally

        $.ajax({
            url: '{{ route("corporate.module4.assignSubmodules") }}',
            method: 'POST',
            data: $(this).serialize(), // Serialize form data
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect_url;
                    }, 2000); // 2-second delay
                } else {
                    alert('Something went wrong. Please try again.');
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                alert('An error occurred while submitting the form.');
            }
        });
    });
});

</script>

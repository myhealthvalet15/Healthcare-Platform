 $(document).ready(function() {
        $('.module-checkbox').on('change', function() {
            const moduleId = $(this).val();
            const submodulesContainer = $('#submodules-' + moduleId);
            if ($(this).is(':checked')) {
                submodulesContainer.collapse('show');
            } else {
                submodulesContainer.collapse('hide');
            }
        });
        $('.module-header').on('click', function(event) {
            if (!$(event.target).is('.module-checkbox')) {
                const targetCollapse = $(this).find('[data-bs-toggle="collapse"]').data('bs-target');
                $(targetCollapse).collapse('toggle');
            }
        });
        $('.module-checkbox:checked').each(function() {
            const moduleId = $(this).val();
            $('#submodules-' + moduleId).collapse('show');
        });
        $('#modulesForm').submit(function(event) {
            const modulesData = [];
            $('.module-checkbox:checked').each(function() {
                const moduleId = $(this).val();
                const subModuleIds = [];
                $('#submodules-' + moduleId).find('.submodule-checkbox:checked').each(function() {
                    subModuleIds.push($(this).val());
                });
                modulesData.push({
                    module_id: moduleId,
                    sub_module_ids: subModuleIds
                });
            });
        });
    });
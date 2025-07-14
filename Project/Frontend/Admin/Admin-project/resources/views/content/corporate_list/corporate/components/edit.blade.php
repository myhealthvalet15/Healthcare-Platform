@extends('layouts/layoutMaster')

@section('title', 'Components ')
@section('description', 'Manage Components ')
@section('content')
<!-- @if(isset($corporateForms))
    <div class="alert alert-success">Corporate Forms loaded: {{ count($corporateForms) }}</div>
@else
    <div class="alert alert-danger">Corporate Forms not loaded</div>
@endif -->

<div class="container row">
    <div class="d-flex justify-content-between align-items-center col-md-12 mb-3">
        <!-- Corporate Name and Title -->
        <div class="col-md-5">
        <p class="mb-2 text-muted">
            Corporate &raquo; Corporate List
        </p>
            <h3 class="text-primary mb-3">
                <strong>{{$corporate_name}}</strong>
                <p class="text-dark small">Corporate Components Details</p>
            </h3>
        </div>

        <!-- Icons for editing options -->
        <div class="col-md-7 text-end">

        <a href="{{ route('corporate.edit', $id) }}" class="btn btn-dark btn-sm"
         data-bs-toggle="tooltip" title="Edit Corporate Details">
         <i class="fas fa-building"></i>
     </a>

     <a href="{{ route('corporate.editAddress', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
     class="btn btn-info btn-sm" data-bs-toggle="tooltip"
         title="Edit corporaate address Details">
         <i class="fas fa-map-marker-alt"></i>
     </a>
    
     <a href="{{ route('corporate.editEmployeeTypes',['id' => $id, 'corporate_id' => $corporate_id]) }}"
         class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Edit Employee Types">
         <i class="fas fa-users"></i>
     </a>

     <a href="{{ route('corporate.editComponents', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
         class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="edit components Details">
         <i class="fas fa-home"></i>
     </a>
    
     <a href="{{ route('corporate.editAdminUsers', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
         class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
         title="edit Corporate super  Admin">
         <i class="fas fa-user-tie"></i>
     </a>

        </div>
    </div>
   
</div>
<div id="form-step-4" class="step-form-4">

    <form id="modulesForm" class="p-4 rounded shadow-sm" method="post" action="{{ route('corporate_updatecomponents') }}">
        @csrf

   @php
function getSelectedSubmodules($components, $moduleId) {
    return collect($components)
        ->where('module_id', $moduleId)
        ->flatMap(function ($component) {
            return collect($component['submodules'] ?? [])->pluck('sub_module_id');
        })
        ->map(fn($id) => (string) $id) // Ensure all IDs are strings
        ->all();
}
@endphp


        
        @foreach($modules as $module)
            @php
                $isModuleSelected = collect($components)->contains('module_id', $module['module_id']);
                $selectedSubmoduleIds = getSelectedSubmodules($components, $module['module_id'], $corporateForms);
//print_r($selectedSubmoduleIds);
            @endphp
            
            <div class="module mb-3">
                <div class="module-header p-3 d-flex justify-content-start align-items-center">
                    <input type="checkbox"
                           class="module-checkbox me-2"
                           name="module_id[]"
                           value="{{ $module['module_id'] }}"
                           {{ $isModuleSelected ? 'checked' : '' }}>
                    <strong>{{ $module['module_name'] }}</strong>

                    @if(!empty($module['sub_modules']))
                        <span class="toggle-icon cursor-pointer ms-3"
                              data-bs-toggle="collapse"
                              data-bs-target="#submodules-{{ $module['module_id'] }}"
                              aria-expanded="{{ $isModuleSelected ? 'true' : 'false' }}"
                              aria-controls="submodules-{{ $module['module_id'] }}">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    @endif
                </div>

                @if(!empty($module['sub_modules']))
                    <div class="submodules-container mt-3">
                        <div class="submodules collapse p-3 {{ $isModuleSelected ? 'show' : '' }}" id="submodules-{{ $module['module_id'] }}">
                           @php
$currentSubmodules = $module['sub_modules'] ?? [];
@endphp
                       
                            @foreach($currentSubmodules as $subModule)
@php
    $subModuleIdStr = (string) $subModule['sub_module_id'];
    $isSubmoduleSelected = in_array($subModuleIdStr, $selectedSubmoduleIds);
@endphp

    <div class="submodule-container mb-3 p-2 border rounded shadow-sm">
        <div class="form-check mb-2">
            <input type="checkbox"
                   class="form-check-input submodule-checkbox"
                   name="sub_module_id[{{ $module['module_id'] }}][]"
                   value="{{ $subModule['sub_module_id'] }}"
                   id="submodule-{{ $subModule['sub_module_id'] }}"
                   {{ $isSubmoduleSelected ? 'checked' : '' }}>
            <label class="form-check-label" for="submodule-{{ $subModule['sub_module_id'] }}">
                {{ $subModule['sub_module_name'] }}
            </label>
        </div>
    </div>
@endforeach


                        </div>
                    </div>
                 @else
        <!-- Send empty array for modules without submodules -->
        <input type="hidden" name="sub_module_id[{{ $module['module_id'] }}][]" value="">
    @endif
            </div>
        @endforeach

        <div class="text-center">
            <button type="submit" id="submit" class="btn btn-primary btn-lg rounded-pill px-4">Submit</button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Toggle submodules visibility on module checkbox change
       $('.module-checkbox').on('change', function () {
    const moduleId = $(this).val();
    const submodulesContainer = $('#submodules-' + moduleId);

    if ($(this).is(':checked')) {
        submodulesContainer.collapse('show');
    } else {
        submodulesContainer.collapse('hide');
        // Removed: .find('.submodule-checkbox').prop('checked', false);
    }
});

        // Toggle collapse on module header click, excluding the checkbox itself
        $('.module-header').on('click', function (event) {
            if (!$(event.target).is('.module-checkbox')) {
                const targetCollapse = $(this).find('[data-bs-toggle="collapse"]').data('bs-target');
                $(targetCollapse).collapse('toggle');
            }
        });

        // Ensure submodules are shown when the module is selected on page load
        $('.module-checkbox:checked').each(function () {
            const moduleId = $(this).val();
            $('#submodules-' + moduleId).collapse('show');
        });

        // Handle form submission for debugging (for testing purposes)
        $('#modulesForm').submit(function (event) {
            const modulesData = [];

            $('.module-checkbox:checked').each(function () {
                const moduleId = $(this).val();
                const subModuleIds = [];

                $('#submodules-' + moduleId).find('.submodule-checkbox:checked').each(function () {
                    subModuleIds.push($(this).val());
                });

                modulesData.push({
                    module_id: moduleId,
                    sub_module_ids: subModuleIds
                });
            });

            // Uncomment the line below to inspect collected data
            // console.log(modulesData);
        });
    });
</script>
@endpush
@endsection

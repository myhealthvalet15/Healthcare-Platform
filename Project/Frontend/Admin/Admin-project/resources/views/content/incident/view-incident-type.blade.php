@extends('layouts/layoutMaster')

@section('title', 'Incident types')

@section('content')

    <!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/assets/js/ui-modals.js', 'resources/assets/js/questions.js', 'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

<!-- Vendor Script -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

<!-- Basic Bootstrap Table -->
<div class="card">
    <div class="d-flex justify-content-between align-items-center card-header">
        <h5 class="mb-0">Incident Available</h5>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addincident">
            <i class="ti ti-plus me-1"></i> Add Incident
        </button>
        <!-- Add Modal -->
        <div class="modal fade" id="addincident" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Add Incident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-4">
                                <label for="incident-name" class="form-label">Incident Name</label>
                                <input type="text" id="incident-name" class="form-control" placeholder="Enter Name">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="add-new-incident">Save
                            Changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->
        <!-- Edit Modal -->
        <div class="modal fade" id="editincident" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Incident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-4">
                                <label for="incident-name" class="form-label">Incident Name</label>
                                <input type="text" id="incident_name" class="form-control" placeholder="Edit Name">
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="emailBasic" class="form-label">Status</label>
                                <div class="demo-vertical-spacing">
                                    <label class="switch">
                                        <input type="checkbox" class="switch-input" id="status_switch_edit"
                                            checked="true">
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label" id="status-label-edit">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="edit-incident">Save
                            Changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Ends -->
    </div>
    <div class="table-responsive text-nowrap">
       <div id="preloader" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
         <p>Fetching Incident type...</p> 
        </div>

        <table class="table" id="incidenttable">
            <thead>
                <tr>
                    <th>SNO</th>
                    <th>Incident Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
   <tbody>
        @foreach ($incident as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item['incident_type_name'] }}</td>
                <td>
    <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $item['incident_type_id'] }}" data-name="{{ $item['incident_type_name'] }}">
        Edit
    </button>
    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $item['incident_type_id'] }}">
        Delete
    </button>
</td>
            </tr>
        @endforeach
    </tbody>
    </table>
    </div>
</div>

<hr class="my-12">
<script src="/lib/js/page-scripts/show-incident.js"></script>

</script>
@endsection

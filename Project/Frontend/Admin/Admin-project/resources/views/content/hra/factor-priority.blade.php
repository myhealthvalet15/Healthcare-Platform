@extends('layouts/layoutMaster')

@section('title', 'HRA Factor Priority')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/toastr/toastr.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app-ecommerce-category-list.js', 'resources/assets/js/extended-ui-sweetalert2.js', 'resources/assets/js/form-basic-inputs.js', 'resources/assets/js/ui-toasts.js'])
@endsection

@section('content')
    <style>
        .factor-row {
            transition: all 0.3s ease;
        }

        .factor-row:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .priority-badge {
            min-width: 30px;
            display: inline-block;
            text-align: center;
            margin-right: 10px;
        }
    </style>

    <div class="row justify-content-center">
        <div id="preloader" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Fetching Datas...</p>
        </div>
        <div class="col-md-10" id="contents-container" style="display: none;">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">HRA Factors Priority</h5>
                    <div class="ms-auto">
                        <button id="view-questions-button" class="btn btn-secondary">
                            <i class="bx bx-save me-1"></i> View Questions
                        </button>
                        <button id="save-changes-button" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="templates" class="form-select form-select-lg">
                                    <option value>Select a Template</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="factors-dropdown-container">
                            <div class="form-floating">
                                <select id="factors" class="form-select form-select-lg">
                                    <option value>Select a Factor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card border shadow-none">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Existing Factors</h6>
                        </div>
                        <div class="card-body p-0">
                            <div id="existing-factors-container" class="list-group list-group-flush">
                                <div id="no-factors-message" class="list-group-item text-center text-muted py-3">
                                    No factors selected. Choose a template and add factors.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"
        integrity="sha512-csIng5zcB+XpulRUa+ev1zKo7zRNGpEaVfNB9On1no9KYTEY/rLGAEEpvgdw6nim1WdTuihZY1eqZ31K7/fZjw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/lib/js/page-scripts/factor-priority.js"></script>
@endsection

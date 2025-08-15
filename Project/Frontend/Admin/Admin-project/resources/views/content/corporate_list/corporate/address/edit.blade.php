@extends('layouts/layoutMaster')
@section('title', 'Edit Corporate Address')
@section('description', 'Description of my dashboard')
@section('content')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js',
'resources/assets/js/form-wizard-validation.js'
])
@endsection
<div class="container">
    <div class="container row">
        <div class="d-flex justify-content-between align-items-center col-md-12 mb-3">
            <div class="col-md-5">
                <p class="mb-2 text-muted">
                    Corporate &raquo; Corporate List
                </p>
                <h3 class="text-primary mb-3">
                    <strong>{{ $corporate_name['corporate_name'] }}</strong>
                    <p class="text-dark small">Corporate Address Details</p>
                </h3>
            </div>
            <div class="col-md-7 text-end">
                <a href="{{ route('corporate.edit', ['id' => $id]) }}"
                    class="btn btn-dark btn-sm me-2" data-bs-toggle="tooltip"
                    title="Edit Corporate Details">
                    <i class="fas fa-building"></i>
                </a>
                <a href="{{ route('corporate.editAddress', ['id' => $id, 'corporate_id' => $corporate_id]) }}"
                    class="btn btn-info btn-sm me-2" data-bs-toggle="tooltip"
                    title="Edit Corporate Address Details">
                    <i class="fas fa-map-marker-alt"></i>
                </a>
                @if($corporate_address['corporate_id'] == $corporate_address['location_id'])
                <a href="{{ route('corporate.editEmployeeTypes',['id' => $id, 'corporate_id' => $corporate_id]) }}" class="btn btn-primary btn-sm"
                    data-bs-toggle="tooltip" title="Edit Employee Types">
                    <i class="fas fa-users"></i>
                </a>
                <a href="{{ route('corporate.editComponents', ['id' => $id, 'corporate_id' => $corporate_id]) }}" class="btn btn-success btn-sm"
                    data-bs-toggle="tooltip" title="edit components Details">
                    <i class="fas fa-home"></i>
                </a>
                @endif
                <a href="{{ route('corporate.editAdminUsers',['id' => $id, 'corporate_id' => $corporate_id]) }}"
                    class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="edit Corporate super  Admin">
                    <i class="fas fa-user-tie"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <div class="card shadow rounded-lg border-0" style="max-width: 800px; width: 100%;">
            <div class="card-body p-4">
                <div id="form-step-2" class="step-form">
                    <form id="step-2-form" method="post"
                        action="{{ route('corporate.updateaddress', ['id' => $corporate_address['id']]) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="formValidationCountry">Pincode</label>
                                <select class="select2 form-control border rounded" id="formValidationCountry"
                                    name="pincode_id">
                                    <option value="{{ $pincode['address_id'] }}">
                                        {{ $pincode['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="areaoptions">Area</label>
                                <select class="select2 form-control border rounded" id="areaoptions" name="area_id">
                                    <option value="{{ $area['address_id'] }}">
                                        {{ $area['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="city">City</label>
                                <select class="select2 form-control border rounded" id="city" name="city_id">
                                    <option value="{{ $city['address_id'] }}">
                                        {{ $city['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="state">State</label>
                                <select class="select2 form-control border rounded" id="state" name="state_id">
                                    <option value="{{ $state['address_id'] }}">
                                        {{ $state['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="country">Country</label>
                                <select class="select2 form-control border rounded" id="country" name="country_id">
                                    <option value="{{ $country['address_id'] }}">
                                        {{ $country['address_name'] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" id="latitude" value="{{$corporate_address['latitude']}}"
                                    name="latitude" class="form-control border rounded shadow-sm"
                                    placeholder="Latitude">
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" id="longitude" value="{{$corporate_address['longitude']}}"
                                    name="longitude" class="form-control border rounded shadow-sm"
                                    placeholder="Longitude">
                            </div>
                            <div class="col-md-6">
                                <label for="website_link" class="form-label">Website Link</label>
                                <input type="text" id="website_link" name="website_link"
                                    value="{{$corporate_address['website_link']}}"
                                    class="form-control border rounded shadow-sm" placeholder="Website Link">
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" id="submit-form"
                                    class="btn btn-success rounded-pill shadow-sm px-4 py-2 w-50">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/edit.js"></script>
@endsection
@extends('layouts/layoutMaster')

@section('title', 'Add New Invoice - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss','resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->


@section('content')
<div class="col-12 mb-6">
  <div id="wizard-validation" class="bs-stepper mt-2">


    <div class="bs-stepper-content">
      <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
        <button type="button" class="btn btn-primary" id="back-to-list" onclick="window.location.href='/others/invoice'"
          style="margin-right: 20px;">Back to Invoice</button>
      </div>
      <form id="wizard-validation-form" method="post">



        <div class="content">

          <div class="col-xl mb-6">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Basic with Icons</h5>
                <small class="text-muted float-end">Merged input group</small>
              </div>
              <div class="card-body">
                <form>
                  <div class="mb-6">
                    <label class="form-label" for="basic-icon-default-fullname">Full Name</label>
                    <div class="input-group input-group-merge">
                      <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                      <input type="text" class="form-control" id="basic-icon-default-fullname" placeholder="John Doe"
                        aria-label="John Doe" aria-describedby="basic-icon-default-fullname2" />
                    </div>
                  </div>
                  <div class="mb-6">
                    <label class="form-label" for="basic-icon-default-company">Company</label>
                    <div class="input-group input-group-merge">
                      <span id="basic-icon-default-company2" class="input-group-text"><i
                          class="ti ti-building"></i></span>
                      <input type="text" id="basic-icon-default-company" class="form-control" placeholder="ACME Inc."
                        aria-label="ACME Inc." aria-describedby="basic-icon-default-company2" />
                    </div>
                  </div>
                  <div class="mb-6">
                    <label class="form-label" for="basic-icon-default-email">Email</label>
                    <div class="input-group input-group-merge">
                      <span class="input-group-text"><i class="ti ti-mail"></i></span>
                      <input type="text" id="basic-icon-default-email" class="form-control" placeholder="john.doe"
                        aria-label="john.doe" aria-describedby="basic-icon-default-email2" />
                      <span id="basic-icon-default-email2" class="input-group-text">@example.com</span>
                    </div>
                    <div class="form-text"> You can use letters, numbers & periods </div>
                  </div>
                  <div class="mb-6">
                    <label class="form-label" for="basic-icon-default-phone">Phone No</label>
                    <div class="input-group input-group-merge">
                      <span id="basic-icon-default-phone2" class="input-group-text"><i class="ti ti-phone"></i></span>
                      <input type="text" id="basic-icon-default-phone" class="form-control phone-mask"
                        placeholder="658 799 8941" aria-label="658 799 8941"
                        aria-describedby="basic-icon-default-phone2" />
                    </div>
                  </div>
                  <div class="mb-6">
                    <label class="form-label" for="basic-icon-default-message">Message</label>
                    <div class="input-group input-group-merge">
                      <span id="basic-icon-default-message2" class="input-group-text"><i
                          class="ti ti-message-dots"></i></span>
                      <textarea id="basic-icon-default-message" class="form-control"
                        placeholder="Hi, Do you have a moment to talk Joe?"
                        aria-label="Hi, Do you have a moment to talk Joe?"
                        aria-describedby="basic-icon-default-message2"></textarea>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Send</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <br /><br />
        <!-- Submit Button for Both Forms -->
        <div class="col-sm-6">
          <button type="submit" class="btn btn-primary" id="add_invoice">Save</button>
          <button type="reset" class="btn btn-label-danger waves-effect"
            onclick="window.location.href='/others/invoice'">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
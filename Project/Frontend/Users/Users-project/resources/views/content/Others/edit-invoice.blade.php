@extends('layouts/layoutMaster')

@section('title', 'Edit Invoice - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->


@section('content')
    <div class="col-12 mb-6">
        <div id="wizardb-validation" class="bs-stepper mt-2">


            <div class="bs-stepper-content">
                <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                    <button type="button" class="btn btn-primary" id="back-to-list"
                        onclick="window.location.href='/others/invoice'" style="margin-right: 20px;">Back to Invoice</button>
                </div>
                <form id="wizard-validation-form">
                    <div class="row g-6">
                        <div class="col-sm-6">
                            <label for="invoice_type" class="form-label">
                                <h5>Invoice Type</h5>
                            </label>
                            <div style="color: #4444e5;">
                                <input type="radio" id="cash_invoice" name="invoice_type" value="cash"
                                    style="color:blue;" checked>&nbsp; Cash Invoice&nbsp;
                                <input type="radio" id="po_invoice" name="invoice_type" value="po">&nbsp;PO Invoice
                            </div>
                        </div>
                    </div>

                    <br />


                    <h5>Invoice Details</h5>
                    <!-- PO Invoice Fields (Default) -->
                    <div id="po_invoice_form" class="content">

                        <div class="row g-6">
                            <div class="col-sm-6">
                                <strong>Vendor : </strong> <span id="vendor_name_edit"></span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Po Number : </strong> <span id="ponumber_edit"></span>
                            </div>

                            <div class="col-sm-6">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" id="invoice_date" name="invoice_date" class="form-control" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" id="invoice_number" name="invoice_number" class="form-control"
                                    placeholder="Enter Invoice Number" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="invoice_amount" class="form-label">Amount</label>
                                <input type="number" id="invoice_amount" name="invoice_amount" class="form-control"
                                    placeholder="Enter Amount" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="entry_date" class="form-label">Entry Date</label>
                                <input type="date" id="entry_date" name="entry_date" class="form-control">
                            </div>
                        </div>
                        <br />
                        <h5>Invoice Process</h5>
                        <div class="row g-6">
                            <div class="col-sm-6">
                                <label for="ohc_verify_date" class="form-label">OHC Verification Date</label>
                                <input type="date" id="ohc_verify_date" name="  " class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="hr_verify_date" class="form-label">HR Verification Date</label>
                                <input type="date" id="hr_verify_date" name="hr_verify_date" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="ses_number" class="form-label">SES Number</label>
                                <input type="text" id="ses_number" name="ses_number" class="form-control"
                                    placeholder="Enter SES Number">
                            </div>
                            <div class="col-sm-6">
                                <label for="ses_date" class="form-label">SES Date</label>
                                <input type="date" id="ses_date" name="ses_date" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="head_verify_date" class="form-label">Dept.Head Verification Date</label>
                                <input type="date" id="head_verify_date" name="head_verify_date" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="ses_release_date" class="form-label">SES Released Date</label>
                                <input type="date" id="ses_release_date" name="ses_release_date"
                                    class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="submission_date" class="form-label">Bill Submission Date</label>
                                <input type="date" id="submission_date" name="submission_date" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="payment_date" class="form-label">Payment Advance Date</label>
                                <input type="date" id="payment_date" name="payment_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Cash Invoice Fields -->
                    <div id="cash_invoice_form" class="content" style="display:none;">
                        <div class="row g-6">
                            <div class="col-sm-6">
                                <label for="cash_invoice_date" class="form-label">Cash Invoice Date</label>
                                <input type="date" id="cash_invoice_date" name="cash_invoice_date"
                                    class="form-control" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="cash_invoice_number" class="form-label">Cash Invoice Number</label>
                                <input type="text" id="cash_invoice_number" name="cash_invoice_number"
                                    class="form-control" placeholder="Enter Invoice Number" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="cash_amount" class="form-label">Amount</label>
                                <input type="number" id="cash_amount" name="cash_amount" class="form-control"
                                    placeholder="Enter Amount" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="cash_entry_date" class="form-label">Entry Date</label>
                                <input type="date" id="cash_entry_date" name="cash_entry_date" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <label for="cash_vendor" class="form-label">Cash Invoice Vendor</label>
                                <input type="text" id="cash_vendor" name="cash_vendor" class="form-control"
                                    placeholder="Enter Cash Vendor" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="cash_invoice_details" class="form-label">Cash Invoice Details</label>
                                <input type="text" id="cash_invoice_details" name="cash_invoice_details"
                                    class="form-control" placeholder="Enter Description">
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


    <script src="/lib/js/page-scripts/edit-invoice.js"></script>
@endsection

@extends('layouts/layoutMaster')

@section('title', 'Prescription Template - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="col-12 mb-6">
    <div id="wizard-validation" class="bs-stepper mt-2">       
        <div class="bs-stepper-content">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn btn-primary" id="back-to-list" onclick="window.location.href='/prescription/prescription-template'" style="margin-right: 20px;">Back to List</button>
            </div>
            <!-- Form for Template Name -->
            <div class="template-name-field" style="padding: 10px 0;">
                <label for="template-name" >Template Name:</label>
                <input type="text" id="template-name" name="template-name" class="form-control" placeholder="Enter Template Name" style="width: 100%; max-width: 308px;">
            </div>

            <!-- Prescription Input Section using div layout -->
            <div class="prescription-inputs" style="display: flex; flex-direction: column; gap: 15px; padding: 5px 0; border-bottom: 1px #333 solid; font-weight: bold;">
                <div class="prescription-header" style="display: flex; justify-content: space-between;">
                    <div style="width: 35%;">Drug Name - Type - Strength</div>
                    <div style="width: 5%;">Days</div>
                    <div style="width: 30%;">
                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/morning.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/noon.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/evening.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/night.png">
                            </div>
                    </div>
                    <div style="width: 15%;">AF/BF</div>
                    <div style="width: 15%;">Remarks</div>
                   
                </div>

                <!-- First Prescription Row -->
                <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
    <!-- Drug Name Input -->
    <input type="hidden" name="rowid[]" id="rowid" value="0">
    <div style="width: 35%;">      
        <div class="drug_name" title="drug_name">           
          <select class="hiddendrugname select2" name="drugname[]" id="drug_template_0" style="height:25px;width:85%;">    
                <option value="">Select a Drug</option>
                            <!-- Add drug options dynamically -->
            </select>
        </div>
    </div>

    <!-- Days Input -->
    <div style="width: 5%;">
        <input type="text" class="form-control" maxlength="3" name="duration[]" id="duration" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
    </div>

    <!-- Morning, Noon, Evening, Night Inputs -->
    <div style="width: 30%;margin-left:20px;">
        <div style="float:left;width: 60px;">
            <input type="text" maxlength="2" name="morning[]" class="morning input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
        </div>
        <div style="float:left;width: 60px;">
            <input type="text" maxlength="2" name="afternoon[]" class="afternoon input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
        </div>
        <div style="float:left;width: 60px;">
            <input type="text" maxlength="2" name="evening[]" class="evening input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
        </div>
        <div style="float:left;width: 60px;">
            <input type="text" maxlength="2" name="night[]" class="night input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
        </div>
    </div>

    <!-- AF/BF Select -->
    <div style="width: 15%;text-align:center;">
        <select name="drugintakecondition[]" class="form-select">
            <option value="">-Select-</option>
            <option value="1">Before Food</option>
            <option value="2">After Food</option>
            <option value="3">With Food</option>
            <option value="4">SOS</option>
            <option value="5">Stat</option>
        </select>
    </div>

    <!-- Remarks Input -->
    <div style="width: 15%;">
        <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;">
    </div>

    <!-- Buttons for Add/Remove Rows -->
    <div style="width: 5%; text-align: center;">
        <div style="cursor: pointer;" class="margin-t-8 addjs" onclick="addRow1()">
            <i class="fa-sharp fa-solid fa-square-plus"></i> <!-- Only plus in the first row -->
        </div>
    </div>
</div>

            </div>
        </div>
        <div class="col-12 mt-3 text-end" style="margin-left:-20px;">
    <button type="button" id="submitBtn" class="btn btn-primary">Submit Prescription</button>
</div><br/>
    </div>
</div>
<script src="/lib/js/page-scripts/prescription-template-add.js?v=time()"></script>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


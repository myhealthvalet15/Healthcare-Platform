@extends('layouts/layoutMaster')

@section('title', 'MHC Menu Rights')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@section('content')
<!-- Validation Wizard -->
<div class="col-12 mb-6">

    <div id="wizard-validation" class="bs-stepper mt-2">

        <div class="bs-stepper-content">

            <form id="wizard-validation-form" method="post">
                <!-- Account Details -->
                <div id="account-details-validation" class="content" style="display:block;">
                    <div style=" justify-content: flex-end; margin-bottom: 10px;">
                        <button type="button" class="btn btn-primary" id="back-to-list"
                            onclick="window.location.href='/corporate-users/users-list'"
                            style="margin-right: 20px;">Back to User
                            List</button>
                    </div>
                    <div class="row g-4">
                    <div class="col-md-4">
                        <input type="hidden" name="corporate_user_id" id="corporate_user_id"  class="form-check-input" type="text" value="{{$corporateuser['id']}}">
                        <input type="hidden" name="corporate_menu_rights_id" id="corporate_menu_rights_id" class="form-check-input" type="text" value="">
                            <p style="">{{$corporateuser['first_name']}} {{$corporateuser['last_name']}}</p>
                            <p style="">{{$corporateuser['email']}}</p>
                            <p style="">{{$corporateuser['mobile_country_code']}} {{$corporateuser['mobile_num']}}</p>
                            <label>Department:</label><br>
                            {{ implode(', ', $corporateuser_dept) }}
                          

                        </div>
                    
                    
                    <div class="col-md-8">
                    <div class="row g-12"> 
                    <div class="col-md-6"> 
                        <h5>MENU</h5> 
                     </div>
                     <div class="col-md-2"> 
                        <h5>HIDE</h5> 
                     </div>
                     <div class="col-md-2"> 
                        <h5>VIEW</h5> 
                     </div>
                     <div class="col-md-2"> 
                        <h5>EDIT</h5> 
                     </div>
                    </div>
                    <div class="row g-12"> 
                    <div class="col-md-6"> 
                        <label>Employees</label> 
                     </div>
                     <div class="col-md-2"> 
                     <input name="employees" class="form-check-input" type="radio" value="0" 
                     />
                     </div>
                     <div class="col-md-2"> 
                     <input name="employees" class="form-check-input" type="radio" value="1"
                      />
                     </div>
                     <div class="col-md-2"> 
                     <input name="employees" class="form-check-input" type="radio" value="2"
                      /> 
                     </div>
                    </div>
                    <hr style="border-top: 2px dashed #ccc; margin-bottom: 20px; width: 100%;">

                    <div  id="data-menu"> 

                    </div>
                    <div class="row g-12"> 
                    <div class="col-md-6"> 
                        <label>LANDING PAGE</label> 
                     </div>
                     <div class="col-md-6">
                     <div class="row g-12"> 
                     <div class="col-md-1"> 
                     <input type="checkbox" name="landing_page[]" value="1" class="form-check-input">
                    </div>
                    <div class="col-md-8">
                     <label>Corporate Profile</label>
                    </div>
                      </div>
                      <div class="row g-12">
                      <div class="col-md-1">  
                     <input type="checkbox" name="landing_page[]" value="2" class="form-check-input">
                     </div>
                    <div class="col-md-8"> 
                     <label>Admin Dashboard</label>
                     </div>
                      </div>
                      <div class="row g-12">
                      <div class="col-md-1">   
                     <input type="checkbox" name="landing_page[]" value="3" class="form-check-input">
                    </div>
                    <div class="col-md-8"> 
                     <label>Health Index Dashboard</label>
                   </div>

                      </div>
                      <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" id="save_mhc_rights">Save </button>
                                
                                <button onclick="window.location.href='/corporate-users/users-list'"
                                    class="btn btn-label-danger waves-effect"
                                    data-bs-dismiss="offcanvas">Cancel</button>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            </div>
                     </div>
                    </div>  
                 </form>
        </div>
    </div>
</div>
<!-- /Validation Wizard -->
</div>
<script>


    $(document).ready(function () {

        // Output the URL to check if it's correct
        // console.log("Route URL: {{ route('get-departments-hl1') }}");

        // CSRF Token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');


        // Fetch department when the page loads
        $.ajax({
            url: "{{ route('get-mhc-menu',['id' => $corporateuser['id']]) }}", // The route you defined
            method: 'GET',
            success: function (response) {
             console.log(response); // Log the response to inspect
                 if (response.data) {
                    var data = response.data;
                    var data2 = response.data2;
                    
                    let html = '';
                    if (!data2 || !data2.length){
                        data.forEach(item => {
    if (item.submodules !== null && item.submodules.length > 0) {
        html += `<div class="row g-12">
                    <div class="col-md-12">
                        <h6>${item.component.module.module_name}</h6>
                    </div>
                </div>`;
        item.submodules.forEach(submodule => {
            html += `<div class="row g-12">
                        <div class="col-md-6">
                            <label>${submodule.sub_module_name}</label>
                        </div>
                        <div class="col-md-2">
                            <input name="sub_module_${submodule.id}_radio" class="form-check-input" type="radio" value="0" />
                        </div>
                        <div class="col-md-2">
                            <input name="sub_module_${submodule.id}_radio" class="form-check-input" type="radio" value="1" />
                        </div>
                        <div class="col-md-2">
                            <input name="sub_module_${submodule.id}_radio" class="form-check-input" type="radio" value="2" />
                        </div>
                    </div>`;
        });
        if (item.component.module.module_name === 'MHC') {
            html += `<div class="row g-12">
                        <div class="col-md-6">
                            <label>Employee Monitoring</label>
                        </div>
                        <div class="col-md-2">
                            <input name="employee_monitoring_radio" class="form-check-input" type="radio" value="0" />
                        </div>
                        <div class="col-md-2">
                            <input name="employee_monitoring_radio" class="form-check-input" type="radio" value="1" />
                        </div>
                    </div>
                    <div class="row g-12">
                        <div class="col-md-6">
                            <label>Reports</label>
                        </div>
                        <div class="col-md-2">
                            <input name="reports_radio" class="form-check-input" type="radio" value="0" />
                        </div>
                        <div class="col-md-2">
                            <input name="reports_radio" class="form-check-input" type="radio" value="1" />
                        </div>
                    </div>`;
        }
    } else {
        html += `<div class="row g-12">
                    <div class="col-md-6">
                        <label>${item.component.module.module_name}</label>
                    </div>
                    <div class="col-md-2">
                        <input name="module_${item.component.module.id}_radio" class="form-check-input" type="radio" value="0" />
                    </div>
                    <div class="col-md-2">
                        <input name="module_${item.component.module.id}_radio" class="form-check-input" type="radio" value="1" />
                    </div>
                    <div class="col-md-2">
                        <input name="module_${item.component.module.id}_radio" class="form-check-input" type="radio" value="2" />
                    </div>
                </div>`;
    }
    html += `<hr style="border-top: 2px dashed #ccc; margin-bottom: 20px; width: 100%;">`;
});


    document.getElementById('data-menu').innerHTML = html;
    $('#save_mhc_rights').attr('id', 'save_mhc_rights').text('Save');
                    }   
                else{
                    data.forEach(item => {
    if (item.submodules !== null && item.submodules.length > 0) {
        html += `<div class="row g-12">
                    <div class="col-md-12">
                        <h6>${item.component.module.module_name}</h6>
                    </div>
                </div>`;
        item.submodules.forEach(submodule => {
            let check;
            if (submodule.sub_module_name === 'Diagnostic Assessment') {
                check = data2[0].diagnostic_assessment;
            } else if (submodule.sub_module_name === 'Health Risk Assessment') {
                check = data2[0].hra;
            } else if (submodule.sub_module_name === 'Events') {
                check = data2[0].events;
            }
            html += `<div class="row g-12">
                        <div class="col-md-6">
                            <label>${submodule.sub_module_name}</label>
                        </div>
                        <div class="col-md-2">
                            <input name="sub_module_${submodule.id}_radio" class="form-check-input" type="radio" value="0" ${check == 0 ? 'checked' : ''} />
                        </div>
                        <div class="col-md-2">
                            <input name="sub_module_${submodule.id}_radio" class="form-check-input" type="radio" value="1" ${check == 1 ? 'checked' : ''} />
                        </div>
                        <div class="col-md-2">
                            <input name="sub_module_${submodule.id}_radio" class="form-check-input" type="radio" value="2" ${check == 2 ? 'checked' : ''} />
                        </div>
                    </div>`;
        });
        if (item.component.module.module_name === 'MHC') {
            html += `<div class="row g-12">
                        <div class="col-md-6">
                            <label>Employee Monitoring</label>
                        </div>
                        <div class="col-md-2">
                            <input name="employee_monitoring_radio" class="form-check-input" type="radio" value="0" ${data2[0].employee_monitoring == 0 ? 'checked' : ''} />
                        </div>
                        <div class="col-md-2">
                            <input name="employee_monitoring_radio" class="form-check-input" type="radio" value="1" ${data2[0].employee_monitoring == 1 ? 'checked' : ''} />
                        </div>
                    </div>
                    <div class="row g-12">
                        <div class="col-md-6">
                            <label>Reports</label>
                        </div>
                        <div class="col-md-2">
                            <input name="reports_radio" class="form-check-input" type="radio" value="0" ${data2[0].reports == 0 ? 'checked' : ''} />
                        </div>
                        <div class="col-md-2">
                            <input name="reports_radio" class="form-check-input" type="radio" value="1" ${data2[0].reports == 1 ? 'checked' : ''} />
                        </div>
                    </div>`;
        }
    } else {
        let checkmodule;
        if (item.component.module.module_name === 'Pre-Employment') {
            checkmodule = data2[0].pre_employment;
        } else if (item.component.module.module_name === 'Health Partners') {
            checkmodule = data2[0].health_partner;
        }
        html += `<div class="row g-12">
                    <div class="col-md-6">
                        <label>${item.component.module.module_name}</label>
                    </div>
                    <div class="col-md-2">
                        <input name="module_${item.component.module.id}_radio" class="form-check-input" type="radio" value="0" ${checkmodule == 0 ? 'checked' : ''} />
                    </div>
                    <div class="col-md-2">
                        <input name="module_${item.component.module.id}_radio" class="form-check-input" type="radio" value="1" ${checkmodule == 1 ? 'checked' : ''} />
                    </div>
                    <div class="col-md-2">
                        <input name="module_${item.component.module.id}_radio" class="form-check-input" type="radio" value="2" ${checkmodule == 2 ? 'checked' : ''} />
                    </div>
                </div>`;
    }
    html += `<hr style="border-top: 2px dashed #ccc; margin-bottom: 20px; width: 100%;">`;
});


    document.getElementById('data-menu').innerHTML = html;
    $('input[name="employees"][value="' + data2[0].employees + '"]').prop('checked', true);
    let landingPages = data2[0].landing_page;

landingPages.forEach(function(page) {
    $('input[name="landing_page[]"][value="' + page + '"]').prop('checked', true);
});


$('#corporate_menu_rights_id').val(data2[0].id);

$('#save_mhc_rights').attr('id', 'update_mhc_rights').text('Update');
  }
                    
                } else {
                  
                  console.error('An error occured while fetching data');
                }
            },
            error: function (xhr, status, error) {
                console.error('An error occurred: ' + error);
            }
        });
       
    });
    
    $('#save_mhc_rights').click(function () {
        
        if ($(this).text() === 'Save') {
       
        // CSRF Token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        const formData = $('form').serializeArray();

// To get the form data as an object
const formDataObject = {};
$.each(formData, function() {
    formDataObject[this.name] = this.value;
});

    
        // If all validations pass, send the form data via AJAX
      // console.log(formData);
            // AJAX request to save the data
            $.ajax({
                url: "{{ route('savemhcRights') }}", // Adjust the route if needed
                method: 'POST',
                data: formData,
                success: function (response) {
                    //console.log("response:", response.result);
                    if (response.result === true) {
                        showToast("success", "Data saved successfully!");
                        window.location.href = 'https://login-users.hygeiaes.com/corporate-users/users-list'; // Adjust the URL as needed
                    } else {
                        alert('An error occurred while saving the datas.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('An error occurred: ' + error);
                    alert('An error occurred while saving the data.');
                }
            });
        }else{
          
       // CSRF Token
       var csrfToken = $('meta[name="csrf-token"]').attr('content');
       const formData = $('form').serializeArray();

// To get the form data as an object
const formDataObject = {};
$.each(formData, function() {
   formDataObject[this.name] = this.value;
});

   
       // If all validations pass, send the form data via AJAX
     // console.log(formData);
           // AJAX request to save the data
           $.ajax({
               url: "{{ route('updatemhcRights') }}", // Adjust the route if needed
               method: 'POST',
               data: formData,
               success: function (response) {
                   console.log("response:", response.result);
                   if (response.result === true) {
                       showToast("success", "Data Updated successfully!");
                       window.location.href = 'https://login-users.hygeiaes.com/corporate-users/users-list'; // Adjust the URL as needed
                   } else {
                       alert('An error occurred while saving the datas.');
                   }
               },
               error: function (xhr, status, error) {
                   console.error('An error occurred: ' + error);
                   alert('An error occurred while saving the data.');
               }
           });
       
        }
    });

   
</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
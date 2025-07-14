@extends('layouts/layoutMaster')
@section('title', 'Drug - Template')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<div>
  
  <div class="violet-header">
    <span>div style .violet-header</span> 
  </div>

<div class="boxed-container">
  <table>
    <thead>
      <tr>
        <th align="left">Test Name</th>
        <th align="left">Results</th>
        <th align="left">Unit</th>
        <th align="left">Ranges</th>
      </tr>
    </thead>
    <tbody>
      <tr class="group-header">
        <td colspan="4">Clinical Chemistry - Group Name</td>
        </tr>
        <tr>
          <td class="drug-name group-test">Uric Acid</td>
          <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>mg/dl</td>
          <td>3.5 to 7.2</td>
        </tr>
        <tr>
          <td class="drug-name group-test">Glycosylated Haemoglobin (Hb A1C)</td>
          <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>%</td>
          <td>Normal	< 5.7<br>
            Pre-Diabetes	5.71 - 6.4<br>
            Diabetes	> 6.41</td>
        </tr>
        <tr>
          <td class="drug-name group-test">Urea</td>
          <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>mg/dl</td>
          <td>13 to 45</td>
        </tr>
        <tr>
          <td colspan="4" class="subgroup-header">Glucose Test</td>
        </tr>
      <tr>
        <td class="drug-name subgroup-test">Glucose Test</td>
        <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
        <td>mg/dl</td>
        <td>0.7 to 1.4</td>
      </tr>
      <tr>
        <td class="drug-name subgroup-test">Glucose - Fasting</td>
        <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
        <td>mg/dl</td>
        <td>Test</td>
      </tr>
      <tr>
        <td colspan="4" class="subgroup-header">Hormones</td>
      </tr>
      <tr>
        <td colspan="4" class="subsubgroup-header">Thyroid Profile</td>
      </tr>
      <tr>
        <td class="drug-name subsubgroup-test">Free T3</td>
        <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
        <td>nmol/L</td>
        <td>75 to 200</td>
      </tr>
      <tr>
        <td class="drug-name subsubgroup-test">Free T4</td>
        <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
        <td>pmol/l</td>
        <td>9 to 21</td>
      </tr>
      <tr>
        <td class="drug-name subsubgroup-test">TSH</td>
        <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
        <td>mu/L</td>
        <td>0.2 to 4.5</td>
      </tr>
      <tr class="group-header">
        <td colspan="4">Clinical Pathology - Group Name</td>
        </tr>
        <tr>
          <td class="drug-name group-test">Microalbuminuria</td>
          <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>mg/l<br>mg/24 hrs</td>
          <td>Spot	30 - 300<br>
            24 hrs	30 - 300</td>
        </tr>
        <tr>
          <td class="drug-name group-test">Stool Analysis - Complete</td>
          <td colspan="2"><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>
            Appearance: Brown, Soft, Well Formed, No Blood, mucus, pus, micro-organisms, no cells<br>
            pH: 7 -- 7.5<br>
            Reducing Sugars: < 0.25 gms/dl<br>
            Fat: 2 -- 7 g/ 24 litres</td>
        </tr>
        <tr>
          <td colspan="4" class="subgroup-header">Urine Analysis - Complete</td>
        </tr>
        <tr>
          <td colspan="4" class="subsubgroup-header">Physical Examination</td>
        </tr>
        <tr>
          <td class="drug-name subsubgroup-test">Urine - Specific Gravity</td>
          <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>Sp.gr</td>
          <td>1.003 to 1.04</td>
        </tr>
        <tr>
          <td class="drug-name subsubgroup-test">Urine - pH</td>
          <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          <td>pH</td>
          <td>4.6 to 8</td>
        </tr>
          <tr>
            <td class="drug-name subsubgroup-test">Urine - Quantity</td>
            <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
            <td></td>
            <td>5 - 30</td>
          </tr>
          <tr>
            <td class="drug-name subsubgroup-test">Urine - Colour</td>
            <td colspan="2">
              <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example">
                <option selected>Select Condition</option>
                <option value="Straw Yellow">Straw Yellow</option>
                <option value="Clear">Clear</option>
                <option value="Brown">Brown</option>
                <option value="Pale Yellow">Pale Yellow</option>
                <option value="Others">Others</option>
              </select>

            </td>
            <td><input type="text" class="form-control" id="floatingInput" placeholder="" aria-describedby="floatingInputHelp" /></td>
          </tr>
    </tbody>
  </table>
</div>

</div>
@endsection
<?php

namespace App\Http\Controllers\components\ohc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class health_registry extends Controller
{
    public function displayAddRegistryPage()
    {
        $headerData = 'Add Out Patient Registry';
        return view('content.components.ohc.health-registry.add-registry', ['HeaderData' => $headerData]);
    }
    public function displayListRegistryPage()
    {
        $headerData = 'List registry';
        return view('content.components.ohc.health-registry.list-registry', ['HeaderData' => $headerData]);
    }
    public function displayFollowUpAddRegistryPage($employeeId, $opRegistryId)
    {
        if (!is_numeric($opRegistryId) || !ctype_alnum($employeeId)) {
            return "Invalid Request";
        }
        $url = 'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/' . 1 .  '/' . $employeeId . "/op/" . $opRegistryId;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get($url);
        if ($response->successful()) {
            $data = $response->json();
            if (!isset($data['result']) || !$data['result']) {
                return "Invalid Request";
            }
            return view('content.components.ohc.health-registry.add-followup-registry', [
                'HeaderData' => 'Add Follow Up Registry',
                'employeeData' => $data['message']
            ]);
        }
        return "Invalid Request";
    }
    public function getAllHealthRegistry(Request $request, $employeeId = null)
    {
        if ($employeeId && !ctype_alnum($employeeId)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $corporateId = session('corporate_id');
        $locationId = session('location_id');
        $EmployeeId  = session('employee_id');
        $masterUserEmployeeId = $EmployeeId;
        if (! $corporateId || ! $locationId) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $url = 'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllHealthRegistry/'
            . $corporateId . '/' . $locationId
            . ($employeeId === null ? '' : '/' . $employeeId) . '?masterUserEmployeeId=' . urlencode($masterUserEmployeeId);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get($url);
        return $response;
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    }
    public function getDataByKeywordForAddRegistryPage($keyword, Request $request)
    {
        try {
            $corporateId = session('corporate_id');
            $locationId = session('location_id');
            if (! $corporateId || ! $locationId) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
            }
            if (! $keyword or !ctype_alnum($keyword)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getEmployeeData/' . $corporateId . '/' . $locationId . '/' . $keyword);
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function displayRegistryOutpatientPage($employee_id = null, $op_registry_id = null)
    {
        if ($op_registry_id !== null && !is_numeric($op_registry_id)) {
            return "Invalid Request";
        }
        if (!$employee_id || !ctype_alnum($employee_id)) {
            return "Invalid Request";
        }
        $url = 'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/' . 0 .  '/' . $employee_id;
        if ($op_registry_id !== null) {
            $url .= "/op/" . $op_registry_id;
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get($url);
        if ($response->successful()) {
            $data = $response->json();
            if (!isset($data['result']) || !$data['result']) {
                return "Invalid Request";
            }
            $headerData = 'Add Out Patient';
            if ($op_registry_id !== null) {
                return view('content.components.ohc.health-registry.edit-registry-outpatient', [
                    'HeaderData' => $headerData,
                    'employeeData' => $data['message']
                ]);
            }
            return view('content.components.ohc.health-registry.add-registry-outpatient', [
                'HeaderData' => $headerData,
                'employeeData' => $data['message']
            ]);
        }
        return "Invalid Request";
    }
    public function displayAddTestPage(Request $request, $employee_id = null)
    {
        $route = $request->route();
        $op_registry_id = $route->hasParameter('op_registry_id') ? $route->parameter('op_registry_id') : null;
        $prescription_id = $route->hasParameter('prescription_id') ? $route->parameter('prescription_id') : null;
        if (!$employee_id || !ctype_alnum($employee_id)) {
            return "Invalid Request";
        }
        if ($op_registry_id !== null && !is_numeric($op_registry_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if ($prescription_id !== null && !is_numeric($prescription_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Prescription ID'], 400);
        }
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        if ($op_registry_id === null) {
            $employeeResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/0/' . $employee_id);
        } else {
            $suffix = $prescription_id ? '/prescription/' . $prescription_id : '/op/' . $op_registry_id;
            $employeeResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/0/' . $employee_id . $suffix);
        }
        $prescriptionResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getOnlyPrescriptionTemplate/' . $locationId);
        $pharmacyResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPharmacyDetails/' . $locationId);
        if ($employeeResponse->successful() && $prescriptionResponse->successful() && $pharmacyResponse->successful()) {
            $employeeData = $employeeResponse->json();
            $prescriptionData = $prescriptionResponse->json();
            $pharmacyData = $pharmacyResponse->json();
            if (!isset($employeeData['result']) || !$employeeData['result'] || !$pharmacyData['result']) {
                return "Invalid Request";
            }
            $prescriptionTemplates = collect($prescriptionData['data'])->unique('prescription_template_id')->values();
            return view('content.components.ohc.health-registry.add-test', [
                'HeaderData' => 'Add Employee Prescription',
                'prescriptionTemplates' => $prescriptionTemplates,
                'pharmacyData' => $pharmacyData,
                'employeeData' => $employeeData['message'],
            ]);
        }
        return response()->json([
            'result' => false,
            'message' => 'Invalid Request'
        ]);
    }
    public function addTestForEmployeeForPrescription(Request $request, $employee_id = null, $prescription_id = null)
    {
        if ($employee_id === null || !ctype_alnum($employee_id)) {
            return "Invalid Request";
        }
        if ($prescription_id === null || !ctype_alnum($prescription_id)) {
            return "Invalid Request";
        }
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        if (!$locationId || !$corporateId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        $validatedData = $request->validate([
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'required|integer',
            'selected_datetime' => 'required|date'
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/add-test/' . $employee_id . '/prescription/' . $prescription_id, [
            'corporateId' => $corporateId,
            'locationId' => $locationId,
            'employeeId' => $employee_id,
            'selected_datetime' => $validatedData['selected_datetime'],
            'test_ids' => $validatedData['test_ids']
        ]);
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message']]);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    }
    public function addTestForEmployee(Request $request, $employee_id = null, $op_registry_id = null)
    {
        if ($employee_id === null || !ctype_alnum($employee_id)) {
            return "Invalid Request";
        }
        if ($op_registry_id && !is_numeric($op_registry_id)) {
            return "Invalid Request..";
        }
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        if (!$locationId || !$corporateId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        $validatedData = $request->validate([
            'test_ids' => 'required|array|min:1',
            'test_ids.*' => 'required|integer',
            'selected_datetime' => 'required|date'
        ]);
        if ($op_registry_id) {
            $apiUrl = "https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/add-test/" . $employee_id . '/op/' . $op_registry_id;
        } else {
            $apiUrl = "https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/add-test/" . $employee_id;
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->post($apiUrl, [
            'corporateId' => $corporateId,
            'locationId' => $locationId,
            'employeeId' => $employee_id,
            'selected_datetime' => $validatedData['selected_datetime'],
            'test_ids' => $validatedData['test_ids']
        ]);
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message']]);
        }
        return response()->json(['result' => false, 'message' => $response['message']], $response->status());
    }
    public function getAllBodyParts(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllBodyParts');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function getAllSymptoms(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllSymptoms');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function getAllDiagnosis(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllDiagnosis');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function getAllMedicalSystem(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllMedicalSystem');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function getAllNatureOfInjury(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllNatureOfInjury');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function getAllInjuryMechanism(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllInjuryMechanism');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function getMRNumber(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getMRNumber');
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function saveHealthRegistry(Request $request, $opRegistryId = null)
    {
        if ($opRegistryId !== null && !is_numeric($opRegistryId)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $validatedData = $this->validateHealthRegistry($request);
        if (! $validatedData) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validatedData], 422);
        }
        $corporateId = session('corporate_id');
        $locationId = session('location_id');
        // TODO: To Remove the hardcoded value
        $ohcId = session('ohc_id') ?? 1;
        if (! $corporateId || ! $locationId || ! $ohcId) {
            return response()->json(['message' => 'Invalid Request'], 404);
        }
        if (! $corporateId || ! $locationId) {
            return response()->json(['message' => 'Invalid Request'], 404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/addHealthRegistry', [
            'editExistingOne' => $request->editExistingOne ? 1 : 0,
            'isFollowup' => $request->isFollowup ? 1 : 0,
            'opRegistryId' => $opRegistryId,
            'corporateId' => $corporateId,
            'locationId' => $locationId,
            'movementSlip' => $request->movementSlip,
            'physiotherapy' => $request->physiotherapy,
            'fitnessCert' => $request->fitnessCert,
            'close' => $request->close ? 1 : 0,
            'firstAidBy' => $request->firstAidBy,
            'workShift' => $request->workShift,
            'referral' => $request->referral,
            'hospitalDetails' => $request->hospitalDetails,
            'reportingDateTime' => $request->reportingDateTime,
            'incidentDateTime' => $request->incidentDateTime,
            'ohcId' => $ohcId,
            'employeeId' => $request->employeeId,
            'incidentType' => $request->incidentType,
            'incidentTypeId' => $request->incidentTypeId,
            'vitalParameters' => $request->vitalParameters,
            'doctor' => $request->doctor ?? [],
            'observations' => $request->observations,
            'lostHours' => $request->lostHours,
            'industrialFields' => $request->industrialFields,
            'medicalFields' => $request->medicalFields,
        ]);  
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message'], 'op_registry_id' => $response['op_registry_id']]);
        }
        return response()->json(['result' => false, 'message' => $response['message']], $response->status());
    }
    private function validateHealthRegistry(Request $request)
    {
        $rules = [
            'employeeId' => 'required|string',
            'close' => 'required|boolean',
            'editExistingOne' => 'required|boolean',
            'isFollowup' => 'required|boolean',
            'vitalParameters' => 'required|array',
            'firstAidBy' => 'required|string',
            'workShift' => 'required|integer|in:1,2,3,4',
            'movementSlip' => 'required|boolean',
            'referral' => 'required|string|in:noOutsideReferral,OutsideReferral,ama',
            'physiotherapy' => 'required|boolean',
            'fitnessCert' => 'required|boolean',
            'reportingDateTime' => 'required|date',
            'incidentDateTime' => 'required|date',
            'vitalParameters.vpTemperature_167' => 'nullable|string',
            'vitalParameters.vpSystolic_168' => 'nullable|string',
            'vitalParameters.vpDiastolic_169' => 'nullable|string',
            'vitalParameters.vpPulseRate_170' => 'nullable|string',
            'vitalParameters.vpRespiratory_171' => 'nullable|string',
            'vitalParameters.vpSPO2_172' => 'nullable|string',
            'vitalParameters.vpRandomGlucose_173' => 'nullable|string',
            'incidentType' => 'required|string|in:industrialAccident,medicalIllness,outsideAccident',
            'incidentTypeId' => 'required|integer',
            'doctor' => 'sometimes|array',
            'doctor.doctorId' => 'nullable|string',
            'doctor.doctorName' => 'nullable|string|not_in:Select Doctor',
            'observations' => 'required|array',
            'observations.doctorNotes' => 'nullable|string',
            'observations.medicalHistory' => 'nullable|string',
            'observations.referral' => 'nullable|string',
            'observations.movementSlip' => 'nullable|boolean',
            'observations.fitnessCert' => 'nullable|boolean',
            'observations.physiotherapy' => 'nullable|boolean',
            'lostHours' => 'required|array',
            'lostHours.leaveFrom' => 'required|date',
            'lostHours.leaveUpto' => 'required|date',
            'lostHours.lostHours' => 'required|string',
            'lostHours.outTime' => 'required|date',
        ];
        if ($request->incidentType === 'industrialAccident') {
            $rules = array_merge($rules, [
                'industrialFields' => 'required|array',
                'industrialFields.injuryColor' => 'required|string',
                'industrialFields.sideOfBody' => 'required|array',
                'industrialFields.sideOfBody.left' => 'required|boolean',
                'industrialFields.sideOfBody.right' => 'required|boolean',
                'industrialFields.siteOfInjury' => 'required|array',
                'industrialFields.siteOfInjury.shopFloor' => 'required|boolean',
                'industrialFields.siteOfInjury.nonShopFloor' => 'required|boolean',
                'industrialFields.natureOfInjury' => 'nullable|array',
                'industrialFields.natureOfInjury.*' => 'nullable|string',
                'industrialFields.bodyPartIA' => 'nullable|array',
                'industrialFields.bodyPartIA.*' => 'nullable|string',
                'industrialFields.injuryMechanism' => 'nullable|array',
                'industrialFields.injuryMechanism.*' => 'nullable|string',
                'industrialFields.description' => 'nullable|string',
            ]);
        } elseif ($request->incidentType === 'outsideAccident') {
            $rules = array_merge($rules, [
                'industrialFields' => 'required|array',
                'industrialFields.injuryColor' => 'required|string',
                'industrialFields.sideOfBody' => 'required|array',
                'industrialFields.sideOfBody.left' => 'required|boolean',
                'industrialFields.sideOfBody.right' => 'required|boolean',
                'industrialFields.natureOfInjury' => 'nullable|array',
                'industrialFields.natureOfInjury.*' => 'nullable|string',
                'industrialFields.bodyPartIA' => 'nullable|array',
                'industrialFields.bodyPartIA.*' => 'nullable|string',
                'industrialFields.injuryMechanism' => 'nullable|array',
                'industrialFields.injuryMechanism.*' => 'nullable|string',
                'industrialFields.description' => 'nullable|string',
            ]);
        } elseif ($request->incidentType === 'medicalIllness') {
            $rules = array_merge($rules, [
                'medicalFields' => 'required|array',
                'medicalFields.injuryColor' => 'required|string',
                'medicalFields.bodyPart' => 'nullable|array',
                'medicalFields.bodyPart.*' => 'string',
                'medicalFields.symptoms' => 'nullable|array',
                'medicalFields.symptoms.*' => 'string',
                'medicalFields.medicalSystem' => 'nullable|array',
                'medicalFields.medicalSystem.*' => 'string',
                'medicalFields.diagnosis' => 'nullable|array',
                'medicalFields.diagnosis.*' => 'string',
            ]);
        }
        if ($request->referral === 'OutsideReferral') {
            $rules = array_merge($rules, [
                'hospitalDetails' => 'required|array',
                'hospitalDetails.hospitalName' => 'required|string',
                'hospitalDetails.vehicleType' => 'required|string|in:own,ambulance',
                'hospitalDetails.esiScheme' => 'required|boolean',
            ]);
            if ($request->hospitalDetails['vehicleType'] === 'ambulance') {
                $rules = array_merge($rules, [
                    'hospitalDetails.accompaniedBy' => 'required|string',
                    'hospitalDetails.ambulanceNumber' => 'required|string',
                    'hospitalDetails.driverName' => 'required|string',
                    'hospitalDetails.odometerIn' => 'required|string',
                    'hospitalDetails.odometerOut' => 'required|string',
                    'hospitalDetails.timeIn' => 'required|date_format:H:i',
                    'hospitalDetails.timeOut' => 'required|date_format:H:i',
                ]);
            }
        }
        return $request->validate($rules);
    }
    public function getincidentTypeColorCodes(Request $request)
    {
        try {
            $corporateId = session('corporate_id');
            $locationId = session('location_id');
            if (! $corporateId || ! $locationId) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getincidentTypeColorCodes/' . $corporateId . '/' . $locationId);
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function updateHospitalizationDetails(Request $request)
    {
        Log::info('updateHospitalizationDetails called', $request->all());
        try {
            $headerData = 'Hospitalization Details';
            $employeeId = $request->employee_id;
            $opRegistryId = $request->op_registry_id ?? null;
            $employeeApiUrl = is_null($opRegistryId)
                ? "https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/0/{$employeeId}"
                : "https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/0/{$employeeId}/op/{$opRegistryId}";
            $employeeResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get($employeeApiUrl);
            if (!$employeeResponse->successful()) {
                Log::error('Employee API failed: ' . $employeeResponse->body());
                return response('Failed to fetch employee data', 500);
            }
            $employeeData = $employeeResponse->json();
            if (!isset($employeeData['result']) || !$employeeData['result']) {
                return response('Invalid employee data received', 422);
            }
            return view('content.components.ohc.health-registry.hospitalization-details', [
                'HeaderData' => $headerData,
                'employee_id' => $employeeId,
                'op_registry_id' => $opRegistryId,
                'hospital_name' => $request->hospital_name,
                'employee_name' => $request->employee_name,
                'employee_email' => $request->employee_email,
                'employee_department' => $request->employee_department,
                'employee_dob' => $request->employee_dob,
                'employee_gender' => $request->employee_gender,
                'employee_contact' => $request->employee_contact,
                'employee_designation' => $request->employee_designation,
                'employee_corporate' => $request->employee_corporate,
                'employee_user_id' => $request->employee_user_id,
                'employee_age' => $request->employee_age,
                'employeeData' => $employeeData['message'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating hospitalization: ' . $e->getMessage());
            return response('Error rendering view: ' . $e->getMessage(), 500);
        }
    }
    public function getemployeeDetailsByEmployeeId(Request $request)
    {
        $employeeId = $request->employee_id;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getEmployeesDetailById/' . $employeeId);
            return $response;
            if ($response->successful()) {
                $employee_details = $response->json();
                return response()->json($employee_details);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function updateHospitalizationDetailsById(Request $request)
    {
        Log::info('updateHospitalizationDetailsById called', $request->all());
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|alpha_num',
            'hospital_name' => 'nullable|string',
            'doctor_name' => 'nullable|string',
            'hospital_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'description' => 'nullable|string',
            'condition' => 'nullable|array',
            'condition.*' => 'string',
            'discharge_summary' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'summary_reports.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'employee_user_id' => 'string|alpha_num',
            'op_registry_id' => 'nullable|integer'
            ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $validated = $validator->validated();
        try {
            $dischargeSummaryBase64 = null;
            if ($request->hasFile('discharge_summary')) {
                $file = $request->file('discharge_summary');
                $dischargeSummaryBase64 = 'data:' . $file->getMimeType() . ';base64,' .
                    base64_encode(file_get_contents($file->getRealPath()));
            }
            $summaryReportsBase64 = [];
            if ($request->hasFile('summary_reports')) {
                foreach ($request->file('summary_reports') as $file) {
                    $summaryReportsBase64[] = 'data:' . $file->getMimeType() . ';base64,' .
                        base64_encode(file_get_contents($file->getRealPath()));
                }
            }
            $payload = [
                'employee_id' => $validated['employee_id'],
                 'employee_user_id' => $validated['employee_user_id'],
                'op_registry_id' => $validated['op_registry_id'],
                'hospital_name' => $request->input('hospital_name'),
                'doctor_name' => $request->input('doctor_name'),
                'hospital_id' => $request->input('hospital_id'),
                'doctor_id' => $request->input('doctor_id'),
                'from_date' => $validated['from_date'],
                'to_date' => $validated['to_date'],
                'description' => $request->input('description'),
                'condition' => $request->input('condition', []),
                'discharge_summary_base64' => $dischargeSummaryBase64,
                'summary_reports_base64' => $summaryReportsBase64,
            ];
            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put(
                'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/updateHospitalizationDetailsById',
                $payload
            );
            if ($apiResponse->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => $apiResponse['message'] ?? 'Hospitalization updated successfully',
                ]);
            }
            return response()->json([
                'result' => false,
                'message' => $apiResponse['message'] ?? 'API call failed',
            ], $apiResponse->status());
        } catch (\Exception $e) {
            Log::error('Hospitalization update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'result' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getHospitalizationDetailsById(Request $request, $employee_user_id = null, $op_registry_id = null)
    {
        try {
            $url = "https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getHospitalizationDetailsById/{$employee_user_id}/{$op_registry_id}";
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get($url);
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'data' => $response->json(),
                ]);
            }
            return response()->json([
                'result' => false,
                'message' => $response['message'] ?? 'API call failed',
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Hospitalization fetch error', ['error' => $e->getMessage()]);
            return response()->json([
                'result' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}

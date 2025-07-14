<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class corporateAssignHealthPlans extends Controller
{
    public function displayAssignHealthPlans()
    {
        $headerData = "ASSIGN HEALTH PLANS";
        return view('content.components.mhc.diagnostic-assesssment.corporate-assign-healthplans', ['HeaderData' => $headerData]);
    }
    public function assignHealthPlan(Request $request)
    {
        $corporateId = session("corporate_id");
        $locationId = session("location_id");
        if (!$corporateId or !$locationId) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        $validatedData = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'string|max:255',
            'healthplan_id' => 'required|integer',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assign_date',
            'doctor_id' => 'nullable|integer',
            'favourite_id' => 'nullable|integer',
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/assignHealthPlan/' . $corporateId . '/' . $locationId, [
            'user_ids' => $validatedData['user_ids'],
            'healthplan_id' => $validatedData['healthplan_id'],
            'assign_date' => $validatedData['assign_date'],
            'due_date' => $validatedData['due_date'],
            'doctor_id' => $validatedData['doctor_id'],
            'favourite_id' => $validatedData['favourite_id'],
        ]);
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['message']]);
        }
        return response()->json(['result' => false, 'message' => $response['message']]);
    }
    public function displayAssignHealthPlanList()
    {
        $headerData = "ASSIGN HEALTH PLAN LIST";
        return view('content.components.mhc.diagnostic-assesssment.corporate-assign-healthplan-list', ['HeaderData' => $headerData]);
    }
    public function getAllAssignHealthPlans(Request $request)
    {
        $corporateId = session("corporate_id");
        $locationId = session("location_id");
        $employeeId = session("employee_id");
        if (!$corporateId or !$locationId) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllAssignedHealthPlan/' . $corporateId . '/' . $locationId, [
    'employeeid' => strtolower($employeeId)]);
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json(['result' => false, 'message' => $response['message']]);
    }

    public function getAllColorCodes()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllColorCodes');
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json(['result' => false, 'message' => $response['message']]);
    }

    public function displayHealthplanTestPage($healthplan_id = null, $test_id = null, $test_code = null)
    {
        if ($test_code === null || !is_numeric($test_code)) {
            return "Invalid Request";
        }
        $corporateId = session('corporate_id');
        $locationId = session('location_id');
        if (! $corporateId || ! $locationId) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getTestDetails/' . $corporateId . '/' . $locationId . '/' . $test_code);
        if ($response->successful()) {
            $testDetails = $response['data'];
            return view('content.components.mhc.diagnostic-assesssment.corporate-assign-healthplan-test', ['HeaderData' => 'ASSIGN HEALTH PLAN TEST PAGE', 'testDetails' => $testDetails]);
        }
        return "Invalid Request";
    }

    public function saveCertificateCondition()
    {
        $validatedData = request()->validate([
            'certificate_id' => 'required|integer',
            'test_id' => 'required|integer',
            'healthplan_assigned_status_id' => 'required|integer',
            'condition' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'user_id' => 'required|string',
            'issue_date' => 'nullable|date',
            'next_assessment_date' => 'nullable|date|after_or_equal:issue_date',
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/saveCertificateCondition', [
            'certificate_id' => $validatedData['certificate_id'],
            'test_id' => $validatedData['test_id'],
            'condition' => $validatedData['condition'],
            'remarks' => $validatedData['remarks'],
            'healthplan_assigned_status_id' => $validatedData['healthplan_assigned_status_id'],
            'user_id' => $validatedData['user_id'],
            'issue_date' => $validatedData['issue_date'],
            'next_assessment_date' => $validatedData['next_assessment_date'],
        ]);
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message']]);
        }
        return "Invalid Request";
    }
}

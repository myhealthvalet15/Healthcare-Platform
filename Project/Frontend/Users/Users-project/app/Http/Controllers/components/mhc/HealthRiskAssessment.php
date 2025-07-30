<?php

namespace App\Http\Controllers\components\mhc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HealthRiskAssessment extends Controller
{
    public function displayHRATemplatesPage()
    {
        $headerData = "HRA Templates";
        return view('content.components.mhc.health-risk-assesssment.health-risk-assesssment-templates', ['HeaderData' => $headerData]);
    }
    public function getAllHRATemplates(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/hra/templates/getAllHRATemplates');
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'data' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
    public function getAllLocations(Request $request)
    {
        try {
            $response = Http::withHeaders([
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
             ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllLocations');
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']) && !empty($responseData['data'])) {
                    return response()->json(['result' => true, 'data' => $responseData['data']]);
                } else {
                    return response()->json(['result' => true, 'data' => []], 200);
                }
            }
            return response()->json([true => false, 'error' => 'Error fetching data'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function updateAssignedHraTemplate(Request $request)
    {
        $request->validate([
            'hra_template_id' => 'required|integer',
            'location_id' => 'required',
            'employee_type_id' => 'required|array',
            'employee_type_id.*' => 'required',
            'department_id' => 'required|array',
            'department_id.*' => 'required',
            'designation' => 'nullable|array',
            'designation.*' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);
        try {
            $apiData = [
                'template_id' => $request->input('hra_template_id'),
                'location_id' => $request->input('location_id'),
                'employee_type_id' => $request->input('employee_type_id'),
                'department_id' => $request->input('department_id'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ];
            if ($request->has('designation') && !empty($request->input('designation'))) {
                $apiData['designation'] = $request->input('designation');
            }
            if ($request->input('location_id') === 'all') {
                $apiData['location_id'] = 'all';
            }
            if (in_array('all', $request->input('employee_type_id'))) {
                $apiData['employee_type_id'] = ['all'];
            }
            if (in_array('all', $request->input('department_id'))) {
                $apiData['department_id'] = ['all'];
            }
            if ($request->input('designation')) {
                if (in_array('all', $request->input('designation'))) {
                    $apiData['designation'] = ['all'];
                }
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/hra/templates/updateAssignedHraTemplate', $apiData); 
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => "HRA Template updated successfully"]);
            }
            return response()->json(['result' => false, 'message' => 'Failed to update HRA Template'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }
    public function assignHRATemplate(Request $request)
    {
        $request->validate([
            'hra_template_id' => 'required|integer',
            'location_id' => 'required',
            'employee_type_id' => 'required|array',
            'employee_type_id.*' => 'required',
            'department_id' => 'required|array',
            'department_id.*' => 'required',
            'designation' => 'nullable|array',
            'designation.*' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);
        try {
            $apiData = [
                'template_id' => $request->input('hra_template_id'),
                'location_id' => $request->input('location_id'),
                'employee_type_id' => $request->input('employee_type_id'),
                'department_id' => $request->input('department_id'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ];
            if ($request->has('designation') && !empty($request->input('designation'))) {
                $apiData['designation'] = $request->input('designation');
            }
            if ($request->input('location_id') === 'all') {
                $apiData['location_id'] = 'all';
            }
            if (in_array('all', $request->input('employee_type_id'))) {
                $apiData['employee_type_id'] = ['all'];
            }
            if (in_array('all', $request->input('department_id'))) {
                $apiData['department_id'] = ['all'];
            }
            if ($request->input('designation')) {
                if (in_array('all', $request->input('designation'))) {
                    $apiData['designation'] = ['all'];
                }
            } 
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/hra/templates/assignHRATemplate', $apiData); 
            if ($response->successful()) {
                $responseData = $response->json();
                return response()->json([
                    'result' => true,
                    'message' => 'HRA Template assigned successfully',
                    'data' => $responseData
                ]);
            }
            $errorData = $response->json();
            return response()->json([
                'result' => false,
                'message' => $errorData['message'] ?? 'Failed to assign HRA Template'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getAllAssignedHraTemplates(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/hra/templates/getAllAssignedHraTemplates');
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'data' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
}

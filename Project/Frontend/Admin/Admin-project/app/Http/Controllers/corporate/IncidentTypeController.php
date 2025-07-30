<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class IncidentTypeController extends Controller
{
    public function displayIncidentTypePage(Request $request)
    {
        $incident = $this->getAllIncidentTypes($request);
        return view('content.incident.view-incident-type', ['incident' =>  $incident ]);
    }
    public function getAllIncidentTypes(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get(env('BACKEND_API_URL') . '/V1/corporate-stubs/stubs/getAllIncidentTypes');
            return response()->json([
                'result' => $response->successful(),
                'data' => $response['data'] ?? 'Failed to fetch data'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
    public function addIncidentType(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post(env('BACKEND_API_URL') . '/V1/corporate-stubs/stubs/addIncidentType', [
                'incident_type_name' => $request->incident_type_name
            ]);

            return response()->json([
                'result' => $response->successful(),
                'data' => $response['data'] ?? 'Failed to add data'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
    public function editIncidentType(Request $request, $incident_type_id)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post(env('BACKEND_API_URL') . '/V1/corporate-stubs/stubs/editIncidentType/' . $incident_type_id, [
                'incident_type_name' => $request->incident_type_name
            ]);

            return response()->json([
                'result' => $response->successful(),
                'data' => $response['data'] ?? 'Failed to update data'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
    public function deleteIncidentType(Request $request, $incident_type_id)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete(env('BACKEND_API_URL') . '/V1/corporate-stubs/stubs/deleteIncidentType/' . $incident_type_id);

            return response()->json([
                'result' => $response->successful(),
                'data' => $response['data'] ?? 'Failed to delete data'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
    public function displayAssignIncidentTypePage(Request $request)
    {
        $header = 'Assign Incident Type';
        return view('content.incident.assign-incident-type', ['header' => $header]);
    }
    public function getAllAssignedIncidentTypes(Request $request, $corporate_id)
    {
        try {
            if (!$corporate_id) {
                return response()->json([
                    'result' => false,
                    'data' => 'Corporate ID is required.'
                ], 400);
            }
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get(env('BACKEND_API_URL') . '/V1/corporate-stubs/stubs/getAllAssignedIncidentTypes/' . $corporate_id);
            return response()->json([
                'result' => $response->successful(),
                'data' => $response['data'] ?? 'Failed to fetch data'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }
    }
    public function assignIncidentTypes(Request $request, $corporate_id)
    {
        try {
            if (!$corporate_id) {
                return response()->json([
                    'result' => false,
                    'data' => 'Corporate ID is required.'
                ], 400);
            }
            $validator = Validator::make($request->all(), [
                'incident_types' => 'required|array|min:2|max:5',
                'incident_types.*.id' => 'required|integer',
                'incident_types.*.injury_color_types' => 'required|array',
                'incident_types.*.injury_color_types.*' => ['regex:/^#[0-9A-Fa-f]{6}$/']
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => false,
                    'data' => 'Validation Failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post(env('BACKEND_API_URL') . '/V1/corporate-stubs/stubs/assignIncidentTypes/' . $corporate_id, [
                'incident_types' => $request->input('incident_types')
            ]);
            return response()->json([
                'result' => $response->successful(),
                'data' => $response['data'] ?? 'Failed to assign incident types'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'data' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

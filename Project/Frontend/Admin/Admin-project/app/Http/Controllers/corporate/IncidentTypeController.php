<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IncidentTypeController extends Controller
{
 public function index(Request $request)
    {
        $incident = $this->getAllIncidentTypes($request);
        //dd($incident);exit;
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
}

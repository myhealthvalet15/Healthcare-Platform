<?php

namespace App\Http\Controllers\components\mhc\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MhcReportsController extends Controller
{
    
    public function index(Request $request)
    {
        $headerData = "Health Risk Assessment Reports";
        return view('content.components.mhc.reports.health-risk-reports', ['HeaderData' => $headerData]);
    } 
    public function graphBasedonFilter(Request $request)
    {
        $headerData = "Health Risk Assessment Reports";
        return view('content.components.mhc.reports.health-risk-reports-filter', ['HeaderData' => $headerData]);
    }

    public function getHealthData(Request $request)
    {  
        $corporateId = session('corporate_id');
        $locationId = session('location_id');

        // Validate the request data
        $request->validate([
            'employeeType' => 'nullable|integer',
            'medicalCondition' => 'nullable|integer',
            'department' => 'nullable|integer',
            'ageGroup' => 'nullable|string',
            'fromDate' => 'required|date_format:d/m/Y',
            'toDate' => 'required|date_format:d/m/Y'
        ]);
        // Prepare the request data
        $data = [
            'employeeType' => $request->input('employeeType'),
            'medicalCondition' => $request->input('medicalCondition'),
            'department' => $request->input('department'),
            'ageGroup' => $request->input('ageGroup'),
            'fromDate' => $request->input('fromDate'),
            'toDate' => $request->input('toDate'),
            'reportType' => $request->input('reportType')
        ];  
        try {
             $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getHealthData/" . $locationId . "/" . $corporateId);
        
            // Check if the response is successful
            // Log::info('Response from API: ', ['response' => $response->json()]);
            // dd($response->json());
            // return response()->json(['result' => true, 'data' => $response->json()]);
        return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'data' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Internal Server Error'], 500);
        }          
        


}

}
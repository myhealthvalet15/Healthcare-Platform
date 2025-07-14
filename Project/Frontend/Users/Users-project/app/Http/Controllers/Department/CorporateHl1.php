<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CorporateHl1 extends Controller
{
   
    public function index(Request $request)
    {
        $headerData = "DEPARTMENTS";
        $corporate_id = session('corporate_id');
        $corporate_admin_user_id = session('corporate_admin_user_id');
        $location_id = session('location_id');
    
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/employee-types/employees/employees/index');
    
            $hl1 = []; // Default to empty
    
            if ($response->successful()) {
                $data = $response->json();
                $hl1 = $data['data'] ?? [];
            }
    
            return view('content.departments.corporate-hl1', compact(
                'hl1', 'corporate_id', 'corporate_admin_user_id', 'location_id'
            ), ['HeaderData' => $headerData]);
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    

    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required',
            'location_id' => 'required',
            'corporate_admin_user_id' => 'required',
            'hl1_name' => 'nullable',
            'hl1_code' => 'nullable',
            'active_status' => 'nullable',
        ]);
    
        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Gather the data from the request
        $corporate_id = $request->input('corporate_id');
        $location_id = $request->input('location_id');
        $corporate_admin_user_id = $request->input('corporate_admin_user_id');
        $hl1_name = $request->input('hl1_name');
        $hl1_code = $request->input('hl1_code');
        $active_status = $request->input('active_status');
    
        try {
            // Send the request to the external API using Laravel's HTTP Client
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/employee-types/employees/employees/create', [
                'corporate_id' => $corporate_id,
                'location_id' => $location_id,
                'corporate_admin_user_id' => $corporate_admin_user_id,
                'hl1_name' => $hl1_name,
                'hl1_code' => $hl1_code,
                'active_status' => $active_status,
            ]);
    
            // Check if the response was successful
            if ($response->successful()) {
                // Assuming the response contains a 'success' field or something similar
                $data = $response->json(); // Convert the response to an array
    
                if ($data['success']) {
                    return redirect()->route('employee-Department-hl1')->with('success', 'Department added successfully');
                } else {
                    return redirect()->back()->with('error', 'An error occurred while adding the department');
                }
            } else {
                // If the response status is not successful, handle the error
                return redirect()->back()->with('error', 'An error occurred while adding the department');
            }
    
        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'hl1_name' => 'nullable|string',
            'hl1_code' => 'nullable|string',
            'active_status' => 'nullable|boolean',
        ]);
    
        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Get the input data from the request
        $hl1_name = $request->input('hl1_name');
        $hl1_code = $request->input('hl1_code');
        $active_status = $request->input('active_status');
    
        try {
            // Send the request to the external API using Laravel's HTTP Client
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post("https://api-user.hygeiaes.com/V1/employee-types/employees/employees/update`/{$id}", [
                'hl1_name' => $hl1_name,
                'hl1_code' => $hl1_code,
                'active_status' => $active_status,
            ]);
            return $response;
            // Check if the response was successful
            if ($response->successful()) {
                $data = $response->json(); // Convert the response to an array
    
                if ($data['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'HL1 updated successfully!',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred while updating HL1',
                    ], 500);
                }
            } else {
                // If the response status is not successful, handle the error
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating HL1',
                ], 500);
            }
    
        } catch (\Exception $e) {
            // Catch any exceptions and return an error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function destroy(Request $request, $id)
{
    try {
        // Send DELETE request using Laravel's HTTP client with headers
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->delete("https://api-user.hygeiaes.com/V1/employee-types/employees/employees/delete/{$id}");

        // Check if the response was successful
        if ($response->successful()) {
            $data = $response->json(); // Convert the response to an array

            if ($data['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'HL1 Deleted successfully!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting HL1',
                ], 500);
            }
        } else {
            // If the response status is not successful, handle the error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting HL1',
            ], 500);
        }
    } catch (\Exception $e) {
        // Catch any exceptions and return an error message
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}
}

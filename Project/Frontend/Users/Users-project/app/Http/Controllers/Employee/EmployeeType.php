<?php

namespace App\Http\Controllers\Employee;

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

class EmployeeType extends Controller
{
 
    public function index(Request $request)
    {
        $corporate_id = session('corporate_id');
    
        // Fetch employee type data from the external API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/employee-types/employees/employees/show/' . $corporate_id);
        
        // Check if the response is successful
        if ($response->successful() && $response->getStatusCode() == 200) {
            $data = $response->json();  // Convert the response to an array
            
            // Assuming 'data' holds the employee types information
            $emptype = $data['data'] ?? [];
    
            // Pass data to the Blade view
            return view('content.Employee.EmployeeType.index', compact('emptype', 'corporate_id'));
        }
    
        // Handle failure (you could log or handle errors as needed)
        return response()->json(['result' => false, 'data' => 'Invalid request.']);
    }
    

    public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'employee_type_name.*' => 'required|string|max:255',
        'active_status.*' => 'required|in:0,1',
        'employee_type_id.*' => 'required|integer',
        'Contractors' => 'nullable',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $employeeTypes = [];
    foreach ($request->employee_type_name as $index => $employeeTypeName) {
        $employeeTypes[] = [
            'employee_type_id' => $request->employee_type_id[$index] ?? null,
            'employee_type_name' => $employeeTypeName,
            'active_status' => $request->active_status[$index] ?? 0,
            'checked' => isset($request->Contractors[$index]) && $request->Contractors[$index] === 'on' ? 1 : 0,
            'corporate_id' => $request->corporate_id,
        ];
    }

    try {
        // Replacing the previous POST request with a GET request using Http::get()
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/employee-types/employees/employees/update');

        // Check the response
        $responseData = $response->json(); // Convert the response to array

        if ($responseData['success'] ?? false) {
            return redirect()->route('employee-type')->with('success', 'Employee types updated successfully.');
        } else {
            return redirect()->route('employee-type')->with('error', 'Failed to update employee types.');
        }
    } catch (\Exception $e) {
        return redirect()->route('employee-type')->with('error', 'API request failed: ' . $e->getMessage());
    }
}

public function store(Request $request)
{
    // Prepare data to send in the request
    $postData = [
        'employee_type_name' => $request->input('employee_type_name'),
        'corporate_id' => $request->input('corporate_id'),
        'active_status' => $request->input('active_status'),
    ];

    try {
        // Replacing the previous API request with Laravel's Http::post() method
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/employee-types/employees/employees/add', $postData);

        // Convert the response to array
        $responseData = $response->json();

        // Check if the API returned a successful response
        if ($responseData['success'] ?? false) {
            return redirect()->route('employee-type')->with('success', 'Employee type added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add employee type. Please try again.');
        }
    } catch (\Exception $e) {
        // Log the error and return a generic error message
        Log::error('Error while calling Add Employee Type API', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'An error occurred. Please try again later.');
    }
}

}

<?php

namespace App\Http\Controllers\Prescription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function prescriptionTemplate()
    {
        $headerData = 'Prescription Template List';
        return view('content.Prescription.prescription-template', ['HeaderData' => $headerData]);
    }
    public function prescriptionTemplateAdd()
    {
        $headerData = 'Add Prescription Template';
        return view('content.Prescription.prescription-template-add', ['HeaderData' => $headerData]);
    }
    public function getPrescriptionDetails(Request $request)
    {
        // Get the location ID from the session
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request: Missing location ID'
            ]);
        }

        try {
            // Make the request to the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllPrescriptionTemplate/' . $locationId);

            // Check if the external API request was successful
            if ($response->successful()) {
                $data = $response->json();  // Convert the response to an array

                // Check if data is empty
                if (empty($data['data'])) {
                    return response()->json([
                        'result' => false,
                        'message' => 'No records found for the given location'
                    ]);
                }

                return response()->json([
                    'result' => true,
                    'data' => $data['data']
                ]);
            }

            // Handle 404 status code from the external API
            if ($response->status() == 404) {
                $data = $response->json(); // Extract the body message
                return response()->json([
                    'result' => false,
                    'message' => $data['message'] ?? 'No records found for the given location'
                ]);
            }

            // Handle other unsuccessful response from the external API
            return response()->json([
                'result' => false,
                'message' => 'External API request failed',
                'status' => $response->status(),
                'response_body' => $response->body()
            ], $response->status());
        } catch (\Exception $e) {
            // Catch any errors and return a 503 response
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');
        $requestData = array_merge($request->all(), [
            'location_id' => $locationId,
            'corporate_id' => $corporateId,
            'corporate_user_id' => $userId,
        ]);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addPrescriptionTemplate', $requestData);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function prescriptionTemplateEdit()
    {
        $headerData = 'Edit Prescription Template';
        return view('content.Prescription.prescription-edit', ['HeaderData' => $headerData]);
    }
    public function prescriptionTemplateEditById(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPrescriptionTemplateById/' . $id);
            // return $response;
            if ($response->successful()) {

                $prescription = $response['data'];
                // Get prescription data
                return response()->json($prescription); // Return the data as JSON
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function updatePrescriptionTemplate(Request $request, $id)
    {
        // return 'Hi';
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        // Prepare data for the API request
        $requestData = array_merge($request->all(), [
            'location_id' => $locationId,
            'corporate_id' => $corporateId,
            'corporate_user_id' => $userId,
        ]);
        try {
            // Make the API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/updatePrescriptionTemplate/' . $id, $requestData);
            // return $response;
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Prescription Template updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'result' => false,
                    'message' => 'Error from external API',
                    'details' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' =>
                 'Internal Server Error'
            ], 500);
        }
    }

    public function prescriptionAdd()
    {
        $headerData = 'Add Prescription';
        return view('content.Prescription.prescription-add', ['HeaderData' => $headerData]);
    }

    public function addEmployeePrescription($employee_id = null, $op_registry_id = null)
    {
        try {
            if (!is_null($op_registry_id) && !is_numeric($op_registry_id)) {
                return "Invalid Request";
            }
            if (!$employee_id || !ctype_alnum($employee_id)) {
                return "Invalid Request";
            }
            // return $employee_id;
            // Retrieve location ID from session
            $locationId = session('location_id');
            // return $locationId;
            if (!$locationId) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request: Missing location ID'
                ]);
            }
           
            if ($op_registry_id === null) {
                $employeeResponse = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . request()->cookie('access_token'),
                ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/' . 0 .  '/' . $employee_id);
            } else {
                $employeeResponse = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . request()->cookie('access_token'),
                ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/' . 0 .  '/' . $employee_id . "/op/" . $op_registry_id);
            }
           // return $employeeResponse;
            // Second API Request: Get prescription templates
            $prescriptionResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getOnlyPrescriptionTemplate/' . $locationId);
          //  return $prescriptionResponse;
            $pharmacyResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPharmacyDetails/' . $locationId);
           // return $pharmacyResponse;
            if ($employeeResponse->successful() && $prescriptionResponse->successful() && $pharmacyResponse->successful()) {
                $employeeData = $employeeResponse->json();
                $prescriptionData = $prescriptionResponse->json();
                $pharmacyData     = $pharmacyResponse->json();

                // Validate employee data
                if (!isset($employeeData['result']) || !$employeeData['result'] || !$pharmacyData['result']) {
                    return "Invalid Request";
                }
                $prescriptionTemplates = collect($prescriptionData['data'])->unique('prescription_template_id')->values();

                // Return the view with both employee data and prescription templates
                return view('content.Prescription.add-employee-prescription', [
                    'HeaderData' => 'Add Employee Prescription',
                    // Employee data
                    'prescriptionTemplates' => $prescriptionTemplates,
                    'pharmacyData' => $pharmacyData,
                    'employeeData' => $employeeData['message']  // Prescription templates
                ]);
            }

            // Handle failure if either of the requests fails
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function store_EmployeePrescription(Request $request)
    {
        // return $request;
        Log::info('User Type: ' . $request->user_type);

        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');
        $user_type = session('user_type');
        $master_user_user_id = session('master_user_user_id');
       
        $requestData = array_merge($request->all(), [
            'location_id' => $locationId,
            'corporate_id' => $corporateId,
            'corporate_user_id' => $userId,
            'user_type'=> $user_type,
             'master_user_user_id'=> $master_user_user_id,
              
        ]);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addPrescription', $requestData);
           return $response;
            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function prescriptionView()
    {
        $headerData = 'View Prescription Template';
        return view('content.Prescription.prescription-view', ['HeaderData' => $headerData]);
    }


    public function getAllEmployeePrescription(Request $request)
    {
        // Get the location ID from the session
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request: Missing location ID'
            ]);
        }

        // Get date range from request (assuming format: dd/mm/yyyy)
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');



        $requestData = $request->all();
        //return $requestData;
        try {
            // Make the request to the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getEmployeePrescription/' . $userId, $requestData);


            // Uncomment if you want to debug the raw response
            //return $response;

            if ($response->successful()) {
                $data = $response->json();

                // Check if data is empty
                if (empty($data['data'])) {
                    return response()->json([
                        'result' => false,
                        'message' => 'No records found for the given location'
                    ]);
                }

                $records = $data['data'];

                // Apply date filtering if both dates are present
                if ($fromDate && $toDate) {
                    $fromDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
                    $toDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');

                    $records = array_filter($records, function ($record) use ($fromDateFormatted, $toDateFormatted) {
                        if (!isset($record['date'])) {
                            return false;
                        } // Safeguard
                        $recordDate = \Carbon\Carbon::parse($record['date'])->format('Y-m-d');
                        return $recordDate >= $fromDateFormatted && $recordDate <= $toDateFormatted;
                    });
                }

                return response()->json([
                    'result' => true,
                    'data' => array_values($records) // Re-index the array
                ]);
            }

            // Handle 404 status code from the external API
            if ($response->status() == 404) {
                $data = $response->json(); // Extract the body message
                return response()->json([
                    'result' => false,
                    'message' => $data['message'] ?? 'No records found for the given location'
                ]);
            }

            // Handle other unsuccessful response from the external API
            return response()->json([
                'result' => false,
                'message' => 'External API request failed',
                'status' => $response->status(),
                'response_body' => $response->body()
            ], $response->status());
        } catch (\Exception $e) {
            // Catch any errors and return a 503 response
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    public function getStockByDrugId($id, Request $request)
    {
        try {
            // Make the API call to get stock data by drug id
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getStockByDrugId/' . $id);

            // Check if the request was successful
            if ($response->successful()) {
                // Get the JSON response as an array
                $data = $response->json();
                return $data;
                // Return the response with the desired structure
                return response()->json([
                    'result' => true,
                    'data' => [
                        'total_current_availability' => $data['total_current_availability']
                    ]
                ]);
            } else {
                return redirect()->back()->with('error', 'An error occurred while fetching drug data.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    } public function getStockByDrugIdAndPharmacyId($id, $pharmacy_id, Request $request)
    {
        //return 'hi';
        try {
            // Make the API call to get stock data by drug id
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get("https://api-user.hygeiaes.com/V1/corporate/corporate-components/getStockByDrugIdAndPharmacyId/{$id}/{$pharmacy_id}");
            return $response;
            // Check if the request was successful
            if ($response->successful()) {
                // Get the JSON response as an array
                $data = $response->json();
                return $data;
                // Return the response with the desired structure
                return response()->json([
                    'result' => true,
                    'data' => [
                        'total_current_availability' => $data['total_current_availability']
                    ]
                ]);
            } else {
                return redirect()->back()->with('error', 'An error occurred while fetching drug data.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }




    public function prescriptionPrintOption(Request $request)
    {
        $group = $request->query('group');
        $type = $request->query('type');
        $headerData = 'Print Prescription Template';
        return view('content.Prescription.prescription-print-option', ['HeaderData' => $headerData]);
    }
    public function getPrintPrescriptionById(Request $request, $id)
    {
        //return 'Hi';
        //$group = request()->query('group');
        //return $group;
        //return $request;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get(
                'https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPrintPrescriptionById/' . $id,
                $request
            );
            // return $response;
            if ($response->successful()) {

                $prescription = $response['data'];
                // Get prescription data
                return response()->json($prescription); // Return the data as JSON
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}

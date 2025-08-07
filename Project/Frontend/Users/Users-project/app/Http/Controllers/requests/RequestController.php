<?php

namespace App\Http\Controllers\requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RequestController extends Controller
{
    public function getPendingRequests()
    {
        $menuData = Cache::get('menuData');
        $headerData = 'Pending Requests';
        return view('content.requests.pending-requests', [
            'HeaderData' => $headerData,
           // 'menuData'   => $menuData,
        ]);
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
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getEmployeePrescriptionforPendingRequest/' . $userId, $requestData);


            // Uncomment if you want to debug the raw response


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

    public function closePrescription(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/closePrescription', $request->all());
            return $response;
            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function issuePartlyPrescription(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/issuePartlyPrescription', $request->all());
            return $response;
            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function getCompletedRequests()
    {
        $headerData = 'Completed Requests';
        return view('content.requests.complete-requests', ['HeaderData' => $headerData]);
    }

    public function getCompletedPrescription(Request $request)
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
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllClosedPrescription/' . $userId, $requestData);

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

}

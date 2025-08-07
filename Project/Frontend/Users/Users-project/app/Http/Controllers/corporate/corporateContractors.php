<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class corporateContractors extends Controller
{
    // Added By Bhava on 09-04-2025
    public function index()
    {
        $headerData = 'Contractors List';
        return view('content.corporate.corporate-contractors', ['HeaderData' => $headerData]);
    }
    public function listcontractors(Request $request)
    {
      // return 'Hi';
        try {
            $headerData = "Contractors";
            $locationId = session('location_id');
            if (!$locationId) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request'
                ]);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get("https://api-user.hygeiaes.com/V1/corporate/corporate-components/viewContractors/" . $locationId);
            if ($response->successful()) {
                $contractordata = $response['data'];
                return view('content.corporate-contractors.index', compact('contractordata'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function addContractors(Request $request)
    {
        //return 'Hi';
        Log::info($request->all());
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        // Log::info('Request Data:', $request->all());
        $request->validate([
            'contractor_name' => 'required|string',
            'contractor_email' => 'required|email',
            'contractor_address' => 'required|string',
            'active_status' => 'nullable|boolean',
        ]);
        $contractor_data = [
            'contractor_name' => $request->input('contractor_name'),
            'contractor_email' => $request->input('contractor_email'),
            'contractor_address' => $request->input('contractor_address'),
            'active_status' => $request->has('active_status') ? $request->active_status : 0,  // Default to 0 if not present
            'location_id' => $locationId,
        ];
        //return $contractor_data;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/createContractors', $contractor_data);
            return $response;
            // Log::info($response);
            if ($response->successful() and $response->getStatusCode() === 201) {
                return response()->json([
                    'result' => true,
                    'message' => $response['message']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'message' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function updateContractor(Request $request, $id)
    {
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        // Log::info('Request Data:', $request->all());
        $request->validate([
            'contractor_name' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',

        ]);
        $modified_data = [
            'contractor_name' => $request->input('contractor_name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'active_status' => $request->input('active_status'),
            'location_id' => $locationId,
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/modifyContractors/' . $id, $modified_data);
            if ($response->successful() and $response->getStatusCode() === 201) {
                return response()->json([
                    'result' => true,
                    'message' => $response['message']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'message' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function deleteContractor(Request $request, $id)
    {
        // Log::info($id);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/removeContractors/' . $id);
            if ($response->successful() and $response->getStatusCode() === 201) {
                return response()->json([
                    'result' => true,
                    'message' => $response['message']
                ], $response->status());
            }
            return response()->json([
                'result' => true,
                'message' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function displayDynamicComponentsPage($component, $subcomponent)
    {
        $headerData = ucwords($component) . ', ' . ucwords($subcomponent);
        return view('content.laravel-example.user-management', ['HeaderData' => $headerData]);
    }
    public function getAllComponents($corpId, Request $request)
    {
        if (!$corpId) {
            return response()->json([
                'result' => true,
                'message' => 'Invalid Request',
            ], 400);
        }
        $apiResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllComponent/corpId/" . $corpId);
        return $apiResponse->json();
        if ($apiResponse->successful() && $apiResponse->status() == 200) {
            $responseData = $apiResponse->json();
            return response()->json([
                'result' => true,
                'message' => 'Data Fetched Successfully.',
                'data' => $responseData
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => "Invalid Request/"
            ], 400);
        }
    }
}

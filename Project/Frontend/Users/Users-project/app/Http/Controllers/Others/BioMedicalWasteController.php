<?php

namespace App\Http\Controllers\Others;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BioMedicalWasteController extends Controller
{
    public function bioMedicalWasteList(Request $request)
    {
        $headerData = 'Bio-Medical Waste Details';
        return view('content.Others.bio-medical-waste', ['HeaderData' => $headerData]);
    }
    public function getAllDetails(Request $request)
    {
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }

        // Get the from_date and to_date from the request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllIndustryWasteDetails/' . $locationId);

            // Get the data from the response
            $data = $response->json()['data'];

            // Filter the data by the from_date and to_date
            if ($fromDate && $toDate) {
                $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
                $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');

                $data = array_filter($data, function ($record) use ($fromDate, $toDate) {
                    // Convert record date to 'Y-m-d' format
                    $recordDate = \Carbon\Carbon::parse($record['date'])->format('Y-m-d');
                    return $recordDate >= $fromDate && $recordDate <= $toDate;
                });
            }

            return response()->json([
                'result' => true,
                'data' => array_values($data) // Reset array keys after filtering
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }



    public function addBioMedicalWaste(Request $request)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        try {
            $validated = $request->validate([
                'date' => 'required',
                'red' => 'required|integer',
                'yellow' => 'required|integer',
                'blue' => 'required|integer',
                'white' => 'required|integer',
                'issued_by' => 'required|string',
                'received_by' => 'required|string'
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addBioWasteData', $requestData);

            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => 'Corporate OHC added sucssaascessfully']);
            } else {
                return response()->json(['result' => false, 'message' => 'error to add Corporate OHC', 'details' => $response->body()]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Fill all the details',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'error: ' . $e->getMessage()]);
        }
    }

    public function updateBioMedicalWaste(Request $request, $id)
    {
        // return $request;
        try {
            $validated = $request->validate([
                'red' => 'required|integer',
                'yellow' => 'required|integer',
                'blue' => 'required|integer',
                'white' => 'required|integer',
                'issued_by' => 'required|string',
                'received_by' => 'required|string'
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-user.hygeiaes.com/V1/corporate/corporate-components/updateBioMedicalWasteById/{$id}", [
                'date' => $request->date,
                'red' => $validated['red'],
                'yellow' => $validated['yellow'],
                'blue' => $validated['blue'],
                'white' => $validated['white'],
                'issued_by' => $validated['issued_by'],
                'received_by' => $validated['received_by']
            ]);
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Bio Medical Waste updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'Error Updating Bio Medical Waste',
                    'details' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function bioMedicalPrint($id)
    {
        $headerData = 'BioMedical Print';
        return view('content.Others.bio-medical-print', ['HeaderData' => $headerData]);
    }


}

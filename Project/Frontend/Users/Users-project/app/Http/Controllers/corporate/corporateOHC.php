<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class corporateOHC extends Controller
{

    public function corporateOHCList(Request $request)
    {
        $headerData = 'Corporate OHC Details';
        return view('content.corporate.corporate-ohc', ['HeaderData' => $headerData]);
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
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllOHCDetails/' . $locationId);
            return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }
    public function getAllPharmacyDetails(Request $request)
    {
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPharmacyDetails/' . $locationId);
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            //return $response;
            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }
    public function addCorporateOHC(Request $request)
    {
        //return $request;
        // $locationId = session('location_id');
        // $corporate_id = session('corporate_id');
        try {
            $validated = $request->validate([
                'ohc_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addCorporateOHCData', $request);
            return $response;
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

    public function updateCorporateOHC(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'ohc_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-user.hygeiaes.com/V1/corporate/corporate-components/modifyCorporateOHC/{$id}", [
                'ohc_name' => $validated['ohc_name'],
                'status' => $validated['status']
            ]);
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Corporate OHC updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'Error Updating Corporate OHC',
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
    public function addPharmacy(Request $request)
    {
        //return $request;
        try {
            $validated = $request->validate([
                'pharmacy_name' => 'required|string|max:255',
                'status' => 'required|boolean',
                'main_pharmacy' => 'nullable|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addPharmacyData', $request);
            return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => 'Pharmacy OHC added sucssaascessfully']);
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
            return response()->json(['result' => false, 'message' => 'A main pharmacy already exists for this location: ']);
        }
    }
    public function updatePharmacy(Request $request, $id)
    {
        // return $id;
        try {
            $validated = $request->validate([
                'pharmacy_name' => 'required|string|max:255',
                'status' => 'required|boolean',
                'main_pharmacy' => 'nullable|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-user.hygeiaes.com/V1/corporate/corporate-components/modifyCorporatePharmacy/{$id}", [
                'pharmacy_name' => $validated['pharmacy_name'],
                'status' => $validated['status'],
                'main_pharmacy' => $validated['main_pharmacy']
            ]);
            // return $response;
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Pharmacy OHC updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => false,
                    'message' => 'A main pharmacy already exists for this location',
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
}

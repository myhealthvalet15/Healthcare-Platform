<?php

namespace App\Http\Controllers\Others;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function listInventory(Request $request)
    {
        $headerData = 'Inventory List Details';
        return view('content.Others.inventory', ['HeaderData' => $headerData]);
    }
    public function inventoryList(Request $request)
    {
        $corporate_id = session('corporate_id');
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        try {
            //return 'Hi';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllInventory/' . $locationId, $request->all());
            //return $response;
            $data = $response->json()['data'];
            return response()->json([
                'result' => true,
                'data' => array_values($data) // Reset array keys after filtering
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function inventoryAdd(Request $request)
    {

        $headerData = 'Add New Inventory';
        return view('content.Others.inventory-add', ['HeaderData' => $headerData]);
    }
    public function inventoryEdit(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getInventoryById/' . $id);

            //return $response;
            $headerData = 'Bhava';
            if ($response->successful()) {
                $inventory = $response['data'];
                $headerData = 'Edit Inventory Details';
                return view('content.Others.inventory-edit', compact('inventory'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addInventorytoDB', $requestData);
            //return $response;
            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function update(Request $request, $id)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        // Log::info('Bhavas Inventory Edit  Data:', $request->all());
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/updateInventory/' . $id, $requestData);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getCalibrationHistory(Request $request, $id)
    {
        $corporate_id = session('corporate_id');
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        try {
            //return 'Hi';
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getCalibrationHistory/' . $id);
            return $response;
            if ($response->successful()) {
                $inventory = $response['data'];
                return $inventory;
                $headerData = 'Inventory Details';
                return view('content.Others.inventory', compact('inventory'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: Unable to fetch inventory details.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

}

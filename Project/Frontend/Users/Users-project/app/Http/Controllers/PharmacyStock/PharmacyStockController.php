<?php

namespace App\Http\Controllers\PharmacyStock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PharmacyStockController extends Controller
{
    public function pharmacyStockList(Request $request)
    {
        $headerData = 'Pharmacy Stock Details';
        return view('content.PharmacyStock.index', ['HeaderData' => $headerData]);
    }
    public function getPharmacyStockDetails(Request $request)
    {
        //return 'Hi';
        //return $request;
        $locationId = session('location_id');
        //  $ohcPharmacyId = $request->input('ohc_pharmacy_id');
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
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllPharmacyStock/' . $locationId);

            if ($response->successful()) {

                return response()->json(['result' => true, 'data' => $response['data']]);
            }

            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }
    public function pharmacyStockAdd()
    {
        $headerData = 'Add New Pharmacy Stock';
        return view('content.PharmacyStock.PharmacyStockAdd', ['HeaderData' => $headerData]);
    }
    public function getDrugTemplateDetails(Request $request)
    {
        //  return 'Hi';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getDrugTemplateDetails/');

            if ($response->successful()) {
                $data = $response->json(); // Get the response as an array
                return response()->json([
                    'drugTemplate' => $data['drugTemplate'] ?? [], // Default to empty array if not available
                ]);
            } else {
                return redirect()->back()->with('error', 'An error occurred while fetching drug data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function getDrugTypesAndIngredients(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getDrugTypesAndIngredients/');
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'drugTypes' => $data['drugTypes'] ?? [],
                    'drugIngredients' => $data['drugIngredients'] ?? [],
                ]);
            } else {
                return redirect()->back()->with('error', 'An error occurred while fetching drug data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function store(Request $request)
    {
        //return 'hi';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addPharmacyStock', $request);
            return $response;
            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function update(Request $request, $id)
    {
        //return 'Hi';
        // Log::info('Bhavas Request Data for Pharmacy Stock:', $request->all());
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/updatePharmacyStock/' . $id, $request);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function pharmacyMoveList(Request $request)
    {

        $headerData = 'Pharmacy Stock Details';
        return view('content.PharmacyStock.stockmove', ['HeaderData' => $headerData]);
    }
    public function moveStock(Request $request)
    {

        try {
            // Get the drug_template_id, quantity, and store_id from the request
            $drugTemplateId = $request->input('drug_template_id');
            $quantity = $request->input('quantity');
            $storeId = $request->input('store_id');
            $mainStoreId = $request->input('mainStoreId');
            // return  $storeId;
            // Make sure the required parameters are present
            if (!$drugTemplateId || !$quantity || !$storeId) {
                return response()->json([
                    'result' => false,
                    'message' => 'Missing required parameters: drug_template_id, quantity, or store_id.'
                ], 400);
            }

            // Fetch stock details from your method
            $StockDetails = $this->getMainStockDetails($request);
            //return $StockDetails;
            if (!$StockDetails) {
                return response()->json([
                    'result' => false,
                    'message' => 'Stock details not found for the provided drug_template_id.'
                ], 404);
            }
            $StockDetailsData = $StockDetails->getData(true);
            // Get the current details from the response
            $currentAvailability = $StockDetailsData['data']['current_availability'];
            $soldQuantity = $StockDetailsData['data']['sold_quantity'];
            $drugName = $StockDetailsData['data']['drug_name'];
            $drugStrength = $StockDetailsData['data']['drug_strength'];
            $drugBatch = $StockDetailsData['data']['drug_batch'];
            $manufactureDate = $StockDetailsData['data']['manufacter_date'];
            $expiryDate = $StockDetailsData['data']['expiry_date'];
            $drugType = $StockDetailsData['data']['drug_type'];
            $amountPerTab = $StockDetailsData['data']['amount_per_tab'];
            $totalCost = $StockDetailsData['data']['total_cost'];
            $sgst = $StockDetailsData['data']['sgst'];
            $cgst = $StockDetailsData['data']['cgst'];
            $igst = $StockDetailsData['data']['igst'];
            $ohc_pharmacy_id = $storeId;
            $mainStoreId = $mainStoreId;

            // Calculate the new current availability and sold quantity
            //$newCurrentAvailability = $currentAvailability - $quantity;
            $newSoldQuantity = 0;
            $drug_id  = $StockDetailsData['data']['drug_id'];

            // Prepare data to insert into the stock table
            $newStockData = [
                'ohc_pharmacy_id' => $ohc_pharmacy_id,
                'drug_name' => $drugName,
                'drug_template_id' => $drugTemplateId,
                'drug_batch' => $drugBatch,
                'manufacter_date' => $manufactureDate,
                'expiry_date' => $expiryDate,
                'drug_strength' => $drugStrength,
                'drug_type' => $drugType,
                'quantity' => $quantity, // The quantity that is being moved
                'current_availability' => $quantity,
                'sold_quantity' => $newSoldQuantity,
                'ohc' => 1,
                'master_pharmacy_id' => '',
                'sgst' => $sgst,
                'cgst' => $cgst,
                'igst' => $igst,
                'amount_per_tab' => $amountPerTab,
                'total_cost' => $totalCost,
                'created_at' => now(),
                'updated_at' => now(),
                'drug_id' => $drug_id,
                'mainStoreId' => $mainStoreId,
            ];
            //return  $newStockData;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/moveStocktoMainStore', $newStockData);

            // Return the response from the external API
            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMainStockDetails(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getMainStockDetails/' . $request->drug_template_id);

            // Return the response from the external API
            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }

    }
    public function getSubPharmacyDetails(Request $request, $id)
    {
        $locationId = session('location_id');
        //$storeId = $request->input('store_id');  // Retrieve the store_id sent in the request
        //return $storeId;
        if (!$id) {
            return response()->json([
                'result' => false,
                'message' => 'Store ID is missing from the request'
            ]);
        }

        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request: Missing Location'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getSubPharmacyStockById/' . $id);

            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }

            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }
    public function getPharmacyStockByAvailability(Request $request, $id, $storeId)
    {
        //return $storeId;
        if (!$id) {
            return response()->json([
                'result' => false,
                'message' => 'Store ID is missing from the request'
            ]);
        }


        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getStockByAvailability/' . $id . '/' . $storeId);

            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }

            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }



}

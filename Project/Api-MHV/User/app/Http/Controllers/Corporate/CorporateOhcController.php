<?php

namespace App\Http\Controllers\Corporate;

use App\Models\Corporate\CorporateOHC;
use App\Models\Corporate\CorporatePharmacy;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CorporateOhcController extends Controller
{
    public function getAllOHCDetails($location_id)
    {
        try {
            $ohc = CorporateOHC::where('location_id', $location_id)->get();
            return response()->json([
                'result' => true,
                'data' => $ohc
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the OHC.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getPharmacyDetails($location_id)
    {
        try {
            $pharmacy = CorporatePharmacy::all();
            $pharmacy = CorporatePharmacy::where('location_id', $location_id)->get();
            return response()->json([
                'result' => true,
                'data' => $pharmacy
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the OHC.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function addCorporateOHCDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ohc_name' => 'required|string|max:255',
            'status' => 'nullable|boolean'
        ]);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
        }
        $ohcData = $validator->validated();
        $ohcData['corporate_id'] = $request->corporate_id;
        $ohcData['location_id'] = $request->location_id;
        $ohcData['active_status'] = $request->status;
        $corp_ohc = CorporateOHC::create($ohcData);
        return response()->json(['result' => true, 'data' => $corp_ohc], 201);
    }
    public function updateCorporateOHCById(Request $request, $corporateOHCId)
    {
        $corporateOHC = CorporateOHC::where('corporate_ohc_id', $corporateOHCId)->first();
        if (!$corporateOHC) {
            return response()->json(['message' => 'Corporate OHC not found'], 404);
        }
        $validatedData = $request->validate([
            'ohc_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
        $corporateOHC->ohc_name = $validatedData['ohc_name'];
        $corporateOHC->active_status = $validatedData['status'];
        $corporateOHC->save();
        return response()->json(['message' => 'Corporate OHC updated successfully', 'data' => $corporateOHC], 200);
    }
    public function addPharmacyDataDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_name' => 'required|string|max:255|unique:corporate_ohc_pharmacy,pharmacy_name',
            'status' => 'nullable|boolean',
            'main_pharmacy' => 'nullable|boolean'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => false, 'errors' => $validator->errors()], 422);
        }
        $ohcData = $validator->validated();
        if ($request->main_pharmacy) {
            $existingMainPharmacy = CorporatePharmacy::where('location_id', $request->location_id)
                ->where('main_pharmacy', 1)
                ->exists();
            if ($existingMainPharmacy) {
                return response()->json([
                    'result' => false,
                    'message' => 'A main pharmacy already exists for this location.'
                ], 422);
            }
        }

        $ohcData['corporate_id'] = $request->corporate_id;
        $ohcData['location_id'] = $request->location_id;
        $ohcData['active_status'] = $request->status;
        $ohcData['main_pharmacy'] = $request->main_pharmacy ?? 0;
        $corp_ohc = CorporatePharmacy::create($ohcData);
        return response()->json(['result' => true, 'data' => $corp_ohc], 201);
    }
    public function updatCorporatePharmacyByid(Request $request, $pharmacy_id)
    {
        $corporateOHC = CorporatePharmacy::where('ohc_pharmacy_id', $pharmacy_id)->first();
        if (!$corporateOHC) {
            return response()->json(['message' => 'Corporate Pharmacy not found'], 404);
        }
        $validatedData = $request->validate([
            'pharmacy_name' => 'required|string|max:255|unique:corporate_ohc_pharmacy,pharmacy_name,' . $pharmacy_id . ',ohc_pharmacy_id',
            'status' => 'required|boolean',
            'main_pharmacy' => 'nullable|boolean'
        ]);
        if ($validatedData['main_pharmacy']) {
            $existingMainPharmacy = CorporatePharmacy::where('location_id', $corporateOHC->location_id)
                ->where('main_pharmacy', 1)
                ->where('ohc_pharmacy_id', '!=', $pharmacy_id)
                ->exists();
            if ($existingMainPharmacy) {
                return response()->json([
                    'result' => false,
                    'message' => 'A main pharmacy already exists for this location.'
                ], 422);
            }
        }
        $corporateOHC->pharmacy_name = $validatedData['pharmacy_name'];
        $corporateOHC->active_status = $validatedData['status'];
        $corporateOHC->main_pharmacy = $validatedData['main_pharmacy'] ?? 0;
        $corporateOHC->save();
        return response()->json([
            'message' => 'Pharmacy OHC updated successfully',
            'data' => $corporateOHC
        ], 200);
    }
}

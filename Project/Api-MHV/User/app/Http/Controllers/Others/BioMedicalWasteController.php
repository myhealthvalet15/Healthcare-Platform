<?php

namespace App\Http\Controllers\Others;

use App\Http\Controllers\Controller;
use App\Models\Others\BioMedicalWaste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BioMedicalWasteController extends Controller
{
    public function getAllIndustryWasteDetails($location_id, Request $request)
{
    try {
        $query = BioMedicalWaste::where('loc_id', $location_id);

        // Check for 'from_date' and filter accordingly
        if ($request->has('from_date') && $request->input('from_date')) {
            $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('from_date'))->format('Y-m-d');
            $query->whereDate('date', '>=', $fromDate);
        }

        // Check for 'to_date' and filter accordingly
        if ($request->has('to_date') && $request->input('to_date')) {
            $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('to_date'))->format('Y-m-d');
            $query->whereDate('date', '<=', $toDate);
        }

        // Get filtered records
        $ohc = $query->get();

        return response()->json([
            'result' => true,
            'data' => $ohc
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'result' => false,
            'message' => 'An error occurred while fetching the OHC.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    
    public function addBioWasteData(Request $request)
    {
       //return $request;
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'red' => 'required|integer',
            'yellow' => 'required|integer',
            'blue' => 'required|integer',
            'white' => 'required|integer',
            'issued_by' => 'required|string'


        ]);

        if ($validator->fails()) {
            return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
        }
        $bioWasteData = $validator->validated();
        $bioWasteData['corp_id'] = $request->corporate_id;
        $bioWasteData['loc_id'] = $request->location_id;
        $bioWasteData['received_by'] = $request->received_by;
        //return $bioWasteData;
        $BioWaste = BioMedicalWaste::create($bioWasteData);
        return response()->json(['result' => true, 'data' => $BioWaste], 201);
    }


    public function updateBioMedicalWasteById(Request $request, $industry_id)
    {
        //return $corporateOHCId;
        $bioMedicalWaste = BioMedicalWaste::where('industry_id', $industry_id)->first();
        if (!$bioMedicalWaste) {
            return response()->json(['message' => 'Biowaste not found'], 404);
        }
        $validatedData = $request->validate([
            'date' => 'required',
            'red' => 'required|integer',
            'yellow' => 'required|integer',
            'blue' => 'required|integer',
            'white' => 'required|integer',
            'issued_by' => 'required|string',
            'received_by' => 'required|string'
        ]);
        $bioMedicalWaste->date = $validatedData['date'];
        $bioMedicalWaste->red = $validatedData['red'];
        $bioMedicalWaste->yellow = $validatedData['yellow'];
        $bioMedicalWaste->blue = $validatedData['blue'];
        $bioMedicalWaste->white = $validatedData['white'];
        $bioMedicalWaste->issued_by = $validatedData['issued_by'];
        $bioMedicalWaste->received_by = $validatedData['received_by'];
        $bioMedicalWaste->save();
        return response()->json(['message' => 'Bio-Medical Waste updated successfully', 'data' => $bioMedicalWaste], 200);
    }
}

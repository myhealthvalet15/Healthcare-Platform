<?php

namespace App\Http\Controllers\V1Controllers\Drugs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Drugs\Drugtype;
use Illuminate\Database\QueryException;

class DrugTypeController extends Controller
{
    public function fetchAllDrugtypes()
    {
       
        try {
            $drugtypes = Drugtype::all();
            return response()->json(['data' => $drugtypes], 200);

        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json([
                    'result' => 'failed',
                    'message' => 'Fetched Drug Types Successfully.',
                    'error' => $e->getMessage()
                ], 400);
            }
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while fetching the drug type.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addNewDrugtype(Request $request)
    {
        $validatedData = $request->validate([
            'drug_type_name' => 'required|string',
            'status' => 'nullable|boolean',
            
        ]);
        $drugtype = new Drugtype();
        $drugtype->drug_type_name = $validatedData['drug_type_name'];
        $drugtype->status = $validatedData['status'] ?? 0;
        
        $drugtype->save();
        return response()->json(['message' => 'Drug type created successfully', 'data' => $drugtype], 201);
    }
    public function deleteDrugtypeById($drugTypeId){
        try {
            $drugtype = Drugtype::where('id', $drugTypeId)->first();
            if (!$drugtype) {
                return response()->json(['result' => 'failed', 'message' => 'Drug Type not found'], 404);
            }
            
            $drugtype->delete();
            return response()->json(['result' => 'success', 'message' => 'Drug type deleted successfully'], 200);
        } catch (QueryException $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while deleting the drug type.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function editDrugtypeById(Request $request, $drugTypeId)
    {
       // return $request;
        $drugtype = Drugtype::where('id', $drugTypeId)->first();
        if (!$drugtype) {
            return response()->json(['message' => 'Drug type not found'], 404);
        }
        $validatedData = $request->validate([
            'drug_type_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
        $drugtype->drug_type_name = $validatedData['drug_type_name'];
        $drugtype->status = $validatedData['status'];
        $drugtype->save();
        return response()->json(['message' => 'Drug type updated successfully', 'data' => $drugtype], 200);
    }  
}

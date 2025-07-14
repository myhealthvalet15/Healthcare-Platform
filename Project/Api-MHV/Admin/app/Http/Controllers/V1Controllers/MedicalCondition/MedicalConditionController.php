<?php

namespace App\Http\Controllers\V1Controllers\MedicalCondition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Medicalcondition\Medicalcondition;
use Illuminate\Database\QueryException;

class MedicalConditionController extends Controller
{
    public function fetchAllMedicalCondition()
    {
       
        try {
            $medicalcondition = Medicalcondition::all();
            return response()->json(['data' => $medicalcondition], 200);

        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json([
                    'result' => 'failed',
                    'message' => 'Fetched Medical Condition Successfully.',
                    'error' => $e->getMessage()
                ], 400);
            }
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while fetching the medical condition.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addNewMedicalCondition(Request $request)
    {
        $validatedData = $request->validate([
            'condition_name' => 'required|string',
            'status' => 'nullable|boolean',
            
        ]);
        $medicalcondition = new Medicalcondition();
        $medicalcondition->condition_name = $validatedData['condition_name'];
        $medicalcondition->status = $validatedData['status'] ?? 0;
        
        $medicalcondition->save();
        return response()->json(['message' => 'Medical condition created successfully', 'data' => $medicalcondition], 201);
    }
    public function deleteMedicalConditionById($meidcalConditionId){
       // return $meidcalConditionId;
        try {
        
            $medicalcondition = Medicalcondition::where('condition_id', $meidcalConditionId)->first();
            if (!$medicalcondition) {
                return response()->json(['result' => 'failed', 'message' => 'Medical Condition not found'], 404);
            }
            
            $medicalcondition->delete();
            return response()->json(['result' => 'success', 'message' => 'Medical Condition deleted successfullys'], 200);
        } catch (QueryException $e) {
            //return $meidcalConditionId;
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while deleting the medical condition.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function editMedicalConditionById(Request $request, $meidcalConditionId)
    {
       // return $request;
        $medicalcondition = Medicalcondition::where('condition_id', $meidcalConditionId)->first();
        if (!$medicalcondition) {
            return response()->json(['message' => 'Medical condition not found'], 404);
        }
        $validatedData = $request->validate([
            'condition_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
        $medicalcondition->condition_name = $validatedData['condition_name'];
        $medicalcondition->status = $validatedData['status'];
        $medicalcondition->save();
        return response()->json(['message' => 'Medical condition updated successfully', 'data' => $medicalcondition], 200);
    }  
}

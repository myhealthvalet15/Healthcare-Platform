<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Outpatient;
use Exception;

class InjuryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $componentTypes = [1, 2, 3, 4, 5, 6, 7, 8, 99];
            $outpatients = [];
    
            foreach ($componentTypes as $type) {
                $outpatients["injury_$type"] = Outpatient::where('op_component_type', $type)
                    ->get() // Change pagination count if needed
                    ->toArray();
            }
    
            return response()->json([
                'success' => true,
                'data' => $outpatients,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching outpatient data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve outpatient data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
  

    public function create(Request $request)
    {
        try 
        {
            $validatedData = $request->validate([
                'op_component_name' => 'required|string',
                'op_component_type' => 'required|string',
                'active_status' => 'required|boolean'
            ]);
            $outpatient = Outpatient::create([
                'op_component_name' => $validatedData['op_component_name'],
                'op_component_type' => $validatedData['op_component_type'],
                'active_status' => $validatedData['active_status']
            ]);
            return response()->json($outpatient, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create outpatient', 'error' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'op_component_name' => 'required|string',
                'op_component_type' => 'required|string',
                
            ]);

            $outpatient = Outpatient::findOrFail($id);
            $outpatient->update([
                'op_component_name' => $request->op_component_name,
                'op_component_type' => $request->op_component_type,
               
            ]);

            return response()->json($outpatient);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update outpatient'], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $outpatient = Outpatient::findOrFail($id);
            $outpatient->delete();

            return response()->json(['message' => 'Outpatient deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete outpatient'], 500);
        }
    }

}

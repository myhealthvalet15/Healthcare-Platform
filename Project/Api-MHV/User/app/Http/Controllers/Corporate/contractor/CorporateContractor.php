<?php

namespace App\Http\Controllers\Corporate\contractor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\corporate_contractors;
use Illuminate\Support\Facades\Validator;
use Exception;

class CorporateContractor extends Controller
{
    public function viewContractors($location_id, Request $request)
    {
        if (!$location_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Location ID"
            ]);
        }
        try {
            $data = corporate_contractors::where('location_id', $location_id)
                ->select('corporate_contractors_id', 'contractor_name', 'email', 'address', 'active_status')
                ->orderBy('corporate_contractors_id', 'desc')
                ->get();
            if ($data->isEmpty()) {
                return response()->json([
                    "result" => false,
                    "message" => "No data found for the provided Location ID"
                ]);
            }
            $contractors = $data->map(function ($contractor) {
                return [
                    'corporate_contractors_id' => $contractor->corporate_contractors_id,
                    'contractor_name' => $contractor->contractor_name,
                    'contractor_email' => $contractor->email,
                    'contractor_address' => $contractor->address,
                    'active_status' => $contractor->active_status,
                ];
            });
            return response()->json([
                "result" => true,
                "data" => $contractors
            ]);
        } catch (Exception $e) {
            Log::error('Error in fetching data: ' . $e->getMessage());
            return response()->json([
                "result" => false,
                "message" => "An error occurred while fetching data"
            ]);
        }
    }
    public function createContractors(Request $request)
    {
        try {
            // return $request;
            $request->validate([
                'contractor_name' => 'required|string',
                'contractor_email' => 'required|email|unique:corporate_contractors,email',
                'contractor_address' => 'required|string',
                'active_status' => 'nullable|boolean',
            ]);
            // Get the last highest ID and increment it by 1
            // Check if the email already exists in the database
            $existingContractor = corporate_contractors::where('email', $request->contractor_email)->first();

            if ($existingContractor) {
                return response()->json([
                    'error' => true,
                    'message' => 'A contractor with this email already exists.'
                ], 400); // Return a bad request if email already exists
            }
            $lastId = corporate_contractors::max('corporate_contractors_id') ?? 0;
            $newId = $lastId + 1;
            $contractor = corporate_contractors::create([
                'corporate_contractors_id' => $newId, // Assign the incremented ID
                'contractor_name' =>  ucwords($request->contractor_name),
                'email' =>  ucwords($request->contractor_email),
                'address' =>  ucwords($request->contractor_address),
                'location_id' => $request->location_id,
                'active_status' => $request->active_status,

            ]);

            return response()->json(['success' => true, 'message' => 'Contractor Addedd Successfullydfdfsdfdf'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create employee types', 'error' => $e->getMessage()], 500);
        }
    }



    public function modifyContractors(Request $request, $id)
    {
        Log::info($request->all());
       // return $request;
        $validator = Validator::make($request->all(), [
            'contractor_name' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'active_status' => 'nullable|boolean',
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
       
        try {
            $modifiedContractor = corporate_contractors::findOrFail($id);
            Log::info('Contractor data before update:', $modifiedContractor->toArray());

           
            $modifiedContractor->update([
                'contractor_name' => $request->contractor_name,
                'email' => $request->email,
                'address' => $request->address,
                'active_status' => $request->active_status,
            ]);
             // Refresh the model to get the latest data
    $modifiedContractor->refresh();

    // Log updated data
    Log::info('Contractor data after update:', $modifiedContractor->toArray());
            return response()->json([
                'success' => true,
                'data' => $modifiedContractor,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error updating Contractor record', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating record',
            ], 500);
        }
    }
    public function removeContractors($id)
    {
        try {
            $delete_contractor = corporate_contractors::findOrFail($id);
            $delete_contractor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting Contractor record', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting record',
            ], 500);
        }
    }
}

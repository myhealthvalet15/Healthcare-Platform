<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department\CorporateHl1;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class CorporateHl1Controller extends Controller
{
    public function index(Request $request)
    {
        //return 'Hello';
        try {
            $corporateHl1Records = CorporateHl1::all();
            //  return $corporateHl1Records;
            return response()->json([
                'success' => true,
                'data' => $corporateHl1Records,
            ], 200);

        } catch (Exception $e) {
            Log::error('Error fetching CorporateHl1 records', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error fetching records',
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required|string',
            'location_id' => 'required|string',
            'corporate_admin_user_id' => 'required|string',
            'hl1_name' => 'nullable',
            'hl1_code' => 'nullable',
            'active_status' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $corporateHl1 = CorporateHl1::create($validator->validated());
            return response()->json(['success' => true, 'data' => $corporateHl1], 201);
        } catch (\Exception $e) {
            Log::error('Error creating record: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating record'], 500);
        }
    }
    public function show($id)
    {
        try {
            $corporateHl1 = CorporateHl1::findOrFail($id);
            return response()->json(['success' => true, 'data' => $corporateHl1], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching record: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching record'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        //return 'Hi';
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'nullable|string',
            'location_id' => 'nullable|string',
            'corporate_admin_user_id' => 'nullable|string',
            'hl1_name' => 'nullable|string',
            'hl1_code' => 'nullable|string',
            'active_status' => 'nullable|boolean',
        ]);

        // If validation fails, return the error messages
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Try to find the CorporateHl1 record by ID
            $corporateHl1 = CorporateHl1::findOrFail($id);

            // Update the record with validated data
            $corporateHl1->update([
                'hl1_name' => $request->hl1_name,
                'hl1_code' => $request->hl1_code,
                'active_status' => $request->active_status,
            ]);

            // Return a success response with the updated data
            return response()->json([
                'success' => true,
                'data' => $corporateHl1,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Specific error handling for record not found
            Log::error('CorporateHl1 not found for update', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => "CorporateHl1 record with ID {$id} not found.",
            ], 404);
        } catch (\Exception $e) {
            // General error handling
            Log::error('Error updating CorporateHl1 record', [
                'id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),  // Log the input data for troubleshooting
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating HL1.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $corporateHl1 = CorporateHl1::findOrFail($id);
            $corporateHl1->delete();
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error deleting CorporateHl1 record', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record',
            ], 500);
        }
    }
}

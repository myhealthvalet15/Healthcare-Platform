<?php

namespace App\Http\Controllers\Others;

use App\Models\Others\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function getAllInventory($location_id, Request $request)
{
    try {
        $query = Inventory::where('location_id', $location_id);
        

        // Check for 'status' and filter accordingly
        if ($request->has('status') && $request->input('status') !== '') {
            $status = (int) $request->input('status'); // Ensure it's an integer
            Log::info('Filter by status: ' . $status);
            $query->where('in_use', $status); // Assuming 'in_use' column exists in the table
        }

        // Log the query and bindings for debugging
        Log::info($query->toSql(), $query->getBindings());

        // Execute the query and get the results
        $inventory = $query->get();

        // If no records were found, return a response with an empty array and a message
        if ($inventory->isEmpty()) {
            return response()->json([
                'result' => true,
                'message' => 'No inventory found for the given criteria',
                'data' => [] // Empty data array for the frontend to handle
            ], 200); // 200 OK but with an empty data array
        }

        // Return the data with a successful response
        return response()->json([
            'result' => true,
            'data' => $inventory
        ], 200);
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        Log::error('Failed to retrieve inventory: ' . $e->getMessage());

        return response()->json([
            'result' => false,
            'message' => 'An error occurred while retrieving the inventory.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function addInventorytoDB(Request $request)
    {
       //return $request;
        // Validate incoming request according to the schema
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'purchase_order' => 'required|string|max:255',
            'equipment_name' => 'required|string|max:255',
            'equipment_code' => 'required|string',
            'equipment_lifetime' => 'required|integer',
            'manufacture_date' => 'required',
            'equipment_cost' => 'required',
            'vendors' => 'required',
            'calibrated_date' => 'required',
           
            'equipment_cost' => 'required|numeric',

        ]);

        // If validation fails, return errors
         if ($validator->fails()) {
            return response()->json(['error' => false, 'errors' => $validator->errors()], 422);
        }

        try {
           // return $request;
            // Create a new DrugTemplate record with the validated data
            $InventoryData = $validator->validated();           
            $InventoryData['corporate_id'] = $request->corporate_id;
            $InventoryData['location_id'] = $request->location_id;
            $InventoryData['in_use'] = 0;

            $InventoryData['date'] = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            $InventoryData['calibrated_date'] = Carbon::createFromFormat('d-m-Y', $request->calibrated_date)->format('Y-m-d');
            $InventoryData['manufacture_date'] = Carbon::createFromFormat('d-m-Y', $request->manufacture_date)->format('Y-m-d');
          

           // return  $InventoryData;
            
           // $InventoryData['calibrated_date'] = \Carbon\Carbon::createFromFormat('d/m/Y',  $request->calibrated_date)->format('Y-m-d');
            $InventoryData['manufacturers'] = $request->manufacturers;
            $InventoryData['next_calibration_date'] = NULL;
            $InventoryData['calibration_comments'] = '';
           
             Log::info('Bhavas Inventory data', $InventoryData);

            // Create and save the new DrugTemplate record
            $inventory = Inventory::create($InventoryData);

            // Return success response
            return response()->json(['success' => true, 'data' => $inventory], 201);
        } catch (\Exception $e) {
            // Log the exception message with more details
            Log::error('Failed to save Inventory', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()  // Log the request data
            ]);

            return response()->json(['success' => false, 'message' => 'An error occurred while saving the drug template.'], 500);
        }
    }


    public function getInventoryById($id)
    {
        //return $id;
        try {
            $inventory = Inventory::where('corporate_inventory_id', $id)->first(); // Retrieve single record for given ID
            if (!$inventory) {
                return response()->json(['message' => 'Inventory not found'], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $inventory, // Return the required drug template data
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve inventory', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve inventory'], 500);
        }
    }
    public function updateInventory(Request $request, $id)
{
    // Map 'comments' to 'calibration_comments' if it exists
    if ($request->has('comments')) {
        $request->merge(['calibration_comments' => $request->input('comments')]);
    }

    // Map 'status' to 'in_use' if it exists
    if ($request->has('status')) {
        $in_use = $request->input('status') == 'active' ? true : false;
        $request->merge(['in_use' => $in_use]);
    }

    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'calibrated_date' => 'required|date',
        'next_calibration_date' => 'required|date',
        'calibration_comments' => 'required|string|max:500',
        'in_use' => 'required|boolean', // assuming in_use is a boolean field
    ]);

    // If validation fails, return errors
    if ($validator->fails()) {
        return response()->json(['error' => true, 'errors' => $validator->errors()], 422);
    }

    try {
        // Get the existing inventory by ID
        $inventory = Inventory::findOrFail($id);

        // Get the validated request data
        $validatedData = $validator->validated();

        // Format the date fields
        $validatedData['calibrated_date'] = Carbon::createFromFormat('d-m-Y', $request->calibrated_date)->format('Y-m-d');
        $validatedData['next_calibration_date'] = Carbon::createFromFormat('d-m-Y', $request->next_calibration_date)->format('Y-m-d');

        // Get the existing calibration history, or initialize an empty array
        $calibrationHistory = $inventory->calibration_history ?? [];

        // Add the new calibration data to the history
        $calibrationHistory[] = [
            'calibrated_date' => $validatedData['calibrated_date'], // use the new calibrated_date
            'calibration_comments' => $validatedData['calibration_comments'], // use the new comment
            'in_use' => $validatedData['in_use'], // use the new status (in_use)
            'updated_at' => now(),  // Store the date of this history entry
        ];
       // return $calibrationHistory;
        // Update the inventory data with the new calibration history
        Log::info('Calibration History before saving:', ['calibrationHistory' => $calibrationHistory]);

// Update the inventory data


$inventory->calibration_history = $calibrationHistory;
$inventory->next_calibration_date = $validatedData['next_calibration_date'];
$inventory->in_use = $validatedData['in_use'];
$inventory->calibrated_date = $validatedData['calibrated_date'];
$inventory->calibration_comments = $validatedData['calibration_comments'];
$inventory->save();       
Log::info('Updated inventory:', ['inventory' => $inventory]);
        // Return a success response with the updated inventory data
        return response()->json(['success' => true, 'data' => $inventory], 200);
    } catch (\Exception $e) {
        // Log the error details for debugging
        Log::error('Failed to update Inventory', [
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'request_data' => $request->all()  // Log the incoming request data
        ]);

        // Return a response indicating failure
        return response()->json(['success' => false, 'message' => 'An error occurred while updating the inventory.'], 500);
    }
}

     
// In InventoryController

public function getCalibrationHistory($inventoryId)
{
    try {
        // Fetch the inventory item
        $inventory = Inventory::findOrFail($inventoryId);
        
        // Return calibration history as JSON
        return response()->json([
            'calibration_history' => $inventory->calibration_history
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Unable to fetch calibration history',
            'message' => $e->getMessage()
        ], 500);
    }
}

}

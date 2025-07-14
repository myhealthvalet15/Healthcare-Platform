<?php

namespace App\Http\Controllers\DrugTemplate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DrugTemplate;
use App\Models\Drugs\Drugtype;
use App\Models\Drugs\Drugingredient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class DrugTemplateController extends Controller
{
    public function getAllDrugTemplates($location_id, Request $request)
    {
        try {
            $drugtemplates = DrugTemplate::all();
            $drugtemplates = DrugTemplate::where('location_id', $location_id)->get();
            return response()->json(['data' => $drugtemplates], 200);
        } catch (Exception $e) {
            Log::error('Failed to retrieve drug templates', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve drug template'], 500);
        }
    }
    public function getDrugTypesAndIngredients(Request $request)
    {

        try {

            $drugTypes = Drugtype::all();

            // Fetch all drug ingredients
            $drugIngredients = Drugingredient::all();

            // Return both drug types and ingredients as JSON
            return response()->json([
                'drugTypes' => $drugTypes,
                 'drugIngredients' => $drugIngredients
            ], 200);
        } catch (\Exception $e) {
            // Log the error and return a generic message
            Log::error('Failed to retrieve drug types and ingredients', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve drug types and ingredients'], 500);
        }
    }
    public function addDrugTemplate(Request $request)
    {
        //return $request;
        $ingredients = implode(',', $request->drug_ingredients);
        // Validate incoming request according to the schema
        $validator = Validator::make($request->all(), [
            'drug_name' => 'required|string|max:255',
            'drug_manufacturer' => 'required|string|max:255',
            'drug_type' => 'required|integer',
            //'drug_ingredients' => 'nullable|string',  // Now drug_ingredients will always be a string or NULL
            'drug_strength' => 'required|string|max:255',
            'restock_alert_count' => 'required|integer',
            'crd' => 'required|string|max:255',
            'schedule' => 'required|string|max:255',
            'id_no' => 'required|integer',
            'hsn_code' => 'required|string|max:255',
            'unit_issue' => 'required|string|max:255',
            'amount_per_strip' => 'required|numeric',  // float in schema
            'tablet_in_strip' => 'required|integer',  // integer in schema
            'amount_per_tab' => 'required|numeric',  // float in schema
            'discount' => 'required|numeric',  // float in schema
            'sgst' => 'required|numeric',  // float in schema
            'cgst' => 'required|numeric',  // float in schema
            'igst' => 'required|numeric',  // float in schema
            'bill_status' => 'required|string|max:255',
            'otc' => 'nullable|boolean',
            'crd' => 'nullable|boolean',
             // varchar(255) in schema
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['error' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Create a new DrugTemplate record with the validated data
            $drugTemplateData = $validator->validated();

            // Add session data
            $drugTemplateData['corporate_id'] = $request->corporate_id;
            $drugTemplateData['location_id'] = $request->location_id;
            $drugTemplateData['ohc'] = 1;  // Assuming default value for OHC
            $drugTemplateData['master_pharmacy_id'] = 1;
            // Assuming default value for master_pharmacy_id
            $drugTemplateData['drug_ingredient'] = $ingredients;
            // Log data being saved (to debug the values)
            Log::info('Location Id Testing saved:', $drugTemplateData);
            //return $drugTemplateData;
            // Create and save the new DrugTemplate record
            $drugTemplate = DrugTemplate::create($drugTemplateData);

            // Return success response
            return response()->json(['success' => true, 'data' => $drugTemplate], 201);
        } catch (\Exception $e) {
            // Log the exception message with more details
            Log::error('Failed to save drug template', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()  // Log the request data
            ]);

            return response()->json(['success' => false, 'message' => 'An error occurred while saving the drug template.'], 500);
        }
    }


    public function getDrugTemplatesById($id)
    {
        //return $id;
        try {
            $drugtemplates = DrugTemplate::where('drug_template_id', $id)->first(); // Retrieve single record for given ID
            if (!$drugtemplates) {
                return response()->json(['message' => 'Drug template not found'], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $drugtemplates, // Return the required drug template data
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve drug templates', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve drug template'], 500);
        }
    }
    public function updateDrugTemplate(Request $request, $id)
    {
       Log::info('Incoming request data:', $request->all());
  
        // Validate incoming request according to the schema
        $validator = Validator::make($request->all(), [
            'drug_name' => 'required|string|max:255',
            'drug_manufacturer' => 'required|string|max:255',
            'drug_type' => 'required|integer',
            //'drug_ingredients' => 'nullable|array', // now accepting array format for ingredients
            'drug_strength' => 'required|string|max:255',
            'restock_alert_count' => 'required|integer',
            'schedule' => 'required|string|max:255',
            'id_no' => 'required|integer',
            'hsn_code' => 'required|string|max:255',
            'unit_issue' => 'required|string|max:255',
            'amount_per_strip' => 'required|numeric',
            'tablet_in_strip' => 'required|integer',
            'amount_per_tab' => 'required|numeric',
            'discount' => 'required|numeric',
            'sgst' => 'required|numeric',
            'cgst' => 'required|numeric',
            'igst' => 'required|numeric',
            'bill_status' => 'required|boolean',
            'otc' => 'nullable|boolean',
            'crd' => 'nullable|boolean',
             // changed to boolean since it's a checkbox
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json(['error' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Get the existing drug template by ID
            $drugTemplate = DrugTemplate::findOrFail($id);

            // Prepare the ingredients (handling array from the request)
            $ingredients = implode(',', $request->drug_ingredients);

            // Update the drug template data
            $drugTemplateData = $validator->validated();
            $drugTemplateData['corporate_id'] = $request->corporate_id;
            $drugTemplateData['location_id'] = $request->location_id;
            $drugTemplateData['drug_ingredient'] = $ingredients; // update ingredients
            $drugTemplateData['bill_status'] = $request->bill_status ? 1 : 0;  // convert to 1 or 0 based on checkbox

           // Log data being updated (for debugging purposes)
           // Log::info('Updating DrugTemplate with data:', $drugTemplateData);
          //dd($drugTemplateData);
            // Update the record in the database
             $updated = $drugTemplate->update($drugTemplateData);
             //Log::info('Update Status:', ['updated' => $updated]);



            // Return success response
            // return $response;
            return response()->json(['success' => true, 'data' => $drugTemplate], 200);
        } catch (\Exception $e) {
            // Log the exception message with more details
            Log::error('Failed to update drug template', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()  // Log the request data
            ]);

            return response()->json(['success' => false, 'message' => 'An error occurred while updating the drug template.'], 500);
        }
    }
}

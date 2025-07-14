<?php

namespace App\Http\Controllers\V1Controllers\Forms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Forms\CorporateForms;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CorporateFormController extends Controller
{
  

public function displayAllCorporateForms()
{
    try {
        $corporateForms = DB::table('corporate_forms')
            ->join('states', 'corporate_forms.state', '=', 'states.id')
            ->select('corporate_forms.*', 'states.statename')
            ->get();

        return response()->json(['data' => $corporateForms], 200);
    } catch (QueryException $e) {
        return response()->json([
            'result' => 'failed',
            'message' => 'An error occurred while fetching the forms.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function displayAllStates()
{
    try {
        $states = DB::table('states')
            ->select('id', 'statename')
            ->get();

        return response()->json(['data' => $states], 200);
    } catch (QueryException $e) {
        return response()->json([
            'result' => 'failed',
            'message' => 'An error occurred while fetching the states.',
            'error' => $e->getMessage()
        ], 500);
    }
}


   public function addNewForm(Request $request)
{
    // Validate input
    $validatedData = $request->validate([
        'form_name' => 'required|string|max:255',
        'statename' => 'required|integer',
        'status' => 'required|boolean',
    ]);

    // Check if a record with the same form_name and state already exists
    $existingForm = corporateForms::where('form_name', $validatedData['form_name'])
                                  ->where('state', $validatedData['statename'])
                                  ->first();

    if ($existingForm) {
        return response()->json([
            'message' => 'A form with the same name and state already exists.'
        ], 409); // 409 Conflict
    }

    // Create a new form entry
    $forms = new corporateForms();
    $forms->form_name = $validatedData['form_name'];
    $forms->state = $validatedData['statename'];
    $forms->status = $validatedData['status'] ?? 0;

    // Save the form to the database
    $forms->save();

    // Return success response
    return response()->json([
        'message' => 'Form created successfully',
        'data' => $forms
    ], 201);
}


 public function updateFormById(Request $request, $formId)
{
    // Find the existing form
    $forms = corporateForms::where('corporate_form_id', $formId)->first();
    if (!$forms) {
        return response()->json(['message' => 'Form not found'], 404);
    }

    // Validate the incoming request
    $validatedData = $request->validate([
        'form_name' => 'required|string|max:255',
        'statename' => 'required|integer',
        'status' => 'required|boolean',
    ]);

    // Check for duplicate (excluding the current form)
    $existingForm = corporateForms::where('form_name', $validatedData['form_name'])
                                  ->where('state', $validatedData['statename'])
                                  ->where('corporate_form_id', '!=', $formId)
                                  ->first();

    if ($existingForm) {
        return response()->json([
            'message' => 'A form with the same name and state already exists.'
        ], 409); // 409 Conflict
    }

    // Update the form
    $forms->form_name = $validatedData['form_name'];
    $forms->state = $validatedData['statename'];
    $forms->status = $validatedData['status'] ?? 0;
    $forms->save();

    // Return success response
    return response()->json([
        'message' => 'Form updated successfully',
        'data' => $forms
    ], 200);
}

    public function deleteFormsById($Id){
       //return $Id; 
        try {
            $forms = CorporateForms::where('corporate_form_id', $Id)->first();
            if (!$forms) {
                return response()->json(['result' => 'failed', 'message' => 'Forms not found'], 404);
            }
            
            $forms->delete();
            return response()->json(['result' => 'success', 'message' => 'Forms deleted successfully'], 200);
        } catch (QueryException $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while deleting the forms.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   
}

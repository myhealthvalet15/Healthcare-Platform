<?php

namespace App\Http\Controllers\V1Controllers\Drugs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Drugs\Drugingredient;
use Illuminate\Database\QueryException;

class DrugIngredientController extends Controller
{
    public function displayDrugIngredient()
    {
        //return 'Hi';
        try {
            $drugingredients = Drugingredient::all();
            //return $drugingredients;
            return response()->json(['data' => $drugingredients], 200);

        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json([
                    'result' => 'failed',
                    'message' => 'Fetched Ingredients Successfully.',
                    'error' => $e->getMessage()
                ], 400);
            }
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while fetching the ingredient.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addDrugIngredient(Request $request)
{
    $validatedData = $request->validate([
        'drug_ingredients' => 'required|string',
        'status' => 'nullable|boolean',
    ]);

    // Create a new drug ingredient
    $ingredient = new Drugingredient();
    $ingredient->drug_ingredients = $validatedData['drug_ingredients'];
    $ingredient->status = $validatedData['status'] ?? 0;
    
    // Save the ingredient to the database
    $ingredient->save();

    // Return the ingredient along with the ID
    return response()->json(['message' => 'Ingredients created successfully', 'data' => $ingredient], 201);
}

    public function deleteDrugIngredient($ingredientId){
        try {
            $ingredient = Drugingredient::where('id', $ingredientId)->first();
            if (!$ingredient) {
                return response()->json(['result' => 'failed', 'message' => 'Ingredient not found'], 404);
            }
            
            $ingredient->delete();
            return response()->json(['result' => 'success', 'message' => 'Ingredients deleted successfully'], 200);
        } catch (QueryException $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while deleting the ingredients.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateDrugIngredient(Request $request, $ingredientId)
    {
       // return $request;
        $ingredient = Drugingredient::where('id', $ingredientId)->first();
        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }
        $validatedData = $request->validate([
            'drug_ingredients' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);
        $ingredient->drug_ingredients = $validatedData['drug_ingredients'];
        $ingredient->status = $validatedData['status'];
        $ingredient->save();
        return response()->json(['message' => 'Ingredients updated successfully', 'data' => $ingredient], 200);
    }  
}

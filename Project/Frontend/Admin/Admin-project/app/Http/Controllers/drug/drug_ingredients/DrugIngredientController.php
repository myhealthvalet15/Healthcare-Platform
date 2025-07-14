<?php

namespace App\Http\Controllers\drug\drug_ingredients;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class DrugIngredientController extends Controller
{


    public function ingredients(Request $request)
    {
        $drugingredients = $this->getAllIngredients($request);
        return view('content.drug.drug_ingredients', ['drugingredients' => $drugingredients]);
    }
    public function getAllIngredients(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/getAllingredients');

            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json($response['data']);
            }
            return response()->json(['error' => 'error to fetch data'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function addingredients(Request $request)
    {
        try {
            $validated = $request->validate([
                'drug_ingredients' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/addingredients', [
                'drug_ingredients' => $validated['drug_ingredients'],
                'status' => $validated['status'],
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => 'Medical condition added successfully']);
            } else {
                return response()->json(['result' => 'error', 'message' => 'error to medical condition', 'details' => $response->body()]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Fill all the details',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['result' => "error", 'message' => 'error: ' . $e->getMessage()]);
        }
    }
    public function editIngredients(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'drug_ingredients' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),

            ])->put("https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/editIngredients/{$id}", [
                'drug_ingredients' => $validated['drug_ingredients'],
                'status' => $validated['status']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Ingredients updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'error to update ingredients',
                    'details' => $response->body()
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function deleteIngredients(Request $request, $id)
    {
        //$id = 1;
        // return 'hi';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/deleteIngredients/' . $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Ingredients deleted successfully']);
            }
            return response()->json(['result' => 'error', 'message' => $response['message']], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }
}

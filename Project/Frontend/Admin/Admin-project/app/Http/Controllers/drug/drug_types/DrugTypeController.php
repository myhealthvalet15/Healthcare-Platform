<?php

namespace App\Http\Controllers\drug\drug_types;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DrugTypeController extends Controller
{
    public function drugtypes(Request $request)
    {
        $drugtypes = $this->getAllDrugtypes($request);
        return view('content.drug.drug_types', ['drugtypes' => $drugtypes]);
    }
    public function getAllDrugtypes(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/getAllDrugtypes');
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
    public function addDrugtype(Request $request)
    {
        try {
            $validated = $request->validate([
                'drug_type_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/addDrugtype', [
                'drug_type_name' => $validated['drug_type_name'],
                'status' => $validated['status'],
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => 'Drug type added successfully']);
            } else {
                return response()->json(['result' => 'error', 'message' => 'error to add drug type', 'details' => $response->body()]);
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
    public function editDrugtype(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'drug_type_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/editDrugtype/{$id}", [
                'drug_type_name' => $validated['drug_type_name'],
                'status' => $validated['status']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Drug type updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'error to update drug type',
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
    public function deleteDrugtype(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/drugs-stubs/drugs-stubs/deleteDrugtype/' . $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Drug type deleted successfully']);
            }
            return response()->json(['result' => 'error', 'message' => $response['message']], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }
}

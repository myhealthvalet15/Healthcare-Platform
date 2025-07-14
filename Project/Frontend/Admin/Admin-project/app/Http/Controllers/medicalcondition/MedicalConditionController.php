<?php

namespace App\Http\Controllers\medicalcondition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


class MedicalConditionController extends Controller
{  
     
    public function medicalcondition(Request $request)
    {
        $medical_condition = $this->getAllMedicalCondition($request);
        return view('content.medicalcondition.medical_condition', ['medical_condition' => $medical_condition]);
        
    }
    public function getAllMedicalCondition(Request $request)
    {
        //return 'hello';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ])->get('https://api-admin.hygeiaes.com/V1/medicalcondition-stubs/medicalcondition-stubs/getAllMedicalCondition');
            
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
    public function addMedicalCondition(Request $request)
    {
        try {
            $validated = $request->validate([
                'condition_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/medicalcondition-stubs/medicalcondition-stubs/addMedicalCondition', [
                'condition_name' => $validated['condition_name'],
                'status' => $validated['status'],
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error','message' => 'Unauthenticated'], 401);
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
    public function editMedicalCondition(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'condition_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                 
            ])->put("https://api-admin.hygeiaes.com/V1/medicalcondition-stubs/medicalcondition-stubs/editMedicalCondition/{$id}",
             [
                'condition_name' => $validated['condition_name'],
                'status' => $validated['status']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Medical Condition updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'error to update medical condition',
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
    public function deleteMedicalCondition(Request $request, $id)
    {
        //$id = 1;
        //return $id;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/medicalcondition-stubs/medicalcondition-stubs/deleteMedicalCondition/'. $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Medical condition deleted successfully']);
            }
            return response()->json(['result' => 'error', 'message' => $response['message']], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }

   
}

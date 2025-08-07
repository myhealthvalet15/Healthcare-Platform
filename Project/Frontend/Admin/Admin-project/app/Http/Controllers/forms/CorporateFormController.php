<?php

namespace App\Http\Controllers\forms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CorporateFormController extends Controller
{
    public function listCorporateForms(Request $request)
    {
        return view('content.forms.list-forms');
    }
    public function getAllForms(Request $request)
    {
        // return 'Hello';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/forms-stubs/forms-stubs/getAllCorporateForms');

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
    public function getAllStates(Request $request)
    {
        // return 'Hello';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/forms-stubs/forms-stubs/getAllStates');

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
    public function addNewForm(Request $request)
    {
        //return 'Hello';
        //Log::info('Request data:', $request->all());
        try {
            $validated = $request->validate([
                'form_name' => 'required|string|max:255',
                'statename' => 'required|integer',
                'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/forms-stubs/forms-stubs/addNewForm', [
                'form_name' => $validated['form_name'],
                'statename' => $validated['statename'],
                'status' => $validated['status'],
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => 'Form added successfully']);
            } else {
                return response()->json(['result' => 'error', 'message' => 'A form with the same name and state already exists.', 'details' => $response->body()]);
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
    public function editForm(Request $request, $id)
    {
        try {
            $validated = $request->validate([
               'form_name' => 'required|string|max:255',
               'statename' => 'required|integer',
               'status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-admin.hygeiaes.com/V1/forms-stubs/forms-stubs/updateForm/{$id}", [
               'form_name' => $validated['form_name'],
                'statename' => $validated['statename'],
                'status' => $validated['status'],
            ]);

            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Form updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'A form with the same name and state already exists',
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

    public function deleteForms(Request $request, $id)
    {
        //return $id;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/forms-stubs/forms-stubs/deleteForms/' . $id);

            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Forms deleted successfully']);
            }
            return response()->json(['result' => 'error', 'message' => $response['message']], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }

}

<?php

namespace App\Http\Controllers\hra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class templateController extends Controller
{
    public function index(Request $request)
    {
        $templates = $this->getAllTemplates($request);
        return view('content.hra.show-templates', ['templates' => $templates]);
    }

    public function getAllTemplates(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getAllTemplates');

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


    public function addNewTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'template_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/hra/templates/addTemplate', [
                'template_name' => $validated['template_name'],
                'active_status' => $validated['active_status'],
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => 'Template added successfully']);
            } else {
                return response()->json(['result' => 'error', 'message' => $response['message']]);
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

    public function deleteTemplate(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/hra/templates/deleteTemplate/' . $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Template deleted successfully']);
            }

            return response()->json(['result' => 'error', 'message' => 'error in deleting template'], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }

    public function editTemplate(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'template_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-admin.hygeiaes.com/V1/hra/templates/editTemplate/{$id}", [
                'template_name' => $validated['template_name'],
                'active_status' => $validated['active_status']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Template updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'error to update template',
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

    public function publishTemplate(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|integer',
            'total_adjustment_value' => 'required|integer',
            'factors' => 'required|array|min:1',
            'factors.*.factor_id' => 'required|integer',
            'factors.*.max_value' => 'required|integer',
            'factors.*.factor_adjustment_value' => 'required|integer',
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post("https://api-admin.hygeiaes.com/V1/hra/templates/{$validated['template_id']}/publishTemplate", [
            'total_adjustment_value' => $validated['total_adjustment_value'],
            'factors' => $validated['factors'],
        ]);
        if ($response->successful()) {
            return response()->json([
                'result' => true,
                'message' => 'Template updated successfully'
            ]);
        } else {
            return response()->json([
                'result' => false,
                'message' => $response['message']
            ], $response->status());
        }
    }
}

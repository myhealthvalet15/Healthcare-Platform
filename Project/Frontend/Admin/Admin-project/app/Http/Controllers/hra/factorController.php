<?php
namespace App\Http\Controllers\hra;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
class factorController extends Controller
{
    public function factor(Request $request)
    {
        $factors = $this->getAllFactors($request);
        return view('content.hra.show-factors', ['factors' => $factors]);
    }
    public function getAllFactors(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ])->get('https://api-admin.hygeiaes.com/V1/hra/factors/getallfactors');
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
    public function addNewFactor(Request $request)
    {
        try {
            $validated = $request->validate([
                'factor_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/hra/factors/add-factors', [
                'factor_name' => $validated['factor_name'],
                'active_status' => $validated['active_status'],
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error','message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => 'Factor added successfully']);
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
    public function deleteFactor(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/hra/factors/deletefactor/' . $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Factor deleted successfully']);
            }
            return response()->json(['result' => 'error', 'message' => 'error in deleting factor'], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }
    public function editFactor(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'factor_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                 'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put("https://api-admin.hygeiaes.com/V1/hra/factors/editfactor/{$id}", [
                'new_factor_name' => $validated['factor_name'],
                'active_status' => $validated['active_status']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json([
                    'result' => 'success',
                    'message' => 'Factor updated successfully'
                ]);
            } else {
                return response()->json([
                    'result' => 'error',
                    'message' => 'error to update factor',
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
}

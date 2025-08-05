<?php

namespace App\Http\Controllers\doctor_qualification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $apiResponse = Http::post('https://api-admin.hygeiaes.com/api/doctor/index', [
                'access_token' => $request->cookie('access_token')
            ]);

            $data = $apiResponse->json('data');
            $doctors = $data ?? [];

            return view('content.doctor_qualifications.doctor_qualification', compact('doctors'));
        } catch (\Exception $e) {
            Log::error('Doctor index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load doctor qualifications.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'qualification_name' => 'required|string|max:255',
            'qualification_type' => 'required',
            'active_status' => 'required',
        ]);

        try {
            $apiResponse = Http::post('https://api-admin.hygeiaes.com/api/doctor/add', [
                'qualification_name' => $request->qualification_name,
                'qualification_type' => $request->qualification_type,
                'active_status' => $request->active_status,
                'access_token' => $request->cookie('access_token'),
            ]);

            if ($apiResponse->json('success')) {
                return redirect()->route('doctor.index')->with('success', 'Doctor qualification added successfully!');
            } else {
                return back()->with('error', 'Failed to add Doctor qualification.');
            }
        } catch (\Exception $e) {
            Log::error('Add doctor qualification error: ' . $e->getMessage());
            return back()->with('error', 'Unable to add Doctor qualification. Please try again later.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'qualification_name' => 'required|string|max:255',
            'qualification_id' => 'required',
            'active_status' => 'required',
        ]);

        try {
            $apiResponse = Http::post("https://api-admin.hygeiaes.com/api/doctor/update/{$request->qualification_id}", [
                'qualification_name' => $request->qualification_name,
                'active_status' => $request->active_status,
                'access_token' => $request->cookie('access_token'),
            ]);

            if ($apiResponse->json('success')) {
                return response()->json(['message' => 'Doctor qualification updated successfully!']);
            } else {
                return response()->json(['error' => 'Failed to update Doctor qualification.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Update doctor qualification error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update doctor qualification.'], 500);
        }
    }
}

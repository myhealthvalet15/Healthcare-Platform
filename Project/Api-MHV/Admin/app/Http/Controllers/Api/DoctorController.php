<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctorqualification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class DoctorController extends Controller
{
    public function index() {
        try {
            $doctors = Doctorqualification::all();
            return response()->json(['data' => $doctors], 200);
        } catch (Exception $e) {
            Log::error('Error fetching doctor qualifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch doctor qualifications.'], 500);
        }
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'qualification_name' => 'required|string|max:255',
            'qualification_type' => 'required|string|max:255',
            'active_status'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $doctor = Doctorqualification::create([
                'qualification_name' => $request->input('qualification_name'),
                'qualification_type' => $request->input('qualification_type'),
                'active_status' => $request->input('active_status')
            ]);
            return response()->json(['message' => 'Doctor qualification added successfully!', 'data' => $doctor], 201);
        } catch (Exception $e) {
            Log::error('Error adding doctor qualification: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add doctor qualification.'], 500);
        }
    }

    
    public function update(Request $request, $id) {
        
        $validator = Validator::make($request->all(), [
            'qualification_name' => 'required|string|max:255',
            'active_status'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $qualification = Doctorqualification::find($id);
            
            if (!$qualification) {
                return response()->json(['error' => 'Qualification not found.'], 404);
            }

            $qualification->qualification_name = $request->input('qualification_name');
            $qualification->active_status = $request->input('active_status');
            $qualification->save();

            return response()->json(['message' => 'Qualification updated successfully!', 'data' => $qualification], 200);
        } catch (Exception $e) {
            Log::error('Error updating doctor qualification: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update doctor qualification.'], 500);
        }
    }
    
}

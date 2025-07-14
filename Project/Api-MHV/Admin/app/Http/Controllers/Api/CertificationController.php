<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class CertificationController extends Controller
{
    public function getAllCertificates(Request $request)
    {
        $certificates = Certification::all();
        if ($certificates->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No certificates found.'
            ], 200);
        }
        return response()->json([
            'result' => true,
            'total_count' => $certificates->count(),
            'certificates' => $certificates
        ]);
    }

    public function index(Request $request)
    {

        try {
            // Get all certificates
            $certificates = Certification::all();

            // Log::info($certificates);


            // Check if certificates exist
            if ($certificates->isEmpty()) {
                return response()->json(['message' => 'No certificates found.'], 200); // Return a 404 with a message if no certificates
            }

            return response()->json([
                'total_count' => $certificates->count(),
                'certificates' => $certificates
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve certificates'], 500);
        }
    }


    public function show(Request $request, $id)
    {
        try {
            $certificate = Certification::findOrFail($id);
            return response()->json($certificate);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve certificate'], 500);
        }
    }


    public function create(Request $request)
    {
        // Log::info('Certificate creation request', $request->all());

        try {
            $validator = Validator::make($request->all(), [

                'certification_title' => 'required',
                'short_tag' => 'required',
                'content' => 'required',
                'conditions' => 'required',
                'color_conditions' => 'required',
                'active_status' => 'nullable',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $data = [
                'corporate_id' => 'MCBoAmzVFigh',
                'certification_title' => $request->certification_title,
                'short_tag' => $request->short_tag,
                'content' => $request->content,
                'condition' => $request->conditions,
                'color_condition' => $request->color_conditions,
                'active_status' => $request->active_status ?? 1,
            ];

            Log::debug('Prepared data for certification', $data);

            $certificate = Certification::create($data);

            return response()->json($certificate, 201);
        } catch (\Throwable $e) {
            Log::error('Error occurred during certificate creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'An error occurred while creating the certificate.'], 500);
        }
    }


    // public function create(Request $request)
    // {
    //     // Log::info('certificate list',$request->all());

    //     try {
    //         // Get all certificates
    //         $certificates = Certification::all();

    //         // Log::info($certificates);


    //         // Check if certificates exist
    //         if ($certificates->isEmpty()) {
    //             return response()->json(['message' => 'No certificates found.'],200); // Return a 404 with a message if no certificates
    //         }

    //         return response()->json([
    //             'total_count' => $certificates->count(), 
    //             'certificates' => $certificates          
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Failed to retrieve certificates'], 500);
    //     }

    // }





    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'nullable|integer',
            'certification_title' => 'required|string',
            'short_tag' => 'required|string',
            'content' => 'required|string',
            'condition' => 'required|array',
            'color_condition' => 'required|array',
            'active_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $certificate = Certification::findOrFail($id);
            $certificate->update($validator->validated());
            return response()->json($certificate);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update certificate'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $certificate = Certification::findOrFail($id);
            $certificate->delete();
            return response()->json('delete successfully', 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete certificate'], 500);
        }
    }
}

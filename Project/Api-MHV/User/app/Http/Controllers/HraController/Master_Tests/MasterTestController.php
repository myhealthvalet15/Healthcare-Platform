<?php

namespace App\Http\Controllers\HraController\Master_Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hra\Master_Tests\MasterTest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MasterTestController extends Controller
{
    public function addTest(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'test_name' => 'required|string|max:255',
            'test_desc' => 'nullable|string',
            'testgroup_id' => 'nullable|integer',
            'subgroup_id' => 'nullable|integer',
            'subsubgroup_id' => 'nullable|integer',
            'unit' => 'nullable|string',
            'range' => 'nullable|string',
            'm_min_max' => 'nullable|json',
            'f_min_max' => 'nullable|json',
            'type' => 'nullable|integer',
            'numeric_type' => 'nullable|integer',
            'condition' => 'nullable|json',
            'numeric_condition' => 'nullable|string',
            'normal_values' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $validator->errors(),
            ], 400);
        }

        try {
            // Check if a test with the same test_name already exists
            $existingTest = MasterTest::where('test_name', $request->test_name)->first();

            if ($existingTest) {
                // Return a meaningful error message if the test_name already exists
                return response()->json([
                    'error' => 'Test name already exists. Please choose a different test name.',
                ], 409); // HTTP 409 Conflict
            }

            // Create a new test if no duplicate is found
            $test = MasterTest::create($request->all());

            return response()->json([
                'message' => 'Test added successfully.',
                'data' => $test,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error adding test: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'.$e->getMessage()], 500);
        }
    }


    public function getTest($id)
    {
        try {
            $test = MasterTest::find($id); // Now 'find' will look for 'master_test_id'
            if (!$test) {
                return response()->json(['error' => 'Test not found.'], 404);
            }
            return response()->json(['data' => $test], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching test: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }


    public function getAllTests()
    {
        try {
            $tests = MasterTest::all();
            return response()->json(['data' => $tests], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching all tests: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function getTestNamesAndIds()
    {
        try {
            $testNamesAndIds = MasterTest::pluck('test_name', 'master_test_id');
            return response()->json(['data' => $testNamesAndIds], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching test names and IDs: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function updateTest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'test_name' => 'nullable|string|max:255',
            'test_desc' => 'nullable|string',
            'testgroup_id' => 'nullable|integer',
            'subgroup_id' => 'nullable|string',
            'subsubgroup_id' => 'nullable|string',
            'unit' => 'nullable|string',
            'range' => 'nullable|string',
            'm_min_max' => 'nullable|json',
            'f_min_max' => 'nullable|json',
            'type' => 'nullable|string',
            'numeric_type' => 'nullable|string',
            'condition' => 'nullable|json',
            'numeric_condition' => 'nullable|string',
            'normal_values' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $validator->errors(),
            ], 400);
        }

        try {
            $test = MasterTest::find($id);
            if (!$test) {
                return response()->json(['error' => 'Test not found.'], 404);
            }
            $test->update($request->all());
            return response()->json([
                'message' => 'Test updated successfully.',
                'data' => $test,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating test: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function deleteTest($id)
    {
        try {
            $test = MasterTest::find($id);
            if (!$test) {
                return response()->json(['error' => 'Test not found.'], 404);
            }
            $test->delete();
            return response()->json(['message' => 'Test deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting test: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}

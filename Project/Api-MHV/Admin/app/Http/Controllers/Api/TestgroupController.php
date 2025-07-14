<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testgroup;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TestgroupController extends Controller
{
    public function index()
    {
        try {
            $testGroups = Testgroup::all(); 
            return response()->json($testGroups, 200); 
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500); // HTTP 500 Internal Server Error
        }
    }
    public function store(Request $request)
    {
     
        $validator = validator($request->all(), [
            'testgroup_name' => 'required|string|max:255',
            'group_type' => 'required|integer|in:1,2,3',
            'group_id' => 'nullable|integer',
            'sub_group_id' => 'nullable|integer',
            'active_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed.',
                'messages' => $validator->errors()
            ], 400); 
        }

        try {
            $testGroupData = [
                'testgroup_name' => $request->testgroup_name,
                'group_type' => $request->group_type,
                'active_status' => $request->active_status ?? 1,
            ];

            if ($request->group_type == 1) {
                $testGroupData['group_id'] = null;
                $testGroupData['sub_group_id'] = null;
            } elseif ($request->group_type == 2) {
                $testGroupData['group_id'] = $request->group_id;
                $testGroupData['sub_group_id'] = null;
            } elseif ($request->group_type == 3) {
                $testGroupData['group_id'] = $request->group_id;
                $testGroupData['sub_group_id'] = $request->sub_group_id;
            }

            $testGroup = Testgroup::create($testGroupData);

            return response()->json($testGroup, 201); 
        } catch (\Exception $e) {
            Log::error('Failed to create TestGroup: ' . $e->getMessage(), [
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Something went wrong.'], 500); // HTTP 500 Internal Server Error
        }
    }
   
    
}

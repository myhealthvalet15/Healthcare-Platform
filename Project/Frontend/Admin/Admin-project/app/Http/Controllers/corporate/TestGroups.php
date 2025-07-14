<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestGroups extends Controller
{
    public function testGroupIndexPage()
    {
        return view('content.test-groups.testGroup-index');
    }

    public function getAllGroups(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/getAllGroup');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Server error'], 500);
        }
    }

    public function getSubGroupOfGroup(Request $request, $groupId)
    {
        try {
            if (!is_numeric($groupId)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request',
                ], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/getSubGroupOfGroup/' . $groupId);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Server error'], 500);
        }
    }
    public function addNewGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/addGroup', [
                'test_group_name' => $request->input('test_group_name'),
                'active_status' => $request->input('active_status'),
            ]);

            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
                'test_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/updateGroup', [
                'test_group_id' => $request->input('test_group_id') ? $request->input('test_group_id') : 0,
                'test_group_name' => $request->input('test_group_name'),
                'active_status' => $request->input('active_status'),
            ]);

            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteGroup(Request $request)
    {
        try {
            $validated = $request->validate([
                'test_group_id' => 'required|integer',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/deleteGroup', [
                'test_group_id' => $validated['test_group_id']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllSubGroups(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/getAllSubGroup');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Server error'], 500);
        }
    }

    public function addNewSubGroup(Request $request)
    {
        try {
            $request->validate([
                'sub_group_name' => 'required|string|max:255',
                'group_id' => 'required|integer',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/addSubGroup', [
                'test_group_id' => $request->input('group_id'),
                'test_sub_group_name' => $request->input('sub_group_name'),
                'active_status' => $request->input('active_status'),
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateSubGroup(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|integer',
                'subgroup_id' => 'required|integer',
                'sub_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/updateSubGroup', [
                'group_id' => $request->input('group_id'),
                'test_group_id' => $request->input('subgroup_id'),
                'test_sub_group_name' => $request->input('sub_group_name'),
                'active_status' => $request->input('active_status'),
            ]);

            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteSubGroup(Request $request)
    {
        try {
            $validated = $request->validate([
                'test_group_id' => 'required|integer',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/deleteSubGroup', [
                'test_group_id' => $validated['test_group_id']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllSubSubGroups(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/getAllSubSubGroup');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Server error'], 500);
        }
    }
    public function addNewSubSubGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
                'test_sub_group_id' => 'required|integer',
                'sub_sub_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/addSubSubGroup', [
                'group_id' => $request->input('test_group_id'),
                'test_sub_group_id' => $request->input('test_sub_group_id'),
                'test_sub_sub_group_name' => $request->input('sub_sub_group_name'),
                'active_status' => $request->input('active_status'),
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function updateSubSubGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
                'test_sub_group_id' => 'required|integer',
                'test_sub_sub_group_id' => 'required|integer',
                'sub_sub_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/updateSubSubGroup', [
                'group_id' => $request->input('test_group_id'),
                'test_sub_group_id' => $request->input('test_sub_group_id'),
                'test_sub_sub_group_id' => $request->input('test_sub_sub_group_id'),
                'test_sub_sub_group_name' => $request->input('sub_sub_group_name'),
                'active_status' => $request->input('active_status'),
            ]);

            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function deleteSubSubGroup(Request $request)
    {
        try {
            $validated = $request->validate([
                'test_group_id' => 'required|integer',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/deleteSubSubGroup', [
                'test_group_id' => $validated['test_group_id']
            ]);
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

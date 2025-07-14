<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use function PHPSTORM_META\type;

class corporateHealthPlans extends Controller
{
    public function displayHealthPlanPage()
    {
        $headerData = "CORPORATE HEALTH PLANS";
        return view('content.corporate.corporate-healthplans', ['HeaderData' => $headerData]);
    }
    public function getAllHealthplans(Request $request)
    {
        try {
            $corporateId = session("corporate_id");
            if (!$corporateId) {
                return response()->json([
                    "result" => false,
                    "message" => "Invalid Request"
                ]);
            }
            if (empty($corporateId)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Corporate Id'
                ], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllHealthPlans/' . $corporateId);
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'message' => $response['data']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'message' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getAllMasterTestsBySubGroup(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllSubGroup');
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'data' => $response['data']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'data' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getAllMasterTests(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/hra/master-tests/getAllTests');
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'data' => $response['data']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'data' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getAllMasterTestsForEmployee(Request $request, $employeeId = null)
    {
        try {
            $route = $request->route();
            $op_registry_id = $route->parameter('op_registry_id');
            $prescription_id = $route->parameter('prescription_id');
            if ($employeeId === null || !ctype_alnum($employeeId)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            if ($op_registry_id !== null && !is_numeric($op_registry_id)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            if ($prescription_id !== null && !is_numeric($prescription_id)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            $mode = null;
            $id = null;
            if ($op_registry_id !== null) {
                $mode = 'op';
                $id = $op_registry_id;
            } elseif ($prescription_id !== null) {
                $mode = 'prescription';
                $id = $prescription_id;
            } else {
                $mode = 'op';
                $id = 0;
            }
            $suffix = '/' . $mode . '/' . $id;
            $url = 'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getTestForEmployee/' . $employeeId . $suffix;
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get($url);
            if ($response->successful() && $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'data' => $response['data']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'message' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function getAllCertificates(Request $request, $corporateId)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllCertificates/' . $corporateId);
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'total_count' => $response['total_count'],
                    'data' => $response['certificates']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'data' => $response['message'] ? $response['message'] : 'Error fetching data'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getAllForms(Request $request, $corporateId)
    {
        try {
            // TODO: this is a temporary fix, need to get the forms from the backend
            return response()->json([
                'result' => true,
                'data' => [
                    [
                        'form_id' => 1001,
                        'form_name' => 'Form 17'
                    ],
                    [
                        'form_id' => 1002,
                        'form_name' => 'Form 40'
                    ],
                    [
                        'form_id' => 1003,
                        'form_name' => 'Form 27'
                    ],
                    [
                        'form_id' => 1004,
                        'form_name' => 'FIR'
                    ],
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function editHealthPlan(Request $request)
    {
        $validatedData = $request->validate([
            'corporate_id' => 'required|string',
            'healthplan_title' => 'required|string',
            'healthplan_description' => 'nullable|string',
            'healthplan_id' => 'required|integer',
            'master_test_id' => 'required|array|min:1',
            'master_test_id.*' => 'required|integer',
            'certificate_id' => 'nullable|array',
            'certificate_id.*' => 'integer',
            'forms' => 'nullable|array',
            'forms.*' => 'integer',
            'isPreEmployement' => 'required|in:0,1',
            'gender' => 'required|array',
            'gender.*' => 'required|in:male,female,others',
            'active_status' => 'required|in:0,1',
        ]);
        // $healthplan_description = $validatedData['healthplan_description'] ?? null;
        $healthplan_description = $validatedData['healthplan_description'] ?? 'No description provided';
        // Log::info('Bhavas Validated Data: ', ['healthplan_description' => $healthplan_description]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/updateHealthplan', [
                'corporate_id' => $validatedData['corporate_id'],
                'healthplan_id' => $validatedData['healthplan_id'],
                'healthplan_title' => $validatedData['healthplan_title'],
                'healthplan_description' => $healthplan_description,
                'master_test_id' => $validatedData['master_test_id'],
                'certificate_id' => $validatedData['certificate_id'],
                'forms' => $validatedData['forms'],
                'isPreEmployement' => $validatedData['isPreEmployement'],
                'gender' => $validatedData['gender'],
                'active_status' => $validatedData['active_status'],
            ]);
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'message' => $response['message']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'message' => $response['message'] ? $response['message'] : 'Error fetching data'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function deleteHealthPlan(Request $request, $corporateId, $healthplanId)
    {
        if (empty($corporateId) || empty($healthplanId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Corporate Id or Healthplan Id'
            ], 400);
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-user.hygeiaes.com/V1/corporate/corporate-components/deleteHealthplan', [
                'corporate_id' => $corporateId,
                'healthplan_id' => $healthplanId
            ]);
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'message' => $response['message']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'data' => $response['message'] ? $response['message'] : 'Error fetching data'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function addHealthPlan(Request $request)
    {
        $validatedData = $request->validate([
            'corporate_id' => 'required|string',
            'healthplan_title' => 'required|string',
            'healthplan_description' => 'nullable|string',
            'master_test_id' => 'required|array|min:1',
            'master_test_id.*' => 'required|integer',
            'certificate_id' => 'nullable|array',
            'certificate_id.*' => 'integer',
            'forms' => 'nullable|array',
            'forms.*' => 'integer',
            'isPreEmployement' => 'required|in:0,1',
            'gender' => 'required|array',
            'gender.*' => 'required|in:male,female,others',
            'active_status' => 'required|in:0,1',
        ]);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addNewHealthPlan', [
                'corporate_id' => $validatedData['corporate_id'],
                'healthplan_title' => $validatedData['healthplan_title'],
                'healthplan_description' => $validatedData['healthplan_description'],
                'master_test_id' => $validatedData['master_test_id'],
                'certificate_id' => $validatedData['certificate_id'],
                'forms' => $validatedData['forms'],
                'isPreEmployement' => $validatedData['isPreEmployement'],
                'gender' => $validatedData['gender'],
                'active_status' => $validatedData['active_status'],
            ]);
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => $response['message']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'data' => $response['message'] ? $response['message'] : 'Error fetching data'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getealthPlan($corporate_id, $healthplan_id, Request $request)
    {
        try {
            if (empty($corporate_id) || empty($healthplan_id) || !is_numeric($healthplan_id)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Corporate Id or Healthplan Id'
                ], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getHealthPlan/' . $corporate_id . '/' . $healthplan_id);
            if ($response->successful() and $response->getStatusCode() === 200) {
                return response()->json([
                    'result' => true,
                    'data' => $response['data']
                ], $response->status());
            }
            return response()->json([
                'result' => false,
                'message' => $response['message']
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}

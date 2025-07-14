<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CorporateHealthplan;
use App\Models\Hra\Master_Tests\MasterTest;
use App\Models\Certification;
use App\Models\TestGroup;

class _CorporateHealthplan extends Controller
{
    public function getAllHealthplans(Request $request, $corporateId)
    {
        if (empty($corporateId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Corporate Id'
            ], 400);
        }
        $healthplans = CorporateHealthplan::where('corporate_id', $corporateId)->get();
        $healthplans = $healthplans->map(function ($healthplan) use ($corporateId) {
            $masterTestIds = json_decode($healthplan->master_test_id);
            $certificateIds = json_decode($healthplan->certificate_id);
            $masterTests = MasterTest::whereIn('master_test_id', $masterTestIds)->pluck('test_name')->toArray();
            $certificates = Certification::whereIn('certificate_id', $certificateIds)
                ->where('corporate_id', $corporateId)
                ->pluck('certification_title')->toArray();
            // $healthplan->master_test_ids = $masterTestIds;
            // $healthplan->master_test_names = $masterTests;
            $healthplan->certificate_names = $certificates;
            $healthplan->testStructure = $this->getTestsByTestsIds($masterTestIds);
            unset($healthplan->master_test_id);
            unset($healthplan->certificate_id);
            return $healthplan;
        });
        return response()->json([
            'result' => true,
            'data' => $healthplans
        ], 200);
    }
    private function getTestsByTestsIds($testIds = null)
    {
        if (empty($testIds)) {
            return [];
        }
        if (!is_array($testIds)) {
            $testIds = explode(',', $testIds);
        }
        if (empty($testIds)) {
            return response()->json(['result' => false, 'message' => 'No test IDs provided'], 400);
        }
        $validTestIds = array_filter($testIds, function ($id) {
            return is_numeric($id);
        });
        if (empty($validTestIds)) {
            return response()->json(['result' => false, 'message' => 'No valid test IDs provided'], 400);
        }
        $masterTests = MasterTest::whereIn('master_test_id', $validTestIds)
            ->select('master_test_id', 'test_name', 'testgroup_id', 'subgroup_id', 'subsubgroup_id', 'unit', 'm_min_max', 'f_min_max')
            ->get();
        if ($masterTests->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'No tests found for the provided IDs'], 404);
        }
        $testGroupData = $this->getTestGroupData($masterTests);
        $groupedTests = $this->organizeTestsHierarchically(
            $masterTests,
            $testGroupData['testGroups'],
            $testGroupData['subGroups'],
            $testGroupData['subSubGroups']
        );
        return $groupedTests;
    }
    private function organizeTestsHierarchically(
        $masterTests,
        $testGroups,
        $subGroups,
        $subSubGroups
    ): array {
        $groupedTests = [];
        foreach ($masterTests as $test) {
            $groupId = $test->testgroup_id;
            $subGroupId = $test->subgroup_id;
            $subSubGroupId = $test->subsubgroup_id;
            $groupName = $testGroups[$groupId] ?? 'Uncategorized';
            if (!isset($groupedTests[$groupName])) {
                $groupedTests[$groupName] = [];
            }
            $testData = [
                'name' => $test->test_name,
                'unit' => $test->unit,
                'm_min_max' => $test->m_min_max,
                'f_min_max' => $test->f_min_max,
                'master_test_id' => $test->master_test_id,
                'test_result' => null
            ];
            $this->categorizeTest(
                $groupedTests,
                $groupName,
                $subGroupId,
                $subSubGroupId,
                $subGroups,
                $subSubGroups,
                $test->test_name,
                $test->unit,
                $test->m_min_max,
                $test->f_min_max,
                $test->master_test_id
            );
        }
        return $groupedTests;
    }
    private function getTestGroupData($masterTests): array
    {
        $testGroupIds = $masterTests->pluck('testgroup_id')->filter()->unique()->toArray();
        $subGroupIds = $masterTests->pluck('subgroup_id')->filter()->unique()->toArray();
        $subSubGroupIds = $masterTests->pluck('subsubgroup_id')->filter()->unique()->toArray();
        $testGroups = TestGroup::whereIn('test_group_id', $testGroupIds)
            ->pluck('test_group_name', 'test_group_id');
        $subGroups = TestGroup::whereIn('test_group_id', $subGroupIds)
            ->pluck('test_group_name', 'test_group_id');
        $subSubGroups = TestGroup::whereIn('test_group_id', $subSubGroupIds)
            ->pluck('test_group_name', 'test_group_id');
        return [
            'testGroups' => $testGroups,
            'subGroups' => $subGroups,
            'subSubGroups' => $subSubGroups
        ];
    }
    private function categorizeTest(
        &$groupedTests,
        $groupName,
        $subGroupId,
        $subSubGroupId,
        $subGroups,
        $subSubGroups,
        $testName,
        $unit = null,
        $m_min_max = null,
        $f_min_max = null,
        $masterTestId = null,
        $testResult = null
    ): void {
        $testData = [
            'name' => $testName,
            'unit' => $unit,
            'm_min_max' => $m_min_max,
            'f_min_max' => $f_min_max,
            'master_test_id' => $masterTestId,
            'test_result' => $testResult
        ];
        if ($subGroupId && isset($subGroups[$subGroupId])) {
            $subGroupName = $subGroups[$subGroupId];
            if (!isset($groupedTests[$groupName][$subGroupName])) {
                $groupedTests[$groupName][$subGroupName] = [];
            }
            if ($subSubGroupId && isset($subSubGroups[$subSubGroupId])) {
                $subSubGroupName = $subSubGroups[$subSubGroupId];
                if (!isset($groupedTests[$groupName][$subGroupName][$subSubGroupName])) {
                    $groupedTests[$groupName][$subGroupName][$subSubGroupName] = [];
                }
                $groupedTests[$groupName][$subGroupName][$subSubGroupName][] = $testData;
            } else {
                $groupedTests[$groupName][$subGroupName][] = $testData;
            }
        } else {
            $groupedTests[$groupName][] = $testData;
        }
    }
    public function getHealthplan($corporateId, $healthplanId)
    {
        if (empty($corporateId) || empty($healthplanId) || !is_numeric($healthplanId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Corporate Id or Healthplan Id'
            ], 400);
        }
        $healthplan = CorporateHealthplan::where('corporate_id', $corporateId)->where('corporate_healthplan_id', $healthplanId)->first();
        if (empty($healthplan)) {
            return response()->json([
                'result' => false,
                'message' => 'Healthplan not found'
            ], 404);
        }
        return response()->json([
            'result' => true,
            'data' => $healthplan
        ], 200);
    }
    public function deleteHealthplan(Request $request)
    {
        $validatedData = $request->validate([
            'corporate_id' => 'required|string',
            'healthplan_id' => 'required|string',
        ]);
        $corporateId = $validatedData['corporate_id'];
        $healthplanId = $validatedData['healthplan_id'];
        if (empty($corporateId) || empty($healthplanId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Corporate Id or Healthplan Id'
            ], 400);
        }
        $healthplan = CorporateHealthplan::where('corporate_id', $corporateId)->where('corporate_healthplan_id', $healthplanId)->first();
        if (empty($healthplan)) {
            return response()->json([
                'result' => false,
                'message' => 'Healthplan not found'
            ], 404);
        }
        $healthplan->delete();
        return response()->json([
            'result' => true,
            'message' => 'Healthplan deleted successfully'
        ], 200);
    }
    public function addHealthplan(Request $request)
    {
        // TODO: In both add and update healthplan
        // TODO: FORMS id shld be checked from the table 'forms'
        // TODO: To check the incoming corporate_id with the users corporate_id, if not same then return 'Invalid Request error'
        // TODO: auth('api')->id() shld be used to get the user id, but it is not working here
        $validatedData = $request->validate([
            'corporate_id' => 'required|string|exists:master_corporate,corporate_id',
            'healthplan_title' => 'required|string|unique:corporate_healthplan,healthplan_title',
            'healthplan_description' => 'nullable|string',
            'master_test_id' => 'nullable|array',
            'master_test_id.*' => 'integer|exists:master_test,master_test_id',
            'certificate_id' => 'nullable|array',
            'certificate_id.*' => 'integer|exists:certification,certificate_id',
            'forms' => 'nullable|array',
            'forms.*' => 'integer',
            'isPreEmployement' => 'required|in:0,1',
            'gender' => 'required|array',
            'gender.*' => 'required|in:male,female,others',
            'active_status' => 'required|in:0,1',
        ]);
        try {
            $healthplan = new CorporateHealthplan();
            $healthplan->corporate_id = $validatedData['corporate_id'];
            $healthplan->healthplan_title = $validatedData['healthplan_title'];
            $healthplan->healthplan_description = $validatedData['healthplan_description'];
            $healthplan->master_test_id = $validatedData['master_test_id'] ? json_encode($validatedData['master_test_id']) : null;
            $healthplan->certificate_id = json_encode($validatedData['certificate_id']);
            $healthplan->forms = $validatedData['forms'] ? json_encode($validatedData['forms']) : null;
            $healthplan->isPreEmployement = $validatedData['isPreEmployement'];
            $healthplan->gender = json_encode($validatedData['gender']);
            $healthplan->active_status = $validatedData['active_status'];
            $healthplan->created_by = auth('api')->id() ?? null;
            $healthplan->save();
            return response()->json([
                'result' => true,
                'message' => 'Healthplan added successfully',
                'data' => $healthplan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to add healthplan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateHealthplan(Request $request)
    {
        $validatedData = $request->validate([
            'corporate_id' => 'required|string|exists:master_corporate,corporate_id',
            'healthplan_id' => 'required|string|exists:corporate_healthplan,corporate_healthplan_id',
        ]);
        $corporateId = $validatedData['corporate_id'];
        $healthplanId = $validatedData['healthplan_id'];
        $healthplan = CorporateHealthplan::where('corporate_id', $corporateId)
            ->where('corporate_healthplan_id', $healthplanId)
            ->first();
        if (!$healthplan) {
            return response()->json([
                'result' => false,
                'message' => 'Healthplan not found',
            ], 404);
        }
        $validatedData = $request->validate([
            'healthplan_title' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($healthplan, $corporateId) {
                    $exists = CorporateHealthplan::where('corporate_id', $corporateId)
                        ->where('healthplan_title', $value)
                        ->where('corporate_healthplan_id', '!=', $healthplan->corporate_healthplan_id)
                        ->exists();
                    if ($exists) {
                        $fail('The healthplan title already exists');
                    }
                },
            ],
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
        $healthplan->modified_by = auth('api')->id() ?: null;
        $healthplan->modified_date = now();
        $healthplan->healthplan_title = $validatedData['healthplan_title'] ?? $healthplan->healthplan_title;
        $healthplan->healthplan_description = !empty($validatedData['healthplan_description']) || $validatedData['healthplan_description'] === '0'
            ? $validatedData['healthplan_description']
            : $healthplan->healthplan_description;
        $healthplan->master_test_id = json_encode($validatedData['master_test_id']);
        $healthplan->certificate_id = json_encode($validatedData['certificate_id']);
        $healthplan->forms = $validatedData['forms'] ? json_encode($validatedData['forms']) : null;
        $healthplan->isPreEmployement = $validatedData['isPreEmployement'];
        $healthplan->active_status = $validatedData['active_status'];
        $healthplan->gender = json_encode($validatedData['gender']);
        try {
            $healthplan->save();
            return response()->json([
                'result' => true,
                'message' => 'Healthplan updated successfully',
                'data' => $healthplan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to update healthplan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

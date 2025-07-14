<?php

namespace App\Http\Controllers\V1Controllers\CorporateController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TestGroup;
use Illuminate\Validation\Rule;

class TestGroups extends Controller
{
    public function __construct()
    {
    }
    // TODO: Duplicate Entries are not allowed in the same group and same sub group name, to be fixed that ....
    private function formatGroupDataRecursive($group, $level)
    {
        if ($level === 1) {
            $subgroups = TestGroup::where('group_id', $group->test_group_id)
                ->get()
                ->map(function ($subgroup) use ($level) {
                    return $this->formatGroupDataRecursive($subgroup, $level + 1);
                });
        } elseif ($level === 2) {
            $subgroups = TestGroup::where('test_group_id', $group->test_group_id)
                ->where('group_type', 3)
                ->get()
                ->map(function ($subgroup) use ($level) {
                    return $this->formatGroupDataRecursive($subgroup, $level + 1);
                });
        } else {
            $subgroups = [];
        }
        return [
            'test_group_id' => $group->test_group_id,
            'test_group_name' => $group->test_group_name,
            'group_type' => $group->group_type,
            'group_id' => $group->group_id,
            'subgroup_id' => $group->subgroup_id,
            'active_status' => $group->active_status,
            'subgroups' => $subgroups,
        ];
    }
    private function formatSubGroupDataRecursive($group)
    {
        $subgroups = TestGroup::where('subgroup_id', $group->test_group_id)
            ->where('group_type', 3)
            ->get()
            ->map(function ($subgroup) {
                return $this->formatSubGroupDataRecursive($subgroup);
            });
        return [
            'test_group_id' => $group->test_group_id,
            'test_group_name' => $group->test_group_name,
            'group_type' => $group->group_type,
            'group_id' => $group->group_id,
            'subgroup_id' => $group->subgroup_id,
            'active_status' => $group->active_status,
            'subgroups' => $subgroups,
        ];
    }
    public function getAllGroup()
    {
        $groups = TestGroup::where('group_type', 1)->get()->map(function ($group) {
            return $this->formatGroupDataRecursive($group, 1);
        });
        return response()->json([
            'result' => true,
            'data' => $groups
        ], 200);
    }
    public function getAllSubGroup()
    {
        $allGroups = TestGroup::all()->keyBy('test_group_id');
        $groups = TestGroup::where('group_type', 2)->get()->map(function ($group) use ($allGroups) {
            return [
                'test_group_id' => $group->test_group_id,
                'test_group_name' => $group->test_group_name,
                'group_type' => $group->group_type,
                'group_id' => $group->group_id,
                'subgroup_id' => $group->subgroup_id,
                'active_status' => $group->active_status,
                'mother_group' => $allGroups->get($group->group_id)->test_group_name ?? null,
                'subgroups' => $this->formatSubGroupDataRecursive($group),
            ];
        });
        return response()->json([
            'result' => true,
            'data' => $groups
        ], 200);
    }
    public function getAllSubSubGroup()
    {
        $allGroups = TestGroup::all()->keyBy('test_group_id');
        $groups = TestGroup::where('group_type', 3)->get()->map(function ($group) use ($allGroups) {
            return [
                'test_group_id' => $group->test_group_id,
                'test_group_name' => $group->test_group_name,
                'group_type' => $group->group_type,
                'group_id' => $group->group_id,
                'subgroup_id' => $group->subgroup_id,
                'active_status' => $group->active_status,
                'mother_group' => $allGroups->get($group->group_id)->test_group_name ?? null,
                'mother_subgroup' => $allGroups->get($group->subgroup_id)->test_group_name ?? null,
            ];
        });
        return response()->json([
            'result' => true,
            'data' => $groups
        ], 200);
    }
    public function getGroup($groupId)
    {
        if (!is_numeric($groupId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid group id',
            ], 400);
        }
        $group = TestGroup::where('test_group_id', $groupId)
            ->where('group_type', 1)
            ->first();
        if (!$group) {
            return response()->json([
                'result' => false,
                'message' => 'Group not found',
            ], 404);
        }
        $formattedGroup = $this->formatGroupDataRecursive($group, 1);
        return response()->json([
            'result' => true,
            'data' => $formattedGroup
        ], 200);
    }
    public function getSubGroup($subGroupId)
    {
        if (!is_numeric($subGroupId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid group id',
            ], 400);
        }
        $subGroup = TestGroup::where('test_group_id', $subGroupId)
            ->where('group_type', 2)
            ->first();
        if (!$subGroup) {
            return response()->json([
                'result' => false,
                'message' => 'Sub Group not found',
            ], 404);
        }
        $formattedSubGroup = $this->formatSubGroupDataRecursive($subGroup);
        return response()->json([
            'result' => true,
            'data' => $formattedSubGroup
        ], 200);
    }
    public function getSubSubGroup($subSubGroupId)
    {
        if (!is_numeric($subSubGroupId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid group id',
            ], 400);
        }
        $subSubGroup = TestGroup::where('test_group_id', $subSubGroupId)
            ->where('group_type', 3)
            ->first();
        if (!$subSubGroup) {
            return response()->json([
                'result' => false,
                'message' => 'Sub Sub Group not found',
            ], 404);
        }
        return response()->json([
            'result' => true,
            'data' => $subSubGroup
        ], 200);
    }
    public function getSubGroupOfGroup($groupId)
    {
        if (!is_numeric($groupId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request',
            ], 400);
        }
        $groups = TestGroup::where('group_type', 2)->where('group_id', $groupId)->get()->map(function ($group) {
            return $this->formatGroupDataRecursive($group, 1);
        });
        return response()->json([
            'result' => true,
            'data' => $groups
        ], 200);
    }
    public function addGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $group = TestGroup::where('test_group_name', $request->test_group_name)
                ->where('group_type', 1)
                ->first();
            if ($group) {
                return response()->json([
                    'result' => false,
                    'message' => 'Group already exists',
                ], 400);
            }
            $group = TestGroup::create([
                'test_group_name' => $request->test_group_name,
                'active_status' => $request->active_status,
                'group_type' => 1
            ]);
            return response()->json([
                'result' => true,
                'message' => 'Group added successfully',
                'data' => $group
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
                'test_group_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('test_group', 'test_group_name')->ignore($request->test_group_id, 'test_group_id')
                ],
                'active_status' => 'required|boolean',
            ]);
            $group = TestGroup::where('test_group_id', $request->test_group_id)
                ->where('group_type', 1)
                ->first();
            if (!$group) {
                return response()->json([
                    'result' => false,
                    'message' => 'Group not found',
                ], 404);
            }
            $group->test_group_name = $request->test_group_name;
            $group->active_status = $request->active_status;
            $group->save();
            return response()->json([
                'result' => true,
                'message' => 'Group updated successfully',
                'data' => $group
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
            ]);
            $group = TestGroup::where('test_group_id', $request->test_group_id)
                ->where('group_type', 1)
                ->first();
            if (!$group) {
                return response()->json([
                    'result' => false,
                    'message' => 'Group not found',
                ], 404);
            }
            $group->delete();
            return response()->json([
                'result' => true,
                'message' => 'Group deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function addSubGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
                'test_sub_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $group = TestGroup::where('test_group_id', $request->test_group_id)
                ->where('group_type', 1)
                ->first();
            if (!$group) {
                return response()->json([
                    'result' => false,
                    'message' => 'Group not found',
                ], 404);
            }
            $subGroup = TestGroup::where('test_group_name', $request->test_sub_group_name)
                ->where('group_type', 2)
                ->first();
            if ($subGroup) {
                return response()->json([
                    'result' => false,
                    'message' => 'Sub Group already exists',
                ], 400);
            }
            $group = TestGroup::create([
                'group_id' => $request->test_group_id,
                'test_group_name' => $request->test_sub_group_name,
                'active_status' => $request->active_status,
                'group_type' => 2
            ]);
            return response()->json([
                'result' => true,
                'message' => 'Sub Group added successfully',
                'data' => $group
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateSubGroup(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|integer',
                'test_group_id' => 'required|integer',
                'test_sub_group_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('test_group', 'test_group_name')->ignore($request->test_group_id, 'test_group_id')
                ],
                'active_status' => 'required|boolean',
            ]);
            $group = TestGroup::where('test_group_id', $request->test_group_id)
                ->where('group_type', 2)
                ->first();
            if (!$group) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request',
                ], 404);
            }
            $group->test_group_name = $request->test_sub_group_name;
            $group->active_status = $request->active_status;
            $group->save();
            return response()->json([
                'result' => true,
                'message' => 'Sub Group updated successfully',
                'data' => $group
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteSubGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
            ]);
            $group = TestGroup::where('test_group_id', $request->test_group_id)
                ->where('group_type', 2)
                ->first();
            if (!$group) {
                return response()->json([
                    'result' => false,
                    'message' => 'Group not found',
                ], 404);
            }
            $group->delete();
            return response()->json([
                'result' => true,
                'message' => 'Sub Group deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function addSubSubGroup(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|integer',
                'test_sub_group_id' => 'required|integer',
                'test_sub_sub_group_name' => 'required|string|max:255',
                'active_status' => 'required|boolean',
            ]);
            $subGroup = TestGroup::where('test_group_id', $request->test_sub_group_id)
                ->where('group_type', 2)
                ->first();
            if (!$subGroup) {
                return response()->json([
                    'result' => false,
                    'message' => 'Sub Group not found',
                ], 404);
            }
            $subSubGroup = TestGroup::where('test_group_name', $request->test_sub_sub_group_name)
                ->where('group_type', 3)
                ->first();
            if ($subSubGroup) {
                return response()->json([
                    'result' => false,
                    'message' => 'Sub Sub Group already exists',
                ], 400);
            }
            $subSubGroupCombination = TestGroup::where('group_id', $request->group_id)
                ->where('test_group_id', $request->test_sub_group_id)
                ->where('group_type', 2)
                ->first();
            if (!$subSubGroupCombination) {
                return response()->json([
                    'result' => false,
                    'message' => 'Selected Sub Group is not linked with the Group, First link the Sub Group with the desired Group and then try again.',
                ], 400);
            }
            $group = TestGroup::create([
                'test_group_name' => $request->test_sub_sub_group_name,
                'active_status' => $request->active_status,
                'group_id' => $request->group_id,
                'subgroup_id' => $request->test_sub_group_id,
                'group_type' => 3
            ]);
            return response()->json([
                'result' => true,
                'message' => 'Sub Sub Group added successfully',
                'data' => $group
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateSubSubGroup(Request $request)
    {
        try {
            $request->validate([
                'group_id' => 'required|integer',
                'test_sub_group_id' => 'required|integer',
                'test_sub_sub_group_id' => 'required|integer',
                'test_sub_sub_group_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('test_group', 'test_group_name')->ignore($request->test_sub_sub_group_id, 'test_group_id')
                ],
                'active_status' => 'required|boolean',
            ]);
            $subSubGroupCombination = TestGroup::where('group_id', $request->group_id)
                ->where('subgroup_id', $request->test_sub_group_id)
                ->where('test_group_id', $request->test_sub_sub_group_id)
                ->where('group_type', 3)
                ->first();
            if (! $subSubGroupCombination) {
                return response()->json([
                    'result' => false,
                    'message' => 'Test Groups Combination are mismatched.'
                ], 200);
            }
            $subSubGroupCombination->test_group_name = $request->test_sub_sub_group_name;
            $subSubGroupCombination->active_status = $request->active_status;
            $subSubGroupCombination->save();
            return response()->json([
                'result' => true,
                'message' => 'Sub Sub Group updated successfully',
                'data' => $subSubGroupCombination
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteSubSubGroup(Request $request)
    {
        try {
            $request->validate([
                'test_group_id' => 'required|integer',
            ]);
            $subSubGroup = TestGroup::where('test_group_id', $request->test_group_id)
                ->where('group_type', 3)
                ->first();
            if (!$subSubGroup) {
                return response()->json([
                    'result' => false,
                    'message' => 'Sub Sub Group not found',
                ], 404);
            }
            $subSubGroup->delete();
            return response()->json([
                'result' => true,
                'message' => 'Sub Sub Group deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

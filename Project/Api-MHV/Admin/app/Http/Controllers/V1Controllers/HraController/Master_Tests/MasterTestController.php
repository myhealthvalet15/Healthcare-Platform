<?php

namespace App\Http\Controllers\V1Controllers\HraController\Master_Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Hra\Master_Tests\MasterTest;
use App\Models\TestGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MasterTestController extends Controller
{
    private function addEditMasterTest($request, $mode = null, $id = null)
    {
        try {
            if ($mode === 'edit' and (!is_numeric($id))) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request',
                ], 400);
            }
            if ($mode === 'edit' and (is_numeric($id))) {
                $test = MasterTest::find($id);
                if (!$test) {
                    return response()->json([
                        'result' => false,
                        'message' => 'Invalid Request',
                    ], 400);
                }
            }
            $validated = $request->validate([
                'test_name' => 'required|string|max:255',
                'group_id' => 'required|integer',
                'sub_group_id' => 'nullable|integer',
                'sub_sub_group_id' => 'nullable|integer',
                'description' => 'nullable|string',
                'remarks' => 'nullable|string',
                'type' => 'required|string|in:text,numeric',
                'unit' => 'nullable|string'
            ]);
            if ($validated['type'] == 'text') {
                $validated = array_merge($validated, $request->validate([
                    'condition' => 'nullable|array|max:5',
                    'condition.*' => 'nullable|string',
                ]));
                $condition = json_encode($request->condition);
                $testData = [
                    'test_name' => $request->test_name,
                    'test_desc' => $request->description ?? null,
                    'testgroup_id' => $request->group_id,
                    'subgroup_id' => $request->sub_group_id ?? null,
                    'subsubgroup_id' => $request->sub_sub_group_id ?? null,
                    'unit' => $request->unit ?? null,
                    'age_range' => null,
                    'm_min_max' => $request->m_min_max ? json_encode($request->m_min_max) : null,
                    'f_min_max' => $request->f_min_max ? json_encode($request->f_min_max) : null,
                    'type' => $request->type,
                    'numeric_type' => $request->numeric_type ?? null,
                    'condition' => $condition,
                    'numeric_condition' => $request->numeric_condition ?? null,
                    'normal_values' => $request->normal_values ?? null,
                    'remarks' => $request->remarks ?? null,
                ];
            } else if ($validated['type'] == 'numeric') {
                $validated_numeric_type = $request->validate([
                    'numeric_type' => 'required|string',
                ]);
                $validated = array_merge($validated, $validated_numeric_type);
                if ($validated['numeric_type'] === 'no-age-range') {
                    $validated_no_age_range = array_merge($validated, $request->validate([
                        'no_age_range_female_max' => 'required|numeric',
                        'no_age_range_female_min' => 'required|numeric',
                        'no_age_range_male_max' => 'required|numeric',
                        'no_age_range_male_min' => 'required|numeric',
                    ]));
                    $maleMinMax = [
                        'min' => $validated_no_age_range['no_age_range_male_min'],
                        'max' => $validated_no_age_range['no_age_range_male_max']
                    ];
                    $femaleMinMax = [
                        'min' => $validated_no_age_range['no_age_range_female_min'],
                        'max' => $validated_no_age_range['no_age_range_female_max'],
                    ];
                    $testData = [
                        'test_name' => $request->test_name,
                        'test_desc' => $request->description ?? null,
                        'testgroup_id' => $request->group_id,
                        'subgroup_id' => $request->sub_group_id ?? null,
                        'subsubgroup_id' => $request->sub_sub_group_id ?? null,
                        'unit' => $request->unit ?? null,
                        'age_range' => null,
                        'm_min_max' => json_encode($maleMinMax),
                        'f_min_max' => json_encode($femaleMinMax),
                        'type' => $request->type,
                        'numeric_type' => $request->numeric_type ?? null,
                        'condition' => $request->condition ? implode(',', $request->condition) : null,
                        'numeric_condition' => $request->numeric_condition ?? null,
                        'normal_values' => $request->normal_values ?? null,
                        'remarks' => $request->remarks ?? null,
                    ];
                } elseif ($validated['numeric_type'] === 'multiple-age-range') {
                    $validated_multiple_age_range = array_merge($validated, $request->validate([
                        "ageFrom" => "required|array|max:5",
                        "ageFrom.*" => "required|integer",
                        "ageTo" => "required|array|max:5",
                        "ageTo.*" => "required|integer",
                        'multiple_age_range_max_female' => 'required|array|max:5',
                        'multiple_age_range_max_female.*' => 'required|numeric',
                        'multiple_age_range_min_female' => 'required|array|max:5',
                        'multiple_age_range_min_female.*' => 'required|numeric',
                        'multiple_age_range_max_male' => 'required|array|max:5',
                        'multiple_age_range_max_male.*' => 'required|numeric',
                        'multiple_age_range_min_male' => 'required|array|max:5',
                        'multiple_age_range_min_male.*' => 'required|numeric',
                    ]));
                    $ageRangeLengths = [
                        count($validated_multiple_age_range['ageFrom']),
                        count($validated_multiple_age_range['ageTo']),
                        count($validated_multiple_age_range['multiple_age_range_max_female']),
                        count($validated_multiple_age_range['multiple_age_range_min_female']),
                        count($validated_multiple_age_range['multiple_age_range_max_male']),
                        count($validated_multiple_age_range['multiple_age_range_min_male']),
                    ];
                    if (count(array_unique($ageRangeLengths)) > 1) {
                        return response()->json([
                            'result' => false,
                            'message' => 'Invalid Request.',
                        ], 400);
                    }
                    $maleMinMax = [
                        'min' => $validated_multiple_age_range['multiple_age_range_min_male'],
                        'max' => $validated_multiple_age_range['multiple_age_range_max_male']
                    ];
                    $femaleMinMax = [
                        'min' => $validated_multiple_age_range['multiple_age_range_min_female'],
                        'max' => $validated_multiple_age_range['multiple_age_range_max_female'],
                    ];
                    $agerange = array_map(fn($from, $to) => $from . '-' . $to, $validated_multiple_age_range['ageFrom'], $validated_multiple_age_range['ageTo']);
                    $testData = [
                        'test_name' => $request->test_name,
                        'test_desc' => $request->description ?? null,
                        'testgroup_id' => $request->group_id,
                        'subgroup_id' => $request->sub_group_id ?? null,
                        'subsubgroup_id' => $request->sub_sub_group_id ?? null,
                        'unit' => $request->unit ?? null,
                        'age_range' => json_encode($agerange),
                        'm_min_max' => json_encode($maleMinMax),
                        'f_min_max' => json_encode($femaleMinMax),
                        'type' => $request->type,
                        'numeric_type' => $request->numeric_type ?? null,
                        'condition' => $request->condition ? implode(',', $request->condition) : null,
                        'numeric_condition' => $request->numeric_condition ?? null,
                        'normal_values' => $request->normal_values ?? null,
                        'remarks' => $request->remarks ?? null,
                    ];
                } elseif ($validated['numeric_type'] === 'multiple-text-value') {
                    $validated_multiple_text_values = array_merge($validated, $request->validate([
                        'text_value_description' => 'required|array|max:5',
                        'text_value_description.*' => 'required|string',
                        'multiple_text_value_max_female' => 'required|array|max:5',
                        'multiple_text_value_max_female.*' => 'required|numeric',
                        'multiple_text_value_min_female' => 'required|array|max:5',
                        'multiple_text_value_min_female.*' => 'required|numeric',
                        'multiple_text_value_max_male' => 'required|array|max:5',
                        'multiple_text_value_max_male.*' => 'required|numeric',
                        'multiple_text_value_min_male' => 'required|array|max:5',
                        'multiple_text_value_min_male.*' => 'required|numeric',
                    ]));
                    $textValueLengths = [
                        count($validated_multiple_text_values['text_value_description']),
                        count($validated_multiple_text_values['multiple_text_value_max_female']),
                        count($validated_multiple_text_values['multiple_text_value_min_female']),
                        count($validated_multiple_text_values['multiple_text_value_max_male']),
                        count($validated_multiple_text_values['multiple_text_value_min_male']),
                    ];
                    if (count(array_unique($textValueLengths)) > 1) {
                        return response()->json([
                            'result' => false,
                            'message' => 'Invalid Request.',
                        ], 400);
                    }
                    $maleMinMax = [
                        'min' => $validated_multiple_text_values['multiple_text_value_min_male'],
                        'max' => $validated_multiple_text_values['multiple_text_value_max_male']
                    ];
                    $femaleMinMax = [
                        'min' => $validated_multiple_text_values['multiple_text_value_min_female'],
                        'max' => $validated_multiple_text_values['multiple_text_value_max_female'],
                    ];
                    $multipleTextValueDescriotion = $validated_multiple_text_values['text_value_description'];
                    $testData = [
                        'test_name' => $request->test_name,
                        'test_desc' => $request->description ?? null,
                        'testgroup_id' => $request->group_id,
                        'subgroup_id' => $request->sub_group_id ?? null,
                        'subsubgroup_id' => $request->sub_sub_group_id ?? null,
                        'unit' => $request->unit ?? null,
                        'age_range' => null,
                        'm_min_max' => json_encode($maleMinMax),
                        'f_min_max' => json_encode($femaleMinMax),
                        'multiple_text_value_description' => json_encode($multipleTextValueDescriotion),
                        'type' => $request->type,
                        'numeric_type' => $request->numeric_type ?? null,
                        'condition' => $request->condition ? implode(',', $request->condition) : null,
                        'numeric_condition' => $request->numeric_condition ?? null,
                        'normal_values' => $request->normal_values ?? null,
                        'remarks' => $request->remarks ?? null,
                    ];
                } elseif ($validated['numeric_type'] === 'just-values') {
                    $validated_multiple_text_values = array_merge($validated, $request->validate([
                        "just_values" => "required|string"
                    ]));
                    $testData = [
                        'test_name' => $request->test_name,
                        'test_desc' => $request->description ?? null,
                        'testgroup_id' => $request->group_id,
                        'subgroup_id' => $request->sub_group_id ?? null,
                        'subsubgroup_id' => $request->sub_sub_group_id ?? null,
                        'unit' => $request->unit ?? null,
                        'age_range' => null,
                        'm_min_max' => null,
                        'f_min_max' => null,
                        'type' => $request->type,
                        'numeric_type' => $request->numeric_type ?? null,
                        'condition' => $request->condition ? implode(',', $request->condition) : null,
                        'numeric_condition' => $request->numeric_condition ?? null,
                        'normal_values' => $request->just_values ?? null,
                        'remarks' => $request->remarks ?? null,
                    ];
                } else {
                    return response()->json(['result' => false, 'message' => 'Bad Request'], 400);
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Bad Request'], 400);
            }
            $group = TestGroup::where('test_group_id', $request->group_id)->where('group_type', 1)->first();
            if (!$group) return response()->json(['result' => false, 'message' => 'Group not found.'], 404);
            if (isset($request->sub_group_id)) {
                $subGroup = TestGroup::where('test_group_id', $request->sub_group_id)->where('group_type', 2)->first();
                if (!$subGroup) return response()->json(['result' => false, 'message' => 'Sub group not found.'], 404);
            }
            if (isset($request->sub_sub_group_id)) {
                $subSubGroup = TestGroup::where('test_group_id', $request->sub_sub_group_id)->where('group_type', 3)->first();
                if (!$subSubGroup) return response()->json(['result' => false, 'message' => 'Sub sub group not found.'], 404);
            }
            $testId = $id;
            if (in_array($mode, ['edit', 'add'])) {
                $query = MasterTest::where('test_name', $request->test_name);
                if ($mode === 'edit') {
                    $query->where('master_test_id', '!=', $testId);
                }
                $existingTest = $query->first();

                if ($existingTest) {
                    return response()->json([
                        'result' => false,
                        'message' => $request->test_name . ' already exists.',
                    ], 409);
                }

                if ($mode === 'edit') {
                    $test = MasterTest::find($testId);
                    if ($test) {
                        $test->update($testData);
                        return response()->json([
                            'result' => true,
                            'message' => 'Test updated successfully.',
                        ], 200);
                    }
                    return response()->json([
                        'result' => false,
                        'message' => 'Test not found.',
                    ], 404);
                }

                $testData = MasterTest::create($testData);
                return response()->json([
                    'result' => true,
                    'message' => 'Test added successfully.',
                ], 201);
            }

            return response()->json([
                'result' => false,
                'message' => 'Invalid Request',
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error', 'error' => 'An unexpected error occurred.' . $e->getMessage()], 500);
        }
    }
    public function addTest(Request $request)
    {
        return $this->addEditMasterTest($request, $mode = 'add');
    }
    public function updateTest(Request $request, $id)
    {
        return $this->addEditMasterTest($request, $mode = 'edit', $id);
    }
    public function getTest($id)
    {
        try {
            $test = MasterTest::find($id);
            if (!$test) {
                return response()->json(['error' => 'Test not found.'], 404);
            }
            return response()->json(['data' => $test], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    public function getAllTests()
    {
        try {
            $masterTests = MasterTest::all()->map(function ($test) {
                return [
                    'master_test_id' => $test->master_test_id,
                    'test_name' => $test->test_name,
                    'test_desc' => $test->test_desc,
                    'testgroup' => [
                        'id' => $test->testgroup_id,
                        'name' => TestGroup::where('test_group_id', $test->testgroup_id)->value('test_group_name') ?? null
                    ],
                    'subgroup' => [
                        'id' => $test->subgroup_id,
                        'name' => TestGroup::where('test_group_id', $test->subgroup_id)->value('test_group_name') ?? null
                    ],
                    'subsubgroup' => [
                        'id' => $test->subsubgroup_id,
                        'name' => TestGroup::where('test_group_id', $test->subsubgroup_id)->value('test_group_name') ?? null
                    ],
                    'unit' => $test->unit,
                    'age_range' => $test->age_range,
                    'm_min_max' => $test->m_min_max,
                    'f_min_max' => $test->f_min_max,
                    'type' => $test->type,
                    'numeric_type' => $test->numeric_type,
                    'multiple_text_value_description' => $test->multiple_text_value_description,
                    'condition' => $test->condition,
                    'numeric_condition' => $test->numeric_condition,
                    'normal_values' => $test->normal_values,
                    'remarks' => $test->remarks,
                    'created_on' => $test->created_on,
                ];
            });
            return response()->json(['data' => $masterTests], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    public function getTestNamesAndIds()
    {
        try {
            $testNamesAndIds = MasterTest::pluck('test_name', 'master_test_id');
            return response()->json(['data' => $testNamesAndIds], 200);
        } catch (\Exception $e) {
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
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}

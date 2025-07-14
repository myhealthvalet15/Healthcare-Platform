<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class Tests extends Controller
{
    public function addMasterTestsPage()
    {
        return view('content.test-groups.masterTest-add');
    }
    public function editMasterTestsPage(Request $request, $id)
    {
        if (!is_numeric($id)) {
            return redirect()->back()->with('error', 'Invalid Request');
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/hra/master-tests/getTest/' . $id);
        if ($response->successful()) {
            $data = $response->json()['data'];
            session(['testId_toEdit' => $id]);
            return view('content.test-groups.mastertest-edit', ['testData' => $data]);
        }
        return "Invalid request";
    }

    public function editMastertests(Request $request, $id)
    {
        if (session("testId_toEdit") !== $id) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request',
            ], 400);
        }
        return $this->addEditMasterTest($request, $mode = "edit");
    }

    public function showMasterTestsPage()
    {
        return view('content.test-groups.masterTest-show');
    }
    public function addMastertests(Request $request)
    {
        return $this->addEditMasterTest($request, $mode = "add");
    }

    private function addEditMasterTest($request, $mode = null)
    {
        try {
            if ($mode !== 'add' && $mode !== 'edit') {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request',
                ], 400);
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
                $conditionValidated = $request->validate([
                    'condition' => 'nullable|array|max:5',
                    'condition.*' => 'nullable|string',
                ]);
                $validated = array_merge($validated, $conditionValidated);
                $testDatas = [
                    "test_name" => $validated['test_name'],
                    "group_id" => $validated['group_id'],
                    "sub_group_id" => $validated['sub_group_id'] ?? null,
                    "sub_sub_group_id" => $validated['sub_sub_group_id'] ?? null,
                    "description" => $validated['description'],
                    "remarks" => $validated['remarks'],
                    "type" => $validated['type'],
                    "unit" => $validated['unit'],
                    "condition" => $validated['condition'],
                ];
            } elseif ($validated['type'] == 'numeric') {
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
                    $validated = array_merge($validated, $validated_no_age_range);
                    $testDatas = [
                        "test_name" => $validated['test_name'],
                        "group_id" => $validated['group_id'],
                        "sub_group_id" => $validated['sub_group_id'] ?? null,
                        "sub_sub_group_id" => $validated['sub_sub_group_id'] ?? null,
                        "description" => $validated['description'],
                        "remarks" => $validated['remarks'],
                        "type" => $validated['type'],
                        "unit" => $validated['unit'],
                        "numeric_type" => $validated['numeric_type'],
                        "no_age_range_female_max" => $validated['no_age_range_female_max'],
                        "no_age_range_female_min" => $validated['no_age_range_female_min'],
                        "no_age_range_male_max" => $validated['no_age_range_male_max'],
                        "no_age_range_male_min" => $validated['no_age_range_male_min']
                    ];
                } elseif ($validated['numeric_type'] === 'multiple-age-range') {
                    $validated_multiple_age_range = array_merge($validated, $request->validate([
                        "ageFrom" => "required|array|max:5",
                        "ageFrom.*" => "required|numeric",
                        "ageTo" => "required|array|max:5",
                        "ageTo.*" => "required|numeric",
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
                            'message' => 'All age range arrays (ageFrom, ageTo, male/female max and min) must have the same length.',
                        ], 400);
                    }
                    $validated = array_merge($validated, $validated_multiple_age_range);
                    $testDatas = [
                        "test_name" => $validated['test_name'],
                        "group_id" => $validated['group_id'],
                        "sub_group_id" => $validated['sub_group_id'] ?? null,
                        "sub_sub_group_id" => $validated['sub_sub_group_id'] ?? null,
                        "description" => $validated['description'],
                        "remarks" => $validated['remarks'],
                        "type" => $validated['type'],
                        "unit" => $validated['unit'],
                        "numeric_type" => $validated['numeric_type'],
                        "ageFrom" => $validated['ageFrom'],
                        "ageTo" => $validated['ageTo'],
                        "multiple_age_range_max_female" => $validated['multiple_age_range_max_female'],
                        "multiple_age_range_min_female" => $validated['multiple_age_range_min_female'],
                        "multiple_age_range_max_male" => $validated['multiple_age_range_max_male'],
                        "multiple_age_range_min_male" => $validated['multiple_age_range_min_male']
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
                            'message' => 'All text value arrays (description, male/female max and min) must have the same length.',
                        ], 400);
                    }

                    $validated = array_merge($validated, $validated_multiple_text_values);
                    $testDatas = [
                        "test_name" => $validated['test_name'],
                        "group_id" => $validated['group_id'],
                        "sub_group_id" => $validated['sub_group_id'] ?? null,
                        "sub_sub_group_id" => $validated['sub_sub_group_id'] ?? null,
                        "description" => $validated['description'],
                        "remarks" => $validated['remarks'],
                        "type" => $validated['type'],
                        "unit" => $validated['unit'],
                        "numeric_type" => $validated['numeric_type'],
                        "text_value_description" => $validated['text_value_description'],
                        "multiple_text_value_max_female" => $validated['multiple_text_value_max_female'],
                        "multiple_text_value_min_female" => $validated['multiple_text_value_min_female'],
                        "multiple_text_value_max_male" => $validated['multiple_text_value_max_male'],
                        "multiple_text_value_min_male" => $validated['multiple_text_value_min_male']
                    ];
                } elseif ($validated['numeric_type'] === 'just-values') {
                    $validated_multiple_text_values = array_merge($validated, $request->validate([
                        "just_values" => "required|string"
                    ]));
                    $validated = array_merge($validated, $validated_multiple_text_values);
                    $testDatas = [
                        "test_name" => $validated['test_name'],
                        "group_id" => $validated['group_id'],
                        "sub_group_id" => $validated['sub_group_id'] ?? null,
                        "sub_sub_group_id" => $validated['sub_sub_group_id'] ?? null,
                        "description" => $validated['description'],
                        "remarks" => $validated['remarks'],
                        "type" => $validated['type'],
                        "unit" => $validated['unit'],
                        "numeric_type" => $validated['numeric_type'],
                        "just_values" => $validated['just_values']
                    ];
                } else {
                    return response()->json(['result' => false, 'message' => 'Bad Request'], 400);
                }
            } else {
                return response()->json(['result' => false, 'message' => 'Bad Request'], 400);
            }
            $url = 'https://api-admin.hygeiaes.com/V1/hra/master-tests/' .
                ($mode == 'add' ? 'addTest' : 'editTest/' . session("testId_toEdit"));
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ]);
            if ($mode == 'add') {
                $response = $response->post($url, $testDatas);
            } else {
                $response = $response->put($url, $testDatas);
            }
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Fill all the details',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['result' => "error", 'message' => 'error: ' . $e->getMessage()]);
        }
    }
}

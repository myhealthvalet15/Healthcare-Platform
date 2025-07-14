<?php

namespace App\Http\Controllers\V1Controllers\HraController\Templates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Hra\Questions\HraQuestions;
use App\Models\V1Models\Hra\Templates\HraTemplate;
use App\Models\V1Models\Hra\Templates\HraTemplateQuestions;
use App\Models\V1Models\Hra\Factors\HraFactor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use phpseclib3\Crypt\RC2;

class TemplateController extends Controller
{
    public function addTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'total_adjustment_value' => 'nullable|integer',
            'maximum_value' => 'nullable|integer',
            'factor_adjustment_value' => 'nullable|integer',
            'health_index_formula' => 'nullable|string',
            'priority' => 'nullable|integer',
            'active_status' => 'nullable|integer|in:0,1',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        DB::beginTransaction();
        try {
            $activeFactors = HraFactor::where('active_status', 1)->get(['factor_id']);
            if ($activeFactors->isEmpty()) {
                return response()->json(['error' => 'No active factors found in hra_factors table.'], 404);
            }
            $existingTemplateFactors = HraTemplate::where('template_name', $request->template_name)
                ->whereIn('factor_id', $activeFactors->pluck('factor_id'))
                ->get();
            if ($existingTemplateFactors->count() === $activeFactors->count()) {
                return response()->json([
                    'result' => false,
                    'message' => "Template already exists."
                ], 409);
            }
            $maxTemplateId = HraTemplate::max('template_id') ?? 0;
            $template_id = $maxTemplateId + 1;
            $templates = [];
            foreach ($activeFactors as $factor) {
                $existingFactorTemplate = HraTemplate::where('template_name', $request->template_name)
                    ->where('factor_id', $factor->factor_id)
                    ->first();
                if ($existingFactorTemplate) {
                    continue;
                }
                $templateData = $request->all();
                $templateData['factor_id'] = $factor->factor_id;
                $templateData['template_id'] = $template_id;
                $template = HraTemplate::create($templateData);
                $templates[] = $template;
            }
            if (empty($templates)) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => "Template already exists."
                ], 409);
            }
            DB::commit();
            return response()->json([
                'message' => 'Templates added successfully for new factors.',
                'data' => $templates,
            ], 201);
        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return response()->json(['error' => 'Database error: ' . $e->getMessage()], 409);
            }
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function editTemplate(Request $request, $template_id)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'nullable|string|max:255',
            'total_adjustment_value' => 'nullable|integer',
            'factor_id' => 'nullable|integer',
            'maximum_value' => 'nullable|integer',
            'factor_adjustment_value' => 'nullable|integer',
            'health_index_formula' => 'nullable|string',
            'priority' => 'nullable|integer',
            'active_status' => 'nullable|integer|in:0,1',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        try {
            $template = HraTemplate::where('template_id', $template_id)->first();
            if (!$template) {
                return response()->json(['error' => 'Template not found'], 404);
            }
            $template->update($request->only([
                'template_name',
                'total_adjustment_value',
                'factor_id',
                'maximum_value',
                'factor_adjustment_value',
                'health_index_formula',
                'priority',
                'active_status'
            ]));
            return response()->json(['message' => 'Template updated successfully', 'data' => $template], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function deleteTemplate($template_id)
    {
        try {
            $template = HraTemplate::where('template_id', $template_id)->first();
            if (!$template) {
                return response()->json(['error' => 'Template not found'], 404);
            }
            $deletedTemplate = $template;
            HraTemplateQuestions::where('template_id', $template_id)->delete();
            $template->delete();
            return response()->json([
                'message' => 'Template deleted successfully',
                'deleted_template' => $deletedTemplate
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getTemplate($id)
    {
        $validator = Validator::make(['template_id' => $id], [
            'template_id' => 'required|exists:hra_templates,template_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'error', 'error' => $validator->errors()], 400);
        }
        try {
            $template = HraTemplate::where('template_id', $id)->get();
            if ($template->isEmpty()) {
                return response()->json(['error' => 'Template not found'], 404);
            }
            $factorIds = $template->pluck('factor_id')->filter()->unique()->values();
            $factors = HraFactor::pluck('factor_name', 'factor_id');
            $factorKeyValuePairs = $factorIds->mapWithKeys(function ($factorId) use ($factors) {
                return [$factorId => $factors[$factorId] ?? null];
            });
            $priorities = $template->pluck('priority', 'factor_id')->unique()->values();
            $prioritiesWithFactorId = $template->mapWithKeys(function ($item) {
                return [$item->factor_id => $item->priority];
            });
            $baseTemplate = $template->first();
            $baseTemplate->factors = $factorKeyValuePairs;
            $baseTemplate->priorities = $prioritiesWithFactorId;
            $baseTemplate->makeHidden(['factor_id']);
            return response()->json(['data' => $baseTemplate], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getAllTemplates(Request $request)
    {
        try {
            $templates = HraTemplate::all();
            $factors = HraFactor::pluck('factor_name', 'factor_id');
            $groupedTemplates = $templates->groupBy('template_id')->map(function ($group) use ($factors) {
                $baseTemplate = $group->first();
                $factorIds = $group->pluck('factor_id')->filter()->unique()->values();
                $factorKeyValuePairs = $factorIds->mapWithKeys(function ($factorId) use ($factors) {
                    return [$factorId => $factors[$factorId] ?? null];
                });
                $priorities = $group->pluck('priority', 'factor_id')->unique()->values();
                $prioritiesWithFactorId = $group->mapWithKeys(function ($item) {
                    return [$item->factor_id => $item->priority];
                });
                $baseTemplate->factors = $factorKeyValuePairs;
                $baseTemplate->priorities = $prioritiesWithFactorId;
                $baseTemplate->makeHidden(['factor_id']);
                return $baseTemplate;
            })->values();
            return response()->json(['data' => $groupedTemplates], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getAllFactorPriority(Request $request)
    {
        try {
            $templates = HraTemplate::where('priority', '>', 0)->get();
            $factors = HraFactor::where('active_status', 1)->get()->keyBy('factor_id');
            $groupedTemplates = $templates->groupBy('template_id')->map(function ($items, $templateId) use ($factors) {
                return [
                    'template_id' => $templateId,
                    'template_name' => $items->first()->template_name,
                    'factors' => $items->map(function ($item) use ($factors) {
                        return [
                            'factor_id' => $item->factor_id,
                            'factor_name' => $factors->get($item->factor_id)->factor_name ?? null,
                            'priority' => $item->priority,
                            'active_status' => $item->active_status,
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
                        ];
                    })->filter(function ($factor) {
                        return $factor['factor_name'] !== null;
                    }),
                ];
            })->values();
            return response()->json(['data' => $groupedTemplates], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getFactorPriority($template_id)
    {
        try {
            $templates = HraTemplate::where('template_id', $template_id)
                ->where('priority', '>', 0)
                ->get();
            if ($templates->isEmpty()) {
                return response()->json(['error' => 'Invalid template. No data found for the given template ID.'], 404);
            }
            $factors = HraFactor::where('active_status', 1)->get()->keyBy('factor_id');
            $response = [
                'template_id' => $template_id,
                'template_name' => $templates->first()->template_name ?? null,
                'factors' => $templates->map(function ($item) use ($factors) {
                    return [
                        'factor_id' => $item->factor_id,
                        'factor_name' => $factors->get($item->factor_id)->factor_name ?? null,
                        'priority' => $item->priority,
                        'active_status' => $item->active_status,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                })->filter(function ($factor) {
                    return $factor['factor_name'] !== null;
                })->values(),
            ];
            return response()->json(['data' => $response], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function setFactorPriority($template_id, Request $request)
    {
        $validated = $request->validate([
            'factor_id' => 'required|array',
            'factor_id.*' => 'integer',
        ]);
        $factorIds = $validated['factor_id'];
        if (count($factorIds) !== count(array_unique($factorIds))) {
            return response()->json([
                'message' => 'Invalid request. factor_id array contains duplicate values.'
            ], 400);
        }
        DB::beginTransaction();
        try {
            $templateExists = HraTemplate::where('template_id', $template_id)->exists();
            if (!$templateExists) {
                DB::rollBack();
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid template ID.'
                ], 404);
            }
            $validFactors = HraFactor::whereIn('factor_id', $factorIds)
                ->where('active_status', true)
                ->pluck('factor_id')
                ->toArray();
            if (count($validFactors) !== count($factorIds)) {
                $invalidFactors = array_diff($factorIds, $validFactors);
                DB::rollBack();
                return response()->json([
                    'message' => 'Invalid request. One or more factor_ids are inactive or do not exist.',
                    'inactive_factors' => $invalidFactors
                ], 400);
            }
            foreach ($factorIds as $factorId) {
                $existingRow = HraTemplate::where('template_id', $template_id)
                    ->where('factor_id', $factorId)
                    ->first();
                if (!$existingRow) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Bad Request, factor_id $factorId is not linked with $template_id"
                    ], 500);
                }
                if ($existingRow->active_status !== 1) {
                    DB::rollBack();
                    return response()->json([
                        'result' => false,
                        'message' => "This template is not activated."
                    ], 400);
                }
            }
            HraTemplate::where('template_id', $template_id)->update(['priority' => null]);
            foreach ($factorIds as $index => $factorId) {
                HraTemplate::where('template_id', $template_id)
                    ->where('factor_id', $factorId)
                    ->update(['priority' => $index + 1]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Priority updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal server error. Contact developer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function setQuestionFactorPriority(Request $request, $template_id, $factor_id)
    {
        try {
            $validatedData = $request->validate([
                'question_id' => 'nullable|array',
                'question_id.*' => 'integer|distinct'
            ]);
            $questionIds = $validatedData['question_id'] ?? [];
            $factor = HraFactor::where('factor_id', $factor_id)
                ->where('active_status', 1)
                ->first();
            if (!$factor) {
                return response()->json(['result' => 'error', 'message' => 'Factor is not Activated or Invalid Factor.'], 422);
            }
            $template = HraTemplate::where('template_id', $template_id)
                ->where('factor_id', $factor_id)
                ->where('active_status', 1)
                ->first();
            if (!$template) {
                return response()->json(['result' => 'error', 'message' => 'Template is not Activated or Invalid Template.'], 422);
            }
            $existingQuestionIds = HraTemplateQuestions::whereIn('question_id', $questionIds)
                ->where('template_id', $template_id)
                ->pluck('question_id')
                ->toArray();
            $filteredQuestionIds = array_values(array_diff($questionIds, $existingQuestionIds));
            if (empty($filteredQuestionIds)) {
                return response()->json(['result' => 'error', 'message' => 'Nothing Updated'], 404);
            }
            $factorPriority = $template->priority;
            if (empty($questionIds)) {
                HraTemplateQuestions::where('template_id', $template_id)
                    ->where('factor_id', $factor_id)
                    ->delete();
                return response()->json(['result' => 'success', 'message' => 'All questions deleted successfully.'], 200);
            }
            $invalidQuestions = array_filter($questionIds, function ($questionId) {
                return !HraQuestions::where('question_id', $questionId)->exists();
            });
            if (!empty($invalidQuestions)) {
                return response()->json([
                    'result' => 'error',
                    'message' => 'Invalid question IDs provided.',
                    'invalid_question_ids' => $invalidQuestions,
                ], 422);
            }
            HraTemplateQuestions::where('template_id', $template_id)
                ->where('factor_id', $factor_id)
                ->whereNotIn('question_id', $questionIds)
                ->delete();
            foreach ($questionIds as $index => $questionId) {
                $priority = $index + 1;
                $existingRow = HraTemplateQuestions::where('template_id', $template_id)
                    ->where('factor_id', $factor_id)
                    ->where('question_id', $questionId)
                    ->first();
                if ($existingRow) {
                    $existingRow->update(['question_priority' => $priority]);
                } else {
                    HraTemplateQuestions::create([
                        'template_id' => $template_id,
                        'factor_id' => $factor_id,
                        'question_id' => $questionId,
                        'question_priority' => $priority,
                        'factor_priority' => $factorPriority,
                        'type' => 0,
                        'status' => 1,
                    ]);
                }
            }
            return response()->json(['result' => 'success', 'message' => 'Question priorities and factor priority updated successfully.'], 200);
        } catch (QueryException $e) {
            return response()->json(['result' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getAllQuestionFactorPriority($templateId)
    {
        try {
            $data = HraTemplateQuestions::all(['template_id', 'factor_id', 'question_id', 'question_priority', 'factor_priority']);
            if ($data->isEmpty()) {
                return response()->json(['result' => 'success', 'message' => 'No Data Found'], 404);
            }
            $groupedData = $data->groupBy(function ($item) {
                return $item->template_id . '-' . $item->factor_id;
            })->map(function ($group) use ($templateId) {
                $firstItem = $group->first();
                if ($firstItem->template_id != $templateId) {
                    return null;
                }
                $template = HraTemplate::find($firstItem->template_id);
                $factor = HraFactor::find($firstItem->factor_id);
                $questions = $group->map(function ($item) {
                    $question = HraQuestions::find($item->question_id);
                    return [
                        'question_id' => $item->question_id,
                        'question_name' => $question ? $question->question : 'N/A',
                        'question_priority' => $item->question_priority,
                        'points' => $question->points,
                        'gender' => $question->gender
                    ];
                });
                $sortedQuestions = $questions->sortBy('question_priority');
                return [
                    'Template_id' => $firstItem->template_id,
                    'Factor_id' => $firstItem->factor_id,
                    'Template_name' => $template ? $template->template_name : 'N/A',
                    'Factor_name' => $factor ? $factor->factor_name : 'N/A',
                    'Factor_priority' => $firstItem->factor_priority,
                    'questions' => $sortedQuestions,
                ];
            });
            $filteredData = $groupedData->filter();
            if ($filteredData->isEmpty()) {
                return response()->json(['result' => 'success', 'message' => 'No Data Found for the specified template_id'], 404);
            }
            return response()->json(['result' => 'success', 'message' => $filteredData], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getQuestionFactorPriority($template_id, $factor_id)
    {
        try {
            $data = HraTemplateQuestions::where('template_id', $template_id)
                ->where('factor_id', $factor_id)
                ->get(['template_id', 'factor_id', 'question_id', 'question_priority', 'factor_priority']);
            if ($data->isEmpty()) {
                return response()->json(['result' => 'error', 'message' => 'No data found for the given template_id and factor_id in hra_template_questions.'], 404);
            }
            $template = HraTemplate::find($template_id);
            $factor = HraFactor::find($factor_id);
            if (!$template || !$factor) {
                return response()->json(['result' => 'error', 'message' => 'Invalid Template or Factor ID.'], 400);
            }
            $questions = $data->sortBy('question_priority')->map(function ($item) {
                $question = HraQuestions::find($item->question_id);
                return [
                    'question_id' => $item->question_id,
                    'question_name' => $question ? $question->question : 'Unknown',
                    'question_priority' => $item->question_priority,
                    'gender' => $question->gender
                ];
            });
            $result = [
                'Template_id' => $template_id,
                'Factor_id' => $factor_id,
                'Template_name' => $template->template_name,
                'Factor_name' => $factor->factor_name,
                'Factor_priority' => $data->first()->factor_priority,
                'questions' => array_values($questions->toArray()),
            ];
            return response()->json(['result' => 'success', 'data' => $result], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function setTriggerQuestionFactorPriority(Request $request, $template_id, $factor_id, $question_id)
    {
        $validated = $request->validate([
            'trigger_1' => 'array|max:100',
            'trigger_1.*' => 'integer',
            'trigger_2' => 'array|max:100',
            'trigger_2.*' => 'integer',
            'trigger_3' => 'array|max:100',
            'trigger_3.*' => 'integer',
            'trigger_4' => 'array|max:100',
            'trigger_4.*' => 'integer',
            'trigger_5' => 'array|max:100',
            'trigger_5.*' => 'integer',
            'trigger_6' => 'array|max:100',
            'trigger_6.*' => 'integer',
            'trigger_7' => 'array|max:100',
            'trigger_7.*' => 'integer',
            'trigger_8' => 'array|max:100',
            'trigger_8.*' => 'integer',
        ]);
        $factor = HraFactor::where('factor_id', $factor_id)
            ->where('active_status', 1)
            ->first();
        if (!$factor) {
            return response()->json(['result' => 'error', 'message' => 'Factor is not active or does not exist'], 400);
        }
        $template = HraTemplate::where('template_id', $template_id)
            ->where('factor_id', $factor_id)
            ->where('active_status', 1)
            ->where('priority', '>=', 1)
            ->first();
        if (!$template) {
            return response()->json(['result' => 'error', 'message' => 'Template does not exist or is not active'], 400);
        }
        $templateQuestion = HraTemplateQuestions::where('template_id', $template_id)
            ->where('factor_id', $factor_id)
            ->where('question_id', $question_id)
            ->first();
        if (!$templateQuestion) {
            return response()->json(['result' => 'error', 'message' => 'Template Question row not found'], 404);
        }
        $allTriggerIds = [];
        $maxTriggerValue = 0;
        for ($i = 1; $i <= 8; $i++) {
            $triggerKey = "trigger_$i";
            if (isset($validated[$triggerKey])) {
                foreach ($validated[$triggerKey] as $id) {
                    $allTriggerIds[] = $id;
                }
                $maxTriggerValue = max($maxTriggerValue, $i);
            }
        }
        $questions = HraQuestions::whereIn('question_id', array_unique($allTriggerIds))
            ->where('active_status', 1)
            ->pluck('question_id')
            ->toArray();
        if (count($allTriggerIds) !== count(array_intersect($allTriggerIds, $questions))) {
            return response()->json(['result' => 'error', 'message' => 'One or more question IDs are invalid or inactive'], 400);
        }
        $question = HraQuestions::where('question_id', $question_id)->first();
        if (!$question) {
            return response()->json(['result' => 'error', 'message' => 'Question not found'], 400);
        }
        $answerData = json_decode($question->answer, true);
        if (count($answerData) < $maxTriggerValue) {
            return response()->json(['result' => 'error', 'message' => "Answer data must contain at least $maxTriggerValue key-value pairs"], 400);
        }
        $updateData = [];
        for ($i = 1; $i <= 8; $i++) {
            $triggerKey = "trigger_$i";
            if (isset($validated[$triggerKey])) {
                $updateData[$triggerKey] = json_encode(
                    collect($validated[$triggerKey])
                        ->mapWithKeys(fn ($id, $index) => ["question-" . ($index + 1) => $id])
                        ->toArray()
                );
            } else {
                $updateData[$triggerKey] = null;
            }
        }
        $templateQuestion->update($updateData);
        return response()->json(['result' => 'success', 'message' => 'Triggers updated successfully', 'updated_data' => $updateData], 200);
    }
    public function getTriggerQuestionFactorPriority($template_id, $factor_id, $question_id)
    {
        $factor = HraFactor::where('factor_id', $factor_id)
            ->where('active_status', 1)
            ->first();
        if (!$factor) {
            return response()->json(['error' => 'Factor is not active or does not exist'], 400);
        }
        $factorName = $factor->factor_name;
        $template = HraTemplate::where('template_id', $template_id)
            ->where('factor_id', $factor_id)
            ->where('active_status', 1)
            ->where('priority', '>=', 1)
            ->first();
        if (!$template) {
            return response()->json(['error' => 'Template does not exist or is not active'], 400);
        }
        $templateName = $template->template_name;
        $templateQuestion = HraTemplateQuestions::where('template_id', $template_id)
            ->where('factor_id', $factor_id)
            ->where('question_id', $question_id)
            ->first();
        if (!$templateQuestion) {
            return response()->json(['error' => 'Template Question row not found'], 404);
        }
        $question = HraQuestions::where('question_id', $question_id)->first();
        if (!$question) {
            return response()->json(['error' => 'Question not found'], 404);
        }
        $triggers = [];
        for ($i = 1; $i <= 8; $i++) {
            $triggerKey = "trigger_$i";
            $triggerData = $templateQuestion->{$triggerKey};
            if ($triggerData) {
                $triggers[$triggerKey] = json_decode($triggerData, true);
            }
        }
        return response()->json([
            'success' => true,
            'message' => [
                'template_id' => $template_id,
                'factor_id' => $factor_id,
                'question_id' => $question_id,
                'factor_name' => $factorName,
                'template_name' => $templateName,
                'question_text' => $question->question,
                'triggers' => $triggers
            ],
            'result' => 'success'
        ], 200);
    }
    public function publishTemplate(Request $request, $template_id)
    {
        if (!is_numeric($template_id)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request.'
            ], 400);
        }
        $validated = $request->validate([
            'total_adjustment_value' => 'required|integer',
            'factors' => 'required|array|min:1',
            'factors.*.factor_id' => 'required|integer',
            'factors.*.max_value' => 'required|integer',
            'factors.*.factor_adjustment_value' => 'required|integer',
        ]);
        $template = HraTemplate::where('template_id', $template_id)->where('active_status', 1)->first();
        if (!$template) {
            return response()->json([
                'result' => false,
                'message' => 'Template is not active or does not exist.'
            ], 400);
        }
        $factorIds = array_column($validated['factors'], 'factor_id');
        $activeFactors = HraFactor::whereIn('factor_id', $factorIds)->where('active_status', 1)->pluck('factor_id')->toArray();
        if (count($activeFactors) !== count($factorIds)) {
            return response()->json([
                'result' => false,
                'message' => 'One or more factors are not active.'
            ], 400);
        }
        DB::beginTransaction();
        try {
            $template->total_adjustment_value = $validated['total_adjustment_value'];
            $template->save();
            foreach ($validated['factors'] as $factor) {
                DB::table('hra_templates')
                    ->where('template_id', $template_id)
                    ->where('factor_id', $factor['factor_id'])
                    ->update([
                        'published' => 1,
                        'maximum_value' => $factor['max_value'],
                        'factor_adjustment_value' => $factor['factor_adjustment_value']
                    ]);
            }
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Template published successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Error publishing template',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

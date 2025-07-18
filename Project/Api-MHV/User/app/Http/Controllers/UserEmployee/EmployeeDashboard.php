<?php

namespace App\Http\Controllers\UserEmployee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hra\Templates\HraTemplate;
use App\Models\Corporate\HraAssignedTemplate;
use App\Models\Hra\Templates\HraTemplateQuestions;
use App\Models\Hra\Templates\HraInduvidualAnswer;
use Illuminate\Support\Facades\DB;

class EmployeeDashboard extends Controller
{
    public function getAllAssignedTemplates(Request $request)
    {
        try {
            $corporateId = $request->corporate_id;
            $locationId = $request->location_id;
            $designation = $request->designation;
            $employeeTypeId = $request->employee_type_id;
            $departmentId = $request->department;
            $assignedTemplates = HraAssignedTemplate::where('corporate_id', $corporateId)
                ->where(function ($query) use ($locationId) {
                    $query->where('location', $locationId)
                        ->orWhere('location', 'all');
                })
                ->get();
            if ($assignedTemplates->isEmpty()) {
                return response()->json(['result' => false, 'data' => 'No templates assigned'], 404);
            }
            $validTemplates = [];
            foreach ($assignedTemplates as $template) {
                $designations = (array) $template->designation;
                $employeeTypes = (array) $template->employee_type;
                $departments = (array) $template->department;
                $designationMatch = is_null($template->designation) || in_array($designation, $designations) || in_array('all', $designations);
                $employeeTypeMatch = is_null($template->employee_type) || in_array($employeeTypeId, $employeeTypes) || in_array('all', $employeeTypes);
                $departmentMatch = is_null($template->department) || in_array($departmentId, $departments) || in_array('all', $departments);
                if ($designationMatch && $employeeTypeMatch && $departmentMatch) {
                    $templateData = HraTemplate::where('template_id', $template->template_id)->first();
                    if ($templateData) {
                        $validTemplates[] = [
                            'template_id' => $templateData->template_id,
                            'template_name' => $templateData->template_name
                        ];
                    }
                }
            }
            if (empty($validTemplates)) {
                return response()->json(['result' => true, 'data' => 'No templates assigned'], 404);
            }
            return response()->json(['result' => true, 'data' => $validTemplates], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => "Internal Server Error"], 500);
        }
    }
    private function checkTemplate(Request $request, $templateId)
    {
        $corporateId = $request->corporate_id;
        $locationId = $request->location_id;
        return HraAssignedTemplate::where('corporate_id', $corporateId)
            ->where('template_id', $templateId)
            ->where(function ($query) use ($locationId) {
                $query->where('location', $locationId)
                    ->orWhere('location', 'all');
            })
            ->get();
    }
    public function checkTemplateAccess(Request $request, $templateId)
    {
        try {
            $designation = $request->designation;
            $employeeTypeId = $request->employee_type_id;
            $departmentId = $request->department;
            $assignedTemplates = $this->checkTemplate($request, $templateId);
            foreach ($assignedTemplates as $template) {
                $designations = (array) $template->designation;
                $employeeTypes = (array) $template->employee_type;
                $departments = (array) $template->department;
                $designationMatch = is_null($template->designation) || in_array($designation, $designations) || in_array('all', $designations);
                $employeeTypeMatch = is_null($template->employee_type) || in_array($employeeTypeId, $employeeTypes) || in_array('all', $employeeTypes);
                $departmentMatch = is_null($template->department) || in_array($departmentId, $departments) || in_array('all', $departments);
                if ($designationMatch && $employeeTypeMatch && $departmentMatch) {
                    $templateData = HraTemplate::where('template_id', $template->template_id)->first();
                    if ($templateData) {
                        $isQuestionsAvailableForThisTemplate = HraTemplateQuestions::where('template_id', $templateData->template_id)->exists();
                        return response()->json([
                            'result' => true,
                            'data' => [
                                'template_id' => $templateData->template_id,
                                'template_name' => $templateData->template_name,
                                'is_questions_available' => $isQuestionsAvailableForThisTemplate,
                            ]
                        ], 200);
                    }
                }
            }
            return response()->json(['result' => false, 'data' => 'Invalid Template'], 403);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => "Internal Server Error"], 500);
        }
    }
    public function getTemplateQuestions(Request $request, $templateId)
    {
        try {
            $userId = $request->user_id;
            if ($userId === null) {
                return response()->json(['result' => false, 'data' => 'Invalid Request'], 403);
            }
            if (!is_numeric($templateId)) {
                return response()->json(['result' => false, 'data' => 'Invalid Template'], 403);
            }
            $isTemplateAssigned = $this->checkTemplateAccess($request, $templateId);
            $data = $isTemplateAssigned->getData(true);
            if (!$data['result']) {
                return response()->json(['result' => false, 'data' => 'Invalid Template'], 403);
            }
            $templateQuestions = DB::table('hra_template_questions as htq')
                ->join('hra_question as hq', 'htq.question_id', '=', 'hq.question_id')
                ->leftJoin('hra_factors as hf', 'htq.factor_id', '=', 'hf.factor_id')
                ->where('htq.template_id', $templateId)
                ->orderBy('htq.factor_priority', 'asc')
                ->select([
                    'hf.factor_name',
                    'htq.factor_priority',
                    'htq.question_id',
                    'htq.question_priority',
                    'htq.trigger_1',
                    'htq.trigger_2',
                    'htq.trigger_3',
                    'htq.trigger_4',
                    'htq.trigger_5',
                    'htq.trigger_6',
                    'htq.trigger_7',
                    'htq.trigger_8',
                    'hq.question',
                    'hq.answer',
                    'hq.formula',
                    'hq.test_id',
                    'hq.gender',
                    'hq.comp_value',
                    'hq.types',
                ])
                ->get()
                ->map(function ($item) {
                    foreach (range(1, 8) as $i) {
                        $triggerKey = "trigger_$i";
                        if (!empty($item->$triggerKey)) {
                            $triggerDecoded = json_decode($item->$triggerKey, true);
                            if (is_array($triggerDecoded)) {
                                $newTriggerData = [];
                                foreach ($triggerDecoded as $key => $val) {
                                    if (is_numeric($val)) {
                                        $questionData = DB::table('hra_question')
                                            ->select('question_id', 'question', 'answer', 'types')
                                            ->where('question_id', $val)
                                            ->first();
                                        if ($questionData) {
                                            $newTriggerData[$key] = [
                                                'question_id' => $questionData->question_id,
                                                'question' => $questionData->question,
                                                'answer' => json_decode($questionData->answer, true),
                                                'types' => $questionData->types,
                                            ];
                                        } else {
                                            $newTriggerData[$key] = null;
                                        }
                                    } else {
                                        $newTriggerData[$key] = $val;
                                    }
                                }
                                $item->$triggerKey = $newTriggerData;
                            }
                        }
                    }
                    return $item;
                });
            if ($templateQuestions->isEmpty()) {
                return response()->json(['result' => false, 'data' => 'No questions found for this template'], 404);
            }
            $individualAnswers = DB::table('hra_induvidual_answers')
                ->where('user_id', $userId)
                ->where('template_id', $templateId)
                ->select('question_id', 'answer', 'trigger_question_of')
                ->get()
                ->keyBy('question_id');
            $mergedQuestions = $templateQuestions->map(function ($question) use ($individualAnswers) {
                $questionId = $question->question_id;
                if (isset($individualAnswers[$questionId])) {
                    $individualAnswer = $individualAnswers[$questionId];
                    if ($individualAnswer->trigger_question_of === null) {
                        $question->answered = $individualAnswer->answer;
                        $question->trigger_question_of = null;
                    }
                }
                foreach (range(1, 8) as $i) {
                    $triggerKey = "trigger_$i";
                    if (!empty($question->$triggerKey) && is_array($question->$triggerKey)) {
                        foreach ($question->$triggerKey as $triggerQuestionKey => $triggerQuestion) {
                            if (is_array($triggerQuestion) && isset($triggerQuestion['question_id'])) {
                                $triggerQuestionId = $triggerQuestion['question_id'];
                                if (isset($individualAnswers[$triggerQuestionId])) {
                                    $triggerIndividualAnswer = $individualAnswers[$triggerQuestionId];
                                    if ($triggerIndividualAnswer->trigger_question_of == $questionId) {
                                        $question->{$triggerKey}[$triggerQuestionKey]['answered'] = $triggerIndividualAnswer->answer;
                                        $question->{$triggerKey}[$triggerQuestionKey]['trigger_question_of'] = $triggerIndividualAnswer->trigger_question_of;
                                    }
                                }
                            }
                        }
                    }
                }
                return $question;
            });
            return response()->json(['result' => true, 'data' => $mergedQuestions], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => "Internal Server Error"], 500);
        }
    }
    public function saveHraTemplateQuestionnaireAnswers(Request $request, $templateId)
    {
        try {
            if (!is_numeric($templateId)) {
                return response()->json(['result' => false, 'data' => 'Invalid Request'], 403);
            }
            $validated = $request->validate([
                'template_id' => 'required|integer|in:' . $templateId,
                'is_partial' => 'required|boolean',
                'answers' => 'required|array|min:1|max:500',
                'answers.*.question_id' => 'required|integer',
                'answers.*.answer' => 'required',
                'answers.*.triggers' => 'sometimes|array',
                'answers.*.triggers.*.question_id' => 'required_with:answers.*.triggers|integer',
                'answers.*.triggers.*.answer' => 'required_with:answers.*.triggers',
            ]);
            $is_partial = $validated['is_partial'];
            $submittedQuestionIds = collect($validated['answers'])->flatMap(function ($answer) {
                $ids = [$answer['question_id']];
                if (isset($answer['triggers'])) {
                    $triggerIds = collect($answer['triggers'])->pluck('question_id')->filter()->all();
                    $ids = array_merge($ids, $triggerIds);
                }
                return $ids;
            })->unique()->values()->all();
            $questions = HraTemplateQuestions::where('template_id', $templateId)->get();
            $validQuestionIds = $questions->flatMap(function ($q) {
                $ids = [(int) $q->question_id];
                for ($i = 1; $i <= 8; $i++) {
                    $triggerField = $q->{"trigger_$i"};
                    if ($triggerField) {
                        $decoded = json_decode($triggerField, true);
                        if (is_array($decoded)) {
                            $ids = array_merge($ids, array_map('intval', array_values($decoded)));
                        }
                    }
                }
                return $ids;
            })->unique()->values()->all();
            $invalidIds = array_diff($submittedQuestionIds, $validQuestionIds);
            if (!empty($invalidIds)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Request',
                ], 422);
            } 
            foreach ($validated['answers'] as $answer) {
                $motherId = $answer['question_id'];
                if (isset($answer['triggers'])) {
                    $motherQuestion = $questions->firstWhere('question_id', $motherId);
                    if (!$motherQuestion) {
                        return response()->json([
                            'result' => false,
                            'message' => "Mother question ID $motherId not found in template.",
                        ], 422);
                    }
                    $triggerFields = [
                        $motherQuestion->trigger_1,
                        $motherQuestion->trigger_2,
                        $motherQuestion->trigger_3,
                        $motherQuestion->trigger_4,
                        $motherQuestion->trigger_5,
                        $motherQuestion->trigger_6,
                        $motherQuestion->trigger_7,
                        $motherQuestion->trigger_8,
                    ];
                    $allTriggerValues = collect($triggerFields)
                        ->filter()
                        ->flatMap(function ($json) {
                            $decoded = json_decode($json, true);
                            return is_array($decoded) ? array_values($decoded) : [];
                        })
                        ->map(fn($id) => (int) $id)
                        ->values()
                        ->all();
                    foreach ($answer['triggers'] as $trigger) {
                        if (!in_array((int) $trigger['question_id'], $allTriggerValues)) {
                            return response()->json([
                                'result' => false,
                                'message' => "Trigger question ID {$trigger['question_id']} is not a valid child of mother question ID $motherId.",
                            ], 422);
                        }
                    }
                }
                $existingAnswer = HraInduvidualAnswer::where('template_id', $templateId)
                    ->where('user_id', $request->user_id)
                    ->where('question_id', $motherId)
                    ->where('trigger_question_of', null)
                    ->first();
                $answerData = [
                    'template_id' => $templateId,
                    'user_id' => $request->user_id,
                    'question_id' => $motherId,
                    'trigger_question_of' => null,
                    'answer' => is_array($answer['answer']) ? json_encode($answer['answer']) : $answer['answer'],
                    'points' => 0,
                    'test_results' => null,
                    'question_status' => 1,
                    'reference_question' => 0,
                ];
                if ($existingAnswer) {
                    $existingAnswer->update(['answer' => $answerData['answer']]);
                } else {
                    HraInduvidualAnswer::create($answerData);
                }
                if (isset($answer['triggers'])) {
                    foreach ($answer['triggers'] as $trigger) {
                        $existingTriggerAnswer = HraInduvidualAnswer::where('template_id', $templateId)
                            ->where('user_id', $request->user_id)
                            ->where('question_id', $trigger['question_id'])
                            ->where('trigger_question_of', $motherId)
                            ->first();
                        $triggerAnswerData = [
                            'template_id' => $templateId,
                            'user_id' => $request->user_id,
                            'question_id' => $trigger['question_id'],
                            'trigger_question_of' => $motherId,
                            'answer' => is_array($trigger['answer']) ? json_encode($trigger['answer']) : $trigger['answer'],
                            'points' => 0,
                            'test_results' => null,
                            'question_status' => 1,
                            'reference_question' => 0,
                        ];
                        if ($existingTriggerAnswer) {
                            $existingTriggerAnswer->update(['answer' => $triggerAnswerData['answer']]);
                        } else {
                            HraInduvidualAnswer::create($triggerAnswerData);
                        }
                    }
                }
            }
            return response()->json(['result' => true, 'data' => 'Answers saved successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

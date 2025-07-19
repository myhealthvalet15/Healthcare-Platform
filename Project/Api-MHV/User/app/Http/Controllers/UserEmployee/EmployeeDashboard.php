<?php

namespace App\Http\Controllers\UserEmployee;

use App\Http\Controllers\Controller;
use App\Models\Corporate\EmployeeUserMapping;
use Illuminate\Http\Request;
use App\Models\Hra\Templates\HraTemplate;
use App\Models\Corporate\HraAssignedTemplate;
use App\Models\Hra\Templates\HraTemplateQuestions;
use App\Models\Hra\Questions\HraQuestions;
use App\Models\Hra\Templates\HraInduvidualAnswer;
use App\Models\HraOverallResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeDashboard extends Controller
{
    private function matches($value, $list)
    {
        if (is_null($list)) {
            return true;
        }
        $array = is_array($list) ? $list : (array) $list;
        return in_array($value, $array) || in_array('all', $array);
    }
    public function getAllAssignedTemplates(Request $request)
    {
        try {
            $corporateId = $request->corporate_id;
            $locationId = $request->location_id;
            $designation = $request->designation;
            $employeeTypeId = $request->employee_type_id;
            $departmentId = $request->department;
            $assignedTemplates = HraAssignedTemplate::where('corporate_id', $corporateId)
                ->where(function ($q) use ($locationId) {
                    $q->where('location', $locationId)
                        ->orWhere('location', 'all');
                })
                ->get();
            if ($assignedTemplates->isEmpty()) {
                return response()->json(['result' => false, 'data' => 'No templates assigned'], 404);
            }
            $templateIds = $assignedTemplates->pluck('template_id')->unique()->toArray();
            $templateModels = HraTemplate::whereIn('template_id', $templateIds)
                ->get(['template_id', 'template_name'])
                ->keyBy('template_id');
            $validTemplates = $assignedTemplates->filter(function ($t) use ($designation, $employeeTypeId, $departmentId) {
                return
                    $this->matches($designation, $t->designation) &&
                    $this->matches($employeeTypeId, $t->employee_type) &&
                    $this->matches($departmentId, $t->department);
            })->map(function ($t) use ($templateModels) {
                $template = $templateModels[$t->template_id] ?? null;
                return $template ? [
                    'template_id'   => $template->template_id,
                    'template_name' => $template->template_name
                ] : null;
            })->filter()->values()->all();
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
                if (
                    $this->matches($designation, $template->designation) &&
                    $this->matches($employeeTypeId, $template->employee_type) &&
                    $this->matches($departmentId, $template->department)
                ) {
                    $templateData = HraTemplate::where('template_id', $template->template_id)->first();
                    if ($templateData) {
                        $isQuestionsAvailable = HraTemplateQuestions::where('template_id', $templateData->template_id)->exists();
                        $isHraOverallResultsExists = HraOverallResult::where('user_id', $request->user_id)
                            ->where('hra_template_id', $templateId)
                            ->exists();
                        return response()->json([
                            'result' => true,
                            'data' => [
                                'template_id' => $templateData->template_id,
                                'template_name' => $templateData->template_name,
                                'is_questions_available' => $isQuestionsAvailable,
                                'is_hra_overall_results_exists' => $isHraOverallResultsExists,
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
            if (!$userId || !is_numeric($templateId)) {
                return response()->json(['result' => false, 'data' => 'Invalid Request'], 403);
            }
            $access = $this->checkTemplateAccess($request, $templateId)->getData(true);
            if (!$access['result']) {
                return response()->json(['result' => false, 'data' => 'Invalid Template'], 403);
            }
            $questions = DB::table('hra_template_questions as htq')
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
                ])->get();
            if ($questions->isEmpty()) {
                return response()->json(['result' => false, 'data' => 'No questions found for this template'], 404);
            }
            $triggerQuestionIds = [];
            foreach ($questions as $item) {
                foreach (range(1, 8) as $i) {
                    $triggerKey = "trigger_$i";
                    if (!empty($item->$triggerKey)) {
                        $decoded = json_decode($item->$triggerKey, true);
                        if ($decoded && is_array($decoded)) {
                            $triggerQuestionIds = array_merge($triggerQuestionIds, array_values($decoded));
                        }
                    }
                }
            }
            $triggerQuestionIds = array_unique(array_filter($triggerQuestionIds, 'is_numeric'));
            $triggerQuestions = [];
            if ($triggerQuestionIds) {
                $triggerQuestions = DB::table('hra_question')
                    ->whereIn('question_id', $triggerQuestionIds)
                    ->select('question_id', 'question', 'answer', 'types')
                    ->get()
                    ->keyBy('question_id')->toArray();
            }
            $templateQuestions = $questions->map(function ($item) use ($triggerQuestions) {
                foreach (range(1, 8) as $i) {
                    $triggerKey = "trigger_$i";
                    if (!empty($item->$triggerKey)) {
                        $decoded = json_decode($item->$triggerKey, true);
                        if (is_array($decoded)) {
                            $item->$triggerKey = collect($decoded)->map(function ($val, $k) use ($triggerQuestions) {
                                if (is_numeric($val) && isset($triggerQuestions[$val])) {
                                    $q = $triggerQuestions[$val];
                                    return [
                                        'question_id' => $q->question_id,
                                        'question' => $q->question,
                                        'answer' => json_decode($q->answer, true),
                                        'types' => $q->types,
                                    ];
                                }
                                return $val;
                            })->all();
                        }
                    }
                }
                return $item;
            });
            $individualAnswers = DB::table('hra_induvidual_answers')
                ->where('user_id', $userId)
                ->where('template_id', $templateId)
                ->select('question_id', 'answer', 'trigger_question_of')
                ->get()
                ->keyBy(function ($item) {
                    return $item->question_id . '-' . ($item->trigger_question_of ?: 'main');
                });
            $mergedQuestions = $templateQuestions->map(function ($question) use ($individualAnswers) {
                $qid = $question->question_id;
                $mainKey = $qid . '-main';
                if (isset($individualAnswers[$mainKey])) {
                    $ia = $individualAnswers[$mainKey];
                    $question->answered = $ia->answer;
                    $question->trigger_question_of = null;
                }
                foreach (range(1, 8) as $i) {
                    $triggerKey = "trigger_$i";
                    if (!empty($question->$triggerKey) && is_array($question->$triggerKey)) {
                        foreach ($question->$triggerKey as $k => $triggerQuestion) {
                            if (is_array($triggerQuestion) && isset($triggerQuestion['question_id'])) {
                                $tk = $triggerQuestion['question_id'] . '-' . $qid;
                                if (isset($individualAnswers[$tk])) {
                                    $tAns = $individualAnswers[$tk];
                                    $question->{$triggerKey}[$k]['answered'] = $tAns->answer;
                                    $question->{$triggerKey}[$k]['trigger_question_of'] = $tAns->trigger_question_of;
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
            $answers = collect($validated['answers']);
            $userId = $request->user_id;
            $questions = HraTemplateQuestions::where('template_id', $templateId)->get();
            $validQuestionIds = $questions->flatMap(function ($q) {
                $ids = [(int)$q->question_id];
                for ($i = 1; $i <= 8; $i++) {
                    if ($q->{"trigger_$i"}) {
                        $decoded = json_decode($q->{"trigger_$i"}, true);
                        if (is_array($decoded)) {
                            $ids = array_merge($ids, array_map('intval', array_values($decoded)));
                        }
                    }
                }
                return $ids;
            })->unique()->values()->all();
            $submittedIds = $answers->flatMap(function ($a) {
                $ids = [$a['question_id']];
                if (isset($a['triggers'])) {
                    $ids = array_merge($ids, collect($a['triggers'])->pluck('question_id')->all());
                }
                return $ids;
            })->unique()->values()->all();
            $invalidIds = array_diff($submittedIds, $validQuestionIds);
            if ($invalidIds) {
                return response()->json(['result' => false, 'data' => 'Invalid Request'], 422);
            }
            $allNeededIds = $submittedIds;
            $hraQuestions = HraQuestions::whereIn('question_id', $allNeededIds)
                ->get(['question_id', 'answer', 'points'])
                ->keyBy('question_id');
            DB::beginTransaction();
            if (! $validated['is_partial']) {
                $actualPoints = 0;
                $factorAdjustmentValue = [];
                $corporateTemplateId = 0; // TODO:
                $hraTemplateId = $templateId;
                $corporateId = $request->corporate_id;
                $locationId = $request->location_id;
                $hl1 = EmployeeUserMapping::where('user_id', $userId)
                    ->where('corporate_id', $corporateId)
                    ->where('location_id', $locationId)
                    ->value('hl1_id');
                $designation = $request->designation;
                $completedDate = now(); // TODO:
                $resultText = 'Answers saved successfully.'; // TODO:
            }
            foreach ($answers as $answer) {
                $motherId = $answer['question_id'];
                $mainHraQ = $hraQuestions[$motherId] ?? null;
                $mainPts = null;
                if ($mainHraQ) {
                    $answerArr = json_decode($mainHraQ->answer, true);
                    $pointsArr = json_decode($mainHraQ->points, true);
                    if (is_array($answerArr) && is_array($pointsArr)) {
                        $idx = array_search($answer['answer'], $answerArr, true);
                        if ($idx !== false && isset($pointsArr[$idx])) {
                            $mainPts = $pointsArr[$idx];
                        }
                    }
                }
                $actualPoints += $mainPts ?? 0;
                $mainRecord = [
                    'template_id' => $templateId,
                    'user_id' => $userId,
                    'question_id' => $motherId,
                    'trigger_question_of' => null,
                    'answer' => is_array($answer['answer']) ? json_encode($answer['answer']) : $answer['answer'],
                    'points' => $mainPts,
                    'test_results' => null,
                    'question_status' => 1,
                    'reference_question' => 0,
                ];
                HraInduvidualAnswer::updateOrCreate(
                    [
                        'template_id' => $templateId,
                        'user_id' => $userId,
                        'question_id' => $motherId,
                        'trigger_question_of' => null
                    ],
                    $mainRecord
                );
                if (!empty($answer['triggers'])) {
                    foreach ($answer['triggers'] as $trigger) {
                        $triggerId = $trigger['question_id'];
                        $triggerQ = $hraQuestions[$triggerId] ?? null;
                        $triggerPts = null;
                        if ($triggerQ) {
                            $answerArr = json_decode($triggerQ->answer, true);
                            $pointsArr = json_decode($triggerQ->points, true);
                            if (is_array($answerArr) && is_array($pointsArr)) {
                                $idx = array_search($trigger['answer'], $answerArr, true);
                                if ($idx !== false && isset($pointsArr[$idx])) {
                                    $triggerPts = $pointsArr[$idx];
                                }
                            }
                        }
                        $actualPoints += $triggerPts ?? 0;
                        $triggerRecord = [
                            'template_id' => $templateId,
                            'user_id' => $userId,
                            'question_id' => $triggerId,
                            'trigger_question_of' => $motherId,
                            'answer' => is_array($trigger['answer']) ? json_encode($trigger['answer']) : $trigger['answer'],
                            'points' => $triggerPts,
                            'test_results' => null,
                            'question_status' => 1,
                            'reference_question' => 0,
                        ];
                        HraInduvidualAnswer::updateOrCreate(
                            [
                                'template_id' => $templateId,
                                'user_id' => $userId,
                                'question_id' => $triggerId,
                                'trigger_question_of' => $motherId
                            ],
                            $triggerRecord
                        );
                    }
                }
            }
            if (! $validated['is_partial'] && $actualPoints !== 0) {
                $templateAdjustmentValue = $this->findTotalAdjustmentValue($templateId);
                $maximumValue = $this->findMaximumValue($templateId);
                if ($templateAdjustmentValue === null) {
                    $obtainedPoints = $actualPoints;
                } else {
                    $obtainedPoints = $actualPoints + $templateAdjustmentValue;
                }
                $healthIndex = round(($obtainedPoints / ($maximumValue + $templateAdjustmentValue)) * 100, 2);
                $factorScore = 0;
                $isHraOverallResultsExists = HraOverallResult::where('user_id', $userId)
                    ->where('hra_template_id', $hraTemplateId)
                    ->exists();
                if ($isHraOverallResultsExists) {
                    return response()->json([
                        'result' => false,
                        'data' => 'HRA result already exists for this user and template.'
                    ], 422);
                }
                $hraOverallResult = HraOverallResult::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'corporate_template_id' => 0,
                        'hra_template_id' => $hraTemplateId,
                        'corporate_id' => $corporateId,
                        'location_id' => $locationId,
                        'hl1' => $hl1,
                        'designation' => $designation,
                        'completed_date' => $completedDate,
                    ],
                    [
                        'obtained_points' => $obtainedPoints,
                        'actual_points' => $actualPoints,
                        'health_index' => $healthIndex,
                        'factor_score' => $factorScore,
                        'result_text' => $resultText,
                    ]
                );
            }
            DB::commit();
            return response()->json(['result' => true, 'data' => 'Answers saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'data' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function findTotalAdjustmentValue($templateId)
    {
        $points = HraTemplateQuestions::where('hra_template_questions.template_id', $templateId)
            ->join('hra_question', 'hra_template_questions.question_id', '=', 'hra_question.question_id')
            ->whereNotNull('hra_question.points')
            ->pluck('hra_question.points')
            ->toArray();
        if (empty($points)) {
            return 0;
        }
        $minValues = [];
        foreach ($points as $point) {
            if (is_string($point)) {
                $decoded = json_decode($point, true);
                if (is_array($decoded)) {
                    $minFromQuestion = min(array_values($decoded));
                    $minValues[] = $minFromQuestion;
                }
            } elseif (is_numeric($point)) {
                $minValues[] = $point;
            }
        }
        if (empty($minValues)) {
            return 0;
        }
        $total = array_sum($minValues);
        return abs((int) $total);
    }
    private function findMaximumValue($templateId)
    {
        $points = HraTemplateQuestions::where('hra_template_questions.template_id', $templateId)
            ->join('hra_question', 'hra_template_questions.question_id', '=', 'hra_question.question_id')
            ->whereNotNull('hra_question.points')
            ->pluck('hra_question.points')
            ->toArray();
        if (empty($points)) {
            return 0;
        }
        $minValues = [];
        foreach ($points as $point) {
            if (is_string($point)) {
                $decoded = json_decode($point, true);
                if (is_array($decoded)) {
                    $minFromQuestion = max(array_values($decoded));
                    $minValues[] = $minFromQuestion;
                }
            } elseif (is_numeric($point)) {
                $minValues[] = $point;
            }
        }
        if (empty($minValues)) {
            return 0;
        }
        $total = array_sum($minValues);
        return abs((int) $total);
    }
}

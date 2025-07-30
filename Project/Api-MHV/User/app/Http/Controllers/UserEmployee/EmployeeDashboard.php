<?php

namespace App\Http\Controllers\UserEmployee;

use App\Http\Controllers\Controller;
use App\Models\Corporate\EmployeeUserMapping;
use Illuminate\Http\Request;
use App\Models\Hra\Factors\HraFactor;
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
            $completedResults = HraOverallResult::whereIn('hra_template_id', $templateIds)->get();
            $completedMap = $completedResults->keyBy('hra_template_id');
            $attendedTemplateIds = HraInduvidualAnswer::whereIn('template_id', $templateIds)
                ->pluck('template_id')
                ->unique()
                ->toArray();
            $allFactorScores = $completedResults->pluck('factor_score')->filter()->toArray();
            $allFactorIds = [];
            foreach ($allFactorScores as $factorScoreRaw) {
                $factorScoreArray = is_string($factorScoreRaw) ? json_decode($factorScoreRaw, true) : $factorScoreRaw;
                if (is_array($factorScoreArray)) {
                    $allFactorIds = array_merge($allFactorIds, array_keys($factorScoreArray));
                }
            }
            $allFactorIds = array_unique($allFactorIds);
            $factors = HraFactor::whereIn('factor_id', $allFactorIds)->pluck('factor_name', 'factor_id')->toArray();
            $validTemplates = $assignedTemplates->filter(function ($t) use ($designation, $employeeTypeId, $departmentId) {
                return
                    $this->matches($designation, $t->designation) &&
                    $this->matches($employeeTypeId, $t->employee_type) &&
                    $this->matches($departmentId, $t->department);
            })->map(function ($t) use ($templateModels, $completedMap, $attendedTemplateIds, $factors) {
                $template = $templateModels[$t->template_id] ?? null;
                if (!$template) {
                    return null;
                }
                $status = 'Not Started';
                $score = '';
                $factorPoints = [];
                if (isset($completedMap[$t->template_id])) {
                    $status = 'Completed';
                    $score = $completedMap[$t->template_id]->health_index;
                    $factorPointsRaw = $completedMap[$t->template_id]->factor_score;
                    $factorScoreArray = is_string($factorPointsRaw)
                        ? json_decode($factorPointsRaw, true)
                        : (is_array($factorPointsRaw) ? $factorPointsRaw : []);
                    foreach ($factorScoreArray as $factorId => $value) {
                        $name = $factors[$factorId] ?? $factorId;
                        $factorPoints[$name] = $value;
                    }
                } elseif (in_array($t->template_id, $attendedTemplateIds)) {
                    $status = 'In Pocess';
                }
                return [
                    'template_id' => $template->template_id,
                    'template_name' => $template->template_name,
                    'status' => $status,
                    'score' => $score,
                    'factor_points' => $factorPoints
                ];
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
                $ids = [(int) $q->question_id];
                for ($i = 1; $i <= 8; $i++) {
                    $trigger = $q->{"trigger_$i"};
                    if ($trigger && is_array($decoded = json_decode($trigger, true))) {
                        $ids = array_merge($ids, array_map('intval', $decoded));
                    }
                }
                return $ids;
            })->unique()->values()->all();
            $submittedIds = $answers->flatMap(function ($answer) {
                $ids = [$answer['question_id']];
                if (!empty($answer['triggers'])) {
                    $ids = array_merge($ids, collect($answer['triggers'])->pluck('question_id')->all());
                }
                return $ids;
            })->unique()->values()->all();
            $invalidIds = array_diff($submittedIds, $validQuestionIds);
            if (!empty($invalidIds)) {
                return response()->json(['result' => false, 'data' => 'Invalid Request'], 422);
            }
            $hraQuestions = HraQuestions::whereIn('question_id', $submittedIds)
                ->get(['question_id', 'answer', 'points'])
                ->keyBy('question_id');
            $templateQuestionFactors = HraTemplateQuestions::where('template_id', $templateId)
                ->pluck('factor_id', 'question_id');
            DB::beginTransaction();
            $actualPoints = 0;
            if (!$validated['is_partial']) {
                $corporateId = $request->corporate_id;
                $locationId = $request->location_id;
                $designation = $request->designation;
                $hl1 = EmployeeUserMapping::where('user_id', $userId)
                    ->where('corporate_id', $corporateId)
                    ->where('location_id', $locationId)
                    ->value('hl1_id');
                $completedDate = now();
                $resultText = 'Answers saved successfully.';
            }
            foreach ($answers as $answer) {
                $motherId = $answer['question_id'];
                $mainPoints = $this->getAnswerPoints($hraQuestions[$motherId] ?? null, $answer['answer']);
                $actualPoints += $mainPoints;
                $this->saveAnswerRecord(
                    $templateId,
                    $userId,
                    $motherId,
                    null,
                    $answer['answer'],
                    $mainPoints,
                    $templateQuestionFactors[$motherId] ?? 0
                );
                if (!empty($answer['triggers'])) {
                    foreach ($answer['triggers'] as $trigger) {
                        $triggerId = $trigger['question_id'];
                        $triggerPoints = $this->getAnswerPoints($hraQuestions[$triggerId] ?? null, $trigger['answer']);
                        $actualPoints += $triggerPoints;
                        $this->saveAnswerRecord(
                            $templateId,
                            $userId,
                            $triggerId,
                            $motherId,
                            $trigger['answer'],
                            $triggerPoints,
                            $templateQuestionFactors[$triggerId] ?? 0
                        );
                    }
                }
            }
            if (!$validated['is_partial'] && $actualPoints !== 0) {
                $adjustment = $this->findAdjustmentValue($templateId);
                $maxValue = $this->findMaximumValue($templateId);
                $obtainedPoints = $actualPoints + ($adjustment ?? 0);
                $healthIndex = round(($obtainedPoints / ($maxValue + $adjustment)) * 100, 2);
                $factorScore = $this->getFactorWiseScore($templateId, $userId);
                $exists = HraOverallResult::where([
                    'user_id' => $userId,
                    'hra_template_id' => $templateId,
                ])->exists();
                if ($exists) {
                    return response()->json([
                        'result' => false,
                        'data' => 'HRA result already exists for this user and template.',
                    ], 422);
                }
                HraOverallResult::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'corporate_template_id' => 0,
                        'hra_template_id' => $templateId,
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
                        'factor_score' => json_encode($factorScore),
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
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function getAnswerPoints($question, $userAnswer)
    {
        if (!$question) {
            return 0;
        }
        $answerArr = json_decode($question->answer, true);
        $pointsArr = json_decode($question->points, true);
        if (is_array($answerArr) && is_array($pointsArr)) {
            $index = array_search($userAnswer, $answerArr, true);
            if ($index !== false && isset($pointsArr[$index])) {
                return $pointsArr[$index];
            }
        }
        return 0;
    }
    private function saveAnswerRecord($templateId, $userId, $questionId, $triggerOf, $answer, $points, $factorId = 0)
    {
        HraInduvidualAnswer::updateOrCreate(
            [
                'template_id' => $templateId,
                'user_id' => $userId,
                'question_id' => $questionId,
                'trigger_question_of' => $triggerOf,
            ],
            [
                'factor_id' => $factorId,
                'template_id' => $templateId,
                'user_id' => $userId,
                'question_id' => $questionId,
                'trigger_question_of' => $triggerOf,
                'answer' => is_array($answer) ? json_encode($answer) : $answer,
                'points' => $points,
                'test_results' => null,
                'question_status' => 1,
                'reference_question' => 0,
            ]
        );
    }
    private function findAdjustmentValue($templateId, $factorId = null)
    {
        $query = HraTemplateQuestions::where('hra_template_questions.template_id', $templateId)
            ->join('hra_question', 'hra_template_questions.question_id', '=', 'hra_question.question_id')
            ->whereNotNull('hra_question.points');
        if (!is_null($factorId)) {
            $query->whereIn('hra_template_questions.factor_id', (array) $factorId);
        }
        $points = $query->pluck('hra_question.points')->toArray();
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
    private function findMaximumValue($templateId, $factorId = null)
    {
        $query = HraTemplateQuestions::where('hra_template_questions.template_id', $templateId)
            ->join('hra_question', 'hra_template_questions.question_id', '=', 'hra_question.question_id')
            ->whereNotNull('hra_question.points');
        if (!is_null($factorId)) {
            $query->whereIn('hra_template_questions.factor_id', (array) $factorId);
        }
        $points = $query->pluck('hra_question.points')->toArray();
        if (empty($points)) {
            return 0;
        }
        $maxValues = [];
        foreach ($points as $point) {
            if (is_string($point)) {
                $decoded = json_decode($point, true);
                if (is_array($decoded)) {
                    $maxFromQuestion = max(array_values($decoded));
                    $maxValues[] = $maxFromQuestion;
                }
            } elseif (is_numeric($point)) {
                $maxValues[] = $point;
            }
        }
        if (empty($maxValues)) {
            return 0;
        }
        $total = array_sum($maxValues);
        return abs((int) $total);
    }
    private function getFactorWiseScore($templateId = null, $userId = null)
    {
        $factorData = $this->getFactorWiseTotalPoints($templateId, $userId);
        $factorPointsArray = $factorData['factorPoints'];
        $factorIds = $factorData['factorIds'];
        $scores = [];
        foreach ($factorIds as $factorId) {
            $totalPoints = $factorPointsArray[$factorId];
            $factorMax = $this->findMaximumValue($templateId, $factorId);
            $factorAdjustment = $this->findAdjustmentValue($templateId, $factorId);
            $adjustedPoints = $totalPoints + $factorAdjustment;
            $denominator = $factorMax + $factorAdjustment;
            $score = $denominator > 0 ? round(($adjustedPoints / $denominator) * 100, 2) : 0;
            $scores[$factorId] = $score;
        }
        return $scores;
    }
    private function getFactorWiseTotalPoints($templateId, $userId)
    {
        $factorWiseTotalPoints = HraInduvidualAnswer::where('template_id', $templateId)
            ->where('user_id', $userId)
            ->get()
            ->groupBy('factor_id');
        $factorPoints = [];
        $factorIds = [];
        foreach ($factorWiseTotalPoints as $factorId => $questions) {
            $sum = $questions->sum(function ($q) {
                return is_numeric($q->points) ? $q->points : 0;
            });
            $factorPoints[$factorId] = $sum;
            $factorIds[] = $factorId;
        }
        return [
            'factorPoints' => $factorPoints,
            'factorIds' => $factorIds,
        ];
    }
}

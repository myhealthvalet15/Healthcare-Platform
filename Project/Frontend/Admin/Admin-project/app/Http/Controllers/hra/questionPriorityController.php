<?php

namespace App\Http\Controllers\hra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class questionPriorityController extends Controller
{
    public function questionFactorPriority(Request $request, $template_id, $factor_id = 0)
    {
        if (!is_numeric($template_id) || !is_numeric($factor_id)) {
            abort(404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getTemplate/' . $template_id);
        if ($response->successful() && $response->status() == 200) {
            $data = $response->json()['data'];
            if (!isset($data['factors']) || !isset($data['priorities'])) {
                return response()->json(['result' => 'error', 'message' => 'Invalid data'], 404);
            }
            $template_name = $data['template_name'];
            $template_id = $data['template_id'];
            $factors_with_priorities = [];
            foreach ($data['priorities'] as $key => $priority) {
                if ($priority >= 1 && isset($data['factors'][$key])) {
                    $factors_with_priorities[$key] = [
                        'name' => $data['factors'][$key],
                        'priority' => $priority
                    ];
                }
            }
            uasort($factors_with_priorities, function ($a, $b) {
                return $a['priority'] <=> $b['priority'];
            });
            if (empty($factors_with_priorities)) {
                $factors_with_priorities = [];
            }
            if (array_key_exists($factor_id, $factors_with_priorities)) {
                return view('content.hra.question-factor-priority', [
                    'factors_with_priorities' => $factors_with_priorities,
                    'template_id' => $template_id,
                    'template_name' => $template_name
                ]);
            }
            return "Invalid Request";
        }
        return "Invalid Request";
    }

    public function viewQuestionFactorPriority(Request $request, $template_id, $factor_id = 0)
    {
        if (!is_numeric($template_id) || !is_numeric($factor_id)) {
            abort(404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getTemplate/' . $template_id);
        if ($response->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        if ($response->successful() && $response->status() == 200) {
            $data = $response->json()['data'];
            $template_name = $data['template_name'];
            $isPublished = $data['published'];
            return view('content.hra.view-question-factor-priority', [
                'template_id' => $template_id,
                'template_name' => $template_name,
                'published' => $isPublished
            ]);
        }
        abort(404);
    }

    public function setQuestionFactorPriority(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|integer',
            'factor_id' => 'required|integer',
            'priority' => 'array',
            'priority.*' => 'integer'

        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid data structure.',
                'messages' => $validator->errors()
            ], 422);
        }
        $priorityData = $request->input('priority');
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ];
        $response = Http::withHeaders($headers)->put(
            "https://api-admin.hygeiaes.com/V1/hra/templates/setQuestionFactorPriority/{$request->input('template_id')}/{$request->input('factor_id')}",
            [
                'question_id' => $priorityData
            ]
        );  
        if ($response->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        if ($response->successful()) {
            return response()->json(['result' => 'success', 'message' => $response['message']]);
        } else {
            return response()->json(['result' => 'error', 'message' => $response['message']], $response->status());
        }
    }

    public function getQuestionFactorPriority(Request $request, $template_id, $factor_id)
    {
        if (!is_numeric($factor_id) || !is_numeric($template_id) || $factor_id <= 0 || $template_id <= 0) {
            return response()->json(['result' => 'error', 'message' => 'Invalid template_id or factor_id'], 400);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getQuestionFactorPriority/' . $template_id . '/' . $factor_id);
        if ($response->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        if ($response->successful()) {
            return response()->json(['result' => 'success', 'message' => $response['data']], $response->status());
        } elseif ($response->status() == 404) {
            return response()->json(['result' => 'success', 'message' => 'No Question Priorities Found'], 200);
        } else {
            return response()->json(['result' => 'error', 'message' => $response['message']], $response->status());
        }
    }

    public function getAllQuestionFactorPriority(Request $request, $template_id)
    {
        if (!is_numeric($template_id) || $template_id <= 0) {
            return response()->json(['result' => 'error', 'message' => 'Invalid template_id or factor_id'], 400);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getTemplate/' . $template_id);
        if ($response->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        if ($response->successful()) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getAllQuestionFactorPriority/' . $template_id);
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => $response['message']], $response->status());
            } else {
                return response()->json(['result' => 'error', 'message' => $response['message']], $response->status());
            }
        } elseif ($response->status() == 404) {
            return response()->json(['result' => 'success', 'message' => 'No Question Priorities Found'], 200);
        } else {
            return response()->json(['result' => 'error', 'message' => $response['message']], $response->status());
        }
    }

    public function getExistingTriggerQuestionsView(Request $request, $templateId, $factorId, $questionId)
    {
        if (!is_numeric($templateId) || !is_numeric($factorId) || !is_numeric($questionId)) {
            abort(404);
        }
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ];
        $response = Http::withHeaders($headers)->get('https://api-admin.hygeiaes.com/V1/hra/templates/getTriggerQuestionFactorPriority/' . $templateId . '/' . $factorId . '/' . $questionId);
        $response_2 = Http::withHeaders($headers)->get('https://api-admin.hygeiaes.com/V1/hra/questions/getQuestion/' . $questionId);
        if ($response->status() === 401 || $response_2->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        if ($response->successful() && $response->status() == 200) {
            if ($response_2->successful() && $response_2->status() == 200) {
                return view('content.hra.trigger-questions', [
                    'data' => $response->json(),
                    'question_data' => $response_2->json()
                ]);
            } else {
                return "Invalid Request";
            }
        } else {
            return "Invalid Request";
        }
    }

    public function getExistingTriggerQuestions(Request $request, $templateId, $factorId, $questionId)
    {
        if (!is_numeric($templateId) || !is_numeric($factorId) || !is_numeric($questionId)) {
            abort(404);
        }
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ];
        $response = Http::withHeaders($headers)->get('https://api-admin.hygeiaes.com/V1/hra/templates/getTriggerQuestionFactorPriority/' . $templateId . '/' . $factorId . '/' . $questionId);
        if ($response->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }
        if ($response->successful() && $response->status() == 200) {
            return response()->json(['result' => 'success', 'message' => $response['message']], $response->status());
        } else {
            return response()->json(['result' => 'error', 'message' => $response['message']], $response->status());
        }
    }

    public function setTriggerQuestions(Request $request, $templateId, $factorId, $questionId)
    {
        if (!is_numeric($templateId) || !is_numeric($factorId) || !is_numeric($questionId)) {
            abort(404);
        }

        $rules = [
            'data' => 'required|array',
            'data.*.answerId' => 'required|in:key1,key2,key3,key4,key5,key6,key7,key8',
            'data.*.questionId' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $formattedData = $this->formatData($request->input('data'));

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ];

        $response = Http::withHeaders($headers)->put(
            "https://api-admin.hygeiaes.com/V1/hra/templates/setTriggerQuestionFactorPriority/" . $templateId . '/' . $factorId . '/' . $questionId,
            $formattedData
        );
        if ($response->status() === 401) {
            return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        if ($response->successful()) {
            return response()->json(['result' => 'success', 'message' => $response['message']]);
        } else {
            return response()->json(['result' => 'error', 'message' => 'Invalid request'], $response->status());
        }
    }


    private function formatData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $answerId = $item['answerId'];
            $questionId = $item['questionId'];

            if (!isset($result[$answerId])) {
                $result[$answerId] = [];
            }
            $result[$answerId][] = $questionId;
        }
        $formattedResult = [];
        foreach ($result as $answerId => $questionIds) {
            $triggerNumber = substr($answerId, 3);
            $triggerKey = "trigger_$triggerNumber";
            $formattedResult[$triggerKey] = $questionIds;
        }
        return $formattedResult;
    }
}

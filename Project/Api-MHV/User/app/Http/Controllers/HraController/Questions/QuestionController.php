<?php

namespace App\Http\Controllers\HraController\Questions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hra\Questions\HraQuestions;
use App\Models\Hra\Templates\HraTemplateQuestions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Hra\Master_Tests\MasterTest;

class QuestionController extends Controller
{
    private function formatKeyValues($values)
    {
        $formatted = [];
        foreach ($values as $index => $value) {

            $formatted['key' . ($index + 1)] = $value;
        }
        return $formatted;
    }
    private function base64UrlDecode($data)
    {
        $base64 = str_replace(['-', '_'], ['+', '/'], $data);
        $padding = strlen($base64) % 4;
        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }
        return trim(str_replace('?', '', base64_decode($base64))) . "?";
    }

    public function getQuestionb64($data)
    {
        try {
            $question = $this->base64UrlDecode($data);
            $question = HraQuestions::where('question', $question)->first();
            if (!$question) {
                return response()->json([
                    'error' => 'Question not found.',
                ], 404);
            }
            if ($question->image) {
                $question->image = basename($question->image);
            }
            return response()->json([
                'data' => $question,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    public function getQuestion($id)
    {
        try {
            $question = HraQuestions::find($id);
            if (!$question) {
                return response()->json([
                    'error' => 'Question not found.',
                ], 404);
            }
            if ($question->image) {
                $question->image = basename($question->image);
            }
            return response()->json([
                'data' => $question,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
    public function addQuestion(Request $request)
    {
        try {
            $allowedFields = [
                'question',
                'option_type',
                'answers',
                'points',
                'image',
                'input_box',
                'formula',
                'tests',
                'comments',
                'dashboard_text',
                'compare_values',
                'gender',
                'is_compare_values',
            ];
            $unexpectedFields = array_diff(array_keys($request->all()), $allowedFields);

            if (!empty($unexpectedFields)) {
                return response()->json([
                    'errors' => ['result' => 'error', 'message' => 'The request contains unexpected fields: ' . implode(', ', $unexpectedFields)]
                ], 422);
            }
            $rules = [
                'question' => 'required|string|max:256|unique:hra_question,question',
                'option_type' => 'required|in:Select Box,Input Box,Check Box,Radio Button',
                'answers' => 'required|array|min:1',
                'points' => 'array',
                'image' => 'nullable|string',
                'input_box' => 'required|boolean',
                'formula' => 'nullable|string',
                'tests' => 'required|array',
                'comments' => 'nullable|string',
                'dashboard_text' => 'nullable|string|max:150',
                'compare_values' => 'nullable|array',
                'gender' => 'required|in:male,female,third_gender',
                'is_compare_values' => 'boolean',
            ];
            if ($request->input('is_compare_values')) {
                $rules['compare_values'] = 'required|array|min:1';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()], 422);
            }

            $isCompareValues = $request->input('is_compare_values');
            $answers = $request->input('answers');
            $points = $request->input('points');
            $compareValues = $request->input('compare_values', []);
            $testIds = $request->input('tests');
            $existingQuestion = HraQuestions::where('question', $request->input('question'))->first();
            if ($existingQuestion) {
                return response()->json(['result' => 'error', 'message' => 'Question already exists.'], 422);
            }
            $gender = $request->input('gender');
            if ($gender === 'third_gender') {
                $gender = str_replace('_', ' ', $gender);
            }

            $questionData = [
                'question' => trim(str_replace('?', '', $request->input('question'))) . "?",
                'option_type' => $request->input('option_type'),
                'input_box' => $request->input('input_box'),
                'formula' => $request->input('formula'),
                'test_id' => json_encode($this->formatKeyValues($testIds)),
                'comments' => $request->input('comments'),
                'dashboard_title' => $request->input('dashboard_text'),
                'gender' => $gender,
                'answer' => json_encode($this->formatKeyValues($answers)),
                'points' => json_encode($this->formatKeyValues($points)),
                'comp_value' => $isCompareValues == 1 ? json_encode($this->formatKeyValues($compareValues)) : null,
                'image' => $request->input('image'),
            ];

            $question = HraQuestions::create($questionData);
            return response()->json(['result' => 'error', 'message' => 'Question added successfully.', 'question' => $question], 201);
        } catch (\Exception $e) {

            return response()->json(['result' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function editQuestion(Request $request, $id)
    {
        try {
            $allowedFields = [
                'question',
                'option_type',
                'answers',
                'points',
                'input_box',
                'formula',
                'tests',
                'comments',
                'dashboard_text',
                'compare_values',
                'gender',
                'is_compare_values',
                'hra_question_image'
            ];
            $unexpectedFields = array_diff(array_keys($request->all()), $allowedFields);
            if (!empty($unexpectedFields)) {
                return response()->json([
                    'errors' => ['unexpected_fields' => 'The request contains unexpected fields: ' . implode(', ', $unexpectedFields)],
                ], 422);
            }
            $validator = Validator::make($request->all(), [
                'question' => 'sometimes|string|max:256|unique:hra_question,question,' . $id . ',question_id',
                'option_type' => 'sometimes|in:Select Box,Input Box,Check Box,Radio Button',
                'answers' => 'sometimes|array',
                'points' => 'sometimes|array',
                'input_box' => 'in:true,false',
                'formula' => 'nullable|string',
                'tests' => 'sometimes|array',
                'comments' => 'nullable|string',
                'dashboard_text' => 'nullable|string|max:150',
                'compare_values' => 'nullable|array',
                'gender' => 'sometimes|in:male,female,third_gender',
                'is_compare_values' => 'in:true,false',
                'hra_question_image' => 'string|nullable'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $question = HraQuestions::where('question_id', $id)->first();
            if (!$question) {
                return response()->json(['error' => 'Question not found.'], 404);
            }
            if ($request->input('is_compare_values')) {
                $rules['compare_values'] = 'required|array|min:1';
            }
            $validator = Validator::make($request->all(), $rules);
            $isCompareValues = $request->input('is_compare_values');
            $answers = $request->input('answers', []);
            $points = $request->input('points', []);
            $compareValues = $request->input('compare_values', []);
            $testIds = $request->input('tests', []);
            $formattedAnswers = $this->formatKeyValues($answers);
            $formattedPoints = $this->formatKeyValues($points);
            $formattedCompareValues = ($isCompareValues === "true") ? $this->formatKeyValues($compareValues) : null;
            $formattedTests = $this->formatKeyValues($testIds);
            $imagePath = $request->input('hra_question_image');
            $questionData = [
                'question' => trim(str_replace('?', '', $request->input('question'))) . "?",
                'types' => $request->input('option_type', $request->input('types')),
                'input_box' => $request->input('input_box', $request->input('input_box')) === 'true' ? 1 : 0,
                'formula' => $request->input('formula', $request->input('formula')),
                'test_id' => json_encode($formattedTests),
                'comments' => $request->input('comments', $request->input('comments')),
                'dashboard_title' => $request->input('dashboard_text', $request->input('dashboard_text')),
                'gender' => $request->input('gender', $request->input('gender')),
                'answer' => json_encode($formattedAnswers),
                'points' => json_encode($formattedPoints),
                'comp_value' => $formattedCompareValues ? json_encode($formattedCompareValues) : null,
            ];
            if ($imagePath != "No Path") {
                $questionData['image'] = $imagePath;
            }
            $question->update($questionData);
            return response()->json(['success' => 'Question updated successfully.', 'question' => $question], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getAllQuestions()
    {
        try {
            $questions = HraQuestions::all();

            foreach ($questions as $question) {
                $testIds = json_decode($question->test_id, true);
                $testNames = [];
                foreach ($testIds as $testId => $value) {
                    $test = MasterTest::where('master_test_id', $value)->first();
                    if ($test) {
                        $testNames[$testId] = $test->test_name;
                    }
                }
                $question->test_names = $testNames;
                if ($question->image) {
                    $question->image = basename($question->image);
                }
            }
            return response()->json([
                'data' => $questions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    public function deleteQuestion($id)
    {
        try {
            $question = HraQuestions::find($id);
            if (!$question) {
                return response()->json([
                    'error' => 'Question not found.',
                ], 404);
            }
            $isAssignedToTemplate = HraTemplateQuestions::where('question_id', $id)->exists();
            if ($isAssignedToTemplate) {
                return response()->json([
                    'result' => false,
                    'message' => 'This question is already assigned to a template. Remove the question from the template before deleting it.',
                ], 422);
            }
            $question->delete();
            return response()->json([
                'message' => 'Question deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again later. ' . $e->getMessage(),
            ], 500);
        }
    }
}

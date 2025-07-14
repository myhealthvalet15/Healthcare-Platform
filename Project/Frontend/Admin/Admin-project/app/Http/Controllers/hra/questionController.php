<?php

namespace App\Http\Controllers\hra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class questionController extends Controller
{
    public function index()
    {
        return view('content.hra.add-questions');
    }

    public function question()
    {
        return view('content.hra.questions');
    }

    public function getAllMasterTest(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/master-tests/getAllTestNames');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful() and isset($response['data'])) {
                return $response['data'];
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAllMasterTests(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/master-tests/getAllTests');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful() and isset($response['data'])) {
                return $response['data'];
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAllQuestion(Request $request)
    {
        try { 
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/questions/getAllQuestions');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful() && isset($response['data'])) {
                return $response->json();
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function addQuestion(Request $request)
    {
        try {
            // return $request->all();
            if ($request->has('tests')) {
                $request->merge([
                    'tests' => array_filter(array_map('intval', json_decode($request->input('tests'), true)), fn($value) => is_int($value) && $value > 0)
                ]);
            }
            $rules = [
                'question' => 'required|string',
                'formula' => 'nullable|string',
                'dashboard_text' => 'nullable|string',
                'answers' => 'nullable|array|min:1',
                'answers.*' => 'nullable|string',
                'points' => 'array',
                'points.*' => 'nullable|string',
                'is_compare_values' => 'boolean',
                'compare_values' => 'nullable|array',
                'tests' => 'nullable|array',
                'tests.*' => 'nullable|integer',
                'gender' => 'required|array',
                'gender.*' => 'in:male,female,third_gender',
                'comments' => 'nullable|string',
                'option_type' => 'required|in:Select Box,Input Box,Check Box,Radio Button',
                // 'input_box' => 'required|boolean',
                'image' => 'nullable|file|image|max:2048',
            ];
            $validatedData = $request->validate($rules);
            if ($request->input('is_compare_values')) {
                $rules['compare_values'] = 'required|array|min:1';
            }
            if ($request->input('option_type') !== 'Input Box' &&  array_filter($request->input('answers'), fn($value) => !is_null($value)) === []) {
                return response()->json(['result' => 'error', 'message' => 'Answers are required for the selected option type.'], 422);
            }
            $question_text = $this->base64UrlEncode($validatedData['question']);
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/questions/getQuestionb64/' . $question_text);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful() and $response->status() == 200) {
                return response()->json(['result' => 'error', 'message' => 'Question already exists, you can\'t proceed with this'], 422);
            } else {
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    if (!$image->isValid() || !$image->isFile()) {
                        return response()->json(['result' => false, 'message' => 'Invalid image file provided.'], 422);
                    }
                    $randomFileName = uniqid('image_', true) . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('/hra/question/', $randomFileName, 'public');
                    $validatedData['image'] = $imagePath;
                } else {
                    $validatedData['image'] = null;
                }
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ])->post('https://api-admin.hygeiaes.com/V1/hra/questions/addQuestion', $validatedData);
                if ($response->status() === 401) {
                    return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
                }
                if ($response->successful()) {
                    return response()->json([
                        'result' => true,
                        'message' => 'Question Added Successfully'
                    ], 201);
                } else {
                    if ($response->status() == 422) {
                        return response()->json(['result' => 'error', 'message' => $response['message']], 422);
                    }
                    return response()->json(['result' => 'error', 'message' => 'Internal Server Error'], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }
    public function deleteQuestion(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                abort(404);
            }
            $question_text = $this->base64UrlEncode($request->input('question'));
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/questions/getQuestion/' . $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful() and $response->status() == 200) {
                $existingImagePath = pathinfo($response['data']['image'])['basename'];
            } else {
                return response()->json(['result' => 'error', 'message' => 'Invalid Request'], 422);
            }
            if ($existingImagePath) {
                Storage::disk('public')->delete('/hra/question/' . $existingImagePath);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->delete('https://api-admin.hygeiaes.com/V1/hra/questions/deleteQuestion/' . $id);
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->status() === 422) {
                return response()->json([
                    'result' => false,
                    'message' => $response['message'],
                ], $response->status());
            }
            if ($response->successful()) {
                return response()->json(['result' => "success", 'message' => 'Question deleted successfully']);
            }
            return response()->json(['result' => 'error', 'message' => 'error in delete factor'], 500);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => 'error: ' . $e->getMessage()], 500);
        }
    }
    private function base64UrlEncode($data)
    {
        $base64 = base64_encode($data);
        $urlSafe = str_replace(['+', '/', '='], ['-', '_', ''], $base64);
        return $urlSafe;
    }
    public function editQuestion(Request $request, $id)
    {
        try {
            if (!$request->hasFile('hra_question_image')) {
                $request->merge(['hra_question_image' => null]);
            }
            $allowedFields = [
                'question',
                'question_old',
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
                'hra_question_image',
            ];

            $unexpectedFields = array_diff(array_keys($request->all()), $allowedFields);
            if (!empty($unexpectedFields)) {
                return response()->json([
                    'result' => 'error',
                    'errors' => ['unexpected_fields___' => 'The request contains unexpected fields: ' . implode(', ', $unexpectedFields)],
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'question' => 'required|string|max:256',
                'question_old' => 'required|string|max:256',
                'option_type' => 'in:Select Box,Input Box,Check Box,Radio Button',
                'answers' => 'required|array|min:1',
                'points' => 'array',
                'image' => 'nullable|string',
                'input_box' => 'string',
                'formula' => 'nullable|string',
                'tests' => 'required|array',
                'comments' => 'nullable|string',
                'dashboard_text' => 'nullable|string|max:150',
                'compare_values' => 'nullable|array',
                'gender' => 'required|array',
                'gender.*' => 'in:male,female,third_gender',
                'is_compare_values' => 'in:true,false',
                'hra_question_image' => 'nullable|image',
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => 'error', 'errors' => $validator->errors()], 422);
            }
            if ($request->input('is_compare_values')) {
                $rules['compare_values'] = 'required|array|min:1';
            }
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()], 422);
            }
            $question_text = $this->base64UrlEncode($request->input('question_old'));
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/hra/questions/getQuestionb64/' . $question_text);

            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful() and $response->status() == 200) {
                $existingImagePath = pathinfo($response['data']['image'])['basename'];
            } else {
                return response()->json(['result' => 'error', 'message' => 'Invalid Request'], 422);
            }
            $imagePath = null;
            if ($request->hasFile('hra_question_image')) {
                $image = $request->file('hra_question_image');
                if (!$image->isValid() || !$image->isFile()) {
                    return response()->json(['result' => 'error', 'message' => 'Invalid image file provided.'], 422);
                }
                if ($existingImagePath != null && !empty($existingImagePath)) {
                    Storage::disk('public')->delete('/hra/question/' . $existingImagePath);
                }
                $randomFileName = uniqid('image_', true) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('/hra/question/', $randomFileName, 'public');
            }
            $payload = $request->only($allowedFields);
            unset($payload['question_old']);
            if ($imagePath) {
                $payload['hra_question_image'] = $imagePath;
            } else {
                $payload['hra_question_image'] = "No Path";
            }
            $headers = [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->put("https://api-admin.hygeiaes.com/V1/hra/questions/editQuestion/$id", $payload);
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'message' => 'Question updated successfully!', 'data' => $response->json()], 200);
            }
            return response()->json(['result' => 'error', 'message' => 'Internal Server Error'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}

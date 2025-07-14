<?php

namespace App\Http\Controllers\UserEmployee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeDashboard extends Controller
{
    public function getAllAssignedTemplates(Request $request)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/master-user/masteruser/getAllAssignedTemplates');
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json(['result' => false, 'data' => $response['data']]);
    }
    private function checkTemplateAccess(Request $request, $templateId)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/V1/master-user/masteruser/check-template-access/{$templateId}");
        return $response;
    }
    public function displayHraQuestionaireTemplate(Request $request, $templateId)
    {
        $headerData = 'HRA Questionnaire Template';
        $response = $this->checkTemplateAccess($request, $templateId); 
        if ($response['result']) {
            return view('content.UserEmployee.HRA.hra_questionaire_template', [
                'templateDetails' => $response['data'],
                'HeaderData' => $headerData,
                'isQuestionsAvailableForThisTemplate' => $response['data']['is_questions_available'] ? 1 : 0,
            ]);
        }
        return "Invalid Template";
    }
    public function getTemplateQuestions(Request $request, $templateId)
    {
        if (!is_numeric($templateId)) {
            return response()->json(['result' => false, 'data' => 'Invalid Template'], 403);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-user.hygeiaes.com/V1/master-user/masteruser/get-template-questions/{$templateId}");
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json(['result' => false, 'data' => $response['data']], 403);
    }
    public function saveHraTemplateQuestionnaireAnswers(Request $request, $templateId)
    {
        if (!is_numeric($templateId)) {
            return response()->json(['result' => false, 'data' => 'Invalid Request'], 403);
        }
        $validated = $request->validate([
            'answers' => 'required|array|min:1|max:500',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer' => 'required',
            'answers.*.triggers' => 'sometimes|array',
            'answers.*.triggers.*.question_id' => 'required_with:answers.*.triggers|integer',
            'answers.*.triggers.*.answer' => 'required_with:answers.*.triggers',
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/master-user/masteruser/save-hra-template-questionnaire-answers/' . $templateId, [
            'template_id' => $templateId,
            'answers' => $validated['answers'],
        ]);
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json([
            'result' => false,
            'data' => 'Failed to save answers',
        ], $response->status());
    }
}

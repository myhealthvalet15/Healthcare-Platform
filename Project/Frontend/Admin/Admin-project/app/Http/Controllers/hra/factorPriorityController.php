<?php

namespace App\Http\Controllers\hra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class factorPriorityController extends Controller
{
    public function index(Request $request, $template_id)
    {
        if (!is_numeric($template_id)) {
            return view('content.NotFound.factorPriorityNotFound');
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/hra/templates/getTemplate/' . $template_id);
        if ($response->successful() && $response->status() == 200) {
            return view('content.hra.factor-priority');
        }
        return view('content.not-found.invalid-data');
    }
    public function redirectToTemplatePage()
    {
        $template_id = 404;
        return redirect()->route('hra-templates', ['template_id' => $template_id]);
    }
    public function setFactorPriority(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'templateId' => 'required|integer',
            'priorities' => 'required|array',
            'priorities.*.factorId' => 'required|integer',
            'priorities.*.priority' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 400);
        }
        $priorities = $request->input('priorities');
        usort($priorities, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        $sortedFactorIds = array_map(function ($priority) {
            return $priority['factorId'];
        }, $priorities);
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ];
        $response = Http::withHeaders($headers)->put(
            "https://api-admin.hygeiaes.com/V1/hra/templates/setFactorPriority/" . $request->input('templateId'),
            ['factor_id' => $sortedFactorIds]
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
}

<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class link2hra extends Controller
{
    public function link2hraIndexPage()
    {
        return view('content.corporate.link-to-hra');
    }

    public function linkCorporate2Hra(Request $request)
    {
        try {
            $request->validate([
                'corporate_id' => 'required|string|max:255',
                'template_ids' => 'required|array',
                'template_ids.*' => 'required|integer',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/link2hra', [
                'corporate_id' => $request->input('corporate_id'),
                'template_ids' => $request->input('template_ids'),
            ]);
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCorporateHraLink(Request $request)
    {
        try {
            $request->validate([
                'corporate_id' => 'required|string|max:255',
                'template_ids' => 'required|array',
                'template_ids.*' => 'required|integer',
            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/updateCorporateHraLink', [
                'corporate_id' => $request->input('corporate_id'),
                'template_ids' => $request->input('template_ids'),
            ]);
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCorporateOfHraTemplate(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate-stubs/stubs/getCorporateOfHraTemplate');
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

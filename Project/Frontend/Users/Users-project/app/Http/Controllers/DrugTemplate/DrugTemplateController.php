<?php

namespace App\Http\Controllers\DrugTemplate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DrugTemplateController extends Controller
{
    public function drugTemplateList(Request $request)
    {
        $corporate_id = session('corporate_id');
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        try {
            $responseTypesAndIngredients = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getDrugTypesAndIngredients/');
            $drugTypes = [];
            $drugIngredients = [];
            if ($responseTypesAndIngredients->successful()) {
                $data = $responseTypesAndIngredients->json();
                $drugTypes = collect($data['drugTypes'] ?? [])
                    ->pluck('drug_type_name', 'id')
                    ->toArray();
                $drugIngredients = collect($data['drugIngredients'] ?? [])
                    ->pluck('drug_ingredients', 'id')
                    ->toArray();
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllDrugTemplates/' . $locationId);
            if ($response->successful()) {
                $drugtemplates = $response['data'];
                $headerData = 'Drug Template Details';
                return view('content.drug-template.drug-template-list', compact('drugtemplates', 'drugTypes', 'drugIngredients'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: Unable to fetch drug templates.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function drugTemplateAdd(Request $request)
    {
        $drugtemplates = 'Bhava';
        $headerData = 'Add New Drug Template';
        return view('content.drug-template.drug-template-add', compact('drugtemplates'), ['HeaderData' => $headerData]);
    }
    public function drugTemplateEdit(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getDrugTemplatesById/' . $id);
            $headerData = 'Bhava';
            if ($response->successful()) {
                $drugtemplates = $response['data'];
                $headerData = 'Drug Template Details';
                return view('content.drug-template.drug-template-edit', compact('drugtemplates'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function getDrugTypesAndIngredients(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getDrugTypesAndIngredients/');
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'drugTypes' => $data['drugTypes'] ?? [],
                    'drugIngredients' => $data['drugIngredients'] ?? [],
                ]);
            } else {
                return redirect()->back()->with('error', 'An error occurred while fetching drug data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function store(Request $request)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        //return $corporateId;
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        //return $requestData;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addDrugTemplate', $requestData);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function update(Request $request, $id)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        //return $corporateId;
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        // Log::info('Bhavas Request Data:', $request->all());
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/updateDrugTemplate/' . $id, $requestData);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function newStyleList()
    {
        $headerData = 'New Style';
        return view('content.drug-template.new-style', ['HeaderData' => $headerData]);
    }
}

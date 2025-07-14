<?php

namespace App\Http\Controllers\V1Controllers\CorporateController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterCorporate;
use App\Models\V1Models\Hra\Templates\HraTemplate;
use App\Models\V1Models\Corporate\CorporateComponents\CorporateComponents;
use Illuminate\Support\Facades\Validator;

class linkCorporateToHra extends Controller
{
    public function getCorporateOfHraTemplate()
    {
        $corporates = CorporateComponents::where('module_id', 1)->get();
        $filtered = $corporates->filter(function ($corporate) {
            $subModuleIds = explode(',', str_replace(['{', '}'], '', $corporate->sub_module_id));
            return in_array(2, $subModuleIds);
        });
        $corporateIds = $filtered->pluck('corporate_id')->unique();
        $masterCorporates = MasterCorporate::whereIn('corporate_id', $corporateIds)
            ->whereColumn('corporate_id', '=', 'location_id')
            ->select('id', 'corporate_id', 'corporate_name')
            ->get()
            ->map(function ($corporate) {
                $hraTemplateIds = CorporateComponents::where('corporate_id', $corporate->corporate_id)
                    ->where('module_id', 1)
                    ->pluck('hra_templates')
                    ->map(function ($item) {
                        return json_decode($item, true);
                    })
                    ->flatten()
                    ->unique();
                $hraTemplates = $hraTemplateIds->mapWithKeys(function ($templateId) {
                    $templateName = HraTemplate::where('template_id', $templateId)->value('template_name');
                    return [$templateId => $templateName];
                })->filter();
                $corporate->hra_template_ids = $hraTemplates->keys()->all();
                $corporate->hra_templates = $hraTemplates->values()->all();
                return $corporate;
            });
        return response()->json([
            'result' => true,
            'data' => $masterCorporates
        ]);
    }
    public function linkCorporate2Hra(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'corporate_id'  => 'required|string|max:255|exists:master_corporate,corporate_id',
            'template_ids'  => 'required|array|min:1',
            'template_ids.*' => 'required|integer|exists:hra_templates,template_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => $validator->errors()], 422);
        }
        $corporateId = $request->corporate_id;
        $templateIds = $request->template_ids;
        try {
            $corporate = MasterCorporate::where('corporate_id', $corporateId)->first();
            if (!$corporate) {
                return response()->json(['result' => false, 'message' => 'Corporate not found'], 404);
            }
            $existingTemplates = HraTemplate::whereIn('template_id', $templateIds)->pluck('template_id')->toArray();
            if (count(array_unique($existingTemplates)) !== count($templateIds)) {
                return response()->json(['result' => false, 'message' => 'One or more templates not found'], 404);
            }
            $corporateComponent = CorporateComponents::where([
                'corporate_id' => $corporateId,
                'module_id'    => 1,
            ])->first();
            if (!$corporateComponent) {
                return response()->json(['result' => false, 'message' => 'Corporate component not found'], 404);
            }
            $corporateComponent->hra_templates = json_encode($templateIds);
            $corporateComponent->save();
            return response()->json([
                'result'  => true,
                'message' => 'Corporate successfully linked to templates',
                'data'    => $corporateComponent
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCorporateHraLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'corporate_id'  => 'required|string|max:255|exists:master_corporate,corporate_id',
            'template_ids'  => 'required|array|min:1',
            'template_ids.*' => 'required|integer|exists:hra_templates,template_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => $validator->errors()], 422);
        }
        $corporateId = $request->corporate_id;
        $templateIds = $request->template_ids;
        try {
            $corporate = MasterCorporate::where('corporate_id', $corporateId)->first();
            if (!$corporate) {
                return response()->json(['result' => false, 'message' => 'Corporate not found'], 404);
            }
            $existingTemplates = HraTemplate::whereIn('template_id', $templateIds)->pluck('template_id')->toArray();
            if (count(array_unique($existingTemplates)) !== count($templateIds)) {
                return response()->json(['result' => false, 'message' => 'One or more templates not found'], 404);
            }
            $corporateComponent = CorporateComponents::where([
                'corporate_id' => $corporateId,
                'module_id'    => 1,
            ])->first();
            if (!$corporateComponent) {
                return response()->json(['result' => false, 'message' => 'Corporate component not found'], 404);
            }
            $corporateComponent->hra_templates = json_encode($templateIds);
            $corporateComponent->save();
            return response()->json([
                'result'  => true,
                'message' => 'Corporate successfully linked to templates',
                'data'    => $corporateComponent
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

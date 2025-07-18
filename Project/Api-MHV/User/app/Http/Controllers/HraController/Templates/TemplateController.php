<?php

namespace App\Http\Controllers\HraController\Templates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Hra\Templates\HraTemplate;
use App\Models\Corporate\CorporateComponents\CorporateComponents;
use App\Models\Corporate\HraAssignedTemplate;
use App\Models\EmployeeType;
use App\Models\Department\CorporateHl1;
use App\Models\Corporate\EmployeeUserMapping;
use Carbon\Carbon;

class TemplateController extends Controller
{
    private function getAllHRATemplates($corporateId)
    {
        try {
            $hraTemplateIds = CorporateComponents::where('module_id', 1)
                ->where('corporate_id', $corporateId)
                ->get()
                ->filter(function ($component) {
                    $subModuleIds = explode(',', str_replace(['{', '}'], '', $component->sub_module_id));
                    return in_array(2, $subModuleIds);
                })
                ->pluck('hra_templates')
                ->map(fn($item) => json_decode($item, true))
                ->flatten()
                ->unique();
            return $hraTemplateIds->mapWithKeys(function ($templateId) {
                $name = HraTemplate::where('template_id', $templateId)->value('template_name');
                return $name ? [$templateId => $name] : [];
            })->map(fn($name, $id) => [
                'template_id' => $id,
                'template_name' => $name
            ])->values()->all();
        } catch (\Exception $e) {
            return false;
        }
    }
    public function getAllHRATemplateDatas(Request $request)
    {
        $corporateId = $request->corporate_id;
        $hraTemplates = $this->getAllHRATemplates($corporateId);
        if ($hraTemplates === false) {
            return response()->json(['result' => false, 'data' => 'Something went wrong'], 500);
        }
        return response()->json(['result' => true, 'data' => $hraTemplates]);
    }
    public function assignHRATemplate(Request $request)
    { 
        $corporateId = $request->corporate_id;
        $locationId = $request->location_id;
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|integer',
            'location_id' => 'required',
            'employee_type_id' => 'required|array',
            'employee_type_id.*' => 'required',
            'department_id' => 'required|array',
            'department_id.*' => 'required',
            'designation' => 'nullable|array',
            'designation.*' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $hraAssignment = HraAssignedTemplate::create([
                'template_id' => $request->template_id,
                'corporate_id' => $corporateId,
                'location_id' => $locationId,
                'location' => $request->location_id,
                'employee_type' => $request->employee_type_id,
                'department' => $request->department_id,
                'designation' => $request->designation ?? [],
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'next_assessment_date' => Carbon::parse($request->from_date)->addYear(),
            ]);
            return response()->json([
                'result' => true,
                'message' => 'HRA Template assigned successfully',
                'data' => $hraAssignment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while assigning the template',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateAssignedHraTemplate(Request $request)
    {
        $corporateId = $request->corporate_id;
        $locationId = $request->location_id;
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|integer',
            'location_id' => 'required',
            'employee_type_id' => 'required|array',
            'employee_type_id.*' => 'required',
            'department_id' => 'required|array',
            'department_id.*' => 'required',
            'designation' => 'nullable|array',
            'designation.*' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $hraAssignment = HraAssignedTemplate::where('template_id', $request->template_id)
                ->where('corporate_id', $corporateId)
                ->first();
            if (! $hraAssignment) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
            }
            $hraAssignment->update([
                'location_id' => $locationId,
                'location' => $request->location_id,
                'employee_type' => $request->employee_type_id,
                'department' => $request->department_id,
                'designation' => $request->designation ?? [],
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
            ]);
            return response()->json([
                'result' => true,
                'message' => 'HRA Template assignment updated successfully',
                'data' => $hraAssignment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while updating the template assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllAssignedHraTemplates(Request $request)
    {
        $corporateId = $request->corporate_id;
        $locationId = $request->location_id;
        $assignedTemplates = HraAssignedTemplate::where('corporate_id', $corporateId)->get();
        $transformed = $assignedTemplates->map(function ($template) use ($corporateId, $locationId) {
            $employeeTypes = $template->employee_type;
            $departments = $template->department;
            $designation = $template->designation;
            $employeeTypeNames = (is_array($employeeTypes) && in_array('all', $employeeTypes))
                ? EmployeeType::where('corporate_id', $corporateId)->pluck('employee_type_name')->toArray()
                : EmployeeType::where('corporate_id', $corporateId)
                ->whereIn('employee_type_id', $employeeTypes)->pluck('employee_type_name')->toArray();
            $departmentNames = (is_array($departments) && in_array('all', $departments))
                ? CorporateHl1::where('corporate_id', $corporateId)->pluck('hl1_name')->toArray()
                : CorporateHl1::where('corporate_id', $corporateId)
                ->whereIn('hl1_id', $departments)->pluck('hl1_name')->toArray();
            $designationNames = (is_array($designation) && in_array('all', $designation))
                ? EmployeeUserMapping::where("corporate_id", $corporateId)
                ->where("location_id", $locationId)->pluck('designation')->unique()->values()
                : $designation;
            return [
                'template_id' => $template->template_id,
                'location_id' => $template->location_id,
                'corporate_id' => $template->corporate_id,
                'location' => $template->location,
                'employee_type' => $employeeTypeNames,
                'department' => $departmentNames,
                'designation' => $designationNames,
                'from_date' => $template->from_date,
                'to_date' => $template->to_date,
            ];
        });
        return response()->json(['result' => true, 'data' => $transformed]);
    }
}

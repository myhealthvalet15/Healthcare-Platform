<?php

namespace App\Http\Controllers\Prescription;

use App\Models\Prescription\Prescription;
use App\Models\Prescription\PrescriptionTemplate;
use App\Models\Prescription\PrescriptionDetails;
use App\Models\Prescription\PrescriptionTemplateDrug;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Department\CorporateHl1;
use App\Models\MasterCorporate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PrescriptionController extends Controller
{
    public function getAllPrescriptionTemplate($location_id)
    {
        try {
            $prescriptionTemplates = DB::table('prescription_template')
                ->join('prescription_template_drugs', 'prescription_template.prescription_template_id', '=', 'prescription_template_drugs.prescription_template_id')
                ->join('drug_template', 'prescription_template_drugs.drug_template_id', '=', 'drug_template.drug_template_id')
                ->where('prescription_template.location_id', $location_id)
                ->select(
                    'prescription_template.*',
                    'prescription_template_drugs.*',
                    'drug_template.drug_name',
                    'drug_template.drug_strength',
                    'drug_template.drug_type'
                )
                ->get();
            if ($prescriptionTemplates->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the given location.'
                ], 404);
            }
            return response()->json([
                'result' => true,
                'data' => $prescriptionTemplates
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Prescription Templates.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function addPrescriptionTemplate(Request $request)
    {
        try {
            $prescriptionTemplateData = [
                'template_name' => $request->template_name,
                'location_id' => $request->location_id,
                'ohc_id' => $request->ohc_id,
                'pharmacy_id' => null,
                'created_by' => auth('api')->user()->id,
            ];
            $prescriptionTemplate = PrescriptionTemplate::create($prescriptionTemplateData);
            $prescriptionTemplateId = $prescriptionTemplate->prescription_template_id;
            if (isset($request->prescriptions) && is_array($request->prescriptions)) {
                foreach ($request->prescriptions as $prescription) {
                    $prescriptionTemplateDrugsData = [
                        'prescription_template_id' => $prescriptionTemplateId,
                        'drug_template_id' => $prescription['drugname'],
                        'intake_days' => $prescription['duration'],
                        'morning' => $prescription['morning'],
                        'afternoon' => $prescription['afternoon'],
                        'evening' => $prescription['evening'],
                        'night' => $prescription['night'],
                        'intake_condition' => $prescription['afbf'],
                        'remarks' => $prescription['remarks'],
                    ];
                    PrescriptionTemplateDrug::create($prescriptionTemplateDrugsData);
                }
            }
            return response()->json(['result' => true, 'message' => 'Prescription added successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'An error occurred while saving the prescription.'], 500);
        }
    }
    public function getPrescriptionTemplateById($prescription_template_id)
    {
        try {
            $prescriptionTemplate = DB::table('prescription_template')
                ->join('prescription_template_drugs', 'prescription_template.prescription_template_id', '=', 'prescription_template_drugs.prescription_template_id')
                ->where('prescription_template.prescription_template_id', '=', $prescription_template_id)
                ->select(
                    'prescription_template.*',
                    'prescription_template_drugs.*'
                )
                ->get();
            if ($prescriptionTemplate->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No prescription template found for the given ID.'
                ], 404);
            }
            return response()->json([
                'result' => true,
                'data' => $prescriptionTemplate
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Prescription Template.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updatePrescriptionTemplate(Request $request, $prescription_template_id)
    {
        try {
            $prescriptionTemplate = PrescriptionTemplate::find($prescription_template_id);
            if (!$prescriptionTemplate) {
                return response()->json(['result' => false, 'message' => 'Prescription template not found.'], 404);
            }
            $prescriptionTemplate->location_id = $request->location_id;
            $prescriptionTemplate->ohc_id = $request->ohc_id;
            $prescriptionTemplate->pharmacy_id = null;
            $prescriptionTemplate->save();
            if (isset($request->prescription_data) && is_array($request->prescription_data)) {
                foreach ($request->prescription_data as $prescription) {
                    $prescriptionTemplateDrug = PrescriptionTemplateDrug::where('prescription_template_id', $prescription_template_id)
                        ->where('drug_template_id', $prescription['drugname'])
                        ->first();
                    if ($prescriptionTemplateDrug) {
                        $prescriptionTemplateDrug->intake_days = $prescription['duration'];
                        $prescriptionTemplateDrug->morning = $prescription['morning'];
                        $prescriptionTemplateDrug->afternoon = $prescription['afternoon'];
                        $prescriptionTemplateDrug->evening = $prescription['evening'];
                        $prescriptionTemplateDrug->night = $prescription['night'];
                        $prescriptionTemplateDrug->intake_condition = $prescription['drugintakecondition'];
                        $prescriptionTemplateDrug->remarks = $prescription['remarks'];
                        $prescriptionTemplateDrug->save();
                    } else {
                        Log::warning('Prescription drug not found for template', ['prescription_template_id' => $prescription_template_id, 'drug_template_id' => $prescription['drugname']]);
                    }
                }
            }
            return response()->json(['result' => true, 'message' => 'Prescription template updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'An error occurred while updating the prescription template.'], 500);
        }
    }
    public function getOnlyPrescriptionTemplate($location_id)
    {
        try {
            $prescriptionTemplates = DB::table('prescription_template')
                ->join('prescription_template_drugs', 'prescription_template.prescription_template_id', '=', 'prescription_template_drugs.prescription_template_id')
                ->where('prescription_template.location_id', $location_id)
                ->select(
                    'prescription_template.*',
                    'prescription_template_drugs.*',
                )
                ->get();
            if ($prescriptionTemplates->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the given location.'
                ], 404);
            }
            return response()->json([
                'result' => true,
                'data' => $prescriptionTemplates
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Prescription Templates.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function addPrescription(Request $request)
    {
        if (auth('api')->user() && auth('api')->user()->getTable() == 'corporate_admin_user') {
            return $this->addPrescriptionFromCorporate($request);
        } elseif (auth('employee_api')->user() && auth('employee_api')->user()->getTable() == 'master_user') {
            return $this->addPrescriptionFromEmployee($request);
        } else {
            return response()->json(['result' => false, 'message' => 'Unauthorized user type'], 403);
        }
    }
    private function addPrescriptionFromCorporate(Request $request)
    {
        //return $request;
      //  return $request->prescription_date;
        try {
            $date = now()->format('dmY');
            $lastPrescription = Prescription::where('prescription_id', 'like', $date . '%')
                ->orderBy('prescription_id', 'desc')
                ->first();
            if ($lastPrescription) {
                $lastIdSuffix = (int)substr($lastPrescription->prescription_id, 8);
                $nextId = $lastIdSuffix + 1;
            } else {
                $nextId = 1;
            }
            
            $prescriptionId = $date . str_pad($nextId, 5, '0', STR_PAD_LEFT);
   $prescriptionDate = null;


    $cleanDate = str_replace(['-', '/'], '-', $request->prescription_date);
    $prescriptionDate = \Carbon\Carbon::createFromFormat('d-m-Y', $cleanDate)->format('Y-m-d H:i:s');
   

         $prescriptionTemplateData = [
                'user_id' => $request->user_id,
                'prescription_id' => $prescriptionId,
                'role_id' => 4,
                'master_doctor_id' => $request->corporate_user_id,
                'op_registry_id' => $request->op_registry_id ?? 0,
                'corporate_ohc_id' => 0,
                'template_id' => $request->prescriptionTemplate,
                'master_hcsp_user_id' => null,
                'attachment_id' => null,
                'is_conformance' => null,
                'doctor_notes' => $request->doctorNotes,
                'user_notes' => $request->patientNotes,
                'share_with_patient' => $request->shareWithPatient,
                'case_id' => $request->case_id,
                'draft_save' => 'no',
                'fav_pharmacy' => $request->pharmacy ?? 0,
                'fav_lab' => $request->fav_lab ?? 0,
                'prescription_date' => $prescriptionDate,
                'order_status' => 0,
                'created_by' =>  auth('api')->user()->id,
                'created_role' => 4,
                'corporate_location_id' => $request->corporate_id,
                'ohc' => $request->ohc ?? 0,
                'alternate_drug' => 0,
                'active_status' => 1,
            ];
         
           
            $prescriptionTemplate = Prescription::create($prescriptionTemplateData);
            logger('Data being saved: ', $prescriptionTemplateData);
            $prescriptionRowId = $prescriptionTemplate->prescription_id;
            if (isset($request->drugs) && is_array($request->drugs)) {
                foreach ($request->drugs as $drug) {
                    $morning = $drug['morning'] ?? 0;
                    $afternoon = $drug['afternoon'] ?? 0;
                    $evening = $drug['evening'] ?? 0;
                    $night = $drug['night'] ?? 0;
                    $prescribedDays = $drug['duration'] ?? 0;
                    $totalDosesPerDay = $morning + $afternoon + $evening + $night;
                    $toIssue = $prescribedDays * $totalDosesPerDay;
                    $remainingMedicine = $prescribedDays * $totalDosesPerDay;
                    $prescriptionDetailsData = [
                        'prescription_row_id' => $prescriptionRowId,
                        'drug_name' => !empty($drug['drugName']) ? $drug['drugName'] : null,
                        'drug_template_id' => !empty($drug['drugTemplateId']) ? $drug['drugTemplateId'] : 0,
                        'to_issue' => $toIssue > 0 ? $toIssue : 0,
                        'remaining_medicine' => $remainingMedicine > 0 ? $remainingMedicine : 0,
                        'substitute_drug' => isset($drug['substituteDrug']) ? $drug['substituteDrug'] : 0,
                        'prescribed_days' => isset($drug['duration']) && $drug['duration'] > 0 ? $drug['duration'] : 1,
                        'early_morning' => null,
                        'morning' => isset($drug['morning']) && $drug['morning'] >= 0 ? $drug['morning'] : 0,
                        'late_morning' => null,
                        'afternoon' => isset($drug['afternoon']) && $drug['afternoon'] >= 0 ? $drug['afternoon'] : 0,
                        'late_afternoon' => null,
                        'evening' => isset($drug['evening']) && $drug['evening'] >= 0 ? $drug['evening'] : 0,
                        'night' => isset($drug['night']) && $drug['night'] >= 0 ? $drug['night'] : 0,
                        'late_night' => null,
                        'intake_condition' => isset($drug['drugIntakeCondition']) ? $drug['drugIntakeCondition'] : null,
                        'remarks' => isset($drug['remarks']) ? $drug['remarks'] : '',
                        'prescription_type' => isset($drug['prescription_type']) ? $drug['prescription_type'] : 'Type1',
                        'alternate_drug' => isset($drug['alternateDrug']) ? $drug['alternateDrug'] : 0,
                        'alternate_quantity' => isset($drug['alternateQuantity']) ? $drug['alternateQuantity'] : 0,
                    ];
                    PrescriptionDetails::create($prescriptionDetailsData);
                }
            }
            if ($request->has('test') && $request->test == 1) {
                return response()->json([
                    'result' => true,
                    'message' => 'Test Prescription added successfully',
                    'prescription_id' => $prescriptionRowId,
                    'employee_id' => $request->user_id
                ], 201);
            } else {
                    logger()->error($e);

                return response()->json(['result' => true, 'message' => 'Prescription added successfully'], 201);
            }
        } catch (\Exception $e) {
    logger()->error('Exception in addPrescriptionFromCorporate: ' . $e->getMessage());
    logger()->error($e->getTraceAsString());
    return response()->json([
        'result' => false,
        'message' => 'An error occurred while saving the prescription.',
        'error' => $e->getMessage() // include this temporarily for debugging
    ], 500);
}
    }


private function addPrescriptionFromEmployee(Request $request)
{
    try {
        $date = now()->format('dmY');
        $lastPrescription = Prescription::where('prescription_id', 'like', $date . '%')
            ->orderBy('prescription_id', 'desc')
            ->first();

        $nextId = $lastPrescription ? ((int)substr($lastPrescription->prescription_id, 8)) + 1 : 1;
        $prescriptionId = $date . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $cleanDate = str_replace(['-', '/'], '-', $request->prescription_date);
        $prescriptionDate = Carbon::createFromFormat('d-m-Y', $cleanDate)->format('Y-m-d H:i:s');

        // Create prescription main record
        $prescriptionTemplate = Prescription::create([
            'user_id' => $request->user_id,
            'prescription_id' => $prescriptionId,
            'role_id' => 2,
            'master_doctor_id' => $request->master_user_user_id,
            'op_registry_id' => $request->op_registry_id ?? 0,
            'corporate_ohc_id' => 0,
            'template_id' => $request->prescriptionTemplate,
            'master_hcsp_user_id' => null,
            'attachment_id' => null,
            'is_conformance' => null,
            'doctor_notes' => $request->doctorNotes,
            'user_notes' => $request->patientNotes,
            'share_with_patient' => $request->shareWithPatient,
            'case_id' => $request->case_id,
            'draft_save' => 'no',
            'fav_pharmacy' => $request->pharmacy ?? 0,
            'fav_lab' => $request->fav_lab ?? 0,
            'prescription_date' => $prescriptionDate,
            'order_status' => 0,
            'created_by' => auth('employee_api')->user()->id,
            'created_role' => 2,
            'corporate_location_id' => $request->corporate_id,
            'ohc' => $request->ohc ?? 0,
            'alternate_drug' => 0,
            'active_status' => 1,
            'prescription_attachments' => !empty($request->prescription_attachments) && is_array($request->prescription_attachments)
                ? json_encode($request->prescription_attachments)
                : null,
        ]);

        $prescriptionRowId = $prescriptionTemplate->prescription_id;

        // Add drugs
        if (isset($request->drugs) && is_array($request->drugs)) {
            foreach ($request->drugs as $drug) {
                $morning = $drug['morning'] ?? 0;
                $afternoon = $drug['afternoon'] ?? 0;
                $evening = $drug['evening'] ?? 0;
                $night = $drug['night'] ?? 0;
                $prescribedDays = $drug['duration'] ?? 0;
                $totalDosesPerDay = $morning + $afternoon + $evening + $night;

                PrescriptionDetails::create([
                    'prescription_row_id' => $prescriptionRowId,
                    'drug_name' => !empty($drug['drugName']) ? $drug['drugName'] : null,
                    'drug_template_id' => !empty($drug['drugTemplateId']) ? $drug['drugTemplateId'] : 0,
                    'to_issue' => $prescribedDays * $totalDosesPerDay,
                    'remaining_medicine' => $prescribedDays * $totalDosesPerDay,
                    'substitute_drug' => $drug['substituteDrug'] ?? 0,
                    'prescribed_days' => $prescribedDays > 0 ? $prescribedDays : 1,
                    'early_morning' => null,
                    'morning' => $morning,
                    'late_morning' => null,
                    'afternoon' => $afternoon,
                    'late_afternoon' => null,
                    'evening' => $evening,
                    'night' => $night,
                    'late_night' => null,
                    'intake_condition' => $drug['drugIntakeCondition'] ?? null,
                    'remarks' => $drug['remarks'] ?? '',
                    'prescription_type' => $drug['prescription_type'] ?? 'Type1',
                    'alternate_drug' => $drug['alternateDrug'] ?? 0,
                    'alternate_quantity' => $drug['alternateQuantity'] ?? 0,
                ]);
            }
        }

        // Response
        if ($request->has('test') && $request->test == 1) {
            return response()->json([
                'result' => true,
                'message' => 'Test Prescription added successfully',
                'prescription_id' => $prescriptionRowId,
                'employee_id' => $request->user_id
            ], 201);
        }

        return response()->json([
            'result' => true,
            'message' => 'Prescription added successfully',
            'prescription_id' => $prescriptionRowId
        ], 201);

    } catch (\Exception $e) {
        logger()->error('Exception in addPrescriptionFromEmployee: ' . $e->getMessage());
        return response()->json([
            'result' => false,
            'message' => 'An error occurred while saving the prescription.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function getEmployeePrescription($userId, Request $request)
    {
        try {
            $pharmacyStockSubquery = DB::table('pharmacy_stock')
                ->select(
                    'drug_template_id',
                    'ohc_pharmacy_id',
                    DB::raw('SUM(current_availability) as total_availability')
                )
                ->groupBy('drug_template_id', 'ohc_pharmacy_id');
            $prescriptionTemplatesQuery = DB::table('prescription')
                ->join('prescription_details', 'prescription.prescription_id', '=', 'prescription_details.prescription_row_id')
                ->leftJoin('drug_template', 'prescription_details.drug_template_id', '=', 'drug_template.drug_template_id')
                ->leftJoin('employee_user_mapping', 'prescription.user_id', '=', 'employee_user_mapping.employee_id')
                ->leftJoin('corporate_admin_user', 'prescription.created_by', '=', 'corporate_admin_user.id')
                ->leftJoin('op_registry', 'prescription.op_registry_id', '=', 'op_registry.op_registry_id')
                ->leftJoinSub($pharmacyStockSubquery, 'pharmacy_stock', function ($join) {
                    $join->on('prescription_details.drug_template_id', '=', 'pharmacy_stock.drug_template_id')
                        ->on('prescription.fav_pharmacy', '=', 'pharmacy_stock.ohc_pharmacy_id');
                })
                ->where('prescription.master_doctor_id', $userId)
                ->orderBy('prescription.prescription_row_id', 'desc')
                ->orderBy('prescription_details.prescription_details_id', 'asc')
                ->select(
                    'prescription.prescription_id',
                    'prescription.user_id',
                    'prescription.op_registry_id',
                    'prescription.master_doctor_id',
                    'prescription.role_id',
                    'prescription.template_id',
                    'prescription.doctor_notes',
                    'prescription.user_notes',
                    'prescription.prescription_date',
                    'prescription.order_status',
                    'prescription.created_at',
                    'prescription.updated_at',
                    'prescription.fav_pharmacy',
                    'prescription.fav_lab',
                    'prescription.ohc',
                    'prescription.alternate_drug',
                    'prescription.active_status',
                    'prescription_details.prescription_details_id',
                    'prescription_details.drug_name',
                    'prescription_details.drug_template_id',
                    'prescription_details.to_issue',
                    'prescription_details.remaining_medicine',
                    'prescription_details.substitute_drug',
                    'prescription_details.prescribed_days',
                    'prescription_details.early_morning',
                    'prescription_details.morning',
                    'prescription_details.afternoon',
                    'prescription_details.evening',
                    'prescription_details.night',
                    'prescription_details.intake_condition',
                    'prescription_details.remarks',
                    'prescription_details.prescription_type',
                    'drug_template.drug_name as drugNameById',
                    'drug_template.drug_strength',
                    'drug_template.drug_type',
                    'corporate_admin_user.first_name as doctor_first_name',
                    'corporate_admin_user.last_name as doctor_last_name',
                    'op_registry.doctor_id as registry_doctor_id',
                    'op_registry.type_of_incident',
                    'op_registry.body_part as registry_body_parts',
                    'op_registry.doctor_notes as registry_doctor_notes',
                    'op_registry.past_medical_history',
                    DB::raw('COALESCE(pharmacy_stock.total_availability, 0) as current_availability')
                );
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            if ($fromDate && $toDate) {
                $fromDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->startOfDay();
                $toDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->endOfDay();
                $prescriptionTemplatesQuery->whereBetween('prescription.created_at', [$fromDateFormatted, $toDateFormatted]);
            } else {
                $today = \Carbon\Carbon::today();
                $prescriptionTemplatesQuery->whereDate('prescription.created_at', $today);
            }
            $prescriptionTemplates = $prescriptionTemplatesQuery->get();
            if ($prescriptionTemplates->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the selected date(s).'
                ], 404);
            }
            $groupedPrescriptions = $prescriptionTemplates->groupBy('prescription_id')->map(function ($items) {
                $prescription = $items->first();
                $bodyPartIds = json_decode($prescription->registry_body_parts, true);
                $bodyPartNames = [];
                if (is_array($bodyPartIds)) {
                    $bodyPartIdsInt = array_map('intval', $bodyPartIds);
                    $bodyPartNames = DB::table('outpatient_component')
                        ->whereIn('op_component_id', $bodyPartIdsInt)
                        ->pluck('op_component_name')
                        ->toArray();
                }
                $userId = $prescription->user_id;
                $employee = EmployeeUserMapping::where('employee_id', $userId)->first();
                if (!$employee) {
                    return null;
                }
                $employeeFirstname = $this->aes256DecryptData($employee->masterUser->first_name);
                $employeeLastname = $this->aes256DecryptData($employee->masterUser->last_name);
                $employeeId = $employee->employee_id;
                $employeeCorporateName = MasterCorporate::where('corporate_id', $employee->corporate_id)->value('corporate_name');
                $employeeLocationName = MasterCorporate::where('corporate_id', $employee->corporate_id)
                    ->where('location_id', $employee->location_id)->value('display_name');
                $employeeDesignation = $employee->designation;
                $employeeDepartment = CorporateHl1::where('hl1_id', $employee->hl1_id)->value('hl1_name');
                $employeeGender = $this->aes256DecryptData($employee->masterUser->gender);
                $employeeDob = $this->aes256DecryptData($employee->masterUser->dob);
                $employeeAge = date_diff(date_create($employeeDob), date_create('now'))->y;
                $employeeDetails = [
                    'employee_id' => $employeeId,
                    'employee_firstname' => $employeeFirstname,
                    'employee_lastname' => $employeeLastname,
                    'employee_corporate_name' => $employeeCorporateName,
                    'employee_location_name' => $employeeLocationName,
                    'employee_designation' => $employeeDesignation,
                    'employee_department' => $employeeDepartment,
                    'employee_gender' => $employeeGender,
                    'employee_age' => $employeeAge
                ];
                return [
                    'prescription_id' => $prescription->prescription_id,
                    'user_id' => $prescription->user_id,
                    'op_registry_id' => $prescription->op_registry_id,
                    'master_doctor_id' => $prescription->master_doctor_id,
                    'role_id' => $prescription->role_id,
                    'template_id' => $prescription->template_id,
                    'doctor_notes' => $prescription->doctor_notes,
                    'user_notes' => $prescription->user_notes,
                    'prescription_date' => $prescription->prescription_date,
                    'order_status' => $prescription->order_status,
                    'created_at' => $prescription->created_at,
                    'updated_at' => $prescription->updated_at,
                    'fav_pharmacy' => $prescription->fav_pharmacy,
                    'fav_lab' => $prescription->fav_lab,
                    'ohc' => $prescription->ohc,
                    'alternate_drug' => $prescription->alternate_drug,
                    'active_status' => $prescription->active_status,
                    'registry_doctor_id' => $prescription->registry_doctor_id,
                    'type_of_incident' => $prescription->type_of_incident,
                    'registry_doctor_notes' => $prescription->registry_doctor_notes,
                    'past_medical_history' => $prescription->past_medical_history,
                    'body_part_ids' => $bodyPartIds,
                    'body_part_names' => $bodyPartNames,
                    'doctor_firstname' => $prescription->doctor_first_name ? $this->aes256DecryptData($prescription->doctor_first_name) : null,
                    'doctor_lastname' => $prescription->doctor_last_name ? $this->aes256DecryptData($prescription->doctor_last_name) : null,
                    'prescription_details' => $items->sortBy('prescription_details_id')->map(function ($detail) {
                        return [
                            'prescription_details_id' => $detail->prescription_details_id,
                            'drug_name' => $detail->drug_name ?? $detail->drugNameById,
                            'drug_template_id' => $detail->drug_template_id,
                            'to_issue' => $detail->to_issue,
                            'remaining_medicine' => $detail->remaining_medicine,
                            'substitute_drug' => $detail->substitute_drug,
                            'prescribed_days' => $detail->prescribed_days,
                            'early_morning' => $detail->early_morning,
                            'morning' => $detail->morning,
                            'afternoon' => $detail->afternoon,
                            'evening' => $detail->evening,
                            'night' => $detail->night,
                            'intake_condition' => $detail->intake_condition,
                            'remarks' => $detail->remarks,
                            'prescription_type' => $detail->prescription_type,
                            'drug_strength' => $detail->drug_strength,
                            'drug_type' => $detail->drug_type,
                            'current_availability' => $detail->current_availability
                        ];
                    }),
                    'employee' => $employeeDetails
                ];
            })->filter()->values();
            return response()->json([
                'result' => true,
                'data' => $groupedPrescriptions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Prescription Templates.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function aes256DecryptData(string $data)
    {
        if ($data === null) {
            return null;
        }
        $decodedData = base64_decode($data);
        if ($decodedData === false) {
            throw new \Exception('Failed to base64 decode data.');
        }
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($decodedData, 0, $ivLength);
        $encryptedData = substr($decodedData, $ivLength);
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $decryptedData = openssl_decrypt($encryptedData, $cipher, $key, 0, $iv);
        if ($decryptedData === false) {
            throw new \Exception('Decryption failed');
        }
        return $decryptedData;
    }
    public function getStockByDrugId($drugTemplateId)
    {
        try {
            $query = DB::table('pharmacy_stock')
                ->where('drug_template_id', $drugTemplateId);
            $totalAvailability = $query->sum('current_availability');
            if ($totalAvailability === null) {
                $totalAvailability = 0;
            }
            return response()->json([
                'result' => true,
                'data' => [
                    'total_current_availability' => $totalAvailability
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the stock data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStockByDrugIdAndPharmacyId($drugTemplateId, $pharmacy_id)
    {
        try {
            $totalAvailability = DB::table('pharmacy_stock')
                ->where('drug_template_id', $drugTemplateId)
                ->where('ohc_pharmacy_id', $pharmacy_id)
                ->sum('current_availability');
            if ($totalAvailability === null) {
                $totalAvailability = 0;
            }
            return response()->json([
                'result' => true,
                'data' => [
                    'total_current_availability' => $totalAvailability
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the stock data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getPrintPrescriptionById($prescription_id = null, Request $request)
    {
        try {
            if ($prescription_id === null || !ctype_alnum($prescription_id)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            $prescription_type = $request->input('group');
            $prescriptionQuery = DB::table('prescription')
                ->join('prescription_details', 'prescription.prescription_id', '=', 'prescription_details.prescription_row_id')
                ->leftJoin('drug_template', 'prescription_details.drug_template_id', '=', 'drug_template.drug_template_id')
                ->leftJoin('corporate_admin_user', 'prescription.master_doctor_id', '=', 'corporate_admin_user.corporate_admin_user_id')
                ->where('prescription.prescription_id', $prescription_id);
            if ($prescription_type === 'Type2') {
                $prescriptionQuery->where('prescription_details.prescription_type', 'Type2');
            }
            $prescriptionTemplate = $prescriptionQuery->select(
                'prescription.*',
                'prescription_details.*',
                'corporate_admin_user.first_name',
                'corporate_admin_user.last_name',
                'drug_template.drug_name as drugNameById',
                'drug_template.drug_strength',
                'drug_template.drug_type'
            )
                ->get();
            if ($prescriptionTemplate->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No prescription template found for the given ID and type.'
                ], 404);
            }
            $employee = EmployeeUserMapping::where('employee_id', $prescriptionTemplate[0]->user_id)->first();
            if ($employee) {
                $employeeFirstname = $this->aes256DecryptData($employee->masterUser->first_name);
                $employeeLastname = $this->aes256DecryptData($employee->masterUser->last_name);
                $employeeId = $employee->employee_id;
                $employeeCorporateName = MasterCorporate::where('corporate_id', $employee->corporate_id)->value('corporate_name');
                $employeeLocationName = MasterCorporate::where('corporate_id', $employee->corporate_id)->where('location_id', $employee->location_id)->value('display_name');
                $employeeDesignation = $employee->designation;
                $employeeDepartment = CorporateHl1::where('hl1_id', $employee->hl1_id)->value('hl1_name');
                $employeeGender = $this->aes256DecryptData($employee->masterUser->gender);
                $employeeDob = $this->aes256DecryptData($employee->masterUser->dob);
                $employeeAge = date_diff(date_create($employeeDob), date_create('now'))->y;
                $prescriptionTemplate[0]->employee_firstname = $employeeFirstname;
                $prescriptionTemplate[0]->employee_lastname = $employeeLastname;
                $prescriptionTemplate[0]->employee_id = $employeeId;
                $prescriptionTemplate[0]->employee_corporate_name = $employeeCorporateName;
                $prescriptionTemplate[0]->employee_location_name = $employeeLocationName;
                $prescriptionTemplate[0]->employee_designation = $employeeDesignation;
                $prescriptionTemplate[0]->employee_department = $employeeDepartment;
                $prescriptionTemplate[0]->employee_gender = $employeeGender;
                $prescriptionTemplate[0]->employee_dob = $employeeDob;
                $prescriptionTemplate[0]->employee_age = $employeeAge;
            } else {
                $prescriptionTemplate[0]->employee_firstname = 'Unknown';
                $prescriptionTemplate[0]->employee_lastname = 'Unknown';
                $prescriptionTemplate[0]->employee_id = null;
                $prescriptionTemplate[0]->employee_corporate_name = 'Unknown';
                $prescriptionTemplate[0]->employee_location_name = 'Unknown';
                $prescriptionTemplate[0]->employee_designation = 'Unknown';
                $prescriptionTemplate[0]->employee_department = 'Unknown';
                $prescriptionTemplate[0]->employee_gender = 'Unknown';
                $prescriptionTemplate[0]->employee_dob = 'Unknown';
                $prescriptionTemplate[0]->employee_age = null;
            }
            $prescriptionTemplate[0]->doctor_name = $prescriptionTemplate[0]->employee_firstname && $prescriptionTemplate[0]->employee_lastname
                ? $prescriptionTemplate[0]->employee_firstname . ' ' . $prescriptionTemplate[0]->employee_lastname
                : ($prescriptionTemplate[0]->first_name && $prescriptionTemplate[0]->last_name
                    ? $prescriptionTemplate[0]->first_name . ' ' . $prescriptionTemplate[0]->last_name
                    : 'Unknown Doctor');
            $prescriptionDetails = $prescriptionTemplate->map(function ($detail) {
                return [
                    'prescription_details_id' => $detail->prescription_details_id,
                    'drug_name' => $detail->drug_name ?? $detail->drugNameById,
                    'drug_template_id' => $detail->drug_template_id,
                    'to_issue' => $detail->to_issue,
                    'remaining_medicine' => $detail->remaining_medicine,
                    'substitute_drug' => $detail->substitute_drug,
                    'prescribed_days' => $detail->prescribed_days,
                    'early_morning' => $detail->early_morning,
                    'morning' => $detail->morning,
                    'afternoon' => $detail->afternoon,
                    'evening' => $detail->evening,
                    'night' => $detail->night,
                    'intake_condition' => $detail->intake_condition,
                    'remarks' => $detail->remarks,
                    'prescription_type' => $detail->prescription_type,
                    'drug_strength' => $detail->drug_strength,
                    'drug_type' => $detail->drug_type,
                ];
            });
            $prescriptionDetails = $prescriptionDetails->unique('prescription_details_id');
            return response()->json([
                'result' => true,
                'data' => [
                    'prescription' => $prescriptionTemplate[0],
                    'prescription_details' => $prescriptionDetails
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Prescription Template.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

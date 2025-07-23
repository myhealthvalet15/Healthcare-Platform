<?php

namespace App\Http\Controllers\otc;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Prescription\Prescription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Prescription\PrescriptionDetails;
use App\Models\OpRegistry;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Department\CorporateHl1;

class OtcController extends Controller
{
    public function addPrescriptionForOTC(Request $request)
    {
        DB::beginTransaction();

        try {
            $lastOpRegistry = OpRegistry::orderBy('op_registry_id', 'desc')->first();
            $newOpRegistryId = $lastOpRegistry ? $lastOpRegistry->op_registry_id + 1 : 1;

            $opRegistry = OpRegistry::create([
                 'op_registry_id' => $newOpRegistryId,
                'doctor_id' => 0,
                'master_user_id' =>  $request->corporate_user_id,
                'corporate_id' => $request->corporate_id,
                'location_id' => $request->location_id,
                'symptoms' => json_encode($request->symptoms ?? []),
                'medical_system' => json_encode($request->medicalSystems ?? []),
                'doctor_notes' => $request->remarks ?? '',
                'type_of_incident' => '',
                'corporate_ohc_id' => 1,
                'open_status' => 1,
                'movement_slip' => 0,
                'fitness_certificate' => 0,
                'physiotherapy' => 0,
                'created_date_time' => now(),
                'day_of_registry' => now()->toDateString(),
                'registry_type' => 'OTC',
                'shift' => $request->shift ?? '',
                'first_aid_by' => $request->first_aid_by ?? '',
                'created_date_time' => $request->created_date_time ?? '',
            ]);

            $opRegistryId = $opRegistry->op_registry_id;

            // STEP 2: Generate prescription ID
            $date = now()->format('dmY');
            $lastPrescription = Prescription::where('prescription_id', 'like', $date . '%')
                ->orderBy('prescription_id', 'desc')
                ->first();

            $nextId = $lastPrescription
                ? ((int)substr($lastPrescription->prescription_id, 8)) + 1
                : 1;

            $prescriptionId = $date . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            // STEP 3: Insert into prescription table
            $prescription = Prescription::create([
                'user_id' => $request->user_id,
                'prescription_id' => $prescriptionId,
                'master_doctor_id' => $request->corporate_user_id,
                'role_id' => 4,
                'op_registry_id' => $opRegistryId,
                'corporate_ohc_id' => 0,
                'template_id' => $request->prescriptionTemplate ?? null,
                'doctor_notes' => $request->remarks ?? '',
                'user_notes' => null,
                'share_with_patient' => $request->shareWithPatient ?? 0,
                'case_id' => $request->case_id ?? 0,
                'draft_save' => 'no',
                'fav_pharmacy' => $request->pharmacy ?? 0,
                'fav_lab' => $request->fav_lab ?? 0,
                'prescription_date' => $request->prescription_date ?? now(),
                'order_status' => 0,
                'created_by' => auth('api')->user()->id,
                'created_role' => 4,
                'corporate_location_id' => $request->corporate_id,
                'ohc' => $request->ohc ?? 0,
                'alternate_drug' => 0,
                'active_status' => 1,
                'is_otc' => 1,
            ]);

            // STEP 4: Insert and issue drugs
            $userId = auth('api')->id() ?? 0;
            $ohcPharmacyId = $request->ohc_pharmacy_id;

            if (isset($request->prescriptions) && is_array($request->prescriptions)) {
                foreach ($request->prescriptions as $drug) {
                    $drugTemplateId = $drug['drugId'] ?? 0;
                    $issuedQty = $drug['issue'] ?? 0;

                    // Insert into PrescriptionDetails
                    $detail = PrescriptionDetails::create([
                        'prescription_row_id' => $prescription->prescription_id,
                        'drug_template_id' => $drugTemplateId,
                        'to_issue' => $issuedQty,
                        'remaining_medicine' => $issuedQty,
                        'prescription_type' => 'Type1',
                        'prescribed_days' => 1,
                        'morning' => 1,
                        'afternoon' => 0,
                        'evening' => 0,
                        'night' => 0,
                    ]);

                    $prescriptionDetailsId = $detail->prescription_details_id;
                    $remainingQty = $issuedQty;

                    // Fetch stock
                    $stocks = DB::table('pharmacy_stock')
                        ->where('ohc_pharmacy_id', $ohcPharmacyId)
                        ->where('drug_template_id', $drugTemplateId)
                        ->where('current_availability', '>', 0)
                        ->orderBy('drug_id', 'asc')
                        ->lockForUpdate()
                        ->get();

                    if ($stocks->sum('current_availability') < $issuedQty) {
                        throw new \Exception("Not enough stock for Drug Template ID: $drugTemplateId.");
                    }

                    foreach ($stocks as $stock) {
                        if ($remainingQty <= 0) {
                            break;
                        }

                        $deductQty = min($remainingQty, $stock->current_availability);

                        // Update stock
                        DB::table('pharmacy_stock')
                            ->where('drug_id', $stock->drug_id)
                            ->update([
                                'current_availability' => $stock->current_availability - $deductQty,
                                'sold_quantity' => $stock->sold_quantity + $deductQty,
                                'updated_at' => now(),
                            ]);

                        // Log sale
                        DB::table('drug_stock_sold')->insert([
                            'pharmacy_stock_id' => $stock->drug_id,
                            'quantity' => $deductQty,
                            'drug_value' => $drugTemplateId,
                            'master_user_id' => 0,
                            'prescription_id' => $prescriptionId,
                            'ohc' => 1,
                            'ohc_pharmacy_id' => $ohcPharmacyId,
                            'move_to' => 0,
                            'pharmacy_walkin' => 0,
                            'created_by' => $userId,
                            'created_on' => now(),
                            'master_pharmacy_id' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $remainingQty -= $deductQty;
                    }

                    // Update PrescriptionDetails: to_issue = 0
                    DB::table('prescription_details')
                        ->where('prescription_details_id', $prescriptionDetailsId)
                        ->update(['to_issue' => 0]);
                }
            }

            // STEP 5: Update prescription order_status
            DB::table('prescription')
                ->where('prescription_id', $prescriptionId)
                ->update([
                    'order_status' => 1,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'result' => true,
                'message' => 'Prescription created and issued successfully.',
                'prescription_id' => $prescriptionId,
                'op_registry_id' => $opRegistryId,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in addPrescriptionForOTC: ' . $e->getMessage());

            return response()->json([
                'result' => false,
                'message' => 'Failed to process prescription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getAllotcDetails($location_id, Request $request)
    {
        try {
            $query = DB::table('prescription')
                ->join('prescription_details', 'prescription.prescription_id', '=', 'prescription_details.prescription_row_id')
                ->leftJoin('drug_template', 'prescription_details.drug_template_id', '=', 'drug_template.drug_template_id')
                ->leftJoin('employee_user_mapping', 'prescription.user_id', '=', 'employee_user_mapping.employee_id')
                ->leftJoin('corporate_admin_user', 'prescription.created_by', '=', 'corporate_admin_user.id')
                ->leftJoin('op_registry', 'prescription.op_registry_id', '=', 'op_registry.op_registry_id')
                ->where('op_registry.location_id', $location_id)
                ->where('prescription.is_otc', 1)
                ->select(
                    'prescription.prescription_id',
                    'prescription.created_at as prescription_created_at',
                    'prescription_details.prescription_details_id',
                    'prescription_details.drug_template_id',
                    'prescription_details.to_issue',
                    'drug_template.drug_name',
                    'drug_template.drug_type',
                    'drug_template.drug_strength',
                    'op_registry.created_at as registry_created_at',
                    'op_registry.medical_system',
                    'op_registry.symptoms',
                    'op_registry.first_aid_by',
                    'op_registry.doctor_notes',
                    'employee_user_mapping.employee_id',
                    'employee_user_mapping.corporate_id',
                    'employee_user_mapping.location_id',
                    'employee_user_mapping.designation',
                    'employee_user_mapping.hl1_id'
                );

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            if ($fromDate && $toDate) {
                $fromDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->startOfDay();
                $toDateFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->endOfDay();
                $query->whereBetween('prescription.created_at', [$fromDateFormatted, $toDateFormatted]);
            }

            $results = $query->get();

            if ($results->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the selected date(s).'
                ], 404);
            }

            // Group by prescription_id
            $grouped = $results->groupBy('prescription_id')->map(function ($items, $prescriptionId) {
                $first = $items->first();

                $employee = EmployeeUserMapping::where('employee_id', $first->employee_id)->first();
                if (!$employee || !$employee->masterUser) {
                    return null;
                }

                $firstName = $this->aes256DecryptData($employee->masterUser->first_name);
                $lastName = $this->aes256DecryptData($employee->masterUser->last_name);
                $dob = $this->aes256DecryptData($employee->masterUser->dob);
                $age = $dob ? date_diff(date_create($dob), date_create('now'))->y : null;

                $department = CorporateHl1::where('hl1_id', $employee->hl1_id)->value('hl1_name');

                // Resolve symptoms
                $symptomIds = json_decode($first->symptoms, true);
                $symptomNames = [];
                if (is_array($symptomIds)) {
                    $symptomIdsInt = array_map('intval', $symptomIds);
                    $symptomNames = DB::table('outpatient_component')
                        ->where('op_component_type', 6)
                        ->whereIn('op_component_id', $symptomIdsInt)
                        ->pluck('op_component_name')
                        ->toArray();
                }

                // Resolve medical systems
                $medicalSystemIds = json_decode($first->medical_system, true);
                $medicalSystemNames = [];
                if (is_array($medicalSystemIds)) {
                    $medicalSystemIdsInt = array_map('intval', $medicalSystemIds);
                    $medicalSystemNames = DB::table('outpatient_component')
                        ->where('op_component_type', 7)
                        ->whereIn('op_component_id', $medicalSystemIdsInt)
                        ->pluck('op_component_name')
                        ->toArray();
                }

                // For each drug, get issued_quantity from drug_stock_sold
                $drugs = $items->map(function ($item) use ($prescriptionId) {
                    $issuedQty = DB::table('drug_stock_sold')
                        ->where('prescription_id', $prescriptionId)
                        ->where('drug_value', $item->drug_template_id)
                        ->sum('quantity');

                    return [
                        'drug_template_id' => $item->drug_template_id,
                        'to_issue' => $item->to_issue,
                        'drug_name' => $item->drug_name,
                        'drug_type' => $item->drug_type,
                        'drug_strength' => $item->drug_strength,
                        'issued_quantity' => (int)$issuedQty,
                    ];
                })->values();

                return [
                    'prescription_id' => $prescriptionId,
                    'prescription_created_at' => $first->prescription_created_at,
                    'registry_created_at' => $first->registry_created_at,
                    'medical_system' => $medicalSystemNames,
                    'symptoms' => $symptomNames,
                    'first_aid_by' => $first->first_aid_by,
                    'doctor_notes' => $first->doctor_notes,
                    'employee_id' => $first->employee_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'age' => $age,
                    'department' => $department,
                    'drugs' => $drugs
                ];
            })->filter()->values();

            return response()->json([
                'result' => true,
                'data' => $grouped
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the data.',
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



}

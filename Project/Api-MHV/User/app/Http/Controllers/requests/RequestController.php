<?php

namespace App\Http\Controllers\requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MasterCorporate;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Department\CorporateHl1;

class RequestController extends Controller
{
    public function getEmployeePrescriptionforPendingRequest($userId, Request $request)
    {
        try {
            // Subquery to get total current_availability grouped by drug_template_id and pharmacy
            $pharmacyStockSubquery = DB::table('pharmacy_stock')
                ->select(
                    'drug_template_id',
                    'ohc_pharmacy_id',
                    DB::raw('SUM(current_availability) as total_availability')
                )
                ->groupBy('drug_template_id', 'ohc_pharmacy_id');

            // Start building the main query
            $prescriptionTemplatesQuery = DB::table('prescription')
                ->join('prescription_details', 'prescription.prescription_id', '=', 'prescription_details.prescription_row_id')
                ->leftJoin('drug_template', 'prescription_details.drug_template_id', '=', 'drug_template.drug_template_id')
                ->leftJoin('employee_user_mapping', 'prescription.user_id', '=', 'employee_user_mapping.employee_id')
                ->leftJoinSub($pharmacyStockSubquery, 'pharmacy_stock', function ($join) {
                    $join->on('prescription_details.drug_template_id', '=', 'pharmacy_stock.drug_template_id')
                         ->on('prescription.fav_pharmacy', '=', 'pharmacy_stock.ohc_pharmacy_id');
                })
                ->where('prescription.master_doctor_id', $userId)
                ->whereNotNull('prescription.order_status') // ✅ Make sure it's not NULL
                ->where('prescription.order_status', '!=', 1) // ✅ Must be exactly 0
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
                    DB::raw('COALESCE(pharmacy_stock.total_availability, 0) as current_availability')
                );

            // ✅ Date filtering logic with format "d/m/Y"
            if ($request->has('from_date') && $request->input('from_date')) {
                $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
                $prescriptionTemplatesQuery->whereDate('prescription.created_at', '>=', $fromDate);
            }

            if ($request->has('to_date') && $request->input('to_date')) {
                $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
                $prescriptionTemplatesQuery->whereDate('prescription.created_at', '<=', $toDate);
            }

            // 🔍 Debug SQL: Show raw query + bindings
            dd($prescriptionTemplatesQuery->toSql(), $prescriptionTemplatesQuery->getBindings());

            // Execute the query
            $prescriptionTemplates = $prescriptionTemplatesQuery->get();

            if ($prescriptionTemplates->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the given filters.'
                ], 404);
            }

            // Group and format the data
            $groupedPrescriptions = $prescriptionTemplates->groupBy('prescription_id')->map(function ($items) {
                $prescription = $items->first();
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
            })->filter()->values(); // Remove nulls from collection

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
    public function issuePartlyPrescription(Request $request)
    {
        try {
            // Validate structure
            $validatedData = $request->validate([
                '*.prescription_id'         => 'required|string',
                '*.ohc_pharmacy_id'         => 'required|integer',
                '*.prescription_details_id' => 'required|integer',
                '*.issued_quantity'         => 'required|integer|min:1',
                '*.drug_template_id'        => 'required|integer',
            ]);

            $userId = auth('api')->id() ?? 0;
            $prescriptionId = $validatedData[0]['prescription_id'] ?? null;

            DB::beginTransaction();

            foreach ($validatedData as $item) {
                $drugTemplateId       = $item['drug_template_id'];
                $ohcPharmacyId        = $item['ohc_pharmacy_id'];
                $issuedQty            = $item['issued_quantity'];
                $prescriptionDetailsId = $item['prescription_details_id'];
                $remainingQty         = $issuedQty;

                Log::info("Processing prescription: $prescriptionId for Drug: $drugTemplateId, Qty: $issuedQty");

                // Get all matching stock entries with availability > 0, ordered by insertion
                $stocks = DB::table('pharmacy_stock')
                    ->where('ohc_pharmacy_id', $ohcPharmacyId)
                    ->where('drug_template_id', $drugTemplateId)
                    ->where('current_availability', '>', 0)
                    ->orderBy('drug_id', 'asc')
                    ->lockForUpdate()
                    ->get();

                if ($stocks->sum('current_availability') < $issuedQty) {
                    throw new \Exception("Not enough stock for Drug Template ID: $drugTemplateId (Requested: $issuedQty, Available: {$stocks->sum('current_availability')}).");
                }

                foreach ($stocks as $stock) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    $deductQty = min($remainingQty, $stock->current_availability);
                    $newAvailability = $stock->current_availability - $deductQty;
                    $newSold = $stock->sold_quantity + $deductQty;

                    // Update pharmacy_stock
                    DB::table('pharmacy_stock')
                        ->where('drug_id', $stock->drug_id)
                        ->update([
                            'current_availability' => $newAvailability,
                            'sold_quantity'        => $newSold,
                            'updated_at'           => now(),
                        ]);

                    // Insert into drug_stock_sold
                    DB::table('drug_stock_sold')->insert([
                        'pharmacy_stock_id'   => $stock->drug_id,
                        'quantity'            => $deductQty,
                        'drug_value'          => $drugTemplateId,
                        'master_user_id'      => 0,
                        'prescription_id'     => $prescriptionId,
                        'ohc'                 => 1,
                        'ohc_pharmacy_id'     => $ohcPharmacyId,
                        'move_to'             => 0,
                        'pharmacy_walkin'     => 0,
                        'created_by'          => $userId,
                        'created_on'          => now(),
                        'master_pharmacy_id'  => 0,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    Log::info("Used stock ID: {$stock->drug_id} - Deducted: $deductQty");

                    $remainingQty -= $deductQty;
                }

                if ($remainingQty > 0) {
                    throw new \Exception("Partial issue: Not enough stock could be deducted for Drug Template ID: $drugTemplateId");
                }

                // Update the to_issue quantity in prescription_details
                $currentToIssue = DB::table('prescription_details')
                    ->where('prescription_details_id', $prescriptionDetailsId)
                    ->lockForUpdate()
                    ->value('to_issue');

                $newToIssue = max(0, $currentToIssue - $issuedQty);

                DB::table('prescription_details')
                    ->where('prescription_details_id', $prescriptionDetailsId)
                    ->update(['to_issue' => $newToIssue]);

                Log::info("Issued total of $issuedQty for Prescription ID: $prescriptionId and updated to_issue to $newToIssue for Prescription Details ID: $prescriptionDetailsId");
            }

            // Update prescription order status
            if ($prescriptionId) {
                DB::table('prescription')
                    ->where('prescription_id', $prescriptionId)
                    ->update([
                        'order_status' => 0,
                        'updated_at'   => now()
                    ]);

                Log::info("Force updated order_status to 1 for Prescription ID: $prescriptionId");
            }

            DB::commit();

            return response()->json([
                'result'  => true,
                'message' => 'Prescription issued successfully with batch-wise stock deduction.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error issuing prescription: ' . $e->getMessage());

            return response()->json([
                'result'  => false,
                'message' => 'Failed to issue prescription.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function closePrescription(Request $request)
    {
        try {
            // Validate structure
            $validatedData = $request->validate([
                '*.prescription_id'         => 'required|string',
                '*.ohc_pharmacy_id'         => 'required|integer',
                '*.prescription_details_id' => 'required|integer',
                '*.issued_quantity'         => 'required|integer|min:1',
                '*.drug_template_id'        => 'required|integer',
            ]);

            $userId = auth('api')->id() ?? 0;
            $prescriptionId = $validatedData[0]['prescription_id'] ?? null;

            DB::beginTransaction();

            foreach ($validatedData as $item) {
                $drugTemplateId       = $item['drug_template_id'];
                $ohcPharmacyId        = $item['ohc_pharmacy_id'];
                $issuedQty            = $item['issued_quantity'];
                $prescriptionDetailsId = $item['prescription_details_id'];
                $remainingQty         = $issuedQty;

                Log::info("Processing prescription: $prescriptionId for Drug: $drugTemplateId, Qty: $issuedQty");

                // Get all matching stock entries with availability > 0, ordered by insertion
                $stocks = DB::table('pharmacy_stock')
                    ->where('ohc_pharmacy_id', $ohcPharmacyId)
                    ->where('drug_template_id', $drugTemplateId)
                    ->where('current_availability', '>', 0)
                    ->orderBy('drug_id', 'asc')
                    ->lockForUpdate()
                    ->get();

                if ($stocks->sum('current_availability') < $issuedQty) {
                    throw new \Exception("Not enough stock for Drug Template ID: $drugTemplateId (Requested: $issuedQty, Available: {$stocks->sum('current_availability')}).");
                }

                foreach ($stocks as $stock) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    $deductQty = min($remainingQty, $stock->current_availability);
                    $newAvailability = $stock->current_availability - $deductQty;
                    $newSold = $stock->sold_quantity + $deductQty;

                    // Update pharmacy_stock
                    DB::table('pharmacy_stock')
                        ->where('drug_id', $stock->drug_id)
                        ->update([
                            'current_availability' => $newAvailability,
                            'sold_quantity'        => $newSold,
                            'updated_at'           => now(),
                        ]);

                    // Insert into drug_stock_sold
                    DB::table('drug_stock_sold')->insert([
                        'pharmacy_stock_id'   => $stock->drug_id,
                        'quantity'            => $deductQty,
                        'drug_value'          => $drugTemplateId,
                        'master_user_id'      => 0,
                        'prescription_id'     => $prescriptionId,
                        'ohc'                 => 1,
                        'ohc_pharmacy_id'     => $ohcPharmacyId,
                        'move_to'             => 0,
                        'pharmacy_walkin'     => 0,
                        'created_by'          => $userId,
                        'created_on'          => now(),
                        'master_pharmacy_id'  => 0,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    Log::info("Used stock ID: {$stock->drug_id} - Deducted: $deductQty");

                    $remainingQty -= $deductQty;
                }

                if ($remainingQty > 0) {
                    throw new \Exception("Partial issue: Not enough stock could be deducted for Drug Template ID: $drugTemplateId");
                }

                // Update the to_issue quantity in prescription_details
                $currentToIssue = DB::table('prescription_details')
                    ->where('prescription_details_id', $prescriptionDetailsId)
                    ->lockForUpdate()
                    ->value('to_issue');

                $newToIssue = 0;

                DB::table('prescription_details')
                    ->where('prescription_details_id', $prescriptionDetailsId)
                    ->update(['to_issue' => $newToIssue]);

                Log::info("Issued total of $issuedQty for Prescription ID: $prescriptionId and updated to_issue to $newToIssue for Prescription Details ID: $prescriptionDetailsId");
            }

            // Update prescription order status
            if ($prescriptionId) {
                DB::table('prescription')
                    ->where('prescription_id', $prescriptionId)
                    ->update([
                        'order_status' => 1,
                        'updated_at'   => now()
                    ]);

                Log::info("Force updated order_status to 1 for Prescription ID: $prescriptionId");
            }

            DB::commit();

            return response()->json([
                'result'  => true,
                'message' => 'Prescription issued successfully with batch-wise stock deduction.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error issuing prescription: ' . $e->getMessage());

            return response()->json([
                'result'  => false,
                'message' => 'Failed to issue prescription.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getAllClosedPrescription($userId, Request $request)
    {
        try {
            // Subquery to get total current_availability grouped by drug_template_id and pharmacy
            $pharmacyStockSubquery = DB::table('pharmacy_stock')
                ->select(
                    'drug_template_id',
                    'ohc_pharmacy_id',
                    DB::raw('SUM(current_availability) as total_availability')
                )
                ->groupBy('drug_template_id', 'ohc_pharmacy_id');

            // Subquery to get total issued quantity from drug_stock_sold
            $drugStockSoldSubquery = DB::table('drug_stock_sold')
                ->select(
                    'prescription_id',
                    'drug_value',
                    DB::raw('SUM(quantity) as issued_quantity')
                )
                ->groupBy('prescription_id', 'drug_value');

            // Main query to get prescription details
            $prescriptionTemplatesQuery = DB::table('prescription')
                ->join('prescription_details', 'prescription.prescription_id', '=', 'prescription_details.prescription_row_id')
                ->leftJoin('drug_template', 'prescription_details.drug_template_id', '=', 'drug_template.drug_template_id')
                ->leftJoin('employee_user_mapping', 'prescription.user_id', '=', 'employee_user_mapping.employee_id')
                ->leftJoin('corporate_admin_user', 'prescription.created_by', '=', 'corporate_admin_user.id')
                ->leftJoinSub($pharmacyStockSubquery, 'pharmacy_stock', function ($join) {
                    $join->on('prescription_details.drug_template_id', '=', 'pharmacy_stock.drug_template_id')
                         ->on('prescription.fav_pharmacy', '=', 'pharmacy_stock.ohc_pharmacy_id');
                })
                ->leftJoinSub($drugStockSoldSubquery, 'drug_stock_sold', function ($join) {
                    $join->on('prescription.prescription_id', '=', 'drug_stock_sold.prescription_id')
                         ->on('prescription_details.drug_template_id', '=', 'drug_stock_sold.drug_value');
                })
                ->where('prescription.master_doctor_id', $userId)
                ->where('prescription.order_status', '=', 1)
                ->where(function($query) {
                    $query->whereNull('prescription.is_otc')
                          ->orWhere('prescription.is_otc', '!=', 1);
                })
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
                    DB::raw('COALESCE(pharmacy_stock.total_availability, 0) as current_availability'),
                    DB::raw('COALESCE(drug_stock_sold.issued_quantity, 0) as issued_quantity')
                );

            $prescriptionTemplates = $prescriptionTemplatesQuery->get();

            if ($prescriptionTemplates->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the given filters.'
                ], 404);
            }

            // Group and format the data
            $groupedPrescriptions = $prescriptionTemplates->groupBy('prescription_id')->map(function ($items) {
                $prescription = $items->first();
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
                            'current_availability' => $detail->current_availability,
                            'issued_quantity' => $detail->issued_quantity
                        ];
                    }),
                    'employee' => $employeeDetails
                ];
            })->filter()->values(); // Remove nulls

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


}

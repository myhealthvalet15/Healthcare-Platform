<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\EmployeeType;
use Exception;
use App\Models\Certification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\corporate_contractors;
use App\Models\Department\CorporateHl1;
use Illuminate\Support\Facades\Log;
use App\Models\CorporateHealthplan;
use App\Models\PrescribedTest;
use App\Models\PrescribedTestData;
use App\Models\HealthplanAssigned;
use App\Models\HealthplanAssignedStatus;
use App\Models\HealthplanAssignedStatusFile;
use App\Models\HealthplanCertification;
use App\Models\Hra\Master_Tests\MasterTest;
use App\Models\Outpatient;
use App\Models\MasterCorporate;
use App\Models\OpRegistry;
use App\Models\Prescription\Prescription;
use App\Http\Controllers\Prescription\PrescriptionController;
use App\Models\OpRegistryTimes;
use App\Models\OpOutsideReferral;
use App\Models\Corporate\MasterUser;
use App\Models\Corporate\CorporateOHC;
use App\Models\TestGroup;
use App\Models\OhcComponents;
use App\Models\HealthParameters;
use App\Models\DrugIngredient;
use App\Models\FoodAllergy;
use Illuminate\Http\JsonResponse;

class corporateEmployees extends Controller
{
    private $corporate_id = null;
    private $location_id = null;
    private function aes256DecryptData(string $data)
    {
        if ($data === null) {
            return null;
        }
        $decodedData = base64_decode($data);
        if ($decodedData === false) {
            throw new Exception('Failed to base64 decode data.');
        }
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($decodedData, 0, $ivLength);
        $encryptedData = substr($decodedData, $ivLength);
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $decryptedData = openssl_decrypt($encryptedData, $cipher, $key, 0, $iv);
        if ($decryptedData === false) {
            throw new Exception('Decryption failed');
        }
        return $decryptedData;
    }
    private function formatEmployeeRow($employee, $masterUser = null)
    {
        if ($masterUser === null && isset($employee->masterUser)) {
            $masterUser = $employee->masterUser;
        }
        $employeeTypeName = DB::table('employee_type')
            ->where('employee_type_id', $employee->employee_type_id)
            ->value('employee_type_name');
        $hl1Name = DB::table('corporate_hl1')
            ->where('hl1_id', $employee->hl1_id)
            ->value('hl1_name');
        return [
            'corporate_id' => $employee->corporate_id,
            'employee_id' => $employee->employee_id,
            'hl1_id' => $employee->hl1_id ?? null,
            'user_id' => $employee->user_id,
            'employee_type_id' => $employee->employee_type_id ?? null,
            'corporate_contractors_id' => $employee->corporate_contractors_id ?? null,
            'hl1_name' => $hl1Name ?? '',
            'first_name' => $this->aes256DecryptData($masterUser->first_name),
            'last_name' => $this->aes256DecryptData($masterUser->last_name),
            'email' => $this->aes256DecryptData($masterUser->email),
            'mob_num' => $this->aes256DecryptData($masterUser->mob_num),
            'dob' => $this->aes256DecryptData($masterUser->dob),
            'gender' => $this->aes256DecryptData($masterUser->gender),
            'designation' => $employee->designation ?? null,
            'employee_type_name' => $employeeTypeName,
        ];
    }
    private function aes256EncryptDataWeak($corporate_id = null, $location_id = null, $keyword)
    {
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $results = collect();
        $employeeUserQuery = DB::table('employee_user_mapping')
            ->where([
                ['corporate_id', '=', $corporate_id],
                ['location_id', '=', $location_id],
            ]);
        $matchedEmployeeUsers = $employeeUserQuery
            ->where('employee_id', 'LIKE', '%' . $keyword . '%')
            ->get();
        $userIds = $matchedEmployeeUsers->pluck('user_id')->toArray();
        if (!empty($userIds)) {
            $masterUserMatches = DB::table('master_user')
                ->whereIn('user_id', $userIds)
                ->get();
            foreach ($masterUserMatches as $masterUser) {
                $employee = $matchedEmployeeUsers->where('user_id', $masterUser->user_id)->first();
                if ($employee) {
                    $formattedResult = $this->formatEmployeeRow($employee, $masterUser);
                    $results->push($formattedResult);
                }
            }
        }
        $fields = [
            'first_name_hash',
            'last_name_hash',
            'mobile_hash'
        ];
        $masterUserQuery = DB::table('master_user');
        foreach ($fields as $field) {
            $matches = $masterUserQuery
                ->selectRaw(
                    "AES_DECRYPT(UNHEX({$field}), ?) AS decrypted_value, master_user.*",
                    [$key]
                )
                ->having('decrypted_value', 'LIKE', '%' . $keyword . '%')
                ->get();
            foreach ($matches as $masterUser) {
                $employee = DB::table('employee_user_mapping')
                    ->where('user_id', $masterUser->user_id)
                    ->first();
                if ($employee) {
                    $formattedResult = $this->formatEmployeeRow($employee, $masterUser);
                    $results->push($formattedResult);
                }
            }
        }
        return $results->unique('user_id');
    }
    public function searchEmployeeDataByKeyword($corporate_id, $location_id, $keyword)
    {
        if (empty($corporate_id) || empty($location_id) || empty($keyword)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if (!ctype_alnum($corporate_id) || !ctype_alnum($location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if (!ctype_alnum($keyword)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request.'], 400);
        }
        if (strlen($keyword) < 3) {
            return response()->json(['result' => false, 'message' => 'Keyword length shld be minimum of 3 charecters.'], 400);
        }
        $employees = $this->aes256EncryptDataWeak($corporate_id, $location_id, $keyword);
        if ($employees->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'No matching employee data found'], 404);
        }
        return response()->json(['result' => true, 'message' => $employees]);
    }
    public function getAllEmployees($corporate_id, $location_id, Request $request)
    {
        $validatedData = $request->validate([
            'department' => 'nullable|array',
            'department.*' => 'string|max:255',
            'designation' => 'nullable|array',
            'designation.*' => 'string|max:255',
            'employee_type_id' => 'nullable|array',
            'employee_type_id.*' => 'string|max:255',
        ]);
        if (!ctype_alnum($corporate_id) || !ctype_alnum($location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request.'], 400);
        }
        $employeesQuery = EmployeeUserMapping::where('corporate_id', $corporate_id)
            ->where('location_id', $location_id)
            ->with('masterUser');
        $filterMapping = [
            'department' => 'hl1_id',
            'designation' => 'designation',
            'employee_type_id' => 'employee_type_id',
        ];
        foreach ($filterMapping as $requestKey => $dbField) {
            if (!empty($validatedData[$requestKey])) {
                $employeesQuery->whereIn($dbField, $validatedData[$requestKey]);
            }
        }
        $employees = $employeesQuery->limit(1000)->get();
        if ($employees->isEmpty()) {
            return response()->json(['result' => true, 'message' => 'No employee data found'], 422);
        }
        $formattedEmployees = $employees->map(function ($employee) {
            $formattedEmployee = $this->formatEmployeeRow($employee, $masterUser = null);
            $formattedEmployee['employee_user_mapping_id'] = $employee->id;
            return $formattedEmployee;
        });
        return response()->json(['result' => true, 'message' => $formattedEmployees]);
    }
    public function getDepartmentsHL1($corporate_id, $location_id)
    {
        if (!$corporate_id || !$location_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        $this->corporate_id = $corporate_id;
        $this->location_id = $location_id;
        try {
            $data = CorporateHl1::where('corporate_id', $this->corporate_id)
                ->where('location_id', $this->location_id)
                ->select('hl1_id', 'hl1_name')
                ->get();
            if ($data->isEmpty()) {
                return response()->json([
                    "result" => false,
                    "message" => "No data found for the provided Corporate and Location ID"
                ]);
            }
            $hl1Departments = $data->map(function ($item) {
                return [
                    'hl1_id' => $item->hl1_id,
                    'hl1_name' => $item->hl1_name,
                ];
            });
            return response()->json([
                "result" => true,
                "data" => $hl1Departments
            ]);
        } catch (Exception $e) {
            return response()->json([
                "result" => false,
                "message" => "An error occurred while fetching data, " . $e->getMessage(),
            ]);
        }
    }
    public function getDesignations($corporate_id, $location_id)
    {
        try {
            if (!$corporate_id || !$location_id) {
                return response()->json([
                    "result" => false,
                    "message" => "Invalid Request"
                ]);
            }
            $designations = EmployeeUserMapping::where("corporate_id", $corporate_id)
                ->where("location_id", $location_id)
                ->pluck('designation')
                ->unique()
                ->values();
            return response()->json([
                "result" => true,
                "data" => $designations
            ]);
        } catch (Exception $e) {
            return response()->json([
                "result" => false,
                "message" => "An error occurred while fetching data"
            ]);
        }
    }
    public function getEmployeeType($corporate_id)
    {
        if (!$corporate_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Corporate ID"
            ]);
        }
        $this->corporate_id = $corporate_id;
        try {
            $data = EmployeeType::where('corporate_id', $this->corporate_id)
                ->get(['employee_type_id', 'employee_type_name', 'checked']);
            if ($data->isEmpty()) {
                return response()->json([
                    "result" => false,
                    "message" => "No data found for the provided Corporate ID"
                ]);
            }
            $employeeTypes = $data->map(function ($item) {
                return [
                    'employee_type_id' => $item->employee_type_id,
                    'employee_type_name' => $item->employee_type_name,
                    'checked' => $item->checked,
                ];
            });
            return response()->json([
                "result" => true,
                "data" => $employeeTypes
            ]);
        } catch (Exception $e) {
            return response()->json([
                "result" => false,
                "message" => "An error occurred while fetching data"
            ]);
        }
    }
    public function getContractors($location_id)
    {
        if (!$location_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Location ID"
            ]);
        }
        $this->location_id = $location_id;
        try {
            $data = corporate_contractors::where('location_id', $this->location_id)
                ->select('corporate_contractors_id', 'contractor_name')
                ->get();
            if ($data->isEmpty()) {
                return response()->json([
                    "result" => false,
                    "message" => "No data found for the provided Location ID"
                ]);
            }
            $contractors = $data->map(function ($contractor) {
                return [
                    'corporate_contractors_id' => $contractor->corporate_contractors_id,
                    'contractor_name' => $contractor->contractor_name,
                ];
            });
            return response()->json([
                "result" => true,
                "data" => $contractors
            ]);
        } catch (Exception $e) {
            return response()->json([
                "result" => false,
                "message" => "An error occurred while fetching data"
            ]);
        }
    }
    public function getDoctors($corporate_id, $location_id)
    {
        if (!$corporate_id || !$location_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        return response()->json([
            "result" => true,
            "data" => [
                ["doctor_id" => 1, "doctor_name" => "doctor 1"],
                ["doctor_id" => 2, "doctor_name" => "doctor 2"]
            ]
        ]);
    }
    public function getFavourite($corporate_id, $location_id)
    {
        if (!$corporate_id || !$location_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        return response()->json([
            "result" => true,
            "data" => [
                ["favourite_id" => 1, "favourite_name" => "favourite 1"],
                ["favourite_id" => 2, "favourite_name" => "favourite 2"]
            ]
        ]);
    }
    public function getLabs($corporate_id, $location_id)
    {
        if (!$corporate_id || !$location_id) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        return response()->json([
            "result" => true,
            "data" => [
                ["lab_id" => 1, "lab_name" => "lab 1"],
                ["lab_id" => 2, "lab_name" => "lab 2"]
            ]
        ]);
    }
    public function assignHealthPlan($corporate_id = null, $location_id = null, Request $request)
    {
        if ($corporate_id === null || $location_id === null) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        if (!ctype_alnum($corporate_id) || !ctype_alnum($location_id)) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        $validatedData = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'string|max:255',
            'healthplan_id' => 'required|integer',
            'assign_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assign_date',
            'doctor_id' => 'nullable|integer',
            'favourite_id' => 'nullable|integer',
        ]);
        $userIds = $validatedData['user_ids'];
        $healthplanId = $validatedData['healthplan_id'];
        $healthplanExists = CorporateHealthplan::where('corporate_healthplan_id', $healthplanId)->first();
        $masterTestIds = json_decode($healthplanExists->master_test_id, true) ?? [];
        if (!$healthplanExists) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Healthplan ID"
            ]);
        }
        // TODO: to implement healthplan certification table using for loop
        $certificate_id = $healthplanExists->certificate_id;
        $last_generate_test_request_id = HealthplanAssigned::max('generate_test_request_id');
        $new_generate_test_request_id = $last_generate_test_request_id + 1;
        if ($userIds[0] === 'all') {
            $existingUserIds = EmployeeUserMapping::where('corporate_id', $corporate_id)
                ->where('corporate_id', $corporate_id)
                ->where('location_id', $location_id)
                ->pluck('user_id')
                ->toArray();
        } else {
            $existingUserIds = EmployeeUserMapping::whereIn('user_id', $userIds)->pluck('user_id')->toArray();
            $missingUserIds = array_diff($userIds, $existingUserIds);
        }
        if (!empty($missingUserIds ?? [])) {
            return response()->json([
                "result" => false,
                "message" => "Some user IDs are invalid",
                "missing_user_ids" => array_values($missingUserIds)
            ]);
        }
        $lastTestCode = PrescribedTest::max('test_code');
        $lastTestNumber = $lastTestCode ? (int) $lastTestCode : 0;
        try {
            DB::beginTransaction();
            $insertedRecords = [];
            foreach ($existingUserIds as $userId) {
                $lastTestNumber++;
                $testCode = $lastTestNumber;
                $healthplanAssigned = HealthplanAssigned::create([
                    'master_lab_id' => 0,
                    'test_code' => $testCode,
                    'user_id' => $userId,
                    'lab_healthplan' => null,
                    'corporate_location_id' => $location_id,
                    'corporate_healthplan_id' => $healthplanId,
                    'generate_test_request_id' => $new_generate_test_request_id,
                    'visit_status' => "Walkin",
                    'pre_emp_user_id' => null,
                    'next_assess_date' => now(),
                    'created_on' => now(),
                    'created_by' => auth('api')->user()->id ?? 'system',
                ]);
                PrescribedTest::create([
                    'test_code' => $testCode,
                    'prescription_id' => null,
                    'location_id' => $location_id,
                    'isAssignedHealthplan' => 1,
                    'case_id' => null,
                    'user_id' => $userId,
                    'doctor_id' => $validatedData['doctor_id'] ?? 0,
                    'hosp_id' => 0,
                    'lab_id' => 0,
                    'op_registry_id' => 0,
                    'fromOp' => 0,
                    'corporate_ohc_id' => 0,
                    'corporate_id' => $corporate_id,
                    'preemp_user_id' => 0,
                    'test_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $validatedData['assign_date'])->format('Y-m-d'),
                    'test_due_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $validatedData['due_date'])->format('Y-m-d'),
                    'test_modified' => null,
                    'favourite_lab' => $validatedData['favourite_id'] ?? null,
                    'created_on' => now(),
                    'created_by' => auth('api')->user()->id ?? 'system',
                    'file_name' => null,
                ]);
                foreach ($masterTestIds as $masterTestId) {
                    PrescribedTestData::create([
                        'test_code' => $testCode,
                        'master_test_id' => $masterTestId,
                        'test_results' => null,
                        'test_condition' => null,
                        'fromOp' => 0,
                        'created_on' => now(),
                        'created_by' => auth('api')->user()->id ?? 'system',
                    ]);
                }
                HealthplanAssignedStatus::create([
                    'test_code' => $testCode,
                    'pending' => now(),
                    'inserted_on' => now(),
                    'inserted_by' => auth('api')->user()->id ?? 'system',
                ]);
                $insertedRecords[] = $testCode;
            }
            DB::commit();
            return response()->json([
                "result" => true,
                "message" => "Health plan assigned successfully",
                "inserted_test_codes" => $insertedRecords
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "result" => false,
                "message" => "Error occurred: " . $e->getMessage()
            ]);
        }
    }
    public function getAllAssignedHealthPlan($corporate_id = null, $location_id = null, Request $request)
    {
        if (auth('api')->user() && auth('api')->user()->getTable() == 'corporate_admin_user') {
            return $this->getAllAssignedHealthPlanForCorporate($corporate_id, $location_id, $request);
        } elseif (auth('employee_api')->user() && auth('employee_api')->user()->getTable() == 'master_user') {
            return $this->getAllAssignedHealthPlanForEmployee($corporate_id, $location_id, $request);
        } else {
            return response()->json(['result' => false, 'message' => 'Unauthorized user type'], 403);
        }
    }
    public function getAllAssignedHealthPlanForCorporate($corporate_id, $location_id, Request $request)
    {
        if ($corporate_id === null || $location_id === null) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        if (!ctype_alnum($corporate_id) || !ctype_alnum($location_id)) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        try {
            // TODO: To Extract the doctor name after doctor module is done
            $results = DB::table('healthplan_assigned as ha')
                ->join('prescribed_test as pt', 'ha.test_code', '=', 'pt.test_code')
                ->join('employee_user_mapping as eum', 'ha.user_id', '=', 'eum.user_id')
                ->leftJoin('corporate_hl1 as ch', 'eum.hl1_id', '=', 'ch.hl1_id')
                ->leftJoin('master_corporate as mc', function ($join) {
                    $join->on('eum.corporate_id', '=', 'mc.corporate_id')
                        ->on('eum.location_id', '=', 'mc.location_id');
                })
                ->leftJoin('master_user as mu', 'ha.user_id', '=', 'mu.user_id')
                ->leftJoin('corporate_healthplan as chp', 'ha.corporate_healthplan_id', '=', 'chp.corporate_healthplan_id')
                ->leftJoin('employee_type as et', 'eum.employee_type_id', '=', 'et.employee_type_id')
                ->where('ha.corporate_location_id', $location_id)
                ->where('eum.corporate_id', $corporate_id)
                ->where('eum.location_id', $location_id)
                ->where('pt.isAssignedHealthplan', 1)
                ->select([
                    'ha.id',
                    'ha.test_code',
                    'ha.user_id',
                    'pt.id as test_id',
                    'pt.test_date',
                    'pt.test_due_date',
                    'eum.employee_id',
                    'eum.designation',
                    'ch.hl1_name',
                    'mc.display_name',
                    'mu.first_name',
                    'mu.last_name',
                    'chp.healthplan_title',
                    'chp.corporate_healthplan_id',
                    'et.employee_type_name'
                ])
                ->orderBy('ha.created_on', 'desc')
                ->get();
            foreach ($results as $item) {
                $item->first_name = $this->aes256DecryptData($item->first_name);
                $item->last_name = $this->aes256DecryptData($item->last_name);
                $statusRecord = DB::table('healthplan_assigned_status')
                    ->where('test_code', $item->test_code)
                    ->first();
                if ($statusRecord) {
                    foreach ($statusRecord as $key => $value) {
                        if (property_exists($item, $key) && $key !== 'id' && $key !== 'test_code') {
                            $item->{'status_' . $key} = $value;
                        } else {
                            $item->$key = $value;
                        }
                    }
                } else {
                    $item->inserted_on = null;
                    $item->inserted_by = null;
                    $item->pending = null;
                    $item->schedule = null;
                    $item->in_process = null;
                    $item->test_completed = null;
                    $item->result_ready = null;
                    $item->no_show = null;
                    $item->certified = null;
                    $item->cancelled = null;
                }
                $item->doctor_name = 'Dr. John Doe';
                $item->diagnosis_center = 'Sunrise Diagnostics';
                $certificateIdsJson = DB::table('corporate_healthplan')
                    ->where('corporate_healthplan_id', $item->corporate_healthplan_id)
                    ->value('certificate_id');
                $firstDecode = json_decode($certificateIdsJson, true);
                $certificateIds = is_string($firstDecode)
                    ? json_decode($firstDecode, true)
                    : $firstDecode;
                if (!is_array($certificateIds)) {
                    $certificateIds = [];
                }
                $certificateIds = array_map('intval', $certificateIds);
                $certifications = DB::table('certification')
                    ->whereIn('certificate_id', $certificateIds)
                    ->select('certificate_id', 'certification_title', 'color_condition', 'condition')
                    ->get()
                    ->map(function ($cert) use ($item) {
                        $hpCert = DB::table('healthplan_certification')
                            ->where('test_code', $item->test_code)
                            ->where('certification_id', $cert->certificate_id)
                            ->select('certified_on', 'next_assessment_date', 'condition', 'color_condition', 'inserted_on', 'remarks')
                            ->first();
                        return [
                            'certificate_id' => $cert->certificate_id,
                            'certification_title' => $cert->certification_title,
                            'condition' => $cert->condition,
                            'color_condition' => json_decode($cert->color_condition, true) ?: null,
                            'healthplan_certification' => [
                                'certified_on' => $hpCert?->certified_on,
                                'next_assessment_date' => $hpCert?->next_assessment_date,
                                'condition' => $hpCert?->condition ?? null,
                                'color_condition' => $hpCert?->color_condition ?? null,
                                'inserted_on' => $hpCert?->inserted_on,
                                'remarks' => $hpCert?->remarks,
                            ]
                        ];
                    })->toArray();
                $item->certifications = $certifications;
            }
            return response()->json([
                "result" => true,
                "message" => "Health plans retrieved successfully",
                "data" => $results
            ]);
        } catch (Exception $e) {
            return response()->json([
                "result" => false,
                "message" => "Error occurred while retrieving health plans: " . $e->getMessage()
            ]);
        }
    }
    public function getAllAssignedHealthPlanForEmployee($corporate_id = null, $location_id = null, Request $request)
    {
        if ($corporate_id === null || $location_id === null) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        if (!ctype_alnum($corporate_id) || !ctype_alnum($location_id)) {
            return response()->json([
                "result" => false,
                "message" => "Invalid Request"
            ]);
        }
        try {
            // TODO: To Extract the doctor name after doctor module is done
            $results = DB::table('healthplan_assigned as ha')
                ->join('prescribed_test as pt', 'ha.test_code', '=', 'pt.test_code')
                ->join('employee_user_mapping as eum', 'ha.user_id', '=', 'eum.user_id')
                ->leftJoin('corporate_hl1 as ch', 'eum.hl1_id', '=', 'ch.hl1_id')
                ->leftJoin('master_corporate as mc', function ($join) {
                    $join->on('eum.corporate_id', '=', 'mc.corporate_id')
                        ->on('eum.location_id', '=', 'mc.location_id');
                })
                ->leftJoin('master_user as mu', 'ha.user_id', '=', 'mu.user_id')
                ->leftJoin('corporate_healthplan as chp', 'ha.corporate_healthplan_id', '=', 'chp.corporate_healthplan_id')
                ->leftJoin('employee_type as et', 'eum.employee_type_id', '=', 'et.employee_type_id')
                ->where('ha.corporate_location_id', $location_id)
                ->where('eum.corporate_id', $corporate_id)
                ->where('eum.location_id', $location_id)
                ->where('pt.isAssignedHealthplan', 1)
                ->when($request->employeeid, function ($query, $employeeId) {
                    return $query->where('eum.employee_id', $employeeId);
                })
                ->select([
                    'ha.id',
                    'ha.test_code',
                    'ha.user_id',
                    'pt.id as test_id',
                    'pt.test_date',
                    'pt.test_due_date',
                    'eum.employee_id',
                    'eum.designation',
                    'ch.hl1_name',
                    'mc.display_name',
                    'mu.first_name',
                    'mu.last_name',
                    'chp.healthplan_title',
                    'chp.corporate_healthplan_id',
                    'et.employee_type_name'
                ])
                ->orderBy('ha.created_on', 'desc')
                ->get();
            foreach ($results as $item) {
                $item->first_name = $this->aes256DecryptData($item->first_name);
                $item->last_name = $this->aes256DecryptData($item->last_name);
                $statusRecord = DB::table('healthplan_assigned_status')
                    ->where('test_code', $item->test_code)
                    ->first();
                if ($statusRecord) {
                    foreach ($statusRecord as $key => $value) {
                        if (property_exists($item, $key) && $key !== 'id' && $key !== 'test_code') {
                            $item->{'status_' . $key} = $value;
                        } else {
                            $item->$key = $value;
                        }
                    }
                } else {
                    $item->inserted_on = null;
                    $item->inserted_by = null;
                    $item->pending = null;
                    $item->schedule = null;
                    $item->in_process = null;
                    $item->test_completed = null;
                    $item->result_ready = null;
                    $item->no_show = null;
                    $item->certified = null;
                    $item->cancelled = null;
                }
                $item->doctor_name = 'Dr. John Doe';
                $item->diagnosis_center = 'Sunrise Diagnostics';
                $certificateIdsJson = DB::table('corporate_healthplan')
                    ->where('corporate_healthplan_id', $item->corporate_healthplan_id)
                    ->value('certificate_id');
                $firstDecode = json_decode($certificateIdsJson, true);
                $certificateIds = is_string($firstDecode)
                    ? json_decode($firstDecode, true)
                    : $firstDecode;
                if (!is_array($certificateIds)) {
                    $certificateIds = [];
                }
                $certificateIds = array_map('intval', $certificateIds);
                $certifications = DB::table('certification')
                    ->whereIn('certificate_id', $certificateIds)
                    ->select('certificate_id', 'certification_title', 'color_condition', 'condition')
                    ->get()
                    ->map(function ($cert) use ($item) {
                        $hpCert = DB::table('healthplan_certification')
                            ->where('test_code', $item->test_code)
                            ->where('certification_id', $cert->certificate_id)
                            ->select('certified_on', 'next_assessment_date', 'condition', 'color_condition', 'inserted_on', 'remarks')
                            ->first();
                        return [
                            'certificate_id' => $cert->certificate_id,
                            'certification_title' => $cert->certification_title,
                            'condition' => $cert->condition,
                            'color_condition' => json_decode($cert->color_condition, true) ?: null,
                            'healthplan_certification' => [
                                'certified_on' => $hpCert?->certified_on,
                                'next_assessment_date' => $hpCert?->next_assessment_date,
                                'condition' => $hpCert?->condition ?? null,
                                'color_condition' => $hpCert?->color_condition ?? null,
                                'inserted_on' => $hpCert?->inserted_on,
                                'remarks' => $hpCert?->remarks,
                            ]
                        ];
                    })->toArray();
                $item->certifications = $certifications;
            }
            return response()->json([
                "result" => true,
                "message" => "Health plans retrieved successfully",
                "data" => $results
            ]);
        } catch (Exception $e) {
            return response()->json([
                "result" => false,
                "message" => "Error occurred while retrieving health plans: " . $e->getMessage()
            ]);
        }
    }
    private function getHealthParameters($healthParameters)
    {
        if (!$healthParameters) {
            return [];
        }
        return [
            'Allergic Foods' => FoodAllergy::whereIn('id', $healthParameters->allergic_food ?? [])
                ->pluck('food_name')
                ->toArray(),
            'Allergic Ingredients' => DrugIngredient::whereIn('id', $healthParameters->allergic_ingredients ?? [])
                ->pluck('drug_ingredients')
                ->toArray(),
            'Published Conditions' => Outpatient::whereIn('op_component_id', $healthParameters->published_conditions ?? [])
                ->where('op_component_type', 8)
                ->pluck('op_component_name')
                ->toArray(),
            'Unpublished Conditions' => $healthParameters->unpublished_conditions ?? [],
        ];
    }
    public function checkEmployeeId($isFollowUp = null, $employee_id = null)
    {
        $route = request()->route();
        $op_registry_id = $route->parameter('op_registry_id') ?? null;
        $prescription_id = $route->parameter('prescription_id') ?? null;
        $isFollowUp = $isFollowUp === '1' ? 1 : ($isFollowUp === '0' ? 0 : null);
        if ($isFollowUp !== null && !in_array($isFollowUp, [0, 1])) {
            return "Invalid Request";
        }
        if (!$employee_id || !ctype_alnum($employee_id)) {
            return "Invalid Request";
        }
        if ($op_registry_id && !is_numeric($op_registry_id)) {
            return "Invalid Request";
        }
        $employee = EmployeeUserMapping::where('employee_id', $employee_id)->first();
        if (!$employee) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $followUpCount = 0;
        $followUpCount = OpRegistry::where('op_registry_id', $op_registry_id)
            ->whereNotNull('followup_count')
            ->max('followup_count');
        $userId = $employee->user_id;
        $corporateId = $employee->corporate_id;
        $locationId = $employee->location_id;
        $masterUser = $employee->masterUser;
        $basicInfo = [
            'employee_id' => $employee->employee_id,
            'employee_firstname' => $this->aes256DecryptData($masterUser->first_name),
            'employee_lastname' => $this->aes256DecryptData($masterUser->last_name),
            'employee_gender' => $this->aes256DecryptData($masterUser->gender),
            'employee_age' => date_diff(date_create($this->aes256DecryptData($masterUser->dob)), date_create('now'))->y,
            'employee_designation' => $employee->designation,
            'employee_corporate_id' => $corporateId,
            'employee_location_id' => $locationId,
        ];
        $corporate = MasterCorporate::where('corporate_id', $corporateId)
            ->select('corporate_name', 'display_name')
            ->first();
        $basicInfo['employee_corporate_name'] = $corporate->corporate_name ?? null;
        $basicInfo['employee_location_name'] = $corporate->display_name ?? null;
        $basicInfo['employee_department'] = CorporateHl1::where('hl1_id', $employee->hl1_id)
            ->value('hl1_name');
        $opRegistryQuery = OpRegistry::where('corporate_id', $corporateId)
            ->where('location_id', $locationId)
            ->where('master_user_id', $userId);
        ($op_registry_id ? $opRegistryQuery->where('op_registry_id', $op_registry_id) : null);
        $opRegistryOpenStatus = (clone $opRegistryQuery)->value('open_status');
        $basicInfo['employee_is_outpatient_added'] = $opRegistryOpenStatus !== null ? 1 : 0;
        $basicInfo['employee_is_outpatient_open'] = ($opRegistryOpenStatus !== null && $opRegistryOpenStatus != 0) ? 1 : 0;
        $opRegistry = null;
        if ($op_registry_id) {
            $opRegistry = (clone $opRegistryQuery)->where('op_registry_id', $op_registry_id)->first();
            if (!$opRegistry) {
                return "Invalid Request";
            }
            $showWhiteStrip = true;
        } elseif ($prescription_id) {
            $prescriptions = Prescription::where('prescription_id', $prescription_id)->exists();
            if (!$prescriptions) {
                return "Invalid Request";
            }
            $basicInfo['employee_is_outpatient_open'] = -1;
            $showWhiteStrip = false;
        } else {
            $opRegistry = (clone $opRegistryQuery)->first();
            $basicInfo['employee_is_outpatient_open'] = -1;
            $showWhiteStrip = false;
        }
        $basicInfo['incidentTypeColorCodes'] = OhcComponents::with([
            'incidentType:incident_type_id,incident_type_name'
        ])
            ->where('corporate_id', $corporateId)
            ->get(['incident_type_id', 'injury_color_types'])
            ->map(function ($item) {
                return [
                    'incident_type_id' => $item->incident_type_id,
                    'incident_type_name' => optional($item->incidentType)->incident_type_name,
                    'injury_color_types' => is_string($item->injury_color_types)
                        ? json_decode($item->injury_color_types, true)
                        : $item->injury_color_types,
                ];
            })
            ->toArray();
        // $basicInfo['incidentTypeColorCodes'] = OhcComponents::where('corporate_id', $corporateId)
        //         ->get(['incident_type_id', 'injury_color_types'])
        //         ->toArray();
        $basicInfo['incidentTypeColorCodesAdded'] = $opRegistry->injury_color_text ?? null;
        $basicInfo['showWhiteStrip'] = $showWhiteStrip;
        $healthParameters = HealthParameters::where('user_id', $userId)->first();
        $basicInfo['healthParameters'] = $healthParameters ? $this->getHealthParameters($healthParameters) : 0;
        $op_registry_datas = null;
        $opOutsideReferral = null;
        $isPrescriptionAdded = 0;
        $isTestAdded = 0;
        if ($opRegistry && $op_registry_id) {
            $registryId = $opRegistry->op_registry_id;
            $opRegistryTimes = OpRegistryTimes::where('op_registry_id', $registryId)->first();
            $opOutsideReferral = OpOutsideReferral::where('op_registry_id', $registryId)->first();
            $isPrescriptionAdded = Prescription::where('op_registry_id', $registryId)
                ->where('corporate_ohc_id', $opRegistry->corporate_ohc_id)
                ->exists() ? 1 : 0;
            $isTestAdded = PrescribedTest::where('corporate_id', $corporateId)
                ->where('isVp', 0)
                ->where('user_id', $userId)
                ->where('op_registry_id', $registryId)
                ->where('fromOp', 1)
                ->exists() ? 1 : 0;
            $prescribedTest = PrescribedTest::where('corporate_id', $corporateId)
                ->where('isVp', 1)
                ->where('user_id', $userId)
                ->where('op_registry_id', $registryId)
                ->where('fromOp', 1)
                ->first();
            $prescribedTestData = $prescribedTest
                ? PrescribedTestData::where('test_code', $prescribedTest->test_code)->where('fromOp', 1)->get()
                : collect();
            $componentTypes = [
                'body_part' => 'body_parts',
                'symptoms' => 'symptoms',
                'diagnosis' => 'diagnosis',
                'medical_system' => 'medical_systems',
                'nature_injury' => 'nature_injuries',
                'mechanism_injury' => 'mechanism_injuries'
            ];
            $componentNames = [];
            foreach ($componentTypes as $dbField => $outputField) {
                $ids = json_decode($opRegistry->$dbField, true) ?? [];
                $componentNames[$outputField] = Outpatient::whereIn('op_component_id', $ids)
                    ->pluck('op_component_name');
            }
            $op_registry_datas = array_merge(
                [
                    'op_registry' => $opRegistry,
                    'op_registry_times' => $opRegistryTimes,
                    'prescribed_test' => $prescribedTest,
                    'prescribed_test_data' => $prescribedTestData,
                ],
                $componentNames
            );
        }
        $basicInfo['op_registry_datas'] = $op_registry_datas;
        $basicInfo['op_outside_referral'] = $opOutsideReferral;
        $basicInfo['isPrescriptionAdded'] = $isPrescriptionAdded;
        $basicInfo['isTestAdded'] = $isTestAdded;
        $basicInfo['followUpCount'] = $followUpCount;
        return response()->json(['result' => true, 'message' => $basicInfo]);
    }
    public function getAllSymptoms()
    {
        $allSymptoms = Outpatient::where('op_component_type', 6)
            ->select('op_component_id', 'op_component_name')
            ->distinct()
            ->get()
            ->toArray();
        return response()->json(['result' => true, 'message' => $allSymptoms]);
    }
    public function getAllBodyParts()
    {
        $allBodyParts = Outpatient::where('op_component_type', 5)
            ->select('op_component_id', 'op_component_name')
            ->distinct()
            ->get()
            ->toArray();
        return response()->json(['result' => true, 'message' => $allBodyParts]);
    }
    public function getAllDiagnosis()
    {
        $allDiagnosis = Outpatient::where('op_component_type', 8)
            ->select('op_component_id', 'op_component_name')
            ->distinct()
            ->get()
            ->toArray();
        return response()->json(['result' => true, 'message' => $allDiagnosis]);
    }
    public function getAllMedicalSystem()
    {
        $allMedicalSystems = Outpatient::where('op_component_type', 7)
            ->select('op_component_id', 'op_component_name')
            ->distinct()
            ->get()
            ->toArray();
        return response()->json(['result' => true, 'message' => $allMedicalSystems]);
    }
    public function getAllNatureOfInjury()
    {
        $allNatureOfInjury = Outpatient::where('op_component_type', 3)
            ->select('op_component_id', 'op_component_name')
            ->distinct()
            ->get()
            ->toArray();
        return response()->json(['result' => true, 'message' => $allNatureOfInjury]);
    }
    public function getAllInjuryMechanism()
    {
        $allInjuryMechanism = Outpatient::where('op_component_type', 4)
            ->select('op_component_id', 'op_component_name')
            ->distinct()
            ->get()
            ->toArray();
        return response()->json(['result' => true, 'message' => $allInjuryMechanism]);
    }
    public function getMRNumber()
    {
        $mrNumber = OpOutsideReferral::max('mr_number') ? OpOutsideReferral::max('mr_number') + 1 : 1;
        return response()->json(['result' => true, 'message' => $mrNumber]);
    }
    public function addHealthRegistry(Request $request)
    {
        DB::beginTransaction();
        try {
            $initialOpRegistryIdForFollowUp = $request->input('opRegistryId') ?? 0;
            $validatedData = $this->validateHealthRegistry($request);
            if ($validatedData['isFollowup'] === 1) {
                $openStatusOfMain = OpRegistry::where('op_registry_id', $initialOpRegistryIdForFollowUp)->where('open_status', '1')->exists();
                if ($openStatusOfMain) {
                    return response()->json([
                        'result' => false,
                        'message' => 'This main registry is already open. Please close it before proceeding with the follow-up.'
                    ], 400);
                }
                $openStatusOfFollowUp = OpRegistry::where('follow_up_op_registry_id', $initialOpRegistryIdForFollowUp)->where('open_status', '1')->exists();
                if ($openStatusOfFollowUp) {
                    return response()->json([
                        'result' => false,
                        'message' => 'The follow-up registry is already open for this main registry. Please close it before proceeding with the new follow-up.'
                    ], 400);
                }
            }
            $employeeInfo = $this->getEmployeeInfo($validatedData);
            if (!$employeeInfo['isValid']) {
                return $employeeInfo['response'];
            }
            $registryInfo = $this->getOrCreateRegistryRecord(
                $validatedData,
                $employeeInfo['userId'],
                $initialOpRegistryIdForFollowUp
            );
            if (!$registryInfo['isValid']) {
                return $registryInfo['response'];
            }
            $opRegistryId = $registryInfo['opRegistryId'];
            $isUpdate = $registryInfo['isUpdate'];
            $componentsValidation = $this->validateMedicalComponents($validatedData);
            if (!$componentsValidation['isValid']) {
                return $componentsValidation['response'];
            }
            $this->saveOrUpdateRegistry(
                $validatedData,
                $opRegistryId,
                $employeeInfo,
                $isUpdate,
                $initialOpRegistryIdForFollowUp,
            );
            $this->savePrescribedTests(
                $validatedData,
                $opRegistryId,
                $employeeInfo['userId'],
                $employeeInfo['corporateId'],
                $employeeInfo['locationId']
            );
            $this->saveRegistryTimes($validatedData, $opRegistryId);
            if ($validatedData['referral'] === 'OutsideReferral') {
                $this->handleOutsideReferral($validatedData, $opRegistryId);
            }
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => $isUpdate
                    ? 'Successfully updated the health registry.'
                    : 'Successfully saved the health registry.',
                'op_registry_id' => $opRegistryId,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->handleException($e);
        }
    }
    private function validateHealthRegistry(Request $request): array
    {
        $baseRules = $this->getBaseValidationRules();
        $incidentTypeRules = $this->getIncidentTypeRules($request->incidentType);
        $referralRules = $this->getReferralRules($request);
        $rules = array_merge($baseRules, $incidentTypeRules, $referralRules);
        return $request->validate($rules);
    }
    private function getBaseValidationRules(): array
    {
        return [
            'corporateId' => 'required|string',
            'locationId' => 'required|string',
            'opRegistryId' => 'nullable|integer',
            'employeeId' => 'required|string',
            'close' => 'required|boolean',
            'ohcId' => 'required|integer',
            'editExistingOne' => 'required|boolean',
            'isFollowup' => 'required|boolean',
            'vitalParameters' => 'required|array',
            'firstAidBy' => 'required|string',
            'workShift' => 'required|integer|in:1,2,3,4',
            'movementSlip' => 'required|boolean',
            'referral' => 'required|string|in:noOutsideReferral,OutsideReferral,ama',
            'physiotherapy' => 'required|boolean',
            'fitnessCert' => 'required|boolean',
            'reportingDateTime' => 'required|date',
            'incidentDateTime' => 'required|date',
            'vitalParameters.vpTemperature_167' => 'nullable|string',
            'vitalParameters.vpSystolic_168' => 'nullable|string',
            'vitalParameters.vpDiastolic_169' => 'nullable|string',
            'vitalParameters.vpPulseRate_170' => 'nullable|string',
            'vitalParameters.vpRespiratory_171' => 'nullable|string',
            'vitalParameters.vpSPO2_172' => 'nullable|string',
            'vitalParameters.vpRandomGlucose_173' => 'nullable|string',
            'incidentType' => 'required|string|in:industrialAccident,medicalIllness,outsideAccident',
            'incidentTypeId' => 'required|integer',
            'doctor' => 'sometimes|array',
            'doctor.doctorId' => 'nullable|string',
            'doctor.doctorName' => 'nullable|string|not_in:Select Doctor',
            'observations' => 'required|array',
            'observations.doctorNotes' => 'nullable|string',
            'observations.medicalHistory' => 'nullable|string',
            'observations.referral' => 'nullable|string',
            'observations.movementSlip' => 'nullable|boolean',
            'observations.fitnessCert' => 'nullable|boolean',
            'observations.physiotherapy' => 'nullable|boolean',
            'lostHours' => 'required|array',
            'lostHours.leaveFrom' => 'required|date',
            'lostHours.leaveUpto' => 'required|date',
            'lostHours.lostHours' => 'required|string',
            'lostHours.outTime' => 'required|date',
        ];
    }
    private function getIncidentTypeRules(?string $incidentType): array
    {
        $rules = [];
        if ($incidentType === 'industrialAccident') {
            $rules = [
                'industrialFields' => 'required|array',
                'industrialFields.injuryColor' => 'required|string',
                'industrialFields.sideOfBody' => 'required|array',
                'industrialFields.sideOfBody.left' => 'required|boolean',
                'industrialFields.sideOfBody.right' => 'required|boolean',
                'industrialFields.siteOfInjury' => 'required|array',
                'industrialFields.siteOfInjury.shopFloor' => 'required|boolean',
                'industrialFields.siteOfInjury.nonShopFloor' => 'required|boolean',
                'industrialFields.natureOfInjury' => 'nullable|array',
                'industrialFields.natureOfInjury.*' => 'nullable|string',
                'industrialFields.bodyPartIA' => 'nullable|array',
                'industrialFields.bodyPartIA.*' => 'nullable|string',
                'industrialFields.injuryMechanism' => 'nullable|array',
                'industrialFields.injuryMechanism.*' => 'nullable|string',
                'industrialFields.description' => 'nullable|string',
            ];
        } elseif ($incidentType === 'outsideAccident') {
            $rules = [
                'industrialFields' => 'required|array',
                'industrialFields.injuryColor' => 'required|string',
                'industrialFields.sideOfBody' => 'required|array',
                'industrialFields.sideOfBody.left' => 'required|boolean',
                'industrialFields.sideOfBody.right' => 'required|boolean',
                'industrialFields.natureOfInjury' => 'nullable|array',
                'industrialFields.natureOfInjury.*' => 'nullable|string',
                'industrialFields.bodyPartIA' => 'nullable|array',
                'industrialFields.bodyPartIA.*' => 'nullable|string',
                'industrialFields.injuryMechanism' => 'nullable|array',
                'industrialFields.injuryMechanism.*' => 'nullable|string',
                'industrialFields.description' => 'nullable|string',
            ];
        } elseif ($incidentType === 'medicalIllness') {
            $rules = [
                'medicalFields' => 'required|array',
                'medicalFields.bodyPart' => 'nullable|array',
                'medicalFields.injuryColor' => 'required|string',
                'medicalFields.bodyPart.*' => 'string',
                'medicalFields.symptoms' => 'nullable|array',
                'medicalFields.symptoms.*' => 'string',
                'medicalFields.medicalSystem' => 'nullable|array',
                'medicalFields.medicalSystem.*' => 'string',
                'medicalFields.diagnosis' => 'nullable|array',
                'medicalFields.diagnosis.*' => 'string',
            ];
        }
        return $rules;
    }
    private function getReferralRules(Request $request): array
    {
        $rules = [];
        if ($request->referral === 'OutsideReferral') {
            $rules = [
                'hospitalDetails' => 'required|array',
                'hospitalDetails.hospitalName' => 'required|string',
                'hospitalDetails.vehicleType' => 'required|string|in:own,ambulance',
                'hospitalDetails.esiScheme' => 'required|boolean',
            ];
            if (isset($request->hospitalDetails['vehicleType']) && $request->hospitalDetails['vehicleType'] === 'ambulance') {
                $rules = array_merge($rules, [
                    'hospitalDetails.accompaniedBy' => 'required|string',
                    'hospitalDetails.ambulanceNumber' => 'required|string',
                    'hospitalDetails.driverName' => 'required|string',
                    'hospitalDetails.odometerIn' => 'required|string',
                    'hospitalDetails.odometerOut' => 'required|string',
                    'hospitalDetails.timeIn' => 'required|date_format:H:i',
                    'hospitalDetails.timeOut' => 'required|date_format:H:i',
                ]);
            }
        }
        return $rules;
    }
    private function getEmployeeInfo(array $validatedData): array
    {
        $employee = EmployeeUserMapping::where('corporate_id', $validatedData['corporateId'])
            ->where('employee_id', $validatedData['employeeId'])
            ->first();
        if (!$employee) {
            return [
                'isValid' => false,
                'response' => response()->json([
                    'result' => false,
                    'message' => 'Invalid Request'
                ], 404)
            ];
        }
        $masterUser = MasterUser::where('user_id', $employee->user_id)->first();
        if (!$masterUser) {
            return [
                'isValid' => false,
                'response' => response()->json([
                    'result' => false,
                    'message' => 'Invalid Request'
                ], 422)
            ];
        }
        return [
            'isValid' => true,
            'employee' => $employee,
            'masterUser' => $masterUser,
            'userId' => $employee->user_id,
            'corporateId' => $employee->corporate_id,
            'locationId' => $employee->location_id,
            'employeeId' => $employee->employee_id,
            'masterUserId' => $masterUser->user_id
        ];
    }
    private function getOrCreateRegistryRecord(
        array $validatedData,
        string $userId,
        ?int $opRegistryId
    ): array {
        $editExistingOne = $validatedData['editExistingOne'] ?? null;
        $isFollowup = $validatedData['isFollowup'] ?? null;
        $masterUserId = MasterUser::where('user_id', $userId)->value('user_id');
        if ($opRegistryId == null) {
            $existingRegistry = OpRegistry::where('master_user_id', $masterUserId)
                ->orderBy('created_date_time', 'desc')
                ->first();
        } else {
            $existingRegistry = OpRegistry::where('master_user_id', $masterUserId)
                ->where('op_registry_id', $opRegistryId)
                ->orderBy('created_date_time', 'desc')
                ->first();
        }
        if ($existingRegistry && $editExistingOne && $isFollowup == 0) {
            if ($existingRegistry['open_status'] == 0) {
                return [
                    'isValid' => false,
                    'response' => response()->json([
                        'result' => false,
                        'message' => 'Invalid Request'
                    ], 422)
                ];
            }
            $opRegistryId = $existingRegistry->op_registry_id;
            $isUpdate = true;
        } else {
            $opRegistryId = OpRegistry::max('op_registry_id');
            $opRegistryId = $opRegistryId ? $opRegistryId + 1 : 1;
            $isUpdate = false;
        }
        return [
            'isValid' => true,
            'opRegistryId' => $opRegistryId,
            'isUpdate' => $isUpdate
        ];
    }
    private function validateMedicalComponents(array $validatedData): array
    {
        if (!empty($validatedData['medicalFields']['bodyPart'] ?? [])) {
            foreach ($validatedData['medicalFields']['bodyPart'] as $bodyPart) {
                $bodyPartExists = Outpatient::where('op_component_id', $bodyPart)
                    ->where('op_component_type', 5)
                    ->exists();
                if (!$bodyPartExists) {
                    return [
                        'isValid' => false,
                        'response' => response()->json([
                            'result' => false,
                            'message' => 'Invalid Request'
                        ], 422)
                    ];
                }
            }
        }
        if (!empty($validatedData['medicalFields']['symptoms'] ?? [])) {
            foreach ($validatedData['medicalFields']['symptoms'] as $symptom) {
                $symptomExists = Outpatient::where('op_component_id', $symptom)
                    ->where('op_component_type', 6)
                    ->exists();
                if (!$symptomExists) {
                    return [
                        'isValid' => false,
                        'response' => response()->json([
                            'result' => false,
                            'message' => 'Invalid Request'
                        ], 422)
                    ];
                }
            }
        }
        if (!empty($validatedData['medicalFields']['medicalSystem'] ?? [])) {
            foreach ($validatedData['medicalFields']['medicalSystem'] as $medicalSystem) {
                $medicalSystemExists = Outpatient::where('op_component_id', $medicalSystem)
                    ->where('op_component_type', 7)
                    ->exists();
                if (!$medicalSystemExists) {
                    return [
                        'isValid' => false,
                        'response' => response()->json([
                            'result' => false,
                            'message' => 'Invalid Request'
                        ], 422)
                    ];
                }
            }
        }
        if (!empty($validatedData['medicalFields']['diagnosis'] ?? [])) {
            foreach ($validatedData['medicalFields']['diagnosis'] as $diagnosis) {
                $diagnosisExists = Outpatient::where('op_component_id', $diagnosis)
                    ->where('op_component_type', 8)
                    ->exists();
                if (!$diagnosisExists) {
                    return [
                        'isValid' => false,
                        'response' => response()->json([
                            'result' => false,
                            'message' => 'Invalid Request'
                        ], 422)
                    ];
                }
            }
        }
        if (!empty($validatedData['industrialFields']['injuryMechanism'] ?? [])) {
            foreach ($validatedData['industrialFields']['injuryMechanism'] as $injuryMechanism) {
                $injuryMechanismExists = Outpatient::where('op_component_id', $injuryMechanism)
                    ->where('op_component_type', 4)
                    ->exists();
                if (!$injuryMechanismExists) {
                    return [
                        'isValid' => false,
                        'response' => response()->json([
                            'result' => false,
                            'message' => 'Invalid Request'
                        ], 422)
                    ];
                }
            }
        }
        if (!empty($validatedData['industrialFields']['symptoms'] ?? [])) {
            foreach ($validatedData['industrialFields']['symptoms'] as $symptoms) {
                $natureOfInjuryExists = Outpatient::where('op_component_id', $symptoms)
                    ->where('op_component_type', 3)
                    ->exists();
                if (!$natureOfInjuryExists) {
                    return [
                        'isValid' => false,
                        'response' => response()->json([
                            'result' => false,
                            'message' => 'Invalid Request'
                        ], 422)
                    ];
                }
            }
        }
        return ['isValid' => true];
    }
    private function saveOrUpdateRegistry(
        array $validatedData,
        int $opRegistryId,
        array $employeeInfo,
        bool $isUpdate,
        int $initialOpRegistryIdForFollowUp
    ): bool {
        $ohcId = $validatedData['ohcId'];
        $editExistingOne = $validatedData['editExistingOne'] ?? null;
        $isFollowup = $validatedData['isFollowup'] ?? null; 
        $opRegistryData = [
            'master_user_id' => $employeeInfo['masterUserId'],
            'corporate_id' => $employeeInfo['corporateId'],
            'doctor_id' => $validatedData['doctor']['doctorId'] ?? 0,
            'location_id' => $employeeInfo['locationId'],
            'corporate_ohc_id' => $ohcId,
            'referral' => $validatedData['referral'],
            'shift' => $validatedData['workShift'],
            'first_aid_by' => $validatedData['firstAidBy'],
            'doctor_notes' => $validatedData['observations']['doctorNotes'],
            'past_medical_history' => $validatedData['observations']['medicalHistory'],
            'attachment' => null,
            'open_status' => $validatedData['close'] ? 0 : 1,
            'fir_status' => 0,
            'description' => $validatedData['industrialFields']['description'] ?? null,
            'movement_slip' => $validatedData['movementSlip'],
            'physiotherapy' => $validatedData['physiotherapy'],
            'fitness_certificate' => $validatedData['fitnessCert'],
            // TODO: To if it is a follow up then simply copy paste the "Types of incident" mother data to follow up
            'type_of_incident' => ucfirst(preg_replace('/(?<!^)([A-Z])/', ' $1', $validatedData['incidentType'])),
            'incident_id' => $validatedData['incidentTypeId'],
            'nature_injury' => json_encode($validatedData['industrialFields']['natureOfInjury'] ?? []),
            'body_side' => json_encode($validatedData['industrialFields']['sideOfBody'] ?? []),
            'mechanism_injury' => json_encode($validatedData['industrialFields']['injuryMechanism'] ?? []),
            'type_of_injury' => null,
            'site_of_injury' => json_encode($validatedData['industrialFields']['siteOfInjury'] ?? []),
            'place_of_accident' => null,
            'injury_color_text' => isset($validatedData['industrialFields']['injuryColor'])
                ? $validatedData['industrialFields']['injuryColor']
                : ($validatedData['medicalFields']['injuryColor'] ?? null),
            'incident_occurance' => null,
            'symptoms' => json_encode($validatedData['medicalFields']['symptoms'] ?? []),
            'medical_system' => json_encode($validatedData['medicalFields']['medicalSystem'] ?? []),
            'diagnosis' => json_encode($validatedData['medicalFields']['diagnosis'] ?? []),
        ];
        if ($validatedData['incidentType'] === 'medicalIllness') {
            $opRegistryData['body_part'] = json_encode($validatedData['medicalFields']['bodyPart'] ?? []);
        } else {
            $opRegistryData['body_part'] = json_encode($validatedData['industrialFields']['bodyPartIA'] ?? []);
        }
        if ($isFollowup == 1) {
            $opRegistryData['follow_up_op_registry_id'] = $initialOpRegistryIdForFollowUp;
        }
        if ($isUpdate && $editExistingOne && $isFollowup == 0) {
            $opRegistryData['updated_at'] = now();
            OpRegistry::where('op_registry_id', $opRegistryId)->update($opRegistryData);
            $testCode = PrescribedTest::where('op_registry_id', $opRegistryId)
                ->where('isVp', 1)
                ->where('fromOp', 1)
                ->pluck('test_code')
                ->map(fn ($code) => (int) $code)
                ->first();
            PrescribedTest::where('op_registry_id', $opRegistryId)->where('fromOp', 1)->where('isVp', 1)->delete();
            PrescribedTestData::where('test_code', $testCode)->where('fromOp', 1)->delete();
            OpRegistryTimes::where('op_registry_id', $opRegistryId)->delete();
            OpOutsideReferral::where('op_registry_id', $opRegistryId)->delete();
        } else {
            $opRegistryData['op_registry_id'] = $opRegistryId;
            $opRegistryData['parent_id'] = null;
            $opRegistryData['followup_count'] = 0;
            if ($isFollowup == 1) {
                $opRegistryData['followup_count'] = OpRegistry::where('follow_up_op_registry_id', $initialOpRegistryIdForFollowUp)->count() + 1;
            }
            $opRegistryData['created_date_time'] = now();
            $opRegistryData['day_of_registry'] = now();
            OpRegistry::create($opRegistryData);
        }
        return true;
    }
    private function savePrescribedTests(
        array $validatedData,
        int $opRegistryId,
        string $userId,
        string $corporateId,
        string $locationId
    ): void {
        $testCode = PrescribedTest::max('test_code') + 1;
        $prescribedTest = [
            'test_code' => $testCode,
            'isVp' => 1,
            'prescription_id' => null,
            'case_id' => null,
            'user_id' => $userId,
            'doctor_id' => $validatedData['doctor']['doctorId'] ?? 0,
            'hosp_id' => 0,
            'lab_id' => 0,
            'op_registry_id' => $opRegistryId,
            'corporate_id' => $corporateId,
            'location_id' => $locationId,
            'preemp_user_id' => 0,
            'test_date' => now(),
            'test_due_date' => now(),
            'test_modified' => null,
            'favourite_lab' => null,
            'created_on' => now(),
            'created_by' => auth('api')->user()->id ?? 'system',
            'fromOp' => 1,
            'file_name' => null,
        ];
        PrescribedTest::create($prescribedTest);
        $vitalParameters = $validatedData['vitalParameters'];
        foreach ($vitalParameters as $key => $value) {
            $parts = explode('_', $key);
            $masterTestId = end($parts);
            $testExists = MasterTest::where('master_test_id', $masterTestId)->exists();
            if ($testExists) {
                PrescribedTestData::updateOrCreate(
                    [
                        'test_code' => $testCode,
                        'master_test_id' => $masterTestId
                    ],
                    [
                        'test_results' => $value,
                        'fromOp' => 1,
                        'text_condition' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
    private function saveRegistryTimes(array $validatedData, int $opRegistryId): void
    {
        $opRegistryTimes = [
            'op_registry_id' => $opRegistryId,
            'incident_date_time' => $validatedData['incidentDateTime'],
            'reporting_date_time' => $validatedData['reportingDateTime'],
            'leave_from_date_time' => $validatedData['lostHours']['leaveFrom'],
            'leave_upto_date_time' => $validatedData['lostHours']['leaveUpto'],
            'lost_hours' => $validatedData['lostHours']['lostHours'],
            'out_date_time' => $validatedData['lostHours']['outTime'],
            'created_by' => auth('api')->user()->id ?? 'system',
            'created_date_time' => now(),
        ];
        OpRegistryTimes::create($opRegistryTimes);
    }
    private function handleOutsideReferral(array $validatedData, int $opRegistryId): void
    {
        $mrNumber = OpOutsideReferral::max('mr_number') ? OpOutsideReferral::max('mr_number') + 1 : 1;
        $opOutsideReferral = [
            'op_registry_id' => $opRegistryId,
            'mr_number' => $mrNumber,
            'vehicle_type' => $validatedData['hospitalDetails']['vehicleType'],
            'employee_esi' => $validatedData['hospitalDetails']['esiScheme'],
            'hospital_name' => $validatedData['hospitalDetails']['hospitalName'],
            'ambulance_driver' => null,
            'ambulance_number' => null,
            'accompanied_by' => null,
            'meter_in' => null,
            'meter_out' => null,
            'ambulance_intime' => null,
            'ambulance_outtime' => null,
        ];
        if ($validatedData['hospitalDetails']['vehicleType'] === 'ambulance') {
            $opOutsideReferral['ambulance_driver'] = $validatedData['hospitalDetails']['driverName'];
            $opOutsideReferral['ambulance_number'] = $validatedData['hospitalDetails']['ambulanceNumber'];
            $opOutsideReferral['accompanied_by'] = $validatedData['hospitalDetails']['accompaniedBy'];
            $opOutsideReferral['meter_in'] = $validatedData['hospitalDetails']['odometerIn'];
            $opOutsideReferral['meter_out'] = $validatedData['hospitalDetails']['odometerOut'];
            $opOutsideReferral['ambulance_intime'] = $validatedData['hospitalDetails']['timeIn'];
            $opOutsideReferral['ambulance_outtime'] = $validatedData['hospitalDetails']['timeOut'];
        }
        OpOutsideReferral::create($opOutsideReferral);
    }
    private function handleException(Exception $e): JsonResponse
    {
        return response()->json([
            'result' => false,
            'message' => 'Internal Server Error: ' . $e->getMessage()
        ], 500);
    }
    public function addTest(Request $request, $employee_id)
    {
        try {
            $route = $request->route();
            $op_registry_id = $route->hasParameter('op_registry_id') ? $route->parameter('op_registry_id') : null;
            $prescription_id = $route->hasParameter('prescription_id') ? $route->parameter('prescription_id') : null;
            $validatedData = $request->validate([
                'test_ids' => 'required|array|min:1',
                'test_ids.*' => 'required|integer',
                'corporateId' => 'string|required',
                'locationId' => 'string|required',
                'employeeId' => 'string|required',
                'selected_datetime' => 'required|date'
            ]);
            $locationId = $validatedData['locationId'];
            if (!$employee_id || !ctype_alnum($employee_id)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            $isOpRegistry = $op_registry_id !== null;
            $isPrescription = $prescription_id !== null;
            if ($isOpRegistry && !is_numeric($op_registry_id)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            if ($isPrescription && !is_numeric($prescription_id)) {
                return response()->json(['result' => false, 'message' => 'Invalid Prescription ID'], 400);
            }
            if ($isOpRegistry && $op_registry_id > 0) {
                $isOpen = OpRegistry::where('op_registry_id', $op_registry_id)
                    ->where('open_status', 1)
                    ->exists();
                if (!$isOpen) {
                    return response()->json(['result' => false, 'message' => 'Invalid Request'], 422);
                }
            }
            if ($isPrescription && $prescription_id > 0) {
                $prescriptionExists = Prescription::where('prescription_id', $prescription_id)
                    ->exists();
                if (!$prescriptionExists) {
                    return response()->json(['result' => false, 'message' => 'Invalid Prescription'], 422);
                }
            }
            // TODO:
            $employee = EmployeeUserMapping::where('corporate_id', $validatedData['corporateId'])
                ->where('location_id', $locationId)
                ->where('employee_id', $validatedData['employeeId'])
                ->first();
            if (!$employee) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 422);
            }
            $masterTestIds = MasterTest::whereIn('master_test_id', $validatedData['test_ids'])
                ->pluck('master_test_id')
                ->toArray();
            if (count($masterTestIds) !== count($validatedData['test_ids'])) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 422);
            }
            DB::beginTransaction();
            $userId = $employee->user_id;
            $corporateId = $employee->corporate_id;
            $testCode = PrescribedTest::max('test_code') + 1;
            $assignedDateTime = $validatedData['selected_datetime'];
            $this->createPrescribedTest(
                $corporateId,
                $locationId,
                $userId,
                $testCode,
                $isOpRegistry ? $op_registry_id : 0,
                $isPrescription ? $prescription_id : null,
                $masterTestIds,
                $assignedDateTime
            );
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Test added successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Failed to add test: ' . $e->getMessage()
            ], 500);
        }
    }
    private function createPrescribedTest($corporateId, $locationId, $userId, $testCode, $op_registry_id, $prescription_id, $masterTestIds, $assignedDateTime)
    {
        $existingPrescribedTest = null;
        if ($op_registry_id) {
            $existingPrescribedTest = $this->getExistingPrescribedTest($corporateId, $userId, $op_registry_id, null);
        } elseif ($prescription_id) {
            $existingPrescribedTest = $this->getExistingPrescribedTest($corporateId, $userId, null, $prescription_id);
        } else {
            $existingPrescribedTest = $this->getExistingPrescribedTest($corporateId, $userId);
        }
        if ($existingPrescribedTest) {
            $testCode = $existingPrescribedTest->test_code;
            $this->deleteExistingTestDataAndTest($existingPrescribedTest);
        } else {
            $testCode = PrescribedTest::max('test_code') + 1;
        }
        $prescribedTest = PrescribedTest::create([
            'corporate_id' => $corporateId,
            'location_id' => $locationId,
            'user_id' => $userId,
            'test_code' => $testCode,
            'prescription_id' => $prescription_id,
            'case_id' => null,
            'doctor_id' => $prescription_id ? (Prescription::find($prescription_id)->doctor_id ?? 0) : 0,
            'hosp_id' => 0,
            'lab_id' => 0,
            'preemp_user_id' => 0,
            'op_registry_id' => $op_registry_id,
            'test_date' => now(),
            'test_due_date' => now(),
            'test_modified' => null,
            'favourite_lab' => null,
            'fromOp' => $op_registry_id ? 1 : 0,
            'created_on' => now(),
            'created_by' => auth('api')->user()->id ?? 'system',
        ]);
        $prescribedTestData = array_map(function ($masterTestId) use ($testCode, $prescription_id, $op_registry_id) {
            return [
                'test_code' => $testCode,
                'master_test_id' => $masterTestId,
                'fromOp' => $op_registry_id ? 1 : 0,
                'created_at' => now(),
                'test_results' => null,
                'text_condition' => null,
                'updated_at' => now(),
            ];
        }, $masterTestIds);
        PrescribedTestData::insert($prescribedTestData);
    }
    private function getExistingPrescribedTest($corporateId, $userId, $op_registry_id = null, $prescription_id = null)
    {
        $query = PrescribedTest::where('corporate_id', $corporateId)
            ->where('user_id', $userId)
            ->where('isVp', 0);
        if ($op_registry_id) {
            $query->where('op_registry_id', $op_registry_id)
                ->where('fromOp', 1);
        } elseif ($prescription_id) {
            $query->where('prescription_id', $prescription_id)
                ->where('fromOp', 0);
        } else {
            $query->where('prescription_id', null)
                ->where('op_registry_id', 0)
                ->where('fromOp', 0);
        }
        return $query->first();
    }
    private function deleteExistingTestDataAndTest($existingPrescribedTest)
    {
        PrescribedTestData::where('test_code', $existingPrescribedTest->test_code)->delete();
        $existingPrescribedTest->delete();
    }
    public function getTestForEmployee(Request $request, $employeeId = null, $op_registry_id = null)
    {
        $route = $request->route();
        $op_registry_id = $route->hasParameter('op_registry_id') ? $route->parameter('op_registry_id') : null;
        $prescription_id = $route->hasParameter('prescription_id') ? $route->parameter('prescription_id') : null;
        if ($employeeId === null || !ctype_alnum($employeeId)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if ($op_registry_id !== null && !is_numeric($op_registry_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if ($prescription_id !== null && !is_numeric($prescription_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $employee = EmployeeUserMapping::where('employee_id', $employeeId)->first();
        if ($employee) {
            $corporateId = $employee->corporate_id;
            $locationId = $employee->location_id;
            $userId = $employee->user_id;
            if ($op_registry_id) {
                $prescribedTests = PrescribedTest::where('corporate_id', $corporateId)
                    ->where('location_id', $locationId)
                    ->where('user_id', $userId)
                    ->where('fromOp', 1)
                    ->where('op_registry_id', $op_registry_id)
                    ->where('isVp', 0)
                    ->get();
            } elseif ($prescription_id) {
                $prescribedTests = PrescribedTest::where('corporate_id', $corporateId)
                    ->where('location_id', $locationId)
                    ->where('user_id', $userId)
                    ->where('fromOp', 0)
                    ->where('prescription_id', $prescription_id)
                    ->where('isVp', 0)
                    ->get();
            } else {
                $prescribedTests = PrescribedTest::where('corporate_id', $corporateId)
                    ->where('location_id', $locationId)
                    ->where('user_id', $userId)
                    ->where('prescription_id', null)
                    ->where('op_registry_id', 0)
                    ->where('fromOp', 0)
                    ->where('isVp', 0)
                    ->get();
            }
            if ($prescribedTests->isEmpty()) {
                return response()->json(['result' => false, 'message' => 'No tests found for this employee'], 404);
            }
            $testCodes = $prescribedTests->pluck('test_code')->toArray();
            $testResults = PrescribedTestData::whereIn('test_code', $testCodes)->where('fromOp', $op_registry_id ? 1 : 0)->get();
            $testIds = $testResults->pluck('master_test_id')->toArray();
            return response()->json(['result' => true, 'data' => $testIds]);
        } else {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 422);
        }
    }
    public function getGroupTestForEmployee(Request $request, $employeeId)
    {
        if (!ctype_alnum($employeeId)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $employee = EmployeeUserMapping::where('employee_id', $employeeId)->first();
        if ($employee) {
            $corporateId = $employee->corporate_id;
            $locationId = $employee->location_id;
            $userId = $employee->user_id;
            $prescribedTests = PrescribedTest::where('user_id', $userId)->where('isVp', 0)
                ->get();
            if ($prescribedTests->isEmpty()) {
                return response()->json(['result' => false, 'message' => 'No tests found for this employee'], 404);
            }
            $testCodes = $prescribedTests->pluck('test_code')->toArray();
            $testResults = PrescribedTestData::whereIn('test_code', $testCodes)->where('fromOp', 0)->get();
            $testIds = $testResults->pluck('master_test_id')->toArray();
            return response()->json(['result' => true, 'data' => $testIds]);
        } else {
            return response()->json(['result' => false, 'message' => 'Employee ID does not exist'], 422);
        }
    }
    public function getAllSubGroup()
    {
        $allGroups = TestGroup::all()->keyBy('test_group_id');
        $subSubGroups = TestGroup::where('group_type', 3)->get()->groupBy('subgroup_id');
        $allTests = MasterTest::select('master_test_id', 'test_name', 'subgroup_id', 'subsubgroup_id')->get();
        $testsBySubgroup = $allTests->groupBy('subgroup_id');
        $testsBySubSubgroup = $allTests->groupBy('subsubgroup_id');
        $subGroups = TestGroup::where('group_type', 2)->get()->map(function ($group) use (
            $allGroups,
            $subSubGroups,
            $testsBySubgroup,
            $testsBySubSubgroup
        ) {
            return [
                'test_group_id' => $group->test_group_id,
                'test_group_name' => $group->test_group_name,
                'group_type' => $group->group_type,
                'group_id' => $group->group_id,
                'subgroup_id' => $group->subgroup_id,
                'active_status' => $group->active_status,
                'mother_group' => $allGroups->get($group->group_id)->test_group_name ?? null,
                'tests' => $testsBySubgroup->get($group->test_group_id)?->map(function ($test) {
                    return [
                        'master_test_id' => $test->master_test_id,
                        'test_name' => $test->test_name,
                    ];
                })->values() ?? [],
                'subgroups' => collect($subSubGroups->get($group->test_group_id))->map(function ($subSub) use ($testsBySubSubgroup) {
                    return [
                        'test_group_id' => $subSub->test_group_id,
                        'test_group_name' => $subSub->test_group_name,
                        'group_type' => $subSub->group_type,
                        'group_id' => $subSub->group_id,
                        'subgroup_id' => $subSub->subgroup_id,
                        'active_status' => $subSub->active_status,
                        'tests' => $testsBySubSubgroup->get($subSub->test_group_id)?->map(function ($test) {
                            return [
                                'master_test_id' => $test->master_test_id,
                                'test_name' => $test->test_name,
                            ];
                        })->values() ?? [],
                    ];
                }),
            ];
        });
        return response()->json([
            'result' => true,
            'data' => [
                'subgroups' => $subGroups,
            ],
        ], 200);
    }
    public function getAllHealthRegistry(Request $request, $corporateId = null, $locationId = null, $employeeId = null)
    {
        if (auth('api')->user() && auth('api')->user()->getTable() == 'corporate_admin_user') {
            return $this->getAllHealthRegistryForCorporate($corporateId, $locationId, $employeeId);
        } elseif (auth('employee_api')->user() && auth('employee_api')->user()->getTable() == 'master_user') {
            return $this->getAllHealthRegistryForEmployee($request, $corporateId, $locationId, $employeeId);
        } else {
            return response()->json(['result' => false, 'message' => 'Unauthorized user type'], 403);
        }
    }
    public function getAllHealthRegistryForCorporate($corporateId = null, $locationId = null, $employeeId = null)
    {
        if ($corporateId === null || $locationId === null) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $employeeMappings = $this->getEmployeeMappings($corporateId, $locationId, $employeeId);
        if ($employeeMappings->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $healthRegistry = [];
        foreach ($employeeMappings as $employee) {
            [$employeeName, $department, $age] = $this->getEmployeeDetails($employee);
            $opRegistry = OpRegistry::join('op_registry_times', 'op_registry.op_registry_id', '=', 'op_registry_times.op_registry_id')
                ->where('op_registry.corporate_id', $corporateId)
                ->where('op_registry.location_id', $locationId)
                ->where('op_registry.master_user_id', $employee->user_id)
                ->select('op_registry.*')
                ->get();
            if ($opRegistry->isEmpty()) {
                continue;
            }
            $registryData = $this->getRegistryData($opRegistry, $employee, $employeeName, $department, $age);
            $healthRegistry = array_merge($healthRegistry, $registryData);
        }
        return response()->json([
            'result' => true,
            'data' => $healthRegistry,
        ], 200);
    }
    public function getAllHealthRegistryForEmployee(Request $request, $corporateId = null, $locationId = null, $employeeId = null)
    {
        $employeeId = $request->masterUserEmployeeId;
        if ($corporateId === null || $locationId === null) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $employeeMappings = $this->getEmployeeMappings($corporateId, $locationId, $employeeId);
        if ($employeeMappings->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $healthRegistry = [];
        foreach ($employeeMappings as $employee) {
            [$employeeName, $department, $age] = $this->getEmployeeDetails($employee);
            $opRegistry = OpRegistry::join('op_registry_times', 'op_registry.op_registry_id', '=', 'op_registry_times.op_registry_id')
                ->where('op_registry.corporate_id', $corporateId)
                ->where('op_registry.location_id', $locationId)
                ->where('op_registry.master_user_id', $employee->user_id)
                ->select('op_registry.*')
                ->get();
            if ($opRegistry->isEmpty()) {
                continue;
            }
            $registryData = $this->getRegistryData($opRegistry, $employee, $employeeName, $department, $age);
            $healthRegistry = array_merge($healthRegistry, $registryData);
        }
        return response()->json([
            'result' => true,
            'data' => $healthRegistry,
        ], 200);
    }
    private function getEmployeeMappings($corporateId, $locationId, $employeeId = null)
    {
        $query = EmployeeUserMapping::where('corporate_id', $corporateId)
            ->where('location_id', $locationId);
        if ($employeeId !== null) {
            $query->where('employee_id', $employeeId);
        }
        return $query->get();
    }
    private function getRegistryData($opRegistry, $employee, $employeeName, $department, $age)
    {
        $results = [];
        $opregistryTimes = OpRegistryTimes::whereIn('op_registry_id', $opRegistry->pluck('op_registry_id'))->get();
        $opOutsideReferral = OpOutsideReferral::whereIn('op_registry_id', $opRegistry->pluck('op_registry_id'))->get();
        $prescribedTest = PrescribedTest::whereIn('op_registry_id', $opRegistry->pluck('op_registry_id'))->where('isVp', 0)->get();
        $prescribedTestData = PrescribedTestData::whereIn('test_code', $prescribedTest->pluck('test_code'))->get();
        $prescriptionServices = new PrescriptionController();
        foreach ($opRegistry as $registry) {
            $registryTimes = $opregistryTimes->where('op_registry_id', $registry->op_registry_id)->first();
            $outsideReferral = $opOutsideReferral->where('op_registry_id', $registry->op_registry_id)->first();
            $prescribedTests = $prescribedTest->where('op_registry_id', $registry->op_registry_id);
            $prescription_id = Prescription::where('op_registry_id', $registry->op_registry_id)
                ->pluck('prescription_id')
                ->first();
            $prescriptions = null;
            if ($prescription_id) {
                $request = request();
                $prescriptions = $prescriptionServices->getPrintPrescriptionById($prescription_id, $request);
                $decodedPrescriptions = $prescriptions->getData(true);
                if (!empty($decodedPrescriptions['data'])) {
                    $prescriptions = [
                        'prescription' => $decodedPrescriptions['data']['prescription'] ?? null,
                        'prescription_details' => $decodedPrescriptions['data']['prescription_details'] ?? [],
                    ];
                } else {
                    $prescriptions = null;
                }
            }
            $testData = [];
            foreach ($prescribedTests as $test) {
                $testData[] = [
                    'test_code' => $test->test_code,
                    'test_ids' => $prescribedTestData->where('test_code', $test->test_code)->pluck('master_test_id'),
                ];
            }
            $employee_gender = MasterUser::where('user_id', $employee->user_id)->value("gender");
            $employee_gender = strtolower($this->aes256DecryptData($employee_gender));
            $entry = [
                'employee_id' => $employee->employee_id,
                'employee_name' => $employeeName,
                'employee_gender' => $employee_gender,
                'age' => $age,
                'department' => $department,
                'registry' => $registry,
                'registry_times' => $registryTimes,
                'outside_referral' => $outsideReferral,
                'prescribed_tests' => $testData,
                'prescriptionsForRegistry' => $prescriptions,
            ];
            if ($registry->type_of_incident === 'medicalIllness') {
                $entry['symptom_names'] = $this->getSymptomsNames($registry);
            } elseif (in_array($registry->type_of_incident, ['industrialAccident', 'outsideAccident'])) {
                $entry['nature_of_injury_names'] = $this->getNatureOfInjuryNames($registry);
            }
            if (!empty($registry->diagnosis)) {
                $entry['diagnosis_names'] = $this->getDiagnosisNames($registry);
            }
            if (!empty($registry->mechanism_injury)) {
                $entry['mechanism_injury'] = $this->getMechanismInjury($registry);
            }
            if (!empty($registry->medical_system)) {
                $entry['medical_system'] = $this->getMedicalSystem($registry);
            }
            if (!empty($registry->body_part)) {
                $entry['body_part'] = $this->getBodyPartName($registry);
            }
            $results[] = $entry;
        }
        return $results;
    }
    private function getEmployeeDetails($employee)
    {
        $firstNameEncrypted = MasterUser::where('user_id', $employee->user_id)->value('first_name');
        $lastNameEncrypted = MasterUser::where('user_id', $employee->user_id)->value('last_name');
        $dobEncrypted = MasterUser::where('user_id', $employee->user_id)->value('dob');
        $firstName = $this->aes256DecryptData($firstNameEncrypted);
        $lastName = $this->aes256DecryptData($lastNameEncrypted);
        $age = $this->aes256DecryptData($dobEncrypted);
        $age = $this->calculateAge($age);
        $department = CorporateHl1::where('hl1_id', $employee->hl1_id)->value('hl1_name');
        return [$firstName . ' ' . $lastName, $department, $age];
    }
    private function getSymptomsNames($registry)
    {
        $symptoms = json_decode($registry->symptoms, true);
        if (!is_array($symptoms)) {
            return [];
        }
        $names = [];
        foreach ($symptoms as $symptom) {
            $name = Outpatient::where('op_component_id', $symptom)
                ->where('op_component_type', 6)
                ->value('op_component_name');
            if ($name) {
                $names[] = $name;
            }
        }
        return $names;
    }
    private function getBodyPartName($registry)
    {
        $bodyparts = json_decode($registry->body_part, true);
        if (!is_array($bodyparts)) {
            return [];
        }
        $names = [];
        foreach ($bodyparts as $bodypart) {
            $name = Outpatient::where('op_component_id', $bodypart)
                ->where('op_component_type', 5)
                ->value('op_component_name');
            if ($name) {
                $names[] = $name;
            }
        }
        return $names;
    }
    private function getDiagnosisNames($registry)
    {
        $diagnosisIds = json_decode($registry->diagnosis, true);
        if (!is_array($diagnosisIds)) {
            return [];
        }
        $names = [];
        foreach ($diagnosisIds as $id) {
            $name = Outpatient::where('op_component_id', $id)
                ->where('op_component_type', 8)
                ->value('op_component_name');
            if ($name) {
                $names[] = $name;
            }
        }
        return $names;
    }
    private function getMechanismInjury($registry)
    {
        $injuryIds = json_decode($registry->mechanism_injury, true);
        if (!is_array($injuryIds)) {
            return [];
        }
        $names = [];
        foreach ($injuryIds as $id) {
            $name = Outpatient::where('op_component_id', $id)
                ->where('op_component_type', 4)
                ->value('op_component_name');
            if ($name) {
                $names[] = $name;
            }
        }
        return $names;
    }
    private function getMedicalSystem($registry)
    {
        $systemIds = json_decode($registry->medical_system, true);
        if (!is_array($systemIds)) {
            return [];
        }
        $names = [];
        foreach ($systemIds as $id) {
            $name = Outpatient::where('op_component_id', $id)
                ->where('op_component_type', 7)
                ->value('op_component_name');
            if ($name) {
                $names[] = $name;
            }
        }
        return $names;
    }
    private function getNatureOfInjuryNames($registry)
    {
        $injuries = json_decode($registry->nature_injury, true);
        if (!is_array($injuries)) {
            return [];
        }
        $names = [];
        foreach ($injuries as $injury) {
            $name = Outpatient::where('op_component_id', $injury)
                ->where('op_component_type', 3)
                ->value('op_component_name');
            if ($name) {
                $names[] = $name;
            }
        }
        return $names;
    }
    /**
     * Test list page
     */
    // TODO: get single test data for the page => https://login-users.hygeiaes.com/ohc/health-registry/list-registry
    // TODO: to filter ishealthplanassigned status check only for the page => https://login-users.hygeiaes.com/ohc/test-list
    public function getAllTestsFromPrescribedTest(Request $request, $corporate_id = null, $location_id = null, $EmployeeuserId = null): JsonResponse
    {
        if (auth('api')->user() && auth('api')->user()->getTable() == 'corporate_admin_user') {
            return $this->getAllTestsFromPrescribedTestForCorporate($request, $corporate_id, $location_id, $EmployeeuserId);
        } elseif (auth('employee_api')->user() && auth('employee_api')->user()->getTable() == 'master_user') {
            return $this->getAllTestsFromPrescribedTestForEmployee($request, $corporate_id, $location_id, $EmployeeuserId);
        } else {
            return response()->json(['result' => false, 'message' => 'Unauthorized user type'], 403);
        }
    }
    public function getAllTestsFromPrescribedTestForCorporate(Request $request, $corporate_id, $location_id, $EmployeeuserId): JsonResponse
    {
        if ($this->isInvalidInput($corporate_id, $location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if (!$this->corporateExists($corporate_id, $location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $prescribedTests = $this->getPrescribedTests($corporate_id, $location_id);
        if ($prescribedTests->isEmpty()) {
            return response()->json(['result' => true, 'message' => 'No Prescribed Tests Found'], 422);
        }
        $prescribedTests = $this->enrichPrescribedTests($prescribedTests);
        return response()->json(['result' => true, 'data' => $prescribedTests]);
    }
    public function getAllTestsFromPrescribedTestForEmployee(Request $request, $corporate_id, $location_id, $EmployeeuserId): JsonResponse
    {
        if ($this->isInvalidInput($corporate_id, $location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if (!$this->corporateExists($corporate_id, $location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $prescribedTests = $this->getPrescribedTestsForEmployee($EmployeeuserId);
        if ($prescribedTests->isEmpty()) {
            return response()->json(['result' => true, 'message' => 'No Prescribed Tests Found'], 422);
        }
        $prescribedTests = $this->enrichPrescribedTests($prescribedTests);
        return response()->json(['result' => true, 'data' => $prescribedTests]);
    }
    public function getSinglePrescribedTest($corporate_id = null, $location_id = null, string $testCode): JsonResponse
    {
        if (empty($testCode) || !is_numeric($testCode)) {
            return response()->json(['result' => false, 'message' => 'Invalid test code'], 400);
        }
        $query = PrescribedTest::where('test_code', $testCode)->where('isVp', 0);
        if ($corporate_id !== null && ctype_alnum($corporate_id)) {
            $query->where('corporate_id', $corporate_id);
        }
        if ($location_id !== null && ctype_alnum($location_id)) {
            $query->where('location_id', $location_id);
        }
        $prescribedTest = $query->first();
        if (!$prescribedTest) {
            return response()->json(['result' => false, 'message' => 'Prescribed Test Not Found'], 404);
        }
        $enrichedTest = $this->enrichSinglePrescribedTest($prescribedTest);
        $status = HealthplanAssignedStatus::where('test_code', $enrichedTest['test_code'])->first();
        $enrichedTest->in_process = null;
        $enrichedTest->result_ready = null;
        $enrichedTest->certified = null;
        $enrichedTest->uploaded_file_name = null;
        if ($status) {
            if ($status->in_process != null) {
                $enrichedTest->in_process = $status->in_process;
            }
            if ($status->result_ready != null) {
                $enrichedTest->result_ready = $status->result_ready;
            }
            if ($status->certified != null) {
                $enrichedTest->certified = $status->certified;
            }
            $filename = HealthplanAssignedStatusFile::where('healthplan_assigned_status_id', $status->id)
                ->value('file_name');
            if ($filename) {
                $enrichedTest->uploaded_file_name = $filename;
            }
        }
        return response()->json(['result' => true, 'data' => $enrichedTest]);
    }
    private function isInvalidInput($corporate_id, $location_id): bool
    {
        return $corporate_id === null || !ctype_alnum($corporate_id) ||
            $location_id === null || !ctype_alnum($location_id);
    }
    private function corporateExists(string $corporate_id, string $location_id): bool
    {
        return MasterCorporate::where('corporate_id', $corporate_id)
            ->where('location_id', $location_id)
            ->exists();
    }
    private function getPrescribedTests(string $corporate_id, string $location_id)
    {
        return PrescribedTest::where('corporate_id', $corporate_id)
            ->where('location_id', $location_id)
            ->where('isVp', 0)
            ->where('isAssignedHealthplan', 0)
            ->get();
    }
    private function getPrescribedTestsForEmployee(string $user_id)
    {
        return PrescribedTest::where('user_id', $user_id)
            ->where('isVp', 0)
            ->where('isAssignedHealthplan', 0)
            ->get();
    }
    private function enrichPrescribedTests($prescribedTests)
    {
        $lookupData = $this->gatherLookupData($prescribedTests);
        foreach ($prescribedTests as $prescribedTest) {
            $this->enrichPrescribedTest($prescribedTest, $lookupData);
        }
        return $prescribedTests;
    }
    private function enrichSinglePrescribedTest($prescribedTest)
    {
        $collection = collect([$prescribedTest]);
        $lookupData = $this->gatherLookupData($collection);
        $this->enrichPrescribedTest($prescribedTest, $lookupData);
        return $prescribedTest;
    }
    private function gatherLookupData($prescribedTests): array
    {
        $userIds = $prescribedTests->pluck('user_id')->unique()->toArray();
        $testCodes = $prescribedTests->pluck('test_code')->unique()->toArray();
        $opRegistryIds = $prescribedTests->pluck('op_registry_id')->filter()->unique()->toArray();
        $employeeMappings = EmployeeUserMapping::whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');
        $masterUsers = MasterUser::whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');
        $healthplanStatuses = HealthplanAssignedStatus::whereIn('test_code', $testCodes)
            ->get()
            ->groupBy('test_code');
        $prescribedTestsData = PrescribedTestData::whereIn('test_code', $testCodes)
            ->select('test_code', 'master_test_id', 'test_results')
            ->get()
            ->groupBy('test_code');
        $masterTests = MasterTest::select('master_test_id', 'test_name', 'testgroup_id', 'subgroup_id', 'subsubgroup_id', 'unit', 'm_min_max', 'f_min_max')
            ->get()
            ->keyBy('master_test_id');
        $departments = CorporateHl1::pluck('hl1_name', 'hl1_id');
        $opRegistryQuery = OpRegistry::whereIn('op_registry_id', $opRegistryIds)
            ->select('op_registry_id', 'doctor_notes', 'past_medical_history', 'referral', 'type_of_incident');
        $opRegistryQuery->addSelect('body_part', 'symptoms', 'diagnosis', 'medical_system');
        $opRegistryQuery->addSelect('injury_color_text', 'body_side', 'site_of_injury', 'nature_injury', 'body_part', 'mechanism_injury');
        $opRegistryData = $opRegistryQuery->get();
        $natureInjuryIds = [];
        $bodyPartIds = [];
        $mechanismInjuryIds = [];
        foreach ($opRegistryData as $record) {
            if (in_array($record->type_of_incident, ['industrialAccident', 'outsideAccident'])) {
                if ($record->nature_injury) {
                    $natureIds = json_decode($record->nature_injury, true);
                    if (is_array($natureIds)) {
                        $natureInjuryIds = array_merge($natureInjuryIds, $natureIds);
                    }
                }
                if ($record->body_part) {
                    $bodyIds = json_decode($record->body_part, true);
                    if (is_array($bodyIds)) {
                        $bodyPartIds = array_merge($bodyPartIds, $bodyIds);
                    }
                }
                if ($record->mechanism_injury) {
                    $mechanismIds = json_decode($record->mechanism_injury, true);
                    if (is_array($mechanismIds)) {
                        $mechanismInjuryIds = array_merge($mechanismInjuryIds, $mechanismIds);
                    }
                }
            }
        }
        $allComponentIds = array_unique(array_merge($natureInjuryIds, $bodyPartIds, $mechanismInjuryIds));
        $outPatientComponents = [];
        if (!empty($allComponentIds)) {
            $outPatientComponents = OutPatient::whereIn('op_component_id', $allComponentIds)
                ->select('op_component_id', 'op_component_name')
                ->get()
                ->keyBy('op_component_id')
                ->toArray();
        }
        $processedOpRegistryData = [];
        foreach ($opRegistryData as $record) {
            $data = [
                'op_registry_id' => $record->op_registry_id,
                'doctor_notes' => $record->doctor_notes,
                'past_medical_history' => $record->past_medical_history,
                'referral' => $record->referral,
                'type_of_incident' => $record->type_of_incident
            ];
            if ($record->type_of_incident === 'medicalIllness') {
                $data['body_part'] = $record->body_part;
                $data['symptoms'] = $record->symptoms;
                $data['diagnosis'] = $record->diagnosis;
                $data['medical_system'] = $record->medical_system;
                if ($record->symptoms) {
                    $symptomIds = json_decode($record->symptoms, true);
                    $symptomNames = [];
                    if (is_array($symptomIds)) {
                        foreach ($symptomIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $symptomNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['symptoms'] = $symptomNames;
                    $data['symptoms_ids'] = $symptomIds;
                }
                if ($record->body_part) {
                    $bodyPartIds = json_decode($record->body_part, true);
                    $bodyPartNames = [];
                    if (is_array($bodyPartIds)) {
                        foreach ($bodyPartIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $bodyPartNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['body_part'] = $bodyPartNames;
                    $data['body_part_ids'] = $bodyPartIds;
                }
                if ($record->diagnosis) {
                    $diagnosisIds = json_decode($record->diagnosis, true);
                    $diagnosisNames = [];
                    if (is_array($diagnosisIds)) {
                        foreach ($diagnosisIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $diagnosisNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['diagnosis'] = $diagnosisNames;
                    $data['diagnosis_ids'] = $diagnosisIds;
                }
                if ($record->medical_system) {
                    $medicalSystemIds = json_decode($record->medical_system, true);
                    $medicalSystemNames = [];
                    if (is_array($medicalSystemIds)) {
                        foreach ($medicalSystemIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $medicalSystemNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['medical_system'] = $medicalSystemNames;
                    $data['medical_system_ids'] = $medicalSystemIds;
                }
            } elseif (in_array($record->type_of_incident, ['industrialAccident', 'outsideAccident'])) {
                $data['injury_color_text'] = $record->injury_color_text;
                $data['body_side'] = $record->body_side;
                $data['site_of_injury'] = $record->site_of_injury;
                if ($record->nature_injury) {
                    $natureIds = json_decode($record->nature_injury, true);
                    $natureNames = [];
                    if (is_array($natureIds)) {
                        foreach ($natureIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $natureNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['nature_injury'] = $natureNames;
                    $data['nature_injury_ids'] = $natureIds;
                }
                if ($record->body_part) {
                    $bodyPartIds = json_decode($record->body_part, true);
                    $bodyPartNames = [];
                    if (is_array($bodyPartIds)) {
                        foreach ($bodyPartIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $bodyPartNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['body_part'] = $bodyPartNames;
                    $data['body_part_ids'] = $bodyPartIds;
                }
                if ($record->mechanism_injury) {
                    $mechanismIds = json_decode($record->mechanism_injury, true);
                    $mechanismNames = [];
                    if (is_array($mechanismIds)) {
                        foreach ($mechanismIds as $id) {
                            if (isset($outPatientComponents[$id])) {
                                $mechanismNames[] = $outPatientComponents[$id]['op_component_name'];
                            }
                        }
                    }
                    $data['mechanism_injury'] = $mechanismNames;
                    $data['mechanism_injury_ids'] = $mechanismIds;
                }
            }
            $processedOpRegistryData[$record->op_registry_id] = $data;
        }
        $testGroupData = $this->getTestGroupData($masterTests);
        return [
            'employeeMappings' => $employeeMappings,
            'masterUsers' => $masterUsers,
            'healthplanStatuses' => $healthplanStatuses,
            'prescribedTestsData' => $prescribedTestsData,
            'masterTests' => $masterTests,
            'departments' => $departments,
            'testGroups' => $testGroupData['testGroups'],
            'subGroups' => $testGroupData['subGroups'],
            'subSubGroups' => $testGroupData['subSubGroups'],
            'opRegistryData' => $processedOpRegistryData
        ];
    }
    private function getTestGroupData($masterTests): array
    {
        $testGroupIds = $masterTests->pluck('testgroup_id')->filter()->unique()->toArray();
        $subGroupIds = $masterTests->pluck('subgroup_id')->filter()->unique()->toArray();
        $subSubGroupIds = $masterTests->pluck('subsubgroup_id')->filter()->unique()->toArray();
        $testGroups = TestGroup::whereIn('test_group_id', $testGroupIds)
            ->pluck('test_group_name', 'test_group_id');
        $subGroups = TestGroup::whereIn('test_group_id', $subGroupIds)
            ->pluck('test_group_name', 'test_group_id');
        $subSubGroups = TestGroup::whereIn('test_group_id', $subSubGroupIds)
            ->pluck('test_group_name', 'test_group_id');
        return [
            'testGroups' => $testGroups,
            'subGroups' => $subGroups,
            'subSubGroups' => $subSubGroups
        ];
    }
    private function enrichPrescribedTest($prescribedTest, array $lookupData): void
    {
        $userId = $prescribedTest->user_id;
        $testCode = $prescribedTest->test_code;
        $opRegistryId = $prescribedTest->op_registry_id;
        $prescriptionId = $prescribedTest->prescription_id;
        if ($prescriptionId > 0) {
            $prescriptionServices = new PrescriptionController();
            $prescriptionResponse = $prescriptionServices->getPrintPrescriptionById($prescriptionId, request());
            if ($prescriptionResponse instanceof JsonResponse) {
                $responseData = $prescriptionResponse->getData();
                if (isset($responseData->result) && $responseData->result === true && isset($responseData->data)) {
                    $prescribedTest->prescription_data = $responseData->data;
                } else {
                    $prescribedTest->prescription_data = null;
                }
            } else {
                $prescribedTest->prescription_data = $prescriptionResponse;
            }
        } else {
            $prescribedTest->prescription_data = null;
        }
        $this->addHealthplanStatusData($prescribedTest, $testCode, $lookupData['healthplanStatuses']);
        $this->addEmployeeAndUserData(
            $prescribedTest,
            $userId,
            $lookupData['employeeMappings'],
            $lookupData['masterUsers'],
            $lookupData['departments']
        );
        $this->addTestGroupData(
            $prescribedTest,
            $testCode,
            $lookupData['prescribedTestsData'],
            $lookupData['masterTests'],
            $lookupData['testGroups'],
            $lookupData['subGroups'],
            $lookupData['subSubGroups']
        );
        $this->addOpRegistryData($prescribedTest, $opRegistryId, $lookupData['opRegistryData']);
    }
    private function addOpRegistryData($prescribedTest, $opRegistryId, $opRegistryData): void
    {
        $opRegistry = $opRegistryId ? ($opRegistryData[$opRegistryId] ?? null) : null;
        if (!$opRegistry) {
            $prescribedTest->doctor_notes = null;
            $prescribedTest->past_medical_history = null;
            $prescribedTest->referral = null;
            $prescribedTest->type_of_incident = null;
            return;
        }
        $prescribedTest->doctor_notes = $opRegistry['doctor_notes'];
        $prescribedTest->past_medical_history = $opRegistry['past_medical_history'];
        $prescribedTest->referral = $opRegistry['referral'];
        $prescribedTest->type_of_incident = $opRegistry['type_of_incident'];
        if ($opRegistry['type_of_incident'] === 'medicalIllness') {
            $prescribedTest->body_part_ids = $opRegistry['body_part'] ?? null;
            $prescribedTest->symptoms_ids = $opRegistry['symptoms'] ?? null;
            $prescribedTest->diagnosis_ids = $opRegistry['diagnosis'] ?? null;
            $prescribedTest->medical_system_ids = $opRegistry['medical_system'] ?? null;
            $prescribedTest->body_part = isset($opRegistry['body_part']) ?
                implode(', ', $opRegistry['body_part']) : null;
            $prescribedTest->symptoms = isset($opRegistry['symptoms']) ?
                implode(', ', $opRegistry['symptoms']) : null;
            $prescribedTest->diagnosis = isset($opRegistry['diagnosis']) ?
                implode(', ', $opRegistry['diagnosis']) : null;
            $prescribedTest->medical_system = isset($opRegistry['medical_system']) ?
                implode(', ', $opRegistry['medical_system']) : null;
            $prescribedTest->injury_color_text = '#006600';
            $prescribedTest->body_side = null;
            $prescribedTest->site_of_injury = null;
            $prescribedTest->nature_injury = null;
            $prescribedTest->mechanism_injury = null;
        } elseif (in_array($opRegistry['type_of_incident'], ['industrialAccident', 'outsideAccident'])) {
            $prescribedTest->injury_color_text = $opRegistry['injury_color_text'] ?? null;
            $prescribedTest->body_side = $opRegistry['body_side'] ?? null;
            $prescribedTest->site_of_injury = $opRegistry['site_of_injury'] ?? null;
            $prescribedTest->nature_injury = isset($opRegistry['nature_injury']) ?
                implode(', ', $opRegistry['nature_injury']) : null;
            $prescribedTest->body_part = isset($opRegistry['body_part']) ?
                implode(', ', $opRegistry['body_part']) : null;
            $prescribedTest->mechanism_injury = isset($opRegistry['mechanism_injury']) ?
                implode(', ', $opRegistry['mechanism_injury']) : null;
            $prescribedTest->nature_injury_ids = $opRegistry['nature_injury_ids'] ?? null;
            $prescribedTest->body_part_ids = $opRegistry['body_part_ids'] ?? null;
            $prescribedTest->mechanism_injury_ids = $opRegistry['mechanism_injury_ids'] ?? null;
            $prescribedTest->symptoms = null;
            $prescribedTest->diagnosis = null;
            $prescribedTest->medical_system = null;
        } else {
            $prescribedTest->body_part = null;
            $prescribedTest->symptoms = null;
            $prescribedTest->diagnosis = null;
            $prescribedTest->medical_system = null;
            $prescribedTest->injury_color_text = null;
            $prescribedTest->body_side = null;
            $prescribedTest->site_of_injury = null;
            $prescribedTest->nature_injury = null;
            $prescribedTest->mechanism_injury = null;
        }
    }
    private function addHealthplanStatusData($prescribedTest, $testCode, $healthplanStatuses): void
    {
        $statusesForTest = isset($healthplanStatuses[$testCode]) ?
            $healthplanStatuses[$testCode]->first() : null;
        $prescribedTest->healthplan_status = $statusesForTest->status ?? 'N/A';
        $prescribedTest->reporting_date_time = OpRegistryTimes::where('op_registry_id', $prescribedTest->op_registry_id)
            ->value('reporting_date_time') ?? 'N/A';
    }
    private function addEmployeeAndUserData(
        $prescribedTest,
        $userId,
        $employeeMappings,
        $masterUsers,
        $departments
    ): void {
        $employeeMapping = $employeeMappings[$userId] ?? null;
        $masterUser = $masterUsers[$userId] ?? null;
        $prescribedTest->employee_id = $employeeMapping->employee_id ?? 'N/A';
        $departmentId = $employeeMapping->hl1_id ?? null;
        $prescribedTest->department = $departmentId ? ($departments[$departmentId] ?? 'N/A') : 'N/A';
        $firstName = $masterUser && $masterUser->first_name ?
            $this->aes256DecryptData($masterUser->first_name) : null;
        $lastName = $masterUser && $masterUser->last_name ?
            $this->aes256DecryptData($masterUser->last_name) : null;
        $prescribedTest->name = trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: 'N/A';
        $dob = $masterUser && $masterUser->dob ? $this->aes256DecryptData($masterUser->dob) : null;
        $prescribedTest->age = $this->calculateAge($dob);
    }
    private function addTestGroupData(
        $prescribedTest,
        $testCode,
        $prescribedTestsData,
        $masterTests,
        $testGroups,
        $subGroups,
        $subSubGroups
    ): void {
        $testDataList = $prescribedTestsData[$testCode] ?? collect();
        $groupedTests = [];
        foreach ($testDataList as $data) {
            $masterTestId = $data->master_test_id;
            $testInfo = $masterTests[$masterTestId] ?? null;
            if (!$testInfo) {
                continue;
            }
            $testName = $testInfo->test_name;
            $groupId = $testInfo->testgroup_id;
            $subGroupId = $testInfo->subgroup_id;
            $subSubGroupId = $testInfo->subsubgroup_id;
            $unit = $testInfo->unit;
            $testResult = $data->test_results;
            $m_min_max = $testInfo->m_min_max;
            $f_min_max = $testInfo->f_min_max;
            $groupName = $testGroups[$groupId] ?? 'Uncategorized';
            if (!isset($groupedTests[$groupName])) {
                $groupedTests[$groupName] = [];
            }
            $this->categorizeTest(
                $groupedTests,
                $groupName,
                $subGroupId,
                $subSubGroupId,
                $subGroups,
                $subSubGroups,
                $testName,
                $unit,
                $m_min_max,
                $f_min_max,
                $masterTestId,
                $testResult
            );
        }
        $prescribedTest->tests = $groupedTests;
    }
    private function categorizeTest(
        &$groupedTests,
        $groupName,
        $subGroupId,
        $subSubGroupId,
        $subGroups,
        $subSubGroups,
        $testName,
        $unit = null,
        $m_min_max = null,
        $f_min_max = null,
        $masterTestId = null,
        $testResult = null
    ): void {
        $testData = [
            'name' => $testName,
            'unit' => $unit,
            'm_min_max' => $m_min_max,
            'f_min_max' => $f_min_max,
            'master_test_id' => $masterTestId,
            'test_result' => $testResult
        ];
        if ($subGroupId && isset($subGroups[$subGroupId])) {
            $subGroupName = $subGroups[$subGroupId];
            if (!isset($groupedTests[$groupName][$subGroupName])) {
                $groupedTests[$groupName][$subGroupName] = [];
            }
            if ($subSubGroupId && isset($subSubGroups[$subSubGroupId])) {
                $subSubGroupName = $subSubGroups[$subSubGroupId];
                if (!isset($groupedTests[$groupName][$subGroupName][$subSubGroupName])) {
                    $groupedTests[$groupName][$subGroupName][$subSubGroupName] = [];
                }
                $groupedTests[$groupName][$subGroupName][$subSubGroupName][] = $testData;
            } else {
                $groupedTests[$groupName][$subGroupName][] = $testData;
            }
        } else {
            $groupedTests[$groupName][] = $testData;
        }
    }
    private function calculateAge($dob)
    {
        if (!$dob) {
            return 'N/A';
        }
        try {
            $dobDate = new \DateTime($dob);
            return $dobDate->diff(new \DateTime())->y;
        } catch (Exception $e) {
            return 'N/A';
        }
    }
    public function saveTestResults(Request $request)
    {
        $validatedData = $request->validate([
            'corporate_id' => 'required|string',
            'employee_id' => 'required|string',
            'location_id' => 'required|string',
            'test_results' => 'required|array',
            'test_results.*.master_test_id' => 'required|integer',
            'test_results.*.test_result' => 'nullable|string',
            'test_results.*.test_code' => 'required|integer',
            'reported_on' => 'nullable|date_format:Y-m-d\TH:i',
            'tested_on' => 'nullable|date_format:Y-m-d\TH:i',
            'document_file' => 'nullable|string',
            'document_filename' => 'nullable|string|max:255',
        ]);
        // TODO: To add the tested on and reported on in the db
        if ($validatedData['employee_id'] === null || !ctype_alnum($validatedData['employee_id'])) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $fileData = $this->validateAndProcessFileData($validatedData);
        if ($fileData === false) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid file format. Only PDF, Word documents, and Excel files are allowed.'
            ], 400);
        }
        $employeeId = $validatedData['employee_id'];
        $corporateId = $validatedData['corporate_id'];
        $locationId = $validatedData['location_id'];
        $testResults = $validatedData['test_results'];
        if ($this->isInvalidInput($corporateId, $locationId)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if (! $this->corporateExists($corporateId, $locationId)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $user_id = EmployeeUserMapping::where('employee_id', $employeeId)->value('user_id');
        if (!$user_id) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $gender = MasterUser::where('user_id', $user_id)->value('gender');
        if ($gender === null) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $gender = strtolower($this->aes256DecryptData($gender));
        $testCodes = collect($testResults)->pluck('test_code')->unique();
        $masterTestIds = collect($testResults)->pluck('master_test_id')->unique();
        $validPrescribedTests = PrescribedTest::where('corporate_id', $corporateId)
            ->where('location_id', $locationId)
            ->where('user_id', $user_id)
            ->whereIn('test_code', $testCodes)
            ->where('isVp', 0)
            ->pluck('test_code')
            ->toArray();
        if (count($validPrescribedTests) !== count($testCodes)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $masterTests = MasterTest::whereIn('master_test_id', $masterTestIds)->get();
        if (count($masterTests) !== count($masterTestIds)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        DB::beginTransaction();
        try {
            $healthplanStatus = HealthplanAssignedStatus::whereIn('test_code', $testCodes)
                ->first();
            if ($healthplanStatus) {
                if ($healthplanStatus->in_process != null && $healthplanStatus->result_ready != null) {
                    return response()->json([
                        'result' => false,
                        'message' => 'You already reported the test.',
                    ], 422);
                }
                if ($validatedData['tested_on'] != null && $validatedData['reported_on'] != null) {
                    if ($healthplanStatus->result_ready === null) {
                        $healthplanStatus->result_ready = $validatedData['reported_on'];
                        $healthplanCertification = HealthplanCertification::whereIn('test_code', $testCodes)->get();
                        if ($healthplanCertification->isNotEmpty()) {
                            $healthplanCertification->each(function ($certification) use ($validatedData) {
                                $certification->certified_on = now();
                                $certification->next_assessment_date = $validatedData['reported_on'];
                                $certification->save();
                            });
                        }
                        $healthplanStatus->save();
                    } else {
                        return response()->json([
                            'result' => false,
                            'message' => 'Invalid Request',
                        ], 422);
                    }
                } elseif ($validatedData['tested_on'] != null && $validatedData['reported_on'] === null) {
                    if ($healthplanStatus->in_process === null) {
                        $healthplanStatus->in_process = $validatedData['tested_on'];
                        $healthplanStatus->save();
                    }
                }
                if ($fileData) {
                    HealthplanAssignedStatusFile::updateOrCreate(
                        ['healthplan_assigned_status_id' => $healthplanStatus->id],
                        [
                            'file_name' => $fileData['filename'],
                            'file_type' => $fileData['mime_type'],
                            'file_base64' => $fileData['base64'],
                            'uploaded_at' => now(),
                        ]
                    );
                }
            }
            foreach ($testResults as $testResult) {
                $masterTestId = $testResult['master_test_id'];
                $masterTest = $masterTests->firstWhere('master_test_id', $masterTestId);
                $textCondition = null;
                if ($masterTest && $masterTest->numeric_type === 'multiple-text-value') {
                    $minMaxRange = null;
                    if ($gender === 'male' && !empty($masterTest->m_min_max)) {
                        $minMaxRange = json_decode($masterTest->m_min_max, true);
                    } elseif ($gender === 'female' && !empty($masterTest->f_min_max)) {
                        $minMaxRange = json_decode($masterTest->f_min_max, true);
                    } elseif (!empty($masterTest->m_min_max)) {
                        $minMaxRange = json_decode($masterTest->m_min_max, true);
                    }
                    $conditionDescriptions = !empty($masterTest->multiple_text_value_description)
                        ? json_decode($masterTest->multiple_text_value_description, true)
                        : [];
                    if (
                        $minMaxRange && $conditionDescriptions &&
                        isset($minMaxRange['min']) && isset($minMaxRange['max']) &&
                        is_array($minMaxRange['min']) && is_array($minMaxRange['max'])
                    ) {
                        $testValue = floatval($testResult['test_result']);
                        for ($i = 0; $i < count($minMaxRange['min']); $i++) {
                            if (isset($minMaxRange['min'][$i], $minMaxRange['max'][$i], $conditionDescriptions[$i])) {
                                $min = floatval($minMaxRange['min'][$i]);
                                $max = floatval($minMaxRange['max'][$i]);
                                if ($testValue >= $min && $testValue <= $max) {
                                    $textCondition = $conditionDescriptions[$i];
                                    break;
                                }
                            }
                        }
                    }
                }
                $prescribedTestData = PrescribedTestData::where('test_code', $testResult['test_code'])
                    ->where('master_test_id', $masterTestId)
                    ->first();
                if ($prescribedTestData) {
                    $testResultValue = trim($testResult['test_result']) === '' ? null : $testResult['test_result'];
                    $prescribedTestData->test_results = $testResultValue;
                    $prescribedTestData->text_condition = $textCondition;
                    $prescribedTestData->save();
                }
            }
            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Test results saved successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => 'Failed to save test results',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function validateAndProcessFileData($validatedData)
    {
        if (empty($validatedData['document_file']) || empty($validatedData['document_filename'])) {
            return null;
        }
        $base64Data = $validatedData['document_file'];
        $filename = $validatedData['document_filename'];
        if (!$this->isValidBase64($base64Data)) {
            return false;
        }
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }
        $decodedData = base64_decode($base64Data);
        $fileSize = strlen($decodedData);
        $maxFileSize = 10 * 1024 * 1024;
        if ($fileSize > $maxFileSize) {
            return false;
        }
        $mimeType = $this->getMimeTypeFromContent($decodedData, $extension);
        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return false;
        }
        if (!$this->isValidFileSignature($decodedData, $extension)) {
            return false;
        }
        return [
            'base64' => $base64Data,
            'filename' => $filename,
            'mime_type' => $mimeType,
            'size' => $fileSize,
            'extension' => $extension
        ];
    }
    private function isValidBase64($data)
    {
        return base64_encode(base64_decode($data, true)) === $data;
    }
    private function getMimeTypeFromContent($content, $extension)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMimeType = $finfo->buffer($content);
        $mimeTypeMap = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        $expectedMimeType = $mimeTypeMap[$extension] ?? 'application/octet-stream';
        return $detectedMimeType && $detectedMimeType !== 'application/octet-stream'
            ? $detectedMimeType
            : $expectedMimeType;
    }
    private function isValidFileSignature($content, $extension)
    {
        if (strlen($content) < 8) {
            return false;
        }
        $signatures = [
            'pdf' => ['25504446'],
            'doc' => ['D0CF11E0A1B11AE1'],
            'docx' => ['504B0304'],
            'xls' => ['D0CF11E0A1B11AE1'],
            'xlsx' => ['504B0304'],
        ];
        if (!isset($signatures[$extension])) {
            return false;
        }
        $fileHeader = strtoupper(bin2hex(substr($content, 0, 8)));
        foreach ($signatures[$extension] as $signature) {
            if (strpos($fileHeader, $signature) === 0) {
                return true;
            }
        }
        return false;
    }
    public function getincidentTypeColorCodes($corporate_id = null, $location_id = null)
    {
        if ($corporate_id === null || $location_id === null) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        if (! $this->corporateExists($corporate_id, $location_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
        }
        $incidentTypeColorCodes = OhcComponents::where('corporate_id', $corporate_id)
            ->value('injury_color_types');
        return response()->json([
            'result' => true,
            'message' => $incidentTypeColorCodes
        ], 200);
    }
    public function saveCertificateCondition()
    {
        $validatedData = request()->validate([
            'certificate_id' => 'required|integer',
            'test_id' => 'required|integer',
            'condition' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
            'healthplan_assigned_status_id' => 'required|integer',
            'user_id' => 'required|string',
            'issue_date' => 'nullable|date',
            'next_assessment_date' => 'nullable|date|after_or_equal:issue_date',
        ]);
        $certificateId = $validatedData['certificate_id'];
        $condition = $validatedData['condition'];
        $remarks = $validatedData['remarks'] ?? null;
        $userId = $validatedData['user_id'];
        $issueDate = $validatedData['issue_date'] ?? now();
        $nextAssessmentDate = $validatedData['next_assessment_date'] ?? null;
        $certification = Certification::where('certificate_id', $certificateId)->first();
        if (!$certification) {
            return response()->json(['result' => false, 'message' => 'Invalid Request']);
        }
        $conditionList = is_array($certification->condition)
            ? $certification->condition
            : json_decode($certification->condition, true);
        if (!in_array($condition, $conditionList)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request']);
        }
        $colorList = is_array($certification->color_condition)
            ? $certification->color_condition
            : json_decode($certification->color_condition, true);
        $conditionIndex = array_search($condition, $conditionList);
        $selectedColor = $conditionIndex !== false && isset($colorList[$conditionIndex])
            ? $colorList[$conditionIndex]
            : null;
        $healthplanAssignedStatus = HealthplanAssignedStatus::find($validatedData['healthplan_assigned_status_id']);
        if (!$healthplanAssignedStatus || $healthplanAssignedStatus->certified !== null || $healthplanAssignedStatus->result_ready === null) {
            return response()->json(['result' => false, 'message' => 'Invalid Request']);
        }
        $testCode = $healthplanAssignedStatus->test_code;
        $data = [
            'user_id' => $userId,
            'certification_id' => $certificateId,
            'test_code' => $testCode,
            'condition' => $condition,
            'color_condition' => $selectedColor,
            'remarks' => $remarks,
            'certified_on' => $issueDate,
            'next_assessment_date' => $nextAssessmentDate,
        ];
        $healthplanCertification = HealthplanCertification::where('test_code', $testCode)
            ->where('certification_id', $certificateId)
            ->first();
        if ($healthplanCertification) {
            $healthplanCertification->update($data);
        } else {
            $data['healthplan_certification_id'] = HealthplanCertification::max('healthplan_certification_id') + 1;
            $data['inserted_on'] = now();
            HealthplanCertification::create($data);
        }
        $allCertifications = HealthplanCertification::where('test_code', $testCode)->pluck('certification_id')->toArray();
        $corporateHealthplanId = HealthplanAssigned::where('test_code', $testCode)
            ->value('corporate_healthplan_id');
        $corporateHealthplanCertificateId = CorporateHealthplan::where('corporate_healthplan_id', $corporateHealthplanId)
            ->value('certificate_id');
        if (json_decode($corporateHealthplanCertificateId) === $allCertifications) {
            HealthplanAssignedStatus::where('test_code', $testCode)->update(['certified' => now()]);
        }
        $action = $healthplanCertification ? 'updated' : 'added';
        return response()->json(['result' => true, 'message' => "Certification {$action} successfully"]);
    }
    public function getAllLocations()
    {
        try {
            if (!(auth('api')->user() && auth('api')->user()->getTable() == 'corporate_admin_user')) {
                return response()->json([
                    'result' => false,
                    'data' => 'Invalid Request'
                ], 403);
            }
            $corporateId = auth('api')->user()->corporate_id;
            if (!$corporateId) {
                return response()->json([
                    'result' => false,
                    'data' => 'Invalid Request'
                ], 400);
            }
            $corporates = MasterCorporate::query()
                ->where('corporate_id', $corporateId)
                ->select(['id', 'location_id', 'display_name'])
                ->get();
            if ($corporates->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sub-location data found.',
                    'data' => []
                ], 200);
            }
            return response()->json([
                'success' => true,
                'data' => $corporates
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching corporate location data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllColorCodes(Request $request)
    {
        if (!(auth('api')->user() && auth('api')->user()->getTable() == 'corporate_admin_user')) {
            return response()->json([
                'result' => false,
                'data' => 'Invalid Request'
            ], 403);
        }
        $corporateId = auth('api')->user()->corporate_id;
        if (!$corporateId) {
            return response()->json([
                'result' => false,
                'data' => 'Invalid Request'
            ], 400);
        }
        $colorCodes = Certification::where('corporate_id', $corporateId)
            ->select(['certification_title', 'condition', 'color_condition'])
            ->get();
        if ($colorCodes->isEmpty()) {
            return response()->json([
                'result' => false,
                'data' => 'No color codes found for the corporate.'
            ], 404);
        }
        $colorCodes->transform(function ($item) {
            $item->condition = is_array($item->condition) ? $item->condition : json_decode($item->condition, true);
            $item->color_condition = is_array($item->color_condition) ? $item->color_condition : json_decode($item->color_condition, true);
            return $item;
        });
        return response()->json([
            'result' => true,
            'data' => $colorCodes
        ], 200);
    }
    public function getEmployeesDetailById($userId)
    {
        $employee = EmployeeUserMapping::with(['masterUser'])->where('employee_id', $userId)->first();
        if (!$employee) {
            return null;
        }
        $masterUser = $employee->masterUser;
        $employeeFirstname = $this->aes256DecryptData($masterUser->first_name ?? '');
        $employeeLastname = $this->aes256DecryptData($masterUser->last_name ?? '');
        $employeeGender = $this->aes256DecryptData($masterUser->gender ?? '');
        $dob = $this->aes256DecryptData($masterUser->dob ?? '');
        $employeeAge = $this->calculateAge($dob);
        $employeeEmail = $this->aes256DecryptData($masterUser->email ?? '');
        $employeeMobile = $this->aes256DecryptData($masterUser->mob_num ?? '');
        $employeeAbhaId = isset($masterUser->abha_id) ? $this->aes256DecryptData($masterUser->abha_id) : null;
        $employeeArea = isset($masterUser->area) ? $this->aes256DecryptData($masterUser->area) : null;
        $employeeZipcode = isset($masterUser->zipcode) ? $this->aes256DecryptData($masterUser->zipcode) : null;
        $employeeAlternativeEmail = isset($masterUser->alternative_email) ? $this->aes256DecryptData($masterUser->alternative_email) : null;
        $dateOfJoining = optional($employee->from_date)
            ? \Carbon\Carbon::parse($employee->from_date)->format('d-m-Y')
            : null;
        $employeeCorporateName = MasterCorporate::where('corporate_id', $employee->corporate_id)->value('corporate_name');
        $employeeLocationName = MasterCorporate::where('corporate_id', $employee->corporate_id)
            ->where('location_id', $employee->location_id)->value('display_name');
        $employeeDepartmentId = $employee->hl1_id;
        $employeeDepartmentName = CorporateHl1::where('hl1_id', $employeeDepartmentId)->value('hl1_name');
        $employeeTypeId = $employee->employee_type_id;
        $employeeTypeName = DB::table('employee_type')->where('employee_type_id', $employeeTypeId)->value('employee_type_name');
        $employee_userId = $employee->user_id;
        return [
            'employee_id'               => $employee->employee_id,
            'employee_firstname'        => $employeeFirstname,
            'employee_lastname'         => $employeeLastname,
            'employee_email'            => $employeeEmail,
            'employee_contact_number'   => $employeeMobile,
            'employee_corporate_name'   => $employeeCorporateName,
            'employee_location_name'    => $employeeLocationName,
            'employee_designation'      => $employee->designation,
            'employee_department_id'    => $employeeDepartmentId,
            'employee_department_name'  => $employeeDepartmentName,
            'employee_type_id'          => $employeeTypeId,
            'employee_type_name'        => $employeeTypeName,
            'employee_gender'           => $employeeGender,
            'employee_age'              => $employeeAge,
            'employee_dob'              => $dob,
            'dateOfJoining'             => $dateOfJoining,
            'profile_pic'               => $masterUser->user_profile_img ?? null,
            'banner'                    => $masterUser->user_banner_img ?? null,
            'abha_id'                   => $employeeAbhaId,
            'area'                      => $employeeArea,
            'zipcode'                   => $employeeZipcode,
            'alternative_email'         => $employeeAlternativeEmail,
            'employee_user_id'          => $employee_userId
        ];
    }
    public function updateHospitalizationDetailsByEmpId(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'description' => 'nullable|string',
            'doctor_id' => 'nullable|integer',
            'doctor_name' => 'nullable|string',
            'hospital_id' => 'nullable|integer',
            'hospital_name' => 'nullable|string',
            'condition' => 'nullable|array',
            'discharge_summary_base64' => 'nullable|string',
            'summary_reports_base64' => 'nullable|array',
            'employee_user_id' => 'required|string|alpha_num',
            'op_registry_id' => 'nullable|integer'
        ]);
        try {
            $masterUserId = $request->employee_user_id;
            $existing = DB::table('hospitalization_details')
                ->where('master_user_id', $masterUserId)
                ->where('op_registry_id', $request->op_registry_id)
                ->where('role_id', 4)
                ->first();
            $data = [
                'op_registry_id' => $request->op_registry_id,
                'doctor_id' => $request->doctor_id,
                'doctor_name' => $request->doctor_name,
                'master_user_id' => $masterUserId,
                'hospital_id' => $request->hospital_id,
                'hospital_name' => $request->hospital_name,
                'from_datetime' => $request->from_date,
                'to_datetime' => $request->to_date,
                'description' => $request->description,
                'condition_id' => json_encode($request->condition),
                'other_condition_name' => null,
                'role_id' => 4,
                'attachment_discharge' => $request->discharge_summary_base64,
                'attachment_test_reports' => json_encode($request->summary_reports_base64),
                'updated_at' => now(),
            ];
            if ($existing) {
                DB::table('hospitalization_details')
                    ->where('master_user_id', $existing->master_user_id)
                    ->where('op_registry_id', $existing->op_registry_id)
                    ->where('role_id', 4)
                    ->update($data);
                $message = 'Hospitalization details updated successfully.';
            } else {
                $data['created_by'] = auth('api')->id();
                $data['created_at'] = now();
                DB::table('hospitalization_details')->insert($data);
                $message = 'Hospitalization details saved successfully.';
            }
            return response()->json([
                'result' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save/update hospitalization details', ['error' => $e->getMessage()]);
            return response()->json([
                'result' => false,
                'message' => 'Failed to process hospitalization details: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getHospitalizationDetailsById($employee_user_Id, $opRegistryId = null)
    {
        $query = DB::table('hospitalization_details')
            ->where('master_user_id', $employee_user_Id)
            ->where('role_id', 4);
        if ($opRegistryId) {
            $query->where('op_registry_id', $opRegistryId);
        }
        $details = $query->get();
        if ($details->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'No hospitalization details found'], 404);
        }
        $allConditions = DB::table('medical_condition')
            ->pluck('condition_name', 'condition_id')
            ->toArray();
        $details->transform(function ($item) use ($allConditions) {
            $conditionName = null;
            if (!empty($item->condition_id)) {
                $conditionIds = json_decode($item->condition_id, true);
                if (is_array($conditionIds)) {
                    $names = [];
                    foreach ($conditionIds as $id) {
                        if (isset($allConditions[$id])) {
                            $names[] = $allConditions[$id];
                        }
                    }
                    $conditionName = $names;
                } elseif (isset($allConditions[$item->condition_id])) {
                    $conditionName = [$allConditions[$item->condition_id]];
                }
            }
            $item->condition_names = $conditionName ?? [];
            return $item;
        });
        return response()->json(['result' => true, 'data' => $details], 200);
    }
}

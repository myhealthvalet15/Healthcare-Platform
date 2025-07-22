<?php

namespace App\Http\Controllers\UserEmployee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Prescription\Prescription;
use App\Models\Prescription\PrescriptionTemplate;
use App\Models\Prescription\PrescriptionDetails;
use App\Models\Prescription\PrescriptionTemplateDrug;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Department\CorporateHl1;
use App\Models\MasterCorporate;
use App\Models\PrescribedTest;
use App\Models\PrescribedTestData;
use App\Models\OpRegistry;
use App\Models\Hra\Master_Tests\MasterTest;
use App\Models\Corporate\MasterUser;
use App\Models\EventsResponse;
use App\Models\HospitalizationDetails;



class EmployeeUserController extends Controller
{
    public function getPrescriptionByEmployeeId($userId, Request $request)
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
                ->where('prescription.user_id', $userId)
                ->orderBy('prescription.prescription_row_id', 'desc')
                ->orderBy('prescription_details.prescription_details_id', 'asc')
                ->select(
                    'prescription.prescription_id',
                    'prescription.user_id',
                    'prescription.op_registry_id',
                    'prescription.master_doctor_id',
                    'prescription.role_id',
                    'prescription.template_id',
                    'prescription.prescription_attachments',
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

            $prescriptionTemplates = $prescriptionTemplatesQuery->get();
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
                     'prescription_attachments' => $prescription->prescription_attachments,
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
    public function getEmployeesDetailById($userId)
{
  //return 'hello';
       $employee = EmployeeUserMapping::with(['masterUser'])->where('employee_id', $userId)->first();
    if (!$employee) {
        return null;
    }
    //return $employee ;
    $masterUser = $employee->masterUser;
    return $masterUser;
    $employeeFirstname = $this->aes256DecryptData($masterUser->first_name ?? '');
    $employeeLastname = $this->aes256DecryptData($masterUser->last_name ?? '');
    $employeeGender = $this->aes256DecryptData($masterUser->gender ?? '');
    $dob = $this->aes256DecryptData($masterUser->dob ?? '');
    $employeeAge = $this->calculateAge($dob);
    $employeeEmail = $this->aes256DecryptData($masterUser->email ?? '');
    $employeeMobile = $this->aes256DecryptData($masterUser->mob_num ?? '');
    $employeeAadharId = $this->aes256DecryptData($masterUser->aadhar_id ?? '');
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
        'aadhar_id'                 => $employeeAadharId,
        'abha_id'                   => $employeeAbhaId,
        'area'                      => $employeeArea,
        'zipcode'                   => $employeeZipcode,
        'alternative_email'         => $employeeAlternativeEmail,
        'employee_user_id'          => $employee_userId
    ];
}

    private function calculateAge($dob)
    {
        return \Carbon\Carbon::parse($dob)->age;
    }
    public function listotcdetailsForEmployeeById($employee_id, Request $request)
    {
        try {
            $query = DB::table('prescription')
                ->join('prescription_details', 'prescription.prescription_id', '=', 'prescription_details.prescription_row_id')
                ->leftJoin('drug_template', 'prescription_details.drug_template_id', '=', 'drug_template.drug_template_id')
                ->leftJoin('employee_user_mapping', 'prescription.user_id', '=', 'employee_user_mapping.employee_id')
                ->leftJoin('corporate_admin_user', 'prescription.created_by', '=', 'corporate_admin_user.id')
                ->leftJoin('op_registry', 'prescription.op_registry_id', '=', 'op_registry.op_registry_id')
                ->where('employee_user_mapping.employee_id', $employee_id)
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
    public function getEmployeeTestForGraph($masterUserId, $masterTestId)
    {
        $opRegistryIds = OpRegistry::where('master_user_id', $masterUserId)
            ->pluck('op_registry_id');

        if ($opRegistryIds->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No OP Registry records found for the user.'
            ]);
        }
        $prescribedTests = PrescribedTest::whereIn('op_registry_id', $opRegistryIds)
            ->where('isVp', 0)
            ->get(['test_code', 'test_date']);

        if ($prescribedTests->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No prescribed tests with isVp = 0 found.'
            ]);
        }
        $testCodes = $prescribedTests->pluck('test_code');
        $testData = PrescribedTestData::whereIn('test_code', $testCodes)
            ->where('master_test_id', $masterTestId)
            ->get(['test_code', 'master_test_id', 'test_results']);

        if ($testData->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No test data found for given test codes and master_test_id.'
            ]);
        }
        $masterTest = MasterTest::find($masterTestId);

        if (!$masterTest) {
            return response()->json([
                'result' => false,
                'message' => 'Master test not found.'
            ]);
        }
        $testDates = $prescribedTests->pluck('test_date', 'test_code');
        $resultData = $testData->map(function ($item) use ($testDates, $masterTest) {
            return [
                'test_code' => $item->test_code,
                'master_test_id' => $item->master_test_id,
                'test_result' => $item->test_results,
                'test_date' => $testDates[$item->test_code] ?? null,
                'test_name' => $masterTest->test_name,
                'unit' => $masterTest->unit,
                'm_min_max' => $masterTest->m_min_max,
                'f_min_max' => $masterTest->f_min_max,
                'normal_values' => $masterTest->normal_values,
                'remarks' => $masterTest->remarks
            ];
        });
        return response()->json([
            'result' => true,
            'data' => $resultData
        ]);
    }
    public function updateEmployeesDetailById($master_user_user_id, Request $request)
    {
        $data = $request->only([
            'first_name',
            'last_name',
            'contact_number',
            'profile_pic',
            'banner',
            'date_of_birth',
            'gender',
            'aadhar_id',
            'abha_id',
            'area',
            'zipcode',
            'alternative_email'
        ]);
        $user = MasterUser::where('user_id', $master_user_user_id)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if (!empty($data['first_name'])) {
            $user->first_name = $this->aes256EncryptData(ucwords($data['first_name']));
            $user->first_name_hash = hash('sha256', trim($data['first_name']));
        }
        if (!empty($data['last_name'])) {
            $user->last_name = $this->aes256EncryptData(ucwords($data['last_name']));
            $user->last_name_hash = hash('sha256', trim($data['last_name']));
        }
        if (!empty($data['contact_number'])) {
            $user->mob_num = $this->aes256EncryptData(trim($data['contact_number']));
            $user->mobile_hash = $this->aes256EncryptDataWeak(trim($data['contact_number']));
        }
        if (!empty($data['profile_pic']) && $this->isValidBase64Image($data['profile_pic'])) {
            $user->user_profile_img = $data['profile_pic'];
        }
        if (!empty($data['banner']) && $this->isValidBase64Image($data['banner'])) {
            $user->user_banner_img = $data['banner'];
        }
        if (!empty($data['date_of_birth'])) {
            $user->dob = $this->aes256EncryptData($data['date_of_birth']);
        }
        if (!empty($data['gender'])) {
            $user->gender = $this->aes256EncryptData($data['gender']);
        }
        if (!empty($data['aadhar_id'])) {
            $user->aadhar_id = $this->aes256EncryptData($data['aadhar_id']);
        }
        if (!empty($data['abha_id'])) {
            $user->abha_id = $this->aes256EncryptData($data['abha_id']);
        }
        if (!empty($data['area'])) {
            $user->area = $this->aes256EncryptData($data['area']);
        }
        if (!empty($data['zipcode'])) {
            $user->zipcode = $this->aes256EncryptData($data['zipcode']);
        }
        if (!empty($data['alternative_email'])) {
            $user->alternative_email = $this->aes256EncryptData($data['alternative_email']);
        }
        $user->save();
        return response()->json(['message' => 'User profile updated successfully']);
    }
    function isValidBase64Image($base64)
    {
        return preg_match('/^data:image\/(png|jpeg|jpg|gif);base64,/', $base64);
    }
    private function aes256EncryptDataWeak($data)
    {
        $key = env('AES_256_ENCRYPTION_KEY');
        $encryptedEmail = DB::selectOne(
            "SELECT HEX(AES_ENCRYPT(?, UNHEX(?))) AS encrypted_email",
            [$data, $key]
        );
        $encryptedValue = $encryptedEmail->encrypted_email ?? null;
        return $encryptedValue;
    }
    private function aes256EncryptData(string $data = null)
    {
        if ($data === null) {
            return null;
        }
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $cipher = 'aes-256-cbc';
        $iv = random_bytes(openssl_cipher_iv_length($cipher));
        $encryptedData = openssl_encrypt($data, $cipher, $key, 0, $iv);
        if ($encryptedData === false) {
            throw new \Exception('Encryption failed');
        }
        return base64_encode($iv . $encryptedData);
    }
  public function getEventsforEmployeesByUserId($userId)
{
    
    $employeeInfo = $this->getEmployeesDetailById($userId);

    if (!$employeeInfo) {
        return response()->json([
            'result' => false,
            'message' => 'Employee details not found for the given user ID.'
        ], 404);
    }

    $employeeType = (string) ($employeeInfo['employee_type_id'] ?? '');
    $department = (string) ($employeeInfo['employee_department_id'] ?? '');

    // Step 1: Fetch matching events with event response status for this employee
    $events = DB::table('events')
        ->join('event_details', function ($join) use ($employeeType, $department) {
            $join->on('events.event_id', '=', 'event_details.event_row_id')
                ->whereRaw('JSON_CONTAINS(event_details.employee_type, ?)', [json_encode($employeeType)])
                ->whereRaw('JSON_CONTAINS(event_details.department, ?)', [json_encode($department)]);
        })
        ->leftJoin('event_responses', function ($join) use ($userId) {
            $join->on('events.event_id', '=', 'event_responses.event_id')
                 ->where('event_responses.user_id', '=', $userId);
        })
        ->orderBy('events.event_id', 'asc')
        ->get([
            'events.event_id',
            'events.from_datetime',
            'events.to_datetime',
            'events.event_name',
            'events.guest_name',
            'events.event_description',
            'event_details.test_taken',
            'event_details.event_row_id',
            'event_details.condition',
            'event_responses.status as response_status'
        ]);

    if ($events->isEmpty()) {
        return response()->json([
            'result' => false,
            'message' => 'No strictly matching events found for this employee.'
        ], 404);
    }

    // Step 2: Attach test names to each event
    foreach ($events as $event) {
        $testIds = json_decode($event->test_taken, true) ?? [];

        if (is_array($testIds) && count($testIds) > 0) {
            $tests = DB::table('master_test')
                ->whereIn('master_test_id', $testIds)
                ->pluck('test_name', 'master_test_id');

            $event->test_names = $tests;
        } else {
            $event->test_names = [];
        }
    }

    return response()->json([
        'result' => true,
        'data' => $events,
        'employee_type_id' => $employeeType,
        'employee_department_id' => $department,
        'employee_type_name' => $employeeInfo['employee_type_name'] ?? '',
        'employee_department_name' => $employeeInfo['employee_department_name'] ?? ''
    ], 200);
}
    
public function getEventDetails($userId)
{
    $employeeInfo = $this->getEmployeesDetailById($userId);
//return $employeeInfo;
    if (!$employeeInfo) {
        return response()->json([
            'result' => false,
            'message' => 'Employee details not found for the given user ID.'
        ], 404);
    }

    $employeeType = (string) ($employeeInfo['employee_type_id'] ?? '');
    $department = (string) ($employeeInfo['employee_department_id'] ?? '');
    $employee_userId = (string) $employeeInfo['employee_user_id'];
    // Step 1: Fetch matching events based on employee_type and department
    $events = DB::table('events')
        ->join('event_details', function ($join) use ($employeeType, $department) {
            $join->on('events.event_id', '=', 'event_details.event_row_id')
                ->whereRaw('JSON_CONTAINS(event_details.employee_type, ?)', [json_encode($employeeType)])
                ->whereRaw('JSON_CONTAINS(event_details.department, ?)', [json_encode($department)]);
        })
        ->leftJoin('event_responses', function ($join) use ($employee_userId) {
            $join->on('events.event_id', '=', 'event_responses.event_id')
                ->where('event_responses.user_id', '=', $employee_userId);
        })
        ->orderBy('events.event_id', 'desc')
        ->get([
            'events.event_id',
            'events.from_datetime',
            'events.to_datetime',
            'events.event_name',
            'events.guest_name',
            'events.event_description',
            'event_details.test_taken',
            'event_details.event_row_id',
            'event_details.condition',
            'event_responses.status as response_status' // Include response status
        ]);

    if ($events->isEmpty()) {
        return response()->json([
            'result' => false,
            'message' => 'No strictly matching events found for this employee.'
        ], 404);
    }

    // Step 2: Attach test names to each event
    foreach ($events as $event) {
        $testIds = json_decode($event->test_taken, true) ?? [];

        if (is_array($testIds) && count($testIds) > 0) {
            $tests = DB::table('master_test')
                ->whereIn('master_test_id', $testIds)
                ->pluck('test_name', 'master_test_id');

            $event->test_names = $tests;
        } else {
            $event->test_names = [];
        }
    }

    return response()->json([
        'result' => true,
        'data' => $events,
        'employee_type_id' => $employeeType,
        'employee_department_id' => $department,
        'employee_type_name' => $employeeInfo['employee_type_name'] ?? '',
        'employee_department_name' => $employeeInfo['employee_department_name'] ?? ''
    ], 200);
}
public function submitEventResponseByEmployeeId(Request $request)
{
    $validatedData = $request->validate([
        'event_id'     => 'required|integer|exists:events,event_id',
        'user_id'      => 'required|string',
        'response'       => 'required|in:yes,no',
        'corporate_id' => 'nullable|string',
    ]);

    try {
        $response = EventsResponse::updateOrCreate(
            [
                // These are the "where" conditions — check if this combination exists
                'event_id' => $validatedData['event_id'],
                'user_id'  => $validatedData['user_id']
            ],
            [
                // These will be inserted or updated
                'status'       => $validatedData['response'],
                'corporate_id' => $validatedData['corporate_id'] ?? null,
            ]
        );

        return response()->json([
            'result' => true,
            'message' => 'Event response submitted successfully.',
            'data' => $response
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'result' => false,
            'message' => 'An error occurred while submitting the event response.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getHospitalizationListByUserId($userId)
{
    try {
        // Step 1: Fetch all conditions in advance
        $allConditions = DB::table('medical_condition')
            ->pluck('condition_name', 'condition_id')
            ->toArray(); // ['51' => 'Diabetes', '52' => 'Hypertension', ...]

        // Step 2: Fetch hospitalization records
        $hospitalizations = DB::table('hospitalization_details')
            ->where('master_user_id', $userId)
            ->get([
                'hospitalization_details_id',
                'master_user_id',
                'hospital_id',
                'hospital_name',
                'doctor_id',
                'doctor_name',
                'from_datetime',
                'to_datetime',
                'description',
                'condition_id',
                'other_condition_name',
                'role_id',
                'attachment_discharge',
                'attachment_test_reports',
            ]);

        // Step 3: Transform records
        $hospitalizations = $hospitalizations->map(function ($item) use ($allConditions) {
            $conditionNames = [];

            if (!empty($item->condition_id)) {
                // Decode condition_id JSON safely
                $conditionIds = json_decode($item->condition_id, true);
                if (is_array($conditionIds)) {
                    foreach ($conditionIds as $id) {
                        if (isset($allConditions[$id])) {
                            $conditionNames[] = $allConditions[$id];
                        }
                    }
                }
            }

            return [
                'hospitalization_id'      => $item->hospitalization_details_id,
                'master_user_id'          => $item->master_user_id,
                'hospital_name'           => $item->hospital_name,
                'hospital_id'             => $item->hospital_id,
                'doctor_id'               => $item->doctor_id,
                'doctor_name'             => $item->doctor_name,
                'from_datetime'           => $item->from_datetime,
                'to_datetime'             => $item->to_datetime,
                'description'             => $item->description,
                'condition_names'         => $conditionNames,
                'other_condition_name'    => $item->other_condition_name,
                'role_id'                 => $item->role_id,
                'attachment_discharge'    => $item->attachment_discharge,
                'attachment_test_reports' => $item->attachment_test_reports,
            ];
        });

        if ($hospitalizations->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No hospitalization records found for this user.',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'data' => $hospitalizations,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'result' => false,
            'message' => 'An error occurred while fetching hospitalization records.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function getMedicalCondition()
{
    try {  
        $conditions = DB::table('medical_condition')
            ->select('condition_id', 'condition_name')
            ->get();

        if ($conditions->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No medical conditions found.'
            ], 404);
        }

        return response()->json([
            'result' => true,
            'data' => $conditions
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'result' => false,
            'message' => 'An error occurred while fetching medical conditions.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
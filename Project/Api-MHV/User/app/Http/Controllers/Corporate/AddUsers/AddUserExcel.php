<?php

namespace App\Http\Controllers\Corporate\AddUsers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\Controller;
use App\Models\MasterCorporate;
use App\Models\corporate_contractors;
use App\Models\Department\CorporateHl1;
use App\Models\Employee\EmployeeType;
use App\Models\Corporate\MasterUser;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Corporate\AddCorporate\AddCorporate;
use Illuminate\Support\Facades\Log;
use DateTime;
use Illuminate\Support\Facades\DB;

class AddUserExcel extends Controller
{
    private $corporate_id = null;
    private $location_id = null;
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

    public function getMasterUserCount()
    {
        try {
            $userDetailsCount = MasterUser::count();
            return response()->json([
                'data' => [
                    'success' => true,
                    'count' => $userDetailsCount
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve data'
            ], 500);
        }
    }
    public function getAddCorporateUploadCount(Request $request)
    {
        try {
            $totalRows = AddCorporate::count();
            $acceptedCount = AddCorporate::where('status', 'accepted')->count();
            $partialCount = AddCorporate::where('status', 'partial')->count();
            return response()->json([
                'data' => [
                    'success' => true,
                    'totalRows' => $totalRows,
                    'acceptedCount' => $acceptedCount,
                    'partialCount' => $partialCount,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve data',
            ], 500);
        }
    }
    public function getAddCorporateExcelFiles(Request $request)
    {
        try {
            $adminId = $request->user()['id'] ?? 0;
            $files = AddCorporate::where('user_id', $adminId)->orderBy('created_at', 'desc')->take(5)->get()->map(function ($file) {
                unset($file->file_base64);
                return $file;
            });
            return response()->json([
                'data' => [
                    'success' => true,
                    'files' => $files
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve data',
            ], 500);
        }
    }
    public function getAddCorporateExcelFileContent(Request $request, $id)
    {
        try {
            $user_id = $request->user()['id'] ?? 0;
            if (!is_numeric($id) || $user_id < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file id'
                ], 400);
            }
            $file = AddCorporate::where('id', $id)->where('user_id', $user_id)->first();
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            return response()->json([
                'data' => [
                    'success' => true,
                    'file' => $file
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve data'
            ], 500);
        }
    }
    private function addUsersExcelInitiate(Request $request)
    {
        $request->validate([
            'file' => 'required|string',
        ]);
        $base64File = $request->input('file');
        $decodedFileContent = base64_decode($base64File);
        if ($decodedFileContent === false) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid base64-encoded file.',
            ], 400);
        }
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('d-m-Y_h-i-s_A');
        $tempFilePath = storage_path("app/addCorporateExcelFiles/users-{$currentTime}.xlsx");
        if (!file_put_contents($tempFilePath, $decodedFileContent)) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to save temporary file.',
            ], 500);
        }
        return $this->validateUsersExcel($tempFilePath);
    }
    public function addUsersExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|string',
                'corporate_id' => 'required|string',
                'location_id' => 'required|string',
            ]);
            $corporate = MasterCorporate::where('corporate_id', $request->input('corporate_id'))
                ->where('location_id', $request->input('location_id'))
                ->first();
            if (!$corporate) {
                return response()->json([
                    'message' => 'Invalid Request: Corporate or Location not found'
                ], 404);
            }
            $this->corporate_id = $request->input('corporate_id');
            $this->location_id = $request->input('location_id');
            $tempFilePath = $this->addUsersExcelInitiate($request);
            if ($tempFilePath instanceof \Illuminate\Http\JsonResponse) {
                return $tempFilePath;
            }
            $data = $this->processExcelFile($tempFilePath, $request);
            $processedRows = $data['processedRows'];
            $skippedRows = $data['skippedRows'];
            $totalRows = $processedRows + $skippedRows;
            $currentTime = date('d-m-Y_h-i-s_A');
            $baseDirectory = storage_path('app/addCorporateExcelFiles/');
            $processedDirectory = $baseDirectory . 'Processed/';
            $unprocessedDirectory = $baseDirectory . 'UnProcessed/';
            foreach ([$baseDirectory, $processedDirectory, $unprocessedDirectory] as $dir) {
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
            }
            if ($skippedRows === 0 && $processedRows > 0 && $processedRows === $totalRows) {
                $processedFilePath = $processedDirectory . "Processed-fully-{$currentTime}.xlsx";
                rename($tempFilePath, $processedFilePath);
                $this->saveFileToDatabase(base64_encode(file_get_contents($processedFilePath)), AddCorporate::STATUS_ACCEPTED, "All rows are processed successfully.", $request);
            } elseif ($processedRows > 0 && $skippedRows > 0) {
                $processedFilePath = $processedDirectory . "Processed-partial-{$currentTime}.xlsx";
                $this->saveProcessedRowsToExcel($processedFilePath, $data['processedRowsData'], $data['headers']);
                $this->saveUnprocessedRowsToExcel($data['headers'], $data['unprocessedRows'], $type = 'partial', $request);
            } elseif ($processedRows === 0 && $skippedRows === $totalRows) {
                $this->saveUnprocessedRowsToExcel($data['headers'], $data['unprocessedRows'], 'fully', $request);
            } else {
                throw new \Exception('Invalid state');
            }
            return response()->json([
                'message' => 'Data upload completed',
                'processed_rows' => $processedRows,
                'skipped_rows' => $skippedRows,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error processing file: ' . $e->getMessage()], 500);
        } finally {
            if (isset($tempFilePath) && file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }
    public function addUsers(Request $request)
    {
        try {
            $request->validate([
                'corporate_id' => 'required|string',
                'location_id' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|in:Male,Female,Others',
                'dob' => 'required|date',
                'email' => 'required|email',
                'mob_country_code' => 'required|string',
                'mob_num' => 'required|string',
                'password' => 'required|string',
                'aadhar_id' => 'nullable|string',
                'abha_id' => 'nullable|string',
                'emp_id' => 'required|string',
                'emp_type' => 'required|integer',
                'other_id' => 'nullable|string',
                'department_id' => 'required|integer',
                'designation' => 'required|string',
                'from_date' => 'required|date',
            ]);
            if ($request->input('emp_type')) {
                $request->validate([
                    'contract_worker_id' => 'required|string',
                    'corporate_contractors_id' => 'required|integer',
                ]);
            }
            if ($request->input('aadhar_id') !== null) {
                if (!ctype_digit($request->input('aadhar_id')) || strlen($request->input('aadhar_id')) !== 12) {
                    return response()->json(['message' => 'Invalid Aadhar ID'], 400);
                }
            }
            if ($request->input('abha_id') !== null) {
                if (!ctype_digit($request->input('abha_id')) || strlen($request->input('abha_id')) !== 14) {
                    return response()->json(['result' => false, 'message' => 'Invalid Abha ID'], 400);
                }
            }
            if (!ctype_digit($request->input('mob_num')) || strlen($request->input('mob_num')) !== 10) {
                return response()->json(['result' => false, 'message' => 'Invalid Mobile Number'], 400);
            }
            $mobCountryCode = $request->input('mob_country_code');
            if (!ctype_digit(str_replace('+', '', ($mobCountryCode))) || strlen($mobCountryCode) > 4) {
                return response()->json(['result' => false, 'message' => 'Invalid Mobile Country Code....'], 400);
            }
            $corporate = MasterCorporate::where('corporate_id', $request->input('corporate_id'))
                ->where('location_id', $request->input('location_id'))
                ->first();
            if (!$corporate) {
                return response()->json(['message' => 'Invalid Request: Corporate or Location not found'], 404);
            }
            $emailHash = hash('sha256', $request->input('email'));
            $firstNameHash = $this->aes256EncryptDataWeak(strtolower($request->input('first_name')));
            $lastNameHash = $this->aes256EncryptDataWeak(strtolower($request->input('last_name')));
            $aadharHash = hash('sha256', $request->input('aadhar_id'));
            $abhaHash = hash('sha256', $request->input('abha_id'));
            $corporateLoginTables = explode(',', env('EMAIL_HASH_CHECK'));
            foreach ($corporateLoginTables as $modelClass) {
                $user = $modelClass::where('email_hash', $emailHash)->first();
                if ($user) {
                    return response()->json(['result' => false, 'message' => 'User with the same email already exists.'], 400);
                }
            }
            if ($request->input('aadhar_id')) {
                if (MasterUser::where('aadhar_hash', $aadharHash)->exists() && !empty($aadharHash)) {
                    return response()->json(['result' => false, 'message' => 'Aadhar ID already exists.'], 400);
                }
            }
            if ($request->input('abha_id')) {
                if (MasterUser::where('abha_hash', $abhaHash)->exists() && !empty($abhaHash)) {
                    return response()->json(['result' => false, 'message' => 'Abha ID already exists.'], 400);
                }
            }
            DB::beginTransaction();
            try {
                $newUserId = $this->generateUniqueUserId();
                $masterUser = MasterUser::create([
                    'user_id' => $newUserId,
                    'first_name' => $this->aes256EncryptData(ucwords($request->input('first_name'))),
                    'last_name' => $this->aes256EncryptData(ucwords($request->input('last_name'))),
                    'dob' => $this->aes256EncryptData($request->input('dob')),
                    'email' => $this->aes256EncryptData($request->input('email')),
                    'email_hash' => $emailHash,
                    'first_name_hash' => $firstNameHash,
                    'last_name_hash' => $lastNameHash,
                    'mob_country_code' => $request->input('mob_country_code'),
                    'mob_num' => $this->aes256EncryptData($request->input('mob_num')),
                    'mobile_hash' => $this->aes256EncryptDataWeak(trim($request->input('mob_num'))),
                    'password' => bcrypt($request->input('password')),
                    'aadhar_id' => $request->input('aadhar_id') ? $this->aes256EncryptData($request->input('aadhar_id')) : null,
                    'aadhar_hash' => $request->input('aadhar_id') ? $aadharHash : null,
                    'abha_id' =>  $request->input('abha_id') ? $this->aes256EncryptData($request->input('abha_id')) : null,
                    'abha_hash' => $request->input('aadhar_id') ? $abhaHash : null,
                    'gender' => $this->aes256EncryptData(strtolower($request->input('gender'))),
                ]);
                EmployeeUserMapping::create([
                    'user_id' => $newUserId,
                    'corporate_id' => $request->input('corporate_id'),
                    'location_id' => $request->input('location_id'),
                    'employee_id' => $request->input('emp_id'),
                    'employee_type_id' => $request->input('emp_type'),
                    'designation' => $request->input('designation'),
                    'corporate_contractors_id' => $request->input('corporate_contractors_id'),
                    'contract_worker_id' => $request->input('contract_worker_id'),
                    'other_id' => $request->input('other_id'),
                    'from_date' => $request->input('from_date'),
                    'hl1_id' => $request->input('department_id'),
                    'created_by' => "corporate_admin_user_id: " . $request->user()['corporate_admin_user_id'],
                ]);
                DB::commit();
                return response()->json(['result' => true, 'message' => 'User created successfully.'], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    private function prepareUserData(array $row, array &$skipReasons): array
    {
        $dateFormats = ['Y-m-d', 'd-m-Y', 'm-d-Y', 'Y/m/d', 'd/m/Y', 'm/d/Y'];
        $dobDate = $this->parseExcelDate($row['E'] ?? null, $dateFormats, $skipReasons, 'DOB');
        $from_date = $this->parseExcelDate($row['R'] ?? null, $dateFormats, $skipReasons, 'From Date');
        $mobCountryCode = str_replace('+', '', trim($row['G'] ?? ''));
        if (!ctype_digit($mobCountryCode) || strlen($mobCountryCode) > 5) {
            $skipReasons[] = "Invalid Mobile Country Code (Max 5 digits)";
            $mobCountryCode = false;
        }
        $mobCountryCode = '+' . $mobCountryCode;
        $mobNum = trim($row['H'] ?? '');
        if (!ctype_digit($mobNum) || strlen($mobNum) > 20) {
            $skipReasons[] = "Invalid Mobile Number (Max 20 digits)";
            $mobNum = false;
        }
        $validateStringFields = [
            'I' => 'password',
            'J' => 'emp_id',
            'M' => 'emp_type',
            'N' => 'other_id',
            'O' => 'corporate_contractors_id',
            'P' => 'contract_worker_id',
            'Q' => 'designation'
        ];
        foreach ($validateStringFields as $fieldKey => $fieldName) {
            if (isset($row[$fieldKey]) && !is_string($row[$fieldKey])) {
                $skipReasons[] = "Invalid {$fieldName} - Must be a string";
            }
        }
        $userData = [
            'first_name' => $this->validateStringField(trim(ucwords($row['B'] ?? '')), $skipReasons, 'First Name'),
            'last_name' => $this->validateStringField(trim(ucwords($row['C'] ?? '')), $skipReasons, 'Last Name'),
            'first_name_hash' => $this->aes256EncryptDataWeak($this->validateStringField(trim(strtolower($row['B'] ?? '')), $skipReasons, 'First Name')),
            'last_name_hash' => $this->aes256EncryptDataWeak($this->validateStringField(trim(strtolower($row['C'] ?? '')), $skipReasons, 'Last Name')),
            'mobile_hash' => $this->aes256EncryptDataWeak(trim($mobNum)),
            'gender' => $this->validateGender(strtolower($row['D']) ?? '', $skipReasons),
            'dob' => $this->isValidDate($dobDate, $dateFormats) ? $dobDate : false,
            'fromdate' => $this->isValidDate($from_date, $dateFormats) && $this->isBetweenYears($from_date, 1970, 2100) ? $from_date : false,
            'email' => $this->validateEmail(trim($row['F'] ?? ''), $skipReasons),
            'mob_country_code' => $mobCountryCode,
            'mob_num' => $mobNum,
            'password' => trim($row['I'] ?? ''),
            'emp_id' => $this->validateEmpId(trim($row['J'] ?? ''), $skipReasons),
            'emp_type' => trim($row['M'] ?? ''),
            'other_id' => trim($row['N'] ?? ''),
            'corporate_contractors_id' => trim($row['O'] ?? ''),
            'contract_worker_id' => trim($row['P'] ?? ''),
            'designation' => trim($row['Q'] ?? ''),
            'aadhar_id' => $this->validateAadhar(trim($row['S'] ?? '')),
            'abha_id' => $this->validateAbha(trim($row['T'] ?? '')),
        ];
        return $userData;
    }
    private function validateStringField($field, &$skipReasons, $fieldName)
    {
        if (empty($field)) {
            $skipReasons[] = "{$fieldName} cannot be null or empty";
            return false;
        }
        return $field;
    }
    private function validateGender($gender, &$skipReasons)
    {
        if (in_array(trim(strtolower($gender)), ['male', 'female', 'others', 'other'])) {
            return ucwords(strtolower($gender));
        } else {
            $skipReasons[] = "Invalid Gender, must be Male, Female, or Others";
            return false;
        }
    }
    private function validateEmail($email, &$skipReasons)
    {
        if (empty($email)) {
            $skipReasons[] = "Email cannot be null or empty";
            return false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $skipReasons[] = "Invalid email format";
            return false;
        }
        return $email;
    }
    private function validateEmpId($empId, &$skipReasons)
    {
        if (empty($empId)) {
            $skipReasons[] = "Employee ID cannot be null or empty";
            return false;
        }
        return $empId;
    }
    private function isBetweenYears($date, $minYear, $maxYear)
    {
        $year = (int) date('Y', strtotime($date));
        return $year >= $minYear && $year <= $maxYear;
    }
    private function validateAadhar($aadhar)
    {
        if (empty($aadhar)) {
            return null;
        }
        if (!ctype_digit($aadhar) || strlen($aadhar) !== 12) {
            return false;
        }
        return $aadhar;
    }
    private function validateAbha($abha)
    {
        if (empty($abha)) {
            return null;
        }
        if (!ctype_digit($abha) || strlen($abha) !== 14) {
            return false;
        }
        return $abha;
    }
    private function validateUsersExcel($tempFilePath)
    {
        if (!is_string($tempFilePath)) {
            throw new \Exception('Invalid file path');
        }
        if (!file_exists($tempFilePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $data = Excel::toArray([], $tempFilePath);
        if (empty($data) || empty($data[0])) {
            return response()->json([
                'result' => false,
                'message' => 'The uploaded Excel file is empty or invalid.'
            ], 400);
        }
        $numberOfSheets = count($data);
        $sheetData = $data[0];
        $requiredFields = explode(',', env('ADD_USERS_REQUIRED_FIELDS'));
        $headers = $sheetData[0];
        if (array_diff($requiredFields, $headers) || array_diff($headers, $requiredFields)) {
            return response()->json([
                'result' => false,
                'message' => 'The uploaded Excel file does not contain the required fields or has extra fields.'
            ], 422);
        }
        if ($numberOfSheets > 1) {
            return response()->json([
                'result' => false,
                'message' => 'The uploaded Excel file contains multiple sheets. Please upload a file with a single sheet.'
            ], 400);
        }
        if (count($sheetData) > 1100 || count($sheetData[0]) > 20) {
            return response()->json([
                'result' => false,
                'message' => 'The uploaded Excel file contains huge data. Please upload a file with a maximum of 1100 rows.'
            ], 400);
        }
        return $tempFilePath;
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
    private function saveMasterUser(array $masterUserData): string
    {
        try {
            $requiredFields = explode(',', env('ADD_USERS_MASTER_USER_REQUIRED_FIELDS'));
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (!in_array($field, ['aadhar_id', 'abha_id']) && (empty($masterUserData[$field]) || !isset($masterUserData[$field]))) {
                    $missingFields[] = $field;
                }
            }
            if (!empty($missingFields)) {
                throw new \Exception("Missing required fields SMU: " . implode(', ', $missingFields));
            }
            $userId = $this->generateUniqueUserId();
            $masterUserData['user_id'] = $userId;
            $emailHash = hash('sha256', $masterUserData['email']);
            $aadharHash = hash('sha256', $masterUserData['aadhar_id']);
            $abhaHash = hash('sha256', $masterUserData['abha_id']);
            if (MasterUser::where('aadhar_hash', $aadharHash)->exists() and !empty($aadharHash)) {
                throw new \Exception("Aadhar ID already exists.");
            }
            if (MasterUser::where('abha_hash', $abhaHash)->exists() and !empty($abhaHash)) {
                throw new \Exception("Abha ID already exists.");
            }
            $corporateLoginTables = explode(',', env('EMAIL_HASH_CHECK'));
            foreach ($corporateLoginTables as $modelClass) {
                $user = $modelClass::where('email_hash', $emailHash)->first();
                if ($user) {
                    throw new \Exception("Email Id already exists in our other records");
                }
            }
            $masterUserData['first_name'] = $this->aes256EncryptData($masterUserData['first_name']);
            $masterUserData['last_name'] = $this->aes256EncryptData($masterUserData['last_name']);
            $masterUserData['mob_num'] = $this->aes256EncryptData($masterUserData['mob_num']);
            $masterUserData['gender'] = $this->aes256EncryptData($masterUserData['gender']);
            $masterUserData['email'] = $this->aes256EncryptData($masterUserData['email']);
            $masterUserData['aadhar_id'] = $this->aes256EncryptData($masterUserData['aadhar_id']) ? $this->aes256EncryptData($masterUserData['aadhar_id']) : null;
            $masterUserData['abha_id'] = $this->aes256EncryptData($masterUserData['abha_id']) ? $this->aes256EncryptData($masterUserData['abha_id']) : null;
            $masterUserData['dob'] = $this->aes256EncryptData($masterUserData['dob']);
            $masterUserData['email_hash'] = $emailHash;
            $masterUserData['aadhar_hash'] = $masterUserData['aadhar_id'] ? $aadharHash : null;
            $masterUserData['abha_hash'] = $masterUserData['abha_id'] ? $abhaHash : null;
            $options = ['cost' => 8];
            $masterUserData['password'] = password_hash($masterUserData['password'], PASSWORD_BCRYPT, $options);
            MasterUser::create($masterUserData);
            return $userId;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    private function saveEmployeeUserMapping(array $EmployeeUserMappingData): void
    {
        try {
            $requiredFields = explode(',', env('ADD_USERS_SAVE_EMPLOYEE_USER_MAPPING_REQUIRED_FIELDS'));
            $missingFields = [];
            $mappingData = array_filter([
                'user_id' => $EmployeeUserMappingData['user_id'],
                'employee_id' => $EmployeeUserMappingData['employee_id'],
                'location_id' => $EmployeeUserMappingData['location_id'],
                'hl1_id' => $EmployeeUserMappingData['hl1_id'],
                'employee_type_id' => $EmployeeUserMappingData['employee_type_id'],
                'other_id' => $EmployeeUserMappingData['other_id'],
                'contract_worker_id' => $EmployeeUserMappingData['contract_worker_id'],
                'corporate_contractors_id' => $EmployeeUserMappingData['corporate_contractors_id'],
                'designation' => $EmployeeUserMappingData['designation'],
                'from_date' => $EmployeeUserMappingData['from_date'],
                'corporate_id' => $EmployeeUserMappingData['corporate_id'],
                'active_status' => $EmployeeUserMappingData['active_status'] ?? true,
                'created_by' => $EmployeeUserMappingData['created_by']
            ]);
            EmployeeUserMapping::create($mappingData);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    private function isValidDate($date, array $formats, $minYear = 1900, $maxYear = 2100): bool
    {
        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $date);
            if ($dt && $dt->format($format) === $date) {
                $year = (int) $dt->format('Y');
                if ($year >= $minYear && $year <= $maxYear) {
                    return true;
                }
            }
        }
        return false;
    }
    private function processExcelFile(string $filePath, $request): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            $headers = array_shift($rows);
            $headers[] = 'Error Details';
            $processedRows = 0;
            $skippedRows = 0;
            $unprocessedRows = [];
            $processedRowsData = [];
            $i = 0;
            foreach ($rows as $rowIndex => $row) {
                $userId = null;
                $skipReasons = [];
                try {
                    $corporateContractorsId = $this->extractContractorWorkingId($row['O']);
                    if (!$corporateContractorsId) {
                        $skipReasons[] = "Invalid corporate_contractors_id: {$row['O']}";
                    }
                    $employeeTypeId = $this->extractEmployeeTypeId($row['M']);
                    if (!$employeeTypeId) {
                        $skipReasons[] = "Invalid employee_type: {$row['M']}";
                    }
                    $departmentId = $this->extractDepartmentId($row['L']);
                    if (!$departmentId) {
                        $skipReasons[] = "Invalid department: {$row['L']}";
                    }
                    $userData = $this->prepareUserData($row, $skipReasons);
                    if (!empty($skipReasons)) {
                        throw new \Exception(implode(', ', $skipReasons));
                    }
                    $userId = $this->saveMasterUser($userData);
                    $createdBy = "corporate_admin_user_id: " . $request->user()['corporate_admin_user_id'];
                    $corporateUserMappingData = [
                        'user_id' => $userId,
                        'location_id' => $this->location_id,
                        'hl1_id' => $departmentId,
                        'employee_type_id' => $employeeTypeId,
                        'other_id' => $userData['other_id'],
                        'contract_worker_id' => $userData['corporate_contractors_id'],
                        'corporate_contractors_id' => $corporateContractorsId,
                        'designation' => $userData['designation'],
                        'from_date' => $userData['fromdate'],
                        'corporate_id' => $this->corporate_id,
                        'active_status' => true,
                        'employee_id' => $userData['emp_id'],
                        'created_by' => $createdBy
                    ];
                    $this->saveEmployeeUserMapping($corporateUserMappingData);
                    $processedRowsData[] = $row;
                    $processedRows++;
                } catch (\Exception $e) {
                    $row['Error Details'] = $e->getMessage();
                    $unprocessedRows[] = $row;
                    $skippedRows++;
                    if ($userId) {
                        $this->revokeMasterUser($userId);
                    }
                }
            }
            return [
                'processedRows' => $processedRows,
                'skippedRows' => $skippedRows,
                'processedRowsData' => $processedRowsData,
                'unprocessedRows' => $unprocessedRows,
                'headers' => $headers
            ];
        } catch (\Exception $e) {
            Log::error("Fatal error processing Excel file: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
    private function revokeMasterUser($userId): void
    {
        MasterUser::where('user_id', $userId)->delete();
    }
    private function extractEmployeeTypeId($employeeTypeText)
    {
        $employeeType = EmployeeType::whereRaw('LOWER(employee_type_name) = ?', [strtolower($employeeTypeText)])
            ->where('corporate_id', $this->corporate_id)
            ->first();
        return $employeeType ? $employeeType->employee_type_id : false;
    }
    private function extractDepartmentId($departmentText)
    {
        $department = CorporateHl1::whereRaw('LOWER(hl1_name) = ?', [strtolower($departmentText)])
            ->where('location_id', $this->location_id)
            ->first();
        return $department ? $department->hl1_id : false;
    }
    private function extractContractorWorkingId($contractorsWorkingText)
    {
        $contractor = corporate_contractors::whereRaw('LOWER(contractor_name) = ?', [strtolower($contractorsWorkingText)])
            ->where('location_id', $this->location_id)
            ->first();
        return $contractor ? $contractor->corporate_contractors_id : false;
    }
    private function generateUniqueUserId(): string
    {
        $maxAttempts = 10;
        $attempt = 0;
        do {
            $randomBytes = bin2hex(random_bytes(5));
            $userId = 'MU' . $randomBytes;
            $exists = MasterUser::where('user_id', $userId)->exists();
            $attempt++;
            if (!$exists) {
                return $userId;
            }
        } while ($attempt < $maxAttempts);
        throw new \Exception('Failed to generate unique user ID after ' . $maxAttempts . ' attempts');
    }
    private function parseExcelDate($value, array $formats, array &$skipReasons, string $fieldName): ?string
    {
        if (empty($value)) {
            $skipReasons[] = "$fieldName is empty";
            return null;
        }
        if (is_numeric($value)) {
            try {
                $unixTimestamp = ($value - 25569) * 86400;
                return date('Y-m-d', $unixTimestamp);
            } catch (\Exception $e) {
                $skipReasons[] = "Invalid Excel numeric date for $fieldName: $value";
                return null;
            }
        }
        foreach ($formats as $format) {
            $parsedDate = \DateTime::createFromFormat($format, $value);
            if ($parsedDate && $parsedDate->format($format) == $value) {
                return $parsedDate->format('Y-m-d');
            }
        }
        $skipReasons[] = "Unable to parse $fieldName: '$value'. Expected formats: " . implode(', ', $formats);
        return null;
    }
    private function saveUnprocessedRowsToExcel(array $headers, array $unprocessedRows, string $type = 'partial', Request $request)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $rowIndex = 2;
        foreach ($unprocessedRows as $row) {
            $sheet->fromArray($row, null, "A{$rowIndex}");
            $rowIndex++;
        }
        $currentTime = date('d-m-Y_h-i-s_A');
        $unprocessedFilePath = storage_path("app/addCorporateExcelFiles/UnProcessed/Unprocessed-{$type}-{$currentTime}.xlsx");
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($unprocessedFilePath);
        if ($type === 'fully') {
            $this->saveFileToDatabase(
                base64_encode(file_get_contents($unprocessedFilePath)),
                AddCorporate::STATUS_DENIED,
                "Entire Excel file is unprocessed. As the contents may already saved in the database or the file is invalid.",
                $request
            );
            return;
        }
        $this->saveFileToDatabase(
            base64_encode(file_get_contents($unprocessedFilePath)),
            AddCorporate::STATUS_PARTIAL,
            "File is partially processed. Please Download the file and check the 'Error Details' column for more information.",
            $request
        );
    }
    private function saveProcessedRowsToExcel(string $filePath, array $processedRows, array $headers = [])
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rowIndex = 1;
        if (!empty($headers)) {
            $sheet->fromArray($headers, null, "A{$rowIndex}");
            $rowIndex++;
        }
        foreach ($processedRows as $row) {
            $sheet->fromArray($row, null, "A{$rowIndex}");
            $rowIndex++;
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    }
    private function saveFileToDatabase($base64File, $status, $reason = null, $request)
    {
        try {
            AddCorporate::create([
                'user_id' => $request->user()['id'],
                'file_name' => 'users-' . date('d-m-Y_h-i-s_A') . '.xlsx',
                'file_base64' => $base64File,
                'status' => $status,
                'denied_reason' => $reason,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

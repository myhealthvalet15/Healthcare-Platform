<?php

namespace App\Http\Controllers\V1Controllers\CorporateController\AddUsers;

use App\Http\Controllers\Controller;
use App\Models\corporate_contractors;
use App\Models\corporate_hl1;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\V1Models\Corporate\MasterUser;
use App\Models\MasterCorporate;
use App\Models\V1Models\Corporate\EmployeeUserMapping;
use App\Models\V1Models\Corporate\AddCorporate\AddCorporate;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeeType;
use DateTime;
use Illuminate\Support\Facades\DB;
use Exception;
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
            $adminId = $request->user()['mhv_admin_id'] ?? 0;
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
            $adminId = $request->user()['mhv_admin_id'] ?? 0;
            if (!is_numeric($id) || $adminId < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Request'
                ], 400);
            }
            $file = AddCorporate::where('id', $id)->where('user_id', $adminId)->first();
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
            $data = $this->processExcelFile($tempFilePath);
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
            $firstNameHash = $this->aes256EncryptDataWeak(strtolower($masterUserData['first_name']));
            $lastNameHash = $this->aes256EncryptDataWeak(strtolower($masterUserData['last_name']));
            $mobileNumHash = $this->aes256EncryptDataWeak(strtolower($masterUserData['mob_num']));
            $masterUserData['first_name'] = $this->aes256EncryptData(ucwords($masterUserData['first_name']));
            $masterUserData['last_name'] = $this->aes256EncryptData(ucwords($masterUserData['last_name']));
            $masterUserData['mob_num'] = $this->aes256EncryptData($masterUserData['mob_num']);
            $masterUserData['gender'] = $this->aes256EncryptData($masterUserData['gender']);
            $masterUserData['email'] = $this->aes256EncryptData($masterUserData['email']);
            $masterUserData['aadhar_id'] = $masterUserData['aadhar_id'] ? $this->aes256EncryptData($masterUserData['aadhar_id']) : null;
            $masterUserData['abha_id'] = $masterUserData['abha_id'] ? $this->aes256EncryptData($masterUserData['abha_id']) : null;
            $masterUserData['dob'] = $this->aes256EncryptData($masterUserData['dob']);
            $masterUserData['email_hash'] = $emailHash;
            $masterUserData['first_name_hash'] = $firstNameHash;
            $masterUserData['last_name_hash'] = $lastNameHash;
            $masterUserData['aadhar_hash'] = $masterUserData['aadhar_id'] ? $aadharHash : null;
            $masterUserData['abha_hash'] = $masterUserData['abha_id'] ? $abhaHash : null;
            $masterUserData['mobile_hash'] = $mobileNumHash;
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
                'created_by' => 'admin'
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
    private function processExcelFile(string $filePath): array
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
        $department = corporate_hl1::whereRaw('LOWER(hl1_name) = ?', [strtolower($departmentText)])
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
                'user_id' => $request->user()['mhv_admin_id'],
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
     public function searchEmployeeDataByKeyword($keyword)
    {
       // return 'Hi';
        if (empty($keyword)) {
            return response()->json(['result' => false, 'message' => 'Invalid Requestss'], 400);
        }
      
        if (!ctype_alnum($keyword)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request.'], 400);
        }
        if (strlen($keyword) < 3) {
            return response()->json(['result' => false, 'message' => 'Keyword length shld be minimum of 3 charecters.'], 400);
        }
        $employees = $this->searchEmployeeDataDecrypted(null,null,$keyword);
        if ($employees->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'No matching employee data found'], 404);
        }
        return response()->json(['result' => true, 'message' => $employees]);
    } 
    
private function searchEmployeeDataDecrypted($corporate_id = null, $location_id = null, $keyword)
    {
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $results = collect();
        $employeeUserQuery = DB::table('employee_user_mapping');
            
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

    
}

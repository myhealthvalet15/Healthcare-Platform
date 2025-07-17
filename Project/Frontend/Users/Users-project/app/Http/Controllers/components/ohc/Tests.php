<?php

namespace App\Http\Controllers\components\ohc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Tests extends Controller
{
    public function displayTestListPage()
    {
        $headerData = 'Test List';
        return view('content.components.ohc.tests', ['HeaderData' => $headerData]);
    }
    public function displayTestDetailsPage($testCode = null)
    {
        if ($testCode === null || !is_numeric($testCode)) {
            return "Invalid Request";
        }
        $corporateId = session('corporate_id');
        $locationId = session('location_id');
        if (! $corporateId || ! $locationId) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getTestDetails/' . $corporateId . '/' . $locationId . '/' . $testCode);
        if ($response->successful()) {
            $testDetails = $response['data'];
            return view('content.components.ohc.test-details', ['HeaderData' => 'Test Details', 'testDetails' => $testDetails]);
        }
        return "Invalid Request";
    }
    public function displayTestDetailsPageDummy($testCode = null)
    {
        return view('content.components.ohc.test-details-templates', ['HeaderData' => 'Test Details']);
    }

    public function getAllTestsFromPrescribedTest(Request $request)
    {
        try {
            $corporateId = session('corporate_id');
            $locationId = session('location_id');
            $EmployeeuserId = session('master_user_user_id');

            if (!$corporateId || !$locationId) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllTestsFromPrescribedTest/'
                . $corporateId . '/' . $locationId . '/' . $EmployeeuserId);
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => $response['message']], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
    public function saveTestResults(Request $request)
    {
        $corporateId = session('corporate_id');
        $locationId = session('location_id');
        if (!$corporateId || !$locationId) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $validatedData = $request->validate([
            'employee_id' => 'required|string',
            'test_results' => 'required|array',
            'test_results.*.master_test_id' => 'required|integer',
            'test_results.*.test_result' => 'nullable|string',
            'test_results.*.test_code' => 'required|integer',
            'reported_on' => 'nullable|date_format:Y-m-d\TH:i',
            'tested_on' => 'nullable|date_format:Y-m-d\TH:i',
            'document_file' => 'nullable|string',
            'document_filename' => 'nullable|string|max:255',
        ]);
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
        $apiData = [
            'corporate_id' => $corporateId,
            'location_id' => $locationId,
            'employee_id' => $validatedData['employee_id'],
            'test_results' => $validatedData['test_results'],
            'reported_on' => $validatedData['reported_on'] ?? null,
            'tested_on' => $validatedData['tested_on'] ?? null,
        ];
        if ($fileData) {
            $apiData['document_file'] = $fileData['base64'];
            $apiData['document_filename'] = $fileData['filename'];
            $apiData['document_mime_type'] = $fileData['mime_type'];
            $apiData['document_size'] = $fileData['size'];
        }
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . request()->cookie('access_token'),
                ])
                ->post('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/saveTestResults', $apiData);
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => 'Test Results Saved Successfully']);
            }
            return response()->json([
                'result' => false,
                'message' => $response->json('message') ?? 'Failed to save test results'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Server error occurred while saving test results'
            ], 500);
        }
    }
    /**
     * Validate and process file data
     *
     * @param array $validatedData
     * @return array|false|null
     */
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
    /**
     * Check if string is valid base64
     *
     * @param string $data
     * @return bool
     */
    private function isValidBase64($data)
    {
        return base64_encode(base64_decode($data, true)) === $data;
    }
    /**
     * Get MIME type from file content and extension
     *
     * @param string $content
     * @param string $extension
     * @return string
     */
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
    /**
     * Validate file signature (magic bytes)
     *
     * @param string $content
     * @param string $extension
     * @return bool
     */
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
}

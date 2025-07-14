<?php

namespace App\Http\Controllers\corporate;

use App\Exports\ExportExcel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use DateTime;

class addCorporateUsers extends Controller
{
    protected function convertArrayToHtmlTable($array)
    {
        $html = '<!DOCTYPE html><html><head><title>Excel Data</title>';
        $html .= '<style>';
        $html .= 'table {border-collapse: collapse; width: 100%;}';
        $html .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $html .= 'tr {border-bottom: 1px solid black;}';
        $html .= 'th {background-color: #f2f2f2; font-weight: bold;}';
        $html .= '</style>';
        $html .= '</head><body>';
        $html .= '<table>';
        $html .= '<thead><tr>';
        if (isset($array[0])) {
            foreach (array_keys($array[0]) as $header) {
                $html .= "<th>{$header}</th>";
            }
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        foreach ($array as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table></body></html>';
        return $html;
    }
    private function convertToXlsx($sheetData)
    {
        if (empty($sheetData)) {
            throw new \Exception('No data provided for Excel conversion.');
        }
        $headers = array_keys($sheetData[0]);
        array_unshift($sheetData, $headers);
        $fileName = 'data_' . Str::random(10);
        $exportData = new ExportExcel($sheetData);
        $excelFilePath = storage_path("app/public/addCorporateExcelFiles/{$fileName}.xlsx");
        Excel::store($exportData, "addCorporateExcelFiles/{$fileName}.xlsx", 'public');
        return $excelFilePath;
    }

    private function extractSheetsCumValidate($file)
    {
        $validator = Validator::make(['file' => $file], [
            'file' => 'required|file|mimes:xls,xlsx|max:5120'
        ]);
        if ($validator->fails()) {
            return [
                'result' => 'failed',
                'message' => $validator->errors(),
                'status' => 422
            ];
        }
        $data = Excel::toArray([], $file);
        if (empty($data) || empty($data[0])) {
            return response()->json([
                'result' => 'failed',
                'message' => 'The uploaded Excel file is empty or invalid.'
            ], 400);
        }
        $numberOfSheets = count($data);
        if ($numberOfSheets > 1) {
            return [
                'result' => 'failed',
                'message' => 'The uploaded Excel file contains multiple sheets. Please upload a file with a single sheet.',
                'status' => 400
            ];
        }
        $sheetData = array_filter($data[0], function ($row) {
            return array_filter($row);
        });
        $headers = array_filter(
            array_map('strtolower', array_map('trim', array_shift($sheetData))),
            function ($value) {
                return $value !== null && trim($value) !== '';
            }
        );
        $sheetData = array_map(function ($row) {
            return array_slice($row, 0, 20);
        }, $sheetData);
        if (count($sheetData) > 1100 || count($sheetData[0]) > 20) {
            return [
                'result' => 'failed',
                'message' => 'The uploaded Excel file contains huge data. Please upload a file with a maximum of 1100 rows.',
                'status' => 400
            ];
        }
        $requiredFields = explode(',', env('ADD_USERS_REQUIRED_FIELDS'));
        if ($missingFields = array_diff($requiredFields, $headers)) {
            return [
                'result' => 'failed',
                'message' => 'The uploaded Excel file is missing required fields: ' . implode(', ', $missingFields),
                'status' => 422
            ];
        }
        $fieldMapping = array_flip($headers);
        $validRows = [];
        $allrows = [];
        $errors = [];
        foreach ($sheetData as $index => $row) {
            $rowNumber = $index + 1;
            $mappedRow = [];
            foreach ($requiredFields as $field) {
                $mappedRow[$field] = $row[$fieldMapping[$field] ?? null] ?? null;
            }
            $mappedRow['row_number'] = $rowNumber;
            $mappedRow['dob'] = isset($mappedRow['dob']) ? $this->normalizeDate(trim($mappedRow['dob'])) : null;
            $mappedRow['fromdate'] = isset($mappedRow['fromdate']) ? $this->normalizeDate(trim($mappedRow['fromdate'])) : null;
            $mappedRow['aadhar_id'] = isset($mappedRow['aadhar_id']) ? str_replace(' ', '', $mappedRow['aadhar_id']) : null;
            $mappedRow['abha_id'] = isset($mappedRow['abha_id']) ? str_replace(' ', '', $mappedRow['abha_id']) : null;
            $mappedRow['gender'] = isset($mappedRow['gender']) ? strtolower(trim($mappedRow['gender'])) : null;
            $mappedRow['mob_country_code'] = isset($mappedRow['mob_country_code'])
                ? (int) str_replace('+', '', trim($mappedRow['mob_country_code']))
                : null;
            $validationRules = [
                'row_number' => 'required|integer',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|in:male,female,other',
                'dob' => 'required|date|after:1969-12-31|before:2101-01-01',
                'email' => 'required|email',
                'mob_country_code' => 'required|integer',
                'mob_num' => 'required|digits_between:10,15',
                'password' => 'required|string',
                'emp_id' => 'required|string',
                'location' => 'required|string',
                'department' => 'required|string',
                'emp_type' => 'required|string',
                'designation' => 'required|string',
                'fromdate' => 'required|date|after:1969-12-31|before:2101-01-01',
                'aadhar_id' => 'nullable|digits:12',
                'abha_id' => 'nullable|digits:14',
            ];
            $validator = Validator::make($mappedRow, $validationRules);
            $allrows[] = $mappedRow;
            if ($validator->fails()) {
                $errors[] = [
                    'row_number' => $rowNumber,
                    'errors' => $validator->errors()->toArray()
                ];
            } else {
                $validRows[] = $mappedRow;
            }
        }
        usort($errors, function ($a, $b) {
            return $a['row_number'] <=> $b['row_number'];
        });
        return [
            'result' => empty($errors) ? 'success' : 'partial_success',
            'message' => empty($errors)
                ? 'File processed successfully.'
                : 'File processed with some errors.',
            'allrows' => $allrows,
            'valid_rows' => $validRows,
            'errors' => $errors,
            'status' => 200
        ];
    }
    private function normalizeDate($date)
    {
        if (empty($date)) {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            return $dateObj ? $dateObj->format('Y-m-d') : null;
        }
        if (is_numeric($date)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        $formats = ['d/m/Y', 'Y/m/d', 'm/d/Y', 'd-m-Y', 'Y-m-d', 'm-d-Y'];
        foreach ($formats as $format) {
            $dateObj = DateTime::createFromFormat($format, $date);
            if ($dateObj !== false) {
                return $dateObj->format('Y-m-d');
            }
        }
        return null;
    }
    public function import(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'file' => 'required|file|mimes:xls,xlsx|max:5120',
                'corporate_id' => 'required|string',
                'location_id' => 'required|string',
            ])->validate();
            $result = $this->extractSheetsCumValidate($request->file('file'));
            if ($result['result'] === 'failed') {
                return response()->json([
                    'result' => 'failed',
                    'message' => $result['message']
                ], $result['status']);
            }
            $validRows = $result['valid_rows'];
            $htmlTable = $this->convertArrayToHtmlTable($result['allrows']);
            $excelFilePath = $this->convertToXlsx($result['allrows']);
            $fileName = pathinfo($excelFilePath, PATHINFO_FILENAME);
            $htmlFilePath = storage_path("app/public/addCorporateExcelFiles/{$fileName}.html");
            File::put($htmlFilePath, $htmlTable);
            return response()->json([
                'result' => 'success',
                'message' => 'File processed successfully.',
                'file_name' => $fileName,
                'total_rows' => count($result['allrows']),
                'valid_rows' => count($validRows),
                'errors' => count($result['errors'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'Failed to process the file. Please check the file content and try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function viewExcelData($corporate_id, $location_id, $fileName)
    {
        if (!is_string($corporate_id) || !is_string($location_id) || !is_string($fileName)) {
            return view('content.not-found.invalid-data');
        }
        $filePath = storage_path('app/public/addCorporateExcelFiles/' . $fileName . '.html');
        if (!File::exists($filePath)) {
            return view('content.not-found.invalid-data');
        }
        $htmlContent = File::get($filePath);
        return view('content.corporate.view-excel', compact('htmlContent', 'fileName'));
    }
    public function deleteExcelData(Request $request)
    {
        $fileName = $request->input('file_name');
        $htmlFilePath = storage_path("app/public/addCorporateExcelFiles/{$fileName}.html");
        $excelFilePath = storage_path("app/public/addCorporateExcelFiles/{$fileName}.xlsx");
        try {
            if (File::exists($htmlFilePath)) {
                File::delete($htmlFilePath);
            }
            if (File::exists($excelFilePath)) {
                File::delete($excelFilePath);
            }
            return response()->json([
                'result' => 'success',
                'message' => 'Files deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'Failed to delete the files.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getMasterUserDetailsCount(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/getMasterUserDetails');
            if ($response->status() === 401) {
                return response()->json(['result' => 'error', 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                return response()->json(['result' => 'success', 'data' => $response['data']]);
            }
            return response()->json(['result' => 'failed', 'error' => 'error to fetch data'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function displayImportView()
    {
        return view('content.corporate.add-corporate');
    }
    private function validateAndProcessExcel(Request $request)
    {
        $request->validate([
            'file_name' => 'string|required',
        ]);
        $fileName = $request->input('file_name');
        $filePath = storage_path('app/public/addCorporateExcelFiles/' . $fileName) . ".xlsx";
        if (!file_exists($filePath)) {
            return response()->json(['result' => 'Failed', 'message' => 'File not found'], 404);
        }
        $data = Excel::toArray([], $filePath);
        if (empty($data) || empty($data[0])) {
            return response()->json([
                'result' => 'failed',
                'message' => 'The uploaded Excel file is empty or invalid.'
            ], 400);
        }
        $numberOfSheets = count($data);
        if ($numberOfSheets > 1) {
            return response()->json([
                'result' => 'failed',
                'message' => 'The uploaded Excel file contains multiple sheets. Please upload a file with a single sheet.'
            ], 400);
        }
        $sheetData = array_filter($data[0], function ($row) {
            return array_filter($row);
        });
        if (count($sheetData) > 1100 || count($sheetData[0]) > 20) {
            return [
                'result' => 'failed',
                'message' => 'The uploaded Excel file contains huge data. Please upload a file with a maximum of 1100 rows.',
                'status' => 400
            ];
        }
        $headers = array_map('strtolower', array_map('trim', array_shift($sheetData)));
        $requiredFields = explode(',', env('ADD_USERS_REQUIRED_FIELDS'));
        if ($missingFields = array_diff($requiredFields, $headers)) {
            return [
                'result' => 'failed',
                'message' => 'The uploaded Excel file is missing required fields: ' . implode(', ', $missingFields),
                'status' => 422
            ];
        }
        $fileContents = file_get_contents($filePath);
        return [
            'filePath' => $filePath,
            'fileContents' => $fileContents,
            'rowCount' => count($sheetData) - 1,
        ];
    }
    public function sendExcelData(Request $request)
    {
        try {
            $request->validate([
                'corporateid' => 'string|required',
                'locationid' => 'string|required',
            ]);
            $fileProcessingResult = $this->validateAndProcessExcel($request);
            if (is_a($fileProcessingResult, \Illuminate\Http\JsonResponse::class)) {
                return $fileProcessingResult;
            }
            $filePath = $fileProcessingResult['filePath'];
            $fileContents = $fileProcessingResult['fileContents'];
            $rowCount = $fileProcessingResult['rowCount'];
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/add-corporate-users/addBulkData', [
                'file' => base64_encode($fileContents),
                'corporate_id' => $request->input('corporateid'),
                'location_id' => $request->input('locationid'),
            ]);
            if ($response->successful()) {
                $this->cleanUpFiles($filePath);
                if ($response['processed_rows'] == $rowCount) {
                    return response()->json([
                        'result' => 'success',
                        'message' => $response
                    ], 200);
                }
                return response()->json(['result' => 'success', 'message' => 'File successfully sent!'], 200);
            }
            return response()->json(['result' => 'failed', 'message' => 'Failed sending file to API'], 500);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'Error sending file to API',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function cleanUpFiles($filePath)
    {
        $htmlFilePath = str_replace('.xlsx', '.html', $filePath);
        if (File::exists($htmlFilePath)) {
            File::delete($htmlFilePath);
        }
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
    public function getUploadedExcelStatus(Request $request)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/getAddUserStatus');
        if ($response->getStatusCode() == 200) {
            return response()->json(['result' => 'success', 'data' => $response->json()]);
        }
        return response()->json(['result' => 'failed', 'message' => 'Error fetching data'], $response->getStatusCode());
    }
    public function getUploadedExcelFileContent(Request $request, $id)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/getAddUserStatusFileContent/' . $id);
        if ($response->getStatusCode() == 200) {
            return response()->json(['result' => 'success', 'data' => $response->json()['data']['file']]);
        }
        return response()->json(['result' => 'failed', 'message' => 'Error fetching data'], $response->getStatusCode());
    }
    public function getAllCorporates(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/api/corporates');
            if ($response->status() === 401) {
                return response()->json(['result' => true, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']) && !empty($responseData['data'])) {
                    return response()->json(['result' => true, 'data' => $responseData['data']]);
                } else {
                    return response()->json(['result' => true, 'data' => []], 200);
                }
            }
            return response()->json(['success' => false, 'error' => 'Error fetching data'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
    public function getAllLocations($id, Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/api/corporates/' . $id . "/locations");
            if ($response->status() === 401) {
                return response()->json(['result' => false, 'message' => 'Unauthenticated'], 401);
            }
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']) && !empty($responseData['data'])) {
                    return response()->json(['result' => true, 'data' => $responseData['data']]);
                } else {
                    return response()->json(['result' => true, 'data' => []], 200);
                }
            }
            return response()->json(['success' => false, 'error' => 'Error fetching data'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }  public function displaySearchUsers()
    {
        $headerData = "Search Users";
        return view('content.corporate.search-user', ['HeaderData' => $headerData]);
    }   public function searchEmployegetDataByKeyword($keyword, Request $request)
    {
       //return 'Hi';
        try {
           
            if (! $keyword or !ctype_alnum($keyword)) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/getEmployeeData/'. $keyword);
          return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
  
}

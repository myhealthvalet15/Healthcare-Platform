<?php
namespace App\Http\Controllers\UserEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class EmployeeUserController extends Controller
{
    public function index()
    {
        $headerData = 'Employee Dashboard';
        return view('content.UserEmployee.user_dashboard', ['HeaderData' => $headerData]);
    }

    public function showPersonalInfo()
    {
        $headerData = 'Personal Information';
        return view('content.UserEmployee.user_employee_personal_information', ['HeaderData' => $headerData]);
    }
    public function showPrescription()
    {
        $headerData = 'Prescription';
        return view('content.UserEmployee.user_employee_prescription', ['HeaderData' => $headerData]);
    }
    public function getuserPrescription(Request $request)
    {
        $employeeid = $request->employee_id;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/master-user/masteruser/getPrescriptionByEmployeeId/' . $employeeid);
            return $response;
            if ($response->successful()) {

                $prescription = $response['data'];

                return response()->json($prescription);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getuserPrescriptionForOtc(Request $request)
    {
        $employeeid = $request->employee_id;
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/master-user/masteruser/getPrescriptionForOtcByEmployeeId/' . $employeeid);
            return $response;
            if ($response->successful()) {

                $prescription = $response['data'];

                return response()->json($prescription);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function showCorporateData()
    {
        $headerData = 'Corporate data';
        return view('content.UserEmployee.user_employee_corporate_data', ['HeaderData' => $headerData]);
    }

    public function showHealthMonitoring()
    {
        $headerData = 'Health Monitoring';
        return view('content.UserEmployee.user_health_monitoring', ['HeaderData' => $headerData]);
    }
    public function showReports()
    {
        $headerData = 'Reports';
        return view('content.UserEmployee.user_employee_reports', ['HeaderData' => $headerData]);
    }
    public function showReportsInGraph()
    {
        $headerData = 'Reports';
        return view('content.UserEmployee.user_employee_reports_graph', ['HeaderData' => $headerData]);
    }
    public function showReportsInGraphForMutipleTest()
    {
        $headerData = 'Reports';
        return view('content.UserEmployee.user_employee_reports_graph_multiple_test', ['HeaderData' => $headerData]);
    }
    public function showSettings()
    {
        $headerData = 'Settings';
        return view('content.UserEmployee.user_employee_settings', ['HeaderData' => $headerData]);
    }
    public function diagnosticAssesment()
    {
        $headerData = 'Diagnostic Assesment';
        return view('content.UserEmployee.user_employee_diagnosticassesment', ['HeaderData' => $headerData]);
    }
    public function healthRiskAssesment()
    {
        $headerData = 'Health Risk Assesment';
        return view('content.UserEmployee.user_employee_hra', ['HeaderData' => $headerData]);
    }
    public function events()
    {
        $headerData = 'Events';
        return view('content.UserEmployee.user_employee_events', ['HeaderData' => $headerData]);
    }

    public function otc()
    {
        $headerData = 'OTC';
        return view('content.UserEmployee.user_employee_otc', ['HeaderData' => $headerData]);
    }
    public function showTest()
    {
        $headerData = 'Test';
        return view('content.UserEmployee.user_employee_test', ['HeaderData' => $headerData]);
    }
    public function hospitalisationDetails()
    {
        $headerData = 'Hospitalization Details';
        return view('content.UserEmployee.user_employee_hospitalization', ['HeaderData' => $headerData]);
    }
    public function userMedicalCondition()
    {
        $headerData = 'Medical Condition';
        return view('content.UserEmployee.user_employee_medicalCondition', ['HeaderData' => $headerData]);
    }
    public function getemployeeDetails(Request $request)
    {
        $employeeId = session('employee_id');
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/master-user/masteruser/getEmployeesDetailById/' . $employeeId);
            if ($response->successful()) {
                $employee_details = $response->json();
                return response()->json($employee_details);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function listotcdetailsForEmployee(Request $request)
    {
        $locationId = session('location_id');
        $employee_id = strtolower(session('employee_id'));
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Requestsssss'
            ]);
        }
        try {
            $url = 'https://api-user.hygeiaes.com/V1/master-user/masteruser/listotcdetailsForEmployeeById/' . $employee_id;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get($url);

            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }

            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }
    public function displayRegistryOutpatientPage(Request $request, $employee_id = null, $op_registry_id = null)
    {
        if ($op_registry_id !== null && !is_numeric($op_registry_id)) {
            return response()->json(['error' => 'Invalid Registry ID'], 400);
        }

        if (!$employee_id || !ctype_alnum($employee_id)) {
            return response()->json(['error' => 'Invalid Employee ID'], 400);
        }
        $url = 'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/0/' . $employee_id;
        if ($op_registry_id !== null) {
            $url .= "/op/" . $op_registry_id;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();
            if (!isset($data['result']) || !$data['result']) {
                return response()->json(['error' => 'Invalid Data'], 400);
            }
            if (request()->ajax()) {
                return response()->json($data['message']);
            }
            return view('content.UserEmployee.user_employee_outpatient', [
                'HeaderData' => 'Out Patient',
                'employeeData' => $data['message']
            ]);
        }

        return response()->json(['error' => 'Request failed'], 500);
    }
    public function outPatient()
    {
        $headerData = 'Out Patient';
        return view('content.UserEmployee.user_employee_outpatient', ['HeaderData' => $headerData]);
    }
    public function getEmployeeTestForGraph($master_user_id, $test_id, Request $request)
    {
        if (!$master_user_id || !$test_id) {
            return response()->json([
                'result' => false,
                'message' => 'Missing required parameters.'
            ]);
        }
        try {
            $url = "https://api-user.hygeiaes.com/V1/master-user/masteruser/getEmployeeTestForGraph/{$master_user_id}/{$test_id}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get($url);
            return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }

            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }
    public function updateProfileDetails(Request $request)
    {
        $master_user_user_id = session('master_user_user_id');
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'aadhar_id' => 'nullable|string|max:20',
            'abha_id' => 'nullable|string|max:50',
            'alternative_email' => 'nullable|email|max:100',
            'area' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:10',
            'profile_pic' => 'nullable|string',
            'banner' => 'nullable|string'
        ]);

        function cleanBase64($base64)
        {
            if (empty($base64)) return null;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64)) {
                return $base64;
            }
            return null;
        }

        if (!empty($validated['profile_pic'])) {
            $cleanedProfilePic = cleanBase64($validated['profile_pic']);
            if ($cleanedProfilePic) {
                $validated['profile_pic'] = $cleanedProfilePic;
            } else {
                return response()->json(['error' => 'Invalid profile_pic base64 format'], 400);
            }
        }
        if (!empty($validated['banner'])) {
            $cleanedBanner = cleanBase64($validated['banner']);
            if ($cleanedBanner) {
                $validated['banner'] = $cleanedBanner;
            } else {
                return response()->json(['error' => 'Invalid banner base64 format'], 400);
            }
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post("https://api-user.hygeiaes.com/V1/master-user/masteruser/updateEmployeesDetailById/{$master_user_user_id}", $validated);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }
    public function getEventsforEmployees(Request $request)
    {
        //sreturn 'Hi';
        $master_user_user_id = session('employee_id');
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/master-user/masteruser/getEventsforEmployees/' . $master_user_user_id);
           // return $response;
            if ($response->successful()) {
                $events = $response['data'];
                return response()->json($events);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getEventDetails(Request $request)
    {
        $employeeId = session('employee_id');
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/master-user/masteruser/getEventDetails/' . $employeeId);
            return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
        }
    }

     public function showTestAdd(Request $request)
   {
    $empId = $request->query('emp');

    // Fetch employee data (adjust table/column names if needed)
    $employeeData = DB::table('employees')->where('employee_id', $empId)->first();

    return view('content.components.ohc.others.testadd', compact('employeeData'));
   }
   public function submitResponse(Request $request)
    { 
       
        $response = $request->validate([
            'event_id' => 'required|integer',
            'response' => 'required|string|in:yes,no',
        ]);
        $response['corporate_id'] = session('corporate_id');
        try {
            $apiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/master-user/masteruser/submitEventResponse', $response);
         //  return $apiResponse;
            if ($apiResponse->successful()) {
                return response()->json(['result' => true, 'message' => 'Response submitted successfully']);
            } else {
                return response()->json(['result' => false, 'message' => 'Failed to submit response'], $apiResponse->status());
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    } public function getEventFromEmail(Request $request)
    {
       //ssss return $request;
       
       $eventId = $request->query('event_id');
    $userId = $request->query('user_id');

    // Pass both to the Blade view
    return view('content.UserEmployee.user_employee_event_response', compact('eventId', 'userId'));
        }

   
}

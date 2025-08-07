<?php

namespace App\Http\Controllers\CorporateUsers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CorporateUserController extends Controller
{
    public function UserList(Request $request)
    {
        $headerData = 'Corporate Users List';
        return view('content.CorporateUsers.user-list', ['HeaderData' => $headerData]);
    }
    public function getUserDetails(Request $request)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        //return $corporateId;
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),

            ])->get('https://api-user.hygeiaes.com/V1/corporate-users/users/getAllUsersDetails/' . $corporateId . '/' . $locationId, $request->all());

            // Get the data from the response
            $data = $response->json()['data'];
            // return $data;
            return response()->json([
                'result' => true,
                'data' => array_values($data) // Reset array keys after filtering
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    public function userAdd(Request $request)
    {
        $headerData = 'Add New User';
        return view('content.CorporateUsers.add-corporate-user', ['HeaderData' => $headerData]);
    }

    public function insertUser(Request $request)
    {

        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        if (!$locationId  || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        $requestData['corporate_admin_user_id'] = $userId;
        $requestData['password'] = "peter@123";

        try {
            $validated = $request->validate([

                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'nullable|string',
                'mobile_country_code' => 'required|string',
                'email' => 'required|email',
                'aadhar' => 'nullable|string',
                'mobile_num' => 'required|integer',
                'setting' => 'nullable|string',

            ]);
            if ($request->input('aadhar') !== null) {
                if (!ctype_digit($request->input('aadhar')) || strlen($request->input('aadhar')) !== 12) {
                    return response()->json([
                        'message' => 'Invalid Aadhar ID'
                    ], 400);
                }
            }

            if (!ctype_digit($request->input('mobile_num')) || strlen($request->input('mobile_num')) !== 10) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Mobile Number'
                ], 400);
            }
            $mobCountryCode = $request->input('mobile_country_code');
            if (!ctype_digit(str_replace('+', '', ($mobCountryCode))) || strlen($request->input('mobile_country_code')) > 4) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Mobile Country Code'
                ], 400);
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate-users/users/addCorporateUSer', $requestData);

            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => 'Data Added Successfully']);
            }
            return response()->json(['result' => false, 'message' => 'Error in adding User', 'details' => $response->body()]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Fill all the details',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'error: ' . $e->getMessage()]);
        }
    }
    public function userEdit(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-users/users/getUserById/' . $id);

            if ($response->successful()) {
                $corporateuser = $response['data'];
                //return $corporateuser;
                $headerData = 'Edit Corporate User Details';
                return view('content.CorporateUsers.edit-corporate-user', compact('corporateuser'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function updateUser(Request $request, $id)
    {
        // return $request;
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        // Define common validation rules
        $validationRules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'nullable|string',
            'mobile_country_code' => 'required|string',
            'email' => 'required|email',
            'aadhar' => 'nullable|string',
            'mobile_num' => 'required|integer',

        ];



        // Validate request data
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Prepare data for the API request
        $requestData = array_merge($request->all(), [
            'location_id' => $locationId,
            'corporate_id' => $corporateId,
            'corporate_user_id' => $userId,
        ]);

        try {
            // Make the API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate-users/users/updateUser/' . $id, $requestData);

            // Check if the response is successful
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => 'User updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'result' => false,
                    'message' => 'Error from external API',
                    'details' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    // MHC MENU RIGHTS
    public function mhcRights(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-users/users/getUserById/' . $id);


            if ($response->successful()) {
                $corporateuser = $response['data'];
                $corporateuser_dept = $response['data2'];

                // return $mhcrights;
                $headerData = 'MHC Rights';
                return view('content.CorporateUsers.mhc-rights', compact('corporateuser', 'corporateuser_dept'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function getmhcMenu(Request $request, $id)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        //return $corporateId;
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),

            ])->get('https://api-user.hygeiaes.com/V1/corporate-users/users/getmhcmenu/' . $corporateId . '/' . $locationId.'/'. $id);
            // return $response->json()['data'];
            // Get the data from the response

            $datas1 = $response->json()['data'];
            $datas2 = $response->json()['data2'];

            return response()->json([
                'result' => true,
                'data' => array_values($datas1),
                'data2' => $datas2, // Reset array keys after filtering
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }
    public function savemhcRights(Request $request)
    {
        $validatedData = $request->validate([

            'landing_page' => 'array',
            'landing_page.*' => 'in:1,2,3',
            'employees' => 'nullable|string|in:0,1,2',
            'employee_monitoring' => 'nullable|string|in:0,1',
            'diagnostic_assessment' => 'nullable|string|in:0,1,2',
            'hra' => 'nullable|string|in:0,1,2',
            'stress_management' => 'nullable|string|in:0,1,2',
            'pre_employment' => 'nullable|string|in:0,1,2',
            'reports' => 'nullable|string|in:0,1',
            'events' => 'nullable|string|in:0,1,2',
            'health_partner' => 'nullable|string|in:0,1,2',
            'corporate_user_id' => 'required|string',

        ]);
        if (! $validatedData) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validatedData], 422);
        }
        // return $request;
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/corporate-users/users/mhcRightsSave', [


            'location_id' => $locationId,
            'corporate_admin_user_id' => $userId,
            'employees' => $request->input('employees'),
            'landing_page' => $request->input('landing_page'),
            'employee_monitoring' => $request->input('employee_monitoring_radio'),
            'diagnostic_assessment' => $request->input('sub_module_1_radio'),
            'hra' => $request->input('sub_module_2_radio'),
            'events' => $request->input('sub_module_3_radio'),
            'pre_employment' => $request->input('module_3_radio'),
            'reports' => $request->input('reports_radio'),
            'health_partner' => $request->input('module_5_radio'),
            'corporate_user_id' => $request->input('corporate_user_id')

           ]);

        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => 'Menu Rights added Successfully']);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    }
    public function updatemhcRights(Request $request)
    {
        $validatedData = $request->validate([

            'landing_page' => 'array',
            'landing_page.*' => 'in:1,2,3',
            'employees' => 'nullable|string|in:0,1,2',
            'employee_monitoring' => 'nullable|string|in:0,1',
            'diagnostic_assessment' => 'nullable|string|in:0,1,2',
            'hra' => 'nullable|string|in:0,1,2',
            'stress_management' => 'nullable|string|in:0,1,2',
            'pre_employment' => 'nullable|string|in:0,1,2',
            'reports' => 'nullable|string|in:0,1',
            'events' => 'nullable|string|in:0,1,2',
            'health_partner' => 'nullable|string|in:0,1,2',
            'corporate_user_id' => 'required|string',

        ]);
        if (! $validatedData) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validatedData], 422);
        }
        // return $request;
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->put('https://api-user.hygeiaes.com/V1/corporate-users/users/mhcRightsUpdate', [

            'id' => $request->input('corporate_menu_rights_id'),
            'location_id' => $locationId,
            'corporate_admin_user_id' => $userId,
            'employees' => $request->input('employees'),
            'landing_page' => $request->input('landing_page'),
            'employee_monitoring' => $request->input('employee_monitoring_radio'),
            'diagnostic_assessment' => $request->input('sub_module_1_radio'),
            'hra' => $request->input('sub_module_2_radio'),
            'events' => $request->input('sub_module_3_radio'),
            'pre_employment' => $request->input('module_3_radio'),
            'reports' => $request->input('reports_radio'),
            'health_partner' => $request->input('module_5_radio'),
            'corporate_user_id' => $request->input('corporate_user_id')

           ]);

        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => 'Menu Rights added Successfully']);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    }

    //OHC MENU RIGHTS

    public function ohcRights(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate-users/users/getUserById/' . $id);


            if ($response->successful()) {
                $corporateuser = $response['data'];
                $corporateuser_dept = $response['data2'];

                // return $mhcrights;
                $headerData = 'OHC Rights';
                return view('content.CorporateUsers.ohc-rights', compact('corporateuser', 'corporateuser_dept'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function getohcMenu(Request $request, $id)
    {
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        //return $corporateId;
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),

            ])->get('https://api-user.hygeiaes.com/V1/corporate-users/users/getohcmenu/' . $corporateId . '/' . $locationId.'/'. $id);
            // return $response->json()['data'];
            // Get the data from the response

            $datas1 = $response->json()['data'];
            $datas2 = $response->json()['data2'];

            return response()->json([
                'result' => true,
                'data' => array_values($datas1),
                'data2' => $datas2, // Reset array keys after filtering
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }
    public function saveohcRights(Request $request)
    {
        $validatedData = $request->validate([

            'doctor' => 'nullable|interger',
            'qualification_id ' => 'nullable|interger',
            'pharmacy_id ' => 'nullable|interger',
            'ohc_dashboard' => 'nullable|interger',
            'out_patient.*' => 'nullable|string|in:0,1,2',
            'prescription' => 'nullable|string|in:0,1,2',
            'tests' => 'nullable|string|in:0,1,2',
            'stocks' => 'nullable|string|in:0,1,2',
            'ohc_report' => 'nullable|string|in:0,1,2',
            'census_report' => 'nullable|string|in:0,1,2',
            'safety_board' => 'nullable|string|in:0,1,2',
            'invoice' => 'nullable|string|in:0,1,2',
            'bio_medical' => 'nullable|string|in:0,1,2',
            'inventory' => 'nullable|string|in:0,1,2',
            'forms' =>  'nullable|string|in:0,1,2',
            'corporate_user_id' => 'required|string',
        ]);
        if (! $validatedData) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validatedData], 422);
        }
        // return $request;
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post('https://api-user.hygeiaes.com/V1/corporate-users/users/ohcRightsSave', [


            'ohc_dashboard' => $locationId,
            'out_patient' => $userId,
            'prescription' => $request->input('employees'),
            'tests' => $request->input('landing_page'),
            'stocks' => $request->input('employee_monitoring_radio'),
            'ohc_report' => $request->input('sub_module_1_radio'),
            'census_report' => $request->input('sub_module_2_radio'),
            'safety_board' => $request->input('reports_radio'),
            'invoice' => $request->input('module_5_radio'),
            'bio_medical' => $request->input('module_5_radio'),
            'inventory' => $request->input('module_5_radio'),
            'forms' => $request->input('module_5_radio'),
            'corporate_user_id' => $request->input('corporate_user_id')

           ]);

        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => 'Menu Rights added Successfully']);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    }
    public function updateohcRights(Request $request)
    {
        $validatedData = $request->validate([
            'doctor' => 'nullable|interger',
            'qualification_id ' => 'nullable|interger',
            'pharmacy_id ' => 'nullable|interger',
            'ohc_dashboard' => 'nullable|interger',
            'out_patient.*' => 'nullable|string|in:0,1,2',
            'prescription' => 'nullable|string|in:0,1,2',
            'tests' => 'nullable|string|in:0,1,2',
            'stocks' => 'nullable|string|in:0,1,2',
            'ohc_report' => 'nullable|string|in:0,1,2',
            'census_report' => 'nullable|string|in:0,1,2',
            'safety_board' => 'nullable|string|in:0,1,2',
            'invoice' => 'nullable|string|in:0,1,2',
            'bio_medical' => 'nullable|string|in:0,1,2',
            'inventory' => 'nullable|string|in:0,1,2',
            'forms' =>  'nullable|string|in:0,1,2',
            'corporate_user_id' => 'required|string',

        ]);
        if (! $validatedData) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validatedData], 422);
        }
        // return $request;
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->put('https://api-user.hygeiaes.com/V1/corporate-users/users/ohcRightsUpdate', [

            'id' => $request->input('corporate_ohc_rights_id'),
            'ohc_dashboard' => $locationId,
            'out_patient' => $userId,
            'prescription' => $request->input('employees'),
            'tests' => $request->input('landing_page'),
            'stocks' => $request->input('employee_monitoring_radio'),
            'ohc_report' => $request->input('sub_module_1_radio'),
            'census_report' => $request->input('sub_module_2_radio'),
            'safety_board' => $request->input('reports_radio'),
            'invoice' => $request->input('module_5_radio'),
            'bio_medical' => $request->input('module_5_radio'),
            'inventory' => $request->input('module_5_radio'),
            'forms' => $request->input('module_5_radio'),
            'corporate_user_id' => $request->input('corporate_user_id')

           ]);

        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => 'Menu Rights added Successfully']);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    }


}

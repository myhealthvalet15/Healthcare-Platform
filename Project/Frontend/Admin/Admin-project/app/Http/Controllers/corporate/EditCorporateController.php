<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\MasterCorporate;

class EditCorporateController extends Controller
{
    protected $httpClient;
    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    public function editAddress(Request $request, $id, $corporate_id)
    {
        try {
            $addressResponse = $this->httpClient->request('GET', "/api/corporate/edit_address/{$id}/{$corporate_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            if ($addressResponse['success']) {
                $corporate_name = $addressResponse['data']['corporate_name'];
                $addressData = $addressResponse['data'];
                $corporate_address = $addressData['corporate_address'];
                $pincode = $addressData['pincode'] ?? '';
                $area = $addressData['area'] ?? '';
                $city = $addressData['city'] ?? '';
                $state = $addressData['state'] ?? '';
                $country = $addressData['country'] ?? '';
                return view('content.corporate_list.corporate.address.edit', compact('id', 'corporate_id', 'corporate_name', 'corporate_address', 'pincode', 'area', 'city', 'state', 'country'));
            } else {
                return redirect()->back()->with('error', 'corporate address  data no more  there ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function updateAddress(Request $request, $id)
    {
        // Log::info("Incoming update address request", $request->all());
        $rules = [
            'country_id' => 'required|numeric',
            'area_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'state_id' => 'required|numeric',
            'pincode_id' => 'required|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'website_link' => 'nullable|url',
        ];
        $validated = $request->validate($rules);
        try {
            $addressResponse = $this->httpClient->request('post', "/api/corporate/update_corporate_address/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
                'json' => $validated,
            ]);
            if ($addressResponse['success']) {
                return redirect()->route('corporate-list')->with('success', 'User updated successfully');
            } else {
                return redirect()->back()->with('error', 'corporate address updated failed');
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody(), true);
            Log::warning("API ClientException occurred", [
                'status' => $response->getStatusCode(),
                'response' => $responseBody,
            ]);
            return response()->json([
                'message' => 'Failed to update address',
                'error' => $responseBody,
            ], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error("An error occurred while updating address", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function editEmployeeTypes(Request $request, $id, $corporate_id)
    {
        $emptype_id = $corporate_id;
        try {
            $response = $this->httpClient->request('POST', "/api/Employeetype/show/{$corporate_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            if ($response['success']) {
                $emptypes = $response['data'];
                $emptype = $emptypes['employee_types'];
                $corporate_name = $emptypes['corporate_name'];
                return view('content.corporate_list.corporate.emptype.edit', compact('id', 'corporate_id', 'emptype', 'emptype_id', 'corporate_name'));
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('HTTP Request failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function updateEmployeeTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_type_name.*' => 'required|string|max:255',
            'active_status.*' => 'required|in:0,1',
            'employee_type_id.*' => 'required|integer',
            'Contractors' => 'nullable',
        ]);
        if ($validator->fails()) {
            return redirect()->route('corporate-list')
                ->withErrors($validator)
                ->withInput();
        }
        $employeeTypes = [];
        foreach ($request->employee_type_name as $index => $employeeTypeName) {
            $employeeTypes[] = [
                'employee_type_id' => $request->employee_type_id[$index] ?? null,
                'employee_type_name' => $employeeTypeName,
                'active_status' => $request->active_status[$index] ?? 0,
                'checked'     => isset($request->Contractors[$index]) && $request->Contractors[$index] === 'on' ? 1 : 0,
                'corporate_id' => $request->corporate_id,
            ];
        }
        try {
            $response = $this->httpClient->request('POST', '/api/Employeetype/update', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $employeeTypes,
            ]);
            $responseData = $response;
            if ($responseData['success'] ?? false) {
                return redirect()->route('corporate-list')->with('success', 'Employee types updated successfully.');
            } else {
                return redirect()->route('corporate-list')->with('error', 'Failed to update employee types.');
            }
        } catch (RequestException $e) {
            return redirect()->route('corporate-list')->with('error', 'API request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('corporate-list')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function editAdminUsers(Request $request, $id, $corporate_id)
    {
        try {
            $response = $this->httpClient->request('GET', "/api/Corporate_admin_user/show/{$id}/{$corporate_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
           // return $response;
            if ($response['success']) {
                $emptypes = $response['data'];
                if (!isset($emptypes['user'])) {
                    return response()->json(['message' => 'Employee type data not found'], 404);
                }
                $emptype = $emptypes['user'];
                $corporate_name = $emptypes['corporate'];
                return view('content.corporate_list.corporate.corporateadmin.edit', compact('id', 'corporate_id', 'emptype', 'corporate_name'));
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('HTTP Request failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch employee type data. Please try again later.'], 500);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
    public function updateAdminUsers(Request $request, $id)
    {
       // return 'Hai';
        Log::debug('Incoming request data', $request->all());
        try {
            $data = $request->all();
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'email' => 'required|email',
                'mobile_country_code' => 'required|string|max:10',
                'mobile_num' => 'required|string|max:15',
                'aadhar' => 'required|string|max:12',
                'signup_by' => 'required|string',
                'active_status' => 'required|boolean',
            ]);
            $employeeData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'],
                'email' => $data['email'],
                'mobile_country_code' => $data['mobile_country_code'],
                'mobile_num' => $data['mobile_num'],
                'aadhar' => $data['aadhar'],
                'signup_by' => $data['signup_by'],
                'signup_on' => now(),
                 'active_status' => (bool) $data['active_status'],
            ];
         
            $response = $this->httpClient->request('POST', "/api/Corporate_admin_user/update/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $employeeData,
            ]);
            
          // return $response;
            if ($response['success']) {
                return redirect()->route('corporate-list')->with('success', 'User updated successfully');
            }
            return redirect()->back()->with('error', 'Failed to update user');
        } catch (\Exception $e) {
            Log::error('Error updating admin user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the user.');
        }
    }
    public function generateUniquecorpId(Request $request)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 10;
        $userId = 'MC';
        for ($i = 0; $i < $length; $i++) {
            $userId .= $characters[random_int(0, strlen($characters) - 1)];
        }
        $userIds = 'MCBoAmzVFigh';
        $existingUser = MasterCorporate::where('corporate_id', $userIds)
            ->exists();
        $retries = 0;
        while ($existingUser && $retries < 5) {
            $userId = 'MC';
            for ($i = 0; $i < $length; $i++) {
                $userId .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $existingUser = MasterCorporate::where('corporate_id', $userId)->exists();
            $retries++;
        }
        if ($existingUser) {
            throw new \Exception('Unable to generate a unique corporate ID after multiple attempts.');
        }
        return $userId;
    }
    public function uniquelocation_ids(Request $request)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 8;
        $prefix = 'MC';
        $retries = 0;
        $maxRetries = 5;
        do {
            $userId = $prefix;
            for ($i = 0; $i < $length; $i++) {
                $userId .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $existingCorporate = MasterCorporate::where('corporate_id', $userId)->exists();
            $existingLocation = MasterCorporate::where('location_id', $userId)->exists();
            $isUnique = !$existingCorporate && !$existingLocation;
            $retries++;
        } while (!$isUnique && $retries < $maxRetries);
        if (!$isUnique) {
            throw new \Exception('Unable to generate a unique corporate ID after multiple attempts.');
        }
        return $userId;
    }
    public function addemployeetype(Request $request)
    {
        $postData = [
            'employee_type_name' => $request->input('employee_type_name'),
            'corporate_id' => $request->input('corporate_id'),
            'active_status' => $request->input('active_status'),
        ];
        // Log::info('Preparing to call Add Employee Type API...', $postData);
        try {
            $response3 = $this->httpClient->request('POST', '/api/Employeetype/add_employeetype', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $postData,
            ]);
            if ($response3['success']) {
                return redirect()->route('corporate.editEmployeeTypes')->with('success', 'Employee type added successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to add employee type. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error while calling Add Employee Type API', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred. Please try again later.');
        }
    }
}

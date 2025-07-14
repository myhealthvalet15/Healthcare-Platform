<?php

namespace App\Http\Controllers\CorporateUsers;

use App\Http\Controllers\Controller;
use App\Models\CorporateAdminUser;
use App\Models\CorporateMenuRights;
use App\Models\Department\CorporateHl1;
use App\Models\Corporate\CorporateUsers\OhcMenuRights;
use App\Models\Corporate\CorporateComponents\CorporateComponents;
use App\Models\Corporate\CorporateComponents\CorporateComponentSubmodules;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MasterCorporate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Crypt;


 
class CorporateUserController extends Controller
{
    public function getAllUsersDetails($corporate_id,$location_id)
    {
        
        if (!ctype_alnum($location_id) || !ctype_alnum($corporate_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request.'], 400);
        }
        $employeesQuery = CorporateAdminUser::where('corporate_id', $corporate_id)
            ->where('location_id', $location_id)
            ->get();
        if ($employeesQuery->isEmpty()) {
            return response()->json(['result' => true, 'message' => 'No User data found'], 422);
        }
        $formattedEmployees = $employeesQuery->map(function ($employee) {
            return $this->formatUserRow($employee);
        });
        return response()->json(['result' => true, 'data' => $formattedEmployees]);
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
            Log::info($data);
            throw new \Exception('Decryption failed');
        }
        return $decryptedData;
    }

    private function formatUserRow($employee)
    {
        return [
            'id' => $employee->id,
            'first_name' => $this->aes256DecryptData($employee->first_name),
            'last_name' => $this->aes256DecryptData($employee->last_name),
            'email' => $this->aes256DecryptData($employee->email),
            'mobile_country_code' => $this->aes256DecryptData($employee->mobile_country_code),
            'mobile_num' => $this->aes256DecryptData($employee->mobile_num),
            'active_status' => $employee->active_status,
            'setting' => $employee->setting,
 
        ];
    }
    public function addCorporateUSer(Request $request)
    {
        // Log::info('CorporateAdminController', $request->all());
        $validator = Validator::make($request->all(), [
           
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'email' => 'required|email|unique:corporate_admin_user,email',
            'mobile_country_code' => 'nullable|string',
            'mobile_num' => 'nullable|string',
            'aadhar' => 'nullable|string',
            'password' => 'nullable|string',
            

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $emailHash = hash('sha256', $request->email);
        $encryptedFirstName = $this->aes256EncryptData(ucwords($request->first_name));
        $encryptedLastName = $this->aes256EncryptData(ucwords($request->last_name));
        $encryptedGender = $this->aes256EncryptData($request->gender);
        $encryptedEmail = $this->aes256EncryptData($request->email);
        $encryptedMobileCountryCode = $this->aes256EncryptData($request->mobile_country_code);
        $encryptedMobileNum = $this->aes256EncryptData($request->mobile_num);
        $hashedPassword = Hash::make($request->password);
        // Prepare the department (handling array from the request)
        $departments = implode(',', $request->department);
        $setting = implode(',', $request->setting);
        $user = CorporateAdminUser::create([
            'corporate_admin_user_id' => $request->corporate_admin_user_id,
            'corporate_id' => $request->corporate_id,
            'location_id' => $request->location_id,
            'first_name' => $encryptedFirstName,
            'last_name' => $encryptedLastName,
            'gender' => $encryptedGender,
            'email' => $encryptedEmail,
            'email_hash' => $emailHash,
            'mobile_country_code' => $encryptedMobileCountryCode,
            'mobile_num' => $encryptedMobileNum,
            'active_status' => $request->active_status ?? 1,
            'super_admin' => $request->super_admin ?? 1,
            'signup_by' => $request->signup_by ?? 'admin',
            'signup_on' => now(),
            'aadhar' => $request->aadhar,
            'password' => $hashedPassword,
            'department' => $departments,
            'setting' => $setting,
        ]);
        $this->sendToMail($request, $request->email, 'Your Login Credentials', $request->email, $request->password);
        return response()->json(['result' => true,'message' => 'Corporate admin user registered successfully', 'user' => $user], 201);
    }
    
    private function sendToMail(Request $request, $to, $subject, $email, $password)
    {
        try {
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Invalid email address provided.');
            }
            $body = "
                <p>Hello,</p>
                <p>Your account has been successfully created. Here are your login credentials:</p>
                <ul>
                    <li><strong>Email:</strong> {$email}</li>
                    <li><strong>Password:</strong> {$password}</li>
                </ul>
                <p>Please log in and change your password for security purposes.</p>
            ";
            $mailController = new MailController();
           // $mailController->sendEmail($request, $subject, $to, $body, 'credentials');
            // Log::info("Email sent to {$to} with subject: {$subject}");
        } catch (\Exception $e) {
            Log::error("Error sending email to {$to}: " . $e->getMessage());
        }
    }
    public function getUserById($id)
    {
        //return $id;
        try {
           // Retrieve single record for given ID
            $user = CorporateAdminUser::where('id', $id)->first();
            
            if (!$user) {
                return response()->json(['message' => 'user not found'], 404);
            }
            
           
          $encryptedFields = [
            'first_name',
            'last_name',
            'gender',
            'email',
            'mobile_country_code',
            'mobile_num',
        ];
        foreach ($encryptedFields as $field) {
            if (isset($user->{$field})) {
                $user->{$field} = $this->aes256DecryptData($user->{$field});
            }
        }
        $departmentIds = explode(',', $user->department);
        $departments = CorporateHl1::whereIn('hl1_id', $departmentIds)->pluck('hl1_name');
       
        
        
            return response()->json([
                'success' => true,
                'data' => $user,
                'data2' =>$departments,
                
               
                // Return the required user data
            ]);
        
        
        } catch (\Exception $e) {
            //Log::error('Failed to retrieve user data', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve user data'], 500);
        }
    }
   
    
    public function updateUser($id,Request $request)
    {
       // Log::info('Starting the update process for user ID: ' . $id);
        // Log::info('Validating incoming request', $request->all());
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'email' => 'nullable',
            'mobile_country_code' => 'nullable',
            'mobile_num' => 'nullable|string',
            'aadhar' => 'nullable|string',
            
            'signup_on' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }
        // Log::info('Validation successful');
        // Log::info('Fetching user with ID: ' . $id);
        $user = CorporateAdminUser::find($id);
        if (!$user) {
            Log::error('User not found for ID: ' . $id);
            return response()->json(['message' => 'User not found'], 404);
        }
        // Log::info('User found', ['user' => $user]);
        try {
          $departments = implode(',', $request->department);
          $setting = implode(',', $request->setting);
            $user->first_name = $this->aes256EncryptData(ucwords($request->first_name));
            $user->last_name = $this->aes256EncryptData(ucwords($request->last_name));
            $user->gender = $this->aes256EncryptData($request->gender);
            $user->email = $this->aes256EncryptData($request->email);
            $user->mobile_country_code = $this->aes256EncryptData($request->mobile_country_code);
            $user->mobile_num = $this->aes256EncryptData($request->mobile_num);
            $user->aadhar = $request->aadhar;
            $user->signup_by = $request->signup_by ?? 'admin';
            $user->signup_on = now();
            $user->department = $departments;
            $user->setting = $setting;
            $user->save();
        } catch (\Exception $e) {
            Log::error('Error occurred while updating user', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update user', 'error' => $e->getMessage()], 500);
        }
        // Log::info('User updated successfully', ['user' => $user]);
        return response()->json(['message' => 'Corporate admin user updated successfully', 'data' => $user], 200);
    }

    //MHC MENU RIGHTS
    public function getmhcMenu($corporate_id,$location_id,$id)
    {
        
        if (!ctype_alnum($location_id) || !ctype_alnum($corporate_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request.'], 400);
        }
       
      
        $corporateComponents = CorporateComponents::where('corporate_id', $corporate_id)
        ->whereIn('module_id', [1, 3, 5])
        ->with('module')->get();
    $componentsWithSubmodules = [];

    foreach ($corporateComponents as $component) {
        if ($component->sub_module_id) {
            $subModuleIds = str_replace(['{', '}'], '', $component->sub_module_id);
            $subModuleIds = explode(',', $subModuleIds);
            $subModules = CorporateComponentSubmodules::whereIn('id', $subModuleIds)->get();
            $componentsWithSubmodules[] = [
                'component' => $component,
                'submodules' => $subModules,
            ];
        } else {
            $componentsWithSubmodules[] = [
                'component' => $component,
                'submodules' => null,
            ];
        }
    }
        if ($corporateComponents->isEmpty()) {
            return response()->json(['result' => true, 'message' => 'No  data found'], 422);
        }
        $mhc = CorporateMenuRights::where('corporate_user_id', $id)->get();
        
        return response()->json(['result' => true, 'data' => $componentsWithSubmodules,'data2' => $mhc]);
    }   
    public function mhcrightsSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            'corporate_admin_user_id' => 'required|string',
            'location_id' => 'required|string',
            'landing_page' => 'array',
            'landing_page.*' => 'in:1,2,3',
            'employees' => 'nullable|string|in:0,1,2',
            'employee_monitoring' => 'nullable|string|in:0,1',
            'diagnostic_assessment' => 'nullable|string|in:0,1,2',
            'hra' => 'nullable|string|in:0,1,2',
            'pre_employment' => 'nullable|string|in:0,1,2',
            'reports' => 'nullable|string|in:0,1',
            'events' => 'nullable|string|in:0,1,2',
            'health_partner' => 'nullable|string|in:0,1,2',
            'corporate_user_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
           
            $mhcmenurights = CorporateMenuRights::create($validator->validated());
          
            return response()->json(['success' => true, 'data' => $mhcmenurights], 201);
        } catch (\Exception $e) {
            Log::error('Error creating record: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating record'], 500);
        }
    }
    public function mhcrightsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            'corporate_admin_user_id' => 'required|string',
            'location_id' => 'required|string',
            'landing_page' => 'array',
            'landing_page.*' => 'in:1,2,3',
            'employees' => 'nullable|string|in:0,1,2',
            'employee_monitoring' => 'nullable|string|in:0,1',
            'diagnostic_assessment' => 'nullable|string|in:0,1,2',
            'hra' => 'nullable|string|in:0,1,2',
            'pre_employment' => 'nullable|string|in:0,1,2',
            'reports' => 'nullable|string|in:0,1',
            'events' => 'nullable|string|in:0,1,2',
            'health_partner' => 'nullable|string|in:0,1,2',
            'corporate_user_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $id=$request->id;
        $mhcmenurightsupdate = CorporateMenuRights::find($id);
        if (!$mhcmenurightsupdate) {
            Log::error('User not found for ID: ' . $id);
            return response()->json(['message' => 'User Rights not found'], 404);
        }
        try {
           
            $mhcmenurightsupdate->update($validator->validated());
          
          
            return response()->json(['success' => true, 'data' => $mhcmenurightsupdate], 201);
        } catch (\Exception $e) {
            Log::error('Error occurred while updating Menu Rights', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update menu rights', 'error' => $e->getMessage()], 500);
        }
        // Log::info('User updated successfully', ['user' => $user]);
        return response()->json(['message' => 'Corporate admin user menu rights updated successfully', 'data' => $user], 200);
    }
   
    //OHC MENU RIGHTS

    public function getohcMenu($corporate_id,$location_id,$id)
    {
        
        if (!ctype_alnum($location_id) || !ctype_alnum($corporate_id)) {
            return response()->json(['result' => false, 'message' => 'Invalid Request.'], 400);
        }
       
      
        $corporateComponents = CorporateComponents::where('corporate_id', $corporate_id)
        ->whereIn('module_id', [2, 4, 6])
        ->with('module')->get();
    $componentsWithSubmodules = [];

    foreach ($corporateComponents as $component) {
        if ($component->sub_module_id) {
            $subModuleIds = str_replace(['{', '}'], '', $component->sub_module_id);
            $subModuleIds = explode(',', $subModuleIds);
            $subModules = CorporateComponentSubmodules::whereIn('id', $subModuleIds)->get();
            $componentsWithSubmodules[] = [
                'component' => $component,
                'submodules' => $subModules,
            ];
        } else {
            $componentsWithSubmodules[] = [
                'component' => $component,
                'submodules' => null,
            ];
        }
    }
        if ($corporateComponents->isEmpty()) {
            return response()->json(['result' => true, 'message' => 'No  data found'], 422);
        }
        $ohc = OhcMenuRights::where('corporate_user_id', $id)->get();
        
        return response()->json(['result' => true, 'data' => $componentsWithSubmodules,'data2' => $ohc]);
    }   
    public function ohcrightsSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'corporate_admin_user_id' => 'required|string',
            'location_id' => 'required|string',
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
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
           
            $ohcmenurights = OhcMenuRights::create($validator->validated());
          
            return response()->json(['success' => true, 'data' => $ohcmenurights], 201);
        } catch (\Exception $e) {
            Log::error('Error creating record: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating record'], 500);
        }
    }
    public function ohcrightsUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            'corporate_admin_user_id' => 'required|string',
            'location_id' => 'required|string',
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
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $id=$request->id;
        $ohcmenurightsupdate = OhcMenuRights::find($id);
        if (!$ohcmenurightsupdate) {
            Log::error('User not found for ID: ' . $id);
            return response()->json(['message' => 'User Rights not found'], 404);
        }
        try {
           
            $ohcmenurightsupdate->update($validator->validated());
          
          
            return response()->json(['success' => true, 'data' => $ohcmenurightsupdate], 201);
        } catch (\Exception $e) {
            Log::error('Error occurred while updating Menu Rights', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update menu rights', 'error' => $e->getMessage()], 500);
        }
        // Log::info('User updated successfully', ['user' => $user]);
        return response()->json(['message' => 'Corporate admin user menu rights updated successfully', 'data' => $user], 200);
    }
    
}

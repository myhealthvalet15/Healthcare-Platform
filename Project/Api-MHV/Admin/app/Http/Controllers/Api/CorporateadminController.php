<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CorporateAdminUser;
use App\Models\MasterCorporate;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// TODO: FIRST_NAME_HASH, LAST_NAME_HASH, MOBILE_HASH, EMAIL_HASH shld be stored as aes256EncryptDataWeak data
// TODO: ALSO CHANGE This HASH LOGIN Process
// TODO: As of now FIRST_NAME_HASH, LAST_NAME_HASH, MOBILE_HASH are saved as aes256EncryptDataWeak hash data in db but the emailhash is stored as hash sha-256 in adduserexcel and addsingleuser
// TODO: As of now FIRST_NAME_HASH, LAST_NAME_HASH, MOBILE_HASH, EMAIL_HASH is stored as hash sha-256 in adduserexcel and addsingleuser
class CorporateadminController extends Controller
{
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
    private function aes256EncryptData(string $data): string
    {
        $key = hex2bin(env('AES_256_ENCRYPTION_KEY'));
        $cipher = 'aes-256-cbc';
        $iv = random_bytes(openssl_cipher_iv_length($cipher));
        $encryptedData = openssl_encrypt($data, $cipher, $key, 0, $iv);
        if ($encryptedData === false) {
            throw new \Exception('Encryption failed');
        }
        return base64_encode($iv . $encryptedData);
    }
    public function store(Request $request)
    {
        // Log::info('CorporateAdminController', $request->all());
        $validator = Validator::make($request->all(), [
            'corporate_admin_user_id' => 'required|string|max:255',
            'corporate_id' => 'required|string|max:255',
            'location_id' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'email' => 'required|email|unique:corporate_admin_user,email',
            'password' => 'required|string',
            'mobile_country_code' => 'nullable|string',
            'mobile_num' => 'nullable|string',
            'aadhar' => 'nullable|string',
            'age' => 'nullable|integer',
            'active_status' => 'nullable|boolean',
            'super_admin' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $emailHash = hash('sha256', $request->email);
        $firstNameHash = hash('sha256', strtolower($request->first_name));
        $lastNameHash = hash('sha256', strtolower($request->last_name));
        $encryptedFirstName = $this->aes256EncryptData(ucwords($request->first_name));
        $encryptedLastName = $this->aes256EncryptData(ucwords($request->last_name));
        $encryptedDob = $this->aes256EncryptData($request->dob);
        $encryptedGender = $this->aes256EncryptData($request->gender);
        $encryptedEmail = $this->aes256EncryptData($request->email);
        $encryptedMobileCountryCode = $this->aes256EncryptData($request->mobile_country_code);
        $encryptedMobileNum = $this->aes256EncryptData($request->mobile_num);
        $hashedPassword = Hash::make($request->password);
        $user = CorporateAdminUser::create([
            'corporate_admin_user_id' => $request->corporate_admin_user_id,
            'corporate_id' => $request->corporate_id,
            'location_id' => $request->location_id,
            'first_name' => $encryptedFirstName,
            'last_name' => $encryptedLastName,
            'dob' => $encryptedDob,
            'gender' => $encryptedGender,
            'email' => $encryptedEmail,
            'email_hash' => $emailHash,
            'first_name_hash' => $firstNameHash,
            'last_name_hash' => $lastNameHash,
            'password' => $hashedPassword,
            'mobile_country_code' => $encryptedMobileCountryCode,
            'mobile_num' => $encryptedMobileNum,
            'active_status' => $request->active_status ?? 1,
            'super_admin' => $request->super_admin ?? 1,
            'signup_by' => $request->signup_by ?? 'admin',
            'signup_on' => now(),
            'aadhar' => $request->aadhar,
            'age' => $request->age,
        ]);
        $this->sendToMail($request, $request->email, 'Your Login Credentials', $request->email, $request->password);
        return response()->json(['message' => 'Corporate admin user registered successfully', 'user' => $user], 201);
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
            $mailController->sendEmail($request, $subject, $to, $body, 'credentials');
            // Log::info("Email sent to {$to} with subject: {$subject}");
        } catch (\Exception $e) {
            Log::error("Error sending email to {$to}: " . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
       // return 'hieeee';
        //Log::info('Starting the update process for user ID: ' . $id);
        //Log::info('Validating incoming request', $request->all());
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'email' => 'nullable',
            'password' => 'nullable|string',
            'mobile_country_code' => 'nullable',
            'mobile_num' => 'nullable|string',
            'aadhar' => 'nullable|string',
            'active_status' => 'nullable|boolean',
            'signup_by' => 'required|string',
            'signup_on' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }
        // Log::info('Validation successful');
        Log::info('Fetching user with ID: ' . $id);
        $user = CorporateAdminUser::find($id);
        if (!$user) {
            Log::error('User not found for ID: ' . $id);
            return response()->json(['message' => 'User not found'], 404);
        }
        // Log::info('User found', ['user' => $user]);
        try {
            $user->first_name = $this->aes256EncryptData(ucwords($request->first_name));
            $user->last_name = $this->aes256EncryptData(ucwords($request->last_name));
            $user->gender = $this->aes256EncryptData($request->gender);
            $user->email = $this->aes256EncryptData($request->email);
            $user->mobile_country_code = $this->aes256EncryptData($request->mobile_country_code);
            $user->mobile_num = $this->aes256EncryptData($request->mobile_num);
            $user->aadhar = $request->aadhar;
            $user->signup_by = $request->signup_by;
            $user->signup_on = now();
            $user->active_status = $request->active_status;
            $user->save();
        } catch (\Exception $e) {
            Log::error('Error occurred while updating user', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update user', 'error' => $e->getMessage()], 500);
        }
         Log::info('User updated successfully', ['user' => $user]);
        return response()->json(['message' => 'Corporate admin user updated successfully', 'user' => $user], 200);
    }
    public function show($id, $corporate_id)
{
    try {
        $master_corp = MasterCorporate::where('id', $id)
            ->select('corporate_name', 'location_id')
            ->first();

        $user = CorporateAdminUser::where('corporate_id', $corporate_id)
            ->where('location_id', $master_corp->location_id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
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
            if (!is_null($user->{$field})) {
                $user->{$field} = $this->decryptField($user->{$field});
            }
        }

//        Log::debug('Decrypted user data:', $user->toArray());

        return response()->json([
            'user' => $user,
            'corporate' => $master_corp->corporate_name
        ], 200);
    } catch (\Throwable $e) {
        Log::error('Error fetching user: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while fetching the user data.'
        ], 500);
    }
}

    /**
     * Decrypt a field if it's encrypted.
     *
     * @param string|null $data
     * @param strin
     * @return string|null
     */
    private function aes256DecryptData(string $data = null)
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
    private function decryptField($data)
    {
        if (empty($data)) {
            return null;
        }
        if ($this->isBase64Encoded($data)) {
            try {
                return $this->aes256DecryptData($data);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                Log::warning("Decryption failed for data: $data");
                return $data;
            }
        }
        return $data;
    }
    /**
     * Check if a string is Base64 encoded.
     *
     * @param string $data
     * @return bool
     */
    private function isBase64Encoded($data)
    {
        $decoded = base64_decode($data, true);
        return $decoded !== false && base64_encode($decoded) === $data;
    }
    public function adminuser_locations(Request $request, $id)
    {
        // Log::info($request->all());
        // Log::info($id);
        try {
            $validator = Validator::make($request->all(), [
                'location_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $address = CorporateAdminUser::where('corporate_id', $id)->first();
            if (!$address) {
                return response()->json(['message' => 'Address not found'], 404);
            }
            $address->location_id = $request->location_id;
            $address->save();
            // Log::info("Updated location_id for corporate_id: " . $id);
            return response()->json(['message' => 'Location updated successfully']);
        } catch (\Exception $e) {
            Log::error("Error updating location: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Employee\EmployeeType;

class EmployeeUserMappingResource extends JsonResource
{
    private function decryptData(string $data = null)
    {
        if ($data === null) {
            return null;
        }

        // Decode from base64
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

    /**
     * Convert the model to an array for response.
     */
    public function toArray($request)
    {
        return [
            'corporate_id' => $this->corporate_id,
            'id' => $this->id,
            'hl1_id' => $this->hl1_id,
            'user_id' => $this->user_id,
            'employee_type_id' => $this->employee_type_id,
            'corporate_contractors_id' => $this->corporate_contractors_id,
            'hl1_name' => $this->corporateHL1->hl1_name ?? null,
            'first_name' => $this->decryptData($this->masterUser->first_name ?? null),
            'email' => $this->decryptData($this->masterUser->email ?? null),
            'mob_num' => $this->decryptData($this->masterUser->mob_num ?? null),
            'dob' => $this->decryptData($this->masterUser->dob ?? null),
            'gender' => $this->decryptData($this->masterUser->gender ?? null),
            'designation' => $this->designation,
            'doj' => $this->from_date,
            'employee_type_name' => $this->employeeType->employee_type_name ?? null,

        ];
    }
}

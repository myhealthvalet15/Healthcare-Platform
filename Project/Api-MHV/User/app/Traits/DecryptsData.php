<?php

namespace App\Traits;

trait DecryptsData
{
    
    public function decryptData($data)
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
}

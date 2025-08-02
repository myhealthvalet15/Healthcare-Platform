<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Kms\V1\CryptoKey;
use Google\Cloud\Kms\V1\CryptoKey\CryptoKeyPurpose;
use Google\Cloud\Kms\V1\KeyManagementServiceClient;
use Google\ApiCore\ApiException;

// 🔐 Load service account credentials
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/config/myhealthvalet-1735545962489-2d6f3a6b09f8.json');

// Setup
$client = new KeyManagementServiceClient();
$projectId = 'myhealthvalet-1735545962489';
$locationId = 'us-east1';
$keyRingId = 'key-ring-1';
$keyId = 'key-p1';

$keyName = $client::cryptoKeyName($projectId, $locationId, $keyRingId, $keyId);

// STEP 1: 🔑 Generate DEK (random 256-bit key for AES-256)
$dek = random_bytes(32); // 32 bytes = 256 bits

// STEP 2: 📦 Encrypt some data using DEK
$dataToEncrypt = "Sensitive user data";
$iv = random_bytes(16); // Initialization vector for AES-CBC

$cipherText = openssl_encrypt($dataToEncrypt, 'aes-256-cbc', $dek, OPENSSL_RAW_DATA, $iv);

// STEP 3: 🔐 Encrypt the DEK using Google Cloud KMS
$encryptResponse = $client->encrypt($keyName, $dek);
$encryptedDek = $encryptResponse->getCiphertext();

// 🧾 Store these securely
file_put_contents('encrypted_data.bin', $cipherText);
file_put_contents('encrypted_dek.bin', $encryptedDek);
file_put_contents('iv.bin', $iv);

echo "✅ Envelope encryption complete.\n";

// To DECRYPT

// STEP 4: 🔐 Decrypt the DEK with KMS
$encryptedDek = file_get_contents('encrypted_dek.bin');
$decryptResponse = $client->decrypt($keyName, $encryptedDek);
$decryptedDek = $decryptResponse->getPlaintext();

// STEP 5: 🔓 Decrypt data using the DEK
$encryptedData = file_get_contents('encrypted_data.bin');
$iv = file_get_contents('iv.bin');

$decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $decryptedDek, OPENSSL_RAW_DATA, $iv);

echo "🔓 Decrypted data: $decryptedData\n";

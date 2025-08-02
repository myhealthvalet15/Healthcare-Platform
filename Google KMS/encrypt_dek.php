<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Kms\V1\KeyManagementServiceClient;

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/config/myhealthvalet-1735545962489-2d6f3a6b09f8.json');

$config = json_decode(file_get_contents('config/project_data.json'), true);

$projectId = $config['projectId'];
$locationId = $config['locationId'];
$keyRingId = $config['keyRingId'];
$keyId = $config['keyId'];

$keyName = KeyManagementServiceClient::cryptoKeyName($projectId, $locationId, $keyRingId, $keyId);
$client = new KeyManagementServiceClient();

$dek = '465ee6836d88794b0246c235b22ce597f7c3e7c7672f75636d9eb8a0d46b2088';

$response = $client->encrypt($keyName, $dek);
$encryptedDek = $response->getCiphertext();

echo "DEK generated and also DEK is encrypted with Google KM: " . base64_encode($encryptedDek);
echo "\n";

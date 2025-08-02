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

$encryptedDek = base64_decode("CiQA1x27YDd2aIjSXhx5q/BmfoPv0lrVNQ6ejhEBRK4vmkVt3+ASaQAfoX3hzwkyWyGdpigu8W1gqz9ipCdc4Hv3K69EUup1gySDH26b0IgnMttwjZ4LNhH9kSadOb/NB9qxxTtnBbzFGuCYwenkOvRPA8xo1m1auB+dgmbrypBfIuiPN7W+PPJUhYGNg+sROw==");

$response = $client->decrypt($keyName, $encryptedDek);
$dek = $response->getPlaintext();

echo "DEK decrypted and ready to use: " . $dek;
echo "\n";

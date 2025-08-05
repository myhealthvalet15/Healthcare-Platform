<?php

function o($x)
{

    $allowedIp = '103.101.58.167';
    function c()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://httpbin.org/ip");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $ip = json_decode($output, true);
        return $ip['origin'];
    }
    $serverIp = c();
    if ($serverIp !== $allowedIp) {
        // header('HTTP/1.1 403 Forbidden');
        echo 'Forbidden' . PHP_EOL;
        // exit;
    }
    $serviceAccountFile = __DIR__ . '/config/myhealthvalet-1735545962489-2d6f3a6b09f8.json';
    $creds = json_decode(file_get_contents($serviceAccountFile), true);
    $header = base64url_encode(json_encode(array(
        "alg" => "RS256",
        "typ" => "JWT"
    )));

    $now = time();
    $claimSet = base64url_encode(json_encode(array(/
        "iss" => $creds['client_email'],
        "scope" => "https://www.googleapis.com/auth/cloud-platform",
        "aud" => "https://oauth2.googleapis.com/token",
        "exp" => $now + 3600,
        "iat" => $now
    )));

    $signatureInput = $header . "." . $claimSet;
    $privateKey = openssl_pkey_get_private($creds['private_key']);
    openssl_sign($signatureInput, $signature, $privateKey, "sha256WithRSAEncryption");
    $jwt = $signatureInput . "." . base64url_encode($signature);

    $tokenRequest = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($tokenRequest, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($tokenRequest, CURLOPT_POST, true);
    curl_setopt($tokenRequest, CURLOPT_POSTFIELDS, http_build_query(array(
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    )));

    $response = curl_exec($tokenRequest);
    $tokenData = json_decode($response, true);
    $accessToken = $tokenData['access_token'];
    curl_close($tokenRequest);
    $encryptedDek = base64_decode($x);

    $config = json_decode(file_get_contents('config/project_data.json'), true);

    $projectId = $config['projectId'];
    $locationId = $config['locationId'];
    $keyRingId = $config['keyRingId'];
    $keyId = $config['keyId'];

    $keyName = "projects/$projectId/locations/$locationId/keyRings/$keyRingId/cryptoKeys/$keyId";
    $kmsUrl = "https://cloudkms.googleapis.com/v1/$keyName:decrypt";

    $kmsRequest = curl_init($kmsUrl);
    curl_setopt($kmsRequest, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($kmsRequest, CURLOPT_POST, true);
    curl_setopt($kmsRequest, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ));
    curl_setopt($kmsRequest, CURLOPT_POSTFIELDS, json_encode(array(
        'ciphertext' => base64_encode($encryptedDek)
    )));

    $kmsResponse = curl_exec($kmsRequest);
    $kmsData = json_decode($kmsResponse, true);
    curl_close($kmsRequest);

    if (isset($kmsData['plaintext'])) {
        $dek = base64_decode($kmsData['plaintext']);
        echo "DEK decrypted and ready to use: " . $dek . "\n";
    } else {
        echo "Decryption failed.\n";
        print_r($kmsData);
    }
    function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

}

o("CiQA1x27YDd2aIjSXhx5q/BmfoPv0lrVNQ6ejhEBRK4vmkVt3+ASaQAfoX3hzwkyWyGdpigu8W1gqz9ipCdc4Hv3K69EUup1gySDH26b0IgnMttwjZ4LNhH9kSadOb/NB9qxxTtnBbzFGuCYwenkOvRPA8xo1m1auB+dgmbrypBfIuiPN7W+PPJUhYGNg+sROw==");
<?php

require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

$keyFile = 'key.prod.txt';
$teamId = 'ZH8JP854B6';
$clientId = 'net.jobmatchy.jp';
$keyId = 'D9G9652794';

$ecdsaKey =  openssl_pkey_get_private('file://./key.prod.txt');

$headers = [
    'kid' => $keyId
];

$issuedAt = time();
$expirationTime = $issuedAt + 86400 * 180; // 180 days validity
$audience = 'https://appleid.apple.com';

$payload = [
    'iss' => $teamId,
    'iat' => $issuedAt,
    'exp' => $expirationTime,
    'aud' => $audience,
    'sub' => $clientId,
];

$token = JWT::encode($payload, $ecdsaKey, 'ES256', null, $headers);

echo $token;
<?php

namespace App\Traits;

trait FirebaseTrait
{
    public function getAccessToken()
    {
        $credentialsFilePath = base_path(env('FIREBASE_CREDENTIALS'));
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];

        return $access_token;
    }
}

<?php

namespace App\Services;

use Google\Client as GoogleClient;


class Google
{
  public function getClient()
  {
    $client = new GoogleClient();
    $client->setApplicationName('Test Google Meet');
    $client->setAccessType('offline');
    $client->setRedirectUri('http://localhost:8000/auth');
    $client->setAuthConfig('../google_credentials.json');
    $client->addScope('https://www.googleapis.com/auth/meetings.space.created');
    if (file_exists(__DIR__ . '/../../token.json')) {
      $accessToken = json_decode(file_get_contents(__DIR__ . '/../../token.json'), true);
      $client->setAccessToken($accessToken);
    }
    if ($client->isAccessTokenExpired()) {
      if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      } else {
        if (!request()->has('code')) {
          $authUrl = $client->createAuthUrl();
          return redirect($authUrl);
        } else {
          $accessToken =  $client->fetchAccessTokenWithAuthCode(request()->input('code'));
          $client->setAccessToken($accessToken);
        }
      }
      file_put_contents(__DIR__ . '/../../token.json', json_encode($client->getAccessToken()));
    }
    return $client;
  }
}

<?php

namespace google;

use Exception;
use Google_Client;
use exceptions\GoogleAPIException;

abstract class ClientBase
{
    private const ACCESS_TOKEN = 'access_token';
    private const ACCESS_TOKEN_EXP = 'access_token_expiry';
    private const REFRESH_TOKEN_NAME = "youtube_refresh_token";

    protected array $scopes;

    public function __construct(array $scopes)
    {
        $this->setScopes($scopes);
    }

    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    public function create(string $credentials, string $refreshToken)
    {
        try {
            $client = new Google_Client();
            $client->setAuthConfig($credentials);
            $client->setPrompt("consent");
            $client->setAccessType("offline");
            $client->setIncludeGrantedScopes(true);
            $client->setScopes($this->scopes);

            if (!$this->isSessionActive(self::REFRESH_TOKEN_NAME) || $this->accessTokenExpiry()) {
                $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $_SESSION[self::ACCESS_TOKEN] = $client->getAccessToken();
                $_SESSION[self::ACCESS_TOKEN_EXP] = time() + 3600;
            }
            return $client;
        } catch (Exception $e) {
            throw new GoogleAPIException($e);
        }
    }

    protected function isSessionActive($refreshToken)
    {
        return isset($_SESSION[$refreshToken]) && isset($_SESSION[self::ACCESS_TOKEN_EXP]);
    }

    protected function accessTokenExpiry()
    {
        return time() > $_SESSION[self::ACCESS_TOKEN];
    }
}

<?php

namespace holybunch\shared\google;

use Exception;
use Google_Client;
use holybunch\shared\exceptions\SharedException;

abstract class ClientBase
{
    public const ACCESS_TOKEN = 'access_token';
    public const ACCESS_TOKEN_EXP = 'access_token_expiry';

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

            if (!$this->isSessionActive() || $this->accessTokenExpiry()) {
                $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $_SESSION[self::ACCESS_TOKEN] = $client->getAccessToken();
                $_SESSION[self::ACCESS_TOKEN_EXP] = time() + 3600;
            }
            return $client;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    protected function isSessionActive()
    {
        return isset($_SESSION[self::ACCESS_TOKEN]) && isset($_SESSION[self::ACCESS_TOKEN_EXP]);
    }

    protected function accessTokenExpiry()
    {
        return time() > $_SESSION[self::ACCESS_TOKEN];
    }
}

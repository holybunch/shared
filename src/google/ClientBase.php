<?php

namespace holybunch\shared\google;

use Exception;
use Google_Client;
use holybunch\shared\exceptions\SharedException;

/**
 * This abstract class serves as a base for implementing Google API client functionalities.
 *
 * @abstract
 */
abstract class ClientBase
{
    public const ACCESS_TOKEN = 'access_token';
    public const ACCESS_TOKEN_EXP = 'access_token_expiry';

    /** @var string[] List of Google API scopes required by the client. */
    protected array $scopes;

    /**
     * ClientBase constructor.
     *
     * @param string[] $scopes The array of scopes to be set for the client.
     */
    public function __construct(array $scopes)
    {
        $this->setScopes($scopes);
    }

    /**
     * Sets the list of Google API scopes required by the client.
     *
     * @param string[] $scopes List of scopes.
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    /**
     * Creates a Google API client using the provided credentials and refresh token.
     * It checks for a valid access token in the session and refreshes it if necessary.
     *
     * @param string $credentials Path to the JSON credentials file for the Google API.
     * @param string $refreshToken Refresh token for obtaining a new access token.
     * @return Google_Client The created Google API client.
     * @throws SharedException If an error occurs during client creation or token refresh.
     */
    public function create(string $credentials, string $refreshToken): Google_Client
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

    /**
     * Checks if an active session exists.
     *
     * @return bool Returns true if an active session exists; otherwise, false.
     */
    protected function isSessionActive(): bool
    {
        return isset($_SESSION[self::ACCESS_TOKEN]) && isset($_SESSION[self::ACCESS_TOKEN_EXP]);
    }

    /**
     * Checks if the access token has expired.
     *
     * @return bool Returns true if the access token has expired; otherwise, false.
     */
    protected function accessTokenExpiry(): bool
    {
        return time() > $_SESSION[self::ACCESS_TOKEN];
    }
}

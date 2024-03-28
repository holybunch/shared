<?php

namespace holybunch\shared\google;

use Exception;
use Google_Client;
use holybunch\shared\exceptions\SharedException;

/**
 * Class ClientBase.
 * This abstract class serves as a base for implementing Google API client functionalities.
 *
 * @abstract
 * @author holybunch
 */
abstract class ClientBase
{
    public const ACCESS_TOKEN = 'access_token';
    public const ACCESS_TOKEN_EXP = 'access_token_expiry';
    private const REFRESH_TOKEN = 'refresh_token';

    /** @var string[] List of Google API scopes required by the client. */
    protected array $scopes;

    /**
     * Constructs a new ClientBase object with the provided array of scopes.
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

    public function updatRefreshToken(string $token, string $configPath): void
    {
        try {
            $jsonData = file_get_contents($configPath);
            if (!$jsonData) {
                throw new Exception("Failed to read configuration data from {$configPath}");
            }
            $data = json_decode($jsonData, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Failed to decode JSON data from {$configPath}");
            }    
            if (!is_array($data)) {
                throw new Exception("JSON data in {$configPath} is not an array");
            }
            if (!isset($data[self::REFRESH_TOKEN])) {
                throw new Exception("Refresh token key is missing in {$configPath}");
            }
        
            $data[self::REFRESH_TOKEN] = $token;            
            if (file_put_contents($configPath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
                throw new Exception("Failed to write updated data to {$configPath}");
            }
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

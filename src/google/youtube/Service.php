<?php

namespace holybunch\shared\google\youtube;

use Exception;
use Google_Client;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\youtube\apis\PlaylistItemsAPI;
use holybunch\shared\google\youtube\apis\PlaylistsAPI;
use holybunch\shared\google\youtube\apis\VideosAPI;

/**
 * Class Service.
 *
 * @final
 * @author holybunch
 */
final class Service
{
    private const REFRESH_TOKEN = 'refresh_token';
    private string $configFilePath;
    private string $credsFilePath;
    private Client $client;
    private Google_Client $googleClient;

    public function __construct(string $configFilePath, string $credsFilePath)
    {
        try {
            $this->configFilePath = $configFilePath;
            $this->credsFilePath = $credsFilePath;
            $this->client = new Client();
            $this->googleClient = $this->client->create($this->credsFilePath, $this->obtainRefreshToken());
        } catch (SharedException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    public function googleClient(): Google_Client
    {
        return $this->googleClient;
    }

    public function playlistsAPI(): PlaylistsAPI
    {
        return new PlaylistsAPI($this->googleClient());
    }

    public function playlistItemsAPI(): PlaylistItemsAPI
    {
        return new PlaylistItemsAPI($this->googleClient());
    }

    public function videosAPI(): VideosAPI
    {
        return new VideosAPI($this->googleClient());
    }

    private function obtainRefreshToken(): string
    {
        try {
            $jsonData = file_get_contents($this->configFilePath);
            if (!$jsonData) {
                throw new Exception("Failed to read configuration data from {$this->configFilePath}");
            }

            $data = json_decode($jsonData, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Failed to decode JSON data from {$this->configFilePath}");
            }
            if (!is_array($data)) {
                throw new Exception("JSON data in {$this->configFilePath} is not an array");
            }

            if (!isset($data[self::REFRESH_TOKEN])) {
                throw new Exception("Refresh token key is missing in {$this->configFilePath}");
            }

            return $data[self::REFRESH_TOKEN];
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    /*
    public function updatRefreshToken(string $token, string $configFilePath): void
    {
        try {
            $jsonData = file_get_contents($configFilePath);
            if (!$jsonData) {
                throw new Exception("Failed to read configuration data from {$configFilePath}");
            }
            $data = json_decode($jsonData, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Failed to decode JSON data from {$configFilePath}");
            }
            if (!is_array($data)) {
                throw new Exception("JSON data in {$configFilePath} is not an array");
            }
            if (!isset($data[self::REFRESH_TOKEN])) {
                throw new Exception("Refresh token key is missing in {$configFilePath}");
            }

            $data[self::REFRESH_TOKEN] = $token;
            if (file_put_contents($configFilePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
                throw new Exception("Failed to write updated data to {$configFilePath}");
            }
        } catch (Exception $e) {
            throw new SharedException($e);
        }

    }
    */
}

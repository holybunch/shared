<?php

namespace holybunch\shared\google\youtube;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Google_Client;
use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\NotFoundException;
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
    public const REFRESH_TOKEN = 'refresh_token';
    private string $configFilePath;
    private string $credsFilePath;
    private Client $client;
    private Google_Client $googleClient;

    /** @var string[] */
    private array $configurationData;

    public function __construct(string $configFilePath, string $credsFilePath)
    {
        $this->configFilePath = $configFilePath;
        $this->credsFilePath = $credsFilePath;
    }

    public function create(): void
    {
        try {
            $this->client = new Client();
            $this->createConfigurationData();
            $this->googleClient = $this->client->create(
                $this->credsFilePath,
                $this->configurationData[self::REFRESH_TOKEN]
            );
        } catch (SharedException $e) {
            throw $e;
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

    public function updatRefreshToken(string $token): void
    {
        try {
            $this->configurationData[self::REFRESH_TOKEN] = $token;
            if (!file_put_contents($this->configFilePath, json_encode($this->configurationData, JSON_PRETTY_PRINT))) {
                throw new Exception("Failed to write updated data to {$this->configFilePath}");
            }
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }

    private function createConfigurationData(): void
    {
        try {
            $jsonData = file_get_contents($this->configFilePath);
            if (!$jsonData) {
                throw new NotFoundException("Failed to read configuration data from {$this->configFilePath}");
            }

            $data = json_decode($jsonData, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new BadRequestException("Failed to decode JSON data from {$this->configFilePath}");
            }

            if (!is_array($data) || !isset($data[self::REFRESH_TOKEN])) {
                throw new BadRequestException("Refresh token key is missing in {$this->configFilePath}");
            }

            $this->configurationData = $data;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}

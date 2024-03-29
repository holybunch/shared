<?php

namespace holybunch\shared\google\youtube;

use Exception;
use Google_Client;
use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\ServiceBase;
use holybunch\shared\google\youtube\apis\PlaylistItemsAPI;
use holybunch\shared\google\youtube\apis\PlaylistsAPI;
use holybunch\shared\google\youtube\apis\VideosAPI;

/**
 * Class Service.
 *
 * This class provides functionality related to Google's YouTube API.
 * It facilitates operations like creating the Google client, accessing various APIs,
 * and updating the refresh token.
 *
 * @final
 * @author holybunch
 */
final class Service extends ServiceBase
{
    public const REFRESH_TOKEN = 'refresh_token';
    private Client $client;
    private Google_Client $googleClient;

    /** @var string[] */
    private array $configurationData;

    /**
     * Constructs the Service object with the paths to the configuration and credentials files.
     *
     * @param string $configFilePath The path to the configuration file.
     * @param string $credsFilePath  The path to the credentials file.
     */
    public function __construct(string $configFilePath, string $credsFilePath)
    {
        parent::__construct();
        $this->configFilePath = $configFilePath;
        $this->credsFilePath = $credsFilePath;
    }

    /**
     * Creates the Google client and initializes necessary data.
     *
     * @throws SharedException If an error occurs during client creation or configuration data retrieval.
     */
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

    /**
     * Retrieves the Google client.
     *
     * @return Google_Client The Google client object.
     */
    public function googleClient(): Google_Client
    {
        return $this->googleClient;
    }

    /**
     * Retrieves the PlaylistsAPI object.
     *
     * @return PlaylistsAPI The PlaylistsAPI object.
     */
    public function playlistsAPI(): PlaylistsAPI
    {
        return new PlaylistsAPI($this->googleClient());
    }

    /**
     * Retrieves the PlaylistItemsAPI object.
     *
     * @return PlaylistItemsAPI The PlaylistItemsAPI object.
     */
    public function playlistItemsAPI(): PlaylistItemsAPI
    {
        return new PlaylistItemsAPI($this->googleClient());
    }

    /**
     * Retrieves the VideosAPI object.
     *
     * @return VideosAPI The VideosAPI object.
     */
    public function videosAPI(): VideosAPI
    {
        return new VideosAPI($this->googleClient());
    }

    /**
     * Updates the refresh token in the configuration data.
     *
     * @param string $token The new refresh token.
     *
     * @throws SharedException If an error occurs while updating the token.
     */
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

    /**
     * Creates configuration data from the configuration file.
     *
     * @throws SharedException If an error occurs while creating configuration data.
     */
    private function createConfigurationData(): void
    {
        try {
            $data = $this->jsonContent($this->configFilePath);

            if (!is_array($data) || !isset($data[self::REFRESH_TOKEN])) {
                throw new BadRequestException("Refresh token key is missing in {$this->configFilePath}");
            }

            $this->configurationData = $data;
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}

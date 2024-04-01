<?php

namespace holybunch\shared\google;

use Exception;
use Google_Client;
use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\NotFoundException;
use holybunch\shared\exceptions\SharedException;

/**
 * Class ServiceBase
 *
 * This abstract class provides a base for Google service classes.
 * It contains common functionality shared among different Google service classes.
 *
 * @abstract
 * @author holybunch
 */
abstract class ServiceBase
{
    protected string $configFilePath;
    protected string $credsFilePath;

    public const REFRESH_TOKEN = 'refresh_token';
    protected Google_Client $googleClient;

    /** @var string[] */
    protected array $configurationData;

    /**
     * Constructor for the ServiceBase class.
     */
    public function __construct()
    {
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
     * Creates a Google client instance configured with a refresh token.
     *
     * This function reads configuration data from the specified file path and extracts the refresh token.
     * It then uses the provided client base to create a Google client instance, passing the credentials file path
     * and the extracted refresh token for configuration.
     *
     * @param ClientBase $client The client base used to create the Google client instance.
     * @throws BadRequestException If the refresh token key is missing in the configuration file.
     */
    protected function createGoogleClientWithRefreshToken(ClientBase $client): void
    {
        $data = $this->jsonContent($this->configFilePath);
        if (!is_array($data) || !isset($data[self::REFRESH_TOKEN])) {
            throw new BadRequestException("Refresh token key is missing in {$this->configFilePath}");
        }
        $this->configurationData = $data;
        $this->googleClient = $client->create(
            $this->credsFilePath,
            $this->configurationData[self::REFRESH_TOKEN]
        );
    }

    /**
     * Read and decode JSON content from a file.
     *
     * @param string $filePath The path to the file.
     * @return mixed The decoded JSON data.
     * @throws NotFoundException If the file is not found or cannot be read.
     * @throws BadRequestException If the JSON data cannot be decoded.
     */
    protected function jsonContent(string $filePath): mixed
    {
        $content = file_get_contents($filePath);
        if (!$content) {
            throw new NotFoundException("Failed to read configuration data from {$this->configFilePath}");
        }

        $data = json_decode($content, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestException("Failed to decode JSON data from {$this->configFilePath}");
        }
        return $data;
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
}

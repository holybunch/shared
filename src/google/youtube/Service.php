<?php

namespace holybunch\shared\google\youtube;

use Exception;
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
 * @author holybunch
 */
class Service extends ServiceBase
{
    /**
     * Constructs the Service object with the paths to the configuration and credentials files.
     *
     * @param string $configFilePath The path to the configuration file.
     * @param string $credsFilePath  The path to the credentials file.
     *
     * @throws SharedException If an error occurs during client creation or configuration data retrieval.
     */
    public function __construct(string $configFilePath, string $credsFilePath)
    {
        try {
            parent::__construct();
            $this->configFilePath = $configFilePath;
            $this->credsFilePath = $credsFilePath;
            $this->createGoogleClientWithRefreshToken(new Client());
        } catch (Exception $e) {
            throw new SharedException($e);
        }
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
}

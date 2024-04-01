<?php

namespace holybunch\shared\google\calendar;

use Exception;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\calendar\apis\EventsAPI;
use holybunch\shared\google\ServiceBase;

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

    public function getEventsAPI(): EventsAPI
    {
        return new EventsAPI($this->googleClient());
    }
}

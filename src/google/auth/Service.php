<?php

namespace holybunch\shared\google\auth;

use Exception;
use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\ServiceBase;

/**
 * Class Service
 *
 * This class provides methods to interact with Google services authentication.
 *
 * @final
 * @author holybunch
 */
final class Service extends ServiceBase
{
    private const CLIENT_ID = "client_id";

    /**
     * Constructs the Service object with the paths to the configuration files.
     *
     * @param string $configFilePath The path to the configuration file.
     */
    public function __construct(string $configFilePath)
    {
        $this->configFilePath = $configFilePath;
    }

    /**
     * Retrieve the client ID from the configuration file.
     *
     * @return string The client ID.
     * @throws SharedException If an error occurs during the process.
     */
    public function clientId(): string
    {
        try {
            $data = $this->jsonContent($this->configFilePath);
            if (!is_array($data) || !isset($data[self::CLIENT_ID])) {
                throw new BadRequestException("Client ID key is missing in {$this->configFilePath}");
            }
            return $data["client_id"];
        } catch (Exception $e) {
            throw new SharedException($e);
        }
    }
}

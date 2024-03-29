<?php

namespace holybunch\shared\google;

use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\NotFoundException;

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

    /**
     * Constructor for the ServiceBase class.
     */
    public function __construct()
    {
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
}

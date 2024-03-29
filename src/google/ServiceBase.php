<?php

namespace holybunch\shared\google;

use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\NotFoundException;

/**
 * Class serviceBase.
 *
 * @abstract
 * @author holybunch
 */
abstract class ServiceBase
{
    protected string $configFilePath;
    protected string $credsFilePath;

    public function __construct()
    {
    }

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

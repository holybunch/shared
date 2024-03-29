<?php

namespace holybunch\shared\google\auth;

use Exception;
use holybunch\shared\exceptions\BadRequestException;
use holybunch\shared\exceptions\SharedException;
use holybunch\shared\google\ServiceBase;

class Service extends ServiceBase {

    private const CLIENT_ID = "client_id";

    public function __construct(string $configFilePath)
    {
        $this->configFilePath = $configFilePath;
    }

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
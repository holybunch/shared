<?php

namespace holybunch\shared\google\youtube;

use Google\ClientBase;
use Google\Service\YouTube;
use Google_Client;

class Client extends ClientBase
{
    private array $scopes;

    public function __construct()
    {
        $this->setScopes([
            YouTube::YOUTUBE,
            YouTube::YOUTUBE_FORCE_SSL,
            YouTube::YOUTUBE_UPLOAD,
            YouTube::YOUTUBE_READONLY,
            YouTube::YOUTUBEPARTNER,
            YouTube::YOUTUBEPARTNER_CHANNEL_AUDIT
        ]);
    }

    public function setScopes(array $scopes): void {
        $this->scopes = $scopes;
    }

    public function create()
    {
        $client = new Google_Client();
        $client->setAuthConfig($config[self::CREDENTIALS_FILE_NAME]);
        $client->setPrompt("consent");
        $client->setAccessType("offline");
        $client->setIncludeGrantedScopes(true);
        $client->setScopes($this->scopes);
    }
}
